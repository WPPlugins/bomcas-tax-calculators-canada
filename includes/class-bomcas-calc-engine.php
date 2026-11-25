<?php
/**
 * Data-driven annual tax, sales tax and mortgage maths for Lite.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calculation engine. Mirrors assets/js/engine-lite.js.
 */
class Bomcas_Calc_Engine {

	/**
	 * Round half away from zero to cents.
	 *
	 * @param float $n Number.
	 * @return float
	 */
	public static function round2( $n ) {
		if ( ! is_finite( $n ) ) {
			return 0.0;
		}
		$sign = $n < 0 ? -1 : 1;
		return $sign * ( round( abs( $n ) * 100 + 1.0e-8 ) / 100 );
	}

	/**
	 * Progressive tax.
	 *
	 * @param float $income   Taxable income.
	 * @param array $brackets Bracket rows.
	 * @return float
	 */
	public static function bracket_tax( $income, $brackets ) {
		$tax  = 0.0;
		$prev = 0.0;
		foreach ( $brackets as $bracket ) {
			$cap   = null === $bracket['upto'] ? $income : (float) $bracket['upto'];
			$upper = min( $income, $cap );
			$slice = max( 0, $upper - $prev );
			$tax  += $slice * (float) $bracket['rate'];
			$prev  = $cap;
			if ( null === $bracket['upto'] || $income <= (float) $bracket['upto'] ) {
				break;
			}
		}
		return $tax;
	}

	/**
	 * Basic personal amount for a jurisdiction record.
	 *
	 * @param array $rec        Jurisdiction.
	 * @param float $net_income Net income proxy.
	 * @return float
	 */
	public static function bpa_amount( $rec, $net_income ) {
		if ( ! empty( $rec['ns_bpa'] ) && 'income' === $rec['ns_bpa']['mode'] ) {
			$ns = $rec['ns_bpa'];
			if ( $net_income <= $ns['from'] ) {
				return (float) $ns['max'];
			}
			if ( $net_income >= $ns['to'] ) {
				return (float) $ns['min'];
			}
			return self::round2( $ns['max'] - ( $net_income - $ns['from'] ) * $ns['rate'] );
		}
		$b = $rec['bpa'];
		if ( null === $b['phase_from'] || $net_income <= $b['phase_from'] ) {
			return (float) $b['max'];
		}
		if ( $net_income >= $b['phase_to'] ) {
			return (float) $b['min'];
		}
		$span  = $b['phase_to'] - $b['phase_from'];
		$extra = $b['max'] - $b['min'];
		return self::round2( $b['max'] - ( $net_income - $b['phase_from'] ) * ( $extra / $span ) );
	}

	/**
	 * Annual CPP/QPP, EI and QPIP.
	 *
	 * @param float $employment Employment income.
	 * @param array $spec       cpp_ei block.
	 * @param bool  $quebec     Quebec worker.
	 * @return array
	 */
	public static function annual_contributions( $employment, $spec, $quebec ) {
		$pen     = max( 0, min( $employment, $spec['ympe'] ) - $spec['exemption'] );
		$rate    = $quebec ? $spec['qpp_rate'] : $spec['rate'];
		$max     = $quebec ? $spec['qpp_max'] : $spec['max'];
		$contrib = self::round2( min( $pen * $rate, $max ) );
		$second  = 0.0;
		if ( ! empty( $spec['yampe'] ) && $employment > $spec['ympe'] && ! empty( $spec['second_rate'] ) ) {
			$band   = min( $employment, $spec['yampe'] ) - $spec['ympe'];
			$second = self::round2( min( $band * $spec['second_rate'], $spec['second_max'] ) );
		}
		$base_rate = $quebec ? $spec['qpp_base_rate'] : $spec['base_rate'];
		if ( 'split' !== $spec['credit_mode'] || null === $base_rate ) {
			$credit_base = $contrib;
			$deduction   = $second;
		} else {
			$base_max    = $quebec ? $spec['qpp_base_max'] : $spec['base_max'];
			$base        = self::round2( min( $contrib * ( $base_rate / $rate ), $base_max ) );
			$credit_base = $base;
			$deduction   = self::round2( ( $contrib - $base ) + $second );
		}
		$ei_rate = $quebec ? $spec['ei_qc_rate'] : $spec['ei_rate'];
		$ei_cap  = $quebec ? $spec['ei_qc_max'] : $spec['ei_max'];
		$ei      = self::round2( min( min( $employment, $spec['ei_max_earnings'] ) * $ei_rate, $ei_cap ) );
		$qpip    = 0.0;
		if ( $quebec && ! empty( $spec['qpip_rate'] ) ) {
			$qpip = self::round2( min( min( $employment, $spec['qpip_max_earnings'] ) * $spec['qpip_rate'], $spec['qpip_max'] ) );
		}
		return array(
			'cpp'        => $quebec ? 0 : $contrib,
			'qpp'        => $quebec ? $contrib : 0,
			'cpp2'       => $second,
			'ei'         => $ei,
			'qpip'       => $qpip,
			'creditBase' => $credit_base,
			'deduction'  => $deduction,
		);
	}

	/**
	 * Ontario health premium.
	 *
	 * @param float $income Taxable income.
	 * @return float
	 */
	public static function health_premium( $income ) {
		if ( $income <= 20000 ) {
			return 0.0;
		}
		$steps = array(
			array( 36000, 0, 0.06, 20000, 300 ),
			array( 48000, 300, 0.06, 36000, 450 ),
			array( 72000, 450, 0.25, 48000, 600 ),
			array( 200000, 600, 0.25, 72000, 750 ),
			array( INF, 750, 0.25, 200000, 900 ),
		);
		foreach ( $steps as $step ) {
			if ( $income <= $step[0] ) {
				return self::round2( min( $step[4], $step[1] + $step[2] * ( $income - $step[3] ) ) );
			}
		}
		return 0.0;
	}

	/**
	 * Annual income-tax estimate.
	 *
	 * @param array $input    Inputs.
	 * @param array $year     Year dataset.
	 * @param array $options  Options.
	 * @return array
	 */
	public static function tax_annual( $input, $year, $options = array() ) {
		$prov_code  = isset( $input['province'] ) ? $input['province'] : 'BC';
		$fed        = $year['jurisdictions']['FED'];
		$prov       = $year['jurisdictions'][ $prov_code ];
		$employment = max( 0, (float) ( $input['employment'] ?? 0 ) );
		$self_emp   = max( 0, (float) ( $input['selfEmployment'] ?? 0 ) );
		$other      = max( 0, (float) ( $input['other'] ?? 0 ) );
		$elig       = max( 0, (float) ( $input['eligibleDividends'] ?? 0 ) );
		$non_elig   = max( 0, (float) ( $input['nonEligibleDividends'] ?? 0 ) );
		$gains      = max( 0, (float) ( $input['capitalGains'] ?? 0 ) );
		$rrsp       = max( 0, (float) ( $input['rrsp'] ?? 0 ) );
		$fhsa       = max( 0, (float) ( $input['fhsa'] ?? 0 ) );
		$quebec     = 'QC' === $prov_code;
		if ( ! empty( $options['skipContributions'] ) ) {
			$contrib = array(
				'cpp'        => 0,
				'qpp'        => 0,
				'cpp2'       => 0,
				'ei'         => (float) ( $options['ei'] ?? 0 ),
				'qpip'       => (float) ( $options['qpip'] ?? 0 ),
				'creditBase' => (float) ( $options['creditBase'] ?? 0 ),
				'deduction'  => 0,
			);
		} else {
			$contrib = self::annual_contributions( $employment, $year['cpp_ei'], $quebec );
		}
		$div              = $year['dividends'];
		$elig_taxable     = $elig * ( 1 + $div['eligible_grossup'] );
		$non_elig_taxable = $non_elig * ( 1 + $div['non_eligible_grossup'] );
		$gains_taxable    = $gains * $year['capital_gains_inclusion'];
		$cash             = $employment + $self_emp + $other + $elig + $non_elig + $gains;
		$grossed          = $employment + $self_emp + $other + $elig_taxable + $non_elig_taxable + $gains_taxable;
		$workers          = 0.0;
		if ( $quebec && ! empty( $year['quebec_workers'] ) && ! isset( $options['taxableIncome'] ) ) {
			$workers = min( (float) $year['quebec_workers']['max'], $employment * (float) $year['quebec_workers']['rate'] );
		}
		if ( isset( $options['taxableIncome'] ) ) {
			$taxable = (float) $options['taxableIncome'];
		} else {
			$taxable = max( 0, $grossed - $rrsp - $fhsa - $contrib['deduction'] - $workers );
		}
		$fed_bpa       = self::bpa_amount( $fed, $taxable );
		$prov_bpa      = self::bpa_amount( $prov, $taxable );
		$credit_rate   = (float) $fed['credit_rate'];
		$basic_fed     = self::bracket_tax( $taxable, $fed['brackets'] );
		$gross_employ  = isset( $options['grossEmployment'] ) ? (float) $options['grossEmployment'] : $employment;
		$cea           = min( (float) ( $fed['cea'] ?? 0 ), $gross_employ );
		$k1            = $credit_rate * $fed_bpa;
		$k2            = $credit_rate * ( $contrib['creditBase'] + $contrib['ei'] + $contrib['qpip'] );
		$k4            = $credit_rate * $cea;
		$dtc           = $elig_taxable * $div['federal_eligible_credit_on_taxable'] + $non_elig_taxable * $div['federal_non_eligible_credit_on_taxable'];
		$basic_federal = max( 0, $basic_fed - $k1 - $k2 - $k4 - $dtc );
		$abatement     = $quebec ? 0.165 * $basic_federal : 0;
		$federal_tax   = max( 0, $basic_federal - $abatement );
		$basic_prov    = self::bracket_tax( $taxable, $prov['brackets'] );
		$k1p           = $prov['credit_rate'] * $prov_bpa;
		$k2p           = $prov['credit_rate'] * ( $contrib['creditBase'] + $contrib['ei'] + ( $quebec ? $contrib['qpip'] : 0 ) );
		$k4p           = ! empty( $prov['cea'] ) ? $prov['credit_rate'] * min( $prov['cea'], $gross_employ ) : 0;
		$after_credits = max( 0, $basic_prov - $k1p - $k2p - $k4p );
		if ( ! empty( $prov['ab_k5p'] ) ) {
			$k5base = $k1p + $k2p;
			if ( $k5base > $prov['ab_k5p']['threshold'] ) {
				$after_credits = max( 0, $after_credits - ( $k5base - $prov['ab_k5p']['threshold'] ) * $prov['ab_k5p']['multiplier'] );
			}
		}
		$surtax = 0.0;
		if ( ! empty( $prov['on_surtax'] ) ) {
			foreach ( $prov['on_surtax'] as $tier ) {
				if ( $after_credits > $tier['over'] ) {
					$surtax += $tier['rate'] * ( $after_credits - $tier['over'] );
				}
			}
		}
		if ( ! empty( $prov['pe_surtax'] ) && $after_credits > $prov['pe_surtax']['over'] ) {
			$surtax += $prov['pe_surtax']['rate'] * ( $after_credits - $prov['pe_surtax']['over'] );
		}
		$health    = ! empty( $prov['on_health'] ) ? self::health_premium( $taxable ) : 0;
		$reduction = 0.0;
		if ( ! empty( $prov['bc_reduction'] ) ) {
			$red = $prov['bc_reduction'];
			if ( $taxable <= $red['threshold'] ) {
				$reduction = min( $after_credits, $red['basic'] );
			} elseif ( $taxable < $red['upper'] ) {
				$reduction = min( $after_credits, max( 0, $red['basic'] - ( $taxable - $red['threshold'] ) * $red['rate'] ) );
			}
		}
		if ( isset( $prov['on_reduction_basic'] ) ) {
			$before    = $after_credits + $surtax;
			$candidate = min( $before, ( 2 * $prov['on_reduction_basic'] ) - $before );
			$reduction = max( 0, $candidate );
		}
		$omit       = ! empty( $options['omit_provincial'] );
		$provincial = $omit ? null : max( 0, $after_credits + $surtax + $health - $reduction );
		return array(
			'federalTax'                  => self::round2( $federal_tax ),
			'provincialTax'               => null === $provincial ? null : self::round2( $provincial ),
			'totalTax'                    => null === $provincial ? null : self::round2( $federal_tax + $provincial ),
			'taxableIncome'               => self::round2( $taxable ),
			'cashIncome'                  => self::round2( $cash ),
			'afterTax'                    => null === $provincial ? null : self::round2( $cash - ( $federal_tax + $provincial ) ),
			'averageRate'                 => null === $provincial || $cash <= 0 ? null : ( $federal_tax + $provincial ) / $cash,
			'lines'                       => array(
				'abatement' => self::round2( $abatement ),
				'surtax'    => self::round2( $surtax ),
				'health'    => self::round2( $health ),
				'reduction' => self::round2( $reduction ),
			),
			'contributions'               => $contrib,
			'workersDeduction'            => self::round2( $workers ),
			'partialEstimate'             => true,
			'quebecProvincialWithholding' => ! empty( $options['omit_provincial'] ) ? 'not_calculated' : null,
		);
	}

	/**
	 * Segment in force on a date.
	 *
	 * @param array  $sales    Sales dataset.
	 * @param string $province Code.
	 * @param string $iso      Date.
	 * @return array|null
	 */
	public static function sales_on_date( $sales, $province, $iso ) {
		if ( empty( $sales['jurisdictions'][ $province ] ) ) {
			return null;
		}
		foreach ( $sales['jurisdictions'][ $province ] as $row ) {
			if ( $iso >= $row['from'] && $iso <= $row['to'] ) {
				return $row;
			}
		}
		return null;
	}

	/**
	 * Add or remove sales tax.
	 *
	 * @param float  $amount    Amount.
	 * @param array  $segment   Rate segment.
	 * @param string $direction add|remove.
	 * @return array
	 */
	public static function sales_tax( $amount, $segment, $direction ) {
		$gst   = (float) ( $segment['gst'] ?? 0 );
		$pst   = (float) ( $segment['pst'] ?? 0 );
		$hst   = (float) ( $segment['hst'] ?? 0 );
		$qst   = (float) ( $segment['qst'] ?? 0 );
		$rate  = $gst + $pst + $hst + $qst;
		$base  = ( 'remove' === $direction && $rate > 0 ) ? $amount / ( 1 + $rate ) : $amount;
		$base  = self::round2( $base );
		$parts = array(
			'gst' => self::round2( $base * $gst ),
			'pst' => self::round2( $base * $pst ),
			'hst' => self::round2( $base * $hst ),
			'qst' => self::round2( $base * $qst ),
		);
		$tax   = self::round2( $parts['gst'] + $parts['pst'] + $parts['hst'] + $parts['qst'] );
		return array(
			'base'  => $base,
			'tax'   => $tax,
			'total' => self::round2( $base + $tax ),
			'rate'  => $rate,
			'parts' => $parts,
		);
	}

	/**
	 * Mortgage or loan payment.
	 *
	 * @param array $input Inputs.
	 * @return array
	 */
	public static function mortgage_payment( $input ) {
		$principal = max( 0, (float) $input['price'] - (float) ( $input['down'] ?? 0 ) );
		$annual    = max( 0, (float) $input['annualRate'] );
		$years     = max( 1, (float) ( $input['amortYears'] ?? 25 ) );
		$per_year  = (float) ( $input['paymentsPerYear'] ?? 12 );
		$compound  = $input['compounding'] ?? 'semi-annual';
		$j         = ( 'monthly' === $compound ) ? ( $annual / $per_year ) : ( pow( 1 + $annual / 2, 2 / $per_year ) - 1 );
		$n         = (int) round( $years * $per_year );
		$payment   = ( 0.0 === $j ) ? $principal / $n : $principal * $j / ( 1 - pow( 1 + $j, -$n ) );
		return array(
			'principal' => self::round2( $principal ),
			'payment'   => self::round2( $payment ),
			'payments'  => $n,
			'interest'  => self::round2( $payment * $n - $principal ),
		);
	}
}
