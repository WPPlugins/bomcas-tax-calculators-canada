<?php
/**
 * Jurisdiction pages: unique body copy built from the published rates.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ranking copy and buyer edits.
 */
class Bomcas_Calc_Copy {

	/**
	 * Buyer overrides. Plugin updates do not write this option.
	 *
	 * @param int $id Page id.
	 * @return array
	 */
	public static function edits( $id ) {
		$saved = get_post_meta( $id, '_bomcas_page_edits', true );
		return is_array( $saved ) ? $saved : array();
	}

	/**
	 * @param int $id Page id.
	 * @return string
	 */
	public static function render( $id ) {
		$edits = self::edits( $id );
		if ( ! empty( $edits['disabled'] ) ) {
			return '';
		}
		$html = self::default_html( $id );
		if ( ! empty( $edits['body'] ) ) {
			$html = Bomcas_Tokens::replace( wp_kses_post( $edits['body'] ) );
		}
		$base = (string) get_post_meta( $id, '_bomcas_page_edit_base', true );
		$now  = md5( self::default_html( $id ) );
		if ( '' !== $base && ! hash_equals( $base, $now ) && empty( $edits['body'] ) ) {
			$html = '<p class="bomcas-note">' . esc_html__( 'A newer explanation is available for this page. Your saved text is what visitors read.', 'bomcas-tax-calculators-canada' ) . '</p>' . $html;
		}
		return $html;
	}

	/**
	 * @param int $id Page id.
	 * @return string
	 */
	public static function default_html( $id ) {
		if ( class_exists( 'Bomcas_Guide' ) ) {
			return Bomcas_Guide::html( $id );
		}
		$facts = self::facts( $id );
		if ( ! $facts ) {
			return '';
		}
		$blocks = self::blocks( $facts );
		$html   = '';
		foreach ( $blocks['sections'] as $section ) {
			$html .= '<h2>' . esc_html( $section['heading'] ) . '</h2>';
			foreach ( $section['paragraphs'] as $paragraph ) {
				$html .= '<p>' . esc_html( $paragraph ) . '</p>';
			}
		}
		if ( $blocks['rows'] ) {
			$html .= '<table class="bomcas-table"><caption>' . esc_html( $blocks['caption'] ) . '</caption><thead><tr><th>' . esc_html__( 'Item', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Figure', 'bomcas-tax-calculators-canada' ) . '</th></tr></thead><tbody>';
			foreach ( $blocks['rows'] as $row ) {
				$html .= '<tr><td>' . esc_html( $row[0] ) . '</td><td>' . esc_html( $row[1] ) . '</td></tr>';
			}
			$html .= '</tbody></table>';
			$html .= '<p>' . esc_html( $blocks['source_line'] ) . '</p>';
		}
		/* translators: %s: a name, amount, count, or year. */
		$html .= '<h2>' . esc_html( sprintf( __( 'Questions about %s', 'bomcas-tax-calculators-canada' ), $facts['name'] ) ) . '</h2>';
		foreach ( $blocks['faqs'] as $faq ) {
			$html .= '<h3>' . esc_html( $faq['q'] ) . '</h3><p>' . esc_html( $faq['a'] ) . '</p>';
		}
		$html .= '<h2>' . esc_html__( 'Related calculators', 'bomcas-tax-calculators-canada' ) . '</h2><ul class="bomcas-nav">';
		foreach ( $blocks['links'] as $link ) {
			$html .= '<li><a href="' . esc_url( $link['url'] ) . '">' . esc_html( $link['label'] ) . '</a></li>';
		}
		$html .= '</ul>';
		$html .= '<p>' . esc_html( $blocks['reviewed'] ) . '</p>';
		if ( class_exists( 'Bomcas_Guide' ) && empty( self::edits( $id )['body'] ) ) {
			$html .= Bomcas_Guide::html( $id );
		}
		return class_exists( 'Bomcas_Tokens' ) ? Bomcas_Tokens::replace( $html ) : $html;
	}

	/**
	 * @param int $id Page id.
	 * @return array|null
	 */
	public static function facts( $id ) {
		$tool  = (string) get_post_meta( $id, '_bomcas_calc_tool', true );
		$place = (string) get_post_meta( $id, '_bomcas_calc_place_name', true );
		if ( '' === $place ) {
			return null;
		}
		$slug = sanitize_title( $place );
		if ( 0 === strpos( $tool, 'us-' ) ) {
			return null;
		}
		$names    = array(
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
		$code     = isset( $names[ $place ] ) ? $names[ $place ] : '';
		$pack     = bomcas_calc_seed( 'personal-tax-current.json' );
		$row      = ( $code && isset( $pack['years'][ Bomcas_Tokens::key() ]['jurisdictions'][ $code ] ) ) ? $pack['years'][ Bomcas_Tokens::key() ]['jurisdictions'][ $code ] : array();
		$brackets = array();
		$floor    = 0;
		foreach ( (array) ( $row['brackets'] ?? array() ) as $bracket ) {
			$brackets[] = array(
				'over' => $floor,
				'rate' => (float) $bracket['rate'],
				'upto' => $bracket['upto'],
			);
			$floor      = null === $bracket['upto'] ? $floor : (float) $bracket['upto'];
		}
		$sales_pack = bomcas_calc_seed( 'sales-tax.json' );
		$sales_rate = 0.0;
		if ( $code && isset( $sales_pack['jurisdictions'][ $code ][0] ) ) {
			$sales_row  = $sales_pack['jurisdictions'][ $code ][0];
			$sales_rate = (float) ( $sales_row['gst'] ?? 0 ) + (float) ( $sales_row['pst'] ?? 0 ) + (float) ( $sales_row['hst'] ?? 0 ) + (float) ( $sales_row['qst'] ?? 0 );
		}
		return array(
			'name'        => $place,
			'slug'        => $slug,
			'code'        => $code ? $code : $place,
			'country'     => 'CA',
			'tool'        => $tool,
			'none'        => false,
			'brackets'    => $brackets,
			'first'       => $brackets ? (float) $brackets[0]['rate'] : 0,
			'top'         => $brackets ? (float) $brackets[ count( $brackets ) - 1 ]['rate'] : 0,
			'sales'       => $sales_rate,
			'standard'    => isset( $row['bpa']['max'] ) ? $row['bpa']['max'] : null,
			'source'      => 'https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html',
			'source_name' => 'Canada Revenue Agency individual income tax rates for {year_label}',
		);
	}

	/**
	 * @param array $facts Facts.
	 * @return array
	 */
	public static function blocks( $facts ) {
		$name     = $facts['name'];
		$count    = count( $facts['brackets'] );
		$first    = rtrim( rtrim( number_format( $facts['first'] * 100, 2, '.', '' ), '0' ), '.' );
		$top      = rtrim( rtrim( number_format( $facts['top'] * 100, 2, '.', '' ), '0' ), '.' );
		$sales    = rtrim( rtrim( number_format( $facts['sales'] * 100, 2, '.', '' ), '0' ), '.' );
		$wage     = 40000 + ( abs( crc32( $facts['code'] . '|' . $facts['tool'] ) ) % 7 ) * 10000;
		$sections = self::tool_sections( $facts, $wage, $first, $top, $sales, $count );
		$rows     = self::tool_rows( $facts, $count, $first, $top, $sales );
		$faqs     = self::tool_faqs( $facts, $first, $count );
		$prefix   = 'US' === $facts['country'] ? '/calculators/us/' : '/calculators/';
		$links    = array(
			array(
				'url'   => home_url( '/calculators/' ),
				'label' => __( 'All calculators', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'url'   => home_url( $prefix . ( 'US' === $facts['country'] ? 'income-tax/' : 'income-tax/' ) . $facts['slug'] . '/' ),
				/* translators: %s: a name, amount, count, or year. */
				'label' => sprintf( __( '%s income tax', 'bomcas-tax-calculators-canada' ), $name ),
			),
			array(
				'url'   => home_url( $prefix . 'sales-tax/' . $facts['slug'] . '/' ),
				/* translators: %s: a name, amount, count, or year. */
				'label' => sprintf( __( '%s sales tax', 'bomcas-tax-calculators-canada' ), $name ),
			),
		);
		if ( 'CA' === $facts['country'] ) {
			$links[] = array(
				'url'   => home_url( '/products/bomcas-tax-payroll-calculators-canada/' ),
				'label' => __( 'Get the plugin', 'bomcas-tax-calculators-canada' ),
			);
		}
		return array(
			'sections'    => $sections,
			'rows'        => $rows,
			/* translators: %s: a name, amount, count, or year. */
			'caption'     => sprintf( __( '%s figures used on this page', 'bomcas-tax-calculators-canada' ), $name ),
			'source_line' => $name . ' ' . __( 'source was reviewed {updated}.', 'bomcas-tax-calculators-canada' ),
			'faqs'        => $faqs,
			'links'       => $links,
			/* translators: %s: a name, amount, count, or year. */
			'reviewed'    => sprintf( __( 'Last reviewed {updated} for %s.', 'bomcas-tax-calculators-canada' ), $name ),
		);
	}

	/**
	 * A few visible paragraphs for this tool. Numbers come from the rate row.
	 *
	 * @param array  $facts Facts.
	 * @param int    $wage  Sample wage.
	 * @param string $first First rate.
	 * @param string $top   Top rate.
	 * @param string $sales Sales rate.
	 * @param int    $count Bracket count.
	 * @return array
	 */
	private static function tool_sections( $facts, $wage, $first, $top, $sales, $count ) {
		$name = $facts['name'];
		$tool = (string) $facts['tool'];
		$ded  = null === $facts['standard'] ? '' : '$' . number_format( (float) $facts['standard'] );
		if ( 'sales-tax' === $tool ) {
			$paragraphs = array(
				$name . ' sales tax on this page uses a combined rate of ' . $sales . ' percent for {year_label}. City and district add-ons are not looked up. Type a local rate only when you already know it.',
				'A wage-tax bracket is a different bill. ' . $name . ' income tax, where the table has one, starts at ' . $first . ' percent and the top published rate is ' . $top . ' percent. Do not add that income tax to a shop receipt.',
			);
		} elseif ( 'vehicle-purchase' === $tool ) {
			$paragraphs = array(
				'A dealer sale in ' . $name . ' is priced with the ' . $sales . ' percent combined sales rate. Title, registration, and a documentation fee are not inside that rate. A trade-in changes the taxed price only when you say so on the form.',
				'The general merchandise examples on the ' . $name . ' sales page are not a vehicle invoice. Private sales can use a different base. Luxury tax is not calculated here.',
			);
		} elseif ( 'payroll-deductions' === $tool ) {
			$paragraphs = array(
				'A ' . $name . ' paycheque estimate takes income tax, the Canada Pension Plan or the Quebec Pension Plan, and employment insurance or the Quebec Parental Insurance Plan off the gross. It is not the annual return. The {year_label} income-tax table for ' . $name . ' still starts at ' . $first . ' percent.',
				'Remittance dates depend on the payer’s class. The net on a sample cheque does not tell you whether you are a regular or an accelerated remitter.',
			);
		} elseif ( 'salary-after-tax' === $tool ) {
			$paragraphs = array(
				'Take-home pay in ' . $name . ' starts from an annual salary, subtracts the {year_label} income tax and the employee pension and employment-insurance amounts, and divides the remainder by the number of pays you pick. A claim code, a taxable benefit, and a bonus are not in the sample.',
				$name . ' income tax on the published table starts at ' . $first . ' percent and reaches ' . $top . ' percent. Those bands are applied to the salary, not to a sales-tax receipt.',
			);
		} elseif ( 'land-transfer-tax' === $tool ) {
			$paragraphs = array(
				$name . ' property transfer tax is a separate table from income tax and from sales tax. The first-time exemption is a box you turn on. It is not assumed. A municipal land transfer tax is added only when the form has a line for it.',
				'Income tax in ' . $name . ' still starts at ' . $first . ' percent on the income-tax page. That rate is not the transfer-tax rate.',
			);
		} else {
			$deduction  = $ded ? ' The amount subtracted before the rate, when the row publishes one flat figure, is ' . $ded . '.' : '';
			$paragraphs = array(
				$name . ' taxes {year_label} wages in ' . $count . ' published steps. The first rate is ' . $first . ' percent and the top rate is ' . $top . ' percent.' . $deduction . ' Federal tax is calculated on the same income and kept separate.',
				'Credits that are not in the rate table — a spouse, a dependant, tuition, medical expenses, donations — are left out. They are not filled in as zero. Sales tax at ' . $sales . ' percent is not part of this wage estimate.',
			);
		}
		return array(
			array(
				'heading'    => $name . ' — how this page is different',
				'paragraphs' => $paragraphs,
			),
		);
	}

	/**
	 * @param array  $facts Facts.
	 * @param int    $count Bracket count.
	 * @param string $first First rate.
	 * @param string $top   Top rate.
	 * @param string $sales Sales rate.
	 * @return array
	 */
	private static function tool_rows( $facts, $count, $first, $top, $sales ) {
		$name = $facts['name'];
		$tool = (string) $facts['tool'];
		if ( in_array( $tool, array( 'sales-tax', 'vehicle-purchase' ), true ) ) {
			return array(
				array( $name . ' combined sales rate', $sales . '%' ),
			);
		}
		$rows = array(
			array( $name . ' published steps', (string) $count ),
			array( $name . ' first rate', $first . '%' ),
			array( $name . ' top rate', $top . '%' ),
		);
		if ( null !== $facts['standard'] && ! in_array( $tool, array( 'payroll-deductions', 'salary-after-tax' ), true ) ) {
			$rows[] = array( $name . ' deduction or basic amount', '$' . number_format( (float) $facts['standard'] ) );
		}
		return $rows;
	}

	/**
	 * @param array  $facts Facts.
	 * @param string $first First rate.
	 * @param int    $count Bracket count.
	 * @return array
	 */
	private static function tool_faqs( $facts, $first, $count ) {
		$name = $facts['name'];
		$tool = (string) $facts['tool'];
		if ( in_array( $tool, array( 'sales-tax', 'vehicle-purchase' ), true ) ) {
			return array(
				array(
					'q' => 'Does the ' . $name . ' page look up a local rate?',
					'a' => 'No. A city, county, or district rate is included only when you type it.',
				),
				array(
					'q' => 'Where do the ' . $name . ' sales figures come from?',
					'a' => 'The {year_label} rate file shipped with the plugin, reviewed {updated}.',
				),
			);
		}
		if ( in_array( $tool, array( 'payroll-deductions', 'salary-after-tax' ), true ) ) {
			return array(
				array(
					'q' => 'Is a ' . $name . ' paycheque the same as the annual return?',
					'a' => 'No. Withholding on a cheque and the tax on the return can use different rates.',
				),
				array(
					'q' => 'Does the ' . $name . ' pay estimate store what I type?',
					'a' => 'No. It runs in the browser unless you separately ask for an email or a PDF.',
				),
			);
		}
		return array(
			array(
				'q' => 'Does ' . $name . ' tax wages in {year_label}?',
				'a' => $facts['none'] ? $name . ' does not tax these wages. Federal income tax is still calculated.' : $name . ' starts at ' . $first . ' percent across ' . $count . ' published steps.',
			),
			array(
				'q' => 'Where do the ' . $name . ' income-tax figures come from?',
				'a' => 'The {year_label} rate file shipped with the plugin, reviewed {updated}.',
			),
		);
	}

	/**
	 * Short factual sentences. Each one starts with the place so sibling pages do not share a passage.
	 *
	 * @param array  $facts   Facts.
	 * @param int    $wage    Sample wage.
	 * @param string $example Worked example.
	 * @return string[]
	 */
	private static function lines( $facts, $wage ) {
		$name  = $facts['name'];
		$sales = rtrim( rtrim( number_format( (float) $facts['sales'] * 100, 2, '.', '' ), '0' ), '.' );
		$tax   = 0.0;
		foreach ( $facts['brackets'] as $i => $bracket ) {
			$start = (float) $bracket['over'];
			$end   = isset( $facts['brackets'][ $i + 1 ] ) ? (float) $facts['brackets'][ $i + 1 ]['over'] : $wage;
			if ( $wage <= $start ) {
				break;
			}
			$tax += ( min( $wage, $end ) - $start ) * (float) $bracket['rate'];
		}
		$say      = function ( $rest ) use ( $name ) {
			return $name . ' ' . $rest;
		};
		$tax_word = 'provincial tax';
		$lines    = array(
			$say( 'is a {year_label} tax estimate.' ),
			$say( 'uses the published rate table.' ),
			$say( $facts['none'] ? 'does not tax wages.' : 'does tax wages.' ),
			$say( 'lists ' . count( $facts['brackets'] ) . ' wage steps.' ),
			$say( 'opens at ' . rtrim( rtrim( number_format( $facts['first'] * 100, 2, '.', '' ), '0' ), '.' ) . ' percent.' ),
			$say( 'tops out at ' . rtrim( rtrim( number_format( $facts['top'] * 100, 2, '.', '' ), '0' ), '.' ) . ' percent.' ),
			$say( 'sales tax is ' . $sales . ' percent.' ),
			$say( 'sample wage is $' . number_format( $wage ) . '.' ),
			$say( $tax_word . ' is $' . number_format( $tax, 2 ) . '.' ),
			$say( 'omits city wage tax.' ),
			$say( 'US' === $facts['country'] ? 'omits a ZIP lookup.' : 'omits a postal-code lookup.' ),
			$say( 'lets you type a local rate.' ),
			$say( 'returns are due in April.' ),
			$say( 'extensions do not skip payment.' ),
			$say( 'payroll follows agency dates.' ),
			$say( 'wage slips stay with you.' ),
			$say( 'does not keep what you type.' ),
			$say( 'is not a filed return.' ),
			$say( 'US' === $facts['country'] ? 'federal deduction is {money:us.std.single}.' : 'basic personal amount is separate from the bracket.' ),
			$say( 'federal brackets still apply.' ),
			$say( 'US' === $facts['country'] ? 'head of household is separate.' : 'the personal return is the sample.' ),
			$say( 'US' === $facts['country'] ? 'married filing jointly is separate.' : 'a spouse amount is not estimated here.' ),
			$say( 'single status is the sample.' ),
			$say( 'local sales is optional.' ),
			$say( 'vehicle pages share this rate.' ),
			$say( 'paycheck pages use this wage tax.' ),
			$facts['none'] ? $say( 'wage line stays at zero.' ) : $say( 'wage line uses each step.' ),
			$say( 'credits off the table are omitted.' ),
			$say( 'last reviewed {updated}.' ),
			$say( 'source is cited under the table.' ),
		);
		foreach ( $facts['brackets'] as $index => $bracket ) {
			$rate    = rtrim( rtrim( number_format( (float) $bracket['rate'] * 100, 2, '.', '' ), '0' ), '.' );
			$over    = '$' . number_format( (float) $bracket['over'] );
			$n       = $index + 1;
			$lines[] = $say( 'step ' . $n . ' starts at ' . $over . '.' );
			$lines[] = $say( 'step ' . $n . ' rate is ' . $rate . ' percent.' );
		}
		$federal = class_exists( 'Bomcas_Tokens' ) ? Bomcas_Tokens::fact_lines( $facts['country'] ) : array();
		foreach ( $federal as $line ) {
			$lines[] = $say( $line );
		}
		return $lines;
	}

	private static function example_sentence( $facts, $wage ) {
		$tax       = 0.0;
		$prev_over = 0;
		$brackets  = $facts['brackets'];
		foreach ( $brackets as $i => $bracket ) {
			$start = (float) $bracket['over'];
			$end   = isset( $brackets[ $i + 1 ] ) ? (float) $brackets[ $i + 1 ]['over'] : $wage;
			if ( $wage <= $start ) {
				break;
			}
			$tax      += ( min( $wage, $end ) - $start ) * (float) $bracket['rate'];
			$prev_over = $start;
		}
		unset( $prev_over );
		return sprintf(
			/* translators: 1: place, 2: wage, 3: tax */
			__( 'On a %1$s illustration of %2$s in wages, the provincial brackets on this page produce about %3$s before credits that are not listed.', 'bomcas-tax-calculators-canada' ),
			$facts['name'],
			'$' . number_format( $wage ),
			'$' . number_format( $tax, 2 )
		);
	}

	/**
	 * Visible questions for schema.
	 *
	 * @param int $id Page id.
	 * @return array
	 */
	public static function boot() {
		add_action( 'admin_post_bomcas_page_content', array( __CLASS__, 'save' ) );
		add_filter( 'document_title_parts', array( __CLASS__, 'title' ) );
		add_action( 'wp_head', array( __CLASS__, 'meta' ), 1 );
		add_filter( 'wpseo_title', array( __CLASS__, 'seo_title' ), 100 );
		add_filter( 'wpseo_metadesc', array( __CLASS__, 'seo_desc' ), 100 );
		add_filter( 'wpseo_opengraph_title', array( __CLASS__, 'seo_title' ), 100 );
		add_filter( 'wpseo_opengraph_desc', array( __CLASS__, 'seo_desc' ), 100 );
		add_filter( 'wpseo_twitter_title', array( __CLASS__, 'seo_title' ), 100 );
		add_filter( 'wpseo_twitter_description', array( __CLASS__, 'seo_desc' ), 100 );
		add_filter( 'wpseo_opengraph_site_name', array( __CLASS__, 'og_site_name' ), 100 );
		add_filter( 'wpseo_schema_webpage', array( __CLASS__, 'schema_webpage' ), 100 );
		add_filter( 'wpseo_schema_website', array( __CLASS__, 'schema_website' ), 100 );
		add_filter( 'rank_math/frontend/title', array( __CLASS__, 'seo_title' ) );
		add_filter( 'rank_math/frontend/description', array( __CLASS__, 'seo_desc' ) );
	}

	/**
	 * Editor screen.
	 */
	public static function menu() {
		add_submenu_page(
			'bomcas-calculators-home',
			__( 'Page content', 'bomcas-tax-calculators-canada' ),
			__( 'Page content', 'bomcas-tax-calculators-canada' ),
			'manage_options',
			'bomcas-page-content',
			array( __CLASS__, 'screen' )
		);
	}

	/**
	 * Edit one calculator page. Advanced fields appear when Pro is active.
	 */
	public static function screen() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'posts_per_page' => 30,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Equality lookup for pages this plugin manages.
					array(
						'key'   => '_bomcas_calc_managed',
						'value' => '1',
					),
				),
			)
		);
		if ( isset( $_GET['_wpnonce'] ) ) {
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'bomcas_page_pick' );
		}
		$id    = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;
		$edits = $id ? self::edits( $id ) : array();
		$pro   = defined( 'BOMCAS_PRO_VERSION' );
		Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
		Bomcas_Admin_UI::open(
			array(
				'doc'     => 'page-content',
				'product' => 'BOMCAS Canada',
				'kicker'  => __( 'Calculators · Page content', 'bomcas-tax-calculators-canada' ),
				'badge'   => $pro ? __( 'Pro', 'bomcas-tax-calculators-canada' ) : __( 'Lite', 'bomcas-tax-calculators-canada' ),
				'help'    => __( 'Edits are stored apart from the published default. A plugin update does not replace them. Reset restores the default.', 'bomcas-tax-calculators-canada' ),
				'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'content' ) : array(),
			)
		);
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Choose a page', 'bomcas-tax-calculators-canada' ) . '</h2><ul>';
		foreach ( $pages as $page ) {
			echo '<li><a href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=bomcas-page-content&post=' . $page->ID ), 'bomcas_page_pick' ) ) . '">' . esc_html( get_the_title( $page ) ) . '</a></li>';
		}
		echo '</ul></section>';
		if ( $id ) {
			echo '<section class="bomcas-admin__card"><h2>' . esc_html( get_the_title( $id ) ) . '</h2>';
			echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
			wp_nonce_field( 'bomcas_page_content' );
			echo '<input type="hidden" name="action" value="bomcas_page_content" /><input type="hidden" name="post" value="' . esc_attr( (string) $id ) . '" />';
			if ( class_exists( 'Bomcas_Tokens' ) ) {
				echo wp_kses( Bomcas_Tokens::picker(), bomcas_calc_allowed_html() );
			}
			echo '<p><label>' . esc_html__( 'Heading', 'bomcas-tax-calculators-canada' ) . ' <input class="regular-text" name="h2" value="' . esc_attr( $edits['h2'] ?? '' ) . '" /></label> ' . wp_kses( Bomcas_Admin_UI::tip( __( 'Replaces the heading under the breadcrumb.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
			echo '<p><label>' . esc_html__( 'Intro', 'bomcas-tax-calculators-canada' ) . '<br /><textarea name="intro" rows="4" class="large-text">' . esc_textarea( $edits['intro'] ?? '' ) . '</textarea></label></p>';
			echo '<p><label>' . esc_html__( 'Button label', 'bomcas-tax-calculators-canada' ) . ' <input name="cta" value="' . esc_attr( $edits['cta'] ?? '' ) . '" /></label></p>';
			if ( $pro ) {
				echo '<p><label>' . esc_html__( 'Search title', 'bomcas-tax-calculators-canada' ) . ' <input class="regular-text" name="seo_title" value="' . esc_attr( $edits['seo_title'] ?? '' ) . '" /></label></p>';
				echo '<p><label>' . esc_html__( 'Search description', 'bomcas-tax-calculators-canada' ) . '<br /><textarea name="seo_desc" rows="3" class="large-text">' . esc_textarea( $edits['seo_desc'] ?? '' ) . '</textarea></label></p>';
				echo '<p><label>' . esc_html__( 'Extra sections', 'bomcas-tax-calculators-canada' ) . '<br /><textarea name="body" rows="8" class="large-text">' . esc_textarea( $edits['body'] ?? '' ) . '</textarea></label></p>';
			} else {
				echo '<p>' . esc_html__( 'Search title, description, and extra sections are edited when Pro is active.', 'bomcas-tax-calculators-canada' ) . '</p>';
			}
			echo '<p><a href="' . esc_url( get_permalink( $id ) ) . '">' . esc_html__( 'Preview the page', 'bomcas-tax-calculators-canada' ) . '</a></p>';
			echo '<div class="bomcas-savebar">';
			echo '<button type="submit" class="button button-primary" name="save_content" value="1">' . esc_html__( 'Save page content', 'bomcas-tax-calculators-canada' ) . '</button> ';
			echo '<button type="submit" class="button" name="reset_content" value="1">' . esc_html__( 'Reset to default', 'bomcas-tax-calculators-canada' ) . '</button>';
			echo '</div></form></section>';
		}
		Bomcas_Admin_UI::close();
	}

	/**
	 * Save or reset. The default HTML is not written into the edit record.
	 */
	public static function save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot edit this page.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_page_content' );
		$id = absint( wp_unslash( $_POST['post'] ?? 0 ) );
		if ( isset( $_POST['reset_content'] ) ) {
			delete_post_meta( $id, '_bomcas_page_edits' );
			delete_post_meta( $id, '_bomcas_page_edit_base' );
		} else {
			$edits = array(
				'h2'    => sanitize_text_field( wp_unslash( $_POST['h2'] ?? '' ) ),
				'intro' => sanitize_textarea_field( wp_unslash( $_POST['intro'] ?? '' ) ),
				'cta'   => sanitize_text_field( wp_unslash( $_POST['cta'] ?? '' ) ),
			);
			if ( defined( 'BOMCAS_PRO_VERSION' ) ) {
				$edits['seo_title'] = sanitize_text_field( wp_unslash( $_POST['seo_title'] ?? '' ) );
				$edits['seo_desc']  = sanitize_textarea_field( wp_unslash( $_POST['seo_desc'] ?? '' ) );
				$edits['body']      = wp_kses_post( wp_unslash( $_POST['body'] ?? '' ) );
			}
			update_post_meta( $id, '_bomcas_page_edits', $edits );
			update_post_meta( $id, '_bomcas_page_edit_base', md5( self::default_html( $id ) ) );
		}
		wp_safe_redirect( admin_url( 'admin.php?page=bomcas-page-content&post=' . $id . '&saved=1' ) );
		exit;
	}

	/**
	 * @param array $parts Title parts.
	 * @return array
	 */
	public static function title( $parts ) {
		if ( ! is_page() || ( function_exists( 'bomcas_calc_seo_controls_title' ) && bomcas_calc_seo_controls_title() ) ) {
			return $parts;
		}
		$edits = self::edits( get_queried_object_id() );
		if ( ! empty( $edits['seo_title'] ) ) {
			$parts['title'] = class_exists( 'Bomcas_Tokens' ) ? Bomcas_Tokens::replace( $edits['seo_title'] ) : $edits['seo_title'];
		}
		return $parts;
	}

	/**
	 * @param string $title Title.
	 * @return string
	 */
	public static function seo_title( $title ) {
		if ( ! self::managed_request() ) {
			return $title;
		}
		$edits = self::edits( get_queried_object_id() );
		if ( ! empty( $edits['seo_title'] ) ) {
			return Bomcas_Tokens::replace( $edits['seo_title'] );
		}
		$owner = self::owner_yoast_title();
		if ( '' !== $owner ) {
			return $owner;
		}
		return function_exists( 'bomcas_calc_public_title' ) ? bomcas_calc_public_title( get_the_title() ) : wp_strip_all_tags( get_the_title() );
	}

	/**
	 * @param string $desc Description.
	 * @return string
	 */
	public static function seo_desc( $desc ) {
		if ( ! is_page() ) {
			return $desc;
		}
		$edits = self::edits( get_queried_object_id() );
		if ( ! empty( $edits['seo_desc'] ) ) {
			return Bomcas_Tokens::replace( $edits['seo_desc'] );
		}
		if ( self::managed_request() && function_exists( 'bomcas_calc_meta_desc' ) ) {
			$ours   = bomcas_calc_meta_desc( get_queried_object_id() );
			$stored = (string) get_post_meta( get_queried_object_id(), '_yoast_wpseo_metadesc', true );
			if ( '' !== $stored && $stored !== $ours ) {
				return $stored;
			}
			return $ours;
		}
		return $desc;
	}

	/**
	 * Owner-written Yoast title. The template this plugin writes is not an owner value.
	 *
	 * @return string
	 */
	private static function owner_yoast_title() {
		$stored = (string) get_post_meta( get_queried_object_id(), '_yoast_wpseo_title', true );
		if ( '' === $stored || '%%title%% · BOMCAS Tax Calculators – Canada' === $stored ) {
			return '';
		}
		$post = get_post();
		if ( $post instanceof WP_Post && function_exists( 'wpseo_replace_vars' ) ) {
			return wpseo_replace_vars( $stored, $post );
		}
		return $stored;
	}

	/**
	 * @param string $name Site name.
	 * @return string
	 */
	public static function og_site_name( $name ) {
		return self::managed_request() ? 'BOMCAS Tax Calculators – Canada' : $name;
	}

	/**
	 * @param array $data Schema piece.
	 * @return array
	 */
	public static function schema_webpage( $data ) {
		if ( ! self::managed_request() || ! is_array( $data ) ) {
			return $data;
		}
		$data['name']        = self::seo_title( isset( $data['name'] ) ? (string) $data['name'] : '' );
		$data['description'] = self::seo_desc( isset( $data['description'] ) ? (string) $data['description'] : '' );
		return $data;
	}

	/**
	 * @param array $data Schema piece.
	 * @return array
	 */
	public static function schema_website( $data ) {
		if ( ! self::managed_request() || ! is_array( $data ) ) {
			return $data;
		}
		$data['name'] = 'BOMCAS Tax Calculators – Canada';
		return $data;
	}

	/**
	 * @return bool
	 */
	private static function managed_request() {
		return is_page() && (bool) get_post_meta( get_queried_object_id(), '_bomcas_calc_managed', true );
	}

	/**
	 * Built-in description when no SEO plugin printed one.
	 */
	public static function meta() {
		if ( ! is_page() || defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
			return;
		}
		if ( class_exists( 'Bomcas_Lic_Market' ) && Bomcas_Lic_Market::current() ) {
			return;
		}
		$edits = self::edits( get_queried_object_id() );
		$desc  = '';
		if ( ! empty( $edits['seo_desc'] ) ) {
			$desc = Bomcas_Tokens::replace( $edits['seo_desc'] );
		} elseif ( self::managed_request() && function_exists( 'bomcas_calc_meta_desc' ) ) {
			$desc = bomcas_calc_meta_desc( get_queried_object_id() );
		}
		if ( '' !== $desc ) {
			echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
		}
	}

	public static function faqs( $id ) {
		$facts = self::facts( $id );
		if ( ! $facts ) {
			return array();
		}
		$faqs = self::blocks( $facts )['faqs'];
		if ( ! class_exists( 'Bomcas_Tokens' ) ) {
			return $faqs;
		}
		foreach ( $faqs as $index => $faq ) {
			$faqs[ $index ]['q'] = Bomcas_Tokens::replace( $faq['q'] );
			$faqs[ $index ]['a'] = Bomcas_Tokens::replace( $faq['a'] );
		}
		return $faqs;
	}
}
