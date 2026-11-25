---
excerpt: Nunavut income tax for {year_label} uses a low first rate and a high basic personal amount. Northern claims you leave blank stay omitted. Sales tax is separate.
---

## What this tax is

Nunavut income tax is the territorial tax on taxable income. The sales table charges GST only, which does not mean income tax is zero. This estimate is employment income for a single person. A northern residence deduction is not assumed.

The basic personal amount is {bpa:NU}. The credit rate is {rate:NU.income.credit}. A balance is due {deadline:balance_due}. Filing by {deadline:file_self_employed} does not delay the balance.

## How the calculation works

Remove only the enhanced CPP deduction. EI premiums are not deductible. Apply federal brackets and Nunavut brackets to the taxable income. Nunavut tax starts at {rate:NU.income.first}, the lowest first rate in the territorial rows, and the top rate is {rate:NU.income.top}. The basic amount is Nunavut’s own figure.

## Worked examples

On {example:NU.income.42000.employment}, territorial tax is {example:NU.income.42000.provincialTax} and federal tax is {example:NU.income.42000.federalTax}. Combined tax is {example:NU.income.42000.totalTax}. The territorial basic amount is {example:NU.income.42000.provincialBpa}. A large basic amount is why the territorial line is small relative to provinces with a smaller amount and a higher first rate. It is not a missing calculation.

On {example:NU.income.86000.employment}, combined tax is {example:NU.income.86000.totalTax}. The wage has entered higher bands. The amount left is {example:NU.income.86000.afterTax}.

On {example:NU.income.155000.employment}, combined tax is {example:NU.income.155000.totalTax}. The top rate of {rate:NU.income.top} applies to the slice above the last threshold.

## Credits and programs

The basic personal amount is what the form applies. Cost-of-living and travel deductions that depend on where you lived are not in the total. The Canada child benefit is not included.

## Common mistakes

Seeing a small territorial tax and retyping the wage because the form “looks too low.” The first wage’s territorial tax is {example:NU.income.42000.provincialTax} because the rate starts at {rate:NU.income.first} and the basic amount is {bpa:NU}. Using the Northwest Territories amount of {bpa:NT}. Adding a sales tax the territory does not levy.

## What changed for this year

The brackets and the basic amount are the {year_label} row checked {updated}.

## Neighbouring jurisdictions

The Northwest Territories start at {rate:NT.income.first} with a basic amount of {bpa:NT}. On the first wage their combined tax is {example:NT.income.42000.totalTax}, against Nunavut’s {example:NU.income.42000.totalTax}.

Yukon mirrors the federal basic amount of {bpa:YT} and starts at {rate:YT.income.first}. Yukon’s combined tax on that wage is {example:YT.income.42000.totalTax}.

## Questions

### Why is the territorial tax so much smaller than the federal tax?

The first rate is {rate:NU.income.first} and the basic amount is {bpa:NU}. Federal tax on the first wage is {example:NU.income.42000.federalTax}. Both numbers are part of the estimate.

### Are GST and income tax the same bill?

No. GST is charged on taxable supplies. It is not subtracted from {example:NU.income.42000.totalTax}.

### Are northern deductions included?

No. Entering nothing for them means they are omitted, not allowed in full.

### Does the top rate apply to all income?

No. {rate:NU.income.top} applies to the top slice.

### Is this an assessment?

No. It is a {year_label} estimate, checked {updated}.

### Is the wage uploaded?

Only if you later ask for an email or a PDF.

## Official sources

- [Nunavut Department of Finance](https://www.gov.nu.ca/finance)
- [Canada Revenue Agency individual income tax rates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html)
- [Canada Revenue Agency important dates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/topics/important-dates-individuals.html)
