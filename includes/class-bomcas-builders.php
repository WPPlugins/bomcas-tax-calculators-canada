<?php
/**
 * Gutenberg blocks, plus Elementor, Divi and Beaver hooks when those plugins are present.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Page builder registration.
 */
class Bomcas_Builders {

	/**
	 * Hooks.
	 */
	public static function boot() {
		add_action( 'init', array( __CLASS__, 'blocks' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'elementor' ) );
		add_action( 'et_builder_ready', array( __CLASS__, 'divi' ) );
		add_action( 'init', array( __CLASS__, 'beaver' ), 20 );
	}

	/**
	 * One dynamic block that renders any bundled calculator.
	 */
	public static function blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		register_block_type(
			BOMCAS_CALC_DIR . 'blocks/calculator-any',
			array(
				'render_callback' => static function ( $attrs ) {
					if ( ! function_exists( 'bomcas_calc_shortcode' ) ) {
						return '';
					}
					return bomcas_calc_shortcode(
						array(
							'id'       => isset( $attrs['calculator'] ) ? sanitize_key( (string) $attrs['calculator'] ) : 'sales-tax',
							'province' => isset( $attrs['province'] ) ? sanitize_text_field( (string) $attrs['province'] ) : '',
						)
					);
				},
			)
		);
	}

	/**
	 * @param object $widgets Elementor manager.
	 */
	public static function elementor( $widgets ) {
		if ( ! class_exists( '\Elementor\Widget_Base' ) || ! is_object( $widgets ) || ! method_exists( $widgets, 'register' ) ) {
			return;
		}
		require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-elementor-widget.php';
		if ( class_exists( 'Bomcas_Elementor_Calc' ) ) {
			$widgets->register( new Bomcas_Elementor_Calc() );
		}
	}

	/**
	 * Divi module hook. The module class is registered only after Divi is ready.
	 */
	public static function divi() {
		if ( ! class_exists( 'ET_Builder_Module' ) ) {
			return;
		}
		update_option( 'bomcas_divi_ready', 1, false );
	}

	/**
	 * Beaver Builder module.
	 */
	public static function beaver() {
		if ( ! class_exists( 'FLBuilder' ) ) {
			return;
		}
		FLBuilder::register_module(
			'Bomcas_Beaver_Calc',
			array(
				'general' => array(
					'title'  => __( 'Calculator', 'bomcas-tax-calculators-canada' ),
					'fields' => array(
						'calculator' => array(
							'type'    => 'text',
							'label'   => __( 'Calculator id', 'bomcas-tax-calculators-canada' ),
							'default' => 'sales-tax',
						),
					),
				),
			)
		);
	}
}
