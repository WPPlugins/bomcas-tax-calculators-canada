---
excerpt: Ontario income tax for {year_label} adds surtax above two provincial-tax thresholds and a separate health premium. This estimate is employment income, not a paycheque.
---

## What this tax is

Ontario income tax is the provincial tax on taxable income. This page is the annual estimate for employment income of a single person. It is not the tax withheld on one paycheque. Surtax and the Ontario Health Premium belong to the annual bill.

The basic personal amount is {bpa:ON}. The credit rate is {rate:ON.income.credit}. A balance is due {deadline:balance_due}. Filing a business return by {deadline:file_self_employed} does not delay that balance.

## How the calculation works

Only the enhanced portion of CPP comes off the wage. EI premiums are not deductible. Federal tax and Ontario tax are calculated on the taxable income that remains.

Ontario tax starts at {rate:ON.income.first} and the top bracket rate is {rate:ON.income.top}. Surtax is not another bracket on income. It starts when basic Ontario tax passes {money:ON.surtax.0}, at {rate:ON.surtax.0}, and a further surtax starts when basic Ontario tax passes {money:ON.surtax.1}, at {rate:ON.surtax.1}. A reduction of {money:ON.reduction} can apply at lower tax levels. The health premium is charged on income bands in the rate file, separately from the brackets, up to a top amount of {money:ON.health.5.cap}.

## Worked examples

On {example:ON.income.42000.employment}, federal tax is {example:ON.income.42000.federalTax} and Ontario tax is {example:ON.income.42000.provincialTax}. Combined tax is {example:ON.income.42000.totalTax}. That Ontario tax is below {money:ON.surtax.0}, so this modest wage is not the surtax case.

At {example:ON.income.86000.employment}, Ontario tax is {example:ON.income.86000.provincialTax}, still under {money:ON.surtax.0}. Combined tax is {example:ON.income.86000.totalTax}. The health premium, if the income band calls for it, is part of the annual Ontario result rather than a labelled line on a paycheque.

At {example:ON.income.155000.employment}, Ontario tax is {example:ON.income.155000.provincialTax}, which is past both {money:ON.surtax.0} and {money:ON.surtax.1}. Combined tax is {example:ON.income.155000.totalTax}. The top bracket rate of {rate:ON.income.top} still applies only to the slice above the last threshold. Surtax is calculated on the Ontario tax, not by taxing the whole wage again at {rate:ON.surtax.1}.

## Credits and programs

The basic personal amount, the Ontario reduction of {money:ON.reduction}, surtax, and the health premium are the Ontario features in this estimate. The Ontario trillium benefit, the Ontario energy and property tax credit, and the Canada child benefit are not included.

## Common mistakes

Calling Ontario tax a city tax. Toronto does not add a wage tax here. Treating surtax as if it applied to the {example:ON.income.42000.employment} wage, whose Ontario tax is under {money:ON.surtax.0}. Copying one paycheque’s withholding onto this annual result. Using Quebec’s abatement on an Ontario return.

## What changed for this year

Brackets, surtax thresholds, the reduction, and the health-premium bands are the {year_label} row checked {updated}. Ontario does not use British Columbia’s July payroll rate.

## Neighbouring jurisdictions

Quebec’s first rate is {rate:QC.income.first} and its basic amount is {bpa:QC}. Quebec residents receive an abatement of {rate:QC.abatement} of basic federal tax, so the federal tax on {example:QC.income.42000.employment} is {example:QC.income.42000.federalTax}, not the Ontario federal figure of {example:ON.income.42000.federalTax}. Quebec does not charge Ontario surtax.

Manitoba phases its basic amount from {money:MB.bpa.from} to {money:MB.bpa.to}. Ontario does not. On the first sample wage, Manitoba’s combined tax is {example:MB.income.42000.totalTax}, against Ontario’s {example:ON.income.42000.totalTax}.

## Questions

### Is surtax a higher bracket?

No. Brackets run from {rate:ON.income.first} to {rate:ON.income.top}. Surtax is a percentage of Ontario tax above {money:ON.surtax.0} and, further, above {money:ON.surtax.1}.

### Does the health premium show on a paycheque?

The premium is an annual amount. A paycheque shows Ontario withholding, not a labelled premium. The top of the scale in the file is {money:ON.health.5.cap}.

### Why is the first sample under the surtax?

Because Ontario tax of {example:ON.income.42000.provincialTax} is less than {money:ON.surtax.0}. The third sample, at {example:ON.income.155000.provincialTax}, is not.

### Are trillium benefits in the total?

No. Combined tax of {example:ON.income.42000.totalTax} is income tax, including the Ontario features the engine applies, not a benefit payment.

### Is this my T1?

No. It is a {year_label} estimate for employment income, checked {updated}.

### Does the form upload the wage?

No, unless you later request an email or a PDF.

## Official sources

- [Ontario income tax](https://www.ontario.ca/page/ontario-income-tax)
- [Canada Revenue Agency individual income tax rates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html)
- [Canada Revenue Agency important dates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/topics/important-dates-individuals.html)
