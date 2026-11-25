<?php
/**
 * Layered rate values: customer override, then a signed push, then the bundled seed.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolver. The pure methods do not read the database.
 */
class Bomcas_Rates {

	/**
	 * Replace leaves in $base with leaves from $overlay. Lists are replaced as a whole.
	 *
	 * @param mixed $base    Bundled value.
	 * @param mixed $overlay Pushed value.
	 * @return mixed
	 */
	public static function merge_replace( $base, $overlay ) {
		if ( ! is_array( $overlay ) ) {
			return $overlay;
		}
		if ( ! is_array( $base ) || self::is_list( $overlay ) || self::is_list( $base ) ) {
			return $overlay;
		}
		$out = $base;
		foreach ( $overlay as $key => $value ) {
			$out[ $key ] = array_key_exists( $key, $out ) ? self::merge_replace( $out[ $key ], $value ) : $value;
		}
		return $out;
	}

	/**
	 * Apply one file's overrides on top of the system value.
	 *
	 * @param mixed  $system    Value after the push is merged.
	 * @param string $file      Seed file name.
	 * @param array  $overrides Override rows.
	 * @return array{data:mixed,conflicts:array}
	 */
	public static function apply_overrides( $system, $file, $overrides ) {
		$out       = $system;
		$conflicts = array();
		foreach ( $overrides as $row ) {
			if ( ! is_array( $row ) || ( $row['file'] ?? '' ) !== $file || empty( $row['path'] ) ) {
				continue;
			}
			$current = self::get_path( $system, $row['path'] );
			if ( self::norm( $current ) !== self::norm( $row['base'] ?? null ) ) {
				$conflicts[] = array(
					'file'     => $file,
					'path'     => $row['path'],
					'from'     => $row['base'] ?? null,
					'to'       => $current,
					'override' => $row['value'] ?? null,
				);
			}
			$out = self::set_path( $out, $row['path'], $row['value'] ?? null );
		}
		return array(
			'data'      => $out,
			'conflicts' => $conflicts,
		);
	}

	/**
	 * Resolve one bundled file.
	 *
	 * @param string $file    File name.
	 * @param mixed  $bundled Decoded seed.
	 * @param array  $pushed  Pushed package or empty.
	 * @param array  $overrides Override rows.
	 * @return array{data:mixed,conflicts:array}
	 */
	public static function resolve( $file, $bundled, $pushed, $overrides ) {
		$system = $bundled;
		if ( is_array( $pushed ) && isset( $pushed['files'][ $file ] ) && is_array( $pushed['files'][ $file ] ) ) {
			$system = self::merge_replace( $bundled, $pushed['files'][ $file ] );
		}
		return self::apply_overrides( $system, $file, $overrides );
	}

	/**
	 * Sign a JSON body. Returns base64 or an empty string when OpenSSL cannot sign.
	 *
	 * @param string $body    Canonical JSON.
	 * @param string $private PEM private key.
	 * @return string
	 */
	public static function sign( $body, $private ) {
		$sig = '';
		if ( '' === $private || ! function_exists( 'openssl_sign' ) ) {
			return '';
		}
		$ok = openssl_sign( $body, $sig, $private, OPENSSL_ALGO_SHA256 );
		return $ok ? base64_encode( $sig ) : ''; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Encodes a binary rate-file signature for transport. Not code.
	}

	/**
	 * Verify a detached signature. A missing key or a mismatch is a failure.
	 *
	 * @param string $body   Canonical JSON.
	 * @param string $sig    Base64 signature.
	 * @param string $public PEM public key.
	 * @return bool
	 */
	public static function verify( $body, $sig, $public ) {
		if ( '' === $body || '' === $sig || '' === $public || ! function_exists( 'openssl_verify' ) ) {
			return false;
		}
		$raw = base64_decode( $sig, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Decodes the rate-file signature header before OpenSSL verification. Not code.
		if ( false === $raw ) {
			return false;
		}
		return 1 === openssl_verify( $body, $raw, $public, OPENSSL_ALGO_SHA256 );
	}

	/**
	 * Numeric leaves, using slash paths.
	 *
	 * @param mixed  $data   Tree.
	 * @param string $prefix Path so far.
	 * @return array<int,array{path:string,value:mixed}>
	 */
	public static function leaves( $data, $prefix = '' ) {
		$rows = array();
		if ( ! is_array( $data ) ) {
			return $rows;
		}
		foreach ( $data as $key => $value ) {
			$path = '' === $prefix ? (string) $key : $prefix . '/' . $key;
			if ( is_array( $value ) ) {
				$rows = array_merge( $rows, self::leaves( $value, $path ) );
			} elseif ( is_int( $value ) || is_float( $value ) || ( is_string( $value ) && is_numeric( $value ) ) ) {
				$rows[] = array(
					'path'  => $path,
					'value' => $value,
				);
			}
		}
		return $rows;
	}

	/**
	 * Bracket order and rate range checks.
	 *
	 * @param mixed  $data Tree.
	 * @param string $path Path.
	 * @return string[]
	 */
	public static function validate_tree( $data, $path = '' ) {
		$errors = array();
		if ( ! is_array( $data ) ) {
			return $errors;
		}
		if ( isset( $data['brackets'] ) && is_array( $data['brackets'] ) ) {
			$prev = null;
			foreach ( $data['brackets'] as $i => $bracket ) {
				if ( ! is_array( $bracket ) || ! isset( $bracket['rate'] ) ) {
					$errors[] = $path . '/brackets/' . $i . ' missing rate';
					continue;
				}
				$rate = $bracket['rate'];
				if ( ! is_numeric( $rate ) || $rate < 0 || $rate > 0.6 ) {
					$errors[] = $path . '/brackets/' . $i . ' rate out of range';
				}
				if ( null !== $bracket['upto'] && '' !== $bracket['upto'] ) {
					if ( ! is_numeric( $bracket['upto'] ) || $bracket['upto'] < 0 ) {
						$errors[] = $path . '/brackets/' . $i . ' threshold out of range';
					} elseif ( null !== $prev && (float) $bracket['upto'] <= (float) $prev ) {
						$errors[] = $path . '/brackets/' . $i . ' thresholds are not increasing';
					}
					$prev = $bracket['upto'];
				}
			}
		}
		foreach ( $data as $key => $value ) {
			$next = '' === $path ? (string) $key : $path . '/' . $key;
			if ( in_array( (string) $key, array( 'rate', 'gst', 'pst', 'hst', 'qst' ), true ) && is_numeric( $value ) ) {
				if ( $value < 0 || $value > 0.6 ) {
					$errors[] = $next . ' out of range';
				}
			} elseif ( is_numeric( $value ) && ! is_string( $key ) ) {
				continue;
			} elseif ( is_numeric( $value ) && $value < 0 ) {
				$errors[] = $next . ' is negative';
			}
			if ( is_array( $value ) ) {
				$errors = array_merge( $errors, self::validate_tree( $value, $next ) );
			}
		}
		return $errors;
	}

	/**
	 * Leaf differences.
	 *
	 * @param mixed  $before Tree.
	 * @param mixed  $after  Tree.
	 * @param string $path   Path.
	 * @return array<int,array{path:string,from:mixed,to:mixed}>
	 */
	public static function diff_leaves( $before, $after, $path = '' ) {
		$diffs = array();
		$left  = array();
		$right = array();
		foreach ( self::leaves( $before, $path ) as $row ) {
			$left[ $row['path'] ] = $row['value'];
		}
		foreach ( self::leaves( $after, $path ) as $row ) {
			$right[ $row['path'] ] = $row['value'];
		}
		foreach ( array_unique( array_merge( array_keys( $left ), array_keys( $right ) ) ) as $key ) {
			$from = $left[ $key ] ?? null;
			$to   = $right[ $key ] ?? null;
			if ( self::norm( $from ) !== self::norm( $to ) ) {
				$diffs[] = array(
					'path' => $key,
					'from' => $from,
					'to'   => $to,
				);
			}
		}
		return $diffs;
	}

	/**
	 * @param mixed  $data Tree.
	 * @param string $path Slash path.
	 * @return mixed
	 */
	public static function get_path( $data, $path ) {
		$node = $data;
		foreach ( explode( '/', (string) $path ) as $part ) {
			if ( ! is_array( $node ) || ! array_key_exists( $part, $node ) ) {
				return null;
			}
			$node = $node[ $part ];
		}
		return $node;
	}

	/**
	 * @param mixed  $data  Tree.
	 * @param string $path  Slash path.
	 * @param mixed  $value Value.
	 * @return mixed
	 */
	public static function set_path( $data, $path, $value ) {
		$parts = explode( '/', (string) $path );
		if ( ! is_array( $data ) ) {
			$data = array();
		}
		$node = &$data;
		foreach ( $parts as $i => $part ) {
			if ( $i === count( $parts ) - 1 ) {
				$node[ $part ] = $value;
				break;
			}
			if ( ! isset( $node[ $part ] ) || ! is_array( $node[ $part ] ) ) {
				$node[ $part ] = array();
			}
			$node = &$node[ $part ];
		}
		return $data;
	}

	/**
	 * @param mixed $value Value.
	 * @return string
	 */
	public static function norm( $value ) {
		if ( null === $value || '' === $value ) {
			return '';
		}
		if ( is_numeric( $value ) ) {
			return (string) ( 0 + $value );
		}
		return (string) $value;
	}

	/**
	 * @param array $data Array.
	 * @return bool
	 */
	public static function is_list( $data ) {
		if ( array() === $data ) {
			return true;
		}
		return array_keys( $data ) === range( 0, count( $data ) - 1 );
	}

	/**
	 * Database-backed resolve for a seed file.
	 *
	 * @param string $file    File name.
	 * @param mixed  $bundled Decoded JSON.
	 * @return mixed
	 */
	public static function apply_dataset( $file, $bundled ) {
		if ( ! function_exists( 'get_option' ) ) {
			return $bundled;
		}
		$pushed = get_option( 'bomcas_rates_pushed', array() );
		if ( is_array( $pushed ) && $pushed && ! self::package_usable( $pushed ) ) {
			self::rollback();
			$pushed = array();
		}
		$overrides = get_option( 'bomcas_rates_overrides', array() );
		$resolved  = self::resolve( $file, $bundled, is_array( $pushed ) ? $pushed : array(), is_array( $overrides ) ? $overrides : array() );
		return $resolved['data'];
	}

	/**
	 * Conflicts for the rates screen.
	 *
	 * @return array
	 */
	public static function conflicts() {
		if ( ! function_exists( 'get_option' ) ) {
			return array();
		}
		$pushed    = get_option( 'bomcas_rates_pushed', array() );
		$overrides = get_option( 'bomcas_rates_overrides', array() );
		$all       = array();
		foreach ( self::bundled_files() as $file => $path ) {
			$raw = is_readable( $path ) ? json_decode( file_get_contents( $path ), true ) : null;
			if ( ! is_array( $raw ) ) {
				continue;
			}
			$resolved = self::resolve( $file, $raw, is_array( $pushed ) ? $pushed : array(), is_array( $overrides ) ? $overrides : array() );
			$all      = array_merge( $all, $resolved['conflicts'] );
		}
		return $all;
	}

	/**
	 * Seed files this install can resolve.
	 *
	 * @return array<string,string>
	 */
	public static function bundled_files() {
		$files = array();
		if ( defined( 'BOMCAS_CALC_DIR' ) ) {
			foreach ( array( 'sales-tax.json', 'personal-tax-current.json' ) as $name ) {
				$files[ $name ] = BOMCAS_CALC_DIR . 'seed/' . $name;
			}
		}
		if ( defined( 'BOMCAS_PRO_DIR' ) ) {
			foreach ( array( 'personal-tax-history.json', 'payroll-constants.json', 'household.json' ) as $name ) {
				$files[ $name ] = BOMCAS_PRO_DIR . 'seed/' . $name;
			}
		}
		return $files;
	}

	/**
	 * A viewer may open rate tools. Hidden sites limit that to the agency allow-list.
	 *
	 * @return bool
	 */
	public static function user_may_manage() {
		if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		$privacy = function_exists( 'get_option' ) ? get_option( 'bomcas_calc_site_privacy', array() ) : array();
		$privacy = is_array( $privacy ) ? $privacy : array();
		$forced  = defined( 'BOMCAS_CALC_HIDE_LICENSE' ) && BOMCAS_CALC_HIDE_LICENSE;
		if ( ! $forced && empty( $privacy['hide'] ) ) {
			return true;
		}
		$ids = array_map( 'intval', (array) ( $privacy['users'] ?? array() ) );
		if ( in_array( (int) get_current_user_id(), $ids, true ) ) {
			return true;
		}
		$cap = isset( $privacy['cap'] ) ? (string) $privacy['cap'] : '';
		return '' !== $cap && current_user_can( $cap );
	}

	/**
	 * Pull a signed package. Lite does this only after an explicit opt-in.
	 *
	 * @return array
	 */
	public static function pull() {
		$pro = defined( 'BOMCAS_PRO_VERSION' );
		$opt = function_exists( 'get_option' ) && '1' === (string) get_option( 'bomcas_rates_optin', '0' );
		if ( ! $pro && ! $opt ) {
			return self::pull_result( false, 'opt-in required' );
		}
		$key = $pro ? (string) get_option( 'bomcas_pro_license_key', '' ) : '';
		if ( $pro && '' === $key ) {
			return self::pull_result( false, 'licence required' );
		}
		$url = (string) get_option( 'bomcas_rates_server', '' );
		if ( '' === $url ) {
			$url = 'https://accountantsexpert.com/wp-json/bomcas-licensing/v1/rates';
		}
		$public = (string) get_option( 'bomcas_rates_public_key', '' );
		if ( '' === $public ) {
			$public = self::pin_public_key( $url );
		}
		if ( '' === $public ) {
			return self::pull_result( false, 'public key is empty' );
		}
		$headers = array( 'Accept' => 'application/json' );
		if ( '' !== $key ) {
			$headers['X-Bomcas-Licence'] = $key;
		}
		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 15,
				'headers' => $headers,
			)
		);
		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return self::pull_result( false, 'request failed' );
		}
		$body = wp_remote_retrieve_body( $response );
		$sig  = (string) wp_remote_retrieve_header( $response, 'x-bomcas-rates-signature' );
		$sum  = (string) wp_remote_retrieve_header( $response, 'x-bomcas-rates-sha256' );
		if ( '' === $sum || ! hash_equals( $sum, hash( 'sha256', $body ) ) || ! self::verify( $body, $sig, $public ) ) {
			self::rollback();
			return self::pull_result( false, 'signature or checksum rejected' );
		}
		$decoded = json_decode( $body, true );
		if ( ! is_array( $decoded ) || empty( $decoded['files'] ) || empty( $decoded['version'] ) || ! self::package_usable( $decoded ) ) {
			self::rollback();
			return self::pull_result( false, 'incomplete package' );
		}
		update_option( 'bomcas_rates_pushed', $decoded, false );
		update_option( 'bomcas_rates_pushed_at', time(), false );
		return self::pull_result( true, '', (string) $decoded['version'] );
	}

	/**
	 * @param bool   $ok      Success.
	 * @param string $error   Error code.
	 * @param string $version Version.
	 * @return array
	 */
	private static function pull_result( $ok, $error, $version = '' ) {
		update_option(
			'bomcas_rates_pull_status',
			array(
				'ok'      => (bool) $ok,
				'error'   => $error,
				'version' => $version,
				'at'      => time(),
			),
			false
		);
		return array(
			'ok'      => (bool) $ok,
			'error'   => $error,
			'version' => $version,
		);
	}

	/**
	 * Drop a saved pack so calculators return to the files shipped in the plugin.
	 */
	public static function rollback() {
		delete_option( 'bomcas_rates_pushed' );
	}

	/**
	 * @param array $package Package.
	 * @return bool
	 */
	public static function package_usable( $package ) {
		if ( empty( $package['files'] ) || ! is_array( $package['files'] ) ) {
			return false;
		}
		foreach ( $package['files'] as $file => $data ) {
			if ( ! is_string( $file ) || ! is_array( $data ) ) {
				return false;
			}
			$errors = self::validate_tree( $data );
			if ( $errors ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Pin the publisher public key from the same host as the rate server.
	 *
	 * @param string $rates_url Rate package URL.
	 * @return string
	 */
	private static function pin_public_key( $rates_url ) {
		$key_url  = str_replace( '/rates', '/rates-key', $rates_url );
		$response = wp_remote_get( $key_url, array( 'timeout' => 10 ) );
		if ( is_wp_error( $response ) ) {
			return '';
		}
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		$key  = is_array( $data ) ? (string) ( $data['public_key'] ?? '' ) : '';
		if ( '' === $key || false === strpos( $key, 'BEGIN PUBLIC KEY' ) ) {
			return '';
		}
		update_option( 'bomcas_rates_public_key', $key, false );
		return $key;
	}
}
