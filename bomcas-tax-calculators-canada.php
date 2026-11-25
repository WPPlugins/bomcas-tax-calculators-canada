<?php
/**
 * Plugin Name: BOMCAS Tax Calculators – Canada
 * Plugin URI: https://accountantsexpert.com/products/bomcas-tax-payroll-calculators-canada/
 * Description: Canadian income tax, GST/HST/PST/QST, and mortgage calculators. Estimates stay in the browser.
 * Version: 1.1.19
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: BOMCAS
 * Author URI: https://bomcas.ca
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bomcas-tax-calculators-canada
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/bomcas-calc-migrate.php';
register_activation_hook( __FILE__, 'bomcas_calc_on_activate' );

if ( defined( 'BOMCAS_CALC_FILE' ) ) {
	if ( BOMCAS_CALC_FILE !== __FILE__ && ! function_exists( 'bomcas_calc_retire_loaded_copy' ) ) {
		/**
		 * Turn off the older folder before this copy defines the same functions.
		 */
		function bomcas_calc_retire_loaded_copy() {
			if ( ! function_exists( 'deactivate_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			deactivate_plugins( plugin_basename( BOMCAS_CALC_FILE ), true );
			bomcas_calc_on_activate();
		}
		bomcas_calc_retire_loaded_copy();
		add_action(
			'admin_notices',
			static function () {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}
				echo '<div class="notice notice-warning"><p>' . esc_html__( 'The older BOMCAS Calculators plugin was turned off so BOMCAS Tax Calculators – Canada can run. Reload this screen.', 'bomcas-tax-calculators-canada' ) . '</p></div>';
			}
		);
	}
	return;
}

define( 'BOMCAS_CALC_VERSION', '1.1.19' );
define( 'BOMCAS_CALC_FILE', __FILE__ );
define( 'BOMCAS_CALC_DIR', plugin_dir_path( __FILE__ ) );
define( 'BOMCAS_CALC_URL', plugin_dir_url( __FILE__ ) );

require_once BOMCAS_CALC_DIR . 'includes/bomcas-calc-bootstrap.php';
