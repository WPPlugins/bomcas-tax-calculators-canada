<?php
/**
 * Customer rate screen. Overrides live in their own option.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rates & Tax Data.
 */
class Bomcas_Rates_Admin {

	/**
	 * Hooks.
	 */
	public static function boot() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_post_bomcas_rates_save', array( __CLASS__, 'save' ) );
		add_action( 'admin_post_bomcas_rates_pull', array( __CLASS__, 'pull_now' ) );
		add_action( 'admin_post_bomcas_rates_export', array( __CLASS__, 'export' ) );
		add_action( 'admin_post_bomcas_rates_import', array( __CLASS__, 'import' ) );
		add_action( 'bomcas_rates_pull', array( 'Bomcas_Rates', 'pull' ) );
		add_action( 'admin_notices', array( __CLASS__, 'critical_notice' ) );
	}

	/**
	 * Menu.
	 */
	public static function menu() {
		add_options_page(
			__( 'Rates & Tax Data', 'bomcas-tax-calculators-canada' ),
			__( 'Rates & Tax Data', 'bomcas-tax-calculators-canada' ),
			'manage_options',
			'bomcas-rates',
			array( __CLASS__, 'render' )
		);
	}

	/**
	 * Critical pushes are announced to people who may manage rates.
	 */
	public static function critical_notice() {
		if ( ! Bomcas_Rates::user_may_manage() || ! bomcas_calc_is_plugin_screen() ) {
			return;
		}
		$pushed = get_option( 'bomcas_rates_pushed', array() );
		if ( empty( $pushed['critical'] ) || get_user_meta( get_current_user_id(), 'bomcas_rates_critical_seen', true ) === (string) ( $pushed['version'] ?? '' ) ) {
			return;
		}
		echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'A critical rate update is available on Rates & Tax Data. Calculators keep using your overrides.', 'bomcas-tax-calculators-canada' ) . '</p></div>';
	}

	/**
	 * Screen.
	 */
	public static function render() {
		if ( ! Bomcas_Rates::user_may_manage() ) {
			Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
			Bomcas_Admin_UI::open(
				array(
					'doc'     => 'rates',
					'product' => __( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ),
					'kicker'  => __( 'Rates and overrides', 'bomcas-tax-calculators-canada' ),
					'badge'   => __( 'Hidden', 'bomcas-tax-calculators-canada' ),
					'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'rates' ) : array(),
				)
			);
			echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Rate tools are hidden', 'bomcas-tax-calculators-canada' ) . '</h2><p>' . esc_html__( 'White-label is on for this user. Overrides and imports stay off this screen.', 'bomcas-tax-calculators-canada' ) . '</p></section>';
			Bomcas_Admin_UI::close();
			return;
		}
		$picked = self::selection();
		$file   = $picked['file'];
		$filter = $picked['filter'];
		$files  = Bomcas_Rates::bundled_files();
		if ( ! isset( $files[ $file ] ) ) {
			$file = 'sales-tax.json';
		}
		$bundled = is_readable( $files[ $file ] ) ? json_decode( file_get_contents( $files[ $file ] ), true ) : array();
		$pushed  = get_option( 'bomcas_rates_pushed', array() );
		$system  = Bomcas_Rates::resolve( $file, $bundled, is_array( $pushed ) ? $pushed : array(), array() );
		$rows    = Bomcas_Rates::leaves( $system['data'] );
		$meta    = ( is_array( $pushed ) && isset( $pushed['meta'][ $file ] ) && is_array( $pushed['meta'][ $file ] ) ) ? $pushed['meta'][ $file ] : array();
		$saved   = get_option( 'bomcas_rates_overrides', array() );
		$mine    = array();
		foreach ( (array) $saved as $row ) {
			if ( is_array( $row ) && ( $row['file'] ?? '' ) === $file ) {
				$mine[ $row['path'] ] = $row;
			}
		}
		$conflicts = array();
		foreach ( Bomcas_Rates::conflicts() as $conflict ) {
			if ( $conflict['file'] === $file ) {
				$conflicts[ $conflict['path'] ] = $conflict;
			}
		}
		Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
		Bomcas_Admin_UI::open(
			array(
				'doc'     => 'rates',
				'product' => __( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ),
				'help'    => __( 'Pick a country, a jurisdiction, a rate type, and a tax year. The table shows only that slice. Export it, or import a file after the dry-run lists any row errors.', 'bomcas-tax-calculators-canada' ),
				'kicker'  => __( 'Calculators · Rates and overrides', 'bomcas-tax-calculators-canada' ),
				'badge'   => (string) get_option( 'bomcas_calc_plan_label', __( 'Lite', 'bomcas-tax-calculators-canada' ) ),
				'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'rates' ) : array(),
			)
		);
		if ( class_exists( 'Bomcas_Tokens' ) ) {
			Bomcas_Tokens::year_form();
		}
		echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Rate version', 'bomcas-tax-calculators-canada' ) . '</h2>';
		$pushed_version = is_array( $pushed ) ? (string) ( $pushed['version'] ?? '' ) : '';
		$pushed_at      = (int) get_option( 'bomcas_rates_pushed_at', 0 );
		$status         = get_option( 'bomcas_rates_pull_status', array() );
		echo '<p>' . esc_html( '' !== $pushed_version ? sprintf( /* translators: 1: version, 2: date */ __( 'Signed pack %1$s, saved %2$s. A failed check returns the calculators to the rates shipped in the plugin.', 'bomcas-tax-calculators-canada' ), $pushed_version, wp_date( 'M j, Y g:i a', $pushed_at ) ) : __( 'Using the rates shipped in the plugin. No signed pack is saved.', 'bomcas-tax-calculators-canada' ) ) . '</p>';
		if ( defined( 'BOMCAS_PRO_VERSION' ) ) {
			echo '<p>' . esc_html__( 'Pro checks accountantsexpert.com once a day when a licence key is saved. The request sends that key. The signature and checksum must match, or the bundled rates stay in use.', 'bomcas-tax-calculators-canada' ) . '</p>';
		} else {
			echo '<p>' . esc_html__( 'Lite receives a new tax year in a plugin update from WordPress.org. A remote download stays off until you opt in below. Opting in sends no calculator inputs and no licence key.', 'bomcas-tax-calculators-canada' ) . '</p>';
		}
		if ( is_array( $status ) && ! empty( $status['error'] ) ) {
			echo '<p>' . esc_html( sprintf( /* translators: %s: error code */ __( 'Last check: %s.', 'bomcas-tax-calculators-canada' ), (string) $status['error'] ) ) . '</p>';
		}
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_rates_pull' );
		echo '<input type="hidden" name="action" value="bomcas_rates_pull" />';
		submit_button( __( 'Update now', 'bomcas-tax-calculators-canada' ), 'secondary' );
		echo '</form></section>';
		if ( $conflicts ) {
			echo '<div class="notice notice-info"><p>' . esc_html__( 'BOMCAS updated a value you had overridden. Your override is still active.', 'bomcas-tax-calculators-canada' ) . '</p><ul>';
			foreach ( $conflicts as $conflict ) {
				echo '<li>' . esc_html( sprintf( 'BOMCAS updated this value from %1$s to %2$s; your override %3$s is still active (%4$s).', $conflict['from'], $conflict['to'], $conflict['override'], $conflict['path'] ) ) . '</li>';
			}
			echo '</ul></div>';
		}
		echo '<p class="bomcas-crumbs-admin">' . esc_html( $picked['crumb'] ) . '</p>';
		echo '<form method="get" class="bomcas-fieldline"><input type="hidden" name="page" value="bomcas-rates" />';
		wp_nonce_field( 'bomcas_rates_pick' );
		echo '<label>' . esc_html__( 'Country', 'bomcas-tax-calculators-canada' ) . ' <select name="country"><option value="CA"' . selected( $picked['country'], 'CA', false ) . '>Canada</option></select></label>';
		echo '<label>' . esc_html__( 'Jurisdiction', 'bomcas-tax-calculators-canada' ) . ' <input type="search" name="jurisdiction" value="' . esc_attr( $picked['jurisdiction'] ) . '" placeholder="' . esc_attr__( 'BC, CA, FED', 'bomcas-tax-calculators-canada' ) . '" /></label>';
		echo '<label>' . esc_html__( 'Rate type', 'bomcas-tax-calculators-canada' ) . ' <select name="rate_type">';
		foreach ( array(
			'income'  => __( 'Income tax', 'bomcas-tax-calculators-canada' ),
			'sales'   => __( 'Sales tax', 'bomcas-tax-calculators-canada' ),
			'payroll' => __( 'Payroll', 'bomcas-tax-calculators-canada' ),
			'federal' => __( 'Federal', 'bomcas-tax-calculators-canada' ),
		) as $key => $label ) {
			echo '<option value="' . esc_attr( $key ) . '"' . selected( $picked['type'], $key, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select></label>';
		echo '<label>' . esc_html__( 'Tax year', 'bomcas-tax-calculators-canada' ) . ' <input type="number" name="tax_year" value="' . esc_attr( $picked['year'] ) . '" /></label>';
		echo '<div class="bomcas-savebar">';
		submit_button( __( 'Show this slice', 'bomcas-tax-calculators-canada' ), 'secondary', '', false );
		echo '</div></form>';
		echo '<p>';
		foreach ( array( 'csv', 'json', 'xlsx' ) as $fmt ) {
			$url = wp_nonce_url( admin_url( 'admin-post.php?action=bomcas_rates_export&format=' . $fmt . '&dataset=' . rawurlencode( $file ) . '&jurisdiction=' . rawurlencode( $filter ) ), 'bomcas_rates_export' );
			/* translators: %s: a name, amount, count, or year. */
			echo '<a class="button" href="' . esc_url( $url ) . '">' . esc_html( sprintf( __( 'Export %s', 'bomcas-tax-calculators-canada' ), strtoupper( $fmt ) ) ) . '</a> ';
		}
		$blank = wp_nonce_url( admin_url( 'admin-post.php?action=bomcas_rates_export&format=template' ), 'bomcas_rates_export' );
		echo '<a class="button" href="' . esc_url( $blank ) . '">' . esc_html__( 'Download blank template', 'bomcas-tax-calculators-canada' ) . '</a></p>';
		$preview = get_transient( 'bomcas_rates_import_preview' );
		if ( is_array( $preview ) ) {
			/* translators: %s: a name, amount, count, or year. */
			echo '<div class="notice notice-info"><p>' . esc_html( sprintf( __( 'Dry run: %1$d rows ready, %2$d errors.', 'bomcas-tax-calculators-canada' ), count( $preview['ok'] ), count( $preview['errors'] ) ) ) . '</p>';
			foreach ( array_slice( $preview['errors'], 0, 8 ) as $error ) {
				echo '<p>' . esc_html( $error ) . '</p>';
			}
			echo '</div>';
		}
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" enctype="multipart/form-data">';
		wp_nonce_field( 'bomcas_rates_import' );
		echo '<input type="hidden" name="action" value="bomcas_rates_import" />';
		echo '<p><label>' . esc_html__( 'Import CSV, JSON, or XLSX', 'bomcas-tax-calculators-canada' ) . ' <input type="file" name="sheet" /></label> ';
		submit_button( __( 'Dry run', 'bomcas-tax-calculators-canada' ), 'secondary', 'dry_run', false );
		echo ' ';
		submit_button( __( 'Apply as overrides', 'bomcas-tax-calculators-canada' ), 'primary', 'apply_import', false );
		echo '</p></form>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_rates_save' );
		echo '<input type="hidden" name="action" value="bomcas_rates_save" /><input type="hidden" name="dataset" value="' . esc_attr( $file ) . '" />';
		echo '<p><label><input type="checkbox" name="show_date" value="1" ' . checked( get_option( 'bomcas_rates_show_date', '1' ), '1', false ) . ' /> ' . esc_html__( 'Show “Rates last updated” on calculators', 'bomcas-tax-calculators-canada' ) . '</label></p>';
		if ( ! defined( 'BOMCAS_PRO_VERSION' ) ) {
			echo '<p><label><input type="checkbox" name="optin" value="1" ' . checked( get_option( 'bomcas_rates_optin', '0' ), '1', false ) . ' /> ' . esc_html__( 'Opt in to download rate updates from the address below. Off leaves this site on the copy shipped in the plugin.', 'bomcas-tax-calculators-canada' ) . '</label></p>';
		}
		echo '<p><label>' . esc_html__( 'Rate server', 'bomcas-tax-calculators-canada' ) . ' <input class="regular-text" name="server" value="' . esc_attr( (string) get_option( 'bomcas_rates_server', '' ) ) . '" placeholder="https://accountantsexpert.com/wp-json/bomcas-licensing/v1/rates" /></label></p>';
		echo '<p><label>' . esc_html__( 'Publisher public key', 'bomcas-tax-calculators-canada' ) . '<br /><textarea name="public_key" rows="4" class="large-text" placeholder="' . esc_attr( get_option( 'bomcas_rates_public_key' ) ? __( 'Saved. Paste a replacement to change it.', 'bomcas-tax-calculators-canada' ) : __( 'Paste the PEM public key', 'bomcas-tax-calculators-canada' ) ) . '">' . esc_textarea( (string) get_option( 'bomcas_rates_public_key', '' ) ) . '</textarea></label></p>';
		echo '<table class="widefat"><thead><tr><th>' . esc_html__( 'Value', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'System', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Source', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Verified', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Override', 'bomcas-tax-calculators-canada' ) . '</th><th></th></tr></thead><tbody>';
		$shown = 0;
		foreach ( $rows as $row ) {
			if ( '' !== $filter && false === stripos( $row['path'], $filter ) ) {
				continue;
			}
			if ( $shown >= 80 ) {
				break;
			}
			++$shown;
			$path = $row['path'];
			$info = isset( $meta[ $path ] ) && is_array( $meta[ $path ] ) ? $meta[ $path ] : array();
			$over = $mine[ $path ]['value'] ?? '';
			echo '<tr><td><code>' . esc_html( $path ) . '</code></td><td>' . esc_html( (string) $row['value'] ) . '</td><td>';
			if ( ! empty( $info['source_url'] ) ) {
				echo '<a href="' . esc_url( $info['source_url'] ) . '">' . esc_html__( 'Source', 'bomcas-tax-calculators-canada' ) . '</a>';
			}
			echo '</td><td>' . esc_html( (string) ( $info['verified'] ?? '' ) ) . '</td><td><input name="override[' . esc_attr( $path ) . ']" value="' . esc_attr( (string) $over ) . '" /></td><td>';
			if ( isset( $conflicts[ $path ] ) ) {
				echo '<button name="keep[]" value="' . esc_attr( $path ) . '" class="button">' . esc_html__( 'Keep', 'bomcas-tax-calculators-canada' ) . '</button> ';
				echo '<button name="use_new[]" value="' . esc_attr( $path ) . '" class="button">' . esc_html__( 'Use new value', 'bomcas-tax-calculators-canada' ) . '</button>';
			}
			echo '</td></tr>';
		}
		echo '</tbody></table>';
		submit_button( __( 'Save overrides', 'bomcas-tax-calculators-canada' ) );
		echo '<p><button name="reset_all" value="1" class="button">' . esc_html__( 'Reset all overrides in this dataset', 'bomcas-tax-calculators-canada' ) . '</button></p>';
		echo '</form>';
		$export = wp_json_encode( $saved );
		echo '<h2>' . esc_html__( 'Export and import', 'bomcas-tax-calculators-canada' ) . '</h2>';
		echo '<textarea class="large-text" rows="6" readonly>' . esc_textarea( $export ? $export : '[]' ) . '</textarea>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_rates_save' );
		echo '<input type="hidden" name="action" value="bomcas_rates_save" /><input type="hidden" name="dataset" value="' . esc_attr( $file ) . '" />';
		echo '<p><textarea name="import_json" class="large-text" rows="4" placeholder="' . esc_attr__( 'Paste an override export', 'bomcas-tax-calculators-canada' ) . '"></textarea></p>';
		submit_button( __( 'Import overrides', 'bomcas-tax-calculators-canada' ) );
		echo '</form></section>';
		Bomcas_Admin_UI::close();
		$pushed_version = is_array( $pushed ) ? (string) ( $pushed['version'] ?? '' ) : '';
		if ( '' !== $pushed_version ) {
			update_user_meta( get_current_user_id(), 'bomcas_rates_critical_seen', $pushed_version );
		}
	}

	/**
	 * Save overrides. Empty fields do not create an override.
	 */
	public static function save() {
		if ( ! Bomcas_Rates::user_may_manage() ) {
			wp_die( esc_html__( 'You cannot change rates.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_rates_save' );
		update_option( 'bomcas_rates_show_date', empty( $_POST['show_date'] ) ? '0' : '1' );
		if ( ! defined( 'BOMCAS_PRO_VERSION' ) ) {
			update_option( 'bomcas_rates_optin', empty( $_POST['optin'] ) ? '0' : '1' );
			self::schedule();
		}
		if ( isset( $_POST['server'] ) ) {
			update_option( 'bomcas_rates_server', esc_url_raw( wp_unslash( $_POST['server'] ) ) );
		}
		if ( ! empty( $_POST['public_key'] ) ) {
			update_option( 'bomcas_rates_public_key', sanitize_textarea_field( wp_unslash( $_POST['public_key'] ) ) );
		}
		$file = isset( $_POST['dataset'] ) ? sanitize_file_name( wp_unslash( $_POST['dataset'] ) ) : '';
		$rows = get_option( 'bomcas_rates_overrides', array() );
		$rows = is_array( $rows ) ? $rows : array();
		if ( ! empty( $_POST['import_json'] ) ) {
			$decoded = json_decode( sanitize_textarea_field( wp_unslash( $_POST['import_json'] ) ), true );
			if ( is_array( $decoded ) ) {
				$rows = $decoded;
			}
		}
		if ( ! empty( $_POST['reset_all'] ) && '' !== $file ) {
			$rows = array_values(
				array_filter(
					$rows,
					static function ( $row ) use ( $file ) {
						return ! is_array( $row ) || ( $row['file'] ?? '' ) !== $file;
					}
				)
			);
		}
		$files   = Bomcas_Rates::bundled_files();
		$bundled = ( '' !== $file && isset( $files[ $file ] ) && is_readable( $files[ $file ] ) ) ? json_decode( file_get_contents( $files[ $file ] ), true ) : array();
		$pushed  = get_option( 'bomcas_rates_pushed', array() );
		$system  = Bomcas_Rates::resolve( $file, is_array( $bundled ) ? $bundled : array(), is_array( $pushed ) ? $pushed : array(), array() );
		$posted  = isset( $_POST['override'] ) && is_array( $_POST['override'] ) ? map_deep( wp_unslash( $_POST['override'] ), 'sanitize_text_field' ) : array();
		$keep    = isset( $_POST['keep'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['keep'] ) ) : array();
		$use_new = isset( $_POST['use_new'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['use_new'] ) ) : array();
		foreach ( $posted as $path => $raw ) {
			$path = sanitize_text_field( $path );
			if ( in_array( $path, $use_new, true ) || '' === trim( (string) $raw ) ) {
				$rows = self::drop( $rows, $file, $path );
				continue;
			}
			if ( ! is_numeric( $raw ) ) {
				continue;
			}
			$value = self::cast( $raw );
			$base  = Bomcas_Rates::get_path( $system['data'], $path );
			$found = false;
			foreach ( $rows as $i => $row ) {
				if ( is_array( $row ) && ( $row['file'] ?? '' ) === $file && ( $row['path'] ?? '' ) === $path ) {
					if ( in_array( $path, $keep, true ) ) {
						$rows[ $i ]['base'] = $base;
					}
					$rows[ $i ]['value'] = $value;
					$found               = true;
				}
			}
			if ( ! $found && Bomcas_Rates::norm( $value ) !== Bomcas_Rates::norm( $base ) ) {
				$rows[] = array(
					'file'  => $file,
					'path'  => $path,
					'value' => $value,
					'base'  => $base,
				);
			}
		}
		update_option( 'bomcas_rates_overrides', array_values( $rows ), false );
		if ( class_exists( 'Bomcas_Tokens' ) ) {
			Bomcas_Tokens::purge();
		}
		wp_safe_redirect( admin_url( 'options-general.php?page=bomcas-rates&dataset=' . rawurlencode( $file ) . '&saved=1' ) );
		exit;
	}

	/**
	 * Explicit pull. The button is the user action.
	 */
	public static function pull_now() {
		if ( ! Bomcas_Rates::user_may_manage() ) {
			wp_die( esc_html__( 'You cannot update rates.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_rates_pull' );
		Bomcas_Rates::pull();
		if ( class_exists( 'Bomcas_Tokens' ) ) {
			Bomcas_Tokens::purge();
		}
		wp_safe_redirect( admin_url( 'options-general.php?page=bomcas-rates&pulled=1' ) );
		exit;
	}

	/**
	 * Daily pull only after Pro is present or Lite opt-in is saved.
	 */
	public static function schedule() {
		$allowed = ( defined( 'BOMCAS_PRO_VERSION' ) && '' !== (string) get_option( 'bomcas_pro_license_key', '' ) ) || ( ! defined( 'BOMCAS_PRO_VERSION' ) && '1' === (string) get_option( 'bomcas_rates_optin', '0' ) );
		if ( $allowed && ! wp_next_scheduled( 'bomcas_rates_pull' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'bomcas_rates_pull' );
		}
		if ( ! $allowed ) {
			wp_clear_scheduled_hook( 'bomcas_rates_pull' );
		}
	}

	/**
	 * @param array  $rows Rows.
	 * @param string $file File.
	 * @param string $path Path.
	 * @return array
	 */
	private static function drop( $rows, $file, $path ) {
		return array_values(
			array_filter(
				$rows,
				static function ( $row ) use ( $file, $path ) {
					return ! is_array( $row ) || ( $row['file'] ?? '' ) !== $file || ( $row['path'] ?? '' ) !== $path;
				}
			)
		);
	}

	/**
	 * @param mixed $raw Posted number.
	 * @return float|int
	 */
	private static function cast( $raw ) {
		$string = trim( (string) $raw );
		if ( false !== strpos( $string, '.' ) ) {
			return (float) $string;
		}
		return (int) $string;
	}

	/**
	 * Guided selection.
	 *
	 * @return array
	 */
	private static function selection() {
		if ( isset( $_GET['_wpnonce'] ) ) {
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'bomcas_rates_pick' );
		}
		$country = 'CA';
		$type    = isset( $_GET['rate_type'] ) ? sanitize_key( wp_unslash( $_GET['rate_type'] ) ) : 'income';
		$year    = isset( $_GET['tax_year'] ) ? sanitize_text_field( wp_unslash( $_GET['tax_year'] ) ) : '2026';
		$juris   = isset( $_GET['jurisdiction'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_GET['jurisdiction'] ) ) ) : 'BC';
		$map     = array(
			'CA' => array(
				'income'  => 'personal-tax-current.json',
				'sales'   => 'sales-tax.json',
				'payroll' => 'payroll-constants.json',
				'federal' => 'personal-tax-current.json',
			),
		);
		$file  = $map[ $country ][ $type ] ?? $map[ $country ]['income'];
		$files = Bomcas_Rates::bundled_files();
		if ( ! isset( $files[ $file ] ) ) {
			$file = 'sales-tax.json';
		}
		return array(
			'country'      => $country,
			'type'         => $type,
			'year'         => $year,
			'jurisdiction' => $juris,
			'file'         => $file,
			'filter'       => $juris,
			'crumb'        => $country . ' / ' . $juris . ' / ' . $type . ' / ' . $year,
		);
	}

	/**
	 * Download the current slice or a blank workbook.
	 */
	public static function export() {
		if ( ! Bomcas_Rates::user_may_manage() ) {
			wp_die( esc_html__( 'You cannot export rates.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_rates_export' );
		$format  = sanitize_key( wp_unslash( $_GET['format'] ?? 'csv' ) );
		$header  = array( 'file', 'path', 'value', 'source_url', 'effective', 'verified', 'notes' );
		$example = array( 'sales-tax.json', 'jurisdictions/BC/0/pst', '0.07', 'https://www2.gov.bc.ca/gov/content/taxes/sales-taxes/pst', '2026-01-01', '2026-11-09', 'Example row. Replace it.' );
		if ( 'template' === $format ) {
			$path = wp_tempnam( 'bomcas-rates.xlsx' );
			Bomcas_Xlsx::write(
				$path,
				array(
					'Rates'        => array( $header, $example ),
					'Instructions' => array(
						array( 'Fill the Rates sheet. Do not change the header row.' ),
						array( 'value must be a number. Rates are decimals, so 7 percent is 0.07.' ),
						array( 'Import with Dry run before you apply the rows.' ),
					),
				)
			);
			bomcas_calc_download( $path, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'bomcas-rates-template.xlsx' );
		}
		$file   = sanitize_file_name( wp_unslash( $_GET['dataset'] ?? 'sales-tax.json' ) );
		$filter = sanitize_text_field( wp_unslash( $_GET['jurisdiction'] ?? '' ) );
		$files  = Bomcas_Rates::bundled_files();
		$data   = isset( $files[ $file ] ) ? json_decode( file_get_contents( $files[ $file ] ), true ) : array();
		$rows   = array( $header );
		foreach ( Bomcas_Rates::leaves( is_array( $data ) ? $data : array() ) as $leaf ) {
			if ( '' !== $filter && false === stripos( $leaf['path'], $filter ) ) {
				continue;
			}
			$rows[] = array( $file, $leaf['path'], $leaf['value'], '', '', '', '' );
		}
		if ( 'json' === $format ) {
			header( 'Content-Disposition: attachment; filename="bomcas-rates.json"' );
			wp_send_json( $rows );
		}
		if ( 'xlsx' === $format ) {
			$path = wp_tempnam( 'bomcas-rates.xlsx' );
			Bomcas_Xlsx::write(
				$path,
				array(
					'Rates'        => $rows,
					'Instructions' => array( array( 'Exported slice. Re-import after a dry run.' ) ),
				)
			);
			bomcas_calc_download( $path, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'bomcas-rates.xlsx' );
		}
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="bomcas-rates.csv"' );
		bomcas_calc_stream_csv( $rows );
		exit;
	}

	/**
	 * Dry-run or apply an uploaded rate sheet as overrides.
	 */
	public static function import() {
		if ( ! Bomcas_Rates::user_may_manage() ) {
			wp_die( esc_html__( 'You cannot import rates.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_rates_import' );
		$upload = array();
		if ( isset( $_FILES['sheet']['tmp_name'], $_FILES['sheet']['name'] ) && is_uploaded_file( sanitize_text_field( $_FILES['sheet']['tmp_name'] ) ) ) {
			$checked = wp_check_filetype_and_ext(
				sanitize_text_field( $_FILES['sheet']['tmp_name'] ),
				sanitize_file_name( wp_unslash( $_FILES['sheet']['name'] ) ),
				array(
					'csv'  => 'text/csv',
					'txt'  => 'text/plain',
					'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				)
			);
			$upload  = array(
				'tmp_name' => sanitize_text_field( $_FILES['sheet']['tmp_name'] ),
				'name'     => sanitize_file_name( wp_unslash( $_FILES['sheet']['name'] ) ),
				'ext'      => (string) ( $checked['ext'] ? $checked['ext'] : '' ),
			);
		}
		$parsed = self::uploaded_rows( $upload );
		$ok     = array();
		$errors = array();
		foreach ( $parsed as $index => $row ) {
			if ( 0 === $index && isset( $row[0] ) && 'file' === strtolower( (string) $row[0] ) ) {
				continue;
			}
			if ( count( $row ) < 3 || ! is_numeric( $row[2] ) ) {
				$errors[] = sprintf( 'Row %d is missing a numeric value.', $index + 1 );
				continue;
			}
			$ok[] = array(
				'file'  => sanitize_file_name( $row[0] ),
				'path'  => sanitize_text_field( $row[1] ),
				'value' => self::cast( $row[2] ),
				'base'  => null,
			);
		}
		set_transient(
			'bomcas_rates_import_preview',
			array(
				'ok'     => $ok,
				'errors' => $errors,
			),
			15 * MINUTE_IN_SECONDS
		);
		if ( isset( $_POST['apply_import'] ) && ! $errors && $ok ) {
			update_option( 'bomcas_rates_overrides', $ok, false );
			if ( class_exists( 'Bomcas_Tokens' ) ) {
				Bomcas_Tokens::purge();
			}
		}
		wp_safe_redirect( admin_url( 'options-general.php?page=bomcas-rates&saved=1' ) );
		exit;
	}

	/**
	 * @return array
	 */
	private static function uploaded_rows( $file ) {
		if ( empty( $file['tmp_name'] ) ) {
			return array();
		}
		$name = sanitize_file_name( $file['name'] ?? '' );
		$path = $file['tmp_name'];
		if ( substr( $name, -5 ) === '.xlsx' ) {
			$rows = Bomcas_Xlsx::read( $path );
			return is_array( $rows ) ? $rows : array();
		}
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
		$fs  = new WP_Filesystem_Direct( false );
		$raw = $fs->get_contents( $path );
		if ( substr( $name, -5 ) === '.json' ) {
			$decoded = json_decode( (string) $raw, true );
			return is_array( $decoded ) ? $decoded : array();
		}
		$rows = array();
		foreach ( preg_split( "/\r\n|\n|\r/", (string) $raw ) as $line ) {
			if ( '' === trim( $line ) ) {
				continue;
			}
			$rows[] = str_getcsv( $line, ',', '"', '' );
		}
		return $rows;
	}
}
