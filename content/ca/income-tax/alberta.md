---
excerpt: Alberta income tax for {year_label} pairs a high basic personal amount with a supplemental credit. The estimate covers employment income and is not a return.
---

## What this tax is

Alberta income tax is the provincial tax on taxable income, added to federal tax. This estimate is for a person with employment income who files as a single person. A spouse, a dependant, tuition, medical expenses, and donations are left out. They are not entered as zero.

The basic personal amount is {bpa:AB}. The credit rate on it is {rate:AB.income.credit}. A balance for the year is due {deadline:balance_due}. A person who carried on a business may file as late as {deadline:file_self_employed}, and the balance is still due on the earlier date.

## How the calculation works

Only the enhanced portion of CPP is deducted before the brackets. EI premiums are not deductible. Federal tax and Alberta tax are then computed on the same taxable income and kept apart.

Alberta tax starts at {rate:AB.income.first} and rises through the bands to {rate:AB.income.top}. The basic personal amount credit comes off the provincial tax. The rate file also carries a supplemental credit: the threshold is {money:AB.k5p.threshold} and the multiplier is {rate:AB.k5p}. That credit is not a second basic personal amount, and it is not a sales-tax rebate. Alberta has no provincial sales tax, which does not change the income-tax arithmetic on this page.

## Worked examples

Someone earning {example:AB.income.42000.employment} has taxable income of {example:AB.income.42000.taxableIncome}. Federal tax is {example:AB.income.42000.federalTax} and Alberta tax is {example:AB.income.42000.provincialTax}. Combined, that is {example:AB.income.42000.totalTax}, so {example:AB.income.42000.afterTax} remains after those two taxes. The federal basic amount used in the credit is {example:AB.income.42000.federalBpa} and the Alberta amount is {example:AB.income.42000.provincialBpa}.

At {example:AB.income.86000.employment} the combined tax is {example:AB.income.86000.totalTax}. The jump from the first wage is not a flat percentage, because a higher wage uses higher bands. The amount left is {example:AB.income.86000.afterTax}.

At {example:AB.income.155000.employment} the combined tax is {example:AB.income.155000.totalTax}, leaving {example:AB.income.155000.afterTax}. The top rate of {rate:AB.income.top} applies only to the slice above the last threshold, not to the whole wage.

## Credits and programs

The basic personal amount of {bpa:AB} and the supplemental credit in the rate file are what this form applies. The Canada child benefit, the GST credit, and Alberta’s own benefit programs are not in the total. Age, pension, and disability amounts are not in it either.

## Common mistakes

Quoting {example:AB.income.42000.totalTax} as the tax every Albertan owes. It is one wage. Treating the amount left, {example:AB.income.42000.afterTax}, as take-home pay. A paycheque still has pension and employment insurance taken off in a different way. Adding a neighbour’s provincial sales tax to this income-tax result.

## What changed for this year

The brackets, the basic amount, and the supplemental credit are the {year_label} row, checked {updated}. A budget that moves a threshold after that check is not guessed.

## Neighbouring jurisdictions

British Columbia starts at {rate:BC.income.first} with a basic amount of {bpa:BC}, then a tax reduction for modest incomes. On {example:BC.income.42000.employment} of employment income, British Columbia’s combined tax is {example:BC.income.42000.totalTax}, against Alberta’s {example:AB.income.42000.totalTax}. British Columbia also charges provincial sales tax. That sales tax is not part of either income-tax figure.

Saskatchewan’s first rate is {rate:SK.income.first} and its basic amount is {bpa:SK}. The same wage produces combined tax of {example:SK.income.42000.totalTax}. Saskatchewan does not use Alberta’s supplemental credit.

## Questions

### Does Alberta tax this wage at one rate?

No. Tax starts at {rate:AB.income.first} and the top rate is {rate:AB.income.top}. Only the slice inside a band is taxed at that band’s rate.

### What is the supplemental credit?

It is the credit stored as a threshold of {money:AB.k5p.threshold} and a multiplier of {rate:AB.k5p}. It is not the basic personal amount of {bpa:AB}.

### Is there a provincial sales tax to add?

No. Alberta does not levy a provincial sales tax. Do not borrow British Columbia’s sales tax and add it to {example:AB.income.42000.totalTax}.

### Are the child benefit and the GST credit in the result?

No. The combined tax on the first wage, {example:AB.income.42000.totalTax}, is income tax only.

### Can I use this as a notice of assessment?

No. It is an estimate from published {year_label} rates for employment income, with the credits listed above left out. It does not know a move or a second job.

### Does the calculator send the wage I type to BOMCAS?

No. The arithmetic stays in the browser unless you later ask for an email or a PDF.

## Official sources

- [Alberta personal income tax](https://www.alberta.ca/personal-income-tax)
- [Canada Revenue Agency individual income tax rates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html)
- [Canada Revenue Agency important dates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/topics/important-dates-individuals.html)
