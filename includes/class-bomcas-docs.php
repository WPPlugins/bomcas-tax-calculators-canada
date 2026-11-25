<?php
/**
 * In-plugin guide. Articles are stored in this file and render in wp-admin.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Help screens for the calculator plugin.
 */
class Bomcas_Docs {

	/**
	 * Hooks.
	 */
	public static function boot() {
		add_action( 'admin_post_bomcas_docs_ticket', array( __CLASS__, 'save_ticket' ) );
	}

	/**
	 * Submenu. Registered with the other calculator screens.
	 */
	public static function menu() {
		add_submenu_page(
			'bomcas-calculators-home',
			__( 'Help', 'bomcas-tax-calculators-canada' ),
			__( 'Help', 'bomcas-tax-calculators-canada' ),
			'manage_options',
			'bomcas-help',
			array( __CLASS__, 'screen' )
		);
	}

	/**
	 * @param string $slug Article id. Empty is the index.
	 * @return string
	 */
	public static function url( $slug = '' ) {
		$url = admin_url( 'admin.php?page=bomcas-help' );
		if ( '' !== $slug && 'help' !== $slug ) {
			$url = add_query_arg( 'article', $slug, $url );
		}
		return $url;
	}

	/**
	 * @param string $slug Article id.
	 * @return bool
	 */
	public static function has( $slug ) {
		return isset( self::catalog()[ $slug ] );
	}

	/**
	 * @param string $slug Article id.
	 * @return string
	 */
	public static function title( $slug ) {
		$all = self::catalog();
		return isset( $all[ $slug ]['title'] ) ? $all[ $slug ]['title'] : '';
	}

	/**
	 * Help screen.
	 */
	public static function screen() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// Read-only guide filters. They do not change stored data.
		$slug  = isset( $_GET['article'] ) ? sanitize_key( wp_unslash( $_GET['article'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$query = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		Bomcas_Admin_UI::enqueue( BOMCAS_CALC_URL, BOMCAS_CALC_VERSION );
		wp_add_inline_script(
			'bomcas-admin',
			'document.addEventListener("input",function(e){if(!e.target.matches("[data-doc-search]")){return;}var q=e.target.value.toLowerCase();document.querySelectorAll("[data-doc-card]").forEach(function(card){var hay=(card.getAttribute("data-doc-text")||"");card.hidden=q!==""&&hay.indexOf(q)===-1;});});'
		);
		$article = self::has( $slug ) ? $slug : '';
		Bomcas_Admin_UI::open(
			array(
				'product' => __( 'BOMCAS Tax Calculators – Canada', 'bomcas-tax-calculators-canada' ),
				'kicker'  => __( 'Help · version ', 'bomcas-tax-calculators-canada' ) . BOMCAS_CALC_VERSION,
				'badge'   => __( 'Guide', 'bomcas-tax-calculators-canada' ),
				'tabs'    => function_exists( 'bomcas_calc_buyer_tabs' ) ? bomcas_calc_buyer_tabs( 'help' ) : array(),
				'doc'     => 'help',
			)
		);
		if ( '' !== $article ) {
			self::render_article( self::catalog()[ $article ] );
		} else {
			self::render_index( $query );
		}
		Bomcas_Admin_UI::close();
	}

	/**
	 * @param string $query Search text.
	 */
	private static function render_index( $query ) {
		echo '<section class="bomcas-admin__card bomcas-doc">';
		echo '<h2>' . esc_html__( 'Help and documentation', 'bomcas-tax-calculators-canada' ) . '</h2>';
		echo '<p>' . esc_html__( 'These articles are part of the plugin. They open here in wp-admin and do not call another site.', 'bomcas-tax-calculators-canada' ) . '</p>';
		echo '<form method="get" class="bomcas-doc__search">';
		echo '<input type="hidden" name="page" value="bomcas-help" />';
		echo '<label>' . esc_html__( 'Search the guide', 'bomcas-tax-calculators-canada' ) . '<input type="search" name="q" data-doc-search value="' . esc_attr( $query ) . '" /></label>';
		submit_button( __( 'Search', 'bomcas-tax-calculators-canada' ), 'secondary', '', false );
		echo '</form>';
		$groups = array();
		foreach ( self::catalog() as $id => $article ) {
			if ( 'help' === $id ) {
				continue;
			}
			$blob = strtolower( $article['title'] . ' ' . $article['summary'] . ' ' . self::plain( $article ) );
			if ( '' !== $query && false === strpos( $blob, strtolower( $query ) ) ) {
				continue;
			}
			$groups[ $article['group'] ][] = $article;
		}
		if ( ! $groups ) {
			echo '<p class="bomcas-empty"><strong>' . esc_html__( 'No articles match that search.', 'bomcas-tax-calculators-canada' ) . '</strong></p>';
		}
		foreach ( $groups as $group => $articles ) {
			echo '<h3>' . esc_html( $group ) . '</h3><div class="bomcas-doc__grid">';
			foreach ( $articles as $article ) {
				$text = strtolower( $article['title'] . ' ' . $article['summary'] . ' ' . self::plain( $article ) );
				echo '<a class="bomcas-doc__card" data-doc-card data-doc-text="' . esc_attr( $text ) . '" href="' . esc_url( self::url( $article['id'] ) ) . '">';
				echo '<strong>' . esc_html( $article['title'] ) . '</strong>';
				echo '<span>' . esc_html( $article['summary'] ) . '</span></a>';
			}
			echo '</div>';
		}
		echo '</section>';
	}

	/**
	 * @param array $article Article.
	 */
	private static function render_article( $article ) {
		echo '<article class="bomcas-admin__card bomcas-doc">';
		echo '<p><a href="' . esc_url( self::url() ) . '">' . esc_html__( 'All articles', 'bomcas-tax-calculators-canada' ) . '</a></p>';
		echo '<h2>' . esc_html( $article['title'] ) . '</h2>';
		echo '<p class="bomcas-doc__summary">' . esc_html( $article['summary'] ) . '</p>';
		foreach ( $article['blocks'] as $block ) {
			if ( ! empty( $block['h'] ) ) {
				echo '<h3>' . esc_html( $block['h'] ) . '</h3>';
			}
			if ( ! empty( $block['p'] ) ) {
				echo '<p>' . esc_html( $block['p'] ) . '</p>';
			}
			if ( ! empty( $block['ul'] ) ) {
				echo '<ul>';
				foreach ( $block['ul'] as $item ) {
					echo '<li>' . esc_html( $item ) . '</li>';
				}
				echo '</ul>';
			}
			if ( ! empty( $block['diagram'] ) ) {
				echo wp_kses( self::diagram( $block['diagram'] ), bomcas_calc_allowed_html() );
			}
			if ( ! empty( $block['links'] ) ) {
				echo '<ul>';
				foreach ( $block['links'] as $link ) {
					echo '<li><a href="' . esc_url( $link['url'] ) . '">' . esc_html( $link['label'] ) . '</a></li>';
				}
				echo '</ul>';
			}
		}
		if ( 'support' === $article['id'] ) {
			self::ticket_form();
		}
		echo '</article>';
	}

	/**
	 * Local support note. The message stays on this site.
	 */
	private static function ticket_form() {
		$saved = isset( $_GET['ticket'] ) ? sanitize_key( wp_unslash( $_GET['ticket'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( '1' === $saved ) {
			echo '<p class="bomcas-doc__note">' . esc_html__( 'The note is saved on this site and a copy was mailed to the site administrator.', 'bomcas-tax-calculators-canada' ) . '</p>';
		}
		echo '<h3>' . esc_html__( 'Write a note', 'bomcas-tax-calculators-canada' ) . '</h3>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="bomcas-form">';
		wp_nonce_field( 'bomcas_docs_ticket' );
		echo '<input type="hidden" name="action" value="bomcas_docs_ticket" />';
		echo '<p><label>' . esc_html__( 'Subject', 'bomcas-tax-calculators-canada' ) . '<input name="subject" required /></label></p>';
		echo '<p class="is-wide"><label>' . esc_html__( 'What happened', 'bomcas-tax-calculators-canada' ) . '<textarea name="message" rows="5" required></textarea></label></p>';
		submit_button( __( 'Save the note', 'bomcas-tax-calculators-canada' ) );
		echo '</form>';
		$tickets = get_option( 'bomcas_support_tickets', array() );
		if ( is_array( $tickets ) && $tickets ) {
			echo '<h3>' . esc_html__( 'Notes on this site', 'bomcas-tax-calculators-canada' ) . '</h3><ul>';
			foreach ( array_slice( $tickets, 0, 10 ) as $ticket ) {
				echo '<li><strong>' . esc_html( $ticket['subject'] ?? '' ) . '</strong> — ' . esc_html( isset( $ticket['time'] ) ? wp_date( 'j M Y', (int) $ticket['time'] ) : '' ) . '<br />' . esc_html( $ticket['message'] ?? '' ) . '</li>';
			}
			echo '</ul>';
		}
	}

	/**
	 * Save a support note locally and mail the site admin.
	 */
	public static function save_ticket() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot save a support note.', 'bomcas-tax-calculators-canada' ) );
		}
		check_admin_referer( 'bomcas_docs_ticket' );
		$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
		$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
		if ( '' === $subject || '' === $message ) {
			wp_safe_redirect( self::url( 'support' ) );
			exit;
		}
		$tickets = get_option( 'bomcas_support_tickets', array() );
		$tickets = is_array( $tickets ) ? $tickets : array();
		array_unshift(
			$tickets,
			array(
				'time'    => time(),
				'user'    => get_current_user_id(),
				'subject' => $subject,
				'message' => $message,
			)
		);
		update_option( 'bomcas_support_tickets', array_slice( $tickets, 0, 30 ), false );
		wp_mail( get_option( 'admin_email' ), '[BOMCAS] ' . $subject, $message );
		wp_safe_redirect( add_query_arg( 'ticket', '1', self::url( 'support' ) ) );
		exit;
	}

	/**
	 * @param array $article Article.
	 * @return string
	 */
	private static function plain( $article ) {
		$text = '';
		foreach ( $article['blocks'] as $block ) {
			$text .= ' ' . ( $block['h'] ?? '' ) . ' ' . ( $block['p'] ?? '' );
			if ( ! empty( $block['ul'] ) ) {
				$text .= ' ' . implode( ' ', $block['ul'] );
			}
		}
		return $text;
	}

	/**
	 * @param string $id Diagram id.
	 * @return string
	 */
	private static function diagram( $id ) {
		$charts = array(
			'year'  => array(
				__( 'Today', 'bomcas-tax-calculators-canada' ),
				__( 'Effective dates', 'bomcas-tax-calculators-canada' ),
				__( 'Newest year', 'bomcas-tax-calculators-canada' ),
				__( 'Page tokens', 'bomcas-tax-calculators-canada' ),
			),
			'calc'  => array(
				__( 'Amount you type', 'bomcas-tax-calculators-canada' ),
				__( 'Browser engine', 'bomcas-tax-calculators-canada' ),
				__( 'Rate file', 'bomcas-tax-calculators-canada' ),
				__( 'Estimate', 'bomcas-tax-calculators-canada' ),
			),
			'rates' => array(
				__( 'Bundled file', 'bomcas-tax-calculators-canada' ),
				__( 'Your override', 'bomcas-tax-calculators-canada' ),
				__( 'Pushed package', 'bomcas-tax-calculators-canada' ),
				__( 'Live page', 'bomcas-tax-calculators-canada' ),
			),
		);
		if ( ! isset( $charts[ $id ] ) ) {
			return '';
		}
		$steps = $charts[ $id ];
		$svg   = '<svg class="bomcas-doc__diagram" viewBox="0 0 720 92" role="img" width="720" height="92">';
		$svg  .= '<title>' . esc_html( implode( ' → ', $steps ) ) . '</title>';
		foreach ( $steps as $index => $label ) {
			$x    = 16 + ( $index * 178 );
			$svg .= '<rect x="' . (int) $x . '" y="22" width="150" height="48" rx="10" fill="#e7f4fb" stroke="#0092db"></rect>';
			$svg .= '<text x="' . (int) ( $x + 75 ) . '" y="50" text-anchor="middle" fill="#001729" font-size="13" font-family="Segoe UI, sans-serif">' . esc_html( $label ) . '</text>';
			if ( $index < count( $steps ) - 1 ) {
				$svg .= '<path d="M' . (int) ( $x + 154 ) . ' 46h16" fill="none" stroke="#ff5e15" stroke-width="2"></path>';
			}
		}
		$svg .= '</svg>';
		return $svg;
	}

	/**
	 * Every article shipped with Lite.
	 *
	 * @return array<string,array>
	 */
	public static function catalog() {
		static $cache = null;
		if ( null !== $cache ) {
			return $cache;
		}
		$rows  = array(
			self::article(
				'help',
				__( 'How to use this guide', 'bomcas-tax-calculators-canada' ),
				__( 'Start', 'bomcas-tax-calculators-canada' ),
				__( 'Search, open an article, and jump here from any calculator screen.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'The guide is stored in the plugin. Opening an article does not contact BOMCAS, a payment service, or a rate server.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Use the search box to match a title or a sentence. Each other admin screen has a Learn more link to the article for that screen. Some field tips include the same link.', 'bomcas-tax-calculators-canada' ) ),
					array(
						'ul' => array(
							__( 'Calculators → Help is this index.', 'bomcas-tax-calculators-canada' ),
							__( 'An article stays available when the site is offline.', 'bomcas-tax-calculators-canada' ),
							__( 'Support notes you write are saved on this site.', 'bomcas-tax-calculators-canada' ),
						),
					),
				)
			),
			self::article(
				'about',
				__( 'About', 'bomcas-tax-calculators-canada' ),
				__( 'Start', 'bomcas-tax-calculators-canada' ),
				__( 'Who writes BOMCAS Tax Calculators – Canada.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'BOMCAS Tax Calculators – Canada is a Canadian tax and payroll plugin for WordPress. Developed by BOMCAS, owned by Dellendo Farquharson.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'The calculators are estimates for the year stored in the rate file. They are not a filing, a ruling, or a Canada Revenue Agency service. What a visitor types stays in the browser unless they later ask for an email or a PDF.', 'bomcas-tax-calculators-canada' ) ),
					array(
						'links' => array(
							array(
								'url'   => 'https://accountantsexpert.com/',
								'label' => __( 'Publisher site', 'bomcas-tax-calculators-canada' ),
							),
						),
					),
				)
			),
			self::article(
				'getting-started',
				__( 'Getting started', 'bomcas-tax-calculators-canada' ),
				__( 'Start', 'bomcas-tax-calculators-canada' ),
				__( 'Install, run the setup wizard, and place the first calculator.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'h' => __( 'Install', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Upload the plugin zip from Plugins → Add New, or copy the folder into wp-content/plugins, then activate BOMCAS Tax Calculators – Canada. The calculators run without an account. A licence is optional and is only for updates and support on the paid add-on.', 'bomcas-tax-calculators-canada' ) ),
					array( 'h' => __( 'Setup wizard', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Calculators → Setup asks for a country focus, an optional industry pack, and a booking page. Country focus is a label for the wizard. It does not hide the other country and it does not decide which pages get created. Leave both country toggles on unless you mean to hide a country.', 'bomcas-tax-calculators-canada' ) ),
					array(
						'ul' => array(
							__( 'Add a calculator with the shortcode or the block. The visitor’s numbers stay in the browser.', 'bomcas-tax-calculators-canada' ),
							__( 'Turn the credit on under Settings only if you want the BOMCAS Canada line. It is off until you save it.', 'bomcas-tax-calculators-canada' ),
							__( 'Open Overview for the licence state, the rate vintage, and recent leads.', 'bomcas-tax-calculators-canada' ),
						),
					),
					array( 'diagram' => 'calc' ),
				)
			),
			self::article(
				'calculators',
				__( 'Calculators', 'bomcas-tax-calculators-canada' ),
				__( 'Calculators', 'bomcas-tax-calculators-canada' ),
				__( 'What each calculator does, and which official table it reads.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'Every estimate is computed in the visitor’s browser from the rate file for the year in force. The result is not a filing, a ruling, or a Canada Revenue Agency service. Credits and residency that the form does not ask for are left out.', 'bomcas-tax-calculators-canada' ) ),
					array( 'diagram' => 'calc' ),
					array( 'h' => __( 'Sales tax', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Adds or removes GST, HST, PST, or QST on a standard taxable amount for the date you enter. Nova Scotia’s change of rate is a dated segment in the sales-tax file, so a date before that day uses the earlier rate. Vehicles, real property, and exempt supplies are not covered.', 'bomcas-tax-calculators-canada' ) ),
					array( 'h' => __( 'Income tax', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Canadian income tax applies the federal and provincial brackets, the basic personal amounts, and the payroll contributions that the personal-tax file lists for employment income. Quebec includes the federal abatement. Ontario includes surtax and the health premium. British Columbia includes the tax reduction.', 'bomcas-tax-calculators-canada' ) ),
					array( 'h' => __( 'Payroll, paycheck, and take-home pay', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'A Canadian cheque uses the pension and employment-insurance factors in the rate file, then the income-tax estimate divided by the number of pays. A British Columbia pay date on or after the July date stored in the dataset uses the July withholding rate from that same row, not the annual return rate.', 'bomcas-tax-calculators-canada' ) ),
					array( 'h' => __( 'Vehicle, land transfer, and mortgage', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Vehicle purchase uses the sales-tax rules for a dealer sale or a private purchase on the date you enter. Land transfer is estimated for British Columbia and Ontario from the published tables. The mortgage calculator uses the interest rate you type and the compounding convention you select. It does not look up a lender rate.', 'bomcas-tax-calculators-canada' ) ),
					array(
						'links' => array(
							array(
								'url'   => 'https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html',
								'label' => __( 'Canada Revenue Agency individual income tax rates', 'bomcas-tax-calculators-canada' ),
							),
							array(
								'url'   => 'https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/gst-hst-businesses.html',
								'label' => __( 'Canada Revenue Agency GST/HST', 'bomcas-tax-calculators-canada' ),
							),
						),
					),
				)
			),
			self::article(
				'toggles',
				__( 'Country and tool toggles', 'bomcas-tax-calculators-canada' ),
				__( 'Calculators', 'bomcas-tax-calculators-canada' ),
				__( 'Hide a country or a single calculator without deleting pages.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'Calculators → Toggles has a switch for Canada and a switch for each tool. Turning a tool off removes it from shortcodes and blocks. Existing pages stay published.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Setup → Country focus does not change these switches. Leave both countries on when the site should offer both. The preset that creates missing pages reads this list only to decide what a shortcode may render, not which pages to insert.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'rates',
				__( 'Rates', 'bomcas-tax-calculators-canada' ),
				__( 'Rates', 'bomcas-tax-calculators-canada' ),
				__( 'Edit a cell, import a sheet, and publish a new year without editing pages.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'Calculators → Rates shows one slice of a rate file: country, jurisdiction, rate type, and tax year. An empty cell does not create an override. A value you change is stored apart from the bundled file, so a plugin update does not wipe it.', 'bomcas-tax-calculators-canada' ) ),
					array( 'diagram' => 'rates' ),
					array( 'h' => __( 'Import and export', 'bomcas-tax-calculators-canada' ) ),
					array(
						'ul' => array(
							__( 'Export writes the current slice as CSV, Excel, or the JSON override list.', 'bomcas-tax-calculators-canada' ),
							__( 'An import starts as a dry run. Apply it only after the row errors are empty.', 'bomcas-tax-calculators-canada' ),
							__( 'Each row needs a file name, a path such as jurisdictions/BC/0/pst, and a number.', 'bomcas-tax-calculators-canada' ),
							__( 'JSON pasted into Import overrides replaces matching paths. It does not delete a path you leave out.', 'bomcas-tax-calculators-canada' ),
						),
					),
					array( 'h' => __( 'Pushed updates and the year in force', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'A signed package can be pulled when Pro is active or when Lite opt-in is saved. Your override still wins until you clear it. The year shown on pages is the newest year in the personal-tax file whose effective date is today or earlier. Year shown on calculator pages overrides that. Leave the box empty to stay automatic.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'If the calendar year is not in the file yet, pages name the latest year as the latest on file and wp-admin shows a warning. They do not present that older year as the current calendar year. Adding the new year updates every marked page on the next view. Saving, importing, pulling, or publishing rates also clears the page cache.', 'bomcas-tax-calculators-canada' ) ),
					array( 'diagram' => 'year' ),
				)
			),
			self::article(
				'tokens',
				__( 'Token reference', 'bomcas-tax-calculators-canada' ),
				__( 'Rates', 'bomcas-tax-calculators-canada' ),
				__( 'Placeholders that become the current rate, amount, or date.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'A token is a placeholder in curly braces. The page replaces it when someone views the page. The stored text keeps the token, so next year’s file changes the number without a page edit.', 'bomcas-tax-calculators-canada' ) ),
					array(
						'ul' => array(
							__( '{year} is the year in force. {year_label} adds “latest rates on file” when this calendar year is missing.', 'bomcas-tax-calculators-canada' ),
							__( '{updated} is the month the dataset was verified. {effective} is the date those rates took effect.', 'bomcas-tax-calculators-canada' ),
							__( '{bpa:BC} is that province’s basic personal amount. Use FED for the federal amount.', 'bomcas-tax-calculators-canada' ),
							__( '{rate:BC.income.first} and {rate:BC.income.top} are bracket rates, written as a percent.', 'bomcas-tax-calculators-canada' ),
							__( '{example:BC.income.42000.total} runs the engine. The 42000 is the wage you want illustrated, not a stored answer.', 'bomcas-tax-calculators-canada' ),
							__( '{deadline:balance_due} and {source:us.federal} read the calendar and the source title in the rate file.', 'bomcas-tax-calculators-canada' ),
						),
					),
					array( 'p' => __( 'On Page content, click the field, then a token button. Hover the button for the meaning. The same tokens work in a heading, an intro, a search title, a search description, and extra sections.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'page-content',
				__( 'Page content and SEO', 'bomcas-tax-calculators-canada' ),
				__( 'Pages', 'bomcas-tax-calculators-canada' ),
				__( 'Change a heading or add sections without losing them on update.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'Calculators → Page content lists pages marked as calculator pages. Heading, intro, and button label replace those parts only. Extra sections, when Pro is active, replace the generated guide and keep your HTML.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Search title and search description are used for the document title and the meta description when you fill them in. Leave them blank to keep the generated title. Put tokens in any of these fields when the sentence includes a year or an amount.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Reset to default deletes only that page’s edit record. It does not delete the page. A plugin update never writes over a saved edit. If the generated guide has changed since you saved, and you did not write extra sections, the page shows a short note that a newer default exists.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'shortcodes',
				__( 'Shortcodes, blocks, and the builder', 'bomcas-tax-calculators-canada' ),
				__( 'Pages', 'bomcas-tax-calculators-canada' ),
				__( 'Put a calculator in a page, a block, or a builder layout.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'The shortcode is [bomcas_calculator id="income-tax" province="BC"]. The id is income-tax, sales-tax, or mortgage-payment. The province code presets the place. Payroll and take-home pay on the Professional and Agency plans use [bomcas_calculator_pro id="payroll-deductions" province="BC"]. Personal does not include payroll.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'The block inserter lists BOMCAS Calculator. It stores the same id and place. The builder screen, when the add-on is active, lays out fields for a custom calculator. Numbers the visitor types are still not posted unless they ask for an email or a PDF.', 'bomcas-tax-calculators-canada' ) ),
					array(
						'ul' => array(
							__( 'A disabled tool renders nothing.', 'bomcas-tax-calculators-canada' ),
							__( 'The hero button on a managed calculator page goes to a service page, not to the contact page.', 'bomcas-tax-calculators-canada' ),
							__( 'Managed pages keep the shortcode in the database. The long guide is added at view time.', 'bomcas-tax-calculators-canada' ),
						),
					),
				)
			),
			self::article(
				'embed',
				__( 'Embed kit', 'bomcas-tax-calculators-canada' ),
				__( 'Pages', 'bomcas-tax-calculators-canada' ),
				__( 'Frame a calculator on another site you control.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'The embed screen is part of the optional add-on. List the host names that may load the frame, one per line. The snippet is an iframe or a script tag with a signature for that host and tool. A domain that is not on the list is refused.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'White-label can hide the credit inside the frame. The calculator still runs in the browser. Do not put a secret key in the snippet. The signature is a permission for that domain, not a licence key.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'leads',
				__( 'Leads and integrations', 'bomcas-tax-calculators-canada' ),
				__( 'Visitors', 'bomcas-tax-calculators-canada' ),
				__( 'The email form, what is stored, and where a copy can be sent.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'The email form under an estimate is off until you enable it on the Leads screen. A lead stores the name, email, calculator, and the result the visitor chose to send. Amounts they typed stay out of the server unless that form is sent. Old leads are deleted after the retention you set. One network address can send only so many leads an hour.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'The consent sentence is required. Extra questions are one per line. A copy of each lead can be mailed to the notify address. Webhooks, Mailchimp, HubSpot, and Google Sheets are filled in on this screen when the add-on is active. Lite keeps the lead list either way.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Usage counts how often a calculator is opened. It does not store the amounts. Filter the lead list by name, email, or calculator id. Delete removes that row.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'usage',
				__( 'Usage', 'bomcas-tax-calculators-canada' ),
				__( 'Visitors', 'bomcas-tax-calculators-canada' ),
				__( 'How often each calculator is opened.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'Calculators → Usage counts openings. It does not record wages, prices, or other amounts the visitor typed. The overview chart uses the same counts for the last 30 days. An empty chart means nothing has been opened in that window.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'booking',
				__( 'Booking', 'bomcas-tax-calculators-canada' ),
				__( 'Visitors', 'bomcas-tax-calculators-canada' ),
				__( 'A button, or an embedded scheduler, after a result.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'Set the button label, the provider name, and the URL of the scheduling page. Blank URL hides the button. Lite shows the button. The add-on can also embed the scheduler on the page. The booking URL is the page you already use. This plugin does not create appointments by itself.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'languages',
				__( 'Languages', 'bomcas-tax-calculators-canada' ),
				__( 'Visitors', 'bomcas-tax-calculators-canada' ),
				__( 'French and Spanish labels on the calculator.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'The language switch is a control on the calculator. It changes the labels shipped with the plugin. It does not translate your page content. A switch requires the matching nonce, so a bare link cannot flip the language. Currency display can follow the locale, including es-MX with USD, from this screen.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Rate figures stay in the dataset’s units. Translating a label does not change a bracket.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'packs',
				__( 'Packs', 'bomcas-tax-calculators-canada' ),
				__( 'Site', 'bomcas-tax-calculators-canada' ),
				__( 'Industry landing pages you choose to publish.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'A pack is a set of landing pages for an industry. Nothing is published until you select a pack and publish it. Publishing can replace a pack page that already exists, so leave this screen alone during an ordinary plugin update. The setup wizard can point at a pack. Choosing none leaves your pages as they are.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'white-label',
				__( 'White-label', 'bomcas-tax-calculators-canada' ),
				__( 'Site', 'bomcas-tax-calculators-canada' ),
				__( 'Hide the licence and the BOMCAS name on a client site.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'Calculators → White-label hides the licence key, the plan, and the account screen from users who are not on the allow list. Allowed user IDs are comma-separated. A capability can be used instead. An optional PIN is stored as a hash and is not shown again. People who are hidden see a short unlock form, not the key.', 'bomcas-tax-calculators-canada' ) ),
					array(
						'ul' => array(
							__( 'Menu label replaces the Calculators name in the left menu.', 'bomcas-tax-calculators-canada' ),
							__( 'Hide BOMCAS Canada in the plugin list when the client should not see the vendor name.', 'bomcas-tax-calculators-canada' ),
							__( 'Agency email is used for renewal mail instead of the BOMCAS address.', 'bomcas-tax-calculators-canada' ),
							__( 'A configuration constant can lock hiding on, so a client administrator cannot turn it off.', 'bomcas-tax-calculators-canada' ),
						),
					),
					array( 'p' => __( 'The calculators keep working while the licence is hidden. Hiding it does not delete the key.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'account',
				__( 'Account and upgrade', 'bomcas-tax-calculators-canada' ),
				__( 'Site', 'bomcas-tax-calculators-canada' ),
				__( 'An optional licence for updates. The calculators do not require one.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'Lite runs sales tax, income tax, and the mortgage calculator with no purchase. Calculators → Account stores a licence key for the optional add-on. The key is saved on this site and is not printed in the page source afterwards. White-label can hide this screen.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'The add-on keeps calculating if a subscription lapses. A licence is how the site receives add-on updates and support. It does not switch the calculators off, and it does not stop you from changing the GPL code. Plan names on the overview are Personal, Professional, and Agency. Billing itself is handled by the site that sold the licence, not by a call from this screen.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'The Pro settings screen shows the update entitlement and a link to the hosted billing page when one has been saved. Pasting a key here does not charge a card.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'privacy',
				__( 'Privacy and accessibility', 'bomcas-tax-calculators-canada' ),
				__( 'Site', 'bomcas-tax-calculators-canada' ),
				__( 'What stays in the browser, and how the screens are meant to be used.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'Calculator inputs stay in the browser. They are not posted, not stored in a cookie, and not sent to an analytics host unless you separately add a measurement id. A lead or a PDF is created only when the visitor asks. The optional credit is a link you turn on. The plugin does not phone home for calculator use.', 'bomcas-tax-calculators-canada' ) ),
					array( 'h' => __( 'Trust screen', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Calculators → Trust records the day you checked an official source. Do not invent that date. The public pages name the source next to the figures. Colour contrast on the admin screens uses the navy, blue, and orange palette, and form fields have visible labels. Tips are buttons that show their text on hover and on focus, so they are available from the keyboard.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'caching',
				__( 'Caching', 'bomcas-tax-calculators-canada' ),
				__( 'Site', 'bomcas-tax-calculators-canada' ),
				__( 'Why a static cache must be cleared when rates change.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'WP Fastest Cache stores a copy of the HTML. A token would otherwise stay frozen at the year that was current when the copy was made. The plugin clears that cache when you save a rate override, apply an import, pull a package, or publish a draft. WP Rocket is cleared too when that plugin is active.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'The daily job also clears the cache on 1 January and on any effective date in the personal-tax file or any sales-tax segment start. That is the day the year in force can change without anyone editing a page. If a host uses a different cache, clear the calculator pages after you import a new year.', 'bomcas-tax-calculators-canada' ) ),
					array( 'diagram' => 'year' ),
				)
			),
			self::article(
				'troubleshooting',
				__( 'Troubleshooting and FAQ', 'bomcas-tax-calculators-canada' ),
				__( 'Support', 'bomcas-tax-calculators-canada' ),
				__( 'The checks to make before you write a support note.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'h' => __( 'A page still shows last year’s number', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Confirm the new year is in the rate file and that its effective date is today or earlier. Clear the page cache. A buyer’s extra sections are shown instead of the generated guide, so those sections need tokens if they name a year.', 'bomcas-tax-calculators-canada' ) ),
					array( 'h' => __( 'A calculator is blank', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Check Toggles. A disabled tool renders nothing. Confirm the shortcode id is one of the shipped tools and the place code matches the file, such as BC or CA.', 'bomcas-tax-calculators-canada' ) ),
					array( 'h' => __( 'A calculator page was not created', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Country focus does not create them. On the add-on’s preset screen, use Add the next few pages. Each click adds a few missing pages and skips any address that already exists.', 'bomcas-tax-calculators-canada' ) ),
					array( 'h' => __( 'The hero button', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'It opens a service page for that kind of work. It does not open the contact page or the calculator page itself.', 'bomcas-tax-calculators-canada' ) ),
					array(
						'ul' => array(
							__( 'Does the plugin store what a visitor types? Only if they send the email form or download a PDF.', 'bomcas-tax-calculators-canada' ),
							__( 'Can I show a credit? Yes. Settings has one unchecked box.', 'bomcas-tax-calculators-canada' ),
							__( 'Will an update replace my page text? No. Saved edits stay.', 'bomcas-tax-calculators-canada' ),
						),
					),
				)
			),
			self::article(
				'changelog',
				__( 'Changelog', 'bomcas-tax-calculators-canada' ),
				__( 'Support', 'bomcas-tax-calculators-canada' ),
				__( 'What changed in this plugin.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'h' => __( '1.0.5', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Shorter calculator heroes, a long guide under each calculator, and figures that come from the rate dataset through tokens. This guide was added. Saving rates clears the page cache.', 'bomcas-tax-calculators-canada' ) ),
					array( 'h' => __( '1.0.4', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Overview chart for the last 30 days. Settings screens use one label-above form. Activation does not create or edit pages.', 'bomcas-tax-calculators-canada' ) ),
					array( 'h' => __( '1.0.3', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Guide-page layout and one-sentence descriptions. Rate tables stay in the plugin unless an administrator opts in.', 'bomcas-tax-calculators-canada' ) ),
					array( 'h' => __( '1.0.2 and earlier', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( '1.0.2 put the calculator before the rate table. 1.0.1 used the banner artwork. 1.0.0 shipped sales tax, income tax, and the mortgage payment.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
			self::article(
				'support',
				__( 'Support notes', 'bomcas-tax-calculators-canada' ),
				__( 'Support', 'bomcas-tax-calculators-canada' ),
				__( 'Record a problem on this site. Nothing is sent to a ticket service.', 'bomcas-tax-calculators-canada' ),
				array(
					array( 'p' => __( 'Write the subject and what happened. The note is stored in this site’s database and a copy is mailed to the WordPress administrator address. The form does not call an outside help desk. Include the page address and the calculator id. Do not paste a secret key or a visitor’s full return.', 'bomcas-tax-calculators-canada' ) ),
					array( 'p' => __( 'Read Troubleshooting first. Many blank calculators are a toggle, a shortcode id, or a cached page.', 'bomcas-tax-calculators-canada' ) ),
				)
			),
		);
		$cache = array();
		foreach ( $rows as $row ) {
			$cache[ $row['id'] ] = $row;
		}
		return $cache;
	}

	/**
	 * @param string $id      Id.
	 * @param string $title   Title.
	 * @param string $group   Group.
	 * @param string $summary Summary.
	 * @param array  $blocks  Blocks.
	 * @return array
	 */
	private static function article( $id, $title, $group, $summary, $blocks ) {
		return array(
			'id'      => $id,
			'title'   => $title,
			'group'   => $group,
			'summary' => $summary,
			'blocks'  => $blocks,
		);
	}
}
