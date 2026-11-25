=== BOMCAS Tax Calculators – Canada ===
Contributors: bomcasmedia
Tags: canada tax calculator, income tax, gst hst, sales tax, mortgage calculator
Requires at least: 6.2
Tested up to: 7.1
Stable tag: 1.1.19
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://bomcas.ca

Estimates Canadian income tax, GST, HST, PST, QST, and a mortgage payment. The numbers stay in the browser.

== Description ==

BOMCAS Tax Calculators – Canada estimates Canadian income tax, GST, HST, PST, QST, and a mortgage payment on your own WordPress page. Built by BOMCAS Canada (https://bomcas.ca), owned by Dellendo Farquharson. Docs: https://accountantsexpert.com/calculators-docs/ . The figures are estimates. They are not a filing, a ruling, or a Canada Revenue Agency service.

The arithmetic runs in the browser. A view count is stored on your own site. Nothing is sent to Google unless you save a measurement id. Email and PDF run only after the visitor submits the optional form.

= Lite and Pro =

Lite includes Canadian income tax, GST, HST, PST, QST, and a mortgage payment. Pro is a separate plugin at https://accountantsexpert.com/products/bomcas-tax-payroll-calculators-canada/ . Lite does not include payroll. Lite does not download or install Pro.

Pro adds payroll deductions, salary after tax, Quebec withholding, self-employed CPP, employer health tax for British Columbia, Manitoba, Newfoundland and Labrador, and Ontario, EI benefits, the Canada child benefit, corporate tax, salary versus dividend, capital cost allowance, capital gains, tax instalments, a late-filing penalty and prescribed interest, donations, a car allowance, vehicle purchase tax, lease versus buy, a mortgage stress-test payment, CMHC insurance, land transfer tax, RRSP room, TFSA room, FHSA room, Old Age Security recovery tax, and the Quebec welcome tax. Land transfer tax uses the brackets in the file for British Columbia and Ontario, including Toronto when that box is checked. Manitoba, New Brunswick, Nova Scotia, Prince Edward Island, and Quebec use a rate or brackets you type.

Lite has no licence. Pro Personal covers 1 site, Professional covers 5 sites, and Agency covers 25 sites. Prices are on https://accountantsexpert.com/products/bomcas-tax-payroll-calculators-canada/ . Updates and support for Pro run while the licence is active or in grace.

Lite limits: three calculators (income tax, sales tax, mortgage payment), the styling shipped with the plugin, and the rate file in the zip. A new tax year arrives when you update this plugin. You can also import a rate file under Calculators, Rates. Lite does not download rates by itself. An optional signed download is off until you check Opt in on that screen. Lite does not include payroll, corporate, savings, or property tools, white-label, a site-count licence, lead export, or a Pro download. Calculators, Account and upgrade shows plans from the store. The buy button sends the site address and admin email only after you check the box. After purchase, download Pro from My Account and upload the zip. The prompt is on that screen and can be dismissed. Visitors do not see it.

= Optional rate download =

This plugin does not contact another server unless you opt in. On Calculators → Rates, the box “Opt in to download rate updates” is unchecked. Checking it lets the plugin request a signed JSON package from the address saved there. The default address is https://accountantsexpert.com/wp-json/bomcas-licensing/v1/rates . The request sends no calculator inputs and no licence key. The reply is checked with a signature and a checksum. If either check fails, the calculators keep the rates shipped in this plugin. Leave the box unchecked and the only rate change is a plugin update.

= Income tax =

Federal tax plus one province or territory for the year in the rate file. The file shipped with this version is the 2026 personal tax year. Places: Alberta, British Columbia, Manitoba, New Brunswick, Newfoundland and Labrador, Northwest Territories, Nova Scotia, Nunavut, Ontario, Prince Edward Island, Quebec, Saskatchewan, and Yukon.

Inputs: place, employment, self-employment, other income, eligible and non-eligible dividends, capital gains, one RRSP and FHSA deduction, and tax already deducted. Credits use CPP, CPP2, and EI, plus QPP and QPIP in Quebec. It does not print a paycheque.

Outputs: taxable income, federal and provincial tax after credits, total tax, average and marginal rates, and after-tax income. A balance shows if tax already deducted is set. Quebec adds the workers deduction.

= Sales tax =

GST, HST, PST, or QST on a standard taxable amount for those 13 places. Segments run from 2022 through 2026. The date selects the segment. Vehicles, real property, and exempt supplies are excluded.

Inputs: place, amount, date, and add or remove tax. Outputs: before tax, GST, PST or RST, HST, QST, tax, and total.

= Mortgage payment =

No posted rate. Inputs: price, down payment, annual rate, years, payments per year, and semi-annual or monthly compounding. Outputs: principal, payment, payment count, and interest.

= Shortcodes and blocks =

`[bomcas_calculator]` attributes: `id` and `province`. Valid ids are income-tax, sales-tax, and mortgage-payment.

The block BOMCAS Tax Calculators – Canada (`bomcas/calculator`) has the attributes `calculator` and `province`. The block `bomcas/calculator-any` uses the same two attributes. Elementor, Divi, and Beaver Builder widgets register only when that builder is active.

Other shortcodes: `[bomcas_compare left="BC" right="AB"]`, `[bomcas_pack id="blogger"]` (Lite lists only packs whose calculators are installed: realtor, dealer, ecommerce, and blogger), `[bomcas_rate_changelog]`, `[bomcas_docs]`, and `[bomcas_roadmap]`.

= On a website =

Put a calculator on a service page, a blog post, a landing page, a lead page next to a contact or booking link, a client portal, a resource hub, or a sidebar. Visitors stay on your page. The guide is ordinary HTML, so the page can be indexed. The form fits a phone, and the result is a definition list.

Accounting and tax firms, bookkeepers, payroll providers, financial advisors, mortgage brokers and realtors, HR and recruiting sites, banks and credit unions, finance writers, non-profits, classrooms, and employer intranets can each use the tool that matches the question their visitors ask.

= Settings =

Calculators, Rates imports or exports a year file. The next view uses the new numbers. You can turn a calculator or a whole country off. The plugin is ready for translation on translate.wordpress.org. An optional credit line stays off until you save it. Copy you saved under Calculators, Page content is left alone.

== Installation ==

1. Upload the zip from Plugins, Add New, Upload Plugin, or copy the `bomcas-tax-calculators-canada` folder into `/wp-content/plugins/`. That folder name is the directory slug. Shortcodes, option names, and hooks keep the `bomcas_` prefix. If `bomcas-tax-payroll-calculators-canada` or `bomcas-calculators` is still installed, activating this copy deactivates that folder. Settings stay.
2. Activate BOMCAS Tax Calculators – Canada.
3. Open Calculators, Help, and follow Getting started.
4. Add the BOMCAS Tax Calculators – Canada block, or a shortcode:

`[bomcas_calculator id="income-tax" province="ON"]`

`[bomcas_calculator id="sales-tax" province="BC"]`

`[bomcas_calculator id="mortgage-payment"]`

Province codes are optional. Income tax and sales tax default to British Columbia.

== Frequently Asked Questions ==

= Which tax year does income tax use? =

The newest year in the personal tax file whose start date is today or earlier. Version 1.1.4 ships the 2026 file. A later file, imported under Calculators, Rates, is used on the next page view.

= Which places are covered? =

Income tax and sales tax cover the federal tables and all 13 provinces and territories. A page uses the province you select.

= Does the plugin store what a visitor types? =

No. The calculator does not post the form, does not call fetch for the arithmetic, and does not write the entries to a cookie or to local storage. A result is stored only if you turn on the optional email or PDF form and the visitor submits it.

= Is an estimate a filing? =

No. It is not a filing, a ruling, or a Canada Revenue Agency service. It does not prepare a T1, a T2, or a remittance.

= Does this plugin include a payroll calculator? =

No. This plugin has three calculators: income tax, sales tax, and a mortgage payment. Income tax uses CPP, CPP2, EI, QPP, and QPIP from the rate file when it works out credits. It does not calculate a paycheque.

= Can I remove sales tax from a total? =

Yes. Set Direction to Remove tax. The result shows the amount before tax, each tax, and the total you started with.

= Does the mortgage calculator use a bank rate? =

No. The visitor types the annual rate. Compounding defaults to the semi-annual convention used for Canadian fixed-rate mortgages. You can switch it to monthly.

= How do I put a calculator on a page? =

Use the BOMCAS Tax Calculators – Canada block, or one of the shortcodes in Installation. The same engine renders both.

= Will an update replace my page text? =

No. Copy you saved under Calculators, Page content stays. The guide under a calculator is drawn when the page is viewed and is not written into the post.

= Does the calculation call another website? =

No. Optional services run only after you save a setting. See External services.

= Who owns the plugin? =

Developed by BOMCAS, owned by Dellendo Farquharson. The author slug for this directory is bomcasmedia.

= What does Pro add, and do I have to buy it? =

Pro is optional. Lite keeps working with no account and no licence. Pro, at https://accountantsexpert.com/products/bomcas-tax-payroll-calculators-canada/, adds payroll deductions, salary after tax, corporate tax, salary versus dividend, capital cost allowance, instalments, late-filing interest, employer health tax, self-employed contributions, vehicle purchase tax, lease versus buy, mortgage affordability, CMHC insurance, land transfer tax, Quebec withholding, RRSP room, TFSA room, FHSA room, capital gains, the Canada child benefit, EI benefits, a car allowance, donations, Old Age Security recovery tax, and the Quebec welcome tax. This plugin will not download Pro.

== Screenshots ==

1. Income tax, with the province list and the RRSP and FHSA deduction.
2. Sales tax, adding GST and PST for a province.
3. Mortgage payment, with the rate typed by the visitor.
4. Income tax on a narrow screen.
5. Settings for BOMCAS Tax Calculators – Canada.
6. The calculator block in the editor.
7. Rates, where a year file can be exported or imported.

== Changelog ==

= 1.1.19 =

* The directory slug and text domain are now bomcas-tax-calculators-canada, matching the WordPress.org permalink. Shortcodes, option names, and hooks keep the bomcas_ prefix, so existing settings stay.
* Activating this folder turns off an older bomcas-tax-payroll-calculators-canada or bomcas-calculators copy if one is still installed.

= 1.1.18 =

* The plugin is now named BOMCAS Tax Calculators – Canada. This plugin has no payroll calculator.
* Scripts and JSON data blocks are printed with the WordPress script functions.
* Output is escaped where it is printed. Admin actions check a nonce and the user's capability.
* Translation files and load_plugin_textdomain() are removed. Translations come from translate.wordpress.org.
* The breadcrumb fix uses the WordPress 6.9 template output buffer. The plugin no longer opens its own buffer.
* Admin notices show only on this plugin's screens and can be dismissed.
* The plugin no longer installs other plugins and no longer runs external programs.
* External services are documented in the readme.

= 1.1.17 =

The remaining United States wording is gone from the page copy, and the translation template was regenerated from the current source.

= 1.1.16 =

The guide seed and the calculator script no longer contain United States figures. Hero image file names no longer say canada-usa. The product band uses navy #0B1F3A when the theme colour variable is missing. The translation template still had older strings; 1.1.17 replaces it.

= 1.1.15 =

Calculator shortcodes and blocks render again. The IRS line, the FICA tokens, and the us-*.webp files are removed. Deleting the older bomcas-calculators plugin no longer loads that plugin’s file.

= 1.1.14 =

Buy and My Account use https://accountantsexpert.com when the licence server is empty, unless Accountants Expert Licensing is active on this site. Activating this folder while the older bomcas-calculators plugin is still on no longer stops with a fatal error. The older folder is turned off. United States shortcode branches in the main file no longer calculate.

= 1.1.13 =

The Rates screen shows the rate version and the last check. Update now downloads a signed pack only after Lite opt-in, or from Pro when a licence key is saved. A failed check keeps the bundled rates.

= 1.1.12 =

Calculator colours default to navy #0B1F3A, body #1B2A44, and orange #E8572A. Link text uses #C2410C so it stays readable on white.

= 1.1.11 =

The directory slug and text domain are bomcas-tax-payroll-calculators-canada. The display name is BOMCAS Tax Calculators – Canada. Shortcodes, option names, and hooks stay on the bomcas_ prefix. Activating this folder deactivates an older bomcas-calculators copy. Settings are kept.

= 1.1.10 =

The public questions cover install, compatibility, rates, privacy, Lite and Pro limits, licensing, coupons, and support.

= 1.1.9 =

Canada only. The three Lite calculators are income tax, sales tax, and a mortgage payment. Account and upgrade loads plans and promotions from the store and opens checkout only after you agree to send the site address and email. Pro is not installed from Lite.

= 1.1.8 =

Uninstall removes Lite options and the leads table. Translations load on init. The second calculator block is registered from block.json.

= 1.1.7 =

The feature page lists every Lite and Pro calculator from the code, with inputs, outputs, and the places each one covers. The readme matches that list.

= 1.1.6 =

The admin shell uses the product name passed by the screen. Accountants Expert Licensing therefore shows Accountants Expert. Calculator screens keep their own name.

= 1.1.5 =

Author link is https://bomcas.ca. The optional calculator credit, off until you save it, points there. Pro and the docs stay on accountantsexpert.com.

= 1.1.4 =

The plugin is named BOMCAS Tax Calculators – Canada for the directory. The folder and the text domain remain bomcas-calculators, so an existing install updates in place. The readme lists each calculator this package actually runs.

= 1.1.3 =

The mortgage calculator sets its rate payload before the page renders, so PHP 8 no longer warns about an undefined variable.

= 1.1.2 =

Credit rows and rate imports pass the CSV escape argument, so PHP 8.4 and 8.5 do not warn on a calculator page.

= 1.1.1 =

Store pages use the Accountants Expert name. The sales-tax note for the Northwest Territories no longer names a city in the freight example.

= 1.1.0 =

The store is accountantsexpert.com. An empty licence server uses this site. Product and publisher links use that store address.

== Upgrade Notice ==

= 1.1.19 =

The plugin folder and text domain are now bomcas-tax-calculators-canada. Settings are kept. If an older folder is still installed, this copy turns it off.

= 1.1.18 =

Renamed to BOMCAS Tax Calculators – Canada. Security and WordPress.org review fixes. Settings are kept.

= 1.1.17 =

Page copy and the translation template no longer describe United States calculators.

= 1.1.16 =

Hero file names no longer say canada-usa. United States examples are out of the guide seed and the calculator script.

= 1.1.15 =

Calculator shortcodes and blocks render again. The plugin folder is bomcas-tax-payroll-calculators-canada. An empty licence server uses https://accountantsexpert.com.

= 1.1.5 =

The optional public credit, still off until you save it, links to https://bomcas.ca.

= 1.1.4 =

Names the plugin BOMCAS Tax Calculators – Canada. Install the new folder bomcas-tax-payroll-calculators-canada. Settings are kept.

== External services ==

The calculators themselves do not contact another server. What a visitor types into a calculator stays in the browser. Each service below is off by default and runs only after a site administrator saves the setting that turns it on.

= Google reCAPTCHA (optional, lead form) =

What it is: Google reCAPTCHA checks that the optional "Email me my results" form was sent by a person.
When: only if the lead form is enabled and a reCAPTCHA secret key is saved under Calculators, Leads, and only when a visitor submits that form.
What is sent: the secret key you saved, the captcha response token from the visitor's browser, and the server's IP address as part of the HTTPS request, to `https://www.google.com/recaptcha/api/siteverify`. No calculator inputs are sent.
Service provider: Google LLC. Terms of Service: https://policies.google.com/terms . Privacy Policy: https://policies.google.com/privacy .

= Cloudflare Turnstile (optional, lead form) =

What it is: Cloudflare Turnstile is an alternative to reCAPTCHA for the same form check.
When: only if the lead form is enabled and a Turnstile secret key is saved under Calculators, Leads, and only when a visitor submits that form. If a Turnstile key is saved, reCAPTCHA is not called.
What is sent: the secret key you saved and the Turnstile response token, to `https://challenges.cloudflare.com/turnstile/v0/siteverify`. No calculator inputs are sent.
Service provider: Cloudflare, Inc. Terms of Service: https://www.cloudflare.com/website-terms/ . Privacy Policy: https://www.cloudflare.com/privacypolicy/ . Turnstile privacy addendum: https://www.cloudflare.com/turnstile-privacy-policy/ .

= Google Analytics 4 (optional) =

What it is: Google Analytics counts visits and calculator use.
When: only if a GA4 measurement ID is saved under Calculators, Usage. Leave the field empty and the Google tag is never loaded.
What is sent: the visitor's browser loads `https://www.googletagmanager.com/gtag/js` and sends a page view and a `bomcas_calculation` event with the calculator name. Google receives the usual browser data (IP address, user agent, page URL). No calculator inputs are sent.
Service provider: Google LLC. Terms of Service: https://marketingplatform.google.com/about/analytics/terms/us/ . Privacy Policy: https://policies.google.com/privacy .

= BOMCAS rate update service at accountantsexpert.com (optional) =

What it is: a signed JSON file with updated tax rates, published by BOMCAS Canada, the developer of this plugin.
When: only after an administrator checks "Opt in to download rate updates" on Calculators, Rates and saves. Then the plugin checks once a day, and when an administrator clicks Update now. With the box unchecked nothing is requested.
What is sent: an HTTPS GET request to the address saved on that screen (default `https://accountantsexpert.com/wp-json/bomcas-licensing/v1/rates`) and, if a public key address is saved, to that address. The request carries the site's IP address and user agent. It sends no calculator inputs, no visitor data, and no licence key.
Service provider: BOMCAS Canada (accountantsexpert.com). Terms of Service: https://accountantsexpert.com/calculators-pro/terms/ . Privacy Policy: https://accountantsexpert.com/privacy/ .

= BOMCAS store at accountantsexpert.com (admin screen only) =

What it is: the store that sells the optional Pro add-on.
When: only when an administrator opens Calculators, Account and upgrade. That screen requests the public plan list (`wp-json/bomcas-licensing/v1/plans`) and the current promotions (`wp-json/bomcas-licensing/v1/promotions`). After the administrator checks the consent box and clicks Buy, it requests a checkout session (`wp-json/bomcas-licensing/v1/checkout-session`). If the administrator pastes a licence key and clicks Connect, it calls `wp-json/bomcas-licensing/v1/status` and `wp-json/bomcas-licensing/v1/activate`. These calls never run on a public page.
What is sent: plan list and promotions send nothing except the request itself. Checkout sends the chosen plan and billing interval, the site address, and the email address the administrator typed, only after the consent box is checked. Connect sends the licence key and the site address.
Where: the licence server saved on that screen. When it is empty, `https://accountantsexpert.com`.
Service provider: BOMCAS Canada (accountantsexpert.com). Terms of Service: https://accountantsexpert.com/calculators-pro/terms/ . Privacy Policy: https://accountantsexpert.com/privacy/ .

The optional email and PDF forms store only what the visitor submits, on your own site, and only after you enable them.
