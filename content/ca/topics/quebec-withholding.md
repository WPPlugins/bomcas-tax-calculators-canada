---
excerpt: Quebec payroll withholding for {year_label} follows the TP-1015 editions loaded in the rate file. A gratuity or a missing edition is unavailable, not zero.
---

## What this is

Quebec income tax on a regular pay is withheld under Revenu Québec’s guide TP-1015.F-V, not under the federal payroll tables alone. The Quebec Pension Plan, Quebec employment insurance, and the Quebec Parental Insurance Plan are separate lines. This screen estimates regular payments for editions the file actually contains. A gratuity, a retroactive payment, or a year with no loaded edition is unavailable. It is not shown as zero.

## How the calculation works

The edition for the year supplies the formulas. Federal withholding and Quebec withholding are both possible on a Quebec pay, and they are not the same number. QPP is not CPP. QPIP is not EI. If the file has no edition for the year, the screen refuses the income-tax withholding rather than borrowing another year’s tables.

## Worked examples

A regular salary with a loaded edition produces a Quebec withholding from that edition and a federal withholding from the federal tables. Adding them is the income-tax withholding, not the employee’s net. QPP, Quebec EI, and QPIP still come off the gross.

A bonus or a retroactive raise is not a regular payment. The screen does not invent TP-1015 treatment for it. The amount stays unavailable so you do not remit a made-up figure.

A year the file does not contain stays unavailable even if you liked last year’s result. Last year’s guide is not this year’s guide.

## Common mistakes

Using Ontario or federal payroll software alone and calling the result Quebec tax. Entering zero when the screen says unavailable, and remitting nothing. Treating QPIP as a second EI premium on top of the Quebec EI rate the file already uses.

## Recent changes

The loaded editions are the ones checked {updated}. Revenu Québec replaces the guide. When a new edition is not in the file, this screen does not approximate it.

## Questions

### Why is the result blank?

Because the payment is not a regular payment the edition covers, or the year has no edition in the file. Blank means unavailable, not zero.

### Is QPP the same as CPP?

No. Quebec employees contribute to the Quebec Pension Plan. Do not withhold CPP as well on the same earnings.

### What is QPIP?

The Quebec Parental Insurance Plan. It is not federal EI and it is not a second copy of Quebec EI.

### Can I use the federal calculator for Quebec tax?

No. Quebec tax withholding follows TP-1015.F-V. The federal tables do not replace it.

### Does a blank let me skip the remittance?

No. Unavailable means this screen will not invent the number. You still withhold under the guide that applies. You do not withhold zero because a calculator refused to guess.

## Official sources

- [Revenu Québec TP-1015.F-V](https://www.revenuquebec.ca/en/online-services/forms-and-publications/current-details/tp-1015-f-v/)
