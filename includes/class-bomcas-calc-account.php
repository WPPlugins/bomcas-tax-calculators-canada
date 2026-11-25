<?php
/**
 * Account and upgrade. Remote calls happen only when this screen is opened or a button is posted.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lite account screen.
 */
class Bomcas_Calc_Account {

	/**
	 * Static catalogue used when the selling site does not answer.
	 *
	 * @return array
	 */
	public static function fallback_plans() {
		return array(
			array(
				'id'       => 'personal',
				'name'     => 'Personal',
				'sites'    => 1,
				'month'    => 19,
				'quarter'  => 49,
				'year'     => 149,
				'features' => array(
					'1 WordPress site',
					'Income tax, sales tax, and a mortgage payment',
					'Payroll and take-home pay are not included',
				),
			),
			array(
				'id'       => 'professional',
				'name'     => 'Professional',
				'sites'    => 5,
				'month'    => 39,
				'quarter'  => 99,
				'year'     => 299,
				'features' => array(
					'5 WordPress sites',
					'Payroll, take-home pay, and the other Pro tools',
					'White-label on a client site',
				),
			),
			array(
				'id'          => 'agency',
				'name'        => 'Agency',
				'sites'       => 25,
				'month'       => 79,
				'quarter'     => 199,
				'year'        => 599,
				'white_label' => true,
				'features'    => array(
					'25 WordPress sites',
					'Every Pro calculator, including payroll and take-home pay',
					'White-label on a client site',
				),
			),
		);
	}

	/**
	 * Screen.
	 */
	public static function render() {
		if ( ! bomcas_calc_license_revealed() ) {
			Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
			Bomcas_Admin_UI::open(
				array(
					'doc'     => 'account',
					'product' => __( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ),
					'kicker'  => __( 'Account and upgrade · version ', 'bomcas-tax-calculators-canada' ) . BOMCAS_CALC_VERSION,
					'badge'   => __( 'Hidden', 'bomcas-tax-calculators-canada' ),
					'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'account' ) : array(),
				)
			);
			echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Licence details are hidden', 'bomcas-tax-calculators-canada' ) . '</h2>';
			echo '<p>' . esc_html__( 'White-label is on for this user. The licence key, plan and upgrade form stay off this screen.', 'bomcas-tax-calculators-canada' ) . '</p></section>';
			Bomcas_Admin_UI::close();
			return;
		}
		$plans = self::load_plans();
		$promos = self::load_promos();
		$state = (string) get_option( 'bomcas_calc_license_state', '' );
		Bomcas_Admin_UI::open(
			array(
				'doc'     => 'account',
				'product' => __( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ),
				'kicker'  => __( 'Account and upgrade · version ', 'bomcas-tax-calculators-canada' ) . BOMCAS_CALC_VERSION,
				'badge'   => '' !== $state ? $state : __( 'Not connected', 'bomcas-tax-calculators-canada' ),
				'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'account' ) : array(),
			)
		);
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Account and upgrade', 'bomcas-tax-calculators-canada' ) . '</h2>';
		if ( '' !== $state ) {
			echo '<p><strong>' . esc_html__( 'Status', 'bomcas-tax-calculators-canada' ) . '</strong> ' . esc_html( $state ) . '</p>';
			echo '<p>' . esc_html__( 'Plan', 'bomcas-tax-calculators-canada' ) . ': ' . esc_html( (string) get_option( 'bomcas_calc_plan_label', '' ) ) . '</p>';
			$renews = (int) get_option( 'bomcas_calc_renews_at', 0 );
			if ( $renews > 0 ) {
				echo '<p>' . esc_html__( 'Renews', 'bomcas-tax-calculators-canada' ) . ': ' . esc_html( wp_date( 'j M Y', $renews ) ) . '</p>';
			}
			echo '<p><a href="' . esc_url( self::server() . '/my-account/' ) . '">' . esc_html__( 'Invoices, cancellation, and plan changes are on My Account.', 'bomcas-tax-calculators-canada' ) . '</a></p>';
		}
		echo '<h2>' . esc_html__( 'Plans', 'bomcas-tax-calculators-canada' ) . '</h2>';
		if ( empty( $plans['live'] ) ) {
			echo '<p>' . esc_html__( 'Live prices could not be loaded. These are the last published catalogue amounts, in Canadian dollars.', 'bomcas-tax-calculators-canada' ) . '</p>';
		}
		echo '<table class="widefat"><thead><tr><th>' . esc_html__( 'Plan', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Monthly', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Quarterly', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Yearly', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Includes', 'bomcas-tax-calculators-canada' ) . '</th></tr></thead><tbody>';
		foreach ( $plans['rows'] as $row ) {
			$lines = '';
			if ( ! empty( $row['features'] ) && is_array( $row['features'] ) ) {
				$lines = implode( '; ', array_map( 'strval', $row['features'] ) );
			}
			echo '<tr><td>' . esc_html( $row['name'] ) . '</td><td>' . esc_html( (string) $row['month'] ) . '</td><td>' . esc_html( (string) $row['quarter'] ) . '</td><td>' . esc_html( (string) $row['year'] ) . '</td><td>' . esc_html( $lines ) . '</td></tr>';
		}
		echo '</tbody></table>';
		echo '<p>' . esc_html__( 'Personal does not include payroll or take-home pay. Those start on Professional.', 'bomcas-tax-calculators-canada' ) . '</p>';
		echo '<h2>' . esc_html__( 'Promotions', 'bomcas-tax-calculators-canada' ) . '</h2>';
		if ( empty( $promos['live'] ) ) {
			echo '<p>' . esc_html__( 'Promotions could not be loaded. You can still enter a code on the store checkout.', 'bomcas-tax-calculators-canada' ) . '</p>';
		} elseif ( empty( $promos['rows'] ) ) {
			$empty = '' !== (string) ( $promos['empty'] ?? '' ) ? (string) $promos['empty'] : __( 'There are currently no promotions available.', 'bomcas-tax-calculators-canada' );
			echo '<p>' . esc_html( $empty ) . '</p>';
		} else {
			echo '<ul>';
			foreach ( $promos['rows'] as $promo ) {
				echo '<li><strong>' . esc_html( (string) ( $promo['code'] ?? '' ) ) . '</strong> ' . esc_html( (string) ( $promo['banner'] ?? '' ) ) . '</li>';
			}
			echo '</ul>';
		}
		// Read-only display flag added by this plugin's own redirect after a nonce-checked form. It changes no data.
		$share_flag = isset( $_GET['bomcas_share'] ) ? sanitize_text_field( wp_unslash( $_GET['bomcas_share'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( '1' === $share_flag ) {
			echo '<div class="notice notice-warning"><p>' . esc_html__( 'Check the box before checkout. The site address and email are sent only when you agree.', 'bomcas-tax-calculators-canada' ) . '</p></div>';
		}
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_calc_checkout' );
		echo '<input type="hidden" name="action" value="bomcas_calc_checkout" />';
		echo '<p><label>' . esc_html__( 'Plan', 'bomcas-tax-calculators-canada' ) . ' <select name="plan">';
		foreach ( $plans['rows'] as $row ) {
			echo '<option value="' . esc_attr( $row['id'] ) . '">' . esc_html( $row['name'] ) . '</option>';
		}
		echo '</select></label></p>';
		echo '<p><label>' . esc_html__( 'Period', 'bomcas-tax-calculators-canada' ) . ' <select name="interval"><option value="month">' . esc_html__( 'Monthly', 'bomcas-tax-calculators-canada' ) . '</option><option value="quarter">' . esc_html__( 'Quarterly', 'bomcas-tax-calculators-canada' ) . '</option><option value="year">' . esc_html__( 'Yearly', 'bomcas-tax-calculators-canada' ) . '</option></select></label></p>';
		echo '<p><label>' . esc_html__( 'Coupon', 'bomcas-tax-calculators-canada' ) . ' <input name="coupon" class="regular-text" value="" autocomplete="off" /></label></p>';
		$email = (string) get_option( 'admin_email' );
		echo '<p><label>' . esc_html__( 'Email for the receipt', 'bomcas-tax-calculators-canada' ) . ' <input type="email" name="email" class="regular-text" value="' . esc_attr( $email ) . '" /></label></p>';
		echo '<p><label><input type="checkbox" name="share" value="1" /> ' . esc_html__( 'Send this site address and the email above to the store, and open checkout. I agree to the licence terms on the store.', 'bomcas-tax-calculators-canada' ) . '</label></p>';
		echo '<div class="bomcas-savebar">';
		submit_button( __( 'Buy', 'bomcas-tax-calculators-canada' ) );
		echo '</div>';
		echo '</form>';
		echo '<h2>' . esc_html__( 'Connect a licence', 'bomcas-tax-calculators-canada' ) . '</h2>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_calc_connect' );
		echo '<input type="hidden" name="action" value="bomcas_calc_connect" />';
		echo '<p><label>' . esc_html__( 'Licence key', 'bomcas-tax-calculators-canada' ) . ' <input name="key" class="regular-text" autocomplete="off" /></label></p>';
		echo '<p><label>' . esc_html__( 'Licence server', 'bomcas-tax-calculators-canada' ) . ' <input name="server" class="regular-text" value="' . esc_attr( (string) get_option( 'bomcas_calc_license_server', '' ) ) . '" placeholder="https://accountantsexpert.com" /></label></p>';
		echo '<p>' . esc_html__( 'Leave this empty to use https://accountantsexpert.com. A saved address, the BOMCAS_STORE_URL constant, or the bomcas_store_url filter replaces that default. This site is used only when Accountants Expert Licensing is active here.', 'bomcas-tax-calculators-canada' ) . '</p>';
		echo '<div class="bomcas-savebar">';
		submit_button( __( 'Connect', 'bomcas-tax-calculators-canada' ) );
		echo '</div>';
		echo '</form>';
		echo '<h2>' . esc_html__( 'After you buy', 'bomcas-tax-calculators-canada' ) . '</h2>';
		echo '<p>' . esc_html__( 'Lite does not install Pro. Open My Account on the store, download the Pro zip, then upload it under Plugins → Add New. Paste the licence key into Pro.', 'bomcas-tax-calculators-canada' ) . '</p>';
		echo '<p><a class="button" href="' . esc_url( self::server() . '/my-account/' ) . '">' . esc_html__( 'Open My Account', 'bomcas-tax-calculators-canada' ) . '</a></p>';
		echo '</section>';
		Bomcas_Admin_UI::close();
	}

	/**
	 * Selling site for plans, checkout, and My Account.
	 *
	 * Empty uses https://accountantsexpert.com. The constant BOMCAS_STORE_URL
	 * or the bomcas_store_url filter can replace that. This site is used only
	 * when Accountants Expert Licensing is running here.
	 *
	 * @return string
	 */
	public static function server() {
		$server = trim( (string) get_option( 'bomcas_calc_license_server', '' ) );
		if ( '' === $server ) {
			$server = self::default_server();
		}
		$server = apply_filters( 'bomcas_calc_license_server', $server );
		if ( ! is_string( $server ) || '' === $server ) {
			$server = 'https://accountantsexpert.com';
		}
		return untrailingslashit( $server );
	}

	/**
	 * Hard default for a customer site. The store itself uses its own address.
	 *
	 * @return string
	 */
	public static function default_server() {
		if ( defined( 'BOMCAS_STORE_URL' ) && is_string( BOMCAS_STORE_URL ) && '' !== BOMCAS_STORE_URL ) {
			return untrailingslashit( BOMCAS_STORE_URL );
		}
		$fallback = defined( 'BOMCAS_LIC_VERSION' ) ? home_url() : 'https://accountantsexpert.com';
		$filtered = apply_filters( 'bomcas_store_url', $fallback );
		if ( ! is_string( $filtered ) || '' === $filtered ) {
			$filtered = 'https://accountantsexpert.com';
		}
		return untrailingslashit( $filtered );
	}

	/**
	 * Plans from the selling site, only because this page was opened.
	 *
	 * @return array
	 */
	public static function load_plans() {
		$cached = get_transient( 'bomcas_calc_plans' );
		if ( is_array( $cached ) && ! empty( $cached['rows'] ) ) {
			return $cached;
		}
		$server   = self::server();
		$response = wp_remote_get(
			trailingslashit( $server ) . 'wp-json/bomcas-licensing/v1/plans',
			array( 'timeout' => 6 )
		);
		if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( ! empty( $body['plans'] ) && is_array( $body['plans'] ) ) {
				$cached = array(
					'live' => true,
					'rows' => $body['plans'],
				);
				set_transient( 'bomcas_calc_plans', $cached, 10 * MINUTE_IN_SECONDS );
				return $cached;
			}
		}
		return array(
			'live' => false,
			'rows' => self::fallback_plans(),
		);
	}

	/**
	 * Active coupon banners. Cached for two minutes. A failed request is not cached.
	 *
	 * @return array
	 */
	public static function load_promos() {
		$cached = get_transient( 'bomcas_calc_promos' );
		if ( is_array( $cached ) ) {
			return $cached;
		}
		$response = wp_remote_get(
			add_query_arg( 'product', 'bomcas-calculators-pro', trailingslashit( self::server() ) . 'wp-json/bomcas-licensing/v1/promotions' ),
			array( 'timeout' => 6 )
		);
		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return array(
				'live' => false,
				'rows' => array(),
			);
		}
		$body   = json_decode( wp_remote_retrieve_body( $response ), true );
		$cached = array(
			'live'  => true,
			'rows'  => isset( $body['promotions'] ) && is_array( $body['promotions'] ) ? $body['promotions'] : array(),
			'empty' => isset( $body['empty'] ) ? (string) $body['empty'] : '',
		);
		set_transient( 'bomcas_calc_promos', $cached, 2 * MINUTE_IN_SECONDS );
		return $cached;
	}

	/**
	 * Hooks for posted actions.
	 */
	public static function boot() {
		add_action( 'admin_post_bomcas_calc_checkout', array( __CLASS__, 'checkout' ) );
		add_action( 'admin_post_bomcas_calc_connect', array( __CLASS__, 'connect' ) );
	}

	/**
	 * Open a hosted checkout in a new request. The button is the user action.
	 */
	public static function checkout() {
		if ( ! bomcas_calc_license_revealed() ) {
			wp_die( esc_html__( 'You cannot upgrade from this site.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_calc_checkout' );
		if ( empty( $_POST['share'] ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=bomcas-account&bomcas_share=1' ) );
			exit;
		}
		$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
		if ( ! is_email( $email ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=bomcas-account&bomcas_share=1' ) );
			exit;
		}
		$server   = self::server();
		$response = wp_remote_post(
			trailingslashit( $server ) . 'wp-json/bomcas-licensing/v1/checkout-session',
			array(
				'timeout' => 12,
				'body'    => array(
					'plan'     => sanitize_key( wp_unslash( $_POST['plan'] ?? 'personal' ) ),
					'interval' => sanitize_key( wp_unslash( $_POST['interval'] ?? 'year' ) ),
					'site'     => home_url(),
					'email'    => $email,
					'coupon'   => sanitize_text_field( wp_unslash( $_POST['coupon'] ?? '' ) ),
					'agree'    => '1',
					'return'   => admin_url( 'admin.php?page=bomcas-account' ),
				),
			)
		);
		$url      = '';
		if ( ! is_wp_error( $response ) ) {
			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			$url  = isset( $body['url'] ) ? $body['url'] : '';
		}
		if ( '' === $url ) {
			$url = add_query_arg(
				array(
					'plan'     => sanitize_key( wp_unslash( $_POST['plan'] ?? 'personal' ) ),
					'interval' => sanitize_key( wp_unslash( $_POST['interval'] ?? 'year' ) ),
					'site'     => home_url(),
				),
				self::server() . '/products/bomcas-tax-payroll-calculators-canada/'
			);
		}
		$host = wp_parse_url( $url, PHP_URL_HOST );
		if ( is_string( $host ) && '' !== $host ) {
			add_filter(
				'allowed_redirect_hosts',
				static function ( $hosts ) use ( $host ) {
					$hosts[] = $host;
					return $hosts;
				}
			);
		}
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Store a key locally and ask the server for status. The key is not printed back.
	 */
	public static function connect() {
		if ( ! bomcas_calc_license_revealed() ) {
			wp_die( esc_html__( 'You cannot connect a licence.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_calc_connect' );
		$key    = sanitize_text_field( wp_unslash( $_POST['key'] ?? '' ) );
		$posted = esc_url_raw( wp_unslash( $_POST['server'] ?? '' ) );
		$server = '' !== $posted ? $posted : self::server();
		if ( '' !== $key ) {
			update_option( 'bomcas_pro_license_key', $key, false );
		}
		if ( '' !== $posted ) {
			update_option( 'bomcas_calc_license_server', $posted, false );
			update_option( 'bomcas_pro_license_server', $posted, false );
		}
		$response = wp_remote_post(
			trailingslashit( $server ) . 'wp-json/bomcas-licensing/v1/status',
			array(
				'timeout' => 8,
				'body'    => array(
					'key'  => $key,
					'site' => home_url(),
				),
			)
		);
		if ( ! is_wp_error( $response ) ) {
			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( ! empty( $body['state'] ) ) {
				update_option( 'bomcas_calc_license_state', sanitize_key( $body['state'] ), false );
				update_option( 'bomcas_calc_plan_label', sanitize_text_field( $body['plan'] ?? '' ), false );
				update_option( 'bomcas_calc_renews_at', isset( $body['renews_at'] ) ? (int) $body['renews_at'] : 0, false );
				update_option( 'bomcas_calc_sites', isset( $body['sites'] ) && is_array( $body['sites'] ) ? array_map( 'esc_url_raw', $body['sites'] ) : array(), false );
				update_option(
					'bomcas_pro_license_status',
					array(
						'state'       => sanitize_key( $body['state'] ),
						'label'       => sanitize_text_field( $body['label'] ?? $body['state'] ),
						'plan'        => sanitize_text_field( $body['plan'] ?? '' ),
						'renews_at'   => isset( $body['renews_at'] ) ? (int) $body['renews_at'] : 0,
						'sites'       => isset( $body['sites'] ) && is_array( $body['sites'] ) ? array_map( 'esc_url_raw', $body['sites'] ) : array(),
						'product'     => sanitize_text_field( $body['product'] ?? '' ),
						'billing_url' => esc_url_raw( $body['billing_url'] ?? '' ),
						'account_url' => esc_url_raw( $body['account_url'] ?? '' ),
						'checked_at'  => time(),
					),
					false
				);
			}
		}
		wp_remote_post(
			trailingslashit( $server ) . 'wp-json/bomcas-licensing/v1/activate',
			array(
				'timeout' => 8,
				'body'    => array(
					'key'  => $key,
					'site' => home_url(),
				),
			)
		);
		wp_safe_redirect( admin_url( 'admin.php?page=bomcas-account&connected=1' ) );
		exit;
	}

}
