---
excerpt: Newfoundland and Labrador income tax for {year_label} uses its own brackets on employment income. It is not a payroll remittance. Labrador is included.
---

## What this tax is

Newfoundland and Labrador income tax is the provincial tax on taxable income, for both the island and Labrador. The estimate does not split the province in two. It is employment income for a single person. Harmonized sales tax is not part of the income-tax total.

The basic personal amount is {bpa:NL}. The credit rate is {rate:NL.income.credit}. A balance is due {deadline:balance_due}. A business filer may file by {deadline:file_self_employed} and still pays the balance on the earlier date.

## How the calculation works

Take only the enhanced CPP deduction. EI premiums are not deductible. Calculate federal tax and provincial tax on the taxable income. Provincial tax starts at {rate:NL.income.first}. The table has more bands than the Maritime provinces, and the top rate is {rate:NL.income.top}. That top rate applies only after income has passed every lower band.

## Worked examples

On {example:NL.income.42000.employment}, most of the taxable income is still in the lower bands. Federal tax is {example:NL.income.42000.federalTax} and provincial tax is {example:NL.income.42000.provincialTax}. Combined tax is {example:NL.income.42000.totalTax}. The top rate is not the rate on this wage.

On {example:NL.income.86000.employment}, combined tax is {example:NL.income.86000.totalTax}. The middle bands are in use. The amount left is {example:NL.income.86000.afterTax}.

On {example:NL.income.155000.employment}, combined tax is {example:NL.income.155000.totalTax}. Part of this wage can reach the higher bands. The slice below each threshold is still taxed at the lower rate for that band. Do not multiply the whole wage by {rate:NL.income.top}.

## Credits and programs

The basic personal amount is the credit applied here. The low-income tax reduction and other provincial benefits are not in the engine result. The Canada child benefit is not included.

## Common mistakes

Using one rate on the entire wage because the top rate is high. Using Nova Scotia’s HST date as if it changed this income tax. Reading {example:NL.income.42000.afterTax} as take-home pay. Treating Labrador as a separate income-tax table when this file has one.

## What changed for this year

The brackets and the basic amount of {bpa:NL} are the {year_label} row checked {updated}.

## Neighbouring jurisdictions

Nova Scotia’s first rate is {rate:NS.income.first} and its basic amount is the flat {bpa:NS}. On the first wage, Nova Scotia’s combined tax is {example:NS.income.42000.totalTax}, against {example:NL.income.42000.totalTax} here. Nova Scotia does not use this province’s top rate of {rate:NL.income.top}.

Quebec’s top rate is {rate:QC.income.top}, and Quebec also abates federal tax by {rate:QC.abatement}. A Newfoundland and Labrador return does not receive that abatement. Quebec’s combined tax on the first wage is {example:QC.income.42000.totalTax}.

## Questions

### Is Labrador taxed separately?

Not in this table. The province is one set of brackets, from {rate:NL.income.first} to {rate:NL.income.top}.

### Does the top rate apply to a modest wage?

No. The first sample’s provincial tax is {example:NL.income.42000.provincialTax}. The top rate is for the slice above the last threshold.

### Is HST in the income-tax figure?

No. HST is a sales tax. Combined tax of {example:NL.income.42000.totalTax} is income tax.

### Are low-income reductions included?

No. The form applies the basic personal amount and the brackets.

### Is this an assessment?

No. It is a {year_label} estimate, checked {updated}.

### Is the wage stored?

Only if you later ask for an email or a PDF.

## Official sources

- [Newfoundland and Labrador personal income tax](https://www.gov.nl.ca/fin/tax-programs-incentives/personal/income/)
- [Canada Revenue Agency individual income tax rates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html)
- [Canada Revenue Agency important dates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/topics/important-dates-individuals.html)
