---
excerpt: New Brunswick sales tax for {year_label} is harmonized sales tax at one rate. Nova Scotia’s rate-change date does not apply to a New Brunswick supply.
---

## What this tax is

New Brunswick charges harmonized sales tax at {rate:NB.sales.hst} on a standard taxable supply. There is no separate GST line and no provincial sales tax beside it. This page is that general supply. It is not income tax and it is not a vehicle private sale.

## How the calculation works

Multiply the pre-tax price by {rate:NB.sales.hst}. The harmonized component is the tax. GST, PST, and QST print as zero because they are not separate charges. The rate does not change on Nova Scotia’s date of {date:NS.sales.latest_from}.

## Worked examples

On {example:NB.sales.2500.amount} the harmonized tax is {example:NB.sales.2500.hst}. The tax line is {example:NB.sales.2500.tax} and the amount with tax is {example:NB.sales.2500.total}.

On {example:NB.sales.18000.amount} the tax is {example:NB.sales.18000.tax}. The relationship is the rate, not a second provincial layer.

## What is left out

Point-of-sale rebates and exempt supplies are not invented. A vehicle is priced on the vehicle page. Real property is not this form.

## Common mistakes

Using Nova Scotia’s current rate of {rate:NS.sales.hst} because the provinces are neighbours. Stacking GST on top of the harmonized tax. Entering {example:NB.sales.2500.total} as the pre-tax price.

## Recent changes

The rate of {rate:NB.sales.hst} is the {year_label} row checked {updated}. The Nova Scotia change on {date:NS.sales.latest_from} is not a New Brunswick change.

## Neighbouring jurisdictions

Nova Scotia’s current harmonized rate is {rate:NS.sales.hst}, down from {rate:NS.sales.prior.hst} on {date:NS.sales.latest_from}. Its tax on the sample price is {example:NS.sales.2500.tax}, against New Brunswick’s {example:NB.sales.2500.tax}.

Quebec charges GST plus QST, not this single rate. Quebec’s tax on the same price is {example:QC.sales.2500.tax}.

## Questions

### Is the rate the same as Nova Scotia’s?

Not after {date:NS.sales.latest_from}. New Brunswick is {rate:NB.sales.hst}. Nova Scotia’s current rate is {rate:NS.sales.hst}.

### Why is GST zero?

Because the tax is harmonized. The tax line is {example:NB.sales.2500.tax}.

### Does a zero PST mean the form failed?

No. There is no separate PST. The harmonized tax is the provincial and federal tax together.

### Are vehicles included?

No. Use the vehicle page.

### When do I remit?

On the frequency assigned to the account. This page does not look it up.

### Is this a return?

No. It is the {year_label} table, checked {updated}.

## Official sources

- [Canada Revenue Agency GST/HST rates](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/gst-hst-businesses/charge-collect-which-rate.html)
- [New Brunswick finance](https://www2.gnb.ca/content/gnb/en/departments/finance.html)
