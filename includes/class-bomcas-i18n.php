<?php
/**
 * English, Canadian French and US Spanish.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Locale switch and number formats.
 */
class Bomcas_I18n {

	/**
	 * Hooks.
	 */
	public static function boot() {
		add_action( 'init', array( __CLASS__, 'remember' ) );
		add_filter( 'determine_locale', array( __CLASS__, 'locale' ) );
	}

	/**
	 * Remember a language choice.
	 */
	public static function remember() {
		if ( ! isset( $_GET['bomcas_lang'] ) ) {
			return;
		}
		$choice = sanitize_text_field( wp_unslash( $_GET['bomcas_lang'] ) );
		$nonce  = isset( $_GET['_bomcas_lang_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_bomcas_lang_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'bomcas_lang_' . $choice ) ) {
			return;
		}
		if ( ! in_array( $choice, array( 'en_CA', 'en_US', 'fr_CA', 'es_MX' ), true ) ) {
			return;
		}
		setcookie( 'bomcas_lang', $choice, time() + YEAR_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
		$_COOKIE['bomcas_lang'] = $choice;
	}

	/**
	 * @param string $locale Site locale.
	 * @return string
	 */
	public static function locale( $locale ) {
		$choice = '';
		if ( isset( $_GET['bomcas_lang'] ) ) {
			$choice = sanitize_text_field( wp_unslash( $_GET['bomcas_lang'] ) );
			$nonce  = isset( $_GET['_bomcas_lang_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_bomcas_lang_nonce'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce, 'bomcas_lang_' . $choice ) ) {
				$choice = '';
			}
		} elseif ( isset( $_COOKIE['bomcas_lang'] ) ) {
			$choice = sanitize_text_field( wp_unslash( $_COOKIE['bomcas_lang'] ) );
		}
		if ( 'es_ES' === $choice ) {
			$choice = 'es_MX';
		}
		$picked = in_array( $choice, array( 'en_CA', 'en_US', 'fr_CA', 'es_MX' ), true ) ? $choice : $locale;
		return $picked;
	}

	/**
	 * @param string $calc Calculator id.
	 * @return array
	 */
	public static function format( $calc ) {
		unset( $calc );
		$locale = self::locale( determine_locale() );
		return array(
			'locale'   => 0 === strpos( $locale, 'fr' ) ? 'fr-CA' : 'en-CA',
			'currency' => 'CAD',
		);
	}

	/**
	 * Language links. Wrapped in nav so the content gate ignores them.
	 *
	 * @return string
	 */
	public static function switcher() {
		$base = remove_query_arg( 'bomcas_lang' );
		$html = '<nav class="bomcas-lang" aria-label="' . esc_attr__( 'Language', 'bomcas-tax-calculators-canada' ) . '">';
		foreach ( array(
			'en_CA' => __( 'English', 'bomcas-tax-calculators-canada' ),
			'fr_CA' => __( 'French', 'bomcas-tax-calculators-canada' ),
			'es_MX' => __( 'Spanish', 'bomcas-tax-calculators-canada' ),
		) as $code => $label ) {
			$html .= '<a href="' . esc_url(
				add_query_arg(
					array(
						'bomcas_lang'        => $code,
						'_bomcas_lang_nonce' => wp_create_nonce( 'bomcas_lang_' . $code ),
					),
					$base
				)
			) . '">' . esc_html( $label ) . '</a> ';
		}
		$html .= '</nav>';
		return $html;
	}

	/**
	 * Quebec terms, shown for a Quebec calculator.
	 *
	 * @param string $province Province code.
	 * @return string
	 */
	public static function quebec_note( $province ) {
		if ( 'QC' !== strtoupper( (string) $province ) ) {
			return '';
		}
		return '<p class="bomcas-note">' . esc_html__( 'In Quebec, the estimate uses the Quebec Pension Plan (QPP, RRQ), the Quebec Parental Insurance Plan (QPIP, RQAP), and the Quebec sales tax (QST, TVQ).', 'bomcas-tax-calculators-canada' ) . '</p>';
	}

	/**
	 * Strings handed to the front-end script.
	 *
	 * @return array
	 */
	public static function js_strings() {
		$data     = array();
		$defaults = array(
			'sent'    => __( 'Sent. Check your inbox.', 'bomcas-tax-calculators-canada' ),
			'pdf'     => __( 'Download PDF', 'bomcas-tax-calculators-canada' ),
			'working' => __( 'Sending…', 'bomcas-tax-calculators-canada' ),
		);
		return array_merge( $defaults, $data );
	}

	/**
	 * Menu.
	 */
	public static function menu() {
		add_submenu_page( 'bomcas-calculators-home', __( 'Languages', 'bomcas-tax-calculators-canada' ), __( 'Languages', 'bomcas-tax-calculators-canada' ), 'manage_options', 'bomcas-languages', array( __CLASS__, 'screen' ) );
	}

	/**
	 * Screen.
	 */
	public static function screen() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
		Bomcas_Admin_UI::open(
			array(
				'doc'     => 'languages',
				'product' => 'BOMCAS Canada',
				'kicker'  => __( 'Calculators · Languages', 'bomcas-tax-calculators-canada' ),
				'badge'   => 'EN · FR · ES',
				'help'    => __( 'Each calculator has an English, French (Canada) and Spanish (US) switch. Amounts use that locale. Quebec pages name RRQ, RQAP and TVQ in French.', 'bomcas-tax-calculators-canada' ),
				'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'languages' ) : array(),
			)
		);
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Languages', 'bomcas-tax-calculators-canada' ) . '</h2><ul>';
		echo '<li>' . esc_html__( 'English (en_CA) is the default.', 'bomcas-tax-calculators-canada' ) . '</li>';
		echo '<li>' . esc_html__( 'French (fr_CA) uses RRQ for the Quebec Pension Plan, RQAP for parental insurance, and TVQ for Quebec sales tax.', 'bomcas-tax-calculators-canada' ) . '</li>';
		echo '<li>' . esc_html__( 'Spanish (es_MX).', 'bomcas-tax-calculators-canada' ) . '</li>';
		echo '</ul><p>' . esc_html__( 'Translated text comes from translate.wordpress.org language packs. Until a pack is published for a language, the calculator text stays in English.', 'bomcas-tax-calculators-canada' ) . '</p><p>' . esc_html__( 'Visitors pick a language on the calculator. The choice is kept for this site.', 'bomcas-tax-calculators-canada' ) . '</p></section>';
		Bomcas_Admin_UI::close();
	}
}
