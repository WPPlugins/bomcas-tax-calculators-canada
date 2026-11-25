---
excerpt: New Brunswick income tax for {year_label} uses four brackets on employment income. Harmonized sales tax is not part of this return. It is not a payroll remittance.
---

## What this tax is

New Brunswick income tax is the provincial tax on taxable income. Harmonized sales tax is a different tax and is not added here. The estimate is employment income for a single person. A spouse, a dependant, tuition, medical expenses, and donations are left out.

The basic personal amount is {bpa:NB}. The credit rate is {rate:NB.income.credit}. A balance is due {deadline:balance_due}. A person who carried on a business may file by {deadline:file_self_employed} without moving that payment date.

## How the calculation works

Only the enhanced portion of CPP comes off the wage. EI premiums are not deductible. Federal tax and New Brunswick tax are calculated on the taxable income that remains. New Brunswick tax starts at {rate:NB.income.first} and the top rate is {rate:NB.income.top}. There are four bands. There is no Quebec abatement and no Ontario surtax.

## Worked examples

Employment income of {example:NB.income.42000.employment} produces federal tax of {example:NB.income.42000.federalTax} and New Brunswick tax of {example:NB.income.42000.provincialTax}. Combined tax is {example:NB.income.42000.totalTax}. The provincial basic amount on the line is {example:NB.income.42000.provincialBpa}.

At {example:NB.income.86000.employment} the wage has moved further through the bands. Combined tax is {example:NB.income.86000.totalTax}, and {example:NB.income.86000.afterTax} remains. That is not double the first result.

At {example:NB.income.155000.employment} combined tax is {example:NB.income.155000.totalTax}. The top rate of {rate:NB.income.top} applies to the slice above the last threshold, not to the entire wage.

## Credits and programs

The basic personal amount is the credit this form applies. New Brunswick’s low-income tax reduction, the tuition rebate, and the home energy benefit are not estimated. The Canada child benefit is not in the combined tax.

## Common mistakes

Adding harmonized sales tax to {example:NB.income.42000.totalTax}. Using Nova Scotia’s basic amount in place of {bpa:NB}. Reading {example:NB.income.42000.afterTax} as the paycheque. Nova Scotia’s HST change date has nothing to do with this income-tax return.

## What changed for this year

The four brackets and the basic amount are the {year_label} row checked {updated}. A budget rate that is not in the file is not applied.

## Neighbouring jurisdictions

Nova Scotia’s first rate is {rate:NS.income.first} and its basic amount is {bpa:NS}. On the first sample wage, Nova Scotia’s combined tax is {example:NS.income.42000.totalTax}, against New Brunswick’s {example:NB.income.42000.totalTax}. Nova Scotia’s basic amount is stored as a flat figure. That is not a reason to change New Brunswick’s amount.

Prince Edward Island’s first rate is {rate:PE.income.first} and its basic amount is {bpa:PE}. The same wage produces combined tax of {example:PE.income.42000.totalTax}.

## Questions

### Is harmonized sales tax part of this page?

No. HST is a sales tax. This page is income tax, from {rate:NB.income.first} up to {rate:NB.income.top}.

### Does New Brunswick have a surtax?

Not in this rate row. Do not borrow Ontario’s surtax thresholds.

### Why did the middle wage not double the tax?

The bands and the basic amount of {bpa:NB} are not a flat percentage. The middle result is {example:NB.income.86000.totalTax}.

### Are low-income benefits included?

No. They are outside this employment-income estimate.

### Is the figure a filing?

No. It is a {year_label} estimate, checked {updated}.

### Does the wage get uploaded?

Only if you later ask for an email or a PDF.

## Official sources

- [New Brunswick taxes](https://www2.gnb.ca/content/gnb/en/departments/finance/taxes.html)
- [Canada Revenue Agency individual income tax rates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html)
- [Canada Revenue Agency important dates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/topics/important-dates-individuals.html)
