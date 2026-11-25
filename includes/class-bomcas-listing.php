<?php
/**
 * Public description of each calculator Lite and Pro actually run.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Store pages and the directory listing reuse this copy.
 */
class Bomcas_Calc_Listing {

	/**
	 * Long feature page.
	 *
	 * @return string
	 */
	public static function features() {
		$html  = '<div class="ae-guide">';
		$html .= '<h2>' . esc_html__( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ) . '</h2>';
		$html .= '<p class="bomcas-lead">' . esc_html__( 'Built by BOMCAS Canada', 'bomcas-tax-calculators-canada' ) . ' (<a href="' . esc_url( 'https://bomcas.ca' ) . '">bomcas.ca</a>). ' . esc_html__( 'Owned by Dellendo Farquharson. Every figure is an estimate from the rate file. It is not a filing, a ruling, or a Canada Revenue Agency service.', 'bomcas-tax-calculators-canada' ) . ' <a href="' . esc_url( 'https://accountantsexpert.com/calculators-docs/' ) . '">' . esc_html__( 'Docs', 'bomcas-tax-calculators-canada' ) . '</a></p>';
		$html .= self::matrix();
		$html .= '<h2>' . esc_html__( 'Lite calculators', 'bomcas-tax-calculators-canada' ) . '</h2>';
		foreach ( self::lite_tools() as $tool ) {
			$html .= self::calculator( $tool['title'], $tool['what'], $tool['where'], $tool['inputs'], $tool['outputs'] );
		}
		$html .= '<h2>' . esc_html__( 'Pro calculators', 'bomcas-tax-calculators-canada' ) . '</h2>';
		$html .= '<p>' . esc_html__( 'The Pro add-on is a separate plugin. Lite does not prompt for it, lock a control, or install it.', 'bomcas-tax-calculators-canada' ) . ' <a href="' . esc_url( 'https://accountantsexpert.com/products/bomcas-tax-payroll-calculators-canada/' ) . '">' . esc_html__( 'See Pro', 'bomcas-tax-calculators-canada' ) . '</a></p>';
		foreach ( self::pro_tools() as $tool ) {
			$html .= self::calculator( $tool['title'], $tool['what'], $tool['where'], $tool['inputs'], $tool['outputs'] );
		}
		$html .= self::platform();
		$html .= self::uses();
		$html .= self::setup();
		$html .= self::faq_html();
		$html .= '</div>';
		return $html;
	}

	/**
	 * Questions and answers used by the page and by FAQ schema.
	 *
	 * @return array<int,array{0:string,1:string}>
	 */
	public static function faqs() {
		return array(
			array(
				__( 'Does this file a return?', 'bomcas-tax-calculators-canada' ),
				__( 'No. The figures are estimates. They are not a filing, a ruling, or a Canada Revenue Agency service. The plugin does not prepare a T1, a T2, a T4, or a remittance.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Where do the typed numbers go?', 'bomcas-tax-calculators-canada' ),
				__( 'The arithmetic runs in the browser. The form is not posted for the calculation. A view count is stored on your own WordPress site. An email, a PDF, or a Google event is sent only after you turn that option on and, for email or PDF, the visitor submits it.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Which tax year does income tax use?', 'bomcas-tax-calculators-canada' ),
				__( 'The newest year in the personal tax file whose start date is today or earlier. The file shipped with Lite is the 2026 personal tax year. A later file, imported under Calculators, Rates, is used on the next page view. Sales-tax segments in the file run from 2022 through 2026, and the date the visitor picks selects the segment.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Which places are covered?', 'bomcas-tax-calculators-canada' ),
				__( 'Canadian income tax and sales tax cover the federal tables and all 13 provinces and territories. Some Pro tools cover fewer places, and those limits are named on each calculator.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Does Lite include Canadian payroll?', 'bomcas-tax-calculators-canada' ),
				__( 'No. Canadian payroll deductions and salary after tax start on the Professional plan. Personal does not include them. Lite income tax does use the CPP, CPP2, EI, QPP, and QPIP amounts from the rate file when it works out credits. It does not show a paycheque.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'What do the Pro plans cover?', 'bomcas-tax-calculators-canada' ),
				__( 'Personal is one site, Professional is five sites, and Agency is twenty-five sites. Prices are in Canadian dollars on the pricing page. The licence is for updates and support while it is active or in grace. The GPL code you already installed keeps running if the subscription ends.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'How do I put a calculator on a page?', 'bomcas-tax-calculators-canada' ),
				__( 'Add the BOMCAS Tax Calculators – Canada block, or the shortcode [bomcas_calculator id="income-tax" province="ON"]. Pro tools use [bomcas_calculator_pro id="payroll-deductions" province="ON"]. The province code is optional. Income tax and sales tax default to British Columbia.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Which WordPress version does it need?', 'bomcas-tax-calculators-canada' ),
				__( 'Lite requires WordPress 6.2 or newer. Pro requires WordPress 6.5 or newer, because it declares Lite as a required plugin. Both require PHP 7.4 or newer.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Does it work with my theme?', 'bomcas-tax-calculators-canada' ),
				__( 'Yes. The calculator is a block and a shortcode. It does not replace your theme, your header, or your styles outside its own form. It has been checked on the Accountants Expert theme and on a default block theme.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Does it work in the block editor?', 'bomcas-tax-calculators-canada' ),
				__( 'Yes. Search the block inserter for BOMCAS Tax Calculators – Canada. Choose income tax, sales tax, or a mortgage payment, and optionally a province. Payroll blocks are not in Lite.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Can I use a shortcode instead of a block?', 'bomcas-tax-calculators-canada' ),
				__( 'Yes. Paste [bomcas_calculator id="sales-tax" province="BC"] into a shortcode block or the classic editor. The ids in Lite are income-tax, sales-tax, and mortgage-payment.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Does it work with Elementor, Beaver Builder, or another page builder?', 'bomcas-tax-calculators-canada' ),
				__( 'Yes, where the builder can place a WordPress block or a shortcode. Use the builder’s shortcode widget if it does not list the block. The calculation still runs in the visitor’s browser.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Does it work on WordPress Multisite?', 'bomcas-tax-calculators-canada' ),
				__( 'Yes. Activate it on the sites that should show a calculator. A Pro licence counts each site address you activate. Network activation is supported. Each site still needs its own activation on the licence if you use Pro updates.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'How accurate are the numbers?', 'bomcas-tax-calculators-canada' ),
				__( 'They follow the rate file shipped with the plugin, or a file you import later. Income tax uses federal and provincial brackets, basic personal amounts, and the CPP, CPP2, EI, QPP, and QPIP amounts in that file. They are estimates for a conversation, not a assessed return.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'How do rate updates work?', 'bomcas-tax-calculators-canada' ),
				__( 'Lite ships the rates in the plugin. A new tax year arrives when you update the plugin from WordPress.org. You can also import a file under Calculators, Rates. A remote download stays off until you opt in on that screen, and it sends no calculator inputs. Pro, with a saved licence key, checks accountantsexpert.com once a day. A failed signature or checksum leaves the bundled rates in place.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Can I replace a rate myself?', 'bomcas-tax-calculators-canada' ),
				__( 'Yes. Calculators, Rates exports the current file and imports a replacement. An override you saved stays in place if a later file touches the same value, and the screen tells you when that happens.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Does it send calculator inputs to Google?', 'bomcas-tax-calculators-canada' ),
				__( 'No, unless you save a Google measurement id. With that field empty, nothing is sent to Google. The arithmetic does not leave the browser.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'What about email and PDF?', 'bomcas-tax-calculators-canada' ),
				__( 'Both stay off until you enable them. A visitor has to submit the form before an email is sent or a PDF is requested. The calculation itself is not that submission.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'What does the site store?', 'bomcas-tax-calculators-canada' ),
				__( 'A view count on your own site. Leads, if you turn the form on, are stored in your database and can be deleted on a schedule you set. Calculator inputs used only for the estimate are not posted to Accountants Expert.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'What does Lite include?', 'bomcas-tax-calculators-canada' ),
				__( 'Three calculators: Canadian income tax, GST, HST, PST and QST, and a mortgage payment. The styling shipped with the plugin. The rate file in the zip. Lite does not include payroll, corporate, savings, or property tools, white-label, a site-count licence, or a Pro download.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Is Lite a trial that locks features?', 'bomcas-tax-calculators-canada' ),
				__( 'No. Lite is the free plugin. Pro tools are not hidden inside it. Limits are the tools Lite simply does not include. Upgrade prompts appear in wp-admin only, and a visitor never sees them.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'How do I upgrade from inside the plugin?', 'bomcas-tax-calculators-canada' ),
				__( 'Open Calculators, Account and upgrade. Plans and any active coupon come from the store. Buy sends this site address and the receipt email only after you check the consent box, then opens Stripe Checkout and returns to the plugin.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Will Lite install Pro for me?', 'bomcas-tax-calculators-canada' ),
				__( 'No. After you buy, open My Account on accountantsexpert.com, download the Pro zip, and upload it under Plugins, Add New. Paste the licence key into Pro.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'How many sites does a plan cover?', 'bomcas-tax-calculators-canada' ),
				__( 'Personal is 1 site, Professional is 5, and Agency is 25. Lite itself is not limited by a site-count licence. The count applies to Pro updates and support.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'How do I activate or remove a site?', 'bomcas-tax-calculators-canada' ),
				__( 'Pro shows the sites on the licence. My Account on accountantsexpert.com is where you activate or deactivate this site, see invoices, and change the plan. The plugin stores the licence key locally and does not print it back.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'When does a plan renew?', 'bomcas-tax-calculators-canada' ),
				__( 'On the renewal date shown in Pro and in My Account. Monthly, quarterly, and yearly prices are the amounts Stripe charges for that period. You can turn renewal off. Access continues until the period you already paid for ends.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Can I cancel, and is unused time refunded?', 'bomcas-tax-calculators-canada' ),
				__( 'You can cancel renewal at any time from My Account. Cancellation stops the next charge. Unused time in a period you already paid for is not refunded, except where the law requires it.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Do coupons work?', 'bomcas-tax-calculators-canada' ),
				__( 'Yes, when the store has an active coupon. The upgrade screen and the pricing page list coupons that have banner text and are still inside their dates and redemption cap. An empty list means there is no public promotion. You can still type a code at checkout.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'What does support cover?', 'bomcas-tax-calculators-canada' ),
				__( 'Pro support and updates run while the licence is active or in grace. Lite is the free plugin. Support for a paid plan is from the store, at the address in My Account. The calculators keep running if a subscription lapses. A lapsed licence pauses updates and support. It does not switch the installed code off.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'What is white-label?', 'bomcas-tax-calculators-canada' ),
				__( 'Professional and Agency can hide the licence key, plan, and upgrade screens from users who are not on an allow list. Personal does not include white-label. Lite does not include it.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Which languages ship with Lite?', 'bomcas-tax-calculators-canada' ),
				__( 'English, French (Canada), and Spanish. The calculators use the language of the WordPress site when a translation is installed.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Does the form fit a phone?', 'bomcas-tax-calculators-canada' ),
				__( 'Yes. The calculator form fits a narrow screen. The gallery includes an income-tax view at phone width. That is the layout that was checked, not a separate mobile app.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Where do I paste the licence key?', 'bomcas-tax-calculators-canada' ),
				__( 'In Pro, on the licence screen. Leave the licence server empty on the store itself. On a customer site, set it to https://accountantsexpert.com. The key is saved on that site and is not shown in the page source afterwards.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'What happens if I do not enter a licence?', 'bomcas-tax-calculators-canada' ),
				__( 'Pro calculators still run. Updates and support stay paused until the licence is active or in grace. Lite never asks for a key.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'How do I pay?', 'bomcas-tax-calculators-canada' ),
				__( 'Stripe Checkout. You can pay by card, and by Apple Pay or Google Pay where Stripe offers them. Accountants Expert does not store card numbers. Prices are in Canadian dollars.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'How does Quebec work?', 'bomcas-tax-calculators-canada' ),
				__( 'Income tax includes the federal abatement and the Quebec workers deduction. Sales tax can show QST. Payroll tools, on Professional and Agency, use RRQ, RQAP, and the Quebec withholding table where that calculator has a row. The salary-versus-dividend table does not include Quebec.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Does land transfer tax include Toronto?', 'bomcas-tax-calculators-canada' ),
				__( 'Land transfer tax is a Pro tool. British Columbia and Ontario use the brackets in the file. A Toronto checkbox adds the municipal tax. Manitoba, New Brunswick, Nova Scotia, and Prince Edward Island use a rate you type. Quebec uses brackets you type. A blank rate means no tax is added.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Is this affiliated with the CRA?', 'bomcas-tax-calculators-canada' ),
				__( 'No. BOMCAS Canada builds the plugin. It is not the Canada Revenue Agency, and it is not a tax filing service.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				__( 'Who makes it?', 'bomcas-tax-calculators-canada' ),
				__( 'BOMCAS Canada, owned by Dellendo Farquharson. The product name is BOMCAS Tax Calculators – Canada. The store is accountantsexpert.com.', 'bomcas-tax-calculators-canada' ),
			),
		);
	}

	/**
	 * @return string
	 */
	private static function matrix() {
		$rows  = array(
			array( __( 'Canadian income tax', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ) ),
			array( __( 'GST, HST, PST and QST', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ) ),
			array( __( 'Mortgage payment', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ) ),
			array( __( 'Canadian payroll and take-home pay', 'bomcas-tax-calculators-canada' ), __( 'No', 'bomcas-tax-calculators-canada' ), __( 'Professional and Agency', 'bomcas-tax-calculators-canada' ) ),
			array( __( 'Corporate, savings, property and benefit tools', 'bomcas-tax-calculators-canada' ), __( 'No', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ) ),
			array( __( 'Rate file you can replace', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ) ),
			array( __( 'Block and shortcode', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ) ),
			array( __( 'English, French (Canada) and Spanish', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ), __( 'Yes', 'bomcas-tax-calculators-canada' ) ),
			array( __( 'Updates and support while the licence is active', 'bomcas-tax-calculators-canada' ), __( 'Free plugin updates', 'bomcas-tax-calculators-canada' ), __( 'Licence required', 'bomcas-tax-calculators-canada' ) ),
			array( __( 'Sites on a paid plan', 'bomcas-tax-calculators-canada' ), __( 'Not limited by a licence', 'bomcas-tax-calculators-canada' ), __( '1, 5 or 25', 'bomcas-tax-calculators-canada' ) ),
		);
		$html  = '<h2>' . esc_html__( 'Lite and Pro', 'bomcas-tax-calculators-canada' ) . '</h2>';
		$html .= '<div class="ae-table-wrap"><table class="ae-compare"><thead><tr><th>' . esc_html__( 'Feature', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Lite', 'bomcas-tax-calculators-canada' ) . '</th><th>' . esc_html__( 'Pro', 'bomcas-tax-calculators-canada' ) . '</th></tr></thead><tbody>';
		foreach ( $rows as $row ) {
			$html .= '<tr><td>' . esc_html( $row[0] ) . '</td><td>' . esc_html( $row[1] ) . '</td><td>' . esc_html( $row[2] ) . '</td></tr>';
		}
		$html .= '</tbody></table></div>';
		return $html;
	}

	/**
	 * @return array<int,array<string,string>>
	 */
	public static function lite_tools() {
		$places = __( 'Alberta, British Columbia, Manitoba, New Brunswick, Newfoundland and Labrador, the Northwest Territories, Nova Scotia, Nunavut, Ontario, Prince Edward Island, Quebec, Saskatchewan, and Yukon.', 'bomcas-tax-calculators-canada' );
		return array(
			array(
				'title'   => __( 'Income tax', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Federal tax plus one province or territory for the year in the rate file. The file shipped with Lite is the 2026 personal tax year. It does not print a paycheque.', 'bomcas-tax-calculators-canada' ),
				'where'   => $places,
				'inputs'  => __( 'Place, employment income, self-employment income, other income, eligible dividends, non-eligible dividends, capital gains, one RRSP and FHSA deduction, and tax already deducted. Credits use the CPP, CPP2, and EI amounts in the file, and QPP and QPIP when the place is Quebec.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'Taxable income, federal tax after credits, provincial or territorial tax after credits, total tax, average rate, marginal rate, and after-tax income. A balance appears when tax already deducted is set. Quebec also shows the workers deduction.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Sales tax', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'GST, HST, PST, or QST on a standard taxable amount. Rate segments in the file run from 2022 through 2026, and the date selects the segment. Vehicles, real property, and exempt supplies are not covered.', 'bomcas-tax-calculators-canada' ),
				'where'   => $places,
				'inputs'  => __( 'Place, amount, date, and whether to add tax or remove it from a total.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'Amount before tax, GST, PST or RST, HST, QST, tax, and the total.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Mortgage payment', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'A blended payment from the numbers the visitor types. The plugin does not ship a posted mortgage rate.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'Not tied to a province. Compounding defaults to the Canadian semi-annual fixed-rate convention.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Price, down payment, annual interest rate, amortization in years, payments per year (monthly, biweekly, or weekly), and compounding (semi-annual or monthly).', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'Principal, payment, number of payments, and interest over the amortization.', 'bomcas-tax-calculators-canada' ),
			),
		);
	}

	/**
	 * @return array<int,array<string,string>>
	 */
	public static function pro_tools() {
		$places = __( 'The 13 provinces and territories, where that tool has a rate row for the year.', 'bomcas-tax-calculators-canada' );
		return array(
			array(
				'title'   => __( 'Payroll deductions', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'CPP, CPP2, EI, and income tax on a regular pay. Quebec uses QPP and QPIP where those amounts are in the payroll file.', 'bomcas-tax-calculators-canada' ),
				'where'   => $places,
				'inputs'  => __( 'Province or territory, pay, and the pay frequency the payroll screen asks for.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The deductions and the net pay for that period. It is an estimate, not a source deduction.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Salary after tax', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Take-home pay after tax, CPP, and EI for the year in the payroll file.', 'bomcas-tax-calculators-canada' ),
				'where'   => $places,
				'inputs'  => __( 'Province or territory and salary.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'Tax, payroll contributions, and the amount left.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Quebec withholding', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Quebec income tax withheld on a paycheque.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'Quebec.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'The pay fields on that screen.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The Quebec withholding estimate.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Self-employed CPP', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'CPP on self-employment income.', 'bomcas-tax-calculators-canada' ),
				'where'   => $places,
				'inputs'  => __( 'Province or territory and self-employment income.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The contribution estimate.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Employer health tax', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Payroll tax for the provinces that have a row in the rate file. British Columbia uses a notch. Ontario phases an exemption out and then uses a rate band. Manitoba uses a notch. Newfoundland and Labrador taxes payroll above an exemption.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'British Columbia, Manitoba, Newfoundland and Labrador, and Ontario. A province that is not in the file is not estimated.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Province and payroll for the years the screen lists.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The tax for the years that have a rate row.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'EI benefits', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'A regular benefit from weekly insurable earnings, up to the maximum for the year you pick. The rate comes from the household file.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'The EI year rows in the rate file. Not a provincial benefit.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Year and average weekly insurable earnings.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The regular benefit estimate.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Canada child benefit', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'The federal benefit for the period named in the rate file. Provincial and territorial child benefits are not added.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'Federal. Shared custody uses half of the benefit when that box is checked.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Adjusted family net income, children under 6, children aged 6 to 17, and shared custody.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The federal benefit estimate.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Corporate tax', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Federal and provincial tax on active business income, using the small-business and general rates in the file. Quebec small-business tax stays at zero unless you confirm the corporation already qualifies. The hours and sector tests are not calculated.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'All 13 provinces and territories have a 2026 row. Some provinces also have earlier years in the file.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Province or territory, active business income, and the taxation-year start used for Quebec.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'Federal tax, provincial tax, and the combined estimate.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Salary versus dividend', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Compares salary with a non-eligible dividend using the gross-up and the dividend tax credits in the file. The dividend table is labelled as the 2025 tax year.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'Alberta, British Columbia, Manitoba, New Brunswick, Newfoundland and Labrador, Nova Scotia, the Northwest Territories, Nunavut, Ontario, Prince Edward Island, Saskatchewan, and Yukon. Quebec is not in that dividend table.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Province or territory and the amount to compare.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The salary result and the dividend result side by side.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Capital cost allowance', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'CCA for the class and cost you enter. Before 2024 the first-year base is one and a half times the addition. For 2024 to 2027 the half-year rule is suspended. From 2028 it returns. Classes 54, 55, and 56 are not offered.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'Classes 8, 10, 10.1, 43, and 50. Class 10.1 uses the ceiling in the rate file.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Class, cost, and the year.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The allowance for that year.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Capital gains', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Tax on a capital gain. The inclusion rate is the one in the rate file, applied to the gain, then taxed with your other income and your province. A principal residence is not included.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'Federal inclusion, plus the provincial tax tables used by the income-tax estimate.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Province, capital gain, and other income.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The taxable portion and the tax estimate.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Tax instalments', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Whether personal instalments are required. The file uses a $3,000 threshold, or $1,800 for Quebec, and the dates 15 March, 15 June, 15 September, and 15 December.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'Personal instalments. Quebec uses the lower threshold in the file.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Province and the net-tax amounts the screen asks for.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'Whether instalments apply, and the quarterly dates.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'CRA penalty and interest', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'A late-filing penalty and prescribed interest. The file includes the 2026 prescribed-rate quarters.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'The quarters present in the prescribed-rate file.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Balance, due date, filing date, and payment date.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The penalty and the interest estimate.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Donations', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'The federal and provincial credit on a gift, using the donation rates for the province.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'The provinces present in the donation-rate table.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Province, donations, and taxable income.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The credit estimate.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Car allowance', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'The prescribed cents-per-kilometre amount. Provinces share one pair of rates. The territories use a higher pair.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'One rate pair for the provinces and one pair for Yukon, the Northwest Territories, and Nunavut.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Year, business kilometres, and whether the travel is in a territory.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The allowance amount.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Vehicle purchase', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Tax on a dealer sale or a private purchase. A dealer sale uses the sales-tax rates. A private sale uses the provincial private-sale rule in the file.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'The provinces and territories that have a vehicle row. British Columbia also asks for passenger or non-passenger.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Who is selling, price, province, body type where it applies, and the fee and financing fields that are shown.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The tax and the amount due.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Lease or buy', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Compares leasing a vehicle with buying it from the payments, term, and tax you enter.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'Uses the sales-tax rates for the province you pick. It is not a lender quote.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Province and the lease and purchase fields on the screen.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The two totals side by side.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Mortgage stress-test payment', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'The stress-test payment and the common debt-service limits from the income, debts, price, and rate you type. It does not approve a mortgage.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'Not a provincial tax. The qualifying rate is the one you type or the floor in the rate file, whichever the screen uses.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Income, debts, price, down payment, and rate.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The stress-test payment and the debt-service ratios.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'CMHC insurance', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'The mortgage insurance premium from the down payment, using the premium bands in the rate file, including the homebuyer band when that box applies.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'The premium table in the file. It is not a CMHC approval.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Price, down payment, and whether the homebuyer band applies.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The premium and the insured mortgage.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Land transfer tax', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Property transfer tax. British Columbia and Ontario use the brackets in the file. Toronto adds the municipal tax when that box is checked. Manitoba, New Brunswick, Nova Scotia, and Prince Edward Island use a rate you type. Quebec uses brackets you type. If those boxes are blank, no tax is shown.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'British Columbia and Ontario have built-in brackets. The other provinces named above need a rate you supply. A province with no rule is not estimated.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Price or value, registration date, first-time buyer, and the province boxes that appear: residential value over $3 million in British Columbia, single-family and Toronto in Ontario, a typed rate in Manitoba, New Brunswick, Nova Scotia, and Prince Edward Island, or typed brackets in Quebec.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The transfer tax.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'RRSP room', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Room from last year’s earned income and the dollar limit in the rate file, minus a pension adjustment you type. Other adjustments on a notice of assessment are not included.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'The RRSP rate and dollar limit in the household file.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Year, earned income last year, and pension adjustment.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The deduction limit from those inputs.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'TFSA room', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'Adds the annual limits for the years you choose, then adds last year’s withdrawals and subtracts this year’s contributions.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'The annual limits in the household file.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'First year, last year, withdrawals last year, and contributions already made this year.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The room from those years and amounts.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'FHSA', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'How much you can still put in a first home savings account. The first year uses the annual amount in the file. Later years can include unused room up to that same annual amount, and the lifetime limit in the file caps the total.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'The FHSA amounts in the household file.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Whether this is the year the account was opened, unused room from last year, and the amount already contributed this year.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The room still available.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Old Age Security', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'The recovery tax when income is above the threshold in the rate file. The repayment is capped at the pension you enter.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'The OAS threshold for the year you pick.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Year, net income, and OAS pension received.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The recovery tax.', 'bomcas-tax-calculators-canada' ),
			),
			array(
				'title'   => __( 'Quebec welcome tax', 'bomcas-tax-calculators-canada' ),
				'what'    => __( 'The municipal welcome tax on a Quebec property transfer, from the brackets in the household file.', 'bomcas-tax-calculators-canada' ),
				'where'   => __( 'Quebec.', 'bomcas-tax-calculators-canada' ),
				'inputs'  => __( 'Year, price in the deed, and municipal market value if it is higher.', 'bomcas-tax-calculators-canada' ),
				'outputs' => __( 'The welcome tax.', 'bomcas-tax-calculators-canada' ),
			),
		);
	}

	/**
	 * @return string
	 */
	private static function platform() {
		$html  = '<h2>' . esc_html__( 'Shortcodes, blocks, and settings', 'bomcas-tax-calculators-canada' ) . '</h2>';
		$html .= '<p>' . esc_html__( 'Lite accepts two attributes. id chooses the calculator. province is a two-letter code and is optional. Income tax and sales tax default to British Columbia. The ids are income-tax, sales-tax, and mortgage-payment.', 'bomcas-tax-calculators-canada' ) . '</p>';
		$html .= '<ul class="bomcas-feature-list">';
		$codes = array(
			'[bomcas_calculator id="income-tax" province="ON"]',
			'[bomcas_calculator id="sales-tax" province="BC"]',
			'[bomcas_calculator id="mortgage-payment"]',
			'[bomcas_calculator_pro id="payroll-deductions" province="ON"]',
			'[bomcas_place tool="income-tax" code="BC" part="all"]',
			'[bomcas_custom id="your-id"]',
			'[bomcas_compare left="BC" right="AB"]',
			'[bomcas_pack id="realtor"]',
		);
		foreach ( $codes as $code ) {
			$html .= '<li><code>' . esc_html( $code ) . '</code></li>';
		}
		$html  .= '</ul>';
		$html  .= '<p>' . esc_html__( 'The block BOMCAS Tax Calculators – Canada (bomcas/calculator) has the attributes calculator and province. A second block, bomcas/calculator-any, has the same two attributes and renders the same shortcode. Elementor, Divi, and Beaver Builder widgets register only when that builder is active.', 'bomcas-tax-calculators-canada' ) . '</p>';
		$html  .= '<p>' . esc_html__( 'Pro place pages use bomcas_place. tool is the calculator id, code is the province or territory, and part is all or a section of the guide. bomcas_custom runs a formula you save in Pro. The allowed functions are fed_tax, prov_tax, sales_tax, and payroll. bomcas_compare takes two province codes. bomcas_pack publishes a set of pages. The pack ids are realtor, dealer, payroll, advisor, ecommerce, lawyer, immigration, and blogger.', 'bomcas-tax-calculators-canada' ) . '</p>';
		$html  .= '<h2>' . esc_html__( 'Display, rates, and privacy', 'bomcas-tax-calculators-canada' ) . '</h2>';
		$html  .= '<ul class="bomcas-feature-list">';
		$points = array(
			__( 'Each field has a label. Currency and percent marks are hidden from assistive technology so the number is read once. The result is a definition list.', 'bomcas-tax-calculators-canada' ),
			__( 'The layout fits a phone. The same page is the one a search engine can index, because the guide is ordinary HTML.', 'bomcas-tax-calculators-canada' ),
			__( 'The arithmetic is a small script on the page. It does not call another website to get the result.', 'bomcas-tax-calculators-canada' ),
			__( 'English, French (Canada), and Spanish (US) are included. Amounts follow that locale. French pages name RRQ, RQAP, and TVQ.', 'bomcas-tax-calculators-canada' ),
			__( 'Calculators, Rates imports or exports a year file. The next view uses the new numbers. You can turn a calculator or a whole country off.', 'bomcas-tax-calculators-canada' ),
			__( 'An optional credit line stays off until you save it. Page text you wrote under Calculators, Page content is left alone.', 'bomcas-tax-calculators-canada' ),
			__( 'Typed values stay in the browser. A view count is saved on your own site. Nothing is sent to Google unless you save a measurement id, and then one event is sent when a calculation runs.', 'bomcas-tax-calculators-canada' ),
			__( 'Email and PDF run only after the visitor submits the optional form. Pro can also print or save a PDF from the button on the calculator.', 'bomcas-tax-calculators-canada' ),
			__( 'Professional and Agency plans record a white-label flag. The privacy screen can hide the licence screens from people who are not on the allow list.', 'bomcas-tax-calculators-canada' ),
		);
		foreach ( $points as $point ) {
			$html .= '<li>' . esc_html( $point ) . '</li>';
		}
		$html .= '</ul>';
		$html .= '<h2>' . esc_html__( 'Licence tiers', 'bomcas-tax-calculators-canada' ) . '</h2>';
		$html .= '<p>' . esc_html__( 'Lite has no licence and no site cap. Pro is billed in Canadian dollars. Personal covers 1 site, Professional covers 5 sites, and Agency covers 25 sites. Monthly, quarterly, and yearly prices are on the pricing page. While the licence is active or in the grace period, Pro can check for updates. Support for that plan runs for the same period. Cancelling stops the next renewal. Access lasts until the period already paid for ends. A copy you already installed keeps running.', 'bomcas-tax-calculators-canada' ) . '</p>';
		return $html;
	}

	/**
	 * @return string
	 */
	private static function uses() {
		$html      = '<h2 id="uses">' . esc_html__( 'What you can use it for', 'bomcas-tax-calculators-canada' ) . '</h2>';
		$html     .= '<p>' . esc_html__( 'The calculator answers a question the visitor already has, on your page. It does not replace a signed engagement, a mortgage approval, or a payroll filing.', 'bomcas-tax-calculators-canada' ) . '</p>';
		$audiences = array(
			__( 'Accounting and tax firms', 'bomcas-tax-calculators-canada' ) => __( 'Put income tax or sales tax on a service page so a prospect sees the scope before they write to you.', 'bomcas-tax-calculators-canada' ),
			__( 'Bookkeepers', 'bomcas-tax-calculators-canada' ) => __( 'Use sales tax and, with Pro, payroll on a client resource page.', 'bomcas-tax-calculators-canada' ),
			__( 'Payroll providers', 'bomcas-tax-calculators-canada' ) => __( 'Professional and Agency include payroll deductions and take-home pay. Personal does not. None of them file a remittance.', 'bomcas-tax-calculators-canada' ),
			__( 'Financial advisors', 'bomcas-tax-calculators-canada' ) => __( 'Pro RRSP, TFSA, FHSA, and capital gains give a meeting a starting number. They are not a financial plan.', 'bomcas-tax-calculators-canada' ),
			__( 'Mortgage brokers and realtors', 'bomcas-tax-calculators-canada' ) => __( 'The mortgage payment, and Pro land transfer tax and CMHC premium, sit on a listing or a pre-approval page.', 'bomcas-tax-calculators-canada' ),
			__( 'HR, recruiting, and job boards', 'bomcas-tax-calculators-canada' ) => __( 'Salary after tax, in Pro, and the Lite paycheck show what a posted salary means after tax.', 'bomcas-tax-calculators-canada' ),
			__( 'Banks and credit unions', 'bomcas-tax-calculators-canada' ) => __( 'A payment or sales-tax estimate on a product page, with your own contact path beside it.', 'bomcas-tax-calculators-canada' ),
			__( 'Finance writers and news sites', 'bomcas-tax-calculators-canada' ) => __( 'A tool page that stays useful after the article is published, and that other sites can link to.', 'bomcas-tax-calculators-canada' ),
			__( 'Non-profits', 'bomcas-tax-calculators-canada' ) => __( 'The donations credit, in Pro, next to a giving page. It is not a charitable receipt.', 'bomcas-tax-calculators-canada' ),
			__( 'Government and education', 'bomcas-tax-calculators-canada' ) => __( 'A classroom or public-information page that shows how a rate is applied, with the source link under the result.', 'bomcas-tax-calculators-canada' ),
			__( 'Employer intranets', 'bomcas-tax-calculators-canada' ) => __( 'Payroll and the car allowance, in Pro, on an internal page. The numbers still stay an estimate.', 'bomcas-tax-calculators-canada' ),
		);
		foreach ( $audiences as $title => $text ) {
			$html .= '<h3>' . esc_html( $title ) . '</h3><p>' . esc_html( $text ) . '</p>';
		}
		$html .= '<h2>' . esc_html__( 'Where to put it', 'bomcas-tax-calculators-canada' ) . '</h2>';
		$html .= '<ul class="bomcas-feature-list">';
		foreach ( array(
			__( 'A service page, under the description of the work you do.', 'bomcas-tax-calculators-canada' ),
			__( 'A blog post that explains one tax, with the calculator under the explanation.', 'bomcas-tax-calculators-canada' ),
			__( 'A landing page aimed at one audience, such as employers or home buyers.', 'bomcas-tax-calculators-canada' ),
			__( 'A lead page, with the contact or booking link beside the result.', 'bomcas-tax-calculators-canada' ),
			__( 'A client portal, for people who already work with you.', 'bomcas-tax-calculators-canada' ),
			__( 'A resource hub that links each calculator to its own address.', 'bomcas-tax-calculators-canada' ),
			__( 'A sidebar, using the same shortcode with a province already chosen.', 'bomcas-tax-calculators-canada' ),
		) as $item ) {
			$html .= '<li>' . esc_html( $item ) . '</li>';
		}
		$html .= '</ul>';
		$html .= '<h2>' . esc_html__( 'What changes on the site', 'bomcas-tax-calculators-canada' ) . '</h2>';
		$html .= '<ul class="bomcas-feature-list">';
		foreach ( array(
			__( 'People stay on your page instead of leaving for another calculator.', 'bomcas-tax-calculators-canada' ),
			__( 'The result is a reason to use the contact or booking link next to it.', 'bomcas-tax-calculators-canada' ),
			__( 'A page that answers a real question can be indexed and linked.', 'bomcas-tax-calculators-canada' ),
			__( 'The firm name is on the same page as the numbers, which is easier to trust than a tool on someone else’s site.', 'bomcas-tax-calculators-canada' ),
			__( 'Fewer messages ask for a basic rate that the page already shows.', 'bomcas-tax-calculators-canada' ),
		) as $item ) {
			$html .= '<li>' . esc_html( $item ) . '</li>';
		}
		$html .= '</ul>';
		return $html;
	}

	/**
	 * @return string
	 */
	private static function setup() {
		$html  = '<h2 id="setup">' . esc_html__( 'How to use it on your website', 'bomcas-tax-calculators-canada' ) . '</h2>';
		$html .= '<ol class="ae-steps">';
		$steps = array(
			__( 'Install', 'bomcas-tax-calculators-canada' )   => __( 'Upload the Lite zip from Plugins, Add New, Upload Plugin, or copy the bomcas-tax-calculators-canada folder into wp-content/plugins. Keep that folder name.', 'bomcas-tax-calculators-canada' ),
			__( 'Activate', 'bomcas-tax-calculators-canada' )  => __( 'Activate BOMCAS Tax Calculators – Canada. Open Calculators, Help, and follow Getting started.', 'bomcas-tax-calculators-canada' ),
			__( 'Add it', 'bomcas-tax-calculators-canada' )    => __( 'Insert the BOMCAS Tax Calculators – Canada block, or paste a shortcode. Set the calculator id and, if you want, the province.', 'bomcas-tax-calculators-canada' ),
			__( 'Configure', 'bomcas-tax-calculators-canada' ) => __( 'Choose the language, turn tools on or off, and import a rate file when the year changes. Leave the Google measurement id empty if you do not want that event.', 'bomcas-tax-calculators-canada' ),
			__( 'Upgrade', 'bomcas-tax-calculators-canada' )   => __( 'Install Pro, then paste the licence key from your Accountants Expert account. Personal, Professional, and Agency set the site count. Updates run while the licence is active or in grace.', 'bomcas-tax-calculators-canada' ),
		);
		$n     = 1;
		foreach ( $steps as $title => $text ) {
			$html .= '<li><span>' . esc_html( (string) $n ) . '</span><h3>' . esc_html( $title ) . '</h3><p>' . esc_html( $text ) . '</p></li>';
			++$n;
		}
		$html .= '</ol>';
		$html .= '<h2>' . esc_html__( 'Example pages', 'bomcas-tax-calculators-canada' ) . '</h2>';
		$html .= '<ul class="bomcas-feature-list">';
		foreach ( array(
			__( 'An income-tax page for Ontario: the shortcode with province ON, a short note that the result is an estimate, and a link to book a review.', 'bomcas-tax-calculators-canada' ),
			__( 'A GST page for a shop: sales tax with the province already set, and the direction left on Add tax.', 'bomcas-tax-calculators-canada' ),
			__( 'A recruiter page: Pro salary after tax under a salary band, with the application link beside it.', 'bomcas-tax-calculators-canada' ),
			__( 'A home-buyer page: the mortgage payment, plus Pro land transfer tax where the province is British Columbia or Ontario.', 'bomcas-tax-calculators-canada' ),
		) as $item ) {
			$html .= '<li>' . esc_html( $item ) . '</li>';
		}
		$html .= '</ul>';
		return $html;
	}

	/**
	 * @return string
	 */
	private static function faq_html() {
		$html = '<h2 id="faq">' . esc_html__( 'Questions', 'bomcas-tax-calculators-canada' ) . '</h2><div class="ae-faq">';
		foreach ( self::faqs() as $pair ) {
			$html .= '<details><summary>' . esc_html( $pair[0] ) . '</summary><p>' . esc_html( $pair[1] ) . '</p></details>';
		}
		$html .= '</div>';
		return $html;
	}

	/**
	 * @param string $title   Heading.
	 * @param string $what    What it calculates.
	 * @param string $where   Coverage.
	 * @param string $inputs  Inputs.
	 * @param string $outputs Outputs.
	 * @return string
	 */
	private static function calculator( $title, $what, $where, $inputs, $outputs ) {
		$html  = '<h3>' . esc_html( $title ) . '</h3>';
		$html .= '<p>' . esc_html( $what ) . '</p>';
		$html .= '<p><strong>' . esc_html__( 'Covers', 'bomcas-tax-calculators-canada' ) . '</strong> ' . esc_html( $where ) . '</p>';
		$html .= '<p><strong>' . esc_html__( 'Inputs', 'bomcas-tax-calculators-canada' ) . '</strong> ' . esc_html( $inputs ) . '</p>';
		$html .= '<p><strong>' . esc_html__( 'Outputs', 'bomcas-tax-calculators-canada' ) . '</strong> ' . esc_html( $outputs ) . '</p>';
		return $html;
	}
}
