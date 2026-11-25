---
excerpt: Nova Scotia sales tax for {year_label} is harmonized sales tax. The rate changed on {date:NS.sales.latest_from}. That date belongs on this page only, not on other provinces.
---

## What this tax is

Nova Scotia charges harmonized sales tax. The current row is {rate:NS.sales.hst}. The previous row in the file was {rate:NS.sales.prior.hst}, and the change takes effect on {date:NS.sales.latest_from}. A supply is taxed under the row for its date. This page is a standard taxable supply, not a vehicle and not income tax.

## How the calculation works

The pre-tax price is multiplied by the harmonized rate for the date. The worked examples on this page use the rate now in force, {rate:NS.sales.hst}. GST, PST, and QST on that line are zero because the tax is a single harmonized amount. A date before {date:NS.sales.latest_from} uses {rate:NS.sales.prior.hst}. Do not apply that earlier rate to a current supply, and do not apply the change date to another province.

## Worked examples

A current supply priced at {example:NS.sales.2500.amount} draws harmonized tax of {example:NS.sales.2500.hst}. The tax is {example:NS.sales.2500.tax} and the amount with tax is {example:NS.sales.2500.total}. That is the rate after {date:NS.sales.latest_from}, not the earlier rate of {rate:NS.sales.prior.hst}.

At {example:NS.sales.18000.amount} the tax is {example:NS.sales.18000.tax}. If the supply were dated before the change, the form would use the earlier rate. These two examples are not that earlier case.

## What is left out

Point-of-sale rebates for particular goods, if a statute still allows one, are not assumed. Vehicles are on the vehicle page. Income tax is a different return.

## Common mistakes

Using {rate:NS.sales.prior.hst} on a supply after {date:NS.sales.latest_from}. Putting this change date on an Alberta or Ontario receipt. Adding a separate GST line on top of the harmonized tax. Typing the tax-included amount back in as the price.

## Recent changes

The rate changed from {rate:NS.sales.prior.hst} to {rate:NS.sales.hst} on {date:NS.sales.latest_from}. The file was checked {updated}. No other province in this set uses that date.

## Neighbouring jurisdictions

New Brunswick’s harmonized rate remains {rate:NB.sales.hst}. On the sample price, New Brunswick’s tax is {example:NB.sales.2500.tax}, against Nova Scotia’s current {example:NS.sales.2500.tax}.

Prince Edward Island’s rate is also {rate:PE.sales.hst}. Its tax on that price is {example:PE.sales.2500.tax}. A move across the boundary changes the rate. It does not average the two.

## Questions

### Which rate do the examples use?

The current rate of {rate:NS.sales.hst}. The earlier rate of {rate:NS.sales.prior.hst} applies before {date:NS.sales.latest_from}.

### Is GST charged as well?

No. The GST line on the example is {example:NS.sales.2500.gst}. The tax is harmonized.

### Can I use this date in Ontario?

No. Ontario’s row does not change on {date:NS.sales.latest_from}.

### Are vehicles on this page?

No. Use the vehicle page.

### Does the calculator know my invoice date?

Only the date you type. The examples illustrate the current row.

### Is this a return?

No. It is the {year_label} table, checked {updated}.

## Official sources

- [Canada Revenue Agency GST/HST rates](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/gst-hst-businesses/charge-collect-which-rate.html)
- [Nova Scotia finance, tax](https://www.novascotia.ca/finance/en/home/taxation.aspx)
