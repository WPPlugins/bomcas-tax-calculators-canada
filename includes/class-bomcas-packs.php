<?php
/**
 * Vertical landing packs. They publish pages; they do not turn tools off.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Realtor, dealer, payroll, advisor, shop and publisher packs.
 */
class Bomcas_Packs {

	/**
	 * Hooks.
	 */
	public static function boot() {
		add_action( 'admin_post_bomcas_pack_publish', array( __CLASS__, 'publish' ) );
		add_shortcode( 'bomcas_pack', array( __CLASS__, 'shortcode' ) );
	}

	/**
	 * Packs whose tools run on this site. Without the Pro add-on only the
	 * Lite calculators (income tax, sales tax, mortgage payment) are listed.
	 *
	 * @return array
	 */
	public static function all() {
		$packs = self::definitions();
		if ( defined( 'BOMCAS_PRO_VERSION' ) ) {
			return $packs;
		}
		$lite = array( 'income-tax', 'sales-tax', 'mortgage-payment' );
		foreach ( $packs as $id => $pack ) {
			$packs[ $id ]['tools'] = array_values( array_intersect( $pack['tools'], $lite ) );
			if ( empty( $packs[ $id ]['tools'] ) ) {
				unset( $packs[ $id ] );
			}
		}
		return $packs;
	}

	/**
	 * @return array
	 */
	private static function definitions() {
		return array(
			'realtor'     => array(
				'title' => __( 'Mortgage and property tax calculators', 'bomcas-tax-calculators-canada' ),
				'tools' => array( 'mortgage-payment', 'land-transfer-tax', 'salary-after-tax' ),
				'cta'   => __( 'Book a pre-approval conversation', 'bomcas-tax-calculators-canada' ),
				'body'  => __( 'Estimate a payment, a land transfer tax and take-home pay before the showing. The figures stay an estimate.', 'bomcas-tax-calculators-canada' ),
			),
			'dealer'      => array(
				'title' => __( 'Vehicle tax calculators for dealers', 'bomcas-tax-calculators-canada' ),
				'tools' => array( 'vehicle-purchase', 'sales-tax' ),
				'cta'   => __( 'Ask for a quote', 'bomcas-tax-calculators-canada' ),
				'body'  => __( 'Show the sales tax on a vehicle and let the shopper email the estimate to the desk.', 'bomcas-tax-calculators-canada' ),
			),
			'payroll'     => array(
				'title' => __( 'Paycheck calculators for HR', 'bomcas-tax-calculators-canada' ),
				'tools' => array( 'payroll-deductions', 'salary-after-tax', 'ei-benefits' ),
				'cta'   => __( 'Talk to payroll', 'bomcas-tax-calculators-canada' ),
				'body'  => __( 'A Canadian pay estimate. Quebec uses RRQ, RQAP and TVQ where those taxes apply.', 'bomcas-tax-calculators-canada' ),
			),
			'advisor'     => array(
				'title' => __( 'Savings calculators for advisors', 'bomcas-tax-calculators-canada' ),
				'tools' => array( 'rrsp-room', 'tfsa-room', 'fhsa', 'capital-gains' ),
				'cta'   => __( 'Book a review', 'bomcas-tax-calculators-canada' ),
				'body'  => __( 'RRSP, TFSA, FHSA and capital gains estimates for a planning meeting. They are not a financial plan.', 'bomcas-tax-calculators-canada' ),
			),
			'ecommerce'   => array(
				'title' => __( 'Sales tax estimate for shops', 'bomcas-tax-calculators-canada' ),
				'tools' => array( 'sales-tax' ),
				'cta'   => __( 'Contact the shop', 'bomcas-tax-calculators-canada' ),
				'body'  => __( 'An educational sales-tax estimate. It does not file a return and it is not a checkout tax service.', 'bomcas-tax-calculators-canada' ),
			),
			'lawyer'      => array(
				'title' => __( 'Tax calculators for law firms', 'bomcas-tax-calculators-canada' ),
				'tools' => array( 'land-transfer-tax', 'capital-gains' ),
				'cta'   => __( 'Book a consult', 'bomcas-tax-calculators-canada' ),
				'body'  => __( 'Land transfer and capital gains estimates for a client meeting. They are not a legal opinion.', 'bomcas-tax-calculators-canada' ),
			),
			'immigration' => array(
				'title' => __( 'Take-home calculators for consultants', 'bomcas-tax-calculators-canada' ),
				'tools' => array( 'salary-after-tax' ),
				'cta'   => __( 'Book a consultation', 'bomcas-tax-calculators-canada' ),
				'body'  => __( 'A take-home estimate by province or territory. This is not an immigration assessment and it does not score an application.', 'bomcas-tax-calculators-canada' ),
			),
			'blogger'     => array(
				'title' => __( 'Tax calculators for articles', 'bomcas-tax-calculators-canada' ),
				'tools' => array( 'income-tax', 'mortgage-payment' ),
				'cta'   => __( 'Read the guide', 'bomcas-tax-calculators-canada' ),
				'body'  => __( 'Put a calculator in an article. Readers can email themselves the estimate if you turn the lead form on.', 'bomcas-tax-calculators-canada' ),
			),
		);
	}

	/**
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function shortcode( $atts ) {
		$atts = shortcode_atts( array( 'id' => 'realtor' ), $atts, 'bomcas_pack' );
		$id   = sanitize_key( $atts['id'] );
		$all  = self::all();
		if ( ! isset( $all[ $id ] ) ) {
			return '';
		}
		$pack = $all[ $id ];
		$html = '<h2>' . esc_html( $pack['title'] ) . '</h2><p>' . esc_html( $pack['body'] ) . '</p><ul>';
		foreach ( $pack['tools'] as $tool ) {
			$html .= '<li>' . esc_html( $tool ) . '</li>';
		}
		$html .= '</ul><p><a class="btn-skew" href="' . esc_url( home_url( '/calculators/' ) ) . '"><span>' . esc_html( $pack['cta'] ) . '</span></a></p>';
		return $html;
	}

	/**
	 * Menu.
	 */
	public static function menu() {
		add_submenu_page( 'bomcas-calculators-home', __( 'Packs', 'bomcas-tax-calculators-canada' ), __( 'Packs', 'bomcas-tax-calculators-canada' ), 'manage_options', 'bomcas-packs', array( __CLASS__, 'screen' ) );
	}

	/**
	 * Screen.
	 */
	public static function screen() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
		Bomcas_Admin_UI::open(
			array(
				'doc'     => 'packs',
				'product' => 'BOMCAS Canada',
				'kicker'  => __( 'Calculators · Packs', 'bomcas-tax-calculators-canada' ),
				'badge'   => __( 'Landing pages', 'bomcas-tax-calculators-canada' ),
				'help'    => __( 'Publishing a pack adds a landing page. It does not disable calculators you already use.', 'bomcas-tax-calculators-canada' ),
				'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'packs' ) : array(),
			)
		);
		echo '<section class="bomcas-admin__card"><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'bomcas_pack_publish' );
		echo '<input type="hidden" name="action" value="bomcas_pack_publish" />';
		echo '<table class="widefat"><thead><tr><th></th><th>' . esc_html__( 'Pack', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Tools', 'bomcas-tax-calculators-canada' ) . '</th></tr></thead><tbody>';
		foreach ( self::all() as $id => $pack ) {
			echo '<tr><td><input type="checkbox" name="packs[]" value="' . esc_attr( $id ) . '" /></td><td>' . esc_html( $pack['title'] ) . ' ' . wp_kses( Bomcas_Admin_UI::tip( $pack['body'] ), Bomcas_Admin_UI::tip_allowed() ) . '</td><td>' . esc_html( implode( ', ', $pack['tools'] ) ) . '</td></tr>';
		}
		echo '</tbody></table>';
		submit_button( __( 'Publish selected packs', 'bomcas-tax-calculators-canada' ) );
		echo '</form></section>';
		Bomcas_Admin_UI::close();
	}

	/**
	 * Create or update landing pages.
	 */
	public static function publish() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot publish packs.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_pack_publish' );
		$wanted = isset( $_POST['packs'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['packs'] ) ) : array();
		$hub    = get_page_by_path( 'calculators' );
		$parent = $hub instanceof WP_Post ? $hub->ID : 0;
		foreach ( self::all() as $id => $pack ) {
			if ( ! in_array( $id, $wanted, true ) ) {
				continue;
			}
			$path  = 'calculators/pack-' . $id;
			$found = get_page_by_path( $path );
			$body  = '[bomcas_pack id="' . $id . '"]';
			if ( $found instanceof WP_Post ) {
				wp_update_post(
					array(
						'ID'           => $found->ID,
						'post_title'   => $pack['title'],
						'post_content' => $body,
					)
				);
			} else {
				wp_insert_post(
					array(
						'post_title'   => $pack['title'],
						'post_name'    => 'pack-' . $id,
						'post_status'  => 'publish',
						'post_type'    => 'page',
						'post_parent'  => $parent,
						'post_content' => $body,
					)
				);
			}
		}
		wp_safe_redirect( admin_url( 'admin.php?page=bomcas-packs&saved=1' ) );
		exit;
	}
}
