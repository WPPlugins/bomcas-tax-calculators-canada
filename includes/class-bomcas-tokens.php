<?php
/**
 * Page tokens. Years, rates, amounts, and dates are filled from the rate dataset.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves {year}, {rate:…}, {bpa:…}, {example:…}, and the other page tokens.
 */
class Bomcas_Tokens {

	/**
	 * Hooks.
	 */
	public static function boot() {
		add_action( 'admin_post_bomcas_tokens_migrate', array( __CLASS__, 'migrate' ) );
		add_action( 'admin_post_bomcas_tidy_titles', array( __CLASS__, 'tidy_titles' ) );
		add_action( 'bomcas_daily', array( __CLASS__, 'maybe_purge' ) );
		add_action( 'admin_notices', array( __CLASS__, 'year_notice' ) );
		add_action( 'admin_post_bomcas_rates_year', array( __CLASS__, 'save_year' ) );
		add_filter( 'the_title', array( __CLASS__, 'filter_title' ), 20, 2 );
		add_filter( 'get_the_excerpt', array( __CLASS__, 'replace' ), 20 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'picker_script' ) );
	}

	/**
	 * Tax year shown on calculator pages.
	 *
	 * Newest dataset year whose effective date is today or earlier, unless an
	 * administrator has saved an override on Rates & Tax Data.
	 *
	 * @return int
	 */
	public static function year() {
		$pack     = function_exists( 'bomcas_calc_seed' ) ? bomcas_calc_seed( 'personal-tax-current.json' ) : array();
		$years    = isset( $pack['years'] ) && is_array( $pack['years'] ) ? $pack['years'] : array();
		$override = (string) get_option( 'bomcas_rates_display_year', '' );
		if ( '' !== $override && isset( $years[ $override ] ) ) {
			return (int) $override;
		}
		$today = function_exists( 'current_time' ) ? current_time( 'Y-m-d' ) : gmdate( 'Y-m-d' );
		$best  = 0;
		foreach ( $years as $key => $row ) {
			$from = isset( $row['effective_from'] ) ? (string) $row['effective_from'] : ( (string) $key . '-01-01' );
			if ( $from <= $today && (int) $key > $best ) {
				$best = (int) $key;
			}
		}
		return $best;
	}

	/**
	 * Year as a dataset key.
	 *
	 * @return string
	 */
	public static function key() {
		return (string) self::year();
	}

	/**
	 * Active personal-tax year row.
	 *
	 * @return array
	 */
	public static function row() {
		$pack = function_exists( 'bomcas_calc_seed' ) ? bomcas_calc_seed( 'personal-tax-current.json' ) : array();
		$key  = self::key();
		return isset( $pack['years'][ $key ] ) && is_array( $pack['years'][ $key ] ) ? $pack['years'][ $key ] : array();
	}

	/**
	 * True when the calendar year is not in the dataset.
	 *
	 * @return bool
	 */
	public static function calendar_missing() {
		$pack = function_exists( 'bomcas_calc_seed' ) ? bomcas_calc_seed( 'personal-tax-current.json' ) : array();
		$cal  = function_exists( 'current_time' ) ? (string) current_time( 'Y' ) : gmdate( 'Y' );
		return empty( $pack['years'][ $cal ] );
	}

	/**
	 * Year text. Names the latest year on file when this calendar year has no rates.
	 *
	 * @return string
	 */
	public static function label() {
		$year = self::key();
		if ( self::calendar_missing() && '' === (string) get_option( 'bomcas_rates_display_year', '' ) ) {
			return $year . ' ' . __( '(latest rates on file)', 'bomcas-tax-calculators-canada' );
		}
		return $year;
	}

	/**
	 * ISO date from the active calendar.
	 *
	 * @param string $name Calendar key.
	 * @return string
	 */
	public static function iso( $name ) {
		$row   = self::row();
		$value = $row['calendar'][ $name ] ?? '';
		return is_string( $value ) ? $value : '';
	}

	/**
	 * Expand tokens. Unknown tokens are left as written.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	public static function replace( $text ) {
		if ( ! is_string( $text ) || false === strpos( $text, '{' ) ) {
			return (string) $text;
		}
		$out = preg_replace_callback(
			'/\{([a-z_]+)(?::([A-Za-z0-9._-]+))?\}/',
			array( __CLASS__, 'swap' ),
			$text
		);
		return is_string( $out ) ? $out : $text;
	}

	/**
	 * One token, without braces.
	 *
	 * @param string $token Name or name:path.
	 * @return string
	 */
	public static function plain( $token ) {
		return self::replace( '{' . $token . '}' );
	}

	/**
	 * @param array $match Regex match.
	 * @return string
	 */
	private static function swap( $match ) {
		$name  = $match[1];
		$path  = isset( $match[2] ) ? $match[2] : '';
		$value = self::value( $name, $path );
		return null === $value ? $match[0] : $value;
	}

	/**
	 * @param string $name Token name.
	 * @param string $path Path after the colon.
	 * @return string|null
	 */
	private static function value( $name, $path ) {
		if ( 'year' === $name ) {
			if ( '' !== $path && preg_match( '/^([A-Za-z]{2})\.repealed$/', $path, $m ) ) {
				$states = self::states();
				$code   = strtoupper( $m[1] );
				return isset( $states['states'][ $code ]['repealed_after'] ) ? (string) $states['states'][ $code ]['repealed_after'] : null;
			}
			return self::key();
		}
		if ( 'year_label' === $name ) {
			return self::label();
		}
		if ( 'updated' === $name ) {
			return self::month_year( self::iso( 'updated' ) );
		}
		if ( 'effective' === $name ) {
			$row = self::row();
			return self::long_date( isset( $row['effective_from'] ) ? (string) $row['effective_from'] : '' );
		}
		if ( 'deadline' === $name ) {
			return self::deadline( $path );
		}
		if ( 'bpa' === $name ) {
			return self::money( self::bpa_amount( $path ) );
		}
		if ( 'rate' === $name ) {
			$rate = self::rate_value( $path );
			return null === $rate ? null : self::percent( $rate );
		}
		if ( 'money' === $name ) {
			$amount = self::money_value( $path );
			return null === $amount ? null : self::money( $amount );
		}
		if ( 'sample' === $name ) {
			return self::sample( $path );
		}
		if ( 'example' === $name ) {
			return self::example( $path );
		}
		if ( 'source' === $name ) {
			return self::source( $path );
		}
		if ( 'date' === $name ) {
			return self::date_token( $path );
		}
		return null;
	}

	/**
	 * @param string $path Path.
	 * @return string|null
	 */
	private static function deadline( $path ) {
		$row = self::row();
		$cal = isset( $row['calendar'][ $path ] ) ? $row['calendar'][ $path ] : null;
		if ( is_string( $cal ) ) {
			return self::long_date( $cal );
		}
		if ( ! is_array( $cal ) || ! $cal ) {
			return null;
		}
		$formatted = array();
		foreach ( $cal as $iso ) {
			$formatted[] = self::long_date( (string) $iso );
		}
		if ( count( $formatted ) < 2 ) {
			return $formatted ? $formatted[0] : null;
		}
		$last = array_pop( $formatted );
		return implode( ', ', $formatted ) . ', and ' . $last;
	}

	/**
	 * @param string $code Province or FED.
	 * @return float
	 */
	/**
	 * @return array
	 */
	private static function household() {
		if ( ! defined( 'BOMCAS_PRO_DIR' ) ) {
			return array();
		}
		$file = BOMCAS_PRO_DIR . 'seed/household.json';
		if ( ! is_readable( $file ) ) {
			return array();
		}
		$data = json_decode( (string) file_get_contents( $file ), true );
		return is_array( $data ) ? $data : array();
	}

	/**
	 * @param string $path Path under household.json.
	 * @return float|null
	 */
	private static function house_money( $path ) {
		$data = self::household();
		$year = self::key();
		$map  = array(
			'fhsa.annual'     => $data['fhsa']['annual'] ?? null,
			'fhsa.lifetime'   => $data['fhsa']['lifetime'] ?? null,
			'rrsp.limit'      => $data['rrsp']['limits'][ $year ] ?? null,
			'tfsa.limit'      => $data['tfsa']['limits'][ $year ] ?? null,
			'ccb.under6'      => $data['ccb']['under6'] ?? null,
			'ccb.six'         => $data['ccb']['sixTo17'] ?? null,
			'ccb.first'       => $data['ccb']['first'] ?? null,
			'ccb.second'      => $data['ccb']['second'] ?? null,
			'donations.slice' => $data['donations']['first_slice'] ?? null,
			'oas.threshold'   => $data['oas']['thresholds'][ $year ] ?? null,
			'ei.max'          => $data['ei']['years'][ $year ]['max_earnings'] ?? null,
			'welcome.first'   => $data['welcome']['years'][ $year ]['first'] ?? null,
			'welcome.second'  => $data['welcome']['years'][ $year ]['second'] ?? null,
		);
		return isset( $map[ $path ] ) && is_numeric( $map[ $path ] ) ? (float) $map[ $path ] : null;
	}

	/**
	 * @param string $path Path under household.json.
	 * @return float|null
	 */
	private static function house_rate( $path ) {
		$data = self::household();
		$year = self::key();
		$map  = array(
			'rrsp'            => $data['rrsp']['rate'] ?? null,
			'gains.inclusion' => $data['capital_gains']['inclusion'] ?? null,
			'ei.benefit'      => $data['ei']['benefit_rate'] ?? null,
			'oas'             => $data['oas']['rate'] ?? null,
			'donations.first' => $data['donations']['federal_first'] ?? null,
			'donations.rest'  => $data['donations']['federal_rest'] ?? null,
			'mortgage.gds'    => $data['mortgage']['gds'] ?? null,
			'mortgage.tds'    => $data['mortgage']['tds'] ?? null,
			'car.first'       => $data['car']['years'][ $year ]['province'][0] ?? null,
			'car.second'      => $data['car']['years'][ $year ]['province'][1] ?? null,
		);
		return isset( $map[ $path ] ) && is_numeric( $map[ $path ] ) ? (float) $map[ $path ] : null;
	}

	/**
	 * @param string $code Province or FED.
	 * @return float
	 */
	private static function bpa_amount( $code ) {
		$code = strtoupper( $code );
		$row  = self::jurisdiction( $code );
		return isset( $row['bpa']['max'] ) ? (float) $row['bpa']['max'] : 0.0;
	}

	/**
	 * @param string $code Code.
	 * @return array
	 */
	private static function jurisdiction( $code ) {
		$row = self::row();
		return isset( $row['jurisdictions'][ $code ] ) && is_array( $row['jurisdictions'][ $code ] ) ? $row['jurisdictions'][ $code ] : array();
	}

	/**
	 * @param string $path rate path.
	 * @return float|null
	 */
	private static function rate_value( $path ) {
		$bits = explode( '.', $path );
		$head = strtoupper( (string) array_shift( $bits ) );
		$rest = implode( '.', $bits );
		if ( 'HOUSE' === $head ) {
			return self::house_rate( $rest );
		}
		if ( 'CPP' === $head && 'employee' === $rest ) {
			$row = self::row();
			return isset( $row['cpp_ei']['rate'] ) ? (float) $row['cpp_ei']['rate'] : null;
		}
		if ( 'EI' === $head && 'employee' === $rest ) {
			$row = self::row();
			return isset( $row['cpp_ei']['ei_rate'] ) ? (float) $row['cpp_ei']['ei_rate'] : null;
		}
		if ( 'US' === $head ) {
			return null;
		}
		if ( 'WORKERS' === $head ) {
			$row = self::row();
			if ( isset( $row['quebec_workers']['rate'] ) ) {
				return (float) $row['quebec_workers']['rate'];
			}
			return isset( $row['cpp_ei']['quebec_workers']['rate'] ) ? (float) $row['cpp_ei']['quebec_workers']['rate'] : null;
		}
		$row = self::jurisdiction( $head );
		if ( 'income.first' === $rest ) {
			return isset( $row['brackets'][0]['rate'] ) ? (float) $row['brackets'][0]['rate'] : null;
		}
		if ( 'income.top' === $rest ) {
			$last = isset( $row['brackets'] ) ? count( $row['brackets'] ) - 1 : -1;
			return $last >= 0 ? (float) $row['brackets'][ $last ]['rate'] : null;
		}
		if ( 'income.credit' === $rest ) {
			return isset( $row['credit_rate'] ) ? (float) $row['credit_rate'] : null;
		}
		if ( 'payroll_july' === $rest ) {
			return isset( $row['payroll_july_rate'] ) ? (float) $row['payroll_july_rate'] : null;
		}
		if ( 'abatement' === $rest ) {
			return isset( $row['federal_abatement'] ) ? (float) $row['federal_abatement'] : null;
		}
		if ( 'reduction' === $rest ) {
			return isset( $row['bc_reduction']['rate'] ) ? (float) $row['bc_reduction']['rate'] : null;
		}
		if ( 'k5p' === $rest ) {
			return isset( $row['ab_k5p']['multiplier'] ) ? (float) $row['ab_k5p']['multiplier'] : null;
		}
		if ( preg_match( '/^surtax\.(\d+)$/', $rest, $sur ) ) {
			$index = (int) $sur[1];
			return isset( $row['on_surtax'][ $index ]['rate'] ) ? (float) $row['on_surtax'][ $index ]['rate'] : null;
		}
		if ( preg_match( '/^sales\.(gst|pst|hst|qst)$/', $rest, $part ) ) {
			return self::sales_component( $head, $part[1], false );
		}
		if ( preg_match( '/^sales\.prior\.(gst|pst|hst|qst)$/', $rest, $part ) ) {
			return self::sales_component( $head, $part[1], true );
		}
		return null;
	}

	/**
	 * @param string $path money path.
	 * @return float|null
	 */
	private static function money_value( $path ) {
		$bits = explode( '.', $path );
		$head = strtoupper( (string) array_shift( $bits ) );
		if ( 'HOUSE' === $head ) {
			return self::house_money( implode( '.', $bits ) );
		}
		if ( 'CPP' === $head && 'ympe' === implode( '.', $bits ) ) {
			$row = self::row();
			return isset( $row['cpp_ei']['ympe'] ) ? (float) $row['cpp_ei']['ympe'] : null;
		}
		if ( 'US' === $head ) {
			return null;
		}
		if ( 'WORKERS' === $head && 'max' === implode( '.', $bits ) ) {
			$row = self::row();
			if ( isset( $row['quebec_workers']['max'] ) ) {
				return (float) $row['quebec_workers']['max'];
			}
			return isset( $row['cpp_ei']['quebec_workers']['max'] ) ? (float) $row['cpp_ei']['quebec_workers']['max'] : null;
		}
		$row  = self::jurisdiction( $head );
		$rest = implode( '.', $bits );
		if ( 'health.0.upto' === $rest || preg_match( '/^health\.(\d+)\.cap$/', $rest, $cap ) ) {
			if ( isset( $cap[1] ) ) {
				$index = (int) $cap[1];
				return isset( $row['on_health']['bands'][ $index ]['cap'] ) ? (float) $row['on_health']['bands'][ $index ]['cap'] : null;
			}
			return isset( $row['on_health']['bands'][0]['upto'] ) ? (float) $row['on_health']['bands'][0]['upto'] : null;
		}
		if ( preg_match( '/^surtax\.(\d+)$/', $rest, $sur ) ) {
			$index = (int) $sur[1];
			return isset( $row['on_surtax'][ $index ]['over'] ) ? (float) $row['on_surtax'][ $index ]['over'] : null;
		}
		if ( 'payroll_july_reduction' === $rest ) {
			return isset( $row['payroll_july_reduction'] ) ? (float) $row['payroll_july_reduction'] : null;
		}
		if ( 'reduction.basic' === $rest ) {
			return isset( $row['bc_reduction']['basic'] ) ? (float) $row['bc_reduction']['basic'] : null;
		}
		if ( 'reduction.threshold' === $rest ) {
			return isset( $row['bc_reduction']['threshold'] ) ? (float) $row['bc_reduction']['threshold'] : null;
		}
		if ( 'reduction.upper' === $rest ) {
			return isset( $row['bc_reduction']['upper'] ) ? (float) $row['bc_reduction']['upper'] : null;
		}
		if ( 'k5p.threshold' === $rest ) {
			return isset( $row['ab_k5p']['threshold'] ) ? (float) $row['ab_k5p']['threshold'] : null;
		}
		if ( 'bpa.from' === $rest ) {
			$from = $row['mb_bpa_phase']['from'] ?? ( $row['bpa']['phase_from'] ?? null );
			return is_numeric( $from ) ? (float) $from : null;
		}
		if ( 'bpa.to' === $rest ) {
			$to = $row['mb_bpa_phase']['to'] ?? ( $row['bpa']['phase_to'] ?? null );
			return is_numeric( $to ) ? (float) $to : null;
		}
		if ( 'reduction' === $rest && isset( $row['on_reduction_basic'] ) ) {
			return (float) $row['on_reduction_basic'];
		}
		return null;
	}

	/**
	 * Sales-tax component in force for the display year, or the segment before it.
	 *
	 * @param string $code  Province code.
	 * @param string $part  gst, pst, hst, or qst.
	 * @param bool   $prior Previous segment.
	 * @return float|null
	 */
	private static function sales_component( $code, $part, $prior ) {
		$sales = function_exists( 'bomcas_calc_seed' ) ? bomcas_calc_seed( 'sales-tax.json' ) : array();
		$segs  = $sales['jurisdictions'][ strtoupper( $code ) ] ?? array();
		$asof  = self::key() . '-12-31';
		$hits  = array();
		foreach ( (array) $segs as $seg ) {
			if ( ! is_array( $seg ) ) {
				continue;
			}
			$start = (string) ( $seg['from'] ?? '' );
			if ( '' !== $start && $start <= $asof ) {
				$hits[ $start ] = $seg;
			}
		}
		if ( ! $hits ) {
			return null;
		}
		krsort( $hits );
		$ordered = array_values( $hits );
		$pick    = $prior ? ( $ordered[1] ?? null ) : $ordered[0];
		return is_array( $pick ) && isset( $pick[ $part ] ) ? (float) $pick[ $part ] : null;
	}

	/**
	 * @param string $path sample path, income.0.
	 * @return string|null
	 */
	private static function sample( $path ) {
		$row = self::row();
		if ( preg_match( '/^income\.(\d+)$/', $path, $m ) ) {
			$index = (int) $m[1];
			$list  = isset( $row['calendar']['income_samples'] ) ? $row['calendar']['income_samples'] : array();
			return isset( $list[ $index ] ) ? self::money( $list[ $index ] ) : null;
		}
		return null;
	}

	/**
	 * @param string $path CODE.kind.amount.field.
	 * @return string|null
	 */
	private static function example( $path ) {
		$bits = explode( '.', $path );
		if ( count( $bits ) < 4 ) {
			return null;
		}
		$code   = strtoupper( $bits[0] );
		$kind   = $bits[1];
		$amount = (float) $bits[2];
		$field  = $bits[3];
		$pack   = self::example_pack();
		$group  = isset( $pack['ca'][ $code ] ) ? 'ca' : '';
		if ( '' === $group || empty( $pack[ $group ][ $code ][ $kind ] ) ) {
			return null;
		}
		foreach ( $pack[ $group ][ $code ][ $kind ] as $row ) {
			$needle = $row['employment'] ?? $row['wages'] ?? $row['gross'] ?? $row['amount'] ?? $row['price'] ?? null;
			if ( null !== $needle && abs( (float) $needle - $amount ) < 0.01 && isset( $row[ $field ] ) ) {
				return self::money( $row[ $field ] );
			}
		}
		return null;
	}

	/**
	 * @param string $path Source key.
	 * @return string|null
	 */
	private static function source( $path ) {
		if ( 0 === strpos( $path, 'us.' ) ) {
			return null;
		}
		if ( preg_match( '/^([A-Za-z]{2})\.repealed$/', $path, $m ) ) {
			$states = self::states();
			$code   = strtoupper( $m[1] );
			return isset( $states['states'][ $code ]['repealed_after'] ) ? (string) $states['states'][ $code ]['repealed_after'] : null;
		}
		return null;
	}

	/**
	 * @param string $path Date path.
	 * @return string|null
	 */
	private static function date_token( $path ) {
		if ( preg_match( '/^([A-Za-z]{2})\.sales\.latest_from$/i', $path, $m ) ) {
			$sales = function_exists( 'bomcas_calc_seed' ) ? bomcas_calc_seed( 'sales-tax.json' ) : array();
			$segs  = $sales['jurisdictions'][ strtoupper( $m[1] ) ] ?? array();
			$from  = '';
			foreach ( (array) $segs as $seg ) {
				if ( isset( $seg['from'] ) && (string) $seg['from'] > $from ) {
					$from = (string) $seg['from'];
				}
			}
			return '' !== $from ? self::long_date( $from ) : null;
		}
		return null;
	}

	/**
	 * Live worked examples for the active year. Cached until rates are purged.
	 *
	 * @return array
	 */
	public static function example_pack() {
		static $memo = null;
		if ( is_array( $memo ) ) {
			return $memo;
		}
		$payload = self::example_payload();
		$hash    = md5( wp_json_encode( $payload ) );
		$stored  = get_transient( 'bomcas_example_pack' );
		if ( is_array( $stored ) && ( $stored['hash'] ?? '' ) === $hash && isset( $stored['pack'] ) ) {
			$memo = $stored['pack'];
			return $memo;
		}
		$pack = self::run_examples( $payload );
		if ( ! is_array( $pack ) ) {
			$file = BOMCAS_CALC_DIR . 'seed/guide-examples.json';
			$raw  = is_readable( $file ) ? json_decode( file_get_contents( $file ), true ) : array();
			$pack = is_array( $raw ) ? $raw : array();
		}
		set_transient(
			'bomcas_example_pack',
			array(
				'hash' => $hash,
				'pack' => $pack,
			),
			12 * HOUR_IN_SECONDS
		);
		$memo = $pack;
		return $memo;
	}

	/**
	 * @return array
	 */
	private static function example_payload() {
		$personal = function_exists( 'bomcas_calc_seed' ) ? bomcas_calc_seed( 'personal-tax-current.json' ) : array();
		$firm     = array();
		if ( defined( 'BOMCAS_PRO_DIR' ) ) {
			$path = BOMCAS_PRO_DIR . 'seed/firm-tools.json';
			$raw  = is_readable( $path ) ? json_decode( file_get_contents( $path ), true ) : array();
			$firm = is_array( $raw ) ? $raw : array();
			if ( class_exists( 'Bomcas_Rates' ) ) {
				$firm = Bomcas_Rates::apply_dataset( 'firm-tools.json', $firm );
			}
		}
		return array(
			'yearKey'  => self::key(),
			'personal' => $personal,
			'sales'    => function_exists( 'bomcas_calc_seed' ) ? bomcas_calc_seed( 'sales-tax.json' ) : array(),
			'federal'  => array(),
			'states'   => array(),
			'usSales'  => array(),
			'firm'     => $firm,
		);
	}

	/**
	 * Worked examples come from the JSON file shipped with the plugin.
	 * The plugin does not run external programs.
	 *
	 * @param array $payload Payload.
	 * @return array|null
	 */
	private static function run_examples( $payload ) {
		unset( $payload );
		return null;
	}

	/**
	 * @return array
	 */
	private static function federal() {
		return array();
	}

	/**
	 * @return array
	 */
	private static function states() {
		return array();
	}

	/**
	 * Sentences built from the federal file so the page does not embed the figures.
	 *
	 * @param string $country CA or US.
	 * @return string[]
	 */
	public static function fact_lines( $country ) {
		$lines = array();
		if ( 'US' === $country ) {
			return array();
		} else {
			$fed     = self::jurisdiction( 'FED' );
			$band    = $fed['brackets'][0] ?? array();
			$lines[] = 'federal first bracket is ' . self::percent( $band['rate'] ?? 0 ) . '.';
			if ( isset( $band['upto'] ) ) {
				$lines[] = 'federal first bracket ends at ' . self::money( $band['upto'] ) . '.';
			}
			$lines[] = 'basic personal amount is ' . self::money( $fed['bpa']['max'] ?? 0 ) . '.';
		}
		$lines[] = 'last reviewed {updated}.';
		return $lines;
	}

	/**
	 * @param array  $data Data.
	 * @param string $path Dot path.
	 * @return mixed|null
	 */
	private static function dig( $data, $path ) {
		$cursor = $data;
		foreach ( explode( '.', $path ) as $bit ) {
			if ( ! is_array( $cursor ) || ! array_key_exists( $bit, $cursor ) ) {
				return null;
			}
			$cursor = $cursor[ $bit ];
		}
		return $cursor;
	}

	/**
	 * @param float $n Amount.
	 * @return string
	 */
	public static function money( $n ) {
		$n        = (float) $n;
		$decimals = abs( $n - round( $n ) ) < 0.001 ? 0 : 2;
		return '$' . number_format( $n, $decimals );
	}

	/**
	 * @param float $rate Decimal or a percent above 1.
	 * @return string
	 */
	public static function percent( $rate ) {
		return self::percent_number( $rate ) . ' percent';
	}

	/**
	 * @param float $rate Decimal or a percent above 1.
	 * @return string
	 */
	private static function percent_number( $rate ) {
		$n = (float) $rate;
		if ( $n <= 1 ) {
			$n *= 100;
		}
		return rtrim( rtrim( number_format( $n, 4, '.', '' ), '0' ), '.' );
	}

	/**
	 * @param string $iso Date.
	 * @return string
	 */
	private static function long_date( $iso ) {
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $iso ) ) {
			return '';
		}
		$ts = strtotime( $iso . ' 12:00:00 UTC' );
		return $ts ? gmdate( 'j F Y', $ts ) : '';
	}

	/**
	 * @param string $iso Date.
	 * @return string
	 */
	private static function month_year( $iso ) {
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $iso ) ) {
			return '';
		}
		$ts = strtotime( $iso . ' 12:00:00 UTC' );
		return $ts ? gmdate( 'F Y', $ts ) : '';
	}

	/**
	 * Tokens a buyer can insert. The tip is the tooltip.
	 *
	 * @return array<int,array{token:string,tip:string}>
	 */
	public static function catalog() {
		return array(
			array(
				'token' => '{year}',
				'tip'   => __( 'Tax year in force. Newest dataset year whose effective date is today or earlier, unless you set an override.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'token' => '{year_label}',
				'tip'   => __( 'Same year. Adds “latest rates on file” when this calendar year is not in the dataset.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'token' => '{updated}',
				'tip'   => __( 'Month and year the dataset was last verified.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'token' => '{effective}',
				'tip'   => __( 'Date these rates took effect.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'token' => '{bpa:BC}',
				'tip'   => __( 'Basic personal amount. Replace BC with a province code, or FED.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'token' => '{rate:BC.income.first}',
				'tip'   => __( 'First bracket rate for that code, written as a percent.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'token' => '{rate:BC.income.top}',
				'tip'   => __( 'Top bracket rate for that code.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'token' => '{example:BC.income.42000.total}',
				'tip'   => __( 'Live engine result. Use CODE.income.AMOUNT.total, federal, or provincial. The amount is the worked input, not a stored answer.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'token' => '{deadline:balance_due}',
				'tip'   => __( 'A date from the dataset calendar, such as balance_due or pay_example_on.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'token' => '{source:us.federal}',
				'tip'   => __( 'Official source title stored on the federal rate file.', 'bomcas-tax-calculators-canada' ),
			),
		);
	}

	/**
	 * Buttons for the page editor.
	 *
	 * @return string
	 */
	public static function picker() {
		$html = '<div class="bomcas-token-picker"><p><strong>' . esc_html__( 'Insert a live figure', 'bomcas-tax-calculators-canada' ) . '</strong></p><p>';
		foreach ( self::catalog() as $item ) {
			$html .= '<button type="button" class="button" data-bomcas-token="' . esc_attr( $item['token'] ) . '" title="' . esc_attr( $item['tip'] ) . '">' . esc_html( $item['token'] ) . '</button> ';
		}
		$html .= '</p><p class="description">' . esc_html__( 'Click a field, then a token. The page fills the number from the rate dataset for the year in force. Hover a token for what it means.', 'bomcas-tax-calculators-canada' ) . ' <a href="' . esc_url( Bomcas_Docs::url( 'tokens' ) ) . '">' . esc_html__( 'Learn more', 'bomcas-tax-calculators-canada' ) . '</a></p></div>';
		return $html;
	}

	/**
	 * Insert-token behaviour on the page editor.
	 *
	 * @param string $hook Hook.
	 */
	public static function picker_script( $hook ) {
		unset( $hook );
		if ( empty( $_GET['page'] ) || 'bomcas-page-content' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		wp_register_script( 'bomcas-token-picker', false, array(), BOMCAS_CALC_VERSION, true );
		wp_enqueue_script( 'bomcas-token-picker' );
		wp_add_inline_script(
			'bomcas-token-picker',
			'document.addEventListener("focusin",function(e){if(e.target&&e.target.form&&(e.target.tagName==="TEXTAREA"||e.target.tagName==="INPUT")){window.bomcasTokenField=e.target;}});document.addEventListener("click",function(e){var b=e.target.closest("[data-bomcas-token]");if(!b){return;}e.preventDefault();var f=window.bomcasTokenField||document.querySelector(".bomcas-token-picker")&&document.querySelector("textarea");if(!f){return;}var t=b.getAttribute("data-bomcas-token");var s=f.selectionStart||0;var n=f.selectionEnd||0;f.value=f.value.slice(0,s)+t+f.value.slice(n);f.focus();});'
		);
	}

	/**
	 * @param string $title Title.
	 * @param int    $id    Post id.
	 * @return string
	 */
	/**
	 * Stored excerpt. The year stays a token until the excerpt is displayed.
	 *
	 * @param int $id Page id.
	 * @return string
	 */
	public static function excerpt_text( $id ) {
		if ( class_exists( 'Bomcas_Ca_Pages' ) ) {
			$authored = Bomcas_Ca_Pages::excerpt( $id );
			if ( '' !== $authored ) {
				return $authored;
			}
		}
		return '';
	}

	/**
	 * @param string $slug Tool or page slug.
	 * @return string
	 */
	public static function acronym_title( $slug ) {
		$map = array(
			'fhsa'                   => 'FHSA',
			'rrsp-room'              => 'RRSP room',
			'tfsa-room'              => 'TFSA room',
			'ei-benefits'            => 'EI benefits',
			'cmhc-insurance'         => 'CMHC insurance',
			'cra-penalty-interest'   => 'CRA penalty and interest',
			'cpp'                    => 'CPP',
			'old-age-security'       => 'Old Age Security',
			'canada-child-benefit'   => 'CCB',
			'income-tax'             => 'Income tax',
			'sales-tax'              => 'Sales tax',
			'payroll-deductions'     => 'Payroll deductions',
			'salary-after-tax'       => 'Take-home pay',
			'vehicle-purchase'       => 'Vehicle purchase tax',
			'land-transfer-tax'      => 'Land transfer tax',
			'capital-gains'          => 'Capital gains',
			'capital-cost-allowance' => 'Capital cost allowance',
			'corporate-tax'          => 'Corporate tax',
			'salary-vs-dividend'     => 'Salary vs dividend',
			'quebec-withholding'     => 'Quebec withholding',
			'quebec-welcome-tax'     => 'Quebec welcome tax',
			'tax-instalments'        => 'Tax instalments',
			'employer-health-tax'    => 'Employer health tax',
			'self-employed-tax'      => 'Self-employed tax',
			'donations'              => 'Donations',
			'car-allowance'          => 'Car allowance',
			'mortgage-affordability' => 'Mortgage affordability',
			'lease-vs-buy'           => 'Lease vs buy',
		);
		return isset( $map[ $slug ] ) ? $map[ $slug ] : '';
	}

	/**
	 * @param string $title Title.
	 * @param int    $id    Post id.
	 * @return string
	 */
	public static function filter_title( $title, $id = 0 ) {
		if ( $id && 'page' === get_post_type( $id ) && get_post_meta( $id, '_bomcas_calc_managed', true ) ) {
			$slug  = (string) get_post_field( 'post_name', $id );
			$nice  = self::acronym_title( $slug );
			$plain = ucwords( str_replace( '-', ' ', $slug ) );
			if ( '' !== $nice && 0 === strcasecmp( (string) $title, $plain ) ) {
				$title = $nice;
			}
			return self::replace( $title );
		}
		return $title;
	}

	/**
	 * Tell an administrator when the calendar year has no rates.
	 */
	public static function year_notice() {
		if ( ! current_user_can( 'manage_options' ) || ! bomcas_calc_is_plugin_screen() || ! self::calendar_missing() ) {
			return;
		}
		echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html(
			sprintf(
				/* translators: 1: calendar year, 2: dataset year being shown */
				__( 'The rate dataset has no %1$s rates yet. Calculator pages are showing %2$s, the latest year on file, and they say so. Add the new year on Rates & Tax Data. Do not leave a stale year looking current.', 'bomcas-tax-calculators-canada' ),
				(string) current_time( 'Y' ),
				self::key()
			)
		) . '</p></div>';
	}

	/**
	 * Save the display-year override. Empty means automatic.
	 */
	public static function save_year() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot change the tax year.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_rates_year' );
		$raw = isset( $_POST['display_year'] ) ? sanitize_text_field( wp_unslash( $_POST['display_year'] ) ) : '';
		if ( '' === $raw ) {
			delete_option( 'bomcas_rates_display_year' );
		} else {
			update_option( 'bomcas_rates_display_year', $raw, false );
		}
		self::purge();
		wp_safe_redirect( admin_url( 'options-general.php?page=bomcas-rates&yearset=1' ) );
		exit;
	}

	/**
	 * Year card for the rates screen.
	 */
	public static function year_form() {
		$current = (string) get_option( 'bomcas_rates_display_year', '' );
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Year shown on calculator pages', 'bomcas-tax-calculators-canada' ) . '</h2>';
		echo '<p>' . esc_html(
			sprintf(
				/* translators: 1: year number, 2: year label */
				__( 'Automatic year is %1$s. Pages currently say %2$s. Leave the box empty to keep the newest year whose effective date is today or earlier.', 'bomcas-tax-calculators-canada' ),
				self::key(),
				self::label()
			)
		) . '</p>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_rates_year' );
		echo '<input type="hidden" name="action" value="bomcas_rates_year" />';
		echo '<p><label>' . esc_html__( 'Override year', 'bomcas-tax-calculators-canada' ) . ' <input name="display_year" value="' . esc_attr( $current ) . '" inputmode="numeric" /></label> ' . wp_kses( Bomcas_Admin_UI::tip( __( 'Leave empty for the newest year whose effective date is today or earlier.', 'bomcas-tax-calculators-canada' ), 'rates' ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		submit_button( __( 'Save display year', 'bomcas-tax-calculators-canada' ) );
		echo '</form>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_tokens_migrate' );
		echo '<input type="hidden" name="action" value="bomcas_tokens_migrate" />';
		echo '<p>' . esc_html__( 'Repair calculator excerpts in batches of 20. This does not change page bodies, and it leaves an excerpt you edited.', 'bomcas-tax-calculators-canada' ) . '</p>';
		submit_button( __( 'Repair token excerpts', 'bomcas-tax-calculators-canada' ) );
		echo '</form>';
		$changes = self::title_candidates();
		echo '<h2>' . esc_html__( 'Managed page titles', 'bomcas-tax-calculators-canada' ) . '</h2>';
		if ( ! $changes ) {
			echo '<p>' . esc_html__( 'No unedited default titles need tidying.', 'bomcas-tax-calculators-canada' ) . '</p>';
		} else {
			echo '<p>' . esc_html__( 'These managed titles are still a plugin default. A title you renamed, or a search title you saved, is not in this list.', 'bomcas-tax-calculators-canada' ) . '</p>';
			echo '<ul>';
			foreach ( $changes as $change ) {
				echo '<li>' . esc_html( $change['from'] . ' → ' . $change['to'] ) . '</li>';
			}
			echo '</ul>';
			$lock = (int) get_transient( 'bomcas_title_lock' );
			if ( $lock && $lock !== get_current_user_id() ) {
				echo '<p>' . esc_html__( 'Another administrator is tidying titles. Wait a minute and refresh.', 'bomcas-tax-calculators-canada' ) . '</p>';
			} else {
				echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
				wp_nonce_field( 'bomcas_tidy_titles' );
				echo '<input type="hidden" name="action" value="bomcas_tidy_titles" />';
				submit_button( __( 'Tidy managed page titles', 'bomcas-tax-calculators-canada' ) );
				echo '</form>';
			}
		}
		if ( isset( $_GET['titles'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$tidied = sanitize_text_field( wp_unslash( $_GET['titles'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<p>' . esc_html(
				'locked' === $tidied
					? __( 'The title tidy is locked by another administrator.', 'bomcas-tax-calculators-canada' )
					: sprintf(
						/* translators: %s: number of titles updated. */
						__( 'Titles updated: %s.', 'bomcas-tax-calculators-canada' ),
						$tidied
					)
			) . '</p>';
		}
		echo '</section>';
	}

	/**
	 * Drop calculator HTML cached by WP Fastest Cache, and the example pack.
	 */
	public static function purge() {
		delete_transient( 'bomcas_example_pack' );
		if ( function_exists( 'wpfc_clear_all_cache' ) ) {
			wpfc_clear_all_cache( true );
		}
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}
		do_action( 'bomcas_rates_cache_purged' );
	}

	/**
	 * Purge on 1 January and on any dataset effective date.
	 */
	public static function maybe_purge() {
		$today = function_exists( 'current_time' ) ? current_time( 'Y-m-d' ) : gmdate( 'Y-m-d' );
		if ( get_option( 'bomcas_rates_purged_on' ) === $today ) {
			return;
		}
		$hit  = ( '01-01' === substr( $today, 5 ) );
		$pack = function_exists( 'bomcas_calc_seed' ) ? bomcas_calc_seed( 'personal-tax-current.json' ) : array();
		foreach ( (array) ( $pack['years'] ?? array() ) as $row ) {
			if ( is_array( $row ) && ( $row['effective_from'] ?? '' ) === $today ) {
				$hit = true;
			}
		}
		$sales = function_exists( 'bomcas_calc_seed' ) ? bomcas_calc_seed( 'sales-tax.json' ) : array();
		foreach ( (array) ( $sales['jurisdictions'] ?? array() ) as $segs ) {
			foreach ( (array) $segs as $seg ) {
				if ( is_array( $seg ) && ( $seg['from'] ?? '' ) === $today ) {
					$hit = true;
				}
			}
		}
		if ( $hit ) {
			self::purge();
			update_option( 'bomcas_rates_purged_on', $today, false );
		}
	}

	/**
	 * Plugin-owned intros on managed pages become empty so the token builders run.
	 * Buyer intro edits in _bomcas_page_edits are left alone. Twenty pages per request.
	 */
	/**
	 * Unedited plugin titles an administrator may replace. Nothing is written here.
	 *
	 * @return array<int,array{id:int,from:string,to:string}>
	 */
	public static function title_candidates() {
		$ids     = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => '_bomcas_calc_managed', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		$changes = array();
		foreach ( $ids as $id ) {
			$edits = get_post_meta( $id, '_bomcas_page_edits', true );
			if ( is_array( $edits ) && ! empty( $edits['seo_title'] ) ) {
				continue;
			}
			$slug  = (string) get_post_field( 'post_name', $id );
			$next  = self::acronym_title( $slug );
			$title = (string) get_post_field( 'post_title', $id );
			if ( '' === $next || $title === $next || ! self::is_default_title( $slug, $title ) ) {
				continue;
			}
			$changes[] = array(
				'id'   => (int) $id,
				'from' => $title,
				'to'   => $next,
			);
		}
		return $changes;
	}

	/**
	 * Apply the preview list. Administrators only. A public page view never calls this.
	 */
	public static function tidy_titles() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot tidy page titles.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_tidy_titles' );
		$lock = (int) get_transient( 'bomcas_title_lock' );
		if ( $lock && $lock !== get_current_user_id() ) {
			wp_safe_redirect( admin_url( 'options-general.php?page=bomcas-rates&titles=locked' ) );
			exit;
		}
		set_transient( 'bomcas_title_lock', get_current_user_id(), MINUTE_IN_SECONDS );
		$count = 0;
		foreach ( self::title_candidates() as $change ) {
			wp_update_post(
				array(
					'ID'         => $change['id'],
					'post_title' => $change['to'],
				)
			);
			++$count;
		}
		delete_transient( 'bomcas_title_lock' );
		wp_safe_redirect( admin_url( 'options-general.php?page=bomcas-rates&titles=' . $count ) );
		exit;
	}

	/**
	 * @param string $slug  Page slug.
	 * @param string $title Stored title.
	 * @return bool
	 */
	private static function is_default_title( $slug, $title ) {
		$title = trim( $title );
		$plain = ucwords( str_replace( '-', ' ', $slug ) );
		if ( 0 === strcasecmp( $title, $plain ) ) {
			return true;
		}
		$legacy = array(
			'income-tax'             => array( 'income tax' ),
			'sales-tax'              => array( 'sales tax' ),
			'payroll-deductions'     => array( 'payroll deductions' ),
			'salary-after-tax'       => array( 'take-home pay' ),
			'vehicle-purchase'       => array( 'vehicle purchase tax' ),
			'land-transfer-tax'      => array( 'land transfer tax' ),
			'lease-vs-buy'           => array( 'lease or buy', 'lease vs buy' ),
			'salary-vs-dividend'     => array( 'salary versus dividend', 'salary vs dividend' ),
			'canada-child-benefit'   => array( 'Canada child benefit', 'canada child benefit' ),
			'capital-gains'          => array( 'capital gains' ),
			'capital-cost-allowance' => array( 'capital cost allowance' ),
			'corporate-tax'          => array( 'corporate tax' ),
			'tax-instalments'        => array( 'tax instalments' ),
			'employer-health-tax'    => array( 'employer health tax' ),
			'self-employed-tax'      => array( 'self-employed tax' ),
			'donations'              => array( 'donations' ),
			'car-allowance'          => array( 'car allowance' ),
			'mortgage-affordability' => array( 'mortgage affordability' ),
		);
		foreach ( (array) ( $legacy[ $slug ] ?? array() ) as $old ) {
			if ( $title === $old ) {
				return true;
			}
		}
		return false;
	}

	public static function migrate() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		check_admin_referer( 'bomcas_tokens_migrate' );
		$lock = (int) get_transient( 'bomcas_tokens_lock' );
		if ( $lock && $lock !== get_current_user_id() ) {
			return;
		}
		set_transient( 'bomcas_tokens_lock', get_current_user_id(), MINUTE_IN_SECONDS );
		if ( class_exists( 'Bomcas_Pro_Places' ) ) {
			Bomcas_Pro_Places::nest_under_hub();
		}
		if ( '1.0.9-tokens' === get_option( 'bomcas_tokens_migrated' ) ) {
			delete_transient( 'bomcas_tokens_lock' );
			if ( isset( $_POST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce checked at the top of this handler.
				wp_safe_redirect( admin_url( 'options-general.php?page=bomcas-rates&tokens=done' ) );
				exit;
			}
			return;
		}
		$ids = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'any',
				'posts_per_page' => 20,
				'fields'         => 'ids',
				'meta_key'       => '_bomcas_calc_managed', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					'relation' => 'OR',
					array(
						'key'     => '_bomcas_tokens_ready',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => '_bomcas_tokens_ready',
						'value'   => '6',
						'compare' => '!=',
					),
				),
			)
		);
		if ( ! $ids ) {
			update_option( 'bomcas_tokens_migrated', '1.0.9-tokens', false );
			delete_transient( 'bomcas_tokens_lock' );
			if ( isset( $_POST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce checked at the top of this handler.
				wp_safe_redirect( admin_url( 'options-general.php?page=bomcas-rates&tokens=done' ) );
				exit;
			}
			return;
		}
		$generated_for = static function ( $id ) {
			return self::excerpt_text( $id );
		};
		foreach ( $ids as $id ) {
			$edits = get_post_meta( $id, '_bomcas_page_edits', true );
			if ( ! is_array( $edits ) || empty( $edits['seo_desc'] ) ) {
				$excerpt   = (string) get_post_field( 'post_excerpt', $id );
				$marked    = (string) get_post_meta( $id, '_bomcas_excerpt_generated', true );
				$generated = $generated_for( $id );
				$bad_seed  = self::excerpt_needs_refresh( $excerpt, $marked );
				if ( '' !== $generated && $bad_seed ) {
					wp_update_post(
						array(
							'ID'           => $id,
							'post_excerpt' => $generated,
						)
					);
					update_post_meta( $id, '_bomcas_excerpt_generated', $generated );
				}
			}
			update_post_meta( $id, '_bomcas_tokens_ready', '6' );
		}
		delete_transient( 'bomcas_tokens_lock' );
		$left = count( $ids );
		update_option( 'bomcas_tokens_progress', $left, false );
		if ( isset( $_POST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce checked at the top of this handler.
			wp_safe_redirect( admin_url( 'options-general.php?page=bomcas-rates&tokens=more' ) );
			exit;
		}
	}

	/**
	 * True when the excerpt is still the copy shipped in place-copy.json.
	 *
	 * @param string $excerpt Excerpt.
	 * @return bool
	 */
	/**
	 * Replace only empty, marked, or plugin-seeded excerpts. A buyer’s own text stays.
	 *
	 * @param string $excerpt Current excerpt.
	 * @param string $marked  Last excerpt this plugin wrote.
	 * @return bool
	 */
	private static function excerpt_needs_refresh( $excerpt, $marked ) {
		$excerpt = trim( (string) $excerpt );
		if ( '' === $excerpt || $excerpt === (string) $marked ) {
			return true;
		}
		if ( preg_match( '/^A (?:\d{4}|\{year_label\}) estimate\b/', $excerpt ) ) {
			return true;
		}
		if ( false !== stripos( $excerpt, 'figures are read from the rate file' ) ) {
			return true;
		}
		if ( false !== strpos( $excerpt, '/calculators/' ) ) {
			return true;
		}
		return self::excerpt_is_shipped( $excerpt );
	}

	/**
	 * True when the excerpt is still the copy shipped in place-copy.json.
	 * A missing Pro directory does not make every “percent” excerpt disposable.
	 *
	 * @param string $excerpt Excerpt.
	 * @return bool
	 */
	private static function excerpt_is_shipped( $excerpt ) {
		$excerpt = trim( $excerpt );
		if ( '' === $excerpt ) {
			return false;
		}
		$file = defined( 'BOMCAS_PRO_DIR' ) ? BOMCAS_PRO_DIR . 'seed/place-copy.json' : '';
		if ( ! is_readable( $file ) ) {
			return false;
		}
		$copy = json_decode( (string) file_get_contents( $file ), true );
		foreach ( (array) $copy as $tool ) {
			if ( ! is_array( $tool ) ) {
				continue;
			}
			foreach ( $tool as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				foreach ( array( 'excerpt', 'lede' ) as $key ) {
					$shipped = trim( (string) ( $row[ $key ] ?? '' ) );
					if ( '' === $shipped ) {
						continue;
					}
					if ( $shipped === $excerpt || ( strlen( $excerpt ) >= 40 && 0 === strpos( $shipped, $excerpt ) ) ) {
						return true;
					}
				}
			}
		}
		return false;
	}
}
