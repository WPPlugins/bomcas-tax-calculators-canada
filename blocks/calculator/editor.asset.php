<?php
/**
 * Dependencies for the calculator block editor script.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'dependencies' => array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render' ),
	'version'      => '1.1.18',
);
