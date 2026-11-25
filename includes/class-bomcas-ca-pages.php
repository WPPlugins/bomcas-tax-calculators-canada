<?php
/**
 * Authored Canadian guides. Numbers stay as tokens until the page is viewed.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads content/ca markdown. There is no sentence generator.
 */
class Bomcas_Ca_Pages {

	/**
	 * @param string $tool Tool id.
	 * @param string $code Province code.
	 * @return string
	 */
	public static function place( $tool, $code ) {
		$slug = self::slug( $code );
		return self::file( 'content/ca/' . $tool . '/' . $slug . '.md' );
	}

	/**
	 * @param string $slug Page slug.
	 * @return string
	 */
	public static function topic( $slug ) {
		return self::file( 'content/ca/topics/' . $slug . '.md' );
	}

	/**
	 * @param string $slug Hub slug.
	 * @return string
	 */
	public static function hub( $slug ) {
		return self::file( 'content/ca/hubs/' . $slug . '.md' );
	}

	/**
	 * Excerpt from the content file for a managed page, still containing tokens.
	 *
	 * @param int $id Page id.
	 * @return string
	 */
	public static function excerpt( $id ) {
		$path = self::path_for_page( $id );
		if ( '' === $path ) {
			return '';
		}
		$raw = self::raw( $path );
		if ( preg_match( '/^---\s*\n(.*?)\n---\s*\n/s', $raw, $m ) && preg_match( '/^excerpt:\s*(.+)$/m', $m[1], $ex ) ) {
			return trim( $ex[1], " \t\"'" );
		}
		return '';
	}

	/**
	 * @param string $code Province code.
	 * @return string
	 */
	public static function slug( $code ) {
		$names = array(
			'AB' => 'alberta',
			'BC' => 'british-columbia',
			'MB' => 'manitoba',
			'NB' => 'new-brunswick',
			'NL' => 'newfoundland-and-labrador',
			'NT' => 'northwest-territories',
			'NS' => 'nova-scotia',
			'NU' => 'nunavut',
			'ON' => 'ontario',
			'PE' => 'prince-edward-island',
			'QC' => 'quebec',
			'SK' => 'saskatchewan',
			'YT' => 'yukon',
		);
		$code  = strtoupper( $code );
		return isset( $names[ $code ] ) ? $names[ $code ] : '';
	}

	/**
	 * @param int $id Page id.
	 * @return string Relative path or empty.
	 */
	public static function path_for_page( $id ) {
		$tool  = (string) get_post_meta( $id, '_bomcas_calc_tool', true );
		$place = (string) get_post_meta( $id, '_bomcas_calc_place_name', true );
		$slug  = (string) get_post_field( 'post_name', $id );
		if ( '' !== $place && 0 !== strpos( $tool, 'us-' ) ) {
			$code = self::code( $place );
			$file = 'content/ca/' . $tool . '/' . self::slug( $code ) . '.md';
			return is_readable( BOMCAS_CALC_DIR . $file ) ? $file : '';
		}
		if ( is_readable( BOMCAS_CALC_DIR . 'content/ca/topics/' . $slug . '.md' ) ) {
			return 'content/ca/topics/' . $slug . '.md';
		}
		if ( is_readable( BOMCAS_CALC_DIR . 'content/ca/hubs/' . $slug . '.md' ) ) {
			return 'content/ca/hubs/' . $slug . '.md';
		}
		return '';
	}

	/**
	 * @param string $place Place name.
	 * @return string
	 */
	private static function code( $place ) {
		$map = array(
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
		return isset( $map[ $place ] ) ? $map[ $place ] : '';
	}

	/**
	 * @param string $relative Path under the plugin.
	 * @return string
	 */
	private static function file( $relative ) {
		$raw = self::raw( $relative );
		if ( '' === $raw ) {
			return '';
		}
		$body = preg_replace( '/^---\s*\n.*?\n---\s*\n/s', '', $raw );
		$html = self::markdown( (string) $body );
		return class_exists( 'Bomcas_Tokens' ) ? Bomcas_Tokens::replace( $html ) : $html;
	}

	/**
	 * @param string $relative Path.
	 * @return string
	 */
	private static function raw( $relative ) {
		$path = BOMCAS_CALC_DIR . $relative;
		if ( ! is_readable( $path ) ) {
			return '';
		}
		$raw = file_get_contents( $path );
		return is_string( $raw ) ? $raw : '';
	}

	/**
	 * A small markdown subset: headings, paragraphs, lists, and links.
	 *
	 * @param string $md Markdown.
	 * @return string
	 */
	private static function markdown( $md ) {
		$html  = '<section class="bomcas-guide">';
		$para  = array();
		$list  = array();
		$flush = static function () use ( &$para, &$list, &$html ) {
			if ( $list ) {
				$html .= '<ul>';
				foreach ( $list as $item ) {
					$html .= '<li>' . self::inline( $item ) . '</li>';
				}
				$html .= '</ul>';
				$list  = array();
			}
			if ( $para ) {
				$html .= '<p>' . self::inline( implode( ' ', $para ) ) . '</p>';
				$para  = array();
			}
		};
		foreach ( preg_split( '/\r\n|\n|\r/', $md ) as $line ) {
			$trim = trim( $line );
			if ( '' === $trim ) {
				$flush();
				continue;
			}
			if ( 0 === strpos( $trim, '### ' ) ) {
				$flush();
				$html .= '<h3>' . self::inline( substr( $trim, 4 ) ) . '</h3>';
				continue;
			}
			if ( 0 === strpos( $trim, '## ' ) ) {
				$flush();
				$html .= '<h2>' . self::inline( substr( $trim, 3 ) ) . '</h2>';
				continue;
			}
			if ( 0 === strpos( $trim, '- ' ) ) {
				if ( $para ) {
					$html .= '<p>' . self::inline( implode( ' ', $para ) ) . '</p>';
					$para  = array();
				}
				$list[] = substr( $trim, 2 );
				continue;
			}
			if ( $list ) {
				$html .= '<ul>';
				foreach ( $list as $item ) {
					$html .= '<li>' . self::inline( $item ) . '</li>';
				}
				$html .= '</ul>';
				$list  = array();
			}
			$para[] = $trim;
		}
		$flush();
		$html = preg_replace_callback(
			'#<h3>(.*?)</h3>\s*<p>(.*?)</p>#s',
			static function ( $match ) {
				return '<details class="faq-item"><summary>' . $match[1] . '</summary><div class="faq-body"><p>' . $match[2] . '</p></div></details>';
			},
			$html
		);
		return $html . '</section>';
	}

	/**
	 * @param string $text Inline markdown.
	 * @return string
	 */
	private static function inline( $text ) {
		$parts = preg_split( '/(\[[^\]]+\]\(https?:\/\/[^)\s]+\))/', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
		$out   = '';
		foreach ( (array) $parts as $part ) {
			if ( preg_match( '/^\[([^\]]+)\]\((https?:\/\/[^)\s]+)\)$/', $part, $m ) ) {
				$out .= '<a href="' . esc_url( $m[2] ) . '">' . esc_html( $m[1] ) . '</a>';
			} else {
				$out .= esc_html( $part );
			}
		}
		return $out;
	}
}
