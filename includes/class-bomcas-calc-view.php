<?php
/**
 * Calculator markup. Cosmetic text may be filtered; rates and disclosures may not.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HTML for a calculator.
 */
class Bomcas_Calc_View {

	/**
	 * Escape text.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	public static function h( $text ) {
		return function_exists( 'esc_html' ) ? esc_html( $text ) : htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Escape an attribute.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	public static function a( $text ) {
		return function_exists( 'esc_attr' ) ? esc_attr( $text ) : htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Render one calculator.
	 *
	 * @param array $args Arguments.
	 * @return string
	 */
	public static function render( $args ) {
		$calc     = $args['calc'];
		$preset   = isset( $args['presentation'] ) ? $args['presentation'] : array();
		$province = isset( $args['province'] ) ? $args['province'] : '';
		$json     = wp_json_encode( $args['payload'] );
		if ( ! function_exists( 'wp_json_encode' ) ) {
			$json = json_encode( $args['payload'] );
		}
		$layout  = isset( $preset['layout'] ) && 'stack' === $preset['layout'] ? 'stack' : 'split';
		$style   = self::css_vars( $preset );
		$title   = isset( $preset['hero_title'] ) && $preset['hero_title'] !== '' ? $preset['hero_title'] : $args['title'];
		$help    = isset( $preset['help_html'] ) ? $preset['help_html'] : '';
		$allowed = self::allowed_html();
		ob_start();
		static $bomcas_form_seq = 0;
		++$bomcas_form_seq;
		$form_id       = 1 === $bomcas_form_seq ? 'bomcas-calc-form' : 'bomcas-calc-form-' . $bomcas_form_seq;
		$bomcas_format = class_exists( 'Bomcas_I18n' ) ? Bomcas_I18n::format( $calc ) : array(
			'locale'   => 'en-CA',
			'currency' => 'CAD',
		);
		echo '<div id="' . esc_attr( $form_id ) . '" class="bomcas-calc" data-bomcas-calc data-calc="' . esc_attr( $calc ) . '" data-locale="' . esc_attr( $bomcas_format['locale'] ) . '" data-currency="' . esc_attr( $bomcas_format['currency'] ) . '" data-bomcas-payload="' . esc_attr( (string) $json ) . '" style="' . esc_attr( $style ) . '">';
		if ( class_exists( 'Bomcas_I18n' ) ) {
			echo wp_kses( Bomcas_I18n::switcher(), bomcas_calc_allowed_html() );
			echo wp_kses( Bomcas_I18n::quebec_note( $province ), bomcas_calc_allowed_html() );
		}
		echo '<div class="bomcas-print-head"><strong>' . esc_html( $title ) . '</strong></div>';
		if ( $help ) {
			echo '<div class="bomcas-help">' . wp_kses( $help, $allowed ) . '</div>';
		}
		echo '<div class="bomcas-layout is-' . esc_attr( $layout ) . '">';
		echo '<div class="bomcas-panel">';
		echo wp_kses( self::fields( $calc, $province, $args ), $allowed );
		echo '<div class="bomcas-actions">';
		echo '<button type="button" class="btn btn-secondary" data-action="print">' . esc_html( self::label( $preset, 'print', __( 'Print / Save PDF report', 'bomcas-tax-calculators-canada' ) ) ) . '</button>';
		echo '<button type="button" class="bomcas-ghost" data-action="pdf">' . esc_html__( 'Download PDF', 'bomcas-tax-calculators-canada' ) . '</button>';
		echo '<button type="button" class="bomcas-ghost" data-action="link">' . esc_html( self::label( $preset, 'link', __( 'Copy link', 'bomcas-tax-calculators-canada' ) ) ) . '</button>';
		echo '<button type="button" class="bomcas-ghost" data-action="reset">' . esc_html( self::label( $preset, 'reset', __( 'Reset', 'bomcas-tax-calculators-canada' ) ) ) . '</button>';
		echo '</div><p data-link-note class="bomcas-note"></p>';
		echo '<p class="bomcas-note">' . esc_html__( 'What you type stays in this browser. It is not sent to the server.', 'bomcas-tax-calculators-canada' ) . '</p>';
		echo '</div><div class="bomcas-panel"><h2>' . esc_html( self::label( $preset, 'results', __( 'Tax estimate', 'bomcas-tax-calculators-canada' ) ) ) . '</h2>';
		echo '<p class="bomcas-note">' . esc_html__( 'These figures are an estimate. They are not a tax return.', 'bomcas-tax-calculators-canada' ) . '</p>';
		echo '<dl data-results class="bomcas-result">' . wp_kses( self::server_results( $calc, $province, $args ), $allowed ) . '</dl>';
		echo '<p data-steps class="bomcas-note"></p>';
		if ( class_exists( 'Bomcas_Leads' ) ) {
			echo wp_kses( Bomcas_Leads::markup( $calc ), bomcas_calc_allowed_html() );
		}
		if ( class_exists( 'Bomcas_Booking' ) ) {
			echo wp_kses( Bomcas_Booking::markup( $calc ), bomcas_calc_allowed_html() );
		}
		if ( ! empty( $args['payload']['years'] ) && count( $args['payload']['years'] ) > 1 ) {
			echo '<div data-compare="1"></div>';
		}
		echo '</div></div>';
		echo wp_kses( self::disclosures( $args ), $allowed );
		if ( class_exists( 'Bomcas_Trust' ) ) {
			echo wp_kses( Bomcas_Trust::verified_html( $args ), bomcas_calc_allowed_html() );
		}
		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * CSS variables from a sanitized presentation.
	 *
	 * @param array $preset Presentation.
	 * @return string
	 */
	private static function css_vars( $preset ) {
		$map  = array(
			'color_navy'   => '--bomcas-navy',
			'color_accent' => '--bomcas-accent',
			'color_ink'    => '--bomcas-ink',
		);
		$bits = array();
		foreach ( $map as $key => $var ) {
			if ( ! empty( $preset[ $key ] ) && preg_match( '/^#[0-9a-fA-F]{6}$/', $preset[ $key ] ) ) {
				$bits[] = $var . ':' . $preset[ $key ];
			}
		}
		return implode( ';', $bits );
	}

	/**
	 * Label override.
	 *
	 * @param array  $preset Presentation.
	 * @param string $key    Key.
	 * @param string $fallback Fallback.
	 * @return string
	 */
	private static function label( $preset, $key, $fallback ) {
		if ( ! empty( $preset['labels'][ $key ] ) ) {
			return $preset['labels'][ $key ];
		}
		return $fallback;
	}

	/**
	 * Inputs.
	 *
	 * @param string $calc     Calculator id.
	 * @param string $province Locked province or empty.
	 * @param array  $args     Args.
	 * @return string
	 */
	private static function fields( $calc, $province, $args ) {
		$html = '';
		if ( 'income-tax' === $calc ) {
			$html .= self::provinces( $province, 'BC' );
			$html .= self::input( 'employment', 'Employment income', '80000' );
			$html .= self::input( 'selfEmployment', 'Self-employment income', '0' );
			$html .= self::input( 'other', 'Other income', '0' );
			$html .= self::input( 'eligibleDividends', 'Eligible dividends (actual)', '0' );
			$html .= self::input( 'nonEligibleDividends', 'Non-eligible dividends (actual)', '0' );
			$html .= self::input( 'capitalGains', 'Capital gains (actual)', '0' );
			$html .= self::input( 'rrsp', 'RRSP and FHSA deduction', '0', 'text' );
			$html .= self::input( 'taxPaid', 'Tax already deducted', '' );
		} elseif ( 'sales-tax' === $calc ) {
			$html .= self::provinces( $province, 'BC' );
			$html .= self::input( 'amount', 'Amount', '100' );
			$html .= self::input( 'date', 'Date', self::today(), 'date', 'date' );
			$html .= '<label class="bomcas-field">Direction<select data-field="direction" data-default="add"><option value="add">Add tax</option><option value="remove">Remove tax</option></select></label>';
		} else {
			$html .= self::input( 'price', 'Price', '750000' );
			$html .= self::input( 'down', 'Down payment', '150000' );
			$html .= self::input( 'annualRate', 'Annual interest rate (%)', '5' );
			$html .= self::input( 'amortYears', 'Amortization (years)', '25' );
			$html .= '<label class="bomcas-field">Payments per year<select data-field="paymentsPerYear" data-default="12"><option value="12">Monthly</option><option value="26">Biweekly</option><option value="52">Weekly</option></select></label>';
			$html .= '<label class="bomcas-field">Compounding<select data-field="compounding" data-default="semi-annual"><option value="semi-annual">Semi-annual (Canadian fixed-rate convention)</option><option value="monthly">Monthly</option></select></label>';
		}
		return $html;
	}

	/**
	 * Number or date input.
	 *
	 * @param string $name    Field.
	 * @param string $label   Label.
	 * @param string $default Default.
	 * @param string $type    Input type.
	 * @param string $mode    Input mode.
	 * @return string
	 */
	private static function today() {
		return function_exists( 'current_time' ) ? current_time( 'Y-m-d' ) : gmdate( 'Y-m-d' );
	}

	private static function input( $name, $label, $default, $type = 'text', $mode = 'decimal' ) {
		$money  = 'date' !== $type && ! in_array( $name, array( 'annualRate', 'amortYears', 'periods', 'sdi', 'local' ), true );
		$prefix = $money ? '<span class="bomcas-affix" aria-hidden="true">$</span>' : '';
		$suffix = 'annualRate' === $name ? '<span class="bomcas-affix" aria-hidden="true">%</span>' : '';
		return '<label class="bomcas-field"><span class="bomcas-label">' . self::h( $label ) . '</span><span class="bomcas-control">' . $prefix
			. '<input data-field="' . self::a( $name ) . '" data-default="' . self::a( $default ) . '" value="' . self::a( $default ) . '" type="' . self::a( $type ) . '" inputmode="' . self::a( $mode ) . '" />' . $suffix . '</span></label>';
	}

	private static function provinces( $locked, $default ) {
		$names   = array(
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
		$current = $locked ? $locked : $default;
		$html    = '<label class="bomcas-field">Province or territory<select data-field="province" data-default="' . self::a( $current ) . '">';
		foreach ( $names as $code => $name ) {
			if ( function_exists( 'selected' ) ) {
				$sel = selected( $code, $current, false );
			} else {
				$sel = $code === $current ? ' selected' : '';
			}
			$html .= '<option value="' . self::a( $code ) . '"' . $sel . '>' . self::h( $name ) . '</option>';
		}
		return $html . '</select></label>';
	}

	/**
	 * Server-rendered default so the page is useful without JavaScript.
	 *
	 * @param string $calc     Calculator.
	 * @param string $province Province.
	 * @param array  $args     Args.
	 * @return string
	 */
	private static function server_results( $calc, $province, $args ) {
		if ( 'sales-tax' === $calc ) {
			$segment = Bomcas_Calc_Engine::sales_on_date( $args['sales'], $province ? $province : 'BC', self::today() );
			$result  = Bomcas_Calc_Engine::sales_tax( 100, $segment, 'add' );
			return self::dt( __( 'Tax on 100', 'bomcas-tax-calculators-canada' ), '$' . number_format( $result['tax'], 2 ) );
		}
		if ( 'mortgage-payment' === $calc ) {
			$result = Bomcas_Calc_Engine::mortgage_payment(
				array(
					'price'           => 750000,
					'down'            => 150000,
					'annualRate'      => 0.05,
					'amortYears'      => 25,
					'paymentsPerYear' => 12,
				)
			);
			return self::dt( 'Sample monthly payment', number_format( $result['payment'], 2 ) );
		}
		$year   = $args['year'];
		$code   = $province ? $province : 'BC';
		$result = Bomcas_Calc_Engine::tax_annual(
			array(
				'employment' => 80000,
				'province'   => $code,
			),
			$year
		);
		return self::dt( __( 'Federal tax on 80,000 of employment income', 'bomcas-tax-calculators-canada' ), '$' . number_format( $result['federalTax'], 2 ) )
			. self::dt( __( 'Provincial tax', 'bomcas-tax-calculators-canada' ), '$' . number_format( $result['provincialTax'], 2 ) )
			. self::dt( __( 'Total tax', 'bomcas-tax-calculators-canada' ), '$' . number_format( $result['totalTax'], 2 ) );
	}

	/**
	 * One description row.
	 *
	 * @param string $label Label.
	 * @param string $value Value.
	 * @return string
	 */
	private static function dt( $label, $value ) {
		return '<dt>' . self::h( $label ) . '</dt><dd>' . self::h( $value ) . '</dd>';
	}

	/**
	 * Required disclosures. Not removable by presentation settings.
	 *
	 * @param array $args Args.
	 * @return string
	 */
	/**
	 * Tags allowed in calculator markup.
	 *
	 * @return array
	 */
	private static function allowed_html() {
		$common = array(
			'class' => true,
			'id'    => true,
			'href'  => true,
			'rel'   => true,
		);
		return array(
			'div'     => $common,
			'p'       => $common,
			'strong'  => $common,
			'em'      => $common,
			'br'      => array(),
			'ul'      => $common,
			'ol'      => $common,
			'li'      => $common,
			'a'       => $common,
			'span'    => array(
				'class'       => true,
				'aria-hidden' => true,
			),
			'label'   => $common,
			'input'   => array(
				'class'        => true,
				'data-field'   => true,
				'data-default' => true,
				'value'        => true,
				'type'         => true,
				'inputmode'    => true,
			),
			'select'  => array(
				'class'        => true,
				'data-field'   => true,
				'data-default' => true,
			),
			'option'  => array(
				'value'    => true,
				'selected' => true,
			),
			'button'  => array(
				'type'        => true,
				'class'       => true,
				'data-action' => true,
			),
			'dl'      => $common,
			'dt'      => $common,
			'dd'      => $common,
			'table'   => $common,
			'thead'   => $common,
			'tbody'   => $common,
			'tr'      => $common,
			'th'      => $common,
			'td'      => $common,
			'section' => $common,
			'h2'      => $common,
			'h3'      => $common,
		);
	}

	/**
	 * Required disclosures. Not removable by presentation settings.
	 *
	 * @param array $args Args.
	 * @return string
	 */
	public static function disclosures( $args ) {
		$html  = '<section class="bomcas-disclosures">';
		$html .= '<h2>' . self::h( __( 'Rates used', 'bomcas-tax-calculators-canada' ) ) . '</h2>';
		$html .= '<p class="bomcas-fresh">' . self::h( $args['freshness'] ) . '</p>';
		if ( ! empty( $args['stale'] ) ) {
			$html .= '<p class="bomcas-stale">Newer rates may apply; the figures below are the latest we have verified.</p>';
		}
		$html .= '<table class="bomcas-table"><thead><tr><th>Item</th><th>Value</th><th>Source</th></tr></thead><tbody>';
		foreach ( $args['rate_rows'] as $row ) {
			$html .= '<tr><td>' . self::h( $row['item'] ) . '</td><td>' . self::h( $row['value'] ) . '</td><td>';
			if ( ! empty( $row['url'] ) ) {
				$html .= '<a href="' . self::a( $row['url'] ) . '" rel="nofollow noopener noreferrer">' . self::h( $row['source'] ) . '</a>';
			} else {
				$html .= self::h( $row['source'] );
			}
			$html .= '</td></tr>';
		}
		$html .= '</tbody></table>';
		$calc  = isset( $args['calc'] ) ? (string) $args['calc'] : '';
		$code  = strtoupper( (string) ( $args['province'] ?? '' ) );
		if ( in_array( $calc, array( 'sales-tax', 'vehicle-purchase' ), true ) ) {
			$note = __( 'Omitted on purpose: a local fee, an exempt or zero-rated supply, and any income-tax credit. This screen does not estimate income tax. A missing amount is left unavailable rather than shown as zero.', 'bomcas-tax-calculators-canada' );
		} elseif ( in_array( $calc, array( 'payroll-deductions', 'salary-after-tax' ), true ) ) {
			$note = __( 'Omitted on purpose: a claim code, a taxable benefit, and a bonus you did not enter. A missing amount is left unavailable rather than shown as zero.', 'bomcas-tax-calculators-canada' );
			if ( 'QC' === $code && 'payroll-deductions' === $calc ) {
				$note .= ' ' . __( 'Quebec regular-payment withholding uses TP-1015.F-V when that edition is loaded. A gratuity is not calculated.', 'bomcas-tax-calculators-canada' );
			}
			if ( 'BC' === $code && 'payroll-deductions' === $calc ) {
				$note .= ' ' . __( 'A British Columbia pay dated on or after the July date in the rate file uses that July withholding rate. The annual return does not.', 'bomcas-tax-calculators-canada' );
			}
		} else {
			$note = __( 'Partial estimate only, not tax or legal advice, and not an official deduction or assessment. Omitted on purpose: spouse and dependant amounts, age, pension and disability amounts, tuition, medical expenses, donations, provincial dividend tax credits, and Quebec refundable credits. EI premiums are not deductible. Only the enhanced portion of CPP is. A missing amount is left unavailable rather than shown as zero.', 'bomcas-tax-calculators-canada' );
		}
		$html .= '<p class="bomcas-disclaimer">' . self::h( $note ) . '</p>';
		if ( isset( $args['calc'] ) && 'sales-tax' === $args['calc'] ) {
			$html .= '<p class="bomcas-note">' . self::h( __( 'Sales tax here is the general rate on a standard taxable supply. It does not cover vehicles, real property, new housing, zero-rated or exempt supplies, or place-of-supply exceptions.', 'bomcas-tax-calculators-canada' ) ) . '</p>';
		}
		if ( ! empty( $args['credit'] ) ) {
			$html .= '<p class="bomcas-credit">' . esc_html__( 'Calculator by', 'bomcas-tax-calculators-canada' ) . ' <a href="' . esc_url( 'https://bomcas.ca' ) . '" rel="noopener">BOMCAS Canada</a></p>';
		}
		$html .= '</section>';
		return $html;
	}
}
