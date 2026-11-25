<?php
/**
 * Directory-slug migration. Loaded from the main file so it runs even when
 * an older copy of the plugin is already loaded in the same request.
 *
 * Function names are unique to this version so they do not collide with
 * bomcas_calc_migrate_install() in Lite 1.1.18 and earlier.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The directory slug changed. Settings, shortcodes, and option names stay on the old prefixes.
 */
function bomcas_calc_on_activate() {
	if ( ! function_exists( 'deactivate_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$old_plugins = array(
		'bomcas-calculators/bomcas-calculators.php',
		'bomcas-tax-payroll-calculators-canada/bomcas-tax-payroll-calculators-canada.php',
	);
	foreach ( $old_plugins as $old ) {
		if ( function_exists( 'is_plugin_active' ) && is_plugin_active( $old ) ) {
			deactivate_plugins( $old );
		}
	}
	bomcas_calc_detach_legacy_uninstall();
	update_option( 'bomcas_calc_directory_slug', 'bomcas-tax-calculators-canada', false );
}

/**
 * Stop WordPress from loading an old plugin file when someone deletes it.
 *
 * The old file declares the same functions. Including it while this copy is loaded is a fatal error.
 */
function bomcas_calc_detach_legacy_uninstall() {
	$old_plugins = array(
		'bomcas-calculators/bomcas-calculators.php',
		'bomcas-tax-payroll-calculators-canada/bomcas-tax-payroll-calculators-canada.php',
	);
	$list = get_option( 'uninstall_plugins', array() );
	if ( ! is_array( $list ) ) {
		return;
	}
	$changed = false;
	foreach ( $old_plugins as $old ) {
		if ( array_key_exists( $old, $list ) ) {
			unset( $list[ $old ] );
			$changed = true;
		}
	}
	if ( $changed ) {
		update_option( 'uninstall_plugins', $list );
	}
}
