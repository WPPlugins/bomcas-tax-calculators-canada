<?php
/**
 * Optional lead form. Calculator inputs stay in the browser until the visitor asks to send them.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Leads table, mail, and admin list.
 */
class Bomcas_Leads {

	const TABLE = 'bomcas_leads';

	/**
	 * Hooks.
	 */
	public static function boot() {
		add_action( 'init', array( __CLASS__, 'install' ) );
		add_action( 'admin_post_bomcas_leads_save', array( __CLASS__, 'save_settings' ) );
		add_action( 'admin_post_bomcas_leads_export', array( __CLASS__, 'export' ) );
		add_action( 'admin_post_bomcas_leads_delete', array( __CLASS__, 'delete_one' ) );
		add_action( 'wp_ajax_bomcas_lead', array( __CLASS__, 'receive' ) );
		add_action( 'wp_ajax_nopriv_bomcas_lead', array( __CLASS__, 'receive' ) );
		add_action( 'wp_ajax_bomcas_pdf', array( __CLASS__, 'pdf' ) );
		add_action( 'wp_ajax_nopriv_bomcas_pdf', array( __CLASS__, 'pdf' ) );
		add_action( 'bomcas_daily', array( __CLASS__, 'purge' ) );
	}

	/**
	 * Create the table once.
	 */
	public static function install() {
		if ( get_option( 'bomcas_leads_db' ) === '1' ) {
			return;
		}
		global $wpdb;
		$table   = $wpdb->prefix . self::TABLE;
		$charset = $wpdb->get_charset_collate();
		$sql     = "CREATE TABLE $table (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			created_at datetime NOT NULL,
			calculator varchar(64) NOT NULL DEFAULT '',
			page_id bigint(20) unsigned NOT NULL DEFAULT 0,
			person_name varchar(190) NOT NULL DEFAULT '',
			email varchar(190) NOT NULL DEFAULT '',
			phone varchar(60) NOT NULL DEFAULT '',
			consent tinyint(1) NOT NULL DEFAULT 0,
			extra longtext NULL,
			results longtext NULL,
			ip_hash char(64) NOT NULL DEFAULT '',
			PRIMARY KEY (id),
			KEY calculator (calculator),
			KEY created_at (created_at)
		) $charset;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		update_option( 'bomcas_leads_db', '1' );
	}

	/**
	 * @return array
	 */
	public static function settings() {
		$saved = get_option( 'bomcas_lead_settings', array() );
		$base  = array(
			'enabled'   => 0,
			'position'  => 'after',
			'notify'    => get_option( 'admin_email' ),
			'retention' => 365,
			'rate'      => 8,
			'consent'   => __( 'I agree to receive this estimate by email. I can unsubscribe at any time.', 'bomcas-tax-calculators-canada' ),
			'fields'    => '',
			'captcha'   => '',
			'turnstile' => '',
		);
		return array_merge( $base, is_array( $saved ) ? $saved : array() );
	}

	/**
	 * Front-end form. Method is GET so the calculator itself is never posted.
	 *
	 * @param string $calc Calculator id.
	 * @return string
	 */
	public static function markup( $calc ) {
		$settings = self::settings();
		if ( empty( $settings['enabled'] ) ) {
			return '';
		}
		$html  = '<form class="bomcas-lead" method="get" action="#" data-lead-form>';
		$html .= '<h3>' . esc_html__( 'Email me my results', 'bomcas-tax-calculators-canada' ) . '</h3>';
		$html .= '<p class="bomcas-hp" aria-hidden="true"><label>Website <input type="text" name="company_website" tabindex="-1" autocomplete="off" /></label></p>';
		$html .= '<p><label>' . esc_html__( 'Name', 'bomcas-tax-calculators-canada' ) . ' <input type="text" name="person_name" required /></label></p>';
		$html .= '<p><label>' . esc_html__( 'Email', 'bomcas-tax-calculators-canada' ) . ' <input type="email" name="email" required /></label></p>';
		$html .= '<p><label>' . esc_html__( 'Phone', 'bomcas-tax-calculators-canada' ) . ' <input type="tel" name="phone" /></label></p>';
		foreach ( self::custom_fields( $settings ) as $label ) {
			$html .= '<p><label>' . esc_html( $label ) . ' <input type="text" name="extra[' . esc_attr( $label ) . ']" /></label></p>';
		}
		$html .= '<p><label><input type="checkbox" name="consent" value="1" required /> ' . esc_html( $settings['consent'] ) . '</label></p>';
		if ( ! empty( $settings['captcha'] ) ) {
			$html .= '<p class="bomcas-note">' . esc_html__( 'Complete the captcha before sending.', 'bomcas-tax-calculators-canada' ) . '</p>';
		}
		$html .= '<p><button type="submit">' . esc_html__( 'Email me my results', 'bomcas-tax-calculators-canada' ) . '</button></p>';
		$html .= '<p class="bomcas-note" data-lead-note></p></form>';
		return $html;
	}

	/**
	 * @param array $settings Settings.
	 * @return string[]
	 */
	private static function custom_fields( $settings ) {
		$lines = preg_split( '/\r\n|\r|\n/', (string) $settings['fields'] );
		$out   = array();
		foreach ( (array) $lines as $line ) {
			$line = sanitize_text_field( $line );
			if ( '' !== $line && count( $out ) < 6 ) {
				$out[] = $line;
			}
		}
		return $out;
	}

	/**
	 * Accept a lead from the visitor's browser after they submit the form.
	 */
	public static function receive() {
		check_ajax_referer( 'bomcas_growth', 'nonce' );
		$result = self::accept( wp_unslash( $_POST ) );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_code() ), 400 );
		}
		wp_send_json_success( array( 'id' => $result ) );
	}

	/**
	 * @param array $input Posted fields.
	 * @return int|WP_Error
	 */
	public static function accept( $input ) {
		$settings = self::settings();
		if ( empty( $settings['enabled'] ) ) {
			return new WP_Error( 'closed' );
		}
		if ( ! empty( $input['company_website'] ) ) {
			return new WP_Error( 'spam' );
		}
		if ( empty( $input['consent'] ) ) {
			return new WP_Error( 'consent' );
		}
		if ( ! self::captcha_ok( $settings, $input ) ) {
			return new WP_Error( 'captcha' );
		}
		$email = sanitize_email( $input['email'] ?? '' );
		$name  = sanitize_text_field( $input['person_name'] ?? '' );
		if ( ! is_email( $email ) || '' === $name ) {
			return new WP_Error( 'fields' );
		}
		if ( ! self::allow_ip( $settings ) ) {
			return new WP_Error( 'rate' );
		}
		$extra = array();
		if ( isset( $input['extra'] ) && is_array( $input['extra'] ) ) {
			foreach ( $input['extra'] as $key => $value ) {
				$extra[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
			}
		}
		$results = array();
		$raw     = $input['results'] ?? array();
		if ( is_string( $raw ) ) {
			$raw = json_decode( $raw, true );
		}
		if ( is_array( $raw ) ) {
			foreach ( array_slice( $raw, 0, 40 ) as $row ) {
				if ( is_array( $row ) ) {
					$results[] = array(
						'label' => sanitize_text_field( $row['label'] ?? '' ),
						'value' => sanitize_text_field( $row['value'] ?? '' ),
					);
				}
			}
		}
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom leads table.
		$wpdb->insert(
			$wpdb->prefix . self::TABLE,
			array(
				'created_at'  => current_time( 'mysql' ),
				'calculator'  => sanitize_key( $input['calculator'] ?? '' ),
				'page_id'     => (int) ( $input['page_id'] ?? 0 ),
				'person_name' => $name,
				'email'       => $email,
				'phone'       => sanitize_text_field( $input['phone'] ?? '' ),
				'consent'     => 1,
				'extra'       => wp_json_encode( $extra ),
				'results'     => wp_json_encode( $results ),
				'ip_hash'     => hash( 'sha256', self::ip() . wp_salt( 'auth' ) ),
			)
		);
		$id = (int) $wpdb->insert_id;
		self::notify( $id, $settings );
		if ( class_exists( 'Bomcas_Stats' ) ) {
			Bomcas_Stats::record( 'leads', sanitize_key( $input['calculator'] ?? '' ), (int) ( $input['page_id'] ?? 0 ) );
		}
		do_action( 'bomcas_lead_created', $id );
		return $id;
	}

	/**
	 * @param array $settings Settings.
	 * @return bool
	 */
	private static function allow_ip( $settings ) {
		$key = 'bomcas_lead_rl_' . md5( self::ip() );
		$n   = (int) get_transient( $key );
		$cap = max( 1, (int) $settings['rate'] );
		if ( $n >= $cap ) {
			return false;
		}
		set_transient( $key, $n + 1, HOUR_IN_SECONDS );
		return true;
	}

	/**
	 * @return string
	 */
	private static function ip() {
		return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0';
	}

	/**
	 * Optional captcha. Skipped when no secret is saved.
	 *
	 * @param array $settings Settings.
	 * @return bool
	 */
	private static function captcha_ok( $settings, $input ) {
		$secret = (string) ( $settings['captcha_secret'] ?? '' );
		$turn   = (string) ( $settings['turnstile_secret'] ?? '' );
		if ( '' === $secret && '' === $turn ) {
			return true;
		}
		$token = sanitize_text_field( $input['captcha_token'] ?? '' );
		if ( '' === $token ) {
			return false;
		}
		// External service, documented in readme.txt under External services:
		// Cloudflare Turnstile when a Turnstile secret is saved, otherwise Google reCAPTCHA.
		// phpcs:ignore PluginCheck.CodeAnalysis.Offloading.OffloadedContent -- Server-side siteverify API call for the captcha token. No script, style, or image is loaded from Cloudflare.
		$url      = '' !== $turn ? 'https://challenges.cloudflare.com/turnstile/v0/siteverify' : 'https://www.google.com/recaptcha/api/siteverify';
		$key      = '' !== $turn ? $turn : $secret;
		$response = wp_remote_post(
			$url,
			array(
				'timeout' => 8,
				'body'    => array(
					'secret'   => $key,
					'response' => $token,
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			return false;
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		return ! empty( $body['success'] );
	}

	/**
	 * @param int   $id Lead id.
	 * @param array $settings Settings.
	 */
	public static function notify( $id, $settings ) {
		$row = self::get( $id );
		if ( ! $row ) {
			return;
		}
		$results = json_decode( (string) $row['results'], true );
		$list    = '';
		foreach ( (array) $results as $item ) {
			$list .= '<tr><td>' . esc_html( $item['label'] ) . '</td><td>' . esc_html( $item['value'] ) . '</td></tr>';
		}
		$brand = class_exists( 'Bomcas_Pro_Brand' ) ? Bomcas_Pro_Brand::current() : array();
		$html  = '<p>' . esc_html( $row['person_name'] ) . '</p><table>' . $list . '</table>';
		$html .= '<p>' . esc_html__( 'Estimate only. Not a tax return.', 'bomcas-tax-calculators-canada' ) . '</p>';
		if ( empty( $brand['full'] ) ) {
			$html .= '<p>BOMCAS Canada</p>';
		} elseif ( ! empty( $brand['contact'] ) ) {
			$html .= '<p>' . esc_html( $brand['contact'] ) . '</p>';
		}
		$headers = array( 'Content-Type: text/html; charset=UTF-8', 'Reply-To: ' . $row['person_name'] . ' <' . $row['email'] . '>' );
		wp_mail( $row['email'], __( 'Your tax estimate', 'bomcas-tax-calculators-canada' ), $html, $headers );
		if ( is_email( $settings['notify'] ) ) {
			wp_mail( $settings['notify'], __( 'New calculator lead', 'bomcas-tax-calculators-canada' ), $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
		}
	}

	/**
	 * @param int $id Id.
	 * @return array|null
	 */
	/**
	 * Newest leads for the overview.
	 *
	 * @param int $limit Rows.
	 * @return array
	 */
	public static function recent( $limit = 5 ) {
		global $wpdb;
		$limit = max( 1, (int) $limit );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT created_at, person_name, calculator FROM %i ORDER BY id DESC LIMIT %d', $wpdb->prefix . self::TABLE, $limit ), ARRAY_A );
		return is_array( $rows ) ? $rows : array();
	}

	public static function get( $id ) {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One lead row, read for an email the visitor just asked to send.
		$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %i WHERE id = %d', $wpdb->prefix . self::TABLE, $id ), ARRAY_A );
		return is_array( $row ) ? $row : null;
	}

	/**
	 * Drop leads older than the retention setting.
	 */
	public static function purge() {
		$settings = self::settings();
		$days     = max( 1, (int) $settings['retention'] );
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Retention delete on the custom leads table.
		$wpdb->query( $wpdb->prepare( 'DELETE FROM %i WHERE created_at < %s', $wpdb->prefix . self::TABLE, gmdate( 'Y-m-d H:i:s', time() - $days * DAY_IN_SECONDS ) ) );
	}

	/**
	 * PDF download. Generated only when the visitor clicks the button.
	 */
	public static function pdf() {
		check_ajax_referer( 'bomcas_growth', 'nonce' );
		$title = sanitize_text_field( wp_unslash( $_POST['title'] ?? 'Tax estimate' ) );
		$raw   = json_decode( sanitize_textarea_field( wp_unslash( $_POST['lines'] ?? '[]' ) ), true );
		$lines = array();
		foreach ( array_slice( (array) $raw, 0, 40 ) as $line ) {
			$lines[] = sanitize_text_field( (string) $line );
		}
		$brand = class_exists( 'Bomcas_Pro_Brand' ) ? Bomcas_Pro_Brand::current() : array( 'full' => false );
		$bytes = Bomcas_Pdf::document( $title, $lines, $brand );
		$path  = wp_tempnam( 'tax-estimate.pdf' );
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
		$fs = new WP_Filesystem_Direct( false );
		$fs->put_contents( $path, (string) $bytes );
		bomcas_calc_download( $path, 'application/pdf', 'tax-estimate.pdf' );
	}

	/**
	 * Admin.
	 */
	public static function menu() {
		add_submenu_page( 'bomcas-calculators-home', __( 'Leads', 'bomcas-tax-calculators-canada' ), __( 'Leads', 'bomcas-tax-calculators-canada' ), 'manage_options', 'bomcas-leads', array( __CLASS__, 'screen' ) );
	}

	/**
	 * Leads screen.
	 */
	public static function screen() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		self::install();
		$settings = self::settings();
		if ( isset( $_GET['_wpnonce'] ) ) {
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'bomcas_leads_filter' );
		}
		$calc   = isset( $_GET['calculator'] ) ? sanitize_key( wp_unslash( $_GET['calculator'] ) ) : '';
		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE;
		$like  = '%' . $wpdb->esc_like( $search ) . '%';
		if ( '' !== $calc && '' !== $search ) {
			$sql = $wpdb->prepare( 'SELECT * FROM %i WHERE calculator = %s AND (email LIKE %s OR person_name LIKE %s) ORDER BY id DESC LIMIT 50', $table, $calc, $like, $like );
		} elseif ( '' !== $calc ) {
			$sql = $wpdb->prepare( 'SELECT * FROM %i WHERE calculator = %s ORDER BY id DESC LIMIT 50', $table, $calc );
		} elseif ( '' !== $search ) {
			$sql = $wpdb->prepare( 'SELECT * FROM %i WHERE email LIKE %s OR person_name LIKE %s ORDER BY id DESC LIMIT 50', $table, $like, $like );
		} else {
			$sql = $wpdb->prepare( 'SELECT * FROM %i ORDER BY id DESC LIMIT 50', $table );
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- $sql is prepared above. The leads table is custom.
		$rows = $wpdb->get_results( $sql, ARRAY_A );
		Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
		Bomcas_Admin_UI::open(
			array(
				'doc'     => 'leads',
				'product' => 'BOMCAS Canada',
				'kicker'  => __( 'Calculators · Leads', 'bomcas-tax-calculators-canada' ),
				'badge'   => defined( 'BOMCAS_PRO_VERSION' ) ? __( 'Pro', 'bomcas-tax-calculators-canada' ) : __( 'Lite', 'bomcas-tax-calculators-canada' ),
				'help'    => __( 'The form is off until you enable it. A visitor is emailed only after they tick the consent box. Retention deletes old rows on the daily job.', 'bomcas-tax-calculators-canada' ),
				'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'leads' ) : array(),
			)
		);
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Lead form', 'bomcas-tax-calculators-canada' ) . '</h2>';
		echo '<form method="post" class="bomcas-formgrid" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_leads_save' );
		echo '<input type="hidden" name="action" value="bomcas_leads_save" />';
		echo '<p class="is-wide bomcas-fieldline"><label class="bomcas-check"><input type="checkbox" name="enabled" value="1" ' . checked( ! empty( $settings['enabled'] ), true, false ) . ' /> ' . esc_html__( 'Show the lead form', 'bomcas-tax-calculators-canada' ) . '</label> ' . wp_kses( Bomcas_Admin_UI::tip( __( 'Off by default. The calculator still runs in the browser.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p class="bomcas-fieldline"><label for="bomcas-lead-notify">' . esc_html__( 'Owner email', 'bomcas-tax-calculators-canada' ) . '</label><input id="bomcas-lead-notify" type="email" name="notify" value="' . esc_attr( $settings['notify'] ) . '" />' . wp_kses( Bomcas_Admin_UI::tip( __( 'A copy of each lead is mailed here. The visitor reply-to is their own address.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p class="bomcas-fieldline"><label for="bomcas-lead-retention">' . esc_html__( 'Keep leads for (days)', 'bomcas-tax-calculators-canada' ) . '</label><input id="bomcas-lead-retention" type="number" min="1" name="retention" value="' . esc_attr( (string) $settings['retention'] ) . '" />' . wp_kses( Bomcas_Admin_UI::tip( __( 'Older rows are deleted. Export before that if you need a copy.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p class="bomcas-fieldline"><label for="bomcas-lead-rate">' . esc_html__( 'Max sends per hour', 'bomcas-tax-calculators-canada' ) . '</label><input id="bomcas-lead-rate" type="number" min="1" name="rate" value="' . esc_attr( (string) $settings['rate'] ) . '" />' . wp_kses( Bomcas_Admin_UI::tip( __( 'Limits repeats from the same network address. A hidden field catches simple bots.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p class="is-wide bomcas-fieldline"><label for="bomcas-lead-consent">' . esc_html__( 'Consent sentence', 'bomcas-tax-calculators-canada' ) . '</label><textarea id="bomcas-lead-consent" name="consent" rows="3">' . esc_textarea( $settings['consent'] ) . '</textarea>' . wp_kses( Bomcas_Admin_UI::tip( __( 'CASL and CAN-SPAM need a clear agreement and a way to stop.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p class="is-wide bomcas-fieldline"><label for="bomcas-lead-fields">' . esc_html__( 'Extra fields, one label per line', 'bomcas-tax-calculators-canada' ) . '</label><textarea id="bomcas-lead-fields" name="fields" rows="3">' . esc_textarea( $settings['fields'] ) . '</textarea>' . wp_kses( Bomcas_Admin_UI::tip( __( 'Each line becomes an extra question on the public form.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p class="bomcas-fieldline"><label for="bomcas-lead-captcha">' . esc_html__( 'reCAPTCHA secret (optional)', 'bomcas-tax-calculators-canada' ) . '</label><input id="bomcas-lead-captcha" type="password" name="captcha_secret" value="" placeholder="' . esc_attr__( 'Leave blank to keep', 'bomcas-tax-calculators-canada' ) . '" />' . wp_kses( Bomcas_Admin_UI::tip( __( 'Used only when a visitor sends the form. Leave empty to skip captcha.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p class="bomcas-fieldline"><label for="bomcas-lead-turnstile">' . esc_html__( 'Turnstile secret (optional)', 'bomcas-tax-calculators-canada' ) . '</label><input id="bomcas-lead-turnstile" type="password" name="turnstile_secret" value="" placeholder="' . esc_attr__( 'Leave blank to keep', 'bomcas-tax-calculators-canada' ) . '" />' . wp_kses( Bomcas_Admin_UI::tip( __( 'Cloudflare Turnstile is an alternative to reCAPTCHA. Leave empty to keep the saved secret.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<div class="bomcas-savebar">';
		submit_button( __( 'Save lead form', 'bomcas-tax-calculators-canada' ) );
		echo '</div></form></section>';
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Leads', 'bomcas-tax-calculators-canada' ) . '</h2>';
		echo '<form method="get" class="bomcas-filters">';
		wp_nonce_field( 'bomcas_leads_filter' );
		echo '<input type="hidden" name="page" value="bomcas-leads" />';
		echo '<label for="bomcas-lead-search">' . esc_html__( 'Search', 'bomcas-tax-calculators-canada' ) . '<input id="bomcas-lead-search" type="search" name="s" value="' . esc_attr( $search ) . '" placeholder="' . esc_attr__( 'Name or email', 'bomcas-tax-calculators-canada' ) . '" /></label>';
		echo '<label for="bomcas-lead-calc">' . esc_html__( 'Calculator', 'bomcas-tax-calculators-canada' ) . '<select id="bomcas-lead-calc" name="calculator">';
		$tools = array(
			''                   => __( 'All calculators', 'bomcas-tax-calculators-canada' ),
			'sales-tax'          => __( 'Sales tax', 'bomcas-tax-calculators-canada' ),
			'income-tax'         => __( 'Income tax', 'bomcas-tax-calculators-canada' ),
			'mortgage-payment'   => __( 'Mortgage payment', 'bomcas-tax-calculators-canada' ),
			'payroll-deductions' => __( 'Payroll', 'bomcas-tax-calculators-canada' ),
			'corporate-tax'      => __( 'Corporate tax', 'bomcas-tax-calculators-canada' ),
		);
		foreach ( $tools as $id => $name ) {
			echo '<option value="' . esc_attr( $id ) . '"' . selected( $calc, $id, false ) . '>' . esc_html( $name ) . '</option>';
		}
		echo '</select></label>';
		echo '<div class="bomcas-buttongroup">';
		submit_button( __( 'Filter', 'bomcas-tax-calculators-canada' ), 'secondary', '', false );
		echo '<a class="button" href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=bomcas_leads_export&format=csv&calculator=' . rawurlencode( $calc ) ), 'bomcas_leads_export' ) ) . '">' . esc_html__( 'Export CSV', 'bomcas-tax-calculators-canada' ) . '</a>';
		echo '<a class="button" href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=bomcas_leads_export&format=xlsx&calculator=' . rawurlencode( $calc ) ), 'bomcas_leads_export' ) ) . '">' . esc_html__( 'Export XLSX', 'bomcas-tax-calculators-canada' ) . '</a>';
		echo '</div></form>';
		echo '<table class="widefat"><thead><tr><th>' . esc_html__( 'When', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Name', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Email', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Calculator', 'bomcas-tax-calculators-canada' ) . '</th><th class="bomcas-col-action">' . esc_html__( 'Action', 'bomcas-tax-calculators-canada' ) . '</th></tr></thead><tbody>';
		foreach ( (array) $rows as $row ) {
			$del  = wp_nonce_url( admin_url( 'admin-post.php?action=bomcas_leads_delete&id=' . (int) $row['id'] ), 'bomcas_leads_delete_' . (int) $row['id'] );
			$when = date_create( (string) $row['created_at'], wp_timezone() );
			echo '<tr><td>' . esc_html( $when ? $when->format( 'M j, Y, g:i A' ) : '' ) . '</td><td>' . esc_html( $row['person_name'] ) . '</td><td>' . esc_html( $row['email'] ) . '</td><td>' . esc_html( $row['calculator'] ) . '</td><td class="bomcas-col-action"><a class="bomcas-rowaction" href="' . esc_url( $del ) . '">' . esc_html__( 'Delete', 'bomcas-tax-calculators-canada' ) . '</a></td></tr>';
		}
		if ( ! $rows ) {
			echo '<tr><td colspan="5">' . esc_html__( 'No leads yet.', 'bomcas-tax-calculators-canada' ) . '</td></tr>';
		}
		echo '</tbody></table></section>';
		if ( ! defined( 'BOMCAS_PRO_VERSION' ) ) {
			echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Integrations', 'bomcas-tax-calculators-canada' ) . '</h2><p>' . esc_html__( 'Webhooks, Mailchimp, HubSpot and Google Sheets are part of Pro. Leads on this site keep working either way.', 'bomcas-tax-calculators-canada' ) . '</p></section>';
		} else {
			do_action( 'bomcas_leads_integrations_form' );
		}
		Bomcas_Admin_UI::close();
	}

	/**
	 * Save settings. Blank captcha secrets keep the previous value.
	 */
	public static function save_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot edit leads.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_leads_save' );
		$old = self::settings();
		$new = array(
			'enabled'          => empty( $_POST['enabled'] ) ? 0 : 1,
			'notify'           => sanitize_email( wp_unslash( $_POST['notify'] ?? '' ) ),
			'retention'        => max( 1, absint( wp_unslash( $_POST['retention'] ?? 365 ) ) ),
			'rate'             => max( 1, absint( wp_unslash( $_POST['rate'] ?? 8 ) ) ),
			'consent'          => sanitize_textarea_field( wp_unslash( $_POST['consent'] ?? '' ) ),
			'fields'           => sanitize_textarea_field( wp_unslash( $_POST['fields'] ?? '' ) ),
			'captcha_secret'   => sanitize_text_field( wp_unslash( $_POST['captcha_secret'] ?? '' ) ),
			'turnstile_secret' => sanitize_text_field( wp_unslash( $_POST['turnstile_secret'] ?? '' ) ),
		);
		if ( '' === $new['captcha_secret'] ) {
			$new['captcha_secret'] = $old['captcha_secret'] ?? '';
		}
		if ( '' === $new['turnstile_secret'] ) {
			$new['turnstile_secret'] = $old['turnstile_secret'] ?? '';
		}
		update_option( 'bomcas_lead_settings', $new );
		wp_safe_redirect( admin_url( 'admin.php?page=bomcas-leads&saved=1' ) );
		exit;
	}

	/**
	 * CSV or xlsx of the current filter.
	 */
	public static function export() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot export leads.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_leads_export' );
		$calc = sanitize_key( wp_unslash( $_GET['calculator'] ?? '' ) );
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE;
		if ( '' === $calc ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT created_at, person_name, email, phone, calculator, results FROM %i ORDER BY id DESC', $table ), ARRAY_A );
		} else {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT created_at, person_name, email, phone, calculator, results FROM %i WHERE calculator = %s ORDER BY id DESC', $table, $calc ), ARRAY_A );
		}
		$grid = array( array( 'created_at', 'name', 'email', 'phone', 'calculator', 'results' ) );
		foreach ( (array) $rows as $row ) {
			$grid[] = array( $row['created_at'], $row['person_name'], $row['email'], $row['phone'], $row['calculator'], $row['results'] );
		}
		$format = isset( $_GET['format'] ) && 'xlsx' === sanitize_key( wp_unslash( $_GET['format'] ) ) ? 'xlsx' : 'csv';
		if ( 'xlsx' === $format ) {
			$path = wp_tempnam( 'leads.xlsx' );
			Bomcas_Xlsx::write( $path, array( 'Leads' => $grid ) );
			bomcas_calc_download( $path, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'leads.xlsx' );
		}
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="leads.csv"' );
		bomcas_calc_stream_csv( $grid );
		exit;
	}

	/**
	 * Delete one lead.
	 */
	public static function delete_one() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot delete leads.', 'bomcas-tax-calculators-canada' ) );
		}
		$id = absint( wp_unslash( $_GET['id'] ?? 0 ) );
		check_admin_referer( 'bomcas_leads_delete_' . $id );
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->delete( $wpdb->prefix . self::TABLE, array( 'id' => $id ), array( '%d' ) );
		wp_safe_redirect( admin_url( 'admin.php?page=bomcas-leads&deleted=1' ) );
		exit;
	}
}
