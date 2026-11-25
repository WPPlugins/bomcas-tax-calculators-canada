<?php
/**
 * Hide licence details on a client site.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Per-site privacy. A wp-config constant locks hiding on.
 */
class Bomcas_Calc_Privacy {

	/**
	 * Hooks.
	 */
	public static function boot() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 20 );
		add_action( 'admin_post_bomcas_calc_privacy', array( __CLASS__, 'save' ) );
		add_action( 'admin_post_bomcas_calc_pin', array( __CLASS__, 'unlock' ) );
		add_filter( 'all_plugins', array( __CLASS__, 'plugins' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'row_meta' ), 10, 2 );
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	/**
	 * @return array
	 */
	public static function settings() {
		$saved = get_option( 'bomcas_calc_site_privacy', array() );
		$saved = is_array( $saved ) ? $saved : array();
		return array(
			'hide'          => self::forced() ? 1 : (int) ( $saved['hide'] ?? 0 ),
			'users'         => isset( $saved['users'] ) ? (string) $saved['users'] : '',
			'cap'           => isset( $saved['cap'] ) ? (string) $saved['cap'] : '',
			'pin_hash'      => isset( $saved['pin_hash'] ) ? (string) $saved['pin_hash'] : '',
			'menu_label'    => isset( $saved['menu_label'] ) ? (string) $saved['menu_label'] : '',
			'hide_branding' => (int) ( $saved['hide_branding'] ?? 0 ),
			'agency_email'  => isset( $saved['agency_email'] ) ? (string) $saved['agency_email'] : '',
		);
	}

	/**
	 * @return bool
	 */
	public static function forced() {
		return defined( 'BOMCAS_CALC_HIDE_LICENSE' ) && BOMCAS_CALC_HIDE_LICENSE;
	}

	/**
	 * @return bool
	 */
	public static function hidden() {
		return self::forced() || ! empty( self::settings()['hide'] );
	}

	/**
	 * Full licence UI for this user.
	 *
	 * @return bool
	 */
	public static function revealed() {
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return ! self::hidden();
		}
		if ( ! self::hidden() ) {
			return true;
		}
		$settings = self::settings();
		$ids      = array_filter( array_map( 'intval', preg_split( '/[\s,]+/', $settings['users'] ) ) );
		$allowed  = in_array( (int) get_current_user_id(), $ids, true );
		if ( ! $allowed && '' !== $settings['cap'] && current_user_can( $settings['cap'] ) ) {
			$allowed = true;
		}
		if ( ! $allowed ) {
			return false;
		}
		if ( '' === $settings['pin_hash'] ) {
			return true;
		}
		return (int) get_user_meta( get_current_user_id(), 'bomcas_calc_pin_until', true ) > time();
	}

	/**
	 * Mask a key. Empty for people who cannot see it.
	 *
	 * @param string $key Key.
	 * @return string
	 */
	public static function mask( $key ) {
		if ( ! self::revealed() ) {
			return '';
		}
		$key = (string) $key;
		if ( strlen( $key ) < 4 ) {
			return '';
		}
		return '••••' . substr( $key, -4 );
	}

	/**
	 * Privacy screen. Hidden users get no licence fields.
	 */
	public static function menu() {
		add_menu_page(
			__( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ),
			self::menu_label(),
			'manage_options',
			'bomcas-calculators-home',
			'bomcas_calc_render_home',
			'dashicons-calculator',
			58
		);
	}

	/**
	 * @return string
	 */
	public static function menu_label() {
		$settings = self::settings();
		if ( $settings['hide_branding'] && '' !== $settings['menu_label'] ) {
			return $settings['menu_label'];
		}
		return __( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' );
	}

	/**
	 * Screen.
	 */
	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$settings = self::settings();
		if ( self::hidden() && ! self::revealed() ) {
			echo '<div class="wrap"><h1>' . esc_html( self::menu_label() ) . '</h1>';
			if ( '' !== $settings['pin_hash'] ) {
				echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
				wp_nonce_field( 'bomcas_calc_pin' );
				echo '<input type="hidden" name="action" value="bomcas_calc_pin" />';
				echo '<p><label>' . esc_html__( 'Unlock PIN', 'bomcas-tax-calculators-canada' ) . ' <input type="password" name="pin" autocomplete="current-password" /></label></p>';
				submit_button( __( 'Unlock', 'bomcas-tax-calculators-canada' ) );
				echo '</form>';
			} else {
				echo '<p>' . esc_html__( 'Licence details are hidden on this site.', 'bomcas-tax-calculators-canada' ) . '</p>';
			}
			echo '</div>';
			return;
		}
		Bomcas_Admin_UI::open(
			array(
				'doc'     => 'white-label',
				'product' => __( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ),
				'kicker'  => __( 'White-label · version ', 'bomcas-tax-calculators-canada' ) . BOMCAS_CALC_VERSION,
				'badge'   => self::hidden() ? __( 'Hidden', 'bomcas-tax-calculators-canada' ) : __( 'Visible', 'bomcas-tax-calculators-canada' ),
				'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'privacy' ) : array(),
			)
		);
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Hide licence and account', 'bomcas-tax-calculators-canada' ) . '</h2>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_calc_privacy' );
		echo '<input type="hidden" name="action" value="bomcas_calc_privacy" />';
		$locked = self::forced();
		echo '<p><label><input type="checkbox" name="hide" value="1" ' . checked( $settings['hide'], 1, false ) . disabled( $locked, true, false ) . ' /> ' . esc_html__( 'Hide licence and account from this site', 'bomcas-tax-calculators-canada' ) . '</label> ' . wp_kses( Bomcas_Admin_UI::tip( __( 'Client users who are not on the allow list no longer see the key or the account screen.', 'bomcas-tax-calculators-canada' ), 'white-label' ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		if ( $locked ) {
			echo '<p>' . esc_html__( 'A configuration constant locks this on. Client administrators cannot turn it off.', 'bomcas-tax-calculators-canada' ) . '</p>';
		}
		echo '<p><label>' . esc_html__( 'Allowed user IDs', 'bomcas-tax-calculators-canada' ) . ' <input name="users" value="' . esc_attr( $settings['users'] ) . '" /></label></p>';
		echo '<p><label>' . esc_html__( 'Or capability', 'bomcas-tax-calculators-canada' ) . ' <input name="cap" value="' . esc_attr( $settings['cap'] ) . '" /></label></p>';
		echo '<p><label>' . esc_html__( 'New unlock PIN (leave blank to keep)', 'bomcas-tax-calculators-canada' ) . ' <input type="password" name="pin" autocomplete="new-password" /></label></p>';
		echo '<p><label>' . esc_html__( 'Menu label', 'bomcas-tax-calculators-canada' ) . ' <input name="menu_label" value="' . esc_attr( $settings['menu_label'] ) . '" /></label></p>';
		echo '<p><label><input type="checkbox" name="hide_branding" value="1" ' . checked( $settings['hide_branding'], 1, false ) . ' /> ' . esc_html__( 'Hide BOMCAS Canada in the plugin list', 'bomcas-tax-calculators-canada' ) . '</label></p>';
		echo '<p><label>' . esc_html__( 'Agency email for renewal mail', 'bomcas-tax-calculators-canada' ) . ' <input type="email" name="agency_email" value="' . esc_attr( $settings['agency_email'] ) . '" /></label></p>';
		echo '<div class="bomcas-savebar">';
		submit_button( __( 'Save privacy', 'bomcas-tax-calculators-canada' ) );
		echo '</div></form></section>';
		Bomcas_Admin_UI::close();
	}

	/**
	 * Save. The constant wins over an attempt to turn hiding off.
	 */
	public static function save() {
		if ( ! current_user_can( 'manage_options' ) || ( self::hidden() && ! self::revealed() && ! self::forced() ) ) {
			wp_die( esc_html__( 'You cannot change this.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_calc_privacy' );
		$current = self::settings();
		$pin     = $current['pin_hash'];
		if ( ! empty( $_POST['pin'] ) ) {
			$pin = password_hash( sanitize_text_field( wp_unslash( $_POST['pin'] ) ), PASSWORD_DEFAULT );
		}
		update_option(
			'bomcas_calc_site_privacy',
			array(
				'hide'          => self::forced() ? 1 : ( empty( $_POST['hide'] ) ? 0 : 1 ),
				'users'         => sanitize_text_field( wp_unslash( $_POST['users'] ?? '' ) ),
				'cap'           => sanitize_key( wp_unslash( $_POST['cap'] ?? '' ) ),
				'pin_hash'      => $pin,
				'menu_label'    => sanitize_text_field( wp_unslash( $_POST['menu_label'] ?? '' ) ),
				'hide_branding' => empty( $_POST['hide_branding'] ) ? 0 : 1,
				'agency_email'  => sanitize_email( wp_unslash( $_POST['agency_email'] ?? '' ) ),
			),
			false
		);
		wp_safe_redirect( admin_url( 'admin.php?page=bomcas-privacy&saved=1' ) );
		exit;
	}

	/**
	 * PIN unlock for an allowed user.
	 */
	public static function unlock() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot unlock this.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_calc_pin' );
		$settings = self::settings();
		$pin      = sanitize_text_field( wp_unslash( $_POST['pin'] ?? '' ) );
		if ( '' !== $settings['pin_hash'] && password_verify( $pin, $settings['pin_hash'] ) ) {
			update_user_meta( get_current_user_id(), 'bomcas_calc_pin_until', time() + 12 * HOUR_IN_SECONDS );
		}
		wp_safe_redirect( admin_url( 'admin.php?page=bomcas-privacy' ) );
		exit;
	}

	/**
	 * @param array $plugins Plugins.
	 * @return array
	 */
	public static function plugins( $plugins ) {
		$settings = self::settings();
		if ( ! $settings['hide_branding'] ) {
			return $plugins;
		}
		foreach ( $plugins as $file => $data ) {
			if ( false === strpos( $file, 'bomcas-calculators' )
				&& false === strpos( $file, 'bomcas-tax-calculators' )
				&& false === strpos( $file, 'bomcas-tax-payroll-calculators' ) ) {
				continue;
			}
			$plugins[ $file ]['Name']        = '' !== $settings['menu_label'] ? $settings['menu_label'] : __( 'Calculators', 'bomcas-tax-calculators-canada' );
			$plugins[ $file ]['Description'] = __( 'Calculators.', 'bomcas-tax-calculators-canada' );
			$plugins[ $file ]['Author']      = '';
			$plugins[ $file ]['AuthorURI']   = '';
		}
		return $plugins;
	}

	/**
	 * @param array  $links Links.
	 * @param string $file  Plugin file.
	 * @return array
	 */
	public static function row_meta( $links, $file ) {
		if ( plugin_basename( BOMCAS_CALC_FILE ) !== $file ) {
			return $links;
		}
		$links = array_values(
			array_filter(
				$links,
				static function ( $link ) {
					return false === stripos( $link, 'account' ) && false === stripos( $link, 'licence' ) && false === stripos( $link, 'license' );
				}
			)
		);
		if ( ! self::revealed() ) {
			return $links;
		}
		$key = (string) get_option( 'bomcas_pro_license_key', '' );
		if ( '' !== $key ) {
			$links[] = esc_html( self::mask( $key ) );
		}
		$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=bomcas-account' ) ) . '">' . esc_html__( 'Manage account', 'bomcas-tax-calculators-canada' ) . '</a>';
		return $links;
	}

	/**
	 * Licence lapse mail goes to the agency. No admin notice on the client site.
	 */
	public static function strip_notices() {
		return;
	}

	/**
	 * Local account payload. Refuses the key to a hidden user.
	 */
	public static function routes() {
		register_rest_route(
			'bomcas-calculators/v1',
			'/account',
			array(
				'methods'             => 'GET',
				'permission_callback' => static function () {
					return current_user_can( 'manage_options' );
				},
				'callback'            => static function () {
					if ( ! Bomcas_Calc_Privacy::revealed() ) {
						return new WP_REST_Response( array( 'hidden' => true ), 403 );
					}
					return new WP_REST_Response(
						array(
							'hidden' => false,
							'plan'   => (string) get_option( 'bomcas_calc_plan_label', '' ),
							'status' => (string) get_option( 'bomcas_calc_license_state', '' ),
							'key'    => Bomcas_Calc_Privacy::mask( (string) get_option( 'bomcas_pro_license_key', '' ) ),
						)
					);
				},
			)
		);
	}
}

/**
 * Whether the current user may see a licence key.
 *
 * @return bool
 */
function bomcas_calc_license_revealed() {
	return class_exists( 'Bomcas_Calc_Privacy' ) && Bomcas_Calc_Privacy::revealed();
}
