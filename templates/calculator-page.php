<?php
/**
 * Layout for a page the calculator plugin created.
 * Matches the guide-page rhythm: hero, breadcrumb, then a white title block.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
while ( have_posts() ) {
	the_post();
	$bomcas_id    = get_the_ID();
	$bomcas_title = get_the_title();
	$bomcas_lede  = bomcas_calc_page_lede( $bomcas_id );
	$bomcas_intro = bomcas_calc_place_intro( $bomcas_id );
	if ( '' === $bomcas_intro ) {
		$bomcas_intro = $bomcas_lede;
	}
	$bomcas_banner = bomcas_calc_banner( $bomcas_id );
	?>
	<section class="page-hero service-hero has-hero-img bomcas-calc-hero" aria-label="<?php esc_attr_e( 'Introduction', 'bomcas-tax-calculators-canada' ); ?>">
		<img class="hero-banner-img" src="<?php echo esc_url( $bomcas_banner['url'] ); ?>"<?php echo empty( $bomcas_banner['srcset'] ) ? '' : ' srcset="' . esc_attr( $bomcas_banner['srcset'] ) . '" sizes="100vw"'; ?> width="<?php echo (int) $bomcas_banner['width']; ?>" height="<?php echo (int) $bomcas_banner['height']; ?>" alt="<?php echo esc_attr( $bomcas_banner['alt'] ); ?>" title="<?php echo esc_attr( $bomcas_banner['title'] ); ?>" decoding="async" loading="eager" fetchpriority="high" />
		<div class="wrap">
			<h1><?php echo esc_html( $bomcas_title ); ?></h1>
			<p class="lede"><?php echo esc_html( $bomcas_lede ); ?></p>
			<div class="btn-row">
				<?php
				$bomcas_body      = (string) get_post_field( 'post_content', $bomcas_id );
				$bomcas_has_form  = false !== strpos( $bomcas_body, 'bomcas-calc-form' ) || false !== strpos( $bomcas_body, '[bomcas_calculator' );
				$bomcas_calc_href = '#bomcas-hub';
				if ( $bomcas_has_form ) {
					$bomcas_calc_href = '#bomcas-calc-form';
				} elseif ( preg_match( '#href="([^"]+/calculators/[^"]+)"#', $bomcas_body, $bomcas_calc_match ) ) {
					$bomcas_calc_href = $bomcas_calc_match[1];
				}
				?>
				<a class="btn-skew" href="<?php echo esc_url( $bomcas_calc_href ); ?>"><span><?php esc_html_e( 'Calculate now', 'bomcas-tax-calculators-canada' ); ?></span></a>
				<a class="btn btn-secondary" href="<?php echo esc_url( home_url( '/products/bomcas-tax-payroll-calculators-canada/' ) ); ?>"><span><?php esc_html_e( 'Get the plugin', 'bomcas-tax-calculators-canada' ); ?></span></a>
			</div>
		</div>
		<?php if ( ! empty( $bomcas_banner['credit'] ) ) : ?>
			<p class="bomcas-photo-credit"><?php esc_html_e( 'Photo:', 'bomcas-tax-calculators-canada' ); ?>
				<a href="<?php echo esc_url( $bomcas_banner['credit_url'] ); ?>"><?php echo esc_html( wp_strip_all_tags( $bomcas_banner['credit'] ) ); ?></a><?php echo empty( $bomcas_banner['licence'] ) ? '' : ', ' . esc_html( $bomcas_banner['licence'] ); ?>
			</p>
		<?php endif; ?>
	</section>
	<div class="breadcrumbs bomcas-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'bomcas-tax-calculators-canada' ); ?>">
		<div class="wrap">
			<?php echo wp_kses_post( bomcas_calc_breadcrumb_html( $bomcas_id ) ); ?>
		</div>
	</div>
	<main id="main" class="site-main">
		<article id="bomcas-hub" <?php post_class( 'bomcas-calc-page' ); ?>>
			<?php
			the_content();
			if ( false === strpos( (string) get_post()->post_content, '[bomcas_place' ) ) {
				$bomcas_src       = 'https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html';
				$bomcas_src_label = __( 'Canada Revenue Agency rate tables', 'bomcas-tax-calculators-canada' );
				echo '<p class="bomcas-note">' . esc_html( Bomcas_Tokens::replace( __( 'Rates last updated: {updated}.', 'bomcas-tax-calculators-canada' ) ) ) . ' <a href="' . esc_url( $bomcas_src ) . '">' . esc_html( $bomcas_src_label ) . '</a></p>';
				bomcas_calc_queue_json( array( 'rates' => array( Bomcas_Tokens::plain( 'updated' ) ) ), 'application/json', 'bomcas-rate-check' );
			}
			echo wp_kses( Bomcas_Calc_Copy::render( $bomcas_id ), bomcas_calc_allowed_html() );
			?>
		</article>
	</main>
	<?php
	if ( class_exists( 'Bomcas_Lic_Market' ) ) {
		echo wp_kses( Bomcas_Lic_Market::product_band(), bomcas_calc_allowed_html() );
	}
}
get_footer();
