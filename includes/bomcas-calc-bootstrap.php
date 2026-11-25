<?php
/**
 * Loaded after the old-plugin guard. Functions here are not compiled if that guard returns.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once BOMCAS_CALC_DIR . 'includes/bomcas-calc-desc.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-tokens.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-docs.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-calc-engine.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-calc-view.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-rates.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-rates-admin.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-calc-copy.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-ca-pages.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-guide.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-xlsx.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-admin-ui.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-calc-privacy.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-calc-account.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-pdf.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-leads.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-stats.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-i18n.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-packs.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-trust.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-builders.php';
require_once BOMCAS_CALC_DIR . 'includes/class-bomcas-listing.php';

/**
 * Load a seed file.
 *
 * @param string $file File name.
 * @return array
 */
function bomcas_calc_seed( $file ) {
	$path = BOMCAS_CALC_DIR . 'seed/' . basename( $file );
	if ( ! is_readable( $path ) ) {
		return array();
	}
	$data = json_decode( file_get_contents( $path ), true );
	if ( ! is_array( $data ) ) {
		return array();
	}
	if ( class_exists( 'Bomcas_Rates' ) ) {
		$data = Bomcas_Rates::apply_dataset( basename( $file ), $data );
	}
	return apply_filters( 'bomcas_calc_seed', $data, basename( $file ) );
}

/**
 * Seed file for a dataset stem and the year in force.
 *
 * Looks for stem-YYYY.json. Falls back to the newest file whose year is not
 * after the year in force. No year is written into the page.
 *
 * @param string $stem File stem, such as us-state-income.
 * @return array
 */
function bomcas_rates_named( $stem ) {
	$stem  = preg_replace( '/[^a-z0-9-]/', '', (string) $stem );
	$year  = class_exists( 'Bomcas_Tokens' ) ? Bomcas_Tokens::year() : 0;
	$dir   = BOMCAS_CALC_DIR . 'seed/';
	$exact = $dir . $stem . '-' . $year . '.json';
	if ( $year && is_readable( $exact ) ) {
		return bomcas_calc_seed( $stem . '-' . $year . '.json' );
	}
	$best   = '';
	$best_y = 0;
	foreach ( glob( $dir . $stem . '-*.json' ) as $path ) {
		if ( preg_match( '/-(\d{4})\.json$/', $path, $m ) && ( ! $year || (int) $m[1] <= $year ) && (int) $m[1] >= $best_y ) {
			$best_y = (int) $m[1];
			$best   = basename( $path );
		}
	}
	return '' !== $best ? bomcas_calc_seed( $best ) : array();
}

/**
 * Current personal-tax year shipped with Lite.
 *
 * @return array
 */
function bomcas_calc_current_year() {
	if ( class_exists( 'Bomcas_Tokens' ) ) {
		return Bomcas_Tokens::row();
	}
	$seed = bomcas_calc_seed( 'personal-tax-current.json' );
	$year = isset( $seed['current_tax_year'] ) ? (string) $seed['current_tax_year'] : '';
	return ( '' !== $year && isset( $seed['years'][ $year ] ) ) ? $seed['years'][ $year ] : array();
}

/**
 * Whether the optional public credit is enabled. Default off.
 *
 * @return bool
 */
function bomcas_calc_tool_enabled( $id ) {
	$disabled = get_option( 'bomcas_calc_disabled_tools', array() );
	if ( is_array( $disabled ) && in_array( $id, $disabled, true ) ) {
		return false;
	}
	$country = 'CA';
	$countries = get_option(
		'bomcas_calc_countries',
		array(
			'CA' => 1,
		)
	);
	if ( is_array( $countries ) && isset( $countries[ $country ] ) && ! $countries[ $country ] ) {
		return false;
	}
	return true;
}

function bomcas_calc_show_credit() {
	return (bool) get_option( 'bomcas_calc_show_credit', false );
}

/**
 * Register hooks.
 */
function bomcas_calc_boot() {
	bomcas_calc_detach_legacy_uninstall();
	add_action( 'init', 'bomcas_calc_register_block' );
	add_action( 'wp_enqueue_scripts', 'bomcas_calc_assets' );
	add_filter( 'body_class', 'bomcas_calc_body_class' );
	add_shortcode( 'bomcas_calculator', 'bomcas_calc_shortcode' );
	add_action( 'admin_menu', 'bomcas_calc_admin_menu' );
	add_action( 'admin_menu', 'bomcas_calc_register_submenus', 21 );
	Bomcas_Rates_Admin::boot();
	Bomcas_Tokens::boot();
	Bomcas_Docs::boot();
	Bomcas_Rates_Admin::schedule();
	Bomcas_Calc_Copy::boot();
	Bomcas_Calc_Privacy::boot();
	Bomcas_Calc_Account::boot();
	Bomcas_Leads::boot();
	Bomcas_Stats::boot();
	Bomcas_Booking::boot();
	Bomcas_I18n::boot();
	Bomcas_Packs::boot();
	Bomcas_Trust::boot();
	Bomcas_Builders::boot();
	if ( ! wp_next_scheduled( 'bomcas_daily' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'bomcas_daily' );
	}
	add_action( 'admin_enqueue_scripts', 'bomcas_calc_admin_assets' );
	add_action( 'admin_post_bomcas_calc_save_credit', 'bomcas_calc_save_credit' );
	add_action( 'wp', 'bomcas_calc_single_canonical' );
	add_action( 'template_redirect', 'bomcas_calc_buffer_schema', 3 );
	add_action( 'wp_footer', 'bomcas_calc_print_queued_json', 99 );
	add_filter( 'template_include', 'bomcas_calc_template' );
	add_filter( 'the_content', 'bomcas_calc_strip_repeated_intro' );
	add_filter( 'document_title_parts', 'bomcas_calc_document_title' );
	add_action( 'wp_head', 'bomcas_calc_schema', 20 );
}

/**
 * One layout for plugin-created calculator pages. Embedded shortcodes keep the host page.
 *
 * @param string $template Template path.
 * @return string
 */
/**
 * One breadcrumb list, matching Home, Calculators, the tool and the province.
 *
 * @param string $html Page HTML.
 * @return string
 */
function bomcas_calc_fix_breadcrumb_schema( $html ) {
	if ( ! is_string( $html ) || ! is_page() || ! get_post_meta( get_queried_object_id(), '_bomcas_calc_managed', true ) ) {
		return $html;
	}
	$id    = get_queried_object_id();
	$items = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => 'Home',
			'item'     => home_url( '/' ),
		),
	);
	$pos   = 2;
	foreach ( array_reverse( get_post_ancestors( $id ) ) as $aid ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos,
			'name'     => wp_strip_all_tags( get_the_title( $aid ) ),
			'item'     => get_permalink( $aid ),
		);
		++$pos;
	}
	$place   = (string) get_post_meta( $id, '_bomcas_calc_place_name', true );
	$items[] = array(
		'@type'    => 'ListItem',
		'position' => $pos,
		'name'     => '' !== $place ? $place : wp_strip_all_tags( get_the_title( $id ) ),
		'item'     => get_permalink( $id ),
	);
	$node    = array(
		'@type'           => 'BreadcrumbList',
		'@id'             => get_permalink( $id ) . '#breadcrumb',
		'itemListElement' => $items,
	);
	$encoded = (string) wp_json_encode( $node );
	if ( ! class_exists( 'WP_HTML_Tag_Processor' ) || ! method_exists( 'WP_HTML_Tag_Processor', 'set_modifiable_text' ) ) {
		return $html;
	}
	$tags = new WP_HTML_Tag_Processor( $html );
	while ( $tags->next_tag( 'SCRIPT' ) ) {
		if ( 'application/ld+json' !== strtolower( (string) $tags->get_attribute( 'type' ) ) ) {
			continue;
		}
		$json = (string) $tags->get_modifiable_text();
		if ( false === strpos( $json, '"BreadcrumbList"' ) ) {
			continue;
		}
		$updated = preg_replace( '/\{"@type":"BreadcrumbList".*?"itemListElement":\[.*?\]\}/', $encoded, $json, 1 );
		if ( is_string( $updated ) && $updated !== $json && $tags->set_modifiable_text( $updated ) ) {
			return $tags->get_updated_html();
		}
	}
	return $html;
}

/**
 * Replace the theme breadcrumb list on calculator pages.
 *
 * The plugin does not open its own output buffer. On WordPress 6.9 and later it
 * uses the core template enhancement output buffer, which core opens and closes.
 * On older versions the theme breadcrumb list is left as it is.
 */
function bomcas_calc_buffer_schema() {
	if ( ! is_page() || ! get_post_meta( get_queried_object_id(), '_bomcas_calc_managed', true ) ) {
		return;
	}
	if ( function_exists( 'wp_start_template_enhancement_output_buffer' ) ) {
		add_filter( 'wp_template_enhancement_output_buffer', 'bomcas_calc_fix_breadcrumb_schema' );
	}
}

/**
 * Queue a JSON or JSON-LD data block for the footer.
 *
 * @param mixed  $data Data to encode.
 * @param string $type Script type, application/json or application/ld+json.
 * @param string $id   Optional element id.
 */
function bomcas_calc_queue_json( $data, $type = 'application/json', $id = '' ) {
	$GLOBALS['bomcas_calc_json_queue']   = isset( $GLOBALS['bomcas_calc_json_queue'] ) ? (array) $GLOBALS['bomcas_calc_json_queue'] : array();
	$GLOBALS['bomcas_calc_json_queue'][] = array(
		'data' => $data,
		'type' => 'application/ld+json' === $type ? 'application/ld+json' : 'application/json',
		'id'   => sanitize_html_class( $id ),
	);
}

/**
 * Print queued data blocks with the core inline script API.
 */
function bomcas_calc_print_queued_json() {
	if ( empty( $GLOBALS['bomcas_calc_json_queue'] ) ) {
		return;
	}
	$seen = array();
	foreach ( (array) $GLOBALS['bomcas_calc_json_queue'] as $item ) {
		$attrs = array( 'type' => $item['type'] );
		if ( '' !== $item['id'] ) {
			if ( isset( $seen[ $item['id'] ] ) ) {
				continue;
			}
			$seen[ $item['id'] ] = true;
			$attrs['id']         = $item['id'];
		}
		wp_print_inline_script_tag( (string) wp_json_encode( $item['data'] ), $attrs );
	}
	$GLOBALS['bomcas_calc_json_queue'] = array();
}

/**
 * Tags and attributes allowed in calculator, guide and admin markup built by this plugin.
 *
 * @return array
 */
function bomcas_calc_allowed_html() {
	static $allowed = null;
	if ( null !== $allowed ) {
		return $allowed;
	}
	$allowed = wp_kses_allowed_html( 'post' );
	$global  = array(
		'class'            => true,
		'id'               => true,
		'style'            => true,
		'title'            => true,
		'role'             => true,
		'hidden'           => true,
		'tabindex'         => true,
		'lang'             => true,
		'aria-label'       => true,
		'aria-hidden'      => true,
		'aria-live'        => true,
		'aria-describedby' => true,
		'aria-labelledby'  => true,
		'aria-expanded'    => true,
		'aria-controls'    => true,
		'aria-current'     => true,
		'data-*'           => true,
	);
	$extra   = array(
		'form'     => array(
			'action'     => true,
			'method'     => true,
			'novalidate' => true,
			'enctype'    => true,
		),
		'input'    => array(
			'type'         => true,
			'name'         => true,
			'value'        => true,
			'placeholder'  => true,
			'checked'      => true,
			'disabled'     => true,
			'readonly'     => true,
			'required'     => true,
			'min'          => true,
			'max'          => true,
			'step'         => true,
			'inputmode'    => true,
			'autocomplete' => true,
			'size'         => true,
			'maxlength'    => true,
			'pattern'      => true,
		),
		'select'   => array(
			'name'     => true,
			'multiple' => true,
			'disabled' => true,
			'required' => true,
		),
		'option'   => array(
			'value'    => true,
			'selected' => true,
			'disabled' => true,
		),
		'optgroup' => array( 'label' => true ),
		'textarea' => array(
			'name'        => true,
			'rows'        => true,
			'cols'        => true,
			'placeholder' => true,
			'required'    => true,
			'readonly'    => true,
		),
		'button'   => array(
			'type'     => true,
			'name'     => true,
			'value'    => true,
			'disabled' => true,
		),
		'label'    => array( 'for' => true ),
		'fieldset' => array(),
		'legend'   => array(),
		'output'   => array( 'for' => true ),
		'nav'      => array(),
		'picture'  => array(),
		'source'   => array(
			'srcset' => true,
			'sizes'  => true,
			'type'   => true,
			'media'  => true,
		),
		'img'      => array(
			'src'           => true,
			'srcset'        => true,
			'sizes'         => true,
			'alt'           => true,
			'width'         => true,
			'height'        => true,
			'loading'       => true,
			'decoding'      => true,
			'fetchpriority' => true,
		),
		'iframe'   => array(
			'src'     => true,
			'width'   => true,
			'height'  => true,
			'loading' => true,
		),
		'svg'      => array(
			'viewbox'     => true,
			'width'       => true,
			'height'      => true,
			'xmlns'       => true,
			'fill'        => true,
			'focusable'   => true,
			'aria-hidden' => true,
		),
		'g'        => array(
			'fill'      => true,
			'transform' => true,
		),
		'path'     => array(
			'd'            => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'rect'     => array(
			'x'      => true,
			'y'      => true,
			'width'  => true,
			'height' => true,
			'rx'     => true,
			'fill'   => true,
			'stroke' => true,
		),
		'circle'   => array(
			'cx'     => true,
			'cy'     => true,
			'r'      => true,
			'fill'   => true,
			'stroke' => true,
		),
		'line'     => array(
			'x1'           => true,
			'y1'           => true,
			'x2'           => true,
			'y2'           => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'polyline' => array(
			'points' => true,
			'fill'   => true,
			'stroke' => true,
		),
		'text'     => array(
			'x'           => true,
			'y'           => true,
			'text-anchor' => true,
			'fill'        => true,
			'font-size'   => true,
			'font-family' => true,
			'font-weight' => true,
		),
		'title'    => array(),
	);
	foreach ( $extra as $tag => $attrs ) {
		$allowed[ $tag ] = array_merge( isset( $allowed[ $tag ] ) ? (array) $allowed[ $tag ] : array(), $attrs );
	}
	foreach ( $allowed as $tag => $attrs ) {
		$allowed[ $tag ] = array_merge( (array) $attrs, $global );
	}
	unset( $allowed['script'], $allowed['style'] );
	return $allowed;
}

/**
 * True on this plugin's own admin screens. Notices stay on these screens.
 *
 * @return bool
 */
function bomcas_calc_is_plugin_screen() {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return false;
	}
	$screen = get_current_screen();
	if ( ! $screen ) {
		return false;
	}
	return false !== strpos( (string) $screen->id, 'bomcas' ) || false !== strpos( (string) $screen->base, 'bomcas' );
}

/**
 * Write rows to the response as CSV, for an administrator's export download.
 *
 * @param array $rows Rows of cells.
 */
function bomcas_calc_stream_csv( $rows ) {
	$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Streams the export download.
	if ( ! $out ) {
		return;
	}
	foreach ( (array) $rows as $row ) {
		$cells = array();
		foreach ( (array) $row as $cell ) {
			$cell = (string) $cell;
			// Keep spreadsheet apps from reading a cell as a formula.
			if ( '' !== $cell && in_array( $cell[0], array( '=', '+', '-', '@' ), true ) && ! is_numeric( $cell ) ) {
				$cell = "'" . $cell;
			}
			$cells[] = $cell;
		}
		fputcsv( $out, $cells, ',', '"', '\\' );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closes the export stream.
}

/**
 * Escape markup built by this plugin, late, right before output.
 *
 * @param string $html Markup.
 * @return string
 */
function bomcas_calc_kses( $html ) {
	return wp_kses( (string) $html, bomcas_calc_allowed_html() );
}

function bomcas_calc_single_canonical() {
	if ( is_page() && get_post_meta( get_queried_object_id(), '_bomcas_calc_managed', true ) ) {
		remove_action( 'wp_head', 'rel_canonical' );
	}
}

/**
 * GPL banner for a calculator hero. Matches the theme's hero image markup.
 *
 * @param int $id Page id.
 * @return array{url:string,alt:string}
 */
/**
 * Send a temporary file and delete it.
 *
 * @param string $path     Absolute path.
 * @param string $type     Content type.
 * @param string $filename Download name.
 */
function bomcas_calc_download( $path, $type, $filename ) {
	nocache_headers();
	header( 'Content-Type: ' . sanitize_mime_type( $type ) );
	header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $filename ) . '"' );
	header( 'X-Content-Type-Options: nosniff' );
	if ( is_readable( $path ) ) {
		// A binary file this plugin just wrote to the temp folder. It is streamed as a download, not printed into HTML.
		readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
	}
	wp_delete_file( $path );
	exit;
}

function bomcas_calc_credit_row( $tool ) {
	static $rows = null;
	if ( null === $rows ) {
		$rows = array();
		$path = BOMCAS_CALC_DIR . 'IMAGE-CREDITS.csv';
		if ( is_readable( $path ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
			$fs    = new WP_Filesystem_Direct( false );
			$raw   = $fs->get_contents( $path );
			$lines = preg_split( "/\r\n|\n|\r/", (string) $raw );
			$head  = str_getcsv( (string) array_shift( $lines ), ',', '"', '' );
			if ( is_array( $head ) ) {
				foreach ( $lines as $line ) {
					if ( '' === trim( $line ) ) {
						continue;
					}
					$cells = str_getcsv( $line, ',', '"', '' );
					$row   = array();
					foreach ( $head as $i => $key ) {
						$row[ $key ] = $cells[ $i ] ?? '';
					}
					if ( ! empty( $row['tool'] ) ) {
						$rows[ $row['tool'] ] = $row;
					}
				}
			}
		}
	}
	return $rows[ $tool ] ?? null;
}

function bomcas_calc_banner( $id ) {
	$tool = (string) get_post_meta( $id, '_bomcas_calc_tool', true );
	if ( '' === $tool ) {
		$tool = (string) get_post_field( 'post_name', $id );
	}
	$credit = bomcas_calc_credit_row( $tool );
	if ( is_array( $credit ) && ! empty( $credit['file'] ) ) {
		$base = BOMCAS_CALC_URL . 'assets/img/heroes/';
		$stem = preg_replace( '/\.webp$/', '', $credit['file'] );
		return apply_filters(
			'bomcas_calc_banner',
			array(
				'url'        => $base . $credit['file'],
				'srcset'     => $base . $stem . '-640.webp 640w, ' . $base . $credit['file'] . ' ' . (int) $credit['width'] . 'w',
				'width'      => (int) $credit['width'],
				'height'     => (int) $credit['height'],
				'alt'        => $credit['alt'],
				'title'      => $credit['alt'],
				'credit'     => $credit['credit'],
				'credit_url' => $credit['source_url'],
				'licence'    => $credit['licence'],
			),
			$id
		);
	}
	$file = 'banner-tax.png';
	if ( in_array( $tool, array( 'sales-tax', 'vehicle-purchase', 'lease-vs-buy' ), true ) ) {
		$file = 'banner-sales.png';
	} elseif ( in_array( $tool, array( 'payroll-deductions', 'employer-health-tax', 'self-employed-tax', 'quebec-withholding' ), true ) ) {
		$file = 'banner-payroll.png';
	} elseif ( in_array( $tool, array( 'land-transfer-tax', 'mortgage-affordability', 'cmhc-insurance', 'mortgage-payment' ), true ) ) {
		$file = 'banner-mortgage.png';
	}
	$alts   = array(
		'banner-sales.png'    => __( 'Geometric banner in green and amber for the sales-tax calculators. Original artwork, not a photograph.', 'bomcas-tax-calculators-canada' ),
		'banner-payroll.png'  => __( 'Geometric banner in purple and teal for the payroll calculators. Original artwork, not a photograph.', 'bomcas-tax-calculators-canada' ),
		'banner-mortgage.png' => __( 'Geometric banner in brown and sand for the property calculators. Original artwork, not a photograph.', 'bomcas-tax-calculators-canada' ),
		'banner-tax.png'      => __( 'Geometric banner in navy and orange for the income-tax calculators. Original artwork, not a photograph.', 'bomcas-tax-calculators-canada' ),
	);
	$banner = array(
		'url'        => BOMCAS_CALC_URL . 'assets/img/' . $file,
		'srcset'     => '',
		'width'      => 1200,
		'height'     => 675,
		'alt'        => $alts[ $file ],
		'title'      => $alts[ $file ],
		'credit'     => '',
		'credit_url' => '',
	);
	return apply_filters( 'bomcas_calc_banner', $banner, $id );
}

/**
 * Visible trail: Calculators, then the tool, then the province when this is a province page.
 *
 * @param int $id Page id.
 * @return string
 */
function bomcas_calc_breadcrumb_html( $id ) {
	$ancestors = array_reverse( get_post_ancestors( $id ) );
	$parts     = array( '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'bomcas-tax-calculators-canada' ) . '</a>' );
	foreach ( $ancestors as $aid ) {
		$parts[] = '<a href="' . esc_url( get_permalink( $aid ) ) . '">' . esc_html( get_the_title( $aid ) ) . '</a>';
	}
	$place   = (string) get_post_meta( $id, '_bomcas_calc_place_name', true );
	$parts[] = '<span>' . esc_html( '' !== $place ? $place : get_the_title( $id ) ) . '</span>';
	return implode( ' <span aria-hidden="true">›</span> ', $parts );
}

function bomcas_calc_strip_repeated_intro( $content ) {
	if ( ! is_page() ) {
		return $content;
	}
	$tool = (string) get_post_meta( get_the_ID(), '_bomcas_calc_tool', true );
	if ( 0 !== strpos( $tool, 'us-' ) ) {
		return $content;
	}
	return preg_replace( '/^\s*<p\b[^>]*>.*?<\/p>\s*/is', '', $content, 1 );
}

function bomcas_calc_template( $template ) {
	if ( ! is_page() ) {
		return $template;
	}
	$id = get_queried_object_id();
	if ( ! $id || ! get_post_meta( $id, '_bomcas_calc_managed', true ) ) {
		return $template;
	}
	$owned = BOMCAS_CALC_DIR . 'templates/calculator-page.php';
	return file_exists( $owned ) ? $owned : $template;
}

/**
 * Unique document title for each calculator URL, including a province page.
 *
 * @param array $parts Title parts.
 * @return array
 */
function bomcas_calc_seo_controls_title() {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'THE_SEO_FRAMEWORK_VERSION' )
		|| defined( 'SLIM_SEO_VER' )
		|| (bool) has_filter( 'pre_get_document_title' );
}

/**
 * A 50–60 character title the theme's length fitter will leave unchanged.
 *
 * @param string $name Page name.
 * @return string
 */
function bomcas_calc_public_title( $name ) {
	$name = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $name ) ) );
	if ( '' === $name ) {
		$name = 'Calculators';
	}
	if ( preg_match( '/\|\s*BOMCAS Tax (?:& Payroll )?Calculators – Canada$/u', $name ) ) {
		return $name;
	}
	return $name . ' | BOMCAS Tax Calculators – Canada';
}

/**
 * A 140–160 character description so a theme fitter does not append a phone number.
 *
 * @param string $lead Lead sentence.
 * @return string
 */
function bomcas_calc_public_desc( $lead ) {
	$lead = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $lead ) ) );
	$len  = function_exists( 'mb_strlen' ) ? mb_strlen( $lead ) : strlen( $lead );
	if ( $len >= 150 && $len <= 160 ) {
		return $lead;
	}
	return $lead;
}

/**
 * Hand-written description for one calculator page, 150–160 characters.
 *
 * @param int $id Page id.
 * @return string
 */
function bomcas_calc_meta_desc( $id ) {
	$tool = (string) get_post_meta( $id, '_bomcas_calc_tool', true );
	if ( '' === $tool ) {
		$tool = (string) get_post_field( 'post_name', $id );
	}
	$place = (string) get_post_meta( $id, '_bomcas_calc_place_name', true );
	return bomcas_calc_compose_desc( $tool, $place );
}

function bomcas_calc_document_title( $parts ) {
	if ( ! is_page() || ! get_post_meta( get_queried_object_id(), '_bomcas_calc_managed', true ) ) {
		return $parts;
	}
	if ( bomcas_calc_seo_controls_title() ) {
		return $parts;
	}
	$id    = get_queried_object_id();
	$edits = class_exists( 'Bomcas_Calc_Copy' ) ? Bomcas_Calc_Copy::edits( $id ) : array();
	if ( ! empty( $edits['seo_title'] ) ) {
		return $parts;
	}
	$parts['title'] = bomcas_calc_public_title( wp_strip_all_tags( get_the_title() ) );
	$parts['site']  = '';
	return $parts;
}

/**
 * Final title after the theme's 50-character fitter.
 *
 * @param string $title Title.
 * @return string
 */
function bomcas_calc_locked_title( $title ) {
	if ( ! is_page() || ! get_post_meta( get_queried_object_id(), '_bomcas_calc_managed', true ) ) {
		return $title;
	}
	return bomcas_calc_public_title( wp_strip_all_tags( get_the_title() ) );
}
add_filter( 'pre_get_document_title', 'bomcas_calc_locked_title', 100 );

/**
 * One WebApplication graph. The theme may still describe the organization.
 */
function bomcas_calc_schema() {
	if ( ! is_page() || ! get_post_meta( get_queried_object_id(), '_bomcas_calc_managed', true ) ) {
		return;
	}
	$permalink = get_permalink();
	$banner    = bomcas_calc_banner( get_queried_object_id() );
	$nodes     = array(
		array(
			'@type' => 'WebApplication',
			'@id'   => $permalink . '#calculator',
			'name'  => wp_strip_all_tags( get_the_title() ),
			'url'   => $permalink,
		),
	);
	if ( ! empty( $banner['url'] ) ) {
		$image = array(
			'@type'      => 'ImageObject',
			'@id'        => $permalink . '#heroimage',
			'url'        => $banner['url'],
			'contentUrl' => $banner['url'],
			'width'      => (int) $banner['width'],
			'height'     => (int) $banner['height'],
			'caption'    => $banner['alt'],
		);
		if ( ! empty( $banner['credit'] ) ) {
			$image['creditText'] = wp_strip_all_tags( $banner['credit'] );
		}
		if ( ! empty( $banner['licence'] ) ) {
			$image['license'] = $banner['licence'];
		}
		$nodes[0]['image'] = array( '@id' => $permalink . '#heroimage' );
		$nodes[]           = $image;
	}
	$bomcas_faqs = class_exists( 'Bomcas_Calc_Copy' ) ? Bomcas_Calc_Copy::faqs( get_queried_object_id() ) : array();
	if ( $bomcas_faqs ) {
		$entities = array();
		foreach ( $bomcas_faqs as $bomcas_faq ) {
			$entities[] = array(
				'@type'          => 'Question',
				'name'           => $bomcas_faq['q'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $bomcas_faq['a'],
				),
			);
		}
		$nodes[] = array(
			'@type'      => 'FAQPage',
			'mainEntity' => $entities,
		);
	}
	if ( 'faq' === (string) get_post_field( 'post_name', get_queried_object_id() ) ) {
		$nodes[] = array(
			'@type'      => 'FAQPage',
			'mainEntity' => array(
				array(
					'@type'          => 'Question',
					'name'           => 'Does a calculator send what I type to the server?',
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => 'No. The arithmetic stays in the browser.',
					),
				),
				array(
					'@type'          => 'Question',
					'name'           => 'Which plan can hide the licence on a client site?',
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => 'Agency, and Professional when that flag is on. White-label: hide your licence from client sites.',
					),
				),
			),
		);
	}
	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => $nodes,
	);
	wp_print_inline_script_tag( (string) wp_json_encode( $graph ), array( 'type' => 'application/ld+json' ) );
}
if ( did_action( 'plugins_loaded' ) ) {
	bomcas_calc_boot();
} else {
	add_action( 'plugins_loaded', 'bomcas_calc_boot' );
}

/**
 * Front assets. Local only.
 */
function bomcas_calc_assets() {
	$style_version = BOMCAS_CALC_VERSION . '.' . (string) filemtime( BOMCAS_CALC_DIR . 'assets/css/calculators.css' );
	wp_register_style( 'bomcas-calculators', BOMCAS_CALC_URL . 'assets/css/calculators.css', array(), $style_version );
	wp_register_script( 'bomcas-calculators-engine', BOMCAS_CALC_URL . 'assets/js/engine-lite.js', array(), BOMCAS_CALC_VERSION, true );
	wp_register_script( 'bomcas-calculators-ui', BOMCAS_CALC_URL . 'assets/js/calculator-ui.js', array( 'bomcas-calculators-engine' ), BOMCAS_CALC_VERSION, true );
	wp_register_script( 'bomcas-growth', BOMCAS_CALC_URL . 'assets/js/growth.js', array(), BOMCAS_CALC_VERSION, true );
	if ( is_singular( 'page' ) && get_post_meta( get_queried_object_id(), '_bomcas_calc_managed', true ) ) {
		wp_enqueue_style( 'bomcas-calculators' );
		wp_enqueue_script( 'bomcas-calculators-engine' );
		wp_enqueue_script( 'bomcas-calculators-ui' );
		bomcas_calc_enqueue_growth();
	}
}

/**
 * Marks calculator pages so the hero can match the guide-page height.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function bomcas_calc_body_class( $classes ) {
	if ( is_singular( 'page' ) && get_post_meta( get_queried_object_id(), '_bomcas_calc_managed', true ) ) {
		$classes[] = 'bomcas-calc-screen';
	}
	return $classes;
}

/**
 * Hero consultation link. Never the contact page and never this page.
 *
 * @param int $id Page id.
 * @return string
 */
function bomcas_calc_hero_service_url( $id ) {
	unset( $id );
	return home_url( '/products/bomcas-tax-payroll-calculators-canada/' );
}

/**
 * One-sentence description of what the page calculates.
 *
 * @param int $id Page id.
 * @return string
 */
function bomcas_calc_page_lede( $id ) {
	$tool = (string) get_post_meta( $id, '_bomcas_calc_tool', true );
	if ( '' === $tool ) {
		$tool = (string) get_post_field( 'post_name', $id );
	}
	$place = (string) get_post_meta( $id, '_bomcas_calc_place_name', true );
	$lines = array(
		'calculators'            => __( 'Pick a calculator for income tax, payroll, sales tax, a vehicle, or a home.', 'bomcas-tax-calculators-canada' ),
		'income-tax'             => __( 'Estimate federal and provincial income tax on the income you enter.', 'bomcas-tax-calculators-canada' ),
		'salary-after-tax'       => __( 'Estimate take-home pay after income tax, CPP, and EI.', 'bomcas-tax-calculators-canada' ),
		'sales-tax'              => __( 'Add or remove GST, HST, PST, or QST on a standard taxable amount.', 'bomcas-tax-calculators-canada' ),
		'payroll-deductions'     => __( 'Estimate CPP, EI, and income tax withheld on a regular paycheque.', 'bomcas-tax-calculators-canada' ),
		'vehicle-purchase'       => __( 'Estimate the tax on a dealer sale or a private vehicle purchase.', 'bomcas-tax-calculators-canada' ),
		'land-transfer-tax'      => __( 'Estimate property transfer tax from the price and the province.', 'bomcas-tax-calculators-canada' ),
		'mortgage-payment'       => __( 'Estimate a mortgage payment from the price, down payment, and rate you enter.', 'bomcas-tax-calculators-canada' ),
		'mortgage-affordability' => __( 'See the stress-test payment and whether the loan fits common debt-service limits.', 'bomcas-tax-calculators-canada' ),
		'corporate-tax'          => __( 'Estimate corporate tax on active business income.', 'bomcas-tax-calculators-canada' ),
		'salary-vs-dividend'     => __( 'Compare take-home cash from a salary and from a non-eligible dividend.', 'bomcas-tax-calculators-canada' ),
		'capital-cost-allowance' => __( 'Estimate capital cost allowance for the class and the cost you enter.', 'bomcas-tax-calculators-canada' ),
		'tax-instalments'        => __( 'Estimate whether personal tax instalments are required.', 'bomcas-tax-calculators-canada' ),
		'cra-penalty-interest'   => __( 'Estimate a late-filing penalty and the prescribed interest on a balance.', 'bomcas-tax-calculators-canada' ),
		'employer-health-tax'    => __( 'Estimate employer health tax on payroll in the provinces that charge it.', 'bomcas-tax-calculators-canada' ),
		'self-employed-tax'      => __( 'Estimate CPP contributions on self-employment income.', 'bomcas-tax-calculators-canada' ),
		'lease-vs-buy'           => __( 'Compare the cost of leasing a vehicle with the cost of buying it.', 'bomcas-tax-calculators-canada' ),
		'cmhc-insurance'         => __( 'Estimate the CMHC mortgage insurance premium from the down payment.', 'bomcas-tax-calculators-canada' ),
		'quebec-withholding'     => __( 'See how Quebec income tax is withheld on a regular paycheque.', 'bomcas-tax-calculators-canada' ),
		'rrsp-room'              => __( 'Estimate RRSP room from last year’s earned income and the dollar limit.', 'bomcas-tax-calculators-canada' ),
		'tfsa-room'              => __( 'Add up TFSA room from the annual limits and the contributions you enter.', 'bomcas-tax-calculators-canada' ),
		'fhsa'                   => __( 'Estimate how much you can still put in a first home savings account.', 'bomcas-tax-calculators-canada' ),
		'capital-gains'          => __( 'Estimate the tax on a capital gain. Half of the gain is included in income.', 'bomcas-tax-calculators-canada' ),
		'canada-child-benefit'   => __( 'Estimate the federal Canada child benefit for the benefit year in the rate dataset.', 'bomcas-tax-calculators-canada' ),
		'ei-benefits'            => __( 'Estimate a regular EI benefit from weekly insurable earnings.', 'bomcas-tax-calculators-canada' ),
		'car-allowance'          => __( 'Estimate a car allowance from the prescribed cents-per-kilometre rates.', 'bomcas-tax-calculators-canada' ),
		'donations'              => __( 'Estimate the federal and provincial tax credit on a donation.', 'bomcas-tax-calculators-canada' ),
		'old-age-security'       => __( 'Estimate the Old Age Security recovery tax when income is above the threshold.', 'bomcas-tax-calculators-canada' ),
		'quebec-welcome-tax'     => __( 'Estimate Quebec’s general welcome tax on a property transfer.', 'bomcas-tax-calculators-canada' ),
	);
	$line  = isset( $lines[ $tool ] ) ? $lines[ $tool ] : __( 'Estimate the tax figure from the amounts you enter.', 'bomcas-tax-calculators-canada' );
	if ( '' !== $place ) {
		$line = sprintf(
			/* translators: 1: year label, 2: province, territory, or state */
			__( 'A {year_label} estimate for %s. What you type stays in this browser.', 'bomcas-tax-calculators-canada' ),
			$place
		);
	}
	return Bomcas_Tokens::replace( $line );
}

/**
 * Unique body intro for a place page. Not the same sentence as the hero.
 *
 * @param int $id Page id.
 * @return string
 */
function bomcas_calc_place_intro( $id ) {
	$edits = class_exists( 'Bomcas_Calc_Copy' ) ? Bomcas_Calc_Copy::edits( $id ) : array();
	if ( ! empty( $edits['intro'] ) ) {
		return Bomcas_Tokens::replace( $edits['intro'] );
	}
	$saved = (string) get_post_meta( $id, '_bomcas_calc_intro', true );
	$tool  = (string) get_post_meta( $id, '_bomcas_calc_tool', true );
	$place = (string) get_post_meta( $id, '_bomcas_calc_place_name', true );
	if ( '' === $place ) {
		return $saved;
	}
	if ( 0 === strpos( $tool, 'us-' ) ) {
		return '';
	}
	$canadian = bomcas_calc_ca_intro( $tool, $place );
	if ( '' !== $canadian ) {
		return $canadian;
	}
	if ( '' !== $saved ) {
		return Bomcas_Tokens::replace( str_replace( '. the ', '. The ', $saved ) );
	}
	return Bomcas_Tokens::replace(
		sprintf(
			/* translators: 1: place name, 2: tool slug */
			__( 'How %1$s %2$s is worked out for {year_label}, using the published brackets for that place.', 'bomcas-tax-calculators-canada' ),
			$place,
			str_replace( '-', ' ', $tool )
		)
	);
}

/**
 * One-sentence 2026 introduction for a Canadian place page.
 *
 * @param string $tool  Tool id.
 * @param string $place Place name.
 * @return string
 */
function bomcas_calc_ca_intro( $tool, $place ) {
	$codes = array(
		'Alberta'                   => 'AB',
		'British Columbia'          => 'BC',
		'Manitoba'                  => 'MB',
		'New Brunswick'             => 'NB',
		'Newfoundland and Labrador' => 'NL',
		'Northwest Territories'     => 'NT',
		'Nova Scotia'               => 'NS',
		'Nunavut'                   => 'NU',
		'Ontario'                   => 'ON',
		'Prince Edward Island'      => 'PE',
		'Quebec'                    => 'QC',
		'Saskatchewan'              => 'SK',
		'Yukon'                     => 'YT',
	);
	if ( ! isset( $codes[ $place ] ) ) {
		return '';
	}
	$code = $codes[ $place ];
	if ( 'salary-after-tax' === $tool ) {
		return Bomcas_Tokens::replace(
			sprintf(
				/* translators: 1: place, 2: province code */
				__( 'Take-home pay in %1$s subtracts {year_label} income tax, starting at {rate:%2$s.income.first}. Only the enhanced portion of CPP is deductible. EI premiums are not. Both still come off the cheque. It is not a pay stub and it does not know a claim code.', 'bomcas-tax-calculators-canada' ),
				$place,
				$code
			)
		);
	}
	if ( 'payroll-deductions' === $tool ) {
		return Bomcas_Tokens::replace(
			sprintf(
				/* translators: 1: An or A, 2: place, 3: province code */
				__( '%1$s %2$s paycheque estimate withholds {year_label} tax, starting from {rate:%3$s.income.first} on the annual table, plus pension and employment insurance. The net on the sample is not the remittance class.', 'bomcas-tax-calculators-canada' ),
				preg_match( '/^[AEIOU]/i', $place ) ? 'An' : 'A',
				$place,
				$code
			)
		);
	}
	if ( in_array( $tool, array( 'income-tax', 'self-employed-tax' ), true ) ) {
		if ( 'ON' === $code ) {
			$text = __( 'Ontario’s {year_label} income tax starts at {rate:ON.income.first}. The basic personal amount is {bpa:ON}. Surtax begins once provincial tax passes {money:ON.surtax.0}, and the Ontario Health Premium is charged separately from the brackets.', 'bomcas-tax-calculators-canada' );
		} elseif ( 'QC' === $code ) {
			$text = __( 'Quebec’s {year_label} income tax starts at {rate:QC.income.first}. The basic personal amount is {bpa:QC}. Residents also receive the {rate:QC.abatement} abatement of basic federal tax.', 'bomcas-tax-calculators-canada' );
		} elseif ( 'BC' === $code ) {
			$text = __( 'British Columbia’s {year_label} income tax starts at {rate:BC.income.first}. The basic personal amount is {bpa:BC}. A tax reduction can lower the bill when income is modest.', 'bomcas-tax-calculators-canada' );
		} else {
			$owned = 's' === substr( $place, -1 ) ? $place . "'" : $place . "'s";
			$text  = sprintf(
				/* translators: 1: possessive place name, 2: province code, 3: province code */
				__( '%1$s {year_label} income tax starts at {rate:%2$s.income.first}. The basic personal amount is {bpa:%3$s}. Only the enhanced portion of CPP is deductible. EI premiums are not. Credits that are not in the rate table are left off.', 'bomcas-tax-calculators-canada' ),
				$owned,
				$code,
				$code
			);
		}
		return Bomcas_Tokens::replace( $text );
	}
	return '';
}

/**
 * Title-case heading for the white section under the breadcrumb.
 *
 * @param string $title Page title.
 * @return string
 */
function bomcas_calc_heading( $title, $id = 0 ) {
	$small = array( 'a', 'an', 'and', 'or', 'of', 'the', 'for', 'to', 'in' );
	$words = preg_split( '/\s+/', trim( (string) $title ) );
	foreach ( $words as $i => $word ) {
		$lower = strtolower( $word );
		if ( $i > 0 && in_array( $lower, $small, true ) ) {
			$words[ $i ] = $lower;
		} else {
			$words[ $i ] = ucwords( $lower );
		}
	}
	$heading       = implode( ' ', $words );
	$place_heading = $id ? (string) get_post_meta( $id, '_bomcas_calc_place_name', true ) : '';
	$tool          = $id ? (string) get_post_meta( $id, '_bomcas_calc_tool', true ) : '';
	if ( '' === $tool && $id ) {
		$tool = (string) get_post_field( 'post_name', $id );
	}
	$aliases = array(
		'calculators'            => __( 'Tax Calculators', 'bomcas-tax-calculators-canada' ),
		'salary-after-tax'       => __( 'Take-home Pay and Payroll', 'bomcas-tax-calculators-canada' ),
		'capital-cost-allowance' => __( 'Capital Cost Allowance Tax Estimate', 'bomcas-tax-calculators-canada' ),
		'vehicle-purchase'       => __( 'Vehicle Purchase Tax', 'bomcas-tax-calculators-canada' ),
		'lease-vs-buy'           => __( 'Lease or Buy and Vehicle Tax', 'bomcas-tax-calculators-canada' ),
		'mortgage-affordability' => __( 'Mortgage Financing Estimate', 'bomcas-tax-calculators-canada' ),
		'mortgage-payment'       => __( 'Mortgage Financing Payment', 'bomcas-tax-calculators-canada' ),
		'cmhc-insurance'         => __( 'CMHC Mortgage Financing Insurance', 'bomcas-tax-calculators-canada' ),
		'quebec-withholding'     => __( 'Quebec Payroll Withholding', 'bomcas-tax-calculators-canada' ),
		'rrsp-room'              => __( 'RRSP Room and Tax', 'bomcas-tax-calculators-canada' ),
		'tfsa-room'              => __( 'TFSA Room and Tax', 'bomcas-tax-calculators-canada' ),
		'fhsa'                   => __( 'FHSA Savings and Tax', 'bomcas-tax-calculators-canada' ),
		'capital-gains'          => __( 'Capital Gains Tax', 'bomcas-tax-calculators-canada' ),
		'canada-child-benefit'   => __( 'Canada Child Benefit Tax Estimate', 'bomcas-tax-calculators-canada' ),
		'ei-benefits'            => __( 'Employment Insurance Payroll Benefit', 'bomcas-tax-calculators-canada' ),
		'car-allowance'          => __( 'Car Allowance Tax Estimate', 'bomcas-tax-calculators-canada' ),
		'donations'              => __( 'Donation Tax Credit', 'bomcas-tax-calculators-canada' ),
		'old-age-security'       => __( 'Old Age Security Recovery Tax', 'bomcas-tax-calculators-canada' ),
		'salary-vs-dividend'     => __( 'Salary Versus Dividend Tax', 'bomcas-tax-calculators-canada' ),
	);
	if ( ! preg_match( '/tax|account|bookkeep|payroll|gst|pst|cra|audit|financ|incorporat|estate|filing|return/i', $heading ) ) {
		if ( isset( $aliases[ $tool ] ) ) {
			$place   = $id ? (string) get_post_meta( $id, '_bomcas_calc_place_name', true ) : '';
			$heading = '' !== $place ? $place . ' ' . $aliases[ $tool ] : $aliases[ $tool ];
		} else {
			$heading .= ' ' . __( 'Tax Estimate', 'bomcas-tax-calculators-canada' );
		}
	}
	if ( 'Calculators' === $heading ) {
		$heading = __( 'Tax Calculators', 'bomcas-tax-calculators-canada' );
	}
	$edits = class_exists( 'Bomcas_Calc_Copy' ) ? Bomcas_Calc_Copy::edits( $id ) : array();
	if ( ! empty( $edits['h2'] ) ) {
		return Bomcas_Tokens::replace( $edits['h2'] );
	}
	if ( 0 === strcasecmp( $heading, trim( (string) $title ) ) ) {
		$heading = sprintf(
			/* translators: %s: calculator name */
			__( 'How to use the %s calculator', 'bomcas-tax-calculators-canada' ),
			$heading
		);
	}
	if ( '' !== $place_heading ) {
		$topics = array(
			'income-tax'         => __( 'income tax', 'bomcas-tax-calculators-canada' ),
			'sales-tax'          => __( 'sales tax', 'bomcas-tax-calculators-canada' ),
			'payroll-deductions' => __( 'payroll tax', 'bomcas-tax-calculators-canada' ),
			'salary-after-tax'   => __( 'take-home pay after tax', 'bomcas-tax-calculators-canada' ),
			'vehicle-purchase'   => __( 'vehicle purchase tax', 'bomcas-tax-calculators-canada' ),
			'land-transfer-tax'  => __( 'land transfer tax', 'bomcas-tax-calculators-canada' ),
		);
		$topic  = isset( $topics[ $tool ] ) ? $topics[ $tool ] : __( 'tax', 'bomcas-tax-calculators-canada' );
		return sprintf(
			/* translators: 1: place name, 2: tax topic */
			__( 'How %1$s %2$s is calculated', 'bomcas-tax-calculators-canada' ),
			$place_heading,
			$topic
		);
	}
	return Bomcas_Tokens::replace( $heading );
}

/**
 * Dynamic block.
 */
function bomcas_calc_register_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	register_block_type(
		BOMCAS_CALC_DIR . 'blocks/calculator',
		array(
			'render_callback' => 'bomcas_calc_block_render',
		)
	);
}

/**
 * Block renderer.
 *
 * @param array $attrs Attributes.
 * @return string
 */
function bomcas_calc_block_render( $attrs ) {
	$id = isset( $attrs['calculator'] ) ? sanitize_key( $attrs['calculator'] ) : 'sales-tax';
	return bomcas_calc_shortcode(
		array(
			'id'       => $id,
			'province' => isset( $attrs['province'] ) ? $attrs['province'] : '',
		)
	);
}

/**
 * Shortcode.
 *
 * @param array $atts Attributes.
 * @return string
 */
function bomcas_calc_enqueue_growth() {
	wp_enqueue_script( 'bomcas-growth' );
	wp_localize_script(
		'bomcas-growth',
		'BomcasGrowth',
		array(
			'ajax'    => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'bomcas_growth' ),
			'page'    => get_queried_object_id(),
			'ga4'     => (string) get_option( 'bomcas_ga4', '' ),
			'strings' => class_exists( 'Bomcas_I18n' ) ? Bomcas_I18n::js_strings() : array(),
		)
	);
}

function bomcas_calc_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'id'       => 'sales-tax',
			'province' => '',
		),
		$atts,
		'bomcas_calculator'
	);
	$id = sanitize_key( $atts['id'] );
	if ( ! in_array( $id, array( 'sales-tax', 'income-tax', 'mortgage-payment' ), true ) ) {
		return '';
	}
	if ( ! bomcas_calc_tool_enabled( $id ) ) {
		return '';
	}
	wp_enqueue_style( 'bomcas-calculators' );
	wp_enqueue_script( 'bomcas-calculators-engine' );
	wp_enqueue_script( 'bomcas-calculators-ui' );
	bomcas_calc_enqueue_growth();
	if ( wp_script_is( 'bomcas-calculators-pro', 'registered' ) ) {
		wp_enqueue_script( 'bomcas-calculators-pro' );
		wp_enqueue_script( 'bomcas-calculators-pro-ui' );
	}
	$province     = strtoupper( preg_replace( '/[^A-Z]/', '', strtoupper( $atts['province'] ) ) );
	$presentation = apply_filters( 'bomcas_calc_presentation', array(), $id );
	$payload      = array();
	$rows         = array();
	$fresh        = '';
	if ( 'sales-tax' === $id ) {
		$payload = bomcas_calc_seed( 'sales-tax.json' );
		$rows    = bomcas_calc_sales_rows( $payload, $province ? $province : 'BC' );
		if ( 'NS' === $province ) {
			$fresh = __( 'Nova Scotia HST changes on {date:NS.sales.latest_from}. Vehicles, real property and exempt supplies are not covered.', 'bomcas-tax-calculators-canada' );
		} else {
			$fresh = __( 'General sales-tax rates for the place you select. Vehicles, real property and exempt supplies are not covered.', 'bomcas-tax-calculators-canada' );
		}
	} elseif ( 'income-tax' === $id ) {
		$year    = bomcas_calc_current_year();
		$payload = array( 'years' => array( (string) $year['tax_year'] => $year ) );
		$rows    = bomcas_calc_income_rows( $year, $province ? $province : 'BC' );
		$fresh   = __( 'Rates effective {effective}. This is an estimate for {year_label}, not an assessment.', 'bomcas-tax-calculators-canada' );
	} else {
		$rows  = array(
			array(
				'item'   => 'Compounding convention',
				'value'  => 'Semi-annual, unless you choose monthly',
				'source' => 'Interest Act section 6 blended-payment disclosure convention',
				'url'    => 'https://laws-lois.justice.gc.ca/eng/acts/I-15/page-1.html',
			),
		);
		$fresh = 'No rate table. You enter the interest rate. The payment uses the compounding convention you select.';
	}
	$payload = apply_filters( 'bomcas_calc_payload', $payload, $id );
	$rows    = apply_filters( 'bomcas_calc_rate_rows', $rows, $id, $province );
	return Bomcas_Calc_View::render(
		array(
			'calc'         => $id,
			'title'        => bomcas_calc_title( $id ),
			'province'     => $province,
			'payload'      => $payload,
			'year'         => 'income-tax' === $id ? bomcas_calc_current_year() : array(),
			'sales'        => 'sales-tax' === $id ? $payload : array(),
			'freshness'    => Bomcas_Tokens::replace( $fresh ),
			'stale'        => false,
			'rate_rows'    => $rows,
			'credit'       => bomcas_calc_show_credit(),
			'presentation' => is_array( $presentation ) ? $presentation : array(),
		)
	);
}

/**
 * Title.
 *
 * @param string $id Calculator.
 * @return string
 */
function bomcas_calc_title( $id ) {
	$titles = array(
		'sales-tax'        => __( 'Sales tax calculator', 'bomcas-tax-calculators-canada' ),
		'income-tax'       => __( 'Income tax calculator', 'bomcas-tax-calculators-canada' ),
		'mortgage-payment' => __( 'Mortgage payment calculator', 'bomcas-tax-calculators-canada' ),
	);
	return isset( $titles[ $id ] ) ? $titles[ $id ] : $id;
}

/**
 * Sales-tax disclosure rows.
 *
 * @param array  $payload Payload.
 * @param string $province Province.
 * @return array
 */
function bomcas_calc_sales_rows( $payload, $province ) {
	$rows = array();
	if ( empty( $payload['jurisdictions'][ $province ] ) ) {
		return $rows;
	}
	foreach ( $payload['jurisdictions'][ $province ] as $segment ) {
		$rate   = ( $segment['gst'] + $segment['pst'] + $segment['hst'] + $segment['qst'] ) * 100;
		$rows[] = array(
			'item'   => $segment['from'] . ' to ' . $segment['to'],
			'value'  => rtrim( rtrim( number_format( $rate, 3, '.', '' ), '0' ), '.' ) . '% ' . $segment['label'],
			'source' => 'CRA GST/HST rates',
			'url'    => 'https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/gst-hst-businesses/charge-collect-which-rate/calculator.html',
		);
	}
	return $rows;
}

/**
 * Income-tax disclosure rows for the current year.
 *
 * @param array  $year Year.
 * @param string $province Province.
 * @return array
 */
function bomcas_calc_income_rows( $year, $province ) {
	$rows = array();
	if ( empty( $year['jurisdictions'][ $province ] ) ) {
		return $rows;
	}
	$rec    = $year['jurisdictions'][ $province ];
	$fed    = $year['jurisdictions']['FED'];
	$rows[] = array(
		'item'   => 'Lowest provincial or territorial rate',
		'value'  => ( $rec['brackets'][0]['rate'] * 100 ) . '%',
		'source' => 'CRA or Revenu Québec rate table',
		'url'    => 'https://www.canada.ca/en/revenue-agency/services/tax/individuals/tax-rates-brackets/current-year.html',
	);
	$rows[] = array(
		'item'   => 'Basic personal amount',
		'value'  => number_format( (float) $rec['bpa']['max'], 0 ),
		'source' => 'Payroll formulas or provincial basic amount',
		'url'    => 'https://www.canada.ca/en/revenue-agency/services/forms-publications/payroll/t4127-payroll-deductions-formulas/t4127-jan.html',
	);
	$rows[] = array(
		'item'   => 'Federal lowest rate',
		'value'  => ( $fed['brackets'][0]['rate'] * 100 ) . '%',
		'source' => 'CRA federal brackets',
		'url'    => 'https://www.canada.ca/en/revenue-agency/services/tax/individuals/tax-rates-brackets/current-year.html',
	);
	return $rows;
}

/**
 * Settings page. Upgrade text stays on this screen.
 */
function bomcas_calc_admin_assets() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || false === strpos( $screen->id, 'bomcas' ) ) {
		return;
	}
	Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
}

function bomcas_calc_buyer_tabs( $current ) {
	$tabs = array(
		array(
			'label'   => __( 'Overview', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-calculators-home' ),
			'current' => 'home' === $current,
		),
		array(
			'label'   => __( 'Settings', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'options-general.php?page=bomcas-calculators' ),
			'current' => 'settings' === $current,
		),
		array(
			'label'   => __( 'Toggles', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-toggles' ),
			'current' => 'toggles' === $current,
		),
		array(
			'label'   => __( 'Rates', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'options-general.php?page=bomcas-rates' ),
			'current' => 'rates' === $current,
		),
		array(
			'label'   => __( 'Page content', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-page-content' ),
			'current' => 'content' === $current,
		),
		array(
			'label'   => __( 'Leads', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-leads' ),
			'current' => 'leads' === $current,
		),
		array(
			'label'   => __( 'Usage', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-stats' ),
			'current' => 'stats' === $current,
		),
		array(
			'label'   => __( 'Booking', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-booking' ),
			'current' => 'booking' === $current,
		),
		array(
			'label'   => __( 'Languages', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-languages' ),
			'current' => 'languages' === $current,
		),
		array(
			'label'   => __( 'Packs', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-packs' ),
			'current' => 'packs' === $current,
		),
		array(
			'label'   => __( 'Trust', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-trust' ),
			'current' => 'trust' === $current,
		),
		array(
			'label'   => __( 'Setup', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-setup' ),
			'current' => 'setup' === $current,
		),
		array(
			'label'   => __( 'Builder', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-builder' ),
			'current' => 'builder' === $current,
		),
		array(
			'label'   => __( 'Account', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-account' ),
			'current' => 'account' === $current,
		),
		array(
			'label'   => __( 'White-label', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-privacy' ),
			'current' => 'privacy' === $current,
		),
		array(
			'label'   => __( 'Help', 'bomcas-tax-calculators-canada' ),
			'url'     => admin_url( 'admin.php?page=bomcas-help' ),
			'current' => 'help' === $current,
		),
	);
	return $tabs;
}

/**
 * Submenus in the same order as the tab bar, after the parent menu exists.
 */
function bomcas_calc_register_submenus() {
	$parent = 'bomcas-calculators-home';
	add_submenu_page( $parent, __( 'Settings', 'bomcas-tax-calculators-canada' ), __( 'Settings', 'bomcas-tax-calculators-canada' ), 'manage_options', 'options-general.php?page=bomcas-calculators' );
	add_submenu_page( $parent, __( 'Toggles', 'bomcas-tax-calculators-canada' ), __( 'Toggles', 'bomcas-tax-calculators-canada' ), 'manage_options', 'bomcas-toggles', 'bomcas_calc_render_toggles' );
	add_submenu_page( $parent, __( 'Rates', 'bomcas-tax-calculators-canada' ), __( 'Rates', 'bomcas-tax-calculators-canada' ), 'manage_options', 'options-general.php?page=bomcas-rates' );
	Bomcas_Calc_Copy::menu();
	Bomcas_Leads::menu();
	Bomcas_Stats::menu();
	Bomcas_Booking::menu();
	Bomcas_I18n::menu();
	Bomcas_Packs::menu();
	Bomcas_Trust::menu();
	add_submenu_page( $parent, __( 'Builder', 'bomcas-tax-calculators-canada' ), __( 'Builder', 'bomcas-tax-calculators-canada' ), 'manage_options', 'admin.php?page=bomcas-builder' );
	add_submenu_page( $parent, __( 'Account & Upgrade', 'bomcas-tax-calculators-canada' ), __( 'Account & Upgrade', 'bomcas-tax-calculators-canada' ), 'manage_options', 'bomcas-account', array( 'Bomcas_Calc_Account', 'render' ) );
	add_submenu_page( $parent, __( 'White-label', 'bomcas-tax-calculators-canada' ), __( 'White-label', 'bomcas-tax-calculators-canada' ), 'manage_options', 'bomcas-privacy', array( 'Bomcas_Calc_Privacy', 'render' ) );
	Bomcas_Docs::menu();
}

function bomcas_calc_admin_menu() {
	add_options_page(
		__( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ),
		__( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ),
		'manage_options',
		'bomcas-calculators',
		'bomcas_calc_render_settings'
	);
}

/**
 * Settings screen.
 */
function bomcas_calc_render_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
	Bomcas_Admin_UI::open(
		array(
			'doc'     => 'getting-started',
			'product' => __( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ),
			'kicker'  => __( 'Settings · version ', 'bomcas-tax-calculators-canada' ) . BOMCAS_CALC_VERSION,
			'badge'   => __( 'Lite', 'bomcas-tax-calculators-canada' ),
			'tabs'    => bomcas_calc_buyer_tabs( 'settings' ),
		)
	);
	echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Credit', 'bomcas-tax-calculators-canada' ) . '</h2>';
	echo '<p>' . esc_html__( 'Sales tax, the current-year income-tax estimate and the mortgage payment calculator run in the browser. This plugin does not send calculator inputs to a server.', 'bomcas-tax-calculators-canada' ) . '</p>';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'bomcas_calc_save_credit' );
	echo '<input type="hidden" name="action" value="bomcas_calc_save_credit" />';
	echo '<label><input type="checkbox" name="bomcas_calc_show_credit" value="1" ' . checked( bomcas_calc_show_credit(), true, false ) . ' /> ';
	echo esc_html__( 'Show “Calculator by BOMCAS Canada” with a link to bomcas.ca beneath calculators and printed reports. Off until you save this box.', 'bomcas-tax-calculators-canada' ) . '</label>';
	echo '<div class="bomcas-savebar">';
	submit_button( __( 'Save credit setting', 'bomcas-tax-calculators-canada' ) );
	echo '</div>';
	echo '</form>';
	if ( ! get_user_meta( get_current_user_id(), 'bomcas_hide_pro_note', true ) ) {
		echo '<h2>' . esc_html__( 'Available in Pro', 'bomcas-tax-calculators-canada' ) . '</h2>';
		echo '<p>' . esc_html__( 'Payroll and take-home pay start on the Professional plan. The $19 Personal plan does not include them. Lite does not install Pro. Download it from My Account and upload the zip yourself.', 'bomcas-tax-calculators-canada' ) . '</p>';
		echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=bomcas-account' ) ) . '">' . esc_html__( 'Compare plans', 'bomcas-tax-calculators-canada' ) . '</a>';
		echo ' · <a href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=bomcas_calc_hide_pro_note' ), 'bomcas_calc_hide_pro_note' ) ) . '">' . esc_html__( 'Dismiss', 'bomcas-tax-calculators-canada' ) . '</a></p>';
	}
	echo '<p><a href="' . esc_url( 'https://bomcas.ca' ) . '">' . esc_html__( 'BOMCAS Canada', 'bomcas-tax-calculators-canada' ) . '</a>';
	echo ' · <a href="' . esc_url( 'https://accountantsexpert.com/products/bomcas-tax-payroll-calculators-canada/' ) . '">' . esc_html__( 'Plugin page', 'bomcas-tax-calculators-canada' ) . '</a>';
	echo ' · <a href="' . esc_url( 'https://accountantsexpert.com/calculators-docs/' ) . '">' . esc_html__( 'Docs', 'bomcas-tax-calculators-canada' ) . '</a>';
	echo ' · <a href="' . esc_url( 'https://accountantsexpert.com/products/bomcas-tax-payroll-calculators-canada/' ) . '">' . esc_html__( 'Pro', 'bomcas-tax-calculators-canada' ) . '</a></p>';
	echo '<p><a href="' . esc_url( Bomcas_Calc_Account::server() . '/calculators-demo/' ) . '">' . esc_html__( 'See the calculator demo', 'bomcas-tax-calculators-canada' ) . '</a></p>';
	echo '</section>';
	Bomcas_Admin_UI::close();
}

/**
 * Buyer overview.
 */
function bomcas_calc_render_home() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
	$state = (string) get_option( 'bomcas_calc_license_state', '' );
	$plan  = (string) get_option( 'bomcas_calc_plan_label', __( 'Lite', 'bomcas-tax-calculators-canada' ) );
	$badge = '' !== $state ? ucfirst( $state ) : __( 'Lite', 'bomcas-tax-calculators-canada' );
	Bomcas_Admin_UI::open(
		array(
			'doc'     => 'getting-started',
			'product' => __( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ),
			'kicker'  => __( 'Overview · version ', 'bomcas-tax-calculators-canada' ) . BOMCAS_CALC_VERSION,
			'badge'   => $badge,
			'tabs'    => bomcas_calc_buyer_tabs( 'home' ),
		)
	);
	$countries = get_option(
		'bomcas_calc_countries',
		array(
			'CA' => 1,
		)
	);
	$codes     = array();
	$names     = array();
	if ( ! empty( $countries['CA'] ) ) {
		$codes[] = 'CA';
		$names[] = __( 'Canada', 'bomcas-tax-calculators-canada' );
	}
	$pushed = (int) get_option( 'bomcas_rates_pushed_at', 0 );
	$rate   = $pushed > 0 ? wp_date( 'M j, Y', $pushed ) : __( 'Shipped copy', 'bomcas-tax-calculators-canada' );
	Bomcas_Admin_UI::kpis(
		array(
			array(
				'icon'  => 'check',
				'label' => __( 'Licence', 'bomcas-tax-calculators-canada' ),
				'value' => '' !== $state ? ucfirst( $state ) : __( 'Local', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'icon'  => 'card',
				'label' => __( 'Plan', 'bomcas-tax-calculators-canada' ),
				'value' => '' !== $plan ? $plan : __( 'Lite', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'icon'  => 'refresh',
				'label' => __( 'Rates', 'bomcas-tax-calculators-canada' ),
				'value' => $rate,
			),
			array(
				'icon'  => 'globe',
				'label' => __( 'Countries', 'bomcas-tax-calculators-canada' ),
				'value' => $codes ? implode( ' + ', $codes ) : __( 'Off', 'bomcas-tax-calculators-canada' ),
				'note'  => $names ? implode( ' and ', $names ) : '',
			),
		)
	);
	$days  = class_exists( 'Bomcas_Stats' ) ? Bomcas_Stats::last_days( 30 ) : array();
	$runs  = 0;
	$leads = 0;
	foreach ( $days as $row ) {
		$runs  += (int) $row['runs'];
		$leads += (int) $row['leads'];
	}
	$metric = $runs > 0 ? 'runs' : 'leads';
	$label  = 'runs' === $metric ? __( 'Calculations, last 30 days', 'bomcas-tax-calculators-canada' ) : __( 'Leads, last 30 days', 'bomcas-tax-calculators-canada' );
	$max    = 1;
	foreach ( $days as $row ) {
		$max = max( $max, (int) $row[ $metric ] );
	}
	echo '<section class="bomcas-admin__card"><h2>' . esc_html( $label ) . '</h2>';
	if ( ( $runs + $leads ) < 1 ) {
		echo '<div class="bomcas-empty"><svg width="48" height="32" viewBox="0 0 48 32" aria-hidden="true"><rect x="2" y="18" width="6" height="10" rx="1" fill="currentColor"></rect><rect x="12" y="12" width="6" height="16" rx="1" fill="currentColor"></rect><rect x="22" y="6" width="6" height="22" rx="1" fill="currentColor"></rect><rect x="32" y="14" width="6" height="14" rx="1" fill="currentColor"></rect><rect x="42" y="9" width="6" height="19" rx="1" fill="currentColor"></rect></svg><strong>' . esc_html__( 'No activity yet', 'bomcas-tax-calculators-canada' ) . '</strong><p>' . esc_html__( 'Each calculation on this site adds a bar. Amounts a visitor types are not stored.', 'bomcas-tax-calculators-canada' ) . '</p></div>';
	} else {
		$vals = array();
		foreach ( $days as $day => $row ) {
			$vals[] = array( $day, (int) $row[ $metric ] );
		}
		$n      = max( 1, count( $vals ) );
		$width  = 720;
		$height = 168;
		$base   = 132;
		$gap    = 3;
		$bar    = ( $width - ( $n - 1 ) * $gap ) / $n;
		echo '<svg class="bomcas-chartsvg" viewBox="0 0 ' . (int) $width . ' ' . (int) $height . '" role="img" aria-label="' . esc_attr( $label ) . '">';
		echo '<line x1="0" y1="' . (int) $base . '" x2="' . (int) $width . '" y2="' . (int) $base . '" stroke="#e3e8ef" stroke-width="1"></line>';
		foreach ( $vals as $i => $point ) {
			$bh   = $point[1] > 0 ? max( 8, (int) round( ( $point[1] / $max ) * ( $base - 8 ) ) ) : 0;
			$x    = $i * ( $bar + $gap );
			$y    = $base - $bh;
			$fill = $point[1] > 0 ? '#0092db' : '#d5dee8';
			if ( $bh < 1 ) {
				continue;
			}
			echo '<rect x="' . esc_attr( (string) $x ) . '" y="' . esc_attr( (string) $y ) . '" width="' . esc_attr( (string) max( 2, $bar - 1 ) ) . '" height="' . esc_attr( (string) $bh ) . '" rx="2" fill="' . esc_attr( $fill ) . '"><title>' . esc_html( $point[0] . ': ' . $point[1] ) . '</title></rect>';
		}
		$first = wp_date( 'M j', strtotime( $vals[0][0] . ' 12:00:00 UTC' ) );
		$last  = wp_date( 'M j', strtotime( $vals[ count( $vals ) - 1 ][0] . ' 12:00:00 UTC' ) );
		echo '<text x="0" y="156" fill="#5c6b7a" font-size="12">' . esc_html( $first ) . '</text>';
		echo '<text x="' . (int) $width . '" y="156" fill="#5c6b7a" font-size="12" text-anchor="end">' . esc_html( $last ) . '</text>';
		echo '</svg>';
		if ( 'runs' === $metric ) {
			/* translators: %d: number of calculations in the last 30 days. */
			echo '<p class="bomcas-chartmeta">' . esc_html( sprintf( _n( '%d calculation on this site. Amounts a visitor types are not stored.', '%d calculations on this site. Amounts a visitor types are not stored.', $runs, 'bomcas-tax-calculators-canada' ), $runs ) ) . '</p>';
		} else {
			/* translators: %d: number of leads in the last 30 days. */
			echo '<p class="bomcas-chartmeta">' . esc_html( sprintf( _n( '%d lead in the last 30 days.', '%d leads in the last 30 days.', $leads, 'bomcas-tax-calculators-canada' ), $leads ) ) . '</p>';
		}
	}
	echo '</section>';
	echo '<div class="bomcas-pair">';
	echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Recent leads', 'bomcas-tax-calculators-canada' ) . '</h2>';
	$leads = class_exists( 'Bomcas_Leads' ) ? Bomcas_Leads::recent( 5 ) : array();
	if ( ! $leads ) {
		echo '<div class="bomcas-empty"><strong>' . esc_html__( 'No leads yet', 'bomcas-tax-calculators-canada' ) . '</strong><p>' . esc_html__( 'Turn the form on under Leads. It stays off until you save that box.', 'bomcas-tax-calculators-canada' ) . '</p></div>';
	} else {
		echo '<table class="widefat"><thead><tr><th>' . esc_html__( 'When', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Name', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Calculator', 'bomcas-tax-calculators-canada' ) . '</th></tr></thead><tbody>';
		foreach ( $leads as $lead ) {
			$when = date_create( (string) $lead['created_at'], wp_timezone() );
			echo '<tr><td>' . esc_html( $when ? $when->format( 'M j, Y, g:i A' ) : '' ) . '</td><td>' . esc_html( $lead['person_name'] ) . '</td><td>' . esc_html( $lead['calculator'] ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}
	echo '</section>';
	$conflicts = class_exists( 'Bomcas_Rates' ) ? Bomcas_Rates::conflicts() : array();
	echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Rate update', 'bomcas-tax-calculators-canada' ) . '</h2>';
	/* translators: %s: date of the signed rate package. */
	echo '<p>' . esc_html( $pushed > 0 ? sprintf( __( 'A signed rate package was saved on %s. Overrides you typed are still used.', 'bomcas-tax-calculators-canada' ), wp_date( 'M j, Y', $pushed ) ) : __( 'This site is using the rate copy shipped in the plugin. Opt in on Rates if you want signed updates.', 'bomcas-tax-calculators-canada' ) ) . '</p>';
	if ( $conflicts ) {
		/* translators: %d: number of rate overrides that differ from the package. */
		echo '<p>' . esc_html( sprintf( __( '%d override still differs from the latest package.', 'bomcas-tax-calculators-canada' ), count( $conflicts ) ) ) . '</p>';
	}
	echo '</section></div>';
	echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Quick actions', 'bomcas-tax-calculators-canada' ) . '</h2><p class="bomcas-buttongroup">';
	echo '<a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=bomcas-account' ) ) . '">' . esc_html__( 'Account and upgrade', 'bomcas-tax-calculators-canada' ) . '</a>';
	echo '<a class="button" href="' . esc_url( admin_url( 'options-general.php?page=bomcas-rates' ) ) . '">' . esc_html__( 'Rates and overrides', 'bomcas-tax-calculators-canada' ) . '</a>';
	echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=bomcas-toggles' ) ) . '">' . esc_html__( 'Country and tool toggles', 'bomcas-tax-calculators-canada' ) . '</a>';
	echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=bomcas-leads' ) ) . '">' . esc_html__( 'Leads', 'bomcas-tax-calculators-canada' ) . '</a>';
	echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=bomcas-page-content' ) ) . '">' . esc_html__( 'Page content', 'bomcas-tax-calculators-canada' ) . '</a>';
	echo '</p></section>';
	Bomcas_Admin_UI::close();
}

/**
 * Country and tool switches.
 */
function bomcas_calc_render_toggles() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['bomcas_toggles'] ) && check_admin_referer( 'bomcas_calc_toggles' ) ) {
		$countries = array(
			'CA' => empty( $_POST['country_ca'] ) ? 0 : 1,
		);
		$disabled  = array();
		foreach ( array( 'sales-tax', 'income-tax', 'mortgage-payment' ) as $id ) {
			if ( empty( $_POST[ 'tool_' . $id ] ) ) {
				$disabled[] = $id;
			}
		}
		update_option( 'bomcas_calc_countries', $countries, false );
		update_option( 'bomcas_calc_disabled_tools', $disabled, false );
		wp_safe_redirect( admin_url( 'admin.php?page=bomcas-toggles&saved=1' ) );
		exit;
	}
	$countries = get_option(
		'bomcas_calc_countries',
		array(
			'CA' => 1,
		)
	);
	$disabled  = (array) get_option( 'bomcas_calc_disabled_tools', array() );
	Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
	Bomcas_Admin_UI::open(
		array(
			'doc'     => 'toggles',
			'product' => __( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ),
			'kicker'  => __( 'Toggles · version ', 'bomcas-tax-calculators-canada' ) . BOMCAS_CALC_VERSION,
			'badge'   => __( 'Lite', 'bomcas-tax-calculators-canada' ),
			'tabs'    => bomcas_calc_buyer_tabs( 'toggles' ),
		)
	);
	echo '<section class="bomcas-admin__card"><h2>' . esc_html__( 'Countries and tools', 'bomcas-tax-calculators-canada' ) . '</h2>';
	echo '<p class="desc">' . esc_html__( 'Turn off a country or a tool. It leaves the hub, menus, shortcodes and the sitemap.', 'bomcas-tax-calculators-canada' ) . '</p>';
	echo '<form method="post">';
	wp_nonce_field( 'bomcas_calc_toggles' );
	echo '<input type="hidden" name="bomcas_toggles" value="1" />';
	echo '<label class="bomcas-toggle"><input type="checkbox" name="country_ca" value="1" ' . checked( ! empty( $countries['CA'] ), true, false ) . ' /><i></i> ' . esc_html__( 'Canada', 'bomcas-tax-calculators-canada' ) . '</label>';
	foreach ( array(
		'sales-tax'        => 'Sales tax',
		'income-tax'       => 'Income tax',
		'mortgage-payment' => 'Mortgage payment',
	) as $id => $label ) {
		echo '<label class="bomcas-toggle"><input type="checkbox" name="tool_' . esc_attr( $id ) . '" value="1" ' . checked( ! in_array( $id, $disabled, true ), true, false ) . ' /><i></i> ' . esc_html( $label ) . '</label>';
	}
	echo '<div class="bomcas-savebar">';
	submit_button( __( 'Save toggles', 'bomcas-tax-calculators-canada' ) );
	echo '</div></form></section>';
	Bomcas_Admin_UI::close();
}

/**
 * Save the credit opt-in.
 */
function bomcas_calc_hide_pro_note() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You cannot change this setting.', 'bomcas-tax-calculators-canada' ) );
	}
	check_admin_referer( 'bomcas_calc_hide_pro_note' );
	update_user_meta( get_current_user_id(), 'bomcas_hide_pro_note', 1 );
	wp_safe_redirect( admin_url( 'options-general.php?page=bomcas-calculators' ) );
	exit;
}
add_action( 'admin_post_bomcas_calc_hide_pro_note', 'bomcas_calc_hide_pro_note' );

function bomcas_calc_save_credit() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You cannot change this setting.', 'bomcas-tax-calculators-canada' ) );
	}
	check_admin_referer( 'bomcas_calc_save_credit' );
	update_option( 'bomcas_calc_show_credit', empty( $_POST['bomcas_calc_show_credit'] ) ? 0 : 1 );
	wp_safe_redirect( admin_url( 'options-general.php?page=bomcas-calculators&updated=1' ) );
	exit;
}

register_uninstall_hook( __FILE__, 'bomcas_calc_uninstall' );

/**
 * Remove Lite options only. Do not touch Pro or commerce tables.
 */
function bomcas_calc_uninstall() {
	delete_option( 'bomcas_calc_show_credit' );
}
