---
excerpt: Quebec sales tax for {year_label} is GST plus QST. QST is not harmonized sales tax, and it is not another province’s provincial sales tax. The two taxes stay separate.
---

## What this tax is

A standard taxable supply in Quebec bears the federal goods and services tax and the Quebec sales tax. The two are separate. QST is not harmonized into a single HST line, and it is not Ontario’s tax. This page is a general supply. It is not the welcome tax on a property transfer and it is not a vehicle private sale.

## How the calculation works

GST at {rate:QC.sales.gst} and QST at {rate:QC.sales.qst} are both calculated from the pre-tax price. They are added to make the tax line. The PST and HST components are zero because those labels are not the taxes Quebec charges. Revenu Québec administers QST. The Canada Revenue Agency administers GST.

## Worked examples

On {example:QC.sales.2500.amount}, GST is {example:QC.sales.2500.gst} and QST is {example:QC.sales.2500.qst}. The tax is {example:QC.sales.2500.tax}, and the amount with tax is {example:QC.sales.2500.total}. QST is the larger of the two components. It is not a second copy of GST.

On {example:QC.sales.18000.amount}, the tax is {example:QC.sales.18000.tax}. Both components scaled with the price. The receipt does not collapse them into harmonized tax.

## What is left out

Some supplies are zero-rated or exempt. This page does not reclassify them. A vehicle bought privately follows the vehicle rule, including the estimated-value comparison, and is not this general supply. The welcome tax uses different brackets.

## Common mistakes

Calling QST “HST” and dropping the GST line. Using Ontario’s single rate of {rate:ON.sales.hst} on a Quebec invoice. Typing {example:QC.sales.2500.total} into the before-tax box. Treating a zero PST line as a broken form.

## Recent changes

The GST rate of {rate:QC.sales.gst} and the QST rate of {rate:QC.sales.qst} are the {year_label} row checked {updated}. Nova Scotia’s harmonized-tax date is not a Quebec date.

## Neighbouring jurisdictions

Ontario charges one harmonized tax at {rate:ON.sales.hst}. On the same pre-tax price, Ontario’s tax is {example:ON.sales.2500.tax} and Quebec’s is {example:QC.sales.2500.tax}. The place of supply picks one system.

New Brunswick also uses a single harmonized rate, {rate:NB.sales.hst}. Its tax on that price is {example:NB.sales.2500.tax}. It has no QST line.

## Questions

### Why are there two amounts?

Because GST and QST are separate taxes. On the first price they are {example:QC.sales.2500.gst} and {example:QC.sales.2500.qst}.

### Is QST charged on top of GST?

On this table each tax is computed from the pre-tax price. The tax line is their sum, {example:QC.sales.2500.tax}.

### Is a zero PST a failure?

No. Quebec does not charge a tax under the PST label. QST is the provincial component.

### Does this include the welcome tax?

No. The welcome tax is a duty on a transfer of an immovable. It has its own page.

### Are vehicles included?

No. Open the vehicle page for a dealer or private sale.

### Is this a return?

No. It is the {year_label} table, checked {updated}.

## Official sources

- [Canada Revenue Agency GST/HST rates](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/gst-hst-businesses/charge-collect-which-rate.html)
- [Revenu Québec GST and QST](https://www.revenuquebec.ca/en/businesses/consumption-taxes/gsthst-and-qst/)
