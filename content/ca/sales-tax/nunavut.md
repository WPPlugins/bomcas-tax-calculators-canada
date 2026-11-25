---
excerpt: Nunavut sales tax for {year_label} is the federal GST. There is no territorial sales tax to add, and a southern retail-tax line does not belong. It is one supply.
---

## What this tax is

Nunavut does not levy a territorial sales tax. A standard taxable supply is charged GST at {rate:NU.sales.gst}. Manitoba retail sales tax, Saskatchewan provincial sales tax, and Quebec sales tax are not territorial taxes. This page is a general supply. Nunavut income tax is real and is calculated on the income-tax page, not on the till.

## How the calculation works

Apply the GST rate to the price before tax. Leave the provincial, harmonized, and Quebec components at zero. Do not borrow a southern PST rate “so the invoice looks complete.” An invoice that looks complete with a tax the territory does not charge is overcharging.

## Worked examples

A price of {example:NU.sales.2500.amount} draws GST of {example:NU.sales.2500.gst}. The tax is {example:NU.sales.2500.tax}. Retail sales tax on the line is {example:NU.sales.2500.pst}.

A price of {example:NU.sales.18000.amount} draws tax of {example:NU.sales.18000.tax}. If a template added Manitoba’s retail tax, it would no longer match this result. Manitoba’s tax on the first price is {example:MB.sales.2500.tax}, and that is a Manitoba supply.

## What is left out

Vehicles are on the vehicle page. There is no territorial registration tax in the sales table. Exempt supplies are not assigned a tax. The form does not know a hamlet’s fee schedule.

## Common mistakes

Importing a PST line from a southern supplier’s software. Adding Nunavut income tax at {rate:NU.income.first} to the receipt. Treating zero provincial tax as an error and typing a number into the gap.

## Recent changes

GST of {rate:NU.sales.gst} is the {year_label} row checked {updated}. Nunavut did not change rates on {date:NS.sales.latest_from}.

## Neighbouring jurisdictions

The Northwest Territories also charge GST only. Their tax on the sample is {example:NT.sales.2500.tax}. Use the page for the territory where the supply is taxed. Do not average them, and do not assume the income-tax results match. They do not.

Quebec’s tax on the same price is {example:QC.sales.2500.tax}, because QST is charged there. QST is not a Nunavut tax.

## Questions

### What do I charge?

GST at {rate:NU.sales.gst}. On the first price that is {example:NU.sales.2500.gst}.

### Why not copy the Manitoba invoice?

Manitoba charges retail sales tax as well. Its tax on this price is {example:MB.sales.2500.tax}. Nunavut does not.

### Is income tax due at the till?

No. Income tax is withheld on wages or paid on a return. It is not this sales tax.

### Is a zero a broken calculator?

No. The zero is the territorial sales tax the law does not levy.

### Are vehicles here?

No. Open the vehicle page.

### Is this a return?

No. It is the {year_label} table, checked {updated}.

## Official sources

- [Canada Revenue Agency GST/HST rates](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/gst-hst-businesses/charge-collect-which-rate.html)
- [Nunavut Department of Finance](https://www.gov.nu.ca/finance)
