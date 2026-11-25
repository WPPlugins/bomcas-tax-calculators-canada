<?php
/**
 * Small xlsx reader and writer. No macros. GPL-compatible, no remote service.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Spreadsheet helper.
 */
class Bomcas_Xlsx {

	/**
	 * @param string $path Path.
	 * @return array<int,array<int,string>>|null
	 */
	public static function read( $path ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			return null;
		}
		$zip = new ZipArchive();
		if ( true !== $zip->open( $path ) ) {
			return null;
		}
		if ( $zip->locateName( 'xl/vbaProject.bin' ) ) {
			$zip->close();
			return null;
		}
		$sheet  = $zip->getFromName( 'xl/worksheets/sheet1.xml' );
		$shared = $zip->getFromName( 'xl/sharedStrings.xml' );
		$zip->close();
		if ( ! is_string( $sheet ) ) {
			return null;
		}
		$strings = array();
		if ( is_string( $shared ) ) {
			$sx = simplexml_load_string( $shared, 'SimpleXMLElement', LIBXML_NONET );
			if ( $sx ) {
				foreach ( $sx->si as $si ) {
					$strings[] = (string) $si->t;
				}
			}
		}
		$xml = simplexml_load_string( $sheet, 'SimpleXMLElement', LIBXML_NONET );
		if ( ! $xml ) {
			return null;
		}
		$rows = array();
		foreach ( $xml->sheetData->row as $row ) {
			$cells = array();
			foreach ( $row->c as $cell ) {
				$type = (string) $cell['t'];
				if ( 'inlineStr' === $type ) {
					$cells[] = (string) $cell->is->t;
				} elseif ( 's' === $type ) {
					$cells[] = $strings[ (int) $cell->v ] ?? '';
				} else {
					$cells[] = (string) $cell->v;
				}
			}
			if ( $cells ) {
				$rows[] = $cells;
			}
		}
		return $rows;
	}

	/**
	 * @param string $path   Destination.
	 * @param array  $sheets Name => rows. First sheet is the data. Second sheet is instructions when present.
	 * @return bool
	 */
	public static function write( $path, $sheets ) {
		if ( ! class_exists( 'ZipArchive' ) || ! $sheets ) {
			return false;
		}
		$zip = new ZipArchive();
		if ( true !== $zip->open( $path, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
			return false;
		}
		$names = array_keys( $sheets );
		$zip->addFromString( '[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' . self::sheet_types( count( $names ) ) . '</Types>' );
		$zip->addFromString( '_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>' );
		$workbook = '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>';
		$rels     = '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
		$i        = 1;
		foreach ( $names as $name ) {
			$workbook .= '<sheet name="' . self::xml( $name ) . '" sheetId="' . $i . '" r:id="rId' . $i . '"/>';
			$rels     .= '<Relationship Id="rId' . $i . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $i . '.xml"/>';
			$zip->addFromString( 'xl/worksheets/sheet' . $i . '.xml', self::sheet_xml( $sheets[ $name ] ) );
			++$i;
		}
		$workbook .= '</sheets></workbook>';
		$rels     .= '</Relationships>';
		$zip->addFromString( 'xl/workbook.xml', $workbook );
		$zip->addFromString( 'xl/_rels/workbook.xml.rels', $rels );
		$zip->close();
		return true;
	}

	/**
	 * @param int $count Sheets.
	 * @return string
	 */
	private static function sheet_types( $count ) {
		$out = '';
		for ( $i = 1; $i <= $count; $i++ ) {
			$out .= '<Override PartName="/xl/worksheets/sheet' . $i . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
		}
		return $out;
	}

	/**
	 * @param array $rows Rows.
	 * @return string
	 */
	private static function sheet_xml( $rows ) {
		$xml = '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
		$r   = 1;
		foreach ( $rows as $row ) {
			$xml .= '<row r="' . $r . '">';
			$c    = 0;
			foreach ( $row as $value ) {
				$col  = chr( 65 + $c );
				$xml .= '<c r="' . $col . $r . '" t="inlineStr"><is><t>' . self::xml( $value ) . '</t></is></c>';
				++$c;
			}
			$xml .= '</row>';
			++$r;
		}
		return $xml . '</sheetData></worksheet>';
	}

	/**
	 * @param mixed $value Value.
	 * @return string
	 */
	private static function xml( $value ) {
		return htmlspecialchars( (string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8' );
	}
}
