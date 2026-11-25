<?php
/**
 * Remove Lite options and the optional leads table. Does not touch Pro or Licensing.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * @return void
 */
function bomcas_calc_delete_site_data() {
	global $wpdb;
	$prefixes = array( 'bomcas_calc_', 'bomcas_rates_', 'bomcas_ga4', 'bomcas_support_' );
	foreach ( $prefixes as $prefix ) {
		$like = $wpdb->esc_like( $prefix ) . '%';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Uninstall removes the plugin's own options.
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $like ) );
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Uninstall removes the plugin's own table.
	$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $wpdb->prefix . 'bomcas_leads' ) );
}

if ( is_multisite() ) {
	$bomcas_calc_sites = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);
	foreach ( $bomcas_calc_sites as $bomcas_calc_site_id ) {
		switch_to_blog( (int) $bomcas_calc_site_id );
		bomcas_calc_delete_site_data();
		restore_current_blog();
	}
} else {
	bomcas_calc_delete_site_data();
}
