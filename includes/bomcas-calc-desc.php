<?php
/**
 * Search descriptions for calculator pages. Each sentence names the tool and, on a place page, the place.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A 150–160 character description. The place name is kept when the page has one.
 *
 * @param string $tool  Calculator id.
 * @param string $place Place name, or empty on a hub.
 * @return string
 */
function bomcas_calc_compose_desc( $tool, $place ) {
	$place = trim( (string) $place );
	$built = bomcas_calc_desc_fit( bomcas_calc_desc_leads( $tool, $place ), bomcas_calc_desc_tails( $tool ) );
	if ( '' !== $built ) {
		return $built;
	}
	$label = bomcas_calc_desc_label( $tool );
	$lead  = '' !== $place
		? $place . ' ' . $label . '. Figures use the current published rates shipped with the plugin.'
		: $label . '. Figures use the current published rates shipped with the plugin.';
	$built = bomcas_calc_desc_fit( array( $lead ), bomcas_calc_desc_tails( $tool ) );
	return '' !== $built ? $built : $lead;
}

/**
 * @param array<int,string> $leads Opening sentences.
 * @param array<int,string> $tails Extra true sentences.
 * @return string
 */
function bomcas_calc_desc_fit( $leads, $tails ) {
	foreach ( $leads as $lead ) {
		$options = array( $lead );
		foreach ( $tails as $tail ) {
			$options[] = $lead . ' ' . $tail;
		}
		$count = count( $tails );
		for ( $i = 0; $i < $count; $i++ ) {
			for ( $j = $i + 1; $j < $count; $j++ ) {
				$options[] = $lead . ' ' . $tails[ $i ] . ' ' . $tails[ $j ];
			}
		}
		foreach ( $options as $sentence ) {
			$length = function_exists( 'mb_strlen' ) ? mb_strlen( $sentence ) : strlen( $sentence );
			if ( $length >= 150 && $length <= 160 ) {
				return $sentence;
			}
		}
	}
	return '';
}

/**
 * @param string $tool Tool id.
 * @return string
 */
function bomcas_calc_desc_label( $tool ) {
	$labels = array(
		'calculators'            => 'Canadian tax and payroll calculators',
		'income-tax'             => 'Income tax',
		'salary-after-tax'       => 'Take-home pay',
		'payroll-deductions'     => 'Payroll deductions',
		'sales-tax'              => 'Sales tax',
		'vehicle-purchase'       => 'Vehicle purchase tax',
		'land-transfer-tax'      => 'Land transfer tax',
		'fhsa'                   => 'FHSA room',
		'rrsp-room'              => 'RRSP room',
		'tfsa-room'              => 'TFSA room',
		'corporate-tax'          => 'Corporate tax',
		'capital-gains'          => 'Capital gains',
		'salary-vs-dividend'     => 'Salary versus dividend',
		'canada-child-benefit'   => 'Canada child benefit',
		'capital-cost-allowance' => 'Capital cost allowance',
		'ei-benefits'            => 'EI benefits',
		'tax-instalments'        => 'Tax instalments',
		'car-allowance'          => 'Car allowance',
		'cra-penalty-interest'   => 'CRA penalty and interest',
		'donations'              => 'Donation credit',
		'employer-health-tax'    => 'Employer health tax',
		'old-age-security'       => 'Old Age Security',
		'self-employed-tax'      => 'Self-employed CPP',
		'quebec-withholding'     => 'Quebec withholding',
		'quebec-welcome-tax'     => 'Quebec welcome tax',
		'lease-vs-buy'           => 'Lease or buy',
		'mortgage-affordability' => 'Mortgage stress-test payment',
		'cmhc-insurance'         => 'CMHC insurance',
		'mortgage-payment'       => 'Mortgage payment',
	);
	if ( isset( $labels[ $tool ] ) ) {
		return $labels[ $tool ];
	}
	return ucwords( str_replace( '-', ' ', (string) $tool ) );
}

/**
 * Sales tax that actually applies in a province. Empty when the place is not one of the thirteen.
 *
 * @param string $place Place.
 * @return string
 */
function bomcas_calc_sales_name( $place ) {
	$gst = array( 'Alberta', 'Northwest Territories', 'Nunavut', 'Yukon' );
	$pst = array( 'British Columbia', 'Manitoba', 'Saskatchewan' );
	$hst = array( 'Ontario', 'New Brunswick', 'Newfoundland and Labrador', 'Nova Scotia', 'Prince Edward Island' );
	if ( 'Quebec' === $place ) {
		return 'GST and QST';
	}
	if ( in_array( $place, $gst, true ) ) {
		return 'GST';
	}
	if ( in_array( $place, $pst, true ) ) {
		return 'GST and PST';
	}
	if ( in_array( $place, $hst, true ) ) {
		return 'HST';
	}
	return '';
}

/**
 * @param string $tool  Tool.
 * @param string $place Place.
 * @return array<int,string>
 */
function bomcas_calc_desc_leads( $tool, $place ) {
	$p     = $place;
	$named = '' !== $p;
	$sales = $named ? bomcas_calc_sales_name( $p ) : '';
	$leads = array();
	switch ( $tool ) {
		case 'calculators':
			$leads[] = 'Income tax, payroll, sales tax, vehicle tax, and home calculators. Each tool has its own page, and the figures come from current rates.';
			break;
		case 'income-tax':
			if ( $named ) {
				$leads[] = $p . ' income tax on wages, self-employment income, dividends, and capital gains. Federal and ' . $p . ' brackets come from current rates.';
				$leads[] = 'Federal income tax and ' . $p . ' tax on wages, self-employment income, dividends, and capital gains. Brackets come from current rates.';
				$leads[] = $p . ' income tax from the wages and investment income you enter. The federal and ' . $p . ' basic personal amounts are in current rates.';
			} else {
				$leads[] = 'Federal and provincial income tax on wages, self-employment income, dividends, and capital gains. Choose a province to use its brackets.';
			}
			break;
		case 'salary-after-tax':
			if ( 'Quebec' === $p ) {
				$leads[] = 'Quebec take-home pay after federal tax, Quebec tax, QPP, EI, and QPIP on the gross pay and the pay period you enter.';
				$leads[] = 'Take-home pay in Quebec after federal tax, Quebec tax, QPP, and QPIP. The gross pay and the pay period are the ones you enter.';
			} elseif ( $named ) {
				$leads[] = $p . ' take-home pay after federal tax, ' . $p . ' tax, CPP, and EI on the gross pay and the pay period you type.';
				$leads[] = 'Take-home pay in ' . $p . ' after income tax, CPP, and EI. The gross pay and the pay period are the ones you enter.';
			} else {
				$leads[] = 'Take-home pay after federal tax, provincial tax, CPP, and EI. Choose a province, then enter the gross pay and the pay period.';
			}
			break;
		case 'payroll-deductions':
			if ( 'Quebec' === $p ) {
				$leads[] = 'Quebec payroll deductions: QPP, QPIP, EI, and Quebec income tax withheld on one regular pay, from current rates.';
				$leads[] = 'QPP, QPIP, EI, and Quebec tax withheld on one regular pay. The pay and the period are the ones you enter.';
			} elseif ( $named ) {
				$leads[] = $p . ' payroll deductions: CPP, EI, and ' . $p . ' income tax withheld on one regular pay. The amounts come from current rates.';
				$leads[] = 'CPP, EI, and ' . $p . ' income tax withheld on one regular pay. The gross pay and the pay period are the ones you enter.';
			} else {
				$leads[] = 'CPP, EI, and provincial income tax withheld on one regular pay. Choose a province. Quebec uses QPP and QPIP.';
			}
			break;
		case 'sales-tax':
			if ( $named && '' !== $sales ) {
				$leads[] = $p . ' sales tax. Add or remove ' . $sales . ' on a standard purchase in ' . $p . '. The rate is the one in the file for the date you choose.';
				$leads[] = 'Add or remove ' . $sales . ' on a standard amount in ' . $p . '. The rate in the file is the one for the date you choose.';
			} elseif ( $named ) {
				$leads[] = $p . ' sales tax on a purchase amount. Add the provincial sales tax that current rates list for ' . $p . ', or remove that tax from a total.';
			} else {
				$leads[] = 'Add or remove GST, HST, PST, or QST on a standard amount. The province and the date you choose select the rate in the file.';
			}
			break;
		case 'vehicle-purchase':
			if ( $named && '' !== $sales ) {
				$leads[] = $p . ' vehicle tax on a dealer sale or a private purchase, including ' . $sales . '. The price is the one you enter.';
				$leads[] = 'Tax on a vehicle bought in ' . $p . ', dealer or private. ' . $sales . ' applies to the price you enter.';
			} elseif ( $named ) {
				$leads[] = $p . ' tax on a vehicle purchase. The sales tax that current rates list for ' . $p . ' is applied to the price you enter.';
			} else {
				$leads[] = 'Tax on a dealer sale or a private vehicle purchase. Choose a province. The sales tax for that province comes from current rates.';
			}
			break;
		case 'land-transfer-tax':
			if ( 'British Columbia' === $p ) {
				$leads[] = 'British Columbia property transfer tax on the price you enter, using the provincial brackets in current rates. The page covers British Columbia.';
			} elseif ( 'Ontario' === $p ) {
				$leads[] = 'Ontario land transfer tax on the price you enter, using the provincial brackets in current rates. This page is for Ontario only.';
			} else {
				$leads[] = 'Land transfer tax for British Columbia and Ontario. Enter the price. The brackets are the ones in current rates for that province.';
			}
			break;
		case 'fhsa':
			$leads[] = 'FHSA room still available, from the annual limit, the lifetime limit, and the contributions you enter. The limits are in current rates.';
			break;
		case 'rrsp-room':
			$leads[] = 'RRSP room from last year\'s earned income, the dollar limit, and the pension adjustment. The dollar limit is the one in current rates.';
			break;
		case 'tfsa-room':
			$leads[] = 'TFSA room from the annual dollar limits and the contributions you enter. Unused room carries forward. The limits are in current rates.';
			break;
		case 'corporate-tax':
			$leads[] = 'Federal and provincial tax on active business income of a Canadian-controlled private corporation, using the small-business limit in current rates for the year.';
			break;
		case 'capital-gains':
			$leads[] = 'Tax on a capital gain. One half of the gain is included in income, and tax is then estimated from the brackets in current rates.';
			break;
		case 'salary-vs-dividend':
			$leads[] = 'Salary compared with a non-eligible dividend drawn from the same corporate profit. Personal tax on each side comes from current rates.';
			break;
		case 'canada-child-benefit':
			$leads[] = 'The Canada child benefit for the number of children and the adjusted family income you enter. The amounts are the ones in current rates.';
			break;
		case 'capital-cost-allowance':
			$leads[] = 'Capital cost allowance for the class and the cost you enter. The class rate is the one in current rates, and the result is not a tax filing.';
			break;
		case 'ei-benefits':
			$leads[] = 'A regular Employment Insurance benefit from insurable earnings. The weekly rate and the maximum are the ones in current rates.';
			break;
		case 'tax-instalments':
			$leads[] = 'Whether personal tax instalments are required, from the balance due and the prior-year amounts you enter. The threshold is in current rates.';
			break;
		case 'car-allowance':
			$leads[] = 'A car allowance at the prescribed cents per kilometre for the kilometres you enter. The rates are the ones in current rates for the year.';
			break;
		case 'cra-penalty-interest':
			$leads[] = 'A late-filing penalty and prescribed interest on the balance and the months late you enter. The rates are the ones in current rates.';
			break;
		case 'donations':
			$leads[] = 'The federal and provincial donation credit on the gift you enter. The credit rates are the ones in current rates for the year.';
			break;
		case 'employer-health-tax':
			$leads[] = 'Employer health tax on a payroll total in a province that charges it. The threshold and the rate are the ones in current rates.';
			break;
		case 'old-age-security':
			$leads[] = 'Old Age Security recovery tax when net income is above the threshold. The threshold and the rate are the ones in current rates.';
			break;
		case 'self-employed-tax':
			$leads[] = 'CPP on self-employment income, both shares, from the pensionable earnings you enter. The rate and the maximum are in current rates.';
			break;
		case 'quebec-withholding':
			$leads[] = 'Quebec income tax withheld on a paycheque. The Quebec tables in current rates are applied to the gross pay and the period you enter.';
			break;
		case 'quebec-welcome-tax':
			$leads[] = 'The Quebec welcome tax on a property transfer. The price you enter is run through the welcome-tax brackets in current rates.';
			break;
		case 'lease-vs-buy':
			$leads[] = 'The cost of leasing a vehicle compared with buying it, from the payments, the price, and the term you enter. Sales tax is included.';
			break;
		case 'mortgage-affordability':
			$leads[] = 'A mortgage payment at the stress-test rate, plus the gross and total debt-service ratios, from the income and the debts you enter.';
			break;
		case 'cmhc-insurance':
			$leads[] = 'The mortgage default-insurance premium from the price and the down payment you enter. The premium schedule is the one in current rates.';
			break;
		case 'mortgage-payment':
			$leads[] = 'A mortgage payment from the price, the interest rate you type, and the amortization. That interest rate is the one you type, not a published tax rate.';
			break;
		default:
			$label   = bomcas_calc_desc_label( $tool );
			$leads[] = $named
				? $p . ' ' . $label . '. The figures use the current published rates shipped with the plugin.'
				: $label . '. The figures use the current published rates shipped with the plugin.';
			break;
	}
	return $leads;
}

/**
 * Extra sentences that are true for the tool. The fitter adds one or two only when the lead is short of 150 characters.
 *
 * @param string $tool Tool.
 * @return array<int,string>
 */
function bomcas_calc_desc_tails( $tool ) {
	$common = array(
		'It is an estimate.',
		'It does not file a return.',
		'The result is an estimate, and it does not file a return.',
		'Your entries stay in this browser.',
		'Nothing you type is stored.',
	);
	$extra  = array(
		'income-tax'         => array( 'The basic personal amount is included.', 'Credits the form does not ask for are left out.' ),
		'payroll-deductions' => array( 'It does not file a T4.' ),
		'sales-tax'          => array( 'A zero-rated supply is not assumed.' ),
		'vehicle-purchase'   => array( 'Dealer fees are not added unless you enter them.' ),
		'land-transfer-tax'  => array( 'Municipal rebates are not assumed.' ),
	);
	$tails  = isset( $extra[ $tool ] ) ? $extra[ $tool ] : array();
	if ( in_array( $tool, array( 'payroll-deductions', 'land-transfer-tax', 'vehicle-purchase' ), true ) ) {
		$common = array_values(
			array_filter(
				$common,
				static function ( $tail ) {
					return false === strpos( $tail, 'file a return' );
				}
			)
		);
		if ( 'payroll-deductions' === $tool ) {
			array_unshift( $tails, 'It estimates the deduction. It does not file a T4 or a remittance.' );
		} elseif ( 'land-transfer-tax' === $tool ) {
			array_unshift( $tails, 'It is not a statement of adjustments.' );
		} else {
			array_unshift( $tails, 'It is not a bill of sale.' );
		}
	}
	return array_merge( $tails, $common );
}
