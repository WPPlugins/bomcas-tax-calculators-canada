<?php
/**
 * Shared admin chrome for buyer and publisher screens.
 *
 * @package Bomcas_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Bomcas_Admin_UI' ) ) {

	/**
	 * Admin shell.
	 */
	class Bomcas_Admin_UI {

		/**
		 * Plugin base URL for the logo.
		 *
		 * @var string
		 */
		public static $base = '';

		/**
		 * Styles and behaviour. Assets are bundled with the plugin.
		 *
		 * @param string $base_url Plugin URL.
		 * @param string $version  Version.
		 */
		public static function enqueue( $base_url, $version ) {
			self::$base = $base_url;
			if ( defined( 'BOMCAS_CALC_DIR' ) ) {
				$stamp = filemtime( BOMCAS_CALC_DIR . 'assets/css/admin.css' );
				if ( $stamp ) {
					$version .= '.' . $stamp;
				}
			}
			wp_enqueue_style( 'bomcas-admin', trailingslashit( $base_url ) . 'assets/css/admin.css', array(), $version );
			wp_enqueue_script( 'bomcas-admin', trailingslashit( $base_url ) . 'assets/js/admin.js', array(), $version, true );
			wp_localize_script(
				'bomcas-admin',
				'BomcasAdminTips',
				array(
					'helpLabel' => __( 'Help', 'bomcas-tax-calculators-canada' ),
					'fields'    => array(
						'currency'                => __( 'Three-letter currency code. Plan prices are stored in CAD.', 'bomcas-tax-calculators-canada' ),
						'code'                    => __( 'The code a customer types at checkout. Letters and digits only.', 'bomcas-tax-calculators-canada' ),
						'amount'                  => __( 'Percent, or a fixed amount in CAD, depending on Kind.', 'bomcas-tax-calculators-canada' ),
						'kind'                    => __( 'Percent reduces the price by that share. Fixed subtracts a dollar amount.', 'bomcas-tax-calculators-canada' ),
						'duration'                => __( 'How many bills the discount applies to: the first payment, several cycles, the first year, or every renewal.', 'bomcas-tax-calculators-canada' ),
						'hide'                    => __( 'Hide the licence key, plan, and account screens from people who are not on the allow list.', 'bomcas-tax-calculators-canada' ),
						'users'                   => __( 'WordPress user IDs, separated by commas, who may see the licence on this site.', 'bomcas-tax-calculators-canada' ),
						'pin'                     => __( 'Optional extra password. It is stored as a hash and is not shown again.', 'bomcas-tax-calculators-canada' ),
						'stripe_secret'           => __( 'Stripe secret key. Leave blank to keep the saved key. It is never printed back.', 'bomcas-tax-calculators-canada' ),
						'coupon'                  => __( 'Optional coupon code. It is checked against the coupons saved on the store.', 'bomcas-tax-calculators-canada' ),
						'country_ca'              => __( 'Show or hide every Canadian calculator.', 'bomcas-tax-calculators-canada' ),
						'license_key'             => __( 'Paste a licence key. It is saved on this site and is not shown in the page source afterwards.', 'bomcas-tax-calculators-canada' ),
						'enabled'                 => __( 'Shows the email form under the estimate. Off until you save it.', 'bomcas-tax-calculators-canada' ),
						'notify'                  => __( 'A copy of each lead is mailed here.', 'bomcas-tax-calculators-canada' ),
						'retention'               => __( 'Leads older than this many days are deleted.', 'bomcas-tax-calculators-canada' ),
						'rate'                    => __( 'How many leads one network address may send in an hour.', 'bomcas-tax-calculators-canada' ),
						'consent'                 => __( 'The sentence the visitor must accept before the form sends.', 'bomcas-tax-calculators-canada' ),
						'fields'                  => __( 'One extra question per line, shown on the public form.', 'bomcas-tax-calculators-canada' ),
						'captcha_secret'          => __( 'Optional reCAPTCHA secret. Blank keeps the saved value.', 'bomcas-tax-calculators-canada' ),
						'turnstile_secret'        => __( 'Optional Turnstile secret. Blank keeps the saved value.', 'bomcas-tax-calculators-canada' ),
						's'                       => __( 'Matches the lead name or email.', 'bomcas-tax-calculators-canada' ),
						'calculator'              => __( 'Limits the list to one calculator id, such as sales-tax.', 'bomcas-tax-calculators-canada' ),
						'ga4'                     => __( 'Optional Google measurement id. Empty means nothing is sent to Google.', 'bomcas-tax-calculators-canada' ),
						'provider'                => __( 'A label for the scheduler. The URL is what visitors open.', 'bomcas-tax-calculators-canada' ),
						'url'                     => __( 'The page opened by the booking button. Blank hides the button.', 'bomcas-tax-calculators-canada' ),
						'label'                   => __( 'The words on the booking button.', 'bomcas-tax-calculators-canada' ),
						'mode'                    => __( 'Lite shows a button. Pro can also embed the scheduler.', 'bomcas-tax-calculators-canada' ),
						'h2'                      => __( 'Replaces the heading under the breadcrumb on this page.', 'bomcas-tax-calculators-canada' ),
						'intro'                   => __( 'Replaces the opening paragraph. A plugin update does not overwrite it.', 'bomcas-tax-calculators-canada' ),
						'cta'                     => __( 'The words on the main button for this page.', 'bomcas-tax-calculators-canada' ),
						'verified_on'             => __( 'The day you checked the official source. Do not invent a date.', 'bomcas-tax-calculators-canada' ),
						'sla_days'                => __( 'A new rate report older than this is marked overdue.', 'bomcas-tax-calculators-canada' ),
						'alerts'                  => __( 'Email rate alerts. Off until you opt in. Visitor amounts are not included.', 'bomcas-tax-calculators-canada' ),
						'short_links'             => __( 'Stores a short code for a shared result link.', 'bomcas-tax-calculators-canada' ),
						'country'                 => __( 'Where this site does most of its work. It does not hide the other country.', 'bomcas-tax-calculators-canada' ),
						'template'                => __( 'Publishes an industry landing page. None leaves the pages as they are.', 'bomcas-tax-calculators-canada' ),
						'booking'                 => __( 'Optional booking page shown after a result.', 'bomcas-tax-calculators-canada' ),
						'hide'                    => __( 'Hides the licence key and upgrade form from people who are not on the allow list.', 'bomcas-tax-calculators-canada' ),
						'users'                   => __( 'WordPress user IDs, separated by commas, who may see the licence.', 'bomcas-tax-calculators-canada' ),
						'pin'                     => __( 'Optional extra password. It is stored as a hash.', 'bomcas-tax-calculators-canada' ),
						'menu_label'              => __( 'Replaces the Calculators name in the left menu when branding is hidden.', 'bomcas-tax-calculators-canada' ),
						'agency_email'            => __( 'Shown to client sites instead of the BOMCAS address.', 'bomcas-tax-calculators-canada' ),
						'server'                  => __( 'The address that publishes signed rate packages.', 'bomcas-tax-calculators-canada' ),
						'public_key'              => __( 'The PEM public key that verifies a rate package.', 'bomcas-tax-calculators-canada' ),
						'dataset'                 => __( 'Which rate file this override belongs to.', 'bomcas-tax-calculators-canada' ),
						'jurisdiction'            => __( 'A place code such as BC or CA. Blank shows the whole slice.', 'bomcas-tax-calculators-canada' ),
						'tax_year'                => __( 'The tax year of the slice you want to see.', 'bomcas-tax-calculators-canada' ),
						'rate_type'               => __( 'Income tax, sales tax, payroll, or the federal table.', 'bomcas-tax-calculators-canada' ),
						'key'                     => __( 'The licence key from the purchase or a complimentary grant.', 'bomcas-tax-calculators-canada' ),
						'plan'                    => __( 'Personal, Professional, or Agency.', 'bomcas-tax-calculators-canada' ),
						'interval'                => __( 'Monthly, quarterly, or yearly billing.', 'bomcas-tax-calculators-canada' ),
						'bomcas_calc_show_credit' => __( 'Adds the BOMCAS Canada credit under calculators. Off until you save it.', 'bomcas-tax-calculators-canada' ),
						'name'                    => __( 'The plan or coupon name customers see.', 'bomcas-tax-calculators-canada' ),
						'month'                   => __( 'Price in CAD billed every month.', 'bomcas-tax-calculators-canada' ),
						'quarter'                 => __( 'Price in CAD billed every three months.', 'bomcas-tax-calculators-canada' ),
						'year'                    => __( 'Price in CAD billed every year.', 'bomcas-tax-calculators-canada' ),
						'sites'                   => __( 'How many sites this plan may activate.', 'bomcas-tax-calculators-canada' ),
						'white_label'             => __( 'Lets an agency hide the licence on a client site.', 'bomcas-tax-calculators-canada' ),
						'email'                   => __( 'The customer email for this complimentary licence.', 'bomcas-tax-calculators-canada' ),
						'do'                      => __( 'Discount, free months, revoke, or extend a complimentary licence.', 'bomcas-tax-calculators-canada' ),
						'title'                   => __( 'The alert title shown in the plugin and the email.', 'bomcas-tax-calculators-canada' ),
						'setup_price'             => __( 'Done-for-you setup price in cents. Blank means it is not offered.', 'bomcas-tax-calculators-canada' ),
					),
				)
			);
		}

		/**
		 * @param array $args Screen args.
		 */
		public static function open( $args ) {
			if ( ! function_exists( 'submit_button' ) ) {
				require_once ABSPATH . 'wp-admin/includes/template.php';
			}
			$tabs  = isset( $args['tabs'] ) ? $args['tabs'] : array();
			$toast = self::notice_flag() ? __( 'Saved.', 'bomcas-tax-calculators-canada' ) : '';
			$name  = isset( $args['product'] ) && '' !== (string) $args['product'] ? (string) $args['product'] : __( 'BOMCAS Canada', 'bomcas-tax-calculators-canada' );
			echo '<div class="bomcas-admin" data-toast="' . esc_attr( $toast ) . '">';
			$logo = self::$base ? trailingslashit( self::$base ) . 'assets/img/bomcas-mark.svg' : '';
			echo '<header class="bomcas-admin__bar"><div class="bomcas-admin__brand">';
			if ( $logo && false === strpos( $name, 'Accountants Expert' ) ) {
				echo '<img class="bomcas-admin__mark" src="' . esc_url( $logo ) . '" alt="' . esc_attr( $name ) . '" />';
			}
			echo '<div><p class="bomcas-admin__name">' . esc_html( $name ) . '</p>';
			echo '<p class="bomcas-admin__meta">' . esc_html( $args['kicker'] ) . '</p>';
			echo '</div></div>';
			echo '<span class="bomcas-admin__badge">' . esc_html( $args['badge'] ) . '</span></header>';
			if ( $tabs ) {
				echo '<nav class="bomcas-admin__nav" aria-label="' . esc_attr__( 'Sections', 'bomcas-tax-calculators-canada' ) . '">';
				foreach ( $tabs as $tab ) {
					echo '<a href="' . esc_url( $tab['url'] ) . '"';
					if ( ! empty( $tab['current'] ) ) {
						echo ' aria-current="page"';
					}
					echo '>' . esc_html( $tab['label'] ) . '</a>';
				}
				echo '</nav>';
			}
			echo '<div class="bomcas-admin__main">';
			$page = filter_input( INPUT_GET, 'page', FILTER_UNSAFE_RAW );
			if ( ! empty( $args['doc'] ) && ! in_array( (string) $page, array( 'bomcas-help', 'bomcas-owner-help' ), true ) ) {
				self::learn_link( (string) $args['doc'] );
			}
			if ( ! empty( $args['help'] ) ) {
				echo '<details class="bomcas-howto" open><summary>' . esc_html__( 'How this works', 'bomcas-tax-calculators-canada' ) . '</summary><p>' . esc_html( $args['help'] ) . '</p></details>';
			}
			echo '<div id="bomcas-confirm" class="bomcas-modal" hidden><div class="bomcas-modal__card" role="dialog" aria-modal="true">';
			echo '<p data-confirm-text></p><p><button type="button" class="button button-primary" data-confirm-go>' . esc_html__( 'Confirm', 'bomcas-tax-calculators-canada' ) . '</button> ';
			echo '<button type="button" class="button" data-confirm-cancel>' . esc_html__( 'Cancel', 'bomcas-tax-calculators-canada' ) . '</button></p></div></div>';
		}

		/**
		 * Close the shell.
		 */
		public static function close() {
			echo '</div></div>';
		}

		/**
		 * @param array $cards Cards.
		 */
		public static function kpis( $cards ) {
			echo '<section class="bomcas-admin__kpis is-' . (int) count( $cards ) . '">';
			foreach ( $cards as $card ) {
				echo '<article class="bomcas-admin__kpi">';
				if ( ! empty( $card['icon'] ) ) {
					echo wp_kses( self::icon( $card['icon'] ), self::icon_allowed() );
				}
				echo '<span>' . esc_html( $card['label'] ) . '</span>';
				$long = isset( $card['value'] ) && strlen( (string) $card['value'] ) > 16;
				echo '<strong' . ( $long ? ' class="is-compact"' : '' ) . '>' . esc_html( $card['value'] ) . '</strong>';
				if ( ! empty( $card['note'] ) ) {
					echo '<em>' . esc_html( $card['note'] ) . '</em>';
				}
				echo '</article>';
			}
			echo '</section>';
		}

		/**
		 * 24px line icon.
		 *
		 * @param string $name Icon name.
		 * @return string
		 */
		public static function icon( $name ) {
			$paths = array(
				'check'   => '<svg class="bomcas-ico" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.8"></circle><path d="M8 12.2l2.4 2.4 5.2-5.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
				'card'    => '<svg class="bomcas-ico" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"></rect><path d="M3 10h18M8 15h4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path></svg>',
				'refresh' => '<svg class="bomcas-ico" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12a7 7 0 1 1-2-4.9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path><path d="M19 4v5h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
				'globe'   => '<svg class="bomcas-ico" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.8"></circle><path d="M3 12h18M12 3c2.5 2.8 3.8 5.8 3.8 9s-1.3 6.2-3.8 9c-2.5-2.8-3.8-5.8-3.8-9s1.3-6.2 3.8-9z" fill="none" stroke="currentColor" stroke-width="1.8"></path></svg>',
			);
			return isset( $paths[ $name ] ) ? $paths[ $name ] : '';
		}

		/**
		 * @return array
		 */
		public static function icon_allowed() {
			return array(
				'svg'    => array(
					'class'       => true,
					'width'       => true,
					'height'      => true,
					'viewbox'     => true,
					'aria-hidden' => true,
				),
				'circle' => array(
					'cx'           => true,
					'cy'           => true,
					'r'            => true,
					'fill'         => true,
					'stroke'       => true,
					'stroke-width' => true,
				),
				'rect'   => array(
					'x'            => true,
					'y'            => true,
					'width'        => true,
					'height'       => true,
					'rx'           => true,
					'fill'         => true,
					'stroke'       => true,
					'stroke-width' => true,
				),
				'path'   => array(
					'd'               => true,
					'fill'            => true,
					'stroke'          => true,
					'stroke-width'    => true,
					'stroke-linecap'  => true,
					'stroke-linejoin' => true,
				),
			);
		}

		/**
		 * @param string $status Status.
		 * @return string
		 */
		public static function tip( $text, $article = '' ) {
			$more = '';
			if ( '' !== $article ) {
				$url = self::doc_url( $article );
				if ( $url ) {
					$more = ' <a class="bomcas-tip__more" href="' . esc_url( $url ) . '">' . esc_html__( 'Learn more', 'bomcas-tax-calculators-canada' ) . '</a>';
				}
			}
			return '<button type="button" class="bomcas-tip" aria-label="' . esc_attr__( 'Help', 'bomcas-tax-calculators-canada' ) . '"><span aria-hidden="true">?</span><span class="bomcas-tip__bubble" role="tooltip">' . esc_html( $text ) . $more . '</span></button>';
		}

		/**
		 * Link to an in-plugin article. No remote request.
		 *
		 * @param string $slug Article id.
		 */
		public static function learn_link( $slug ) {
			$url   = self::doc_url( $slug );
			$title = self::doc_title( $slug );
			if ( ! $url || ! $title ) {
				return;
			}
			echo '<p class="bomcas-doc__jump"><a href="' . esc_url( $url ) . '">' . esc_html(
				sprintf(
					/* translators: %s: article title */
					__( 'Learn more: %s', 'bomcas-tax-calculators-canada' ),
					$title
				)
			) . '</a></p>';
		}

		/**
		 * @param string $slug Article id.
		 * @return string
		 */
		private static function doc_url( $slug ) {
			if ( class_exists( 'Bomcas_Docs' ) && Bomcas_Docs::has( $slug ) ) {
				return Bomcas_Docs::url( $slug );
			}
			if ( class_exists( 'Bomcas_Lic_Docs' ) && Bomcas_Lic_Docs::has( $slug ) ) {
				return Bomcas_Lic_Docs::url( $slug );
			}
			return '';
		}

		/**
		 * @param string $slug Article id.
		 * @return string
		 */
		private static function doc_title( $slug ) {
			if ( class_exists( 'Bomcas_Docs' ) && Bomcas_Docs::has( $slug ) ) {
				return Bomcas_Docs::title( $slug );
			}
			if ( class_exists( 'Bomcas_Lic_Docs' ) && Bomcas_Lic_Docs::has( $slug ) ) {
				return Bomcas_Lic_Docs::title( $slug );
			}
			return '';
		}

		/**
		 * Tags allowed inside a tooltip button.
		 *
		 * @return array
		 */
		public static function tip_allowed() {
			return array(
				'button' => array(
					'type'       => true,
					'class'      => true,
					'aria-label' => true,
				),
				'span'   => array(
					'aria-hidden' => true,
					'class'       => true,
					'role'        => true,
				),
				'a'      => array(
					'class' => true,
					'href'  => true,
				),
			);
		}

		/**
		 * Redirect flags such as saved=1. They do not change data.
		 *
		 * @return bool
		 */
		private static function notice_flag() {
			foreach ( array( 'saved', 'updated', 'granted', 'connected' ) as $flag ) {
				if ( filter_input( INPUT_GET, $flag, FILTER_UNSAFE_RAW ) ) {
					return true;
				}
			}
			return false;
		}

		public static function pill( $status, $label = '' ) {
			$slug = sanitize_html_class( strtolower( (string) $status ) );
			$text = '' !== $label ? $label : (string) $status;
			return '<span class="bomcas-pill is-' . esc_attr( $slug ) . '">' . esc_html( $text ) . '</span>';
		}
	}
}
