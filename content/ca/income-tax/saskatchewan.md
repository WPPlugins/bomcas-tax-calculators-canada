---
excerpt: Saskatchewan income tax for {year_label} uses three brackets and a high basic personal amount that does not phase out. The page is an employment-income estimate.
---

## What this tax is

Saskatchewan income tax applies to taxable income on top of federal tax. The estimate is employment income for a single filer. Credits the form does not ask for are omitted.

The basic personal amount is {bpa:SK}, and the credit rate is {rate:SK.income.credit}. Tax owing for the year is due {deadline:balance_due}. The later filing date of {deadline:file_self_employed} does not move that payment date.

## How the calculation works

The engine deducts only the enhanced portion of CPP. EI premiums are not deductible. It then applies federal brackets and Saskatchewan brackets to the same taxable income.

Saskatchewan has three bands. Tax starts at {rate:SK.income.first} and the top rate is {rate:SK.income.top}. The basic amount does not phase out in this row. There is no Alberta-style supplemental credit and no Manitoba phase-out between two high incomes.

## Worked examples

Employment income of {example:SK.income.42000.employment} produces taxable income of {example:SK.income.42000.taxableIncome}. Federal tax is {example:SK.income.42000.federalTax} and Saskatchewan tax is {example:SK.income.42000.provincialTax}, for a combined {example:SK.income.42000.totalTax}. The provincial basic amount on that line is {example:SK.income.42000.provincialBpa}.

Raising the wage to {example:SK.income.86000.employment} raises combined tax to {example:SK.income.86000.totalTax}. The second band is doing more of the work. The amount left is {example:SK.income.86000.afterTax}.

At {example:SK.income.155000.employment}, combined tax is {example:SK.income.155000.totalTax}. The top rate of {rate:SK.income.top} applies to the slice above the last threshold. The basic amount is still the full {bpa:SK}, because this row does not phase it out.

## Credits and programs

The credit this form applies is the basic personal amount. The Saskatchewan low-income tax credit and the graduate tuition credit are not estimated. The Canada child benefit and the GST credit are not in the combined tax.

## Common mistakes

Importing Manitoba’s phase-out and reducing {bpa:SK} once income passes {money:MB.bpa.from}. That phase-out is Manitoba’s. Treating {example:SK.income.42000.afterTax} as a paycheque. Adding Saskatchewan’s provincial sales tax to the income-tax total.

## What changed for this year

Brackets and the basic amount are the {year_label} row checked {updated}. A change announced after that check is not written in.

## Neighbouring jurisdictions

Alberta’s first rate is {rate:AB.income.first} and its basic amount is {bpa:AB}. Alberta also has a supplemental credit this page does not apply to Saskatchewan. On the first sample wage, Alberta’s combined tax is {example:AB.income.42000.totalTax}, against Saskatchewan’s {example:SK.income.42000.totalTax}.

Manitoba’s first rate is {rate:MB.income.first}. Manitoba phases its basic amount from {money:MB.bpa.from} to {money:MB.bpa.to}. The same wage produces combined tax of {example:MB.income.42000.totalTax}.

## Questions

### Does the Saskatchewan basic amount phase out?

Not in this {year_label} row. It stays {bpa:SK}. Manitoba’s phase-out is a different province.

### How many rates are there?

Three, from {rate:SK.income.first} to {rate:SK.income.top}. The top rate is not a flat tax on the full wage.

### Is provincial sales tax included?

No. Sales tax is a separate page. {example:SK.income.42000.totalTax} is income tax on one wage.

### Why is the middle wage not double the first tax?

Because the bands and the basic amount are not a flat percentage of the wage. Read {example:SK.income.86000.totalTax} on its own.

### Can I file with this figure?

No. It is an estimate. It does not know a move, a spouse, or days of residency.

### Is the wage uploaded?

No, unless you later ask for an email or a PDF.

## Official sources

- [Saskatchewan personal income tax](https://www.saskatchewan.ca/residents/taxes-and-investments/personal-income-tax)
- [Canada Revenue Agency individual income tax rates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html)
- [Canada Revenue Agency important dates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/topics/important-dates-individuals.html)
