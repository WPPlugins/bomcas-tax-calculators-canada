<?php
/**
 * Long-form guide under a calculator. Figures come from the shipped rate files
 * and from guide-examples.json, which is produced by the same engines the page runs.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Jurisdiction and hub guides.
 */
class Bomcas_Guide {

	/**
	 * @param int $id Page id.
	 * @return string
	 */
	public static function html( $id ) {
		// Rendered on the request. 1.0.5 does not write this HTML into post_content.
		// Pages without _bomcas_calc_managed get nothing. A saved Extra sections edit
		// is applied by Bomcas_Calc_Copy::render() and replaces this default.
		if ( ! get_post_meta( $id, '_bomcas_calc_managed', true ) ) {
			return '';
		}
		$tool  = (string) get_post_meta( $id, '_bomcas_calc_tool', true );
		$place = (string) get_post_meta( $id, '_bomcas_calc_place_name', true );
		if ( '' === $place ) {
			$slug     = (string) get_post_field( 'post_name', $id );
			$authored = class_exists( 'Bomcas_Ca_Pages' ) ? Bomcas_Ca_Pages::topic( $slug ) : '';
			if ( '' === $authored && class_exists( 'Bomcas_Ca_Pages' ) ) {
				$authored = Bomcas_Ca_Pages::hub( $slug );
			}
			if ( '' !== $authored ) {
				return self::finish( $authored );
			}
			return self::finish( self::hub( $id, $tool ) );
		}
		$code = self::code_for( $place, $tool );
		if ( '' === $code ) {
			return '';
		}
		if ( 0 === strpos( $tool, 'us-' ) ) {
			return '';
		}
		return self::finish( self::ca( $id, $tool, $code, $place ) );
	}

	/**
	 * Fill tokens after the guide HTML is built.
	 *
	 * @param string $html HTML.
	 * @return string
	 */
	private static function finish( $html ) {
		return class_exists( 'Bomcas_Tokens' ) ? Bomcas_Tokens::replace( $html ) : $html;
	}

	/**
	 * @param string $place Place name.
	 * @param string $tool  Tool id.
	 * @return string
	 */
	private static function code_for( $place, $tool ) {
		if ( 0 === strpos( $tool, 'us-' ) ) {
			return '';
		}
		$map = array(
			'Alberta'                   => 'AB',
			'British Columbia'          => 'BC',
			'Manitoba'                  => 'MB',
			'New Brunswick'             => 'NB',
			'Newfoundland and Labrador' => 'NL',
			'Northwest Territories'     => 'NT',
			'Nova Scotia'               => 'NS',
			'Nunavut'                   => 'NU',
			'Ontario'                   => 'ON',
			'Prince Edward Island'      => 'PE',
			'Quebec'                    => 'QC',
			'Saskatchewan'              => 'SK',
			'Yukon'                     => 'YT',
		);
		return isset( $map[ $place ] ) ? $map[ $place ] : '';
	}

	/**
	 * @return array
	 */
	private static function examples() {
		static $data = null;
		if ( null === $data ) {
			$live = class_exists( 'Bomcas_Tokens' ) ? Bomcas_Tokens::example_pack() : array();
			if ( $live ) {
				$data = $live;
			} else {
				$path = BOMCAS_CALC_DIR . 'seed/guide-examples.json';
				$raw  = is_readable( $path ) ? json_decode( file_get_contents( $path ), true ) : array();
				$data = is_array( $raw ) ? $raw : array();
			}
		}
		return $data;
	}

	/**
	 * @param float $n Amount.
	 * @return string
	 */
	private static function money( $n ) {
		return '$' . number_format( (float) $n, 2 );
	}

	/**
	 * @param float $rate Decimal or already a percent if > 1.
	 * @return string
	 */
	private static function pct( $rate ) {
		$n = (float) $rate;
		if ( $n <= 1 ) {
			$n *= 100;
		}
		return rtrim( rtrim( number_format( $n, 3, '.', '' ), '0' ), '.' ) . '%';
	}

	/**
	 * @param int    $id   Page id.
	 * @param string $tool Tool id.
	 * @return string
	 */
	private static function hub( $id, $tool ) {
		$children       = get_pages(
			array(
				'parent'      => $id,
				'sort_column' => 'post_title',
				'post_status' => 'publish',
			)
		);
		$title          = get_the_title( $id );
		$slug           = (string) get_post_field( 'post_name', $id );
		$place_children = 0;
		foreach ( $children as $child ) {
			if ( '' !== (string) get_post_meta( $child->ID, '_bomcas_calc_place_name', true ) ) {
				++$place_children;
			}
		}
		$html = '<section class="bomcas-guide">';
		if ( $place_children < 1 ) {
			$html .= '<h2>' . esc_html( sprintf( 'How to use the %s calculator', $title ) ) . '</h2>';
			$html .= self::single_calculator( $slug, $title );
			$html .= '</section>';
			return $html;
		}
		$html .= '<h2>' . esc_html( sprintf( 'How to use the %s pages', $title ) ) . '</h2>';
		$html .= '<p>' . esc_html( $title . ' is the index for this calculator. Open one place. The arithmetic stays in your browser. A result is stored on this site only if you later ask for an email or a PDF. Each place page walks that place’s own rates and worked examples for {year_label}.' ) . '</p>';
		if ( 'calculators' === $slug ) {
			$html .= '<p>Each Canadian province and territory has its own page under the tool you picked.</p>';
		}
		$html .= '<p>Pick the place where you live or where the payroll, the sale, or the property is taxed. Do not average two places. A person who moved during the year, or a sale that is taxed where the buyer takes delivery, needs the place the statute names, not the place that is merely convenient. The index does not file a return and it does not replace the notice of assessment.</p>';
		$html .= '<h2>How to read a ' . esc_html( $title ) . ' result</h2>';
		$html .= '<p>' . esc_html( 'Start with the province or territory, then the amount, then the date or the pay frequency the form asks for. The ' . $title . ' pages do not infer a city or a credit you did not type. If a line is blank, the engine refused to invent a zero. Write the blank down as “not calculated” and read the note under the result before you quote the number to a client or to a family member.' ) . '</p>';
		$html .= '<p>' . esc_html( 'Compare two ' . $title . ' pages only when the inputs match. A {sample:income.0} wage is not the same test as an {sample:income.1} wage, and a tax-included price is not the same test as a price before tax. The worked examples on each page name the inputs. If your inputs differ, run the form. Do not scale the example by a percentage in your head, because brackets, credits, and caps are not flat. The ' . $title . ' index is the map. It is not the calculation, and it is not a combined return for every place listed under it.' ) . '</p>';
		$html .= '<p>' . esc_html( 'Keep the source link with the number. The ' . $title . ' set was checked in {updated} against the rate file shipped in the plugin. A bill that passes after that date is not guessed.' ) . '</p>';
		$html .= '<p>' . esc_html( 'Use one ' . $title . ' page per question. If you are comparing a move, open both places and run the same wage, price, or pay in each form. Write down the inputs. The index will not remember them for you. A printed report uses the numbers on the screen at that moment, including any local rate or trade-in box you changed after the worked example.' ) . '</p>';
		$html .= '<p>' . esc_html( 'The ' . $title . ' pages share a method and do not share a result. Brackets, sales-tax labels, pension rules, and transfer-tax tables are read from the place named on the page. If a place is missing from the list, this plugin does not have a published table for it, and it will not fill the gap with a neighbouring place.' ) . '</p>';
		$faqs  = array();
		foreach ( $children as $child ) {
			$place      = (string) get_post_meta( $child->ID, '_bomcas_calc_place_name', true );
			$child_tool = (string) get_post_meta( $child->ID, '_bomcas_calc_tool', true );
			if ( '' === $place ) {
				$html .= '<h2>' . esc_html( get_the_title( $child ) ) . '</h2>';
				$html .= '<p><a href="' . esc_url( get_permalink( $child ) ) . '">' . esc_html( get_the_title( $child ) ) . '</a></p>';
				continue;
			}
			$code  = self::code_for( $place, $child_tool ? $child_tool : $tool );
			$blurb = self::hub_blurb( $child_tool ? $child_tool : $tool, $code, $place );
			$html .= '<h2>' . esc_html( $place ) . '</h2>';
			$html .= '<p>' . esc_html( $blurb ) . ' <a href="' . esc_url( get_permalink( $child ) ) . '">Open the ' . esc_html( $place ) . ' calculator</a>.</p>';
		}
		$faqs[] = array(
			'q' => 'Does the ' . $title . ' index calculate a combined bill?',
			'a' => 'No. Open one jurisdiction. The index only points at the pages that run the engine.',
		);
		$faqs[] = array(
			'q' => 'Where do the worked examples come from?',
			'a' => 'They are outputs of the same engine the form uses, for the wages, prices, or pay amounts printed beside them.',
		);
		$faqs[] = array(
			'q' => 'Can I rely on this index as a filing position?',
			'a' => 'No. It is an estimate for {year_label} published rates. Credits, residency, and local add-ons that the form does not ask for are omitted.',
		);
		$faqs[] = array(
			'q' => 'What should I open after I have a number?',
			'a' => 'Read the worked example on the page, then run the form with your own figures. The calculator does not send what you typed.',
		);
		$faqs[] = array(
			'q' => 'Which year is this index about?',
			'a' => 'The {year_label} rate year shipped with the plugin, checked in {updated}. A later statute is not guessed.',
		);
		$html  .= self::catalog( $slug, $tool );
		$html  .= self::faqs( $faqs );
		if ( function_exists( 'bomcas_calc_show_credit' ) && bomcas_calc_show_credit() ) {
			$html .= '<p><a href="' . esc_url( 'https://bomcas.ca' ) . '">BOMCAS Canada</a></p>';
		}
		$html .= '</section>';
		return $html;
	}

	/**
	 * @param string $title Hub title.
	 * @return string
	 */
	/**
	 * A calculator that is not split into province pages.
	 *
	 * @param string $slug  Page slug.
	 * @param string $title Title.
	 * @return string
	 */
	private static function single_calculator( $slug, $title ) {
		$note = $title . ' is one calculator, not a list of province pages. What you type stays in the browser. The result is an estimate from the rate file for {year_label}, checked {updated}. It is not a filing and it does not replace a notice of assessment.';
		if ( 'fhsa' === $slug && defined( 'BOMCAS_PRO_DIR' ) && is_readable( BOMCAS_PRO_DIR . 'seed/household.json' ) ) {
			$data   = json_decode( (string) file_get_contents( BOMCAS_PRO_DIR . 'seed/household.json' ), true );
			$annual = isset( $data['fhsa']['annual'] ) ? '$' . number_format( (float) $data['fhsa']['annual'] ) : '';
			$life   = isset( $data['fhsa']['lifetime'] ) ? '$' . number_format( (float) $data['fhsa']['lifetime'] ) : '';
			$note   = 'FHSA stands for a first home savings account. The annual room in the rate file is ' . $annual . ' and the lifetime limit is ' . $life . '. There is no separate page for each province. The form uses the federal limits in that file. What you type stays in the browser. This is an estimate for {year_label}, checked {updated}, not a filing.';
		}
		return '<p>' . esc_html( $note ) . '</p>';
	}

	/**
	 * @param string $title Hub title.
	 * @return string
	 */
	private static function hub_method( $title ) {
		$bits = array(
			'Open a jurisdiction page before you quote a figure from the ' . $title . ' index. The index names the example. The page shows the bands and the questions.',
			'Write the amount, the place, and the date next to every ' . $title . ' number you reuse. A number without those three facts cannot be checked later.',
			'Do not add two ' . $title . ' examples together. They are alternative inputs, not slices of one return.',
			'The ' . $title . ' form does not know a spouse, a dependant, or a donation. Leaving them out is deliberate. A zero was not typed for you.',
			'If the ' . $title . ' result is blank, the engine refused to invent a value. Read the note. Do not type zero into a spreadsheet and carry on.',
			'The ' . $title . ' pages name the year and the place next to every worked figure. Use those three facts when you quote a number.',
			'Print the ' . $title . ' report from the button on the page after you have typed your own inputs. The worked example is only a check that the engine still matches the rate file.',
			'Use the contact page when the ' . $title . ' estimate is for a real filing. The calculator will not send the amounts you typed.',
			'A ' . $title . ' page for one place does not inherit a credit from the page next to it. First-time claims, local rates, and private sales are boxes, not defaults.',
			'Read the source link on the ' . $title . ' page before you copy a bracket into a letter. The letter should name the year {year_label} and the place.',
			'The ' . $title . ' index is longer than a single jurisdiction page because it has to point at every place without pretending the places are interchangeable.',
		);
		$html = '<h2>Working rules for the ' . esc_html( $title ) . ' index</h2>';
		foreach ( $bits as $bit ) {
			$html .= '<p>' . esc_html( $bit . ' This ' . $title . ' rule is part of the index, not a credit, a deduction, or a due date.' ) . '</p>';
		}
		return $html;
	}

	/**
	 * One paragraph per jurisdiction, even when the pages are not child posts.
	 *
	 * @param string $slug Page slug.
	 * @param string $tool Tool meta.
	 * @return string
	 */
	private static function catalog( $slug, $tool ) {
		$known = array(
			'income-tax'         => 'income-tax',
			'sales-tax'          => 'sales-tax',
			'payroll-deductions' => 'payroll-deductions',
			'salary-after-tax'   => 'salary-after-tax',
			'vehicle-purchase'   => 'vehicle-purchase',
			'vehicle'            => 'vehicle-purchase',
			'paycheck'           => 'payroll-deductions',
			'land-transfer-tax'  => 'land-transfer-tax',
		);
		$use   = isset( $known[ $slug ] ) ? $known[ $slug ] : $tool;
		if ( '' === $use || 'us' === $slug || 'calculators' === $slug || 0 === strpos( (string) $use, 'us-' ) ) {
			return '';
		}
		$places = self::ca_names();
		$html   = '<h2>Every place in this ' . esc_html( str_replace( '-', ' ', $use ) ) . ' set</h2>';
		$html  .= '<p>' . esc_html( 'The figures in this list are the worked examples from the ' . str_replace( '-', ' ', $use ) . ' engine, not a second calculator. Open the place when you need the bands, the deadlines, and the questions. Do not add the examples across places. A person is taxed in one place at a time for the purpose of this index.' ) . '</p>';
		foreach ( $places as $code => $name ) {
			if ( 'land-transfer-tax' === $use && ! in_array( $code, array( 'BC', 'ON' ), true ) ) {
				continue;
			}
			$html .= '<p>' . esc_html( self::hub_blurb( $use, $code, $name ) ) . '</p>';
		}
		return $html;
	}

	/**
	 * @return array<string,string>
	 */
	private static function ca_names() {
		return array(
			'AB' => 'Alberta',
			'BC' => 'British Columbia',
			'MB' => 'Manitoba',
			'NB' => 'New Brunswick',
			'NL' => 'Newfoundland and Labrador',
			'NT' => 'Northwest Territories',
			'NS' => 'Nova Scotia',
			'NU' => 'Nunavut',
			'ON' => 'Ontario',
			'PE' => 'Prince Edward Island',
			'QC' => 'Quebec',
			'SK' => 'Saskatchewan',
			'YT' => 'Yukon',
		);
	}


	/**
	 * @param string $tool  Tool id.
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @return string
	 */
	private static function hub_blurb( $tool, $code, $place ) {
		$ex = self::examples();
		if ( 0 === strpos( $tool, 'us-' ) ) {
			return '';
		}
		$pack = $ex['ca'][ $code ] ?? array();
		if ( 'sales-tax' === $tool ) {
			$s = $pack['sales'][0] ?? array();
			return 'A ' . self::money( $s['amount'] ?? 0 ) . ' taxable supply in ' . $place . ' draws ' . self::money( $s['tax'] ?? 0 ) . ' of sales tax at the {year_label} rates on this page.';
		}
		if ( 'payroll-deductions' === $tool ) {
			$p = $pack['payroll'][0] ?? array();
			return 'A ' . self::money( $p['gross'] ?? 0 ) . ' biweekly cheque in ' . $place . ' has an estimated net of ' . self::money( $p['net'] ?? 0 ) . ' on the 9 {updated} pay date used for the example.';
		}
		if ( 'salary-after-tax' === $tool ) {
			$s = $pack['salary'][0] ?? array();
			return 'A ' . self::money( $s['amount'] ?? 0 ) . ' salary in ' . $place . ' has an estimated tax of ' . self::money( $s['tax'] ?? 0 ) . ' and an estimated net of ' . self::money( $s['net'] ?? 0 ) . '.';
		}
		if ( 'vehicle-purchase' === $tool ) {
			$v = $pack['vehicle'][0] ?? array();
			return 'A dealer sale at ' . self::money( $v['price'] ?? 0 ) . ' in ' . $place . ' shows tax of ' . self::money( $v['tax'] ?? 0 ) . ' with the trade-in left off.';
		}
		if ( 'land-transfer-tax' === $tool ) {
			$l = $pack['land'][0] ?? null;
			if ( ! $l ) {
				return $place . ' does not have a land-transfer page in this set. British Columbia and Ontario are the two places with a published table.';
			}
			return 'A ' . self::money( $l['price'] ) . ' purchase in ' . $place . ', with no first-time claim, shows transfer tax of ' . self::money( $l['tax'] ) . '.';
		}
		$i = $pack['income'][0] ?? array();
		return 'Employment income of ' . self::money( $i['employment'] ?? 0 ) . ' in ' . $place . ' produces an estimated ' . self::money( $i['totalTax'] ?? 0 ) . ' of federal and territorial or provincial tax on this page, after the basic personal amounts the engine applies.';
	}

	/**
	 * @param int    $id    Page.
	 * @param string $tool  Tool.
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @return string
	 */
	private static function ca( $id, $tool, $code, $place ) {
		unset( $id );
		$year     = bomcas_calc_current_year();
		$prov     = $year['jurisdictions'][ $code ] ?? array();
		$ex       = self::examples()['ca'][ $code ] ?? array();
		$authored = class_exists( 'Bomcas_Ca_Pages' ) ? Bomcas_Ca_Pages::place( $tool, $code ) : '';
		if ( '' === $authored ) {
			return '';
		}
		return $authored . self::worked_json( self::ca_cases( $tool, $code, $ex ) );
	}

	/**
	 * @param string $tool  Tool.
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @param array  $prov  Jurisdiction.
	 * @param array  $year  Year pack.
	 * @param array  $ex    Examples.
	 * @return string
	 */
	private static function ca_body( $tool, $code, $place, $prov, $year, $ex ) {
		if ( 'sales-tax' === $tool ) {
			return self::ca_sales( $code, $place, $ex );
		}
		if ( 'payroll-deductions' === $tool ) {
			return self::ca_payroll( $code, $place, $ex, $year );
		}
		if ( 'salary-after-tax' === $tool ) {
			return self::ca_salary( $code, $place, $ex );
		}
		if ( 'vehicle-purchase' === $tool ) {
			return self::ca_vehicle( $code, $place, $ex );
		}
		if ( 'land-transfer-tax' === $tool ) {
			return self::ca_land( $code, $place, $ex );
		}
		return self::ca_income( $code, $place, $prov, $year, $ex );
	}

	/**
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @param array  $prov  Row.
	 * @param array  $year  Year.
	 * @param array  $ex    Examples.
	 * @return string
	 */
	private static function ca_income( $code, $place, $prov, $year, $ex ) {
		$brackets = (array) ( $prov['brackets'] ?? array() );
		$first    = isset( $brackets[0]['rate'] ) ? self::pct( $brackets[0]['rate'] ) : __( 'the rate on file', 'bomcas-tax-calculators-canada' );
		$top      = $brackets ? self::pct( $brackets[ count( $brackets ) - 1 ]['rate'] ) : __( 'the rate on file', 'bomcas-tax-calculators-canada' );
		$bpa      = self::money( $prov['bpa']['max'] ?? 0 );
		$html     = '<h2>How ' . esc_html( $place ) . ' income tax works in {year_label}</h2>';
		$html    .= '<p>' . esc_html( $place . ' taxes {year_label} taxable income in ' . count( $brackets ) . ' bands. The first band is ' . $first . ' and the top band is ' . $top . '. The basic personal amount used on this estimate is ' . $bpa . ', and the credit rate on that amount is ' . self::pct( $prov['credit_rate'] ?? 0 ) . '. Federal tax is calculated on the same taxable income and then added. This page is an estimate for employment income with no spouse, no dependants, and no donations.' ) . '</p>';
		$html    .= self::feature_paragraph( $code, $place, $prov );
		$html    .= '<h2>The ' . esc_html( $place ) . ' bands, in plain words</h2>';
		$prev     = 0;
		foreach ( $brackets as $i => $band ) {
			$ceiling = null === $band['upto'] ? null : (float) $band['upto'];
			$width   = null === $ceiling ? null : $ceiling - $prev;
			$rate    = self::pct( $band['rate'] );
			if ( null === $ceiling ) {
				$sentence = 'Band ' . ( $i + 1 ) . ' in ' . $place . ' has no ceiling. Every dollar of taxable income above ' . self::money( $prev ) . ' is taxed at ' . $rate . '. A full extra ' . self::money( 10000 ) . ' in this band adds ' . self::money( 10000 * (float) $band['rate'] ) . ' of territorial or provincial tax before credits.';
			} else {
				$sentence = 'Band ' . ( $i + 1 ) . ' in ' . $place . ' covers taxable income from ' . self::money( $prev ) . ' up to ' . self::money( $ceiling ) . '. That slice is ' . self::money( $width ) . ' wide and the rate is ' . $rate . '. Filling the whole slice, and nothing above it, produces ' . self::money( $width * (float) $band['rate'] ) . ' of tax before the basic personal amount credit.';
			}
			$html .= '<p>' . esc_html( $sentence ) . '</p>';
			$prev  = (float) $ceiling;
		}
		$html .= '<h2>Worked examples for ' . esc_html( $place ) . '</h2>';
		$html .= '<p>Each example is employment income only. The engine deducts Canada Pension Plan or Quebec Pension Plan and employment insurance the way the {year_label} payroll factors in the rate file require, then applies the basic personal amounts. Donations, tuition, and a spouse amount are not in these totals.</p>';
		foreach ( (array) ( $ex['income'] ?? array() ) as $row ) {
			$html .= '<p>' . esc_html(
				'At ' . self::money( $row['employment'] ) . ' of employment income in ' . $place
				. ', taxable income on this estimate is ' . self::money( $row['taxableIncome'] )
				. '. Federal tax is ' . self::money( $row['federalTax'] )
				. ' and the ' . $place . ' tax is ' . self::money( $row['provincialTax'] )
				. '. Together that is ' . self::money( $row['totalTax'] )
				. ', so the amount left after those two taxes is ' . self::money( $row['afterTax'] )
				. '. The federal basic personal amount used in the credit is ' . self::money( $row['federalBpa'] )
				. ' and the ' . $place . ' basic personal amount is ' . self::money( $row['provincialBpa'] ) . '.'
			) . '</p>';
		}
		return $html;
	}

	/**
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @param array  $prov  Row.
	 * @return string
	 */
	private static function feature_paragraph( $code, $place, $prov ) {
		if ( 'BC' === $code && ! empty( $prov['bc_reduction'] ) ) {
			$r = $prov['bc_reduction'];
			return '<p>' . esc_html( 'British Columbia also has a tax reduction. The full reduction is ' . self::money( $r['basic'] ) . ' when income is at or under ' . self::money( $r['threshold'] ) . '. It shrinks at ' . self::pct( $r['rate'] ) . ' of income above that line and is gone by ' . self::money( $r['upper'] ) . '. A payroll withholding rate used after {deadline:bc_payroll_july} is not the same figure as this return rate of ' . self::pct( $prov['brackets'][0]['rate'] ) . '.' ) . '</p>';
		}
		if ( 'ON' === $code ) {
			return '<p>Ontario adds a surtax once basic Ontario tax passes the thresholds in the rate file, and it adds the Ontario Health Premium on a separate scale that starts above {money:ON.health.0.upto}. The health premium is not a bracket. A wage that looks lightly taxed in the first band can still owe {money:ON.health.1.cap}, {money:ON.health.2.cap}, {money:ON.health.3.cap}, or {money:ON.health.4.cap} of premium. The examples on this page include both.</p>';
		}
		if ( 'QC' === $code ) {
			return '<p>Quebec residents get the federal abatement, so the federal tax on this page is lower than the federal tax on the same wage in another place. Quebec Pension Plan, the Quebec Parental Insurance Plan, and the workers deduction of up to {money:workers.max} are in the employment-income examples. The solidarity credit is not.</p>';
		}
		if ( ! empty( $prov['bpa']['phase_from'] ) ) {
			return '<p>' . esc_html( $place . ' phases the basic personal amount down once income passes ' . self::money( $prov['bpa']['phase_from'] ) . ', and the amount can reach ' . self::money( $prov['bpa']['min'] ) . ' by ' . self::money( $prov['bpa']['phase_to'] ) . '. A wage under the start of that phase still gets the full ' . self::money( $prov['bpa']['max'] ) . '.' ) . '</p>';
		}
		return '<p>' . esc_html( $place . ' keeps the basic personal amount at ' . self::money( $prov['bpa']['max'] ?? 0 ) . ' across the incomes this form estimates. There is no phase-out in the shipped {year_label} row. Other credits that depend on age, a spouse, or a dependant are omitted rather than shown as zero.' ) . '</p>';
	}

	/**
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @param array  $ex    Examples.
	 * @return string
	 */
	private static function ca_sales( $code, $place, $ex ) {
		$sales = bomcas_calc_seed( 'sales-tax.json' );
		$row   = null;
		foreach ( (array) ( $sales['jurisdictions'][ $code ] ?? array() ) as $segment ) {
			if ( Bomcas_Tokens::iso( 'example_on' ) >= $segment['from'] && Bomcas_Tokens::iso( 'example_on' ) <= $segment['to'] ) {
				$row = $segment;
				break;
			}
		}
		$row   = $row ? $row : array();
		$html  = '<h2>How sales tax works in ' . esc_html( $place ) . ' in {year_label}</h2>';
		$html .= '<p>' . esc_html( $place . ' shows the label ' . ( $row['label'] ?? '' ) . ' on a standard taxable supply. GST is ' . self::pct( $row['gst'] ?? 0 ) . ', PST or RST is ' . self::pct( $row['pst'] ?? 0 ) . ', HST is ' . self::pct( $row['hst'] ?? 0 ) . ', and QST is ' . self::pct( $row['qst'] ?? 0 ) . '. The combined rate on this page is ' . self::pct( $row['gst'] + $row['pst'] + $row['hst'] + $row['qst'] ) . '. Vehicles, real property, and exempt supplies are outside this form.' ) . '</p>';
		if ( 'NS' === $code ) {
			$html .= '<p>Nova Scotia’s HST rate changes on {date:NS.sales.latest_from} inside the shipped table. A date you type before that day uses the earlier rate. A date on or after it uses the later rate. The examples below use that change date, not a British Columbia payroll date.</p>';
		}
		$html .= '<h2>Worked examples for ' . esc_html( $place ) . '</h2>';
		foreach ( (array) ( $ex['sales'] ?? array() ) as $sample ) {
			$html .= '<p>' . esc_html( 'On a ' . self::money( $sample['amount'] ) . ' taxable amount in ' . $place . ' dated {date:' . $code . '.sales.latest_from}, GST is ' . self::money( $sample['gst'] ) . ', the provincial sales tax is ' . self::money( $sample['pst'] ) . ', HST is ' . self::money( $sample['hst'] ) . ', and QST is ' . self::money( $sample['qst'] ) . '. Tax is ' . self::money( $sample['tax'] ) . ' and the amount with tax is ' . self::money( $sample['total'] ) . '.' ) . '</p>';
		}
		$html .= '<p>Type the amount before tax. If you already collected a tax-included price, switch the form to remove tax. The rate is not a tip and it is not a municipal hotel tax.</p>';
		return $html;
	}

	/**
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @param array  $ex    Examples.
	 * @param array  $year  Year.
	 * @return string
	 */
	private static function ca_payroll( $code, $place, $ex, $year ) {
		$spec  = $year['cpp_ei'];
		$html  = '<h2>How a ' . esc_html( $place ) . ' paycheque is estimated for {year_label}</h2>';
		$html .= '<p>' . esc_html( 'The ' . $place . ' cheque uses the {year_label} pension and employment-insurance factors in the rate file. The YMPE is ' . self::money( $spec['ympe'] ) . ' and the employee pension rate outside Quebec is ' . self::pct( $spec['rate'] ) . '. Employment insurance is ' . self::pct( $spec['ei_rate'] ) . ' up to insurable earnings of ' . self::money( $spec['ei_max_earnings'] ) . '.' ) . '</p>';
		if ( 'BC' === $code ) {
			$html .= '<p>A British Columbia pay date on or after {deadline:bc_payroll_july} uses the July withholding rate of {rate:BC.payroll_july} on the provincial portion of the cheque. The annual return still uses the {rate:BC.income.first} first band. Do not copy the July withholding rate onto the T1.</p>';
		}
		if ( 'QC' === $code ) {
			$html .= '<p>Quebec uses the Quebec Pension Plan employee rate of ' . esc_html( self::pct( $spec['qpp_rate'] ) ) . ', Quebec employment insurance of ' . esc_html( self::pct( $spec['ei_qc_rate'] ) ) . ', and QPIP. Regular-payment income-tax withholding follows TP-1015.F-V. A gratuity or a retroactive payment is not calculated, and the net is left blank rather than printed as zero when withholding is unavailable.</p>';
		}
		$html .= '<h2>Worked paycheques in ' . esc_html( $place ) . '</h2>';
		foreach ( (array) ( $ex['payroll'] ?? array() ) as $row ) {
			$pension = 'QC' === $code ? 'QPP ' . self::money( $row['qpp'] ) : 'CPP ' . self::money( $row['cpp'] );
			$html   .= '<p>' . esc_html( 'Gross ' . self::money( $row['gross'] ) . ' paid 26 times in ' . $place . ' on 9 {updated}: federal withholding ' . self::money( $row['federalPeriod'] ) . ', local withholding ' . self::money( $row['provincialPeriod'] ) . ', ' . $pension . ', EI ' . self::money( $row['ei'] ) . ', QPIP ' . self::money( $row['qpip'] ) . '. Estimated net ' . self::money( $row['net'] ) . '.' ) . '</p>';
		}
		return $html;
	}

	/**
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @param array  $ex    Examples.
	 * @return string
	 */
	private static function ca_salary( $code, $place, $ex ) {
		$html  = '<h2>Take-home pay in ' . esc_html( $place ) . ' for {year_label}</h2>';
		$html .= '<p>' . esc_html( 'This ' . $place . ' page turns an annual salary into an estimate of income tax plus the employee pension and employment-insurance amounts, then subtracts them from the salary. It is not a pay stub. It does not know your claim code, a taxable benefit, or a union dues deduction.' ) . '</p>';
		$html .= '<h2>Worked salaries in ' . esc_html( $place ) . '</h2>';
		foreach ( (array) ( $ex['salary'] ?? array() ) as $row ) {
			$html .= '<p>' . esc_html( 'A ' . self::money( $row['amount'] ) . ' salary in ' . $place . ' has estimated tax of ' . self::money( $row['tax'] ) . '. The estimated amount left for the year is ' . self::money( $row['net'] ) . ', which is about ' . self::money( $row['perPeriod'] ) . ' if you spread that net over 26 pays. Benefits and bonuses are not in the salary you typed.' ) . '</p>';
		}
		return $html;
	}

	/**
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @param array  $ex    Examples.
	 * @return string
	 */
	private static function ca_vehicle( $code, $place, $ex ) {
		$html  = '<h2>Vehicle tax in ' . esc_html( $place ) . '</h2>';
		$html .= '<p>' . esc_html( 'The ' . $place . ' vehicle page prices a dealer sale with no trade-in. GST or HST is the federal portion. The provincial portion follows that place’s vehicle rule, which is not always the general sales-tax rate. Luxury tax, a zero-emission reduction, and a vehicle brought in from abroad are not calculated. Private sales use a different base when the form offers that switch.' ) . '</p>';
		$html .= '<h2>Worked dealer prices in ' . esc_html( $place ) . '</h2>';
		foreach ( (array) ( $ex['vehicle'] ?? array() ) as $row ) {
			$html .= '<p>' . esc_html( 'A dealer price of ' . self::money( $row['price'] ) . ' in ' . $place . ' on 9 {updated}, passenger vehicle, no trade-in, shows GST or HST to the seller of ' . self::money( $row['gstToSeller'] ) . ' and a provincial vehicle tax of ' . self::money( $row['provincial'] ) . '. The tax line the engine returns is ' . self::money( $row['tax'] ) . '.' ) . '</p>';
			$ratio = ( $row['price'] > 0 ) ? ( (float) $row['tax'] / (float) $row['price'] ) : 0;
			$html .= '<p>' . esc_html( 'On that ' . self::money( $row['price'] ) . ' ' . $place . ' dealer sale, tax is ' . self::pct( $ratio ) . ' of the price you typed. A trade-in, a private sale, or a wholesale value can change the ' . $place . ' base. Luxury tax is still not in the ' . self::money( $row['tax'] ) . '.' ) . '</p>';
		}
		return $html;
	}

	/**
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @param array  $ex    Examples.
	 * @return string
	 */
	private static function ca_land( $code, $place, $ex ) {
		$html = '<h2>Property transfer tax in ' . esc_html( $place ) . '</h2>';
		if ( empty( $ex['land'] ) ) {
			return $html . '<p>This plugin publishes a land-transfer table only for British Columbia and Ontario. ' . esc_html( $place ) . ' is not given a silent zero.</p>';
		}
		$html .= '<p>' . esc_html( $place . ' transfer tax below is the general table with no first-time exemption. A first-time claim changes the result and is a box on the form, not an assumption. Foreign-buyer taxes are not in these two prices.' ) . '</p>';
		$html .= self::land_bands( $code, $place );
		$html .= '<h2>Worked purchase prices in ' . esc_html( $place ) . '</h2>';
		foreach ( $ex['land'] as $row ) {
			$html .= '<p>' . esc_html( 'A price of ' . self::money( $row['price'] ) . ' in ' . $place . ', residential, no first-time claim, shows transfer tax of ' . self::money( $row['tax'] ) . '. Municipal land transfer tax, where a city charges its own tax, is not added unless the form has a separate line for it.' ) . '</p>';
		}
		return $html;
	}

	/**
	 * Property-transfer bands from the Pro rate file, when that file is installed.
	 *
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @return string
	 */
	private static function land_bands( $code, $place ) {
		if ( ! defined( 'BOMCAS_PRO_DIR' ) ) {
			return '';
		}
		$file = BOMCAS_PRO_DIR . 'seed/firm-tools.json';
		if ( ! is_readable( $file ) ) {
			return '';
		}
		$data = json_decode( (string) file_get_contents( $file ), true );
		$land = isset( $data['land'] ) && is_array( $data['land'] ) ? $data['land'] : array();
		$key  = 'BC' === $code ? 'bc' : ( 'ON' === $code ? 'ontario' : '' );
		if ( '' === $key || empty( $land[ $key ] ) ) {
			return '';
		}
		$bands = 'BC' === $code ? (array) ( $land['bc']['general'] ?? array() ) : (array) ( $land['ontario']['single_family'] ?? array() );
		if ( ! $bands ) {
			return '';
		}
		$html = '<h2>' . esc_html( $place ) . ' general transfer bands</h2>';
		$prev = 0.0;
		foreach ( $bands as $i => $band ) {
			$upto = $band['upto'];
			$rate = self::pct( $band['rate'] );
			if ( null === $upto ) {
				$line = 'Band ' . ( $i + 1 ) . ' of the ' . $place . ' general table has no ceiling. The value above ' . self::money( $prev ) . ' is charged at ' . $rate . '.';
			} else {
				$line = 'Band ' . ( $i + 1 ) . ' of the ' . $place . ' general table runs from ' . self::money( $prev ) . ' to ' . self::money( $upto ) . ' at ' . $rate . '.';
			}
			$html .= '<p>' . esc_html( $line ) . '</p>';
			$prev  = null === $upto ? $prev : (float) $upto;
		}
		if ( 'ON' === $code && ! empty( $land['toronto']['after_2026_04_01'] ) ) {
			$html .= '<p>Toronto charges its own municipal land transfer tax on top of the Ontario table. The Toronto bands are a separate schedule. Leave the Toronto box off and that municipal tax stays out of the worked prices.</p>';
		}
		if ( 'BC' === $code ) {
			$html .= '<p>The British Columbia further tax on a residential value above the threshold in the form is not part of these general bands. A first-time exemption, when the box is on and the buyer qualifies, is also outside these bands.</p>';
		}
		return $html;
	}

	/**
	 * @param string $place Name.
	 * @param array  $ex    Examples.
	 * @param string $tool  Tool.
	 * @return string
	 */
	private static function ca_deadlines( $place, $ex, $tool ) {
		$sample = $ex['income'][1]['totalTax'] ?? ( $ex['sales'][0]['tax'] ?? 0 );
		$html   = '<h2>Filing and payment timing for ' . esc_html( $place ) . '</h2>';
		$html  .= '<p>' . esc_html( 'For a ' . $place . ' resident, the individual return for the {year_label} tax year is due {deadline:balance_due}. If you or your spouse carried on a business in {year_label}, the return is due {deadline:file_self_employed}, but a balance owing is still due {deadline:balance_due}. Instalments, when the usual test says you must pay them, fall on {deadline:instalments}. An estimate of ' . self::money( $sample ) . ' on this page is not itself an instalment voucher.' ) . '</p>';
		if ( 'sales-tax' === $tool ) {
			$html .= '<p>' . esc_html( 'GST/HST filing frequency is assigned on the program account. ' . $place . ' provincial sales tax, where it is separate from HST, has its own return cycle. This calculator does not look up your assigned cycle. Confirm the due date on the government account before you remit the ' . self::money( $ex['sales'][0]['tax'] ?? 0 ) . ' example or any real collection.' ) . '</p>';
		}
		if ( 'payroll-deductions' === $tool ) {
			$html .= '<p>' . esc_html( 'Payroll remittance dates depend on whether the payer is a regular, quarterly, or accelerated remitter. The ' . self::money( $ex['payroll'][0]['net'] ?? 0 ) . ' net on the sample ' . $place . ' cheque does not tell you which class you are in.' ) . '</p>';
		}
		return $html;
	}

	/**
	 * @param string $place Name.
	 * @return string
	 */
	private static function limits( $place, $tool = '' ) {
		$html = '<h2>What the ' . esc_html( $place ) . ' calculator will not do</h2>';
		if ( 'sales-tax' === $tool || 'vehicle-purchase' === $tool ) {
			$html .= '<p>' . esc_html( 'The ' . $place . ' sales form does not price a hotel tax, a luxury tax, or a municipal levy. It does not look up a reseller exemption. What you type stays in the browser unless you later ask for an email or a PDF.' ) . '</p>';
		} elseif ( 'payroll-deductions' === $tool ) {
			$html .= '<p>' . esc_html( 'The ' . $place . ' payroll form does not know your remitter type, a taxable benefit, or a bonus taxed as a gratuity. What you type stays in the browser unless you later ask for an email or a PDF.' ) . '</p>';
		} elseif ( 'salary-after-tax' === $tool ) {
			$html .= '<p>' . esc_html( 'The ' . $place . ' salary form does not know a claim code, union dues, or how many pays your employer actually runs. What you type stays in the browser unless you later ask for an email or a PDF.' ) . '</p>';
		} else {
			$html .= '<p>' . esc_html( 'The ' . $place . ' income-tax estimate omits a spouse, a dependant, tuition, medical expenses, donations, the Canada child benefit, and the GST credit. Those amounts are left out. They are not entered as zero. What you type stays in the browser unless you later ask for an email or a PDF.' ) . '</p>';
		}
		return $html;
	}

	/**
	 * @param string $tool  Tool.
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @return string
	 */
	/**
	 * Visible comparison. Numbers come from the rate row. Nothing is collapsed.
	 *
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @param string $tool  Tool.
	 * @param array  $row   Rate row.
	 * @return string
	 */
	private static function visible_difference( $code, $place, $tool, $row ) {
		$brackets = array();
		if ( isset( $row['brackets'] ) && is_array( $row['brackets'] ) ) {
			$brackets = $row['brackets'];
		} elseif ( isset( $row['single'] ) && is_array( $row['single'] ) ) {
			$brackets = $row['single'];
		}
		$count = count( $brackets );
		$first = isset( $brackets[0]['rate'] ) ? self::pct( $brackets[0]['rate'] ) : '';
		$last  = $brackets ? $brackets[ $count - 1 ] : array();
		$top   = isset( $last['rate'] ) ? self::pct( $last['rate'] ) : '';
		$near  = array(
			'AB' => 'British Columbia and Saskatchewan',
			'BC' => 'Alberta and the Yukon',
			'MB' => 'Saskatchewan and Ontario',
			'NB' => 'Nova Scotia and Quebec',
			'NL' => 'the other Atlantic provinces',
			'NT' => 'Yukon and Nunavut',
			'NS' => 'New Brunswick and Prince Edward Island',
			'NU' => 'the Northwest Territories',
			'ON' => 'Quebec and Manitoba',
			'PE' => 'New Brunswick and Nova Scotia',
			'QC' => 'Ontario and New Brunswick',
			'SK' => 'Alberta and Manitoba',
			'YT' => 'British Columbia and the Northwest Territories',
		);
		$other = isset( $near[ $code ] ) ? $near[ $code ] : 'a neighbouring jurisdiction';
		$topic = str_replace( '-', ' ', $tool );
		$open  = $first ? ' The first published rate is ' . $first . '.' : '';
		$close = $top ? ' The top published rate is ' . $top . '.' : '';
		$html  = '<h2>' . esc_html( $place . ' compared with its neighbours' ) . '</h2>';
		$html .= '<p>' . esc_html( $place . ' shows ' . $count . ' steps on this ' . $topic . ' page.' . $open . $close . ' That schedule is not the one used in ' . $other . '.' ) . '</p>';
		$html .= '<p>For a ' . esc_html( $place ) . ' resident, a balance owing for {year_label} is due {deadline:balance_due}. A ' . esc_html( $place ) . ' filer who carried on a business files by {deadline:file_self_employed}. The balance is still due on the earlier date.</p>';
		$html .= '<p>' . esc_html( 'Use the official source link on this ' . $place . ' page before you copy a figure into a letter. The letter should name ' . $place . ' and the year on the file, not a rate taken from ' . $other . '.' ) . '</p>';
		return $html;
	}

	private static function ca_links( $tool, $code, $place ) {
		$slug    = sanitize_title( $place );
		$related = array(
			'income-tax'         => array( 'sales-tax', 'payroll-deductions', 'salary-after-tax' ),
			'sales-tax'          => array( 'income-tax', 'vehicle-purchase' ),
			'payroll-deductions' => array( 'salary-after-tax', 'income-tax' ),
			'salary-after-tax'   => array( 'payroll-deductions', 'income-tax' ),
			'vehicle-purchase'   => array( 'sales-tax', 'income-tax' ),
			'land-transfer-tax'  => array( 'income-tax', 'sales-tax' ),
		);
		$html    = '<h2>Related ' . esc_html( $place ) . ' calculators and services</h2><ul>';
		foreach ( (array) ( $related[ $tool ] ?? array() ) as $other ) {
			if ( 'land-transfer-tax' === $other && ! in_array( $code, array( 'BC', 'ON' ), true ) ) {
				continue;
			}
			$path  = 'calculators/' . $other . '/' . $slug;
			$html .= '<li><a href="' . esc_url( home_url( '/' . $path . '/' ) ) . '">' . esc_html( ucwords( str_replace( '-', ' ', $other ) ) . ' in ' . $place ) . '</a></li>';
		}
		if ( function_exists( 'bomcas_calc_show_credit' ) && bomcas_calc_show_credit() ) {
			$html .= '<li><a href="' . esc_url( 'https://bomcas.ca' ) . '">BOMCAS Canada</a></li>';
		}
		$html .= '</ul>';
		$html .= '<h2>Official sources for ' . esc_html( $place ) . '</h2><ul>';
		$html .= '<li><a href="https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html">Canada Revenue Agency individual income tax rates</a></li>';
		$html .= '<li><a href="https://www.canada.ca/en/revenue-agency/services/tax/individuals/topics/important-dates-individuals.html">Canada Revenue Agency important dates for individuals</a></li>';
		if ( 'sales-tax' === $tool ) {
			$html .= '<li><a href="https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/gst-hst-businesses/charge-collect-which-rate.html">Canada Revenue Agency GST/HST rates</a></li>';
		}
		$html .= '</ul>';
		return $html;
	}

	/**
	 * @param string $tool  Tool.
	 * @param string $code  Code.
	 * @param string $place Name.
	 * @param array  $prov  Row.
	 * @param array  $ex    Examples.
	 * @return array
	 */
	private static function ca_faqs( $tool, $code, $place, $prov, $ex ) {
		if ( 'sales-tax' === $tool ) {
			$sample = $ex['sales'][0] ?? array();
			return array(
				array(
					'q' => 'What sales tax does ' . $place . ' add to ' . self::money( $sample['amount'] ?? 0 ) . '?',
					'a' => 'On the date in the worked example, the engine adds ' . self::money( $sample['tax'] ?? 0 ) . '. A different date can select a different row when the table has more than one.',
				),
				array(
					'q' => 'Does ' . $place . ' sales tax include a hotel or municipal levy?',
					'a' => 'No. Those are left off. Type the amount before tax unless you switch the form to a tax-included price.',
				),
			);
		}
		if ( 'payroll-deductions' === $tool ) {
			$row = $ex['payroll'][0] ?? array();
			return array(
				array(
					'q' => 'What is the estimated net on a ' . self::money( $row['gross'] ?? 0 ) . ' ' . $place . ' cheque?',
					'a' => 'For 26 pays and the claim the example uses, the engine prints ' . self::money( $row['net'] ?? 0 ) . '. A bonus or a gratuity is not in that net.',
				),
				array(
					'q' => 'Does the ' . $place . ' net tell me my remitter type?',
					'a' => 'No. Regular, quarterly, and accelerated remitters have different due dates. The calculator does not look up your payroll account.',
				),
			);
		}
		if ( 'salary-after-tax' === $tool ) {
			$row = $ex['salary'][0] ?? array();
			return array(
				array(
					'q' => 'What is left from a ' . self::money( $row['amount'] ?? 0 ) . ' salary in ' . $place . '?',
					'a' => 'The worked salary leaves ' . self::money( $row['net'] ?? 0 ) . ' after the estimated tax and employee contributions, about ' . self::money( $row['perPeriod'] ?? 0 ) . ' across 26 pays.',
				),
				array(
					'q' => 'Does the ' . $place . ' salary page know my claim code?',
					'a' => 'No. Union dues, a taxable benefit, and a bonus are not subtracted. Run the form with your own salary rather than scaling the example.',
				),
			);
		}
		if ( 'vehicle-purchase' === $tool ) {
			$row = $ex['vehicle'][0] ?? array();
			return array(
				array(
					'q' => 'How much tax is on a ' . self::money( $row['price'] ?? 0 ) . ' dealer sale in ' . $place . '?',
					'a' => 'With no trade-in, the engine returns ' . self::money( $row['tax'] ?? 0 ) . '. Luxury tax and a zero-emission reduction are not in that figure.',
				),
				array(
					'q' => 'Is a private sale in ' . $place . ' the same as a dealer sale?',
					'a' => 'No. The form uses a different base when you switch to a private sale. The worked lines are dealer sales.',
				),
			);
		}
		if ( 'land-transfer-tax' === $tool ) {
			$row = $ex['land'][0] ?? array();
			return array(
				array(
					'q' => 'What transfer tax does ' . $place . ' show on ' . self::money( $row['price'] ?? 0 ) . '?',
					'a' => 'With no first-time exemption, the engine shows ' . self::money( $row['tax'] ?? 0 ) . '. Turning the first-time box on changes the result.',
				),
				array(
					'q' => 'Does the ' . $place . ' figure include a municipal land transfer tax?',
					'a' => 'Only when the form has a separate line and you are in a city that charges one. The worked prices leave that line off.',
				),
			);
		}
		$sample = $ex['income'][1] ?? array();
		return array(
			array(
				'q' => 'What does ' . $place . ' charge on ' . self::money( $sample['employment'] ?? 0 ) . ' of employment income?',
				'a' => 'The worked line shows ' . self::money( $sample['totalTax'] ?? 0 ) . ' of federal and ' . $place . ' tax together, and ' . self::money( $sample['afterTax'] ?? 0 ) . ' left after those two taxes.',
			),
			array(
				'q' => 'Is the ' . $place . ' basic personal amount phased out?',
				'a' => empty( $prov['bpa']['phase_from'] )
					? 'The shipped {year_label} row keeps it at ' . self::money( $prov['bpa']['max'] ?? 0 ) . ' for the incomes this form estimates.'
					: 'Yes. It starts to fall after ' . self::money( $prov['bpa']['phase_from'] ) . '.',
			),
		);
	}

	/**
	 * @param string $tool Tool.
	 * @param string $code Code.
	 * @param array  $ex   Examples.
	 * @return array
	 */
	private static function ca_cases( $tool, $code, $ex ) {
		if ( 'sales-tax' === $tool ) {
			return array(
				'engine' => 'ca-sales',
				'code'   => $code,
				'cases'  => $ex['sales'] ?? array(),
			);
		}
		if ( 'payroll-deductions' === $tool ) {
			return array(
				'engine' => 'ca-payroll',
				'code'   => $code,
				'cases'  => $ex['payroll'] ?? array(),
			);
		}
		if ( 'salary-after-tax' === $tool ) {
			return array(
				'engine' => 'ca-salary',
				'code'   => $code,
				'cases'  => $ex['salary'] ?? array(),
			);
		}
		if ( 'vehicle-purchase' === $tool ) {
			return array(
				'engine' => 'ca-vehicle',
				'code'   => $code,
				'cases'  => $ex['vehicle'] ?? array(),
			);
		}
		if ( 'land-transfer-tax' === $tool ) {
			return array(
				'engine' => 'ca-land',
				'code'   => $code,
				'cases'  => $ex['land'] ?? array(),
			);
		}
		return array(
			'engine' => 'ca-income',
			'code'   => $code,
			'cases'  => $ex['income'] ?? array(),
		);
	}


	/**
	 * @param string $place  Name.
	 * @param float  $figure A real example from this page.
	 * @return string
	 */
	private static function mistakes( $place, $figure, $tool = '' ) {
		$money = self::money( $figure );
		$line  = 'The figure ' . $money . ' on this ' . $place . ' page is one worked input. It is not the amount every resident owes, and it is not a filing.';
		if ( 'sales-tax' === $tool ) {
			$line = 'Do not treat ' . $money . ' as the sales tax on every ' . $place . ' purchase. It belongs to the price printed beside it. A tax-included price typed into the before-tax box will not match it.';
		} elseif ( 'vehicle-purchase' === $tool ) {
			$line = 'Do not copy ' . $money . ' onto a different ' . $place . ' vehicle. A trade-in, a private sale, or a luxury tax changes the base, and none of those were assumed in that worked price.';
		} elseif ( 'payroll-deductions' === $tool ) {
			$line = 'Do not multiply ' . $money . ' by the number of pays and call it the ' . $place . ' annual return. Withholding on a cheque and the tax on the return are different calculations.';
		} elseif ( 'salary-after-tax' === $tool ) {
			$line = 'Do not read ' . $money . ' as take-home pay for every ' . $place . ' salary. Benefits, a bonus, and a different claim code are not in that worked salary.';
		} elseif ( 'land-transfer-tax' === $tool ) {
			$line = 'Do not quote ' . $money . ' as the transfer tax on every ' . $place . ' purchase. A first-time exemption and a municipal tax are not in that worked price.';
		}
		return '<h2>A mistake to avoid in ' . esc_html( $place ) . '</h2><p>' . esc_html( $line ) . '</p>';
	}

	/**
	 * @param array $faqs Questions.
	 * @return string
	 */
	private static function faqs( $faqs ) {
		$html     = '<h2>Frequently asked questions</h2>';
		$entities = array();
		foreach ( $faqs as $faq ) {
			$html      .= '<details class="faq-item"><summary>' . esc_html( $faq['q'] ) . '</summary><div class="faq-body"><p>' . esc_html( $faq['a'] ) . '</p></div></details>';
			$entities[] = array(
				'@type'          => 'Question',
				'name'           => $faq['q'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $faq['a'],
				),
			);
		}
		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $entities,
		);
		bomcas_calc_queue_json( $schema, 'application/ld+json' );
		return $html;
	}

	/**
	 * @param array $payload Cases.
	 * @return string
	 */
	private static function worked_json( $payload ) {
		bomcas_calc_queue_json( $payload, 'application/json', 'bomcas-worked' );
		return '';
	}
}
