<?php
/**
 * First-party counts. No external tracker unless a GA4 id is saved.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Views, calculations and leads.
 */
class Bomcas_Stats {

	/**
	 * Hooks.
	 */
	public static function boot() {
		add_action( 'admin_post_bomcas_stats_save', array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_bomcas_stat', array( __CLASS__, 'ajax' ) );
		add_action( 'wp_ajax_nopriv_bomcas_stat', array( __CLASS__, 'ajax' ) );
	}

	/**
	 * @param string $event views|runs|leads.
	 * @param string $calc Calculator.
	 * @param int    $page Page id.
	 */
	public static function record( $event, $calc, $page = 0 ) {
		if ( ! in_array( $event, array( 'views', 'runs', 'leads' ), true ) ) {
			return;
		}
		$all = get_option( 'bomcas_stats', array() );
		if ( ! is_array( $all ) ) {
			$all = array();
		}
		$key = gmdate( 'Y-m-d' ) . '|' . sanitize_key( $calc ) . '|' . (int) $page;
		if ( ! isset( $all[ $key ] ) ) {
			$all[ $key ] = array(
				'views' => 0,
				'runs'  => 0,
				'leads' => 0,
			);
		}
		++$all[ $key ][ $event ];
		if ( count( $all ) > 4000 ) {
			$all = array_slice( $all, -3000, null, true );
		}
		update_option( 'bomcas_stats', $all, false );
	}

	/**
	 * Ajax counter. The body is an event name, not the amounts the visitor typed.
	 */
	public static function ajax() {
		check_ajax_referer( 'bomcas_growth', 'nonce' );
		self::record( sanitize_key( wp_unslash( $_POST['event'] ?? '' ) ), sanitize_key( wp_unslash( $_POST['calculator'] ?? '' ) ), absint( wp_unslash( $_POST['page_id'] ?? 0 ) ) );
		wp_send_json_success();
	}

	/**
	 * @return array
	 */
	/**
	 * Daily totals for the chart. Keys are Y-m-d.
	 *
	 * @param int $days Days.
	 * @return array
	 */
	public static function last_days( $days = 30 ) {
		$out = array();
		for ( $i = $days - 1; $i >= 0; $i-- ) {
			$out[ gmdate( 'Y-m-d', time() - $i * DAY_IN_SECONDS ) ] = array(
				'views' => 0,
				'runs'  => 0,
				'leads' => 0,
			);
		}
		foreach ( (array) get_option( 'bomcas_stats', array() ) as $key => $row ) {
			$date = explode( '|', (string) $key )[0];
			if ( ! isset( $out[ $date ] ) ) {
				continue;
			}
			foreach ( array( 'views', 'runs', 'leads' ) as $event ) {
				$out[ $date ][ $event ] += (int) ( $row[ $event ] ?? 0 );
			}
		}
		return $out;
	}

	public static function totals() {
		$out = array(
			'views'   => 0,
			'runs'    => 0,
			'leads'   => 0,
			'by_calc' => array(),
		);
		foreach ( (array) get_option( 'bomcas_stats', array() ) as $key => $row ) {
			$parts = explode( '|', (string) $key );
			$calc  = $parts[1] ?? '';
			foreach ( array( 'views', 'runs', 'leads' ) as $event ) {
				$n              = (int) ( $row[ $event ] ?? 0 );
				$out[ $event ] += $n;
				if ( ! isset( $out['by_calc'][ $calc ] ) ) {
					$out['by_calc'][ $calc ] = array(
						'views' => 0,
						'runs'  => 0,
						'leads' => 0,
					);
				}
				$out['by_calc'][ $calc ][ $event ] += $n;
			}
		}
		return $out;
	}

	/**
	 * Menu.
	 */
	public static function menu() {
		add_submenu_page( 'bomcas-calculators-home', __( 'Usage', 'bomcas-tax-calculators-canada' ), __( 'Usage', 'bomcas-tax-calculators-canada' ), 'manage_options', 'bomcas-stats', array( __CLASS__, 'screen' ) );
	}

	/**
	 * Dashboard.
	 */
	public static function screen() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$totals = self::totals();
		$ga     = (string) get_option( 'bomcas_ga4', '' );
		Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
		Bomcas_Admin_UI::open(
			array(
				'doc'     => 'usage',
				'product' => 'BOMCAS Canada',
				'kicker'  => __( 'Calculators · Usage', 'bomcas-tax-calculators-canada' ),
				'badge'   => __( 'On this site', 'bomcas-tax-calculators-canada' ),
				'help'    => __( 'Counts stay in this site. A GA4 measurement id is optional and is the only external event push.', 'bomcas-tax-calculators-canada' ),
				'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'stats' ) : array(),
			)
		);
		Bomcas_Admin_UI::kpis(
			array(
				array(
					'label' => __( 'Views', 'bomcas-tax-calculators-canada' ),
					'value' => (string) $totals['views'],
				),
				array(
					'label' => __( 'Calculations', 'bomcas-tax-calculators-canada' ),
					'value' => (string) $totals['runs'],
				),
				array(
					'label' => __( 'Leads', 'bomcas-tax-calculators-canada' ),
					'value' => (string) $totals['leads'],
				),
			)
		);
		$max = 1;
		foreach ( $totals['by_calc'] as $row ) {
			$max = max( $max, (int) $row['views'] );
		}
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Top calculators', 'bomcas-tax-calculators-canada' ) . '</h2>';
		foreach ( $totals['by_calc'] as $calc => $row ) {
			$width = (int) round( 100 * $row['views'] / $max );
			echo '<p>' . esc_html( $calc ) . ' <span class="bomcas-bar" style="width:' . esc_attr( (string) $width ) . '%"></span> ' . esc_html( (string) $row['views'] ) . ' / ' . esc_html( (string) $row['runs'] ) . ' / ' . esc_html( (string) $row['leads'] ) . '</p>';
		}
		if ( ! $totals['by_calc'] ) {
			echo '<p>' . esc_html__( 'No counts yet.', 'bomcas-tax-calculators-canada' ) . '</p>';
		}
		echo '</section>';
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Optional analytics', 'bomcas-tax-calculators-canada' ) . '</h2><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_stats_save' );
		echo '<input type="hidden" name="action" value="bomcas_stats_save" />';
		echo '<p><label>' . esc_html__( 'GA4 measurement id', 'bomcas-tax-calculators-canada' ) . ' <input name="ga4" value="' . esc_attr( $ga ) . '" placeholder="G-" /></label> ' . wp_kses( Bomcas_Admin_UI::tip( __( 'Leave empty and nothing is sent to Google. A saved id adds one event when a calculation runs.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		submit_button( __( 'Save usage settings', 'bomcas-tax-calculators-canada' ) );
		echo '</form></section>';
		Bomcas_Admin_UI::close();
	}

	/**
	 * Save the optional GA4 id.
	 */
	public static function save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot edit usage settings.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_stats_save' );
		update_option( 'bomcas_ga4', sanitize_text_field( wp_unslash( $_POST['ga4'] ?? '' ) ) );
		wp_safe_redirect( admin_url( 'admin.php?page=bomcas-stats&saved=1' ) );
		exit;
	}
}

/**
 * Booking link or embed after the result.
 */
class Bomcas_Booking {

	/**
	 * Hooks.
	 */
	public static function boot() {
		add_action( 'admin_post_bomcas_booking_save', array( __CLASS__, 'save' ) );
	}

	/**
	 * @return array
	 */
	public static function settings() {
		$saved = get_option( 'bomcas_booking', array() );
		$base  = array(
			'provider' => 'custom',
			'url'      => '',
			'label'    => __( 'Book a time', 'bomcas-tax-calculators-canada' ),
			'mode'     => 'button',
		);
		return array_merge( $base, is_array( $saved ) ? $saved : array() );
	}

	/**
	 * @return string
	 */
	public static function label_for( $calc ) {
		$labels = array(
			'income-tax'         => __( 'Book a tax review', 'bomcas-tax-calculators-canada' ),
			'mortgage-payment'   => __( 'Book a pre-approval conversation', 'bomcas-tax-calculators-canada' ),
			'payroll-deductions' => __( 'Talk to payroll', 'bomcas-tax-calculators-canada' ),
			'sales-tax'          => __( 'Ask about this sale', 'bomcas-tax-calculators-canada' ),
			'vehicle-purchase'   => __( 'Ask for a vehicle quote', 'bomcas-tax-calculators-canada' ),
		);
		return isset( $labels[ $calc ] ) ? $labels[ $calc ] : __( 'Book a time', 'bomcas-tax-calculators-canada' );
	}

	public static function markup( $calc = '' ) {
		$settings = self::settings();
		$url      = esc_url( $settings['url'] );
		if ( '' === $url ) {
			return '';
		}
		$label = self::label_for( $calc );
		$embed = 'embed' === $settings['mode'] && defined( 'BOMCAS_PRO_VERSION' );
		$html  = '<div class="bomcas-booking">';
		if ( $embed ) {
			$html .= '<iframe title="' . esc_attr( $label ) . '" src="' . $url . '" style="width:100%;min-height:420px;border:0"></iframe>';
		}
		$html .= '<p><a class="btn-skew" href="' . $url . '"><span>' . esc_html( $label ) . '</span></a></p></div>';
		return $html;
	}

	/**
	 * Menu.
	 */
	public static function menu() {
		add_submenu_page( 'bomcas-calculators-home', __( 'Booking', 'bomcas-tax-calculators-canada' ), __( 'Booking', 'bomcas-tax-calculators-canada' ), 'manage_options', 'bomcas-booking', array( __CLASS__, 'screen' ) );
	}

	/**
	 * Screen.
	 */
	public static function screen() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$settings = self::settings();
		Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
		Bomcas_Admin_UI::open(
			array(
				'doc'     => 'booking',
				'product' => 'BOMCAS Canada',
				'kicker'  => __( 'Calculators · Booking', 'bomcas-tax-calculators-canada' ),
				'badge'   => defined( 'BOMCAS_PRO_VERSION' ) ? __( 'Pro', 'bomcas-tax-calculators-canada' ) : __( 'Lite', 'bomcas-tax-calculators-canada' ),
				'help'    => __( 'A button is enough for a Calendly, Cal.com, Acuity or any other URL. An embedded frame is shown when Pro is active.', 'bomcas-tax-calculators-canada' ),
				'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'booking' ) : array(),
			)
		);
		echo '<section class="bomcas-admin__card"><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_booking_save' );
		echo '<input type="hidden" name="action" value="bomcas_booking_save" />';
		echo '<p><label>' . esc_html__( 'Provider', 'bomcas-tax-calculators-canada' ) . ' <select name="provider">';
		foreach ( array(
			'calendly' => 'Calendly',
			'calcom'   => 'Cal.com',
			'acuity'   => 'Acuity',
			'custom'   => __( 'Custom URL', 'bomcas-tax-calculators-canada' ),
		) as $key => $label ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $settings['provider'], $key, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select></label> ' . wp_kses( Bomcas_Admin_UI::tip( __( 'The provider is a label. The link is the URL you paste.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p><label>' . esc_html__( 'URL', 'bomcas-tax-calculators-canada' ) . ' <input class="regular-text" type="url" name="url" value="' . esc_attr( $settings['url'] ) . '" /></label> ' . wp_kses( Bomcas_Admin_UI::tip( __( 'Shown after the estimate. Leave blank to hide the button.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p><label>' . esc_html__( 'Button label', 'bomcas-tax-calculators-canada' ) . ' <input name="label" value="' . esc_attr( $settings['label'] ) . '" /></label></p>';
		echo '<p><label>' . esc_html__( 'Display', 'bomcas-tax-calculators-canada' ) . ' <select name="mode"><option value="button" ' . selected( $settings['mode'], 'button', false ) . '>' . esc_html__( 'Button', 'bomcas-tax-calculators-canada' ) . '</option><option value="embed" ' . selected( $settings['mode'], 'embed', false ) . '>' . esc_html__( 'Embed (Pro)', 'bomcas-tax-calculators-canada' ) . '</option></select></label> ' . wp_kses( Bomcas_Admin_UI::tip( __( 'Lite always shows a button. Pro can also embed the scheduler.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<div class="bomcas-savebar">';
		submit_button( __( 'Save booking', 'bomcas-tax-calculators-canada' ) );
		echo '</div>';
		echo '</form></section>';
		Bomcas_Admin_UI::close();
	}

	/**
	 * Save.
	 */
	public static function save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot edit booking.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_booking_save' );
		$provider = sanitize_key( wp_unslash( $_POST['provider'] ?? 'custom' ) );
		if ( ! in_array( $provider, array( 'calendly', 'calcom', 'acuity', 'custom' ), true ) ) {
			$provider = 'custom';
		}
		update_option(
			'bomcas_booking',
			array(
				'provider' => $provider,
				'url'      => esc_url_raw( wp_unslash( $_POST['url'] ?? '' ) ),
				'label'    => sanitize_text_field( wp_unslash( $_POST['label'] ?? '' ) ),
				'mode'     => 'embed' === sanitize_key( wp_unslash( $_POST['mode'] ?? '' ) ) ? 'embed' : 'button',
			)
		);
		wp_safe_redirect( admin_url( 'admin.php?page=bomcas-booking&saved=1' ) );
		exit;
	}
}
