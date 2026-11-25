---
excerpt: Yukon income tax for {year_label} uses territorial brackets. The basic personal amount follows the federal amount and its phase-out. Employment income only.
---

## What this tax is

Yukon income tax is the territorial tax on taxable income. The basic personal amount follows the federal amount, which is {bpa:YT} before any phase-out. This estimate is employment income for a single person. A northern deduction you do not enter is not filled in.

The credit rate is {rate:YT.income.credit}. A balance is due {deadline:balance_due}. The business filing date {deadline:file_self_employed} does not move the balance. There is no territorial sales tax to add.

## How the calculation works

Only the enhanced portion of CPP comes off the wage. EI premiums are not deductible. Federal tax uses the federal brackets, starting at {rate:FED.income.first}. Yukon tax uses Yukon brackets, starting at {rate:YT.income.first} and reaching {rate:YT.income.top}. Because the basic amount mirrors the federal amount, it also follows the federal phase-out, which begins at {money:YT.bpa.from} and ends at {money:YT.bpa.to}. Below the start of that phase-out, the full amount is available.

## Worked examples

On {example:YT.income.42000.employment}, territorial tax is {example:YT.income.42000.provincialTax} and federal tax is {example:YT.income.42000.federalTax}. Combined tax is {example:YT.income.42000.totalTax}. This wage is far below {money:YT.bpa.from}, so the phase-out is not at work. The territorial basic amount on the line is {example:YT.income.42000.provincialBpa}.

On {example:YT.income.86000.employment}, combined tax is {example:YT.income.86000.totalTax}. The phase-out still has not started. The higher bill is the brackets.

On {example:YT.income.155000.employment}, combined tax is {example:YT.income.155000.totalTax}. This wage is still under {money:YT.bpa.from}. Do not phase the basic amount out on this sample. The top Yukon rate of {rate:YT.income.top} applies only to income above the last threshold.

## Credits and programs

The mirrored basic amount is the credit this form applies, together with the phase-out when income reaches it. Yukon’s other credits, and the Canada child benefit, are not in the total. The federal Canada employment amount is a federal credit. It is not a second Yukon basic amount.

## Common mistakes

Phasing out the basic amount on a wage under {money:YT.bpa.from}. Copying British Columbia’s tax reduction into Yukon. Treating Yukon tax as a flat fraction of the federal tax instead of reading the Yukon brackets. Adding British Columbia PST because a purchase was shipped from there. Place of supply is a sales-tax question.

## What changed for this year

The Yukon brackets, the mirrored basic amount, and the phase-out from {money:YT.bpa.from} to {money:YT.bpa.to} are the {year_label} row checked {updated}.

## Neighbouring jurisdictions

British Columbia starts at {rate:BC.income.first} with its own basic amount of {bpa:BC} and a tax reduction for modest incomes. British Columbia does not mirror the federal phase-out. On the first wage, British Columbia’s combined tax is {example:BC.income.42000.totalTax}, against Yukon’s {example:YT.income.42000.totalTax}.

The Northwest Territories use a basic amount of {bpa:NT} that does not follow the federal phase-out. Their combined tax on that wage is {example:NT.income.42000.totalTax}.

## Questions

### Does the Yukon basic amount match the federal amount?

Yes. Before the phase-out it is {bpa:YT}. It begins to fall at {money:YT.bpa.from}.

### Has the third sample lost the basic amount?

No. {example:YT.income.155000.employment} is still below {money:YT.bpa.from}.

### Is Yukon tax a percentage of federal tax?

No. Yukon has its own brackets, from {rate:YT.income.first} to {rate:YT.income.top}. The basic amount is what mirrors the federal figure.

### Is there territorial sales tax?

No. GST is the sales tax. It is not part of {example:YT.income.42000.totalTax}.

### Is this a filing?

No. It is a {year_label} estimate, checked {updated}.

### Does the wage leave the browser?

Only if you later ask for an email or a PDF.

## Official sources

- [Yukon income tax](https://yukon.ca/en/income-tax)
- [Canada Revenue Agency individual income tax rates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html)
- [Canada Revenue Agency important dates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/topics/important-dates-individuals.html)
