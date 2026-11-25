<?php
/**
 * Elementor widget. Loaded only after Elementor registers widgets.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calculator widget.
 */
class Bomcas_Elementor_Calc extends \Elementor\Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'bomcas_calculator';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'BOMCAS calculator', 'bomcas-tax-calculators-canada' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-calculator';
	}

	/**
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'general' );
	}

	/**
	 * Style and calculator id.
	 */
	protected function register_controls() {
		$this->start_controls_section( 'section', array( 'label' => __( 'Calculator', 'bomcas-tax-calculators-canada' ) ) );
		$this->add_control(
			'calculator',
			array(
				'label'   => __( 'Calculator id', 'bomcas-tax-calculators-canada' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'sales-tax',
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( function_exists( 'bomcas_calc_shortcode' ) ) {
			echo wp_kses( bomcas_calc_shortcode( array( 'id' => sanitize_key( $settings['calculator'] ?? 'sales-tax' ) ) ), bomcas_calc_allowed_html() );
		}
	}
}
