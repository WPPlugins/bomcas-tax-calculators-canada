# Dynamic rates

Calculator pages do not store a tax year, a rate, a dollar amount, or a date in the words a visitor reads. The sentence is written with tokens. Each view fills those tokens from the rate dataset for the year in force.

## Which year

The year is the newest year in `personal-tax-current.json` whose `effective_from` is today or earlier.

An administrator can override that on **Calculators → Rates and overrides**, in **Year shown on calculator pages**. An empty box means automatic.

If the calendar year is not in the dataset, pages say `2026 (latest rates on file)` rather than presenting that year as the current calendar year. Administrators also see a warning in wp-admin. Adding the new year, or receiving a pushed dataset, changes every marked page on the next view. No page has to be edited.

## Tokens

`{year}` `{year_label}` `{updated}` `{effective}` `{bpa:BC}` `{rate:BC.income.first}` `{example:BC.income.42000.total}` `{deadline:balance_due}` `{source:us.federal}`

Worked-example tokens run the same engines as the calculator. The result is cached for twelve hours and dropped when rates are saved, imported, or published.

**Calculators → Page content** has a token picker. Hover a token for what it inserts. Click the field, then the token. Buyer text is stored with the token still in it and expanded on the next view.

## Cache

WP Fastest Cache keeps a static copy of the HTML. The plugin calls `wpfc_clear_all_cache` when overrides are saved, an import is applied, a pull finishes, or Pro publishes a draft. The daily job `bomcas_daily` also purges on 1 January and on any `effective_from` in the personal-tax file or any sales-tax segment start date. WP Rocket is cleared too when that plugin is active.

## What is not rewritten

Pages created earlier keep their shortcode and the `_bomcas_calc_managed` marker. The visible year and figures are drawn at request time. A buyer intro or extra section in `_bomcas_page_edits` is not replaced. Those fields should use the same tokens. The one-time migration only clears the plugin-written intro on a marked page that has no buyer intro, and it sets the excerpt to a token sentence, twenty pages at a time.
