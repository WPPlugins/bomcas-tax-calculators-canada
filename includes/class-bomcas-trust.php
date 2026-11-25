<?php
/**
 * Source dates, rate reports, alerts, share links, compare, onboarding.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Trust, share and setup tools that stay in Lite.
 */
class Bomcas_Trust {

	/**
	 * Hooks.
	 */
	public static function boot() {
		add_action( 'admin_post_bomcas_trust_save', array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_bomcas_rate_issue', array( __CLASS__, 'receive_issue' ) );
		add_action( 'wp_ajax_nopriv_bomcas_rate_issue', array( __CLASS__, 'receive_issue' ) );
		add_action( 'admin_notices', array( __CLASS__, 'notice' ) );
		add_shortcode( 'bomcas_rate_changelog', array( __CLASS__, 'changelog' ) );
		add_shortcode( 'bomcas_compare', array( __CLASS__, 'compare' ) );
		add_shortcode( 'bomcas_docs', array( __CLASS__, 'docs' ) );
		add_shortcode( 'bomcas_roadmap', array( __CLASS__, 'roadmap_public' ) );
		add_action( 'init', array( __CLASS__, 'share_boot' ) );
	}

	/**
	 * @return array
	 */
	public static function settings() {
		$saved = get_option( 'bomcas_trust', array() );
		$base  = array(
			'show_verified' => 1,
			'verified_on'   => '2026-11-08',
			'sla_days'      => 5,
			'alerts'        => 0,
			'short_links'   => 0,
		);
		return array_merge( $base, is_array( $saved ) ? $saved : array() );
	}

	/**
	 * Line under the rate table.
	 *
	 * @param array $args View args.
	 * @return string
	 */
	public static function verified_html( $args ) {
		$settings = self::settings();
		if ( empty( $settings['show_verified'] ) ) {
			return '';
		}
		$when = self::verified_label( $settings['verified_on'] );
		/* translators: %s: a name, amount, count, or year. */
		$html  = '<p class="bomcas-note">' . esc_html( sprintf( __( 'Last verified: %s. Each source link is the publication used for that row.', 'bomcas-tax-calculators-canada' ), $when ) ) . '</p>';
		$html .= self::report_form( isset( $args['calc'] ) ? $args['calc'] : '' );
		return $html;
	}

	/**
	 * @param string $iso Date.
	 * @return string
	 */
	public static function verified_label( $iso ) {
		$time = strtotime( $iso . ' 12:00:00 UTC' );
		if ( ! $time ) {
			return $iso;
		}
		return date_i18n( 'j F Y', $time );
	}

	/**
	 * @param string $calc Calculator.
	 * @return string
	 */
	public static function report_form( $calc ) {
		$html  = '<form class="bomcas-lead" method="get" action="#" data-rate-issue>';
		$html .= '<h3>' . esc_html__( 'Report a rate issue', 'bomcas-tax-calculators-canada' ) . '</h3>';
		$html .= '<p class="bomcas-hp" aria-hidden="true"><label>Website <input type="text" name="company_website" tabindex="-1" autocomplete="off" /></label></p>';
		$html .= '<input type="hidden" name="calculator" value="' . esc_attr( $calc ) . '" />';
		$html .= '<p><label>' . esc_html__( 'Which rate', 'bomcas-tax-calculators-canada' ) . ' <input type="text" name="rate_name" required /></label></p>';
		$html .= '<p><label>' . esc_html__( 'What looks wrong', 'bomcas-tax-calculators-canada' ) . ' <input type="text" name="detail" required /></label></p>';
		$html .= '<p><label>' . esc_html__( 'Email, optional', 'bomcas-tax-calculators-canada' ) . ' <input type="email" name="email" /></label></p>';
		$html .= '<p><button type="submit">' . esc_html__( 'Send report', 'bomcas-tax-calculators-canada' ) . '</button></p>';
		$html .= '<p class="bomcas-note" data-issue-note></p></form>';
		return $html;
	}

	/**
	 * Store a report and hand it to the publisher when that plugin is present.
	 */
	public static function receive_issue() {
		check_ajax_referer( 'bomcas_growth', 'nonce' );
		$result = self::file_issue( wp_unslash( $_POST ) );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_code() ), 400 );
		}
		wp_send_json_success( array( 'id' => $result ) );
	}

	/**
	 * @param array $input Input.
	 * @return string|WP_Error
	 */
	public static function file_issue( $input ) {
		if ( ! empty( $input['company_website'] ) ) {
			return new WP_Error( 'spam' );
		}
		$name   = sanitize_text_field( $input['rate_name'] ?? '' );
		$detail = sanitize_text_field( $input['detail'] ?? '' );
		if ( '' === $name || '' === $detail ) {
			return new WP_Error( 'fields' );
		}
		$issue = array(
			'id'         => wp_generate_password( 8, false, false ),
			'created'    => time(),
			'calculator' => sanitize_key( $input['calculator'] ?? '' ),
			'rate_name'  => $name,
			'detail'     => $detail,
			'email'      => sanitize_email( $input['email'] ?? '' ),
			'status'     => 'new',
		);
		if ( class_exists( 'Bomcas_Lic_Growth' ) ) {
			return Bomcas_Lic_Growth::file_issue( $issue );
		}
		$all   = get_option( 'bomcas_rate_issues', array() );
		$all[] = $issue;
		update_option( 'bomcas_rate_issues', $all, false );
		return $issue['id'];
	}

	/**
	 * @param array $issue Issue.
	 * @return string
	 */
	public static function sla( $issue ) {
		$days = max( 1, (int) self::settings()['sla_days'] );
		$age  = ( time() - (int) $issue['created'] ) / DAY_IN_SECONDS;
		if ( in_array( $issue['status'], array( 'fixed', 'declined' ), true ) ) {
			return 'closed';
		}
		return $age > $days ? 'overdue' : 'within_sla';
	}

	/**
	 * Changelog of alerts and closed reports.
	 *
	 * @return string
	 */
	public static function changelog() {
		$rows = array();
		if ( class_exists( 'Bomcas_Lic_Growth' ) ) {
			$rows = Bomcas_Lic_Growth::changelog_rows();
		}
		$html = '<h2>' . esc_html__( 'Rate update changelog', 'bomcas-tax-calculators-canada' ) . '</h2>';
		if ( ! $rows ) {
			return $html . '<p>' . esc_html__( 'No published rate updates yet.', 'bomcas-tax-calculators-canada' ) . '</p>';
		}
		$html .= '<ul>';
		foreach ( $rows as $row ) {
			$html .= '<li>' . esc_html( $row ) . '</li>';
		}
		return $html . '</ul>';
	}

	/**
	 * In-plugin notice. Shown only after an account opts in.
	 */
	public static function notice() {
		if ( ! current_user_can( 'manage_options' ) || empty( self::settings()['alerts'] ) || ! bomcas_calc_is_plugin_screen() ) {
			return;
		}
		if ( ! class_exists( 'Bomcas_Lic_Growth' ) ) {
			return;
		}
		foreach ( Bomcas_Lic_Growth::pending_alerts( (int) get_option( 'bomcas_alerts_seen', 0 ) ) as $alert ) {
			echo '<div class="notice notice-info is-dismissible"><p>' . esc_html( $alert['title'] . ' — ' . $alert['body'] ) . '</p></div>';
			update_option( 'bomcas_alerts_seen', (int) $alert['id'] );
		}
	}

	/**
	 * Pro may pull a signed dataset only after an explicit opt-in.
	 *
	 * @return bool
	 */
	public static function should_pull() {
		return ( defined( 'BOMCAS_PRO_VERSION' ) && '' !== (string) get_option( 'bomcas_pro_license_key', '' ) ) || '1' === (string) get_option( 'bomcas_rates_optin', '' );
	}

	/**
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function compare( $atts ) {
		$atts = shortcode_atts(
			array(
				'left'  => 'BC',
				'right' => 'AB',
			),
			$atts,
			'bomcas_compare'
		);
		$diff = self::province_diff( $atts['left'], $atts['right'] );
		if ( ! $diff ) {
			return '';
		}
		/* translators: %s: a name, amount, count, or year. */
		$html  = '<table class="bomcas-table"><caption>' . esc_html( sprintf( __( '%1$s compared with %2$s', 'bomcas-tax-calculators-canada' ), $diff['left'], $diff['right'] ) ) . '</caption>';
		$html .= '<thead><tr><th>' . esc_html__( 'Item', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html( $diff['left'] ) . '</th><th>' . esc_html( $diff['right'] ) . '</th><th>' . esc_html__( 'Difference', 'bomcas-tax-calculators-canada' ) . '</th></tr></thead><tbody>';
		$html .= '<tr><td>' . esc_html__( 'First rate', 'bomcas-tax-calculators-canada' ) . '</td><td>' . esc_html( $diff['left_rate'] ) . '</td><td>' . esc_html( $diff['right_rate'] ) . '</td><td>' . esc_html( $diff['delta'] ) . '</td></tr>';
		$html .= '</tbody></table>';
		return $html;
	}

	/**
	 * @param string $left Code.
	 * @param string $right Code.
	 * @return array|null
	 */
	public static function province_diff( $left, $right ) {
		if ( ! function_exists( 'bomcas_calc_seed' ) ) {
			return null;
		}
		$pack  = bomcas_calc_seed( 'personal-tax-current.json' );
		$year  = class_exists( 'Bomcas_Tokens' ) ? Bomcas_Tokens::key() : '';
		$rows  = ( '' !== $year && isset( $pack['years'][ $year ]['jurisdictions'] ) ) ? $pack['years'][ $year ]['jurisdictions'] : array();
		$left  = strtoupper( $left );
		$right = strtoupper( $right );
		if ( empty( $rows[ $left ]['brackets'][0] ) || empty( $rows[ $right ]['brackets'][0] ) ) {
			return null;
		}
		$a = (float) $rows[ $left ]['brackets'][0]['rate'];
		$b = (float) $rows[ $right ]['brackets'][0]['rate'];
		return array(
			'left'       => $left,
			'right'      => $right,
			'left_rate'  => ( $a * 100 ) . '%',
			'right_rate' => ( $b * 100 ) . '%',
			'delta'      => round( ( $a - $b ) * 100, 2 ) . ' points',
		);
	}

	/**
	 * Signed result link. Values are calculator amounts, not names or emails.
	 *
	 * @param array $fields Fields.
	 * @return string
	 */
	public static function sign_result( $fields ) {
		$clean = array();
		foreach ( $fields as $key => $value ) {
			$key = sanitize_key( $key );
			if ( '' === $key || false !== strpos( (string) $value, '@' ) ) {
				continue;
			}
			$clean[ $key ] = sanitize_text_field( (string) $value );
		}
		$body = rtrim( strtr( base64_encode( wp_json_encode( $clean ) ), '+/', '-_' ), '=' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- URL-safe share link of calculator amounts, signed with HMAC. Not code.
		$sig  = substr( hash_hmac( 'sha256', $body, wp_salt( 'auth' ) ), 0, 16 );
		return $body . '.' . $sig;
	}

	/**
	 * @param string $token Token.
	 * @return array|null
	 */
	public static function open_result( $token ) {
		$parts = explode( '.', (string) $token, 2 );
		if ( 2 !== count( $parts ) ) {
			return null;
		}
		$expect = substr( hash_hmac( 'sha256', $parts[0], wp_salt( 'auth' ) ), 0, 16 );
		if ( ! hash_equals( $expect, $parts[1] ) ) {
			return null;
		}
		$json = base64_decode( strtr( $parts[0], '-_', '+/' ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Reads a share link after its HMAC matched. The result is JSON data, never run.
		$data = json_decode( (string) $json, true );
		return is_array( $data ) ? $data : null;
	}

	/**
	 * Apply a signed link to the fragment script.
	 */
	public static function share_boot() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- The query value is an HMAC-signed result token, checked in open_result().
		if ( empty( $_GET['bomcas_result'] ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Same signed token as above.
		$data = self::open_result( sanitize_text_field( wp_unslash( $_GET['bomcas_result'] ) ) );
		if ( ! $data ) {
			return;
		}
		add_action(
			'wp_enqueue_scripts',
			static function () use ( $data ) {
				wp_register_script( 'bomcas-share-link', false, array(), BOMCAS_CALC_VERSION, false );
				wp_enqueue_script( 'bomcas-share-link' );
				wp_add_inline_script( 'bomcas-share-link', 'location.hash=' . wp_json_encode( http_build_query( $data ) ) . ';' );
			}
		);
	}

	/**
	 * @return string
	 */
	public static function docs() {
		if ( isset( $_GET['_wpnonce'] ) ) {
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'bomcas_docs' );
		}
		$q    = isset( $_GET['docq'] ) ? sanitize_text_field( wp_unslash( $_GET['docq'] ) ) : '';
		$html = '<form method="get" action="#bomcas-docs"><p><label>' . esc_html__( 'Search help', 'bomcas-tax-calculators-canada' ) . ' <input type="search" name="docq" value="' . esc_attr( $q ) . '" /></label> ' . wp_nonce_field( 'bomcas_docs', '_wpnonce', true, false ) . '</p></form><div id="bomcas-docs">';
		foreach ( self::articles() as $article ) {
			if ( '' !== $q && false === stripos( $article['title'] . ' ' . $article['body'], $q ) ) {
				continue;
			}
			$html .= '<h2>' . esc_html( $article['title'] ) . '</h2><p>' . esc_html( $article['body'] ) . '</p>';
			if ( ! empty( $article['video'] ) ) {
				$html .= '<p><a href="' . esc_url( $article['video'] ) . '">' . esc_html__( 'Video', 'bomcas-tax-calculators-canada' ) . '</a></p>';
			}
		}
		return $html . '</div>';
	}

	/**
	 * @return array
	 */
	public static function articles() {
		$saved = get_option( 'bomcas_help_articles', array() );
		$base  = array(
			array(
				'title' => __( 'Turn on the lead form', 'bomcas-tax-calculators-canada' ),
				'body'  => __( 'Calculators, then Leads. The form stays off until you enable it. A visitor is emailed only after consent.', 'bomcas-tax-calculators-canada' ),
				'video' => '',
			),
			array(
				'title' => __( 'Privacy of calculator inputs', 'bomcas-tax-calculators-canada' ),
				'body'  => __( 'Amounts stay in the browser. A result is sent only when someone asks for an email or a PDF. Counts do not include those amounts. This follows PIPEDA, Quebec Law 25 and the CCPA expectation that a sale of personal information is off.', 'bomcas-tax-calculators-canada' ),
				'video' => '',
			),
			array(
				'title' => __( 'Accessibility', 'bomcas-tax-calculators-canada' ),
				'body'  => __( 'Fields have labels, buttons have names, and the report form can be used without a mouse. Automated checks are on the Trust screen. They are not a certification.', 'bomcas-tax-calculators-canada' ),
				'video' => '',
			),
		);
		return array_merge( $base, is_array( $saved ) ? $saved : array() );
	}

	/**
	 * @param string $html Page HTML.
	 * @return array
	 */
	public static function audit_html( $html ) {
		$fails = array();
		if ( ! preg_match( '/<label\b/i', $html ) ) {
			$fails[] = 'no_label';
		}
		if ( preg_match( '/<img\b(?![^>]*\balt=)/i', $html ) ) {
			$fails[] = 'img_without_alt';
		}
		if ( preg_match( '/<button\b[^>]*>\s*<\/button>/i', $html ) ) {
			$fails[] = 'empty_button';
		}
		if ( ! preg_match( '/<html[^>]*\blang=/i', $html ) && ! preg_match( '/lang=/i', $html ) ) {
			$fails[] = 'no_lang';
		}
		return array(
			'fails' => $fails,
			'note'  => 'Automated checks only. Not a WCAG certificate.',
		);
	}

	/**
	 * @return string
	 */
	public static function roadmap_public() {
		if ( ! class_exists( 'Bomcas_Lic_Growth' ) ) {
			return '';
		}
		return Bomcas_Lic_Growth::roadmap_html();
	}

	/**
	 * One-click industry setup. Does not turn calculators off.
	 *
	 * @param string $pack Pack id.
	 * @return bool
	 */
	public static function apply_template( $pack ) {
		if ( ! class_exists( 'Bomcas_Packs' ) || ! isset( Bomcas_Packs::all()[ $pack ] ) ) {
			return false;
		}
		$item                = Bomcas_Packs::all()[ $pack ];
		$settings            = Bomcas_Leads::settings();
		$settings['enabled'] = 1;
		update_option( 'bomcas_lead_settings', $settings );
		update_option(
			'bomcas_booking',
			array(
				'provider' => 'custom',
				'url'      => home_url( '/contact/' ),
				'label'    => $item['cta'],
				'mode'     => 'button',
			)
		);
		return true;
	}

	/**
	 * Menu.
	 */
	public static function menu() {
		add_submenu_page( 'bomcas-calculators-home', __( 'Trust', 'bomcas-tax-calculators-canada' ), __( 'Trust', 'bomcas-tax-calculators-canada' ), 'manage_options', 'bomcas-trust', array( __CLASS__, 'screen' ) );
		add_submenu_page( 'bomcas-calculators-home', __( 'Setup', 'bomcas-tax-calculators-canada' ), __( 'Setup', 'bomcas-tax-calculators-canada' ), 'manage_options', 'bomcas-setup', array( __CLASS__, 'wizard' ) );
	}

	/**
	 * Trust screen.
	 */
	public static function screen() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$settings = self::settings();
		Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
		Bomcas_Admin_UI::open(
			array(
				'doc'     => 'privacy',
				'product' => 'BOMCAS Canada',
				'kicker'  => __( 'Calculators · Trust', 'bomcas-tax-calculators-canada' ),
				'badge'   => __( 'Lite', 'bomcas-tax-calculators-canada' ),
				'help'    => __( 'The verified date is the day the bundled sources were read. Reports go to the publisher queue. Email alerts are off until you opt in.', 'bomcas-tax-calculators-canada' ),
				'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'trust' ) : array(),
			)
		);
		echo '<section class="bomcas-admin__card"><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_trust_save' );
		echo '<input type="hidden" name="action" value="bomcas_trust_save" />';
		echo '<p><label><input type="checkbox" name="show_verified" value="1" ' . checked( ! empty( $settings['show_verified'] ), true, false ) . ' /> ' . esc_html__( 'Show last verified on calculators', 'bomcas-tax-calculators-canada' ) . '</label> ' . wp_kses( Bomcas_Admin_UI::tip( __( 'Prints the date under the source table. Turn it off if you do not want the date on the public page.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p><label>' . esc_html__( 'Last verified', 'bomcas-tax-calculators-canada' ) . ' <input type="date" name="verified_on" value="' . esc_attr( $settings['verified_on'] ) . '" /></label> ' . wp_kses( Bomcas_Admin_UI::tip( __( 'Use the day you checked the official source. Do not invent a date.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p><label>' . esc_html__( 'Report SLA in days', 'bomcas-tax-calculators-canada' ) . ' <input type="number" min="1" name="sla_days" value="' . esc_attr( (string) $settings['sla_days'] ) . '" /></label> ' . wp_kses( Bomcas_Admin_UI::tip( __( 'A new report older than this is marked overdue on the publisher queue.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p><label><input type="checkbox" name="alerts" value="1" ' . checked( ! empty( $settings['alerts'] ), true, false ) . ' /> ' . esc_html__( 'Email and show rate alerts for this site', 'bomcas-tax-calculators-canada' ) . '</label> ' . wp_kses( Bomcas_Admin_UI::tip( __( 'Off by default. Alerts are composed on the licence server and never include visitor amounts.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</p>';
		echo '<p><label><input type="checkbox" name="short_links" value="1" ' . checked( ! empty( $settings['short_links'] ), true, false ) . ' /> ' . esc_html__( 'Store short codes for shared results', 'bomcas-tax-calculators-canada' ) . '</label></p>';
		echo '<div class="bomcas-savebar">';
		submit_button( __( 'Save trust settings', 'bomcas-tax-calculators-canada' ) );
		echo '</div>';
		echo '</form></section>';
		$sample = '<html lang="en"><label>Name</label><button>Send</button></html>';
		$audit  = self::audit_html( $sample );
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Accessibility checks', 'bomcas-tax-calculators-canada' ) . '</h2><p>' . esc_html__( 'These checks look for labels, empty buttons and images without alt text. They are not a WCAG 2.2 certificate.', 'bomcas-tax-calculators-canada' ) . '</p>';
		echo '<p>' . esc_html( $audit['fails'] ? implode( ', ', $audit['fails'] ) : __( 'Sample markup passed the automated checks.', 'bomcas-tax-calculators-canada' ) ) . '</p></section>';
		Bomcas_Admin_UI::close();
	}

	/**
	 * Setup wizard.
	 */
	public static function wizard() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
		Bomcas_Admin_UI::open(
			array(
				'doc'     => 'getting-started',
				'product' => 'BOMCAS Canada',
				'kicker'  => __( 'Calculators · Setup', 'bomcas-tax-calculators-canada' ),
				'badge'   => __( 'Wizard', 'bomcas-tax-calculators-canada' ),
				'help'    => __( 'Pick a country, leave the credit off unless you want it, then optionally publish an industry template. Templates add a page, a lead form and a booking button.', 'bomcas-tax-calculators-canada' ),
				'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'setup' ) : array(),
			)
		);
		echo '<ol class="bomcas-steps"><li class="is-on"><span>1</span>' . esc_html__( 'Country', 'bomcas-tax-calculators-canada' ) . '</li><li class="is-on"><span>2</span>' . esc_html__( 'Template', 'bomcas-tax-calculators-canada' ) . '</li><li class="is-on"><span>3</span>' . esc_html__( 'Booking', 'bomcas-tax-calculators-canada' ) . '</li></ol>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_trust_save' );
		echo '<input type="hidden" name="action" value="bomcas_trust_save" /><input type="hidden" name="wizard" value="1" />';
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Step 1. Country', 'bomcas-tax-calculators-canada' ) . '</h2><p class="desc">' . esc_html__( 'Choose where this site does most of its work. Calculators for the other country stay available.', 'bomcas-tax-calculators-canada' ) . '</p>';
		echo '<p class="bomcas-fieldline"><span class="bomcas-labelrow">' . esc_html__( 'Country focus', 'bomcas-tax-calculators-canada' ) . wp_kses( Bomcas_Admin_UI::tip( __( 'Saved on this site. It does not hide a calculator by itself.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</span><select name="country"><option value="CA">' . esc_html__( 'Canada', 'bomcas-tax-calculators-canada' ) . '</option></select></p></section>';
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Step 2. Industry template', 'bomcas-tax-calculators-canada' ) . '</h2><p class="desc">' . esc_html__( 'A template publishes a landing page and turns on the lead form.', 'bomcas-tax-calculators-canada' ) . '</p>';
		echo '<p class="bomcas-fieldline"><span class="bomcas-labelrow">' . esc_html__( 'Template', 'bomcas-tax-calculators-canada' ) . wp_kses( Bomcas_Admin_UI::tip( __( 'Leave this as None if you only want to save the country.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</span><select name="template"><option value="">' . esc_html__( 'None', 'bomcas-tax-calculators-canada' ) . '</option>';
		if ( class_exists( 'Bomcas_Packs' ) ) {
			foreach ( Bomcas_Packs::all() as $id => $pack ) {
				echo '<option value="' . esc_attr( $id ) . '">' . esc_html( $pack['title'] ) . '</option>';
			}
		}
		echo '</select></p></section>';
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Step 3. Booking', 'bomcas-tax-calculators-canada' ) . '</h2><p class="desc">' . esc_html__( 'Optional link shown after a result. The button wording follows the calculator.', 'bomcas-tax-calculators-canada' ) . '</p>';
		echo '<p class="bomcas-fieldline"><span class="bomcas-labelrow">' . esc_html__( 'Booking URL', 'bomcas-tax-calculators-canada' ) . wp_kses( Bomcas_Admin_UI::tip( __( 'Calendly, Cal.com, Acuity, or any other page.', 'bomcas-tax-calculators-canada' ) ), Bomcas_Admin_UI::tip_allowed() ) . '</span><input type="url" name="booking" class="regular-text" placeholder="https://" /></p>';
		submit_button( __( 'Finish setup', 'bomcas-tax-calculators-canada' ) );
		echo '</section></form>';
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Help', 'bomcas-tax-calculators-canada' ) . '</h2>' . wp_kses(
			self::docs(),
			array(
				'form'  => array(
					'method' => true,
					'action' => true,
				),
				'p'     => array(),
				'label' => array(),
				'input' => array(
					'type'  => true,
					'name'  => true,
					'id'    => true,
					'value' => true,
				),
				'div'   => array( 'id' => true ),
				'h2'    => array(),
				'a'     => array( 'href' => true ),
			)
		) . '</section>';
		Bomcas_Admin_UI::close();
	}

	/**
	 * Save.
	 */
	public static function save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot edit trust settings.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_trust_save' );
		if ( ! empty( $_POST['wizard'] ) ) {
			update_option( 'bomcas_setup_country', sanitize_key( wp_unslash( $_POST['country'] ?? 'both' ) ) );
			$template = sanitize_key( wp_unslash( $_POST['template'] ?? '' ) );
			if ( '' !== $template ) {
				self::apply_template( $template );
			}
			if ( ! empty( $_POST['booking'] ) ) {
				$booking        = get_option( 'bomcas_booking', array() );
				$booking['url'] = esc_url_raw( wp_unslash( $_POST['booking'] ) );
				update_option( 'bomcas_booking', $booking );
			}
			wp_safe_redirect( admin_url( 'admin.php?page=bomcas-setup&saved=1' ) );
			exit;
		}
		update_option(
			'bomcas_trust',
			array(
				'show_verified' => empty( $_POST['show_verified'] ) ? 0 : 1,
				'verified_on'   => sanitize_text_field( wp_unslash( $_POST['verified_on'] ?? '2026-11-08' ) ),
				'sla_days'      => max( 1, absint( wp_unslash( $_POST['sla_days'] ?? 5 ) ) ),
				'alerts'        => empty( $_POST['alerts'] ) ? 0 : 1,
				'short_links'   => empty( $_POST['short_links'] ) ? 0 : 1,
			)
		);
		wp_safe_redirect( admin_url( 'admin.php?page=bomcas-trust&saved=1' ) );
		exit;
	}
}
