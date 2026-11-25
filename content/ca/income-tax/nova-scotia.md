---
excerpt: Nova Scotia income tax for {year_label} starts from its own brackets and a flat basic personal amount. The HST change is a sales-tax fact, not an income-tax fact.
---

## What this tax is

Nova Scotia income tax is the provincial tax on taxable income. The harmonized sales tax, including the change dated {date:NS.sales.latest_from}, is not part of this return. The estimate is employment income for a single person, with spouse, dependant, tuition, medical, and donation claims left out.

The basic personal amount is {bpa:NS}. In the rate file it is a flat amount. It does not follow the federal phase-out. The credit rate is {rate:NS.income.credit}. A balance is due {deadline:balance_due}. The business filing date of {deadline:file_self_employed} does not delay the balance.

## How the calculation works

The wage is reduced by the enhanced CPP deduction. EI premiums are not deductible. Federal tax and Nova Scotia tax are then calculated separately. Nova Scotia tax starts at {rate:NS.income.first} and the top rate is {rate:NS.income.top}. The flat basic amount comes off through the credit. Do not phase it out just because the federal amount phases out.

## Worked examples

On {example:NS.income.42000.employment}, federal tax is {example:NS.income.42000.federalTax} and Nova Scotia tax is {example:NS.income.42000.provincialTax}. Combined tax is {example:NS.income.42000.totalTax}. The provincial basic amount on the line is {example:NS.income.42000.provincialBpa}, the flat figure, not a phased federal amount.

At {example:NS.income.86000.employment}, combined tax is {example:NS.income.86000.totalTax}. The basic amount has not been reduced by a phase-out. The higher tax comes from the brackets.

At {example:NS.income.155000.employment}, combined tax is {example:NS.income.155000.totalTax}. The top rate of {rate:NS.income.top} applies to the top slice. The basic amount remains the flat {bpa:NS}.

## Credits and programs

This form applies the flat basic personal amount. The Nova Scotia affordable living tax credit and the poverty-reduction credit are not in the total. The Canada child benefit and the GST credit are not in it either.

## Common mistakes

Phasing out {bpa:NS} because the federal basic amount phases out. Putting the HST change of {date:NS.sales.latest_from} onto the T1. Quoting {example:NS.income.42000.afterTax} as take-home pay. Using Prince Edward Island’s basic amount of {bpa:PE} on a Nova Scotia return.

## What changed for this year

The income-tax brackets and the flat basic amount are the {year_label} row checked {updated}. The sales-tax change on {date:NS.sales.latest_from} is real, and it belongs on the sales-tax page.

## Neighbouring jurisdictions

Prince Edward Island’s first rate is {rate:PE.income.first}. On the first sample wage, Prince Edward Island’s combined tax is {example:PE.income.42000.totalTax}, against Nova Scotia’s {example:NS.income.42000.totalTax}.

New Brunswick’s first rate is {rate:NB.income.first} and its basic amount is {bpa:NB}. The same wage produces combined tax of {example:NB.income.42000.totalTax}. New Brunswick does not use Nova Scotia’s flat-amount flag.

## Questions

### Does the basic amount shrink as income rises?

Not in this row. It stays {bpa:NS}. The federal amount is a different figure and can phase out.

### Does the HST cut lower my income tax?

No. The HST rate in the current sales row is a sales tax. It changed on {date:NS.sales.latest_from}. It is not a bracket on this page.

### Why is Nova Scotia tax higher than New Brunswick on the same wage?

Because the brackets and the basic amount differ. The first-wage results are {example:NS.income.42000.totalTax} and {example:NB.income.42000.totalTax}. They are not an average.

### Are the affordable-living credits included?

No. Combined tax of {example:NS.income.42000.totalTax} leaves them out.

### Is this a filing position?

No. It is a {year_label} estimate, checked {updated}.

### Is the wage sent to the site?

Only if you later ask for an email or a PDF.

## Official sources

- [Nova Scotia personal income tax](https://www.novascotia.ca/finance/en/home/taxation/tax101/personalincometax.aspx)
- [Canada Revenue Agency individual income tax rates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html)
- [Canada Revenue Agency important dates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/topics/important-dates-individuals.html)
