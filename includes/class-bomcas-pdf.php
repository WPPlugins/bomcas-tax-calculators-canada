<?php
/**
 * Small PDF writer. GPL-2.0-or-later, no remote service, no macros.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Text PDF with an optional coloured header and a JPEG logo.
 */
class Bomcas_Pdf {

	/**
	 * @param string $title    Document title.
	 * @param array  $lines    Lines of text.
	 * @param array  $brand    name, contact, colour, disclaimer, logo_id, full.
	 * @return string
	 */
	public static function document( $title, $lines, $brand = array() ) {
		$full      = ! empty( $brand['full'] );
		$name      = isset( $brand['name'] ) ? (string) $brand['name'] : 'BOMCAS Canada';
		$contact   = isset( $brand['contact'] ) ? (string) $brand['contact'] : '';
		$note      = isset( $brand['disclaimer'] ) ? (string) $brand['disclaimer'] : 'Estimate only. Not a tax return.';
		$colour    = self::rgb( isset( $brand['colour'] ) ? $brand['colour'] : '#001729' );
		$logo      = $full ? self::jpeg( isset( $brand['logo_id'] ) ? (int) $brand['logo_id'] : 0 ) : null;
		$objects   = array();
		$objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
		$objects[] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
		$resources = '<< /Font << /F1 5 0 R >>' . ( $logo ? ' /XObject << /Im1 6 0 R >>' : '' ) . ' >>';
		$objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources ' . $resources . ' >>';
		$stream    = self::stream( $title, $lines, $name, $contact, $note, $colour, (bool) $logo, $full );
		$objects[] = '<< /Length ' . strlen( $stream ) . " >>\nstream\n" . $stream . "\nendstream";
		$objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
		if ( $logo ) {
			$objects[] = '<< /Type /XObject /Subtype /Image /Width ' . (int) $logo['w'] . ' /Height ' . (int) $logo['h'] . ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ' . strlen( $logo['bytes'] ) . " >>\nstream\n" . $logo['bytes'] . "\nendstream";
		}
		$pdf     = "%PDF-1.4\n";
		$offsets = array( 0 );
		foreach ( $objects as $i => $body ) {
			$offsets[] = strlen( $pdf );
			$pdf      .= ( $i + 1 ) . " 0 obj\n" . $body . "\nendobj\n";
		}
		$xref  = strlen( $pdf );
		$count = count( $objects ) + 1;
		$pdf  .= "xref\n0 $count\n0000000000 65535 f \n";
		for ( $i = 1; $i < $count; $i++ ) {
			$pdf .= sprintf( "%010d 00000 n \n", $offsets[ $i ] );
		}
		$pdf .= "trailer << /Size $count /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";
		return $pdf;
	}

	/**
	 * @param string $title Title.
	 * @param array  $lines Lines.
	 * @param string $name Firm.
	 * @param string $contact Contact.
	 * @param string $note Disclaimer.
	 * @param array  $rgb Colour.
	 * @param bool   $logo Has a logo.
	 * @param bool   $full Branded.
	 * @return string
	 */
	private static function stream( $title, $lines, $name, $contact, $note, $rgb, $logo, $full ) {
		$ops  = 'q ' . $rgb . " rg 0 752 612 40 re f Q\n";
		$ops .= 'BT /F1 14 Tf 1 1 1 rg 36 766 Td (' . self::text( $full ? $name : 'BOMCAS Canada' ) . ") Tj ET\n";
		if ( $logo ) {
			$ops .= "q 72 0 0 28 470 756 cm /Im1 Do Q\n";
		}
		$ops .= 'BT /F1 16 Tf 0 0 0 rg 36 724 Td (' . self::text( $title ) . ") Tj ET\n";
		$y    = 700;
		$bar  = 0;
		foreach ( $lines as $line ) {
			if ( $y < 72 ) {
				break;
			}
			$clean = self::text( $line );
			if ( 0 === strpos( (string) $line, '|' ) ) {
				$width = max( 8, min( 240, (int) substr( (string) $line, 1, 3 ) ) );
				$ops  .= 'q 0 0.57 0.86 rg 36 ' . ( $y - 2 ) . " $width 8 re f Q\n";
				$clean = self::text( substr( (string) $line, 4 ) );
			}
			$ops .= "BT /F1 11 Tf 0 0 0 rg 36 $y Td (" . $clean . ") Tj ET\n";
			$y   -= 16;
			++$bar;
		}
		if ( $full && '' !== $contact ) {
			$ops .= "BT /F1 10 Tf 36 $y Td (" . self::text( $contact ) . ") Tj ET\n";
			$y   -= 16;
		}
		$ops .= 'BT /F1 9 Tf 36 ' . max( 48, $y ) . ' Td (' . self::text( $note ) . ") Tj ET\n";
		if ( ! $full ) {
			$ops .= 'BT /F1 9 Tf 36 40 Td (Basic report from BOMCAS Canada calculators.) Tj ET' . "\n";
		}
		unset( $bar );
		return $ops;
	}

	/**
	 * @param string $hex Colour.
	 * @return string
	 */
	private static function rgb( $hex ) {
		$hex = ltrim( (string) $hex, '#' );
		if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
			$hex = '001729';
		}
		return sprintf( '%.3f %.3f %.3f', hexdec( substr( $hex, 0, 2 ) ) / 255, hexdec( substr( $hex, 2, 2 ) ) / 255, hexdec( substr( $hex, 4, 2 ) ) / 255 );
	}

	/**
	 * @param string $value Text.
	 * @return string
	 */
	private static function text( $value ) {
		$value = function_exists( 'remove_accents' ) ? remove_accents( (string) $value ) : (string) $value;
		$value = preg_replace( '/[^\x20-\x7E]/', '', $value );
		return str_replace( array( '\\', '(', ')' ), array( '\\\\', '\\(', '\\)' ), $value );
	}

	/**
	 * @param int $attachment_id Attachment.
	 * @return array|null
	 */
	private static function jpeg( $attachment_id ) {
		if ( $attachment_id < 1 || ! function_exists( 'get_attached_file' ) ) {
			return null;
		}
		$path = get_attached_file( $attachment_id );
		if ( ! is_string( $path ) || ! is_readable( $path ) ) {
			return null;
		}
		$bytes = file_get_contents( $path );
		if ( ! is_string( $bytes ) ) {
			return null;
		}
		if ( ! preg_match( '/\.jpe?g$/i', $path ) && function_exists( 'imagecreatefromstring' ) ) {
			$image = imagecreatefromstring( $bytes );
			if ( ! $image ) {
				return null;
			}
			ob_start();
			imagejpeg( $image, null, 80 );
			$bytes = ob_get_clean();
			imagedestroy( $image );
		} elseif ( ! preg_match( '/\.jpe?g$/i', $path ) ) {
			return null;
		}
		$info = function_exists( 'getimagesizefromstring' ) ? getimagesizefromstring( $bytes ) : false;
		if ( ! $info ) {
			return null;
		}
		return array(
			'bytes' => $bytes,
			'w'     => (int) $info[0],
			'h'     => (int) $info[1],
		);
	}
}
