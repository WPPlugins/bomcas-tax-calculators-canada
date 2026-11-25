---
excerpt: British Columbia income tax for {year_label} estimates employment income, including the tax reduction and the basic personal amount. It is not a return.
---

## What this tax is

British Columbia income tax is the provincial tax on taxable income, charged on top of federal income tax. This page estimates it for a person who has employment income, files as a single person, and does not claim a spouse, a dependant, tuition, medical expenses, or donations. Those credits are left out. They are not filled in as zero.

The estimate uses the {year_label} brackets and the basic personal amount of {bpa:BC}. The credit rate on that amount is {rate:BC.income.credit}. A balance owing for the year is due {deadline:balance_due}. Someone who carried on a business may file as late as {deadline:file_self_employed}, but the balance is still due on the earlier date.

## How the calculation works

Taxable income is employment income after the enhanced CPP deduction. EI premiums are not deductible. Federal tax and British Columbia tax are calculated on that same taxable income and kept separate.

British Columbia tax starts at {rate:BC.income.first} and rises through the published bands to {rate:BC.income.top}. The basic personal amount credit comes off the provincial tax. A tax reduction then applies when income is modest. The full reduction is available at lower incomes, it shrinks as income rises, and it disappears once income is high enough that the reduction in the rate file is exhausted. The reduction is not a separate bracket.

Payroll withholding is a different calculation. On a pay date on or after {deadline:bc_payroll_july}, British Columbia withholding uses {rate:BC.payroll_july}, together with the July basic reduction of {money:BC.payroll_july_reduction}. Do not copy that July rate onto the annual return. The return still starts at {rate:BC.income.first}.

## Worked examples

The first example is employment income of {example:BC.income.42000.employment}. Taxable income is {example:BC.income.42000.taxableIncome}. Federal tax is {example:BC.income.42000.federalTax} and British Columbia tax is {example:BC.income.42000.provincialTax}. Together that is {example:BC.income.42000.totalTax}, so {example:BC.income.42000.afterTax} remains after those two taxes. The federal basic personal amount used in the credit is {example:BC.income.42000.federalBpa}, and the provincial amount is {example:BC.income.42000.provincialBpa}.

The second example raises the wage to {example:BC.income.86000.employment}. Combined tax becomes {example:BC.income.86000.totalTax}, and {example:BC.income.86000.afterTax} is left. The increase is not a flat percentage of the first result, because a higher wage uses higher bands and the tax reduction no longer works the same way.

The third example is {example:BC.income.155000.employment} of employment income. Combined tax is {example:BC.income.155000.totalTax}, leaving {example:BC.income.155000.afterTax}. At this level the top provincial rate of {rate:BC.income.top} is in play on the slice above the last threshold. The whole wage is not taxed at that rate.

## Credits and programs

The basic personal amount of {bpa:BC} is the credit this form applies. The tax reduction is the provincial program for modest incomes. The Canada child benefit, the GST credit, and the renter’s credit are not in these totals. Age, pension, and disability amounts are not in them either.

Provincial sales tax is a separate tax. It does not change the income-tax result on this page.

## Common mistakes

People quote {example:BC.income.42000.totalTax} as if every British Columbia resident owed it. It is the result for one wage. People also copy the July withholding rate of {rate:BC.payroll_july} onto the T1. That rate is for paycheques after {deadline:bc_payroll_july}, not for the return. A third mistake is to treat the amount left, such as {example:BC.income.42000.afterTax}, as take-home pay. A paycheque still has pension and employment insurance taken off in a different way.

## What changed for this year

The figures on this page are the {year_label} row, checked {updated}. If a budget changes a bracket or the reduction after that check, the page does not guess the new number. The July withholding rate and the annual return rate remain two different figures in the file.

## Neighbouring jurisdictions

Alberta’s first rate is {rate:AB.income.first} and its basic personal amount is {bpa:AB}. On employment income of {example:AB.income.42000.employment}, Alberta’s combined tax is {example:AB.income.42000.totalTax}, compared with {example:BC.income.42000.totalTax} in British Columbia. Alberta has no provincial sales tax, which does not belong in this income-tax comparison.

Yukon’s first rate is {rate:YT.income.first} and its basic personal amount follows the federal amount, {bpa:YT}. The same wage produces combined tax of {example:YT.income.42000.totalTax}. Yukon does not use the British Columbia tax reduction.

## Questions

### Does British Columbia tax this wage at one flat rate?

No. The tax starts at {rate:BC.income.first} and the top rate is {rate:BC.income.top}. Only the slice of income inside a band is taxed at that band’s rate.

### Is the July payroll rate the rate on my return?

No. After {deadline:bc_payroll_july}, withholding can use {rate:BC.payroll_july}. The return still uses the annual bands, starting at {rate:BC.income.first}.

### Why is the tax reduction missing from a higher wage?

The reduction shrinks as income rises and is gone above the upper threshold in the rate file. A wage such as {example:BC.income.155000.employment} is past the point of the full reduction.

### Are the child benefit and the GST credit in the result?

No. Those payments are not subtracted and not added. The combined tax of {example:BC.income.42000.totalTax} on the first wage is income tax only.

### Can I use this as my notice of assessment?

No. It is an estimate from published {year_label} rates for employment income with the credits listed above left out. It does not know a move, a second job, or days of residency.

### Does the calculator send the wage I type to BOMCAS?

No. The arithmetic stays in the browser unless you later ask for an email or a PDF.

## Official sources

- [British Columbia income tax rates](https://www2.gov.bc.ca/gov/content/taxes/income-taxes/personal/tax-rates)
- [Canada Revenue Agency individual income tax rates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html)
- [Canada Revenue Agency important dates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/topics/important-dates-individuals.html)
