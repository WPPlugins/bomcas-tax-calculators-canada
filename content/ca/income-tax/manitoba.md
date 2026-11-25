---
excerpt: Manitoba income tax for {year_label} uses three brackets, and the basic personal amount shrinks between two high thresholds. The page estimates employment income only.
---

## What this tax is

Manitoba income tax is the provincial tax on taxable income. This page estimates it for employment income of a single person. A spouse, a dependant, tuition, medical expenses, and donations are omitted rather than filled in as zero.

The basic personal amount starts at {bpa:MB}. The credit rate is {rate:MB.income.credit}. The balance for the year is due {deadline:balance_due}. A business filer may file by {deadline:file_self_employed} and still owes the balance on the earlier date.

## How the calculation works

Employment income is reduced by the enhanced CPP deduction. EI premiums are not deductible. Federal tax and Manitoba tax are calculated on that taxable income and shown separately.

Manitoba uses three brackets. Tax starts at {rate:MB.income.first} and the top rate is {rate:MB.income.top}. The basic personal amount is not flat for every income. In the rate file it phases down from {money:MB.bpa.from} to {money:MB.bpa.to}. Income below the start of that phase keeps the full amount. Income inside the phase keeps less. Income above the end of the phase does not keep it.

## Worked examples

On employment income of {example:MB.income.42000.employment}, taxable income is {example:MB.income.42000.taxableIncome}. Federal tax is {example:MB.income.42000.federalTax} and Manitoba tax is {example:MB.income.42000.provincialTax}. Together that is {example:MB.income.42000.totalTax}. This wage is below {money:MB.bpa.from}, so the phase-out has not started. The provincial basic amount on the line is {example:MB.income.42000.provincialBpa}.

The middle wage of {example:MB.income.86000.employment} is still below the phase-out. Combined tax is {example:MB.income.86000.totalTax}, and {example:MB.income.86000.afterTax} remains. The increase comes from the brackets, not from a shrinking basic amount.

The high wage of {example:MB.income.155000.employment} produces combined tax of {example:MB.income.155000.totalTax}. It is still under {money:MB.bpa.from}. Do not describe this wage as having lost the basic amount. The phase-out begins higher than this sample.

## Credits and programs

This form applies the basic personal amount, including the phase-out when income reaches it. Manitoba’s education property-tax credit and other provincial benefits are not subtracted. The Canada child benefit and the GST credit are not in the total.

## Common mistakes

Assuming the basic amount has already shrunk at {example:MB.income.155000.employment}. It has not. The phase-out starts at {money:MB.bpa.from}. Another mistake is to quote {example:MB.income.42000.afterTax} as take-home pay. Pension and employment insurance are still to come off a paycheque. A third is to treat Manitoba’s retail sales tax as part of this income-tax bill.

## What changed for this year

The three brackets and the phase-out thresholds are the {year_label} figures checked {updated}. The page does not blend last year’s basic amount into this year’s wage.

## Neighbouring jurisdictions

Saskatchewan also uses three brackets, but it does not phase the basic amount the way Manitoba does. Saskatchewan’s first rate is {rate:SK.income.first} and its basic amount is {bpa:SK}. On the first sample wage, Saskatchewan’s combined tax is {example:SK.income.42000.totalTax}, compared with Manitoba’s {example:MB.income.42000.totalTax}.

Ontario’s first rate is {rate:ON.income.first}. Ontario adds surtax and a health premium instead of phasing the basic amount between {money:MB.bpa.from} and {money:MB.bpa.to}. The same wage in Ontario produces combined tax of {example:ON.income.42000.totalTax}.

## Questions

### When does the Manitoba basic amount shrink?

It starts to shrink at {money:MB.bpa.from} and is phased out by {money:MB.bpa.to}. The three sample wages on this page are all below the start.

### How many brackets are there?

Three. The first rate is {rate:MB.income.first} and the top rate is {rate:MB.income.top}. The whole wage is not taxed at the top rate.

### Is retail sales tax in this result?

No. Retail sales tax is a separate tax. The combined figure of {example:MB.income.42000.totalTax} is income tax.

### Are family credits included?

No. A spouse, a dependant, and donations are left out. They are not zeros the form invented.

### Is this a notice of assessment?

No. It is a {year_label} estimate for employment income, checked {updated}.

### Does a typed wage leave the browser?

Only if you later ask for an email or a PDF. The calculation itself stays in the browser.

## Official sources

- [Manitoba personal income taxes](https://www.gov.mb.ca/finance/personal/ptaxes.html)
- [Canada Revenue Agency individual income tax rates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html)
- [Canada Revenue Agency important dates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/topics/important-dates-individuals.html)
