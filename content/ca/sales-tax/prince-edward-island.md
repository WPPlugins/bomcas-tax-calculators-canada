---
excerpt: Prince Edward Island sales tax for {year_label} is harmonized sales tax. The neighbouring Nova Scotia rate is lower after Nova Scotia’s own change date.
---

## What this tax is

Prince Edward Island charges harmonized sales tax at {rate:PE.sales.hst} on a standard taxable supply. The tax is one amount. This page is not the Island’s income tax and it is not a vehicle.

## How the calculation works

Multiply the pre-tax price by {rate:PE.sales.hst}. GST is not added again. The worked GST, PST, and QST lines are zero because the harmonized line carries the tax. Nova Scotia’s change on {date:NS.sales.latest_from} does not change this rate.

## Worked examples

On {example:PE.sales.2500.amount} the tax is {example:PE.sales.2500.tax} and the amount with tax is {example:PE.sales.2500.total}. The whole of that tax is the harmonized component.

On {example:PE.sales.18000.amount} the tax is {example:PE.sales.18000.tax}. A shop that used Nova Scotia’s current rate would charge a different amount. This is not that shop.

## What is left out

Exempt and zero-rated supplies are not taxed in silence. The vehicle page handles a car. A municipal fee is not looked up from a postal code.

## Common mistakes

Charging {rate:NS.sales.hst} because the ferry crosses to Nova Scotia. Stacking a separate GST charge on top of the harmonized rate. GST is already inside the harmonized tax, which is why the GST line on the example is {example:PE.sales.2500.gst}. Putting the tax-included total back into the price field.

## Recent changes

The rate of {rate:PE.sales.hst} is the {year_label} row checked {updated}. The Nova Scotia change belongs on the Nova Scotia page.

## Neighbouring jurisdictions

Nova Scotia now charges {rate:NS.sales.hst}. On the sample price its tax is {example:NS.sales.2500.tax}, against {example:PE.sales.2500.tax} here. Before {date:NS.sales.latest_from}, Nova Scotia’s rate was {rate:NS.sales.prior.hst}.

New Brunswick’s rate is {rate:NB.sales.hst}, and its tax on the sample is {example:NB.sales.2500.tax}. Matching New Brunswick’s sales tax does not match New Brunswick’s income tax.

## Questions

### Is the Island’s rate the Nova Scotia rate?

No. The Island is {rate:PE.sales.hst}. Nova Scotia’s current rate is {rate:NS.sales.hst}.

### Where did GST go?

Into the harmonized tax. The separate GST line on the example is {example:PE.sales.2500.gst}.

### Does the rate rise with the price?

No. Both examples use {rate:PE.sales.hst}.

### Are cars included?

No. Use the vehicle page. A private sale can use the greater of price and wholesale.

### When is HST due to the account?

On the assigned filing frequency. This form does not store that date.

### Is this a return?

No. It is the {year_label} table, checked {updated}.

## Official sources

- [Canada Revenue Agency GST/HST rates](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/gst-hst-businesses/charge-collect-which-rate.html)
- [Prince Edward Island finance](https://www.princeedwardisland.ca/en/topic/taxes-and-your-money)
