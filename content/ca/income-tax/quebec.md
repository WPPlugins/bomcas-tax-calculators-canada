---
excerpt: Quebec income tax for {year_label} is charged by Quebec, and the federal abatement lowers basic federal tax. This page does not use another province’s brackets.
---

## What this tax is

Quebec residents pay Quebec income tax to Revenu Québec and federal income tax to the Canada Revenue Agency. The federal bill is reduced by the Quebec abatement. This estimate is employment income for a single person. It is not a Quebec payroll cheque and it does not compute the Quebec Pension Plan contribution you see on a pay stub.

The Quebec basic personal amount is {bpa:QC}. The credit rate is {rate:QC.income.credit}. A balance is due {deadline:balance_due}. The filing date for a person who carried on a business, {deadline:file_self_employed}, does not move the balance.

## How the calculation works

Only the enhanced portion of QPP is deductible. EI premiums are not. Federal tax is calculated, and the abatement of {rate:QC.abatement} of basic federal tax is applied for a Quebec resident. Quebec tax is calculated on its own brackets, starting at {rate:QC.income.first} and reaching {rate:QC.income.top}. The two taxes stay on separate lines and are then added.

The abatement is not a Quebec credit you claim on top of a full federal bill. It is why the federal line on this page is lower than the federal line for the same wage in Ontario.

## Worked examples

On {example:QC.income.42000.employment}, federal tax is {example:QC.income.42000.federalTax} and Quebec tax is {example:QC.income.42000.provincialTax}. Combined tax is {example:QC.income.42000.totalTax}. The federal amount is lower than Ontario’s federal tax of {example:ON.income.42000.federalTax} on the same wage because of the abatement.

At {example:QC.income.86000.employment}, combined tax is {example:QC.income.86000.totalTax}. Quebec tax of {example:QC.income.86000.provincialTax} is the provincial line. It is not Ontario surtax and it is not a health premium.

At {example:QC.income.155000.employment}, combined tax is {example:QC.income.155000.totalTax}. The top Quebec rate of {rate:QC.income.top} applies to the slice above the last threshold. The abatement still acts on basic federal tax. It does not cut the Quebec tax line in half.

## Credits and programs

The basic personal amount of {bpa:QC} is the credit this form applies. Solidarity tax credit, the work premium, and childcare amounts are not in the total. The Canada child benefit is not in it either. The Quebec abatement is already reflected in the federal line.

## Common mistakes

Comparing Quebec’s federal tax with Ontario’s and calling the engine wrong. The difference on the first wage is the abatement. Using Ontario surtax thresholds on a Quebec return. Reading a payroll page that has no Quebec net and deciding this annual tax is zero. Adding QST to the income-tax total.

## What changed for this year

The Quebec brackets, the basic amount, and the abatement of {rate:QC.abatement} are the {year_label} row checked {updated}. A Revenu Québec change that is not in the file is not invented.

## Neighbouring jurisdictions

Ontario starts at {rate:ON.income.first} and adds surtax once Ontario tax passes {money:ON.surtax.0}. Ontario’s combined tax on the first wage is {example:ON.income.42000.totalTax}. Ontario federal tax on that wage is {example:ON.income.42000.federalTax}, without the Quebec abatement.

New Brunswick’s first rate is {rate:NB.income.first}. New Brunswick has no abatement. Its combined tax on the same wage is {example:NB.income.42000.totalTax}.

## Questions

### Why is federal tax lower than in Ontario?

Because a Quebec resident’s basic federal tax is reduced by the abatement of {rate:QC.abatement}. On the first wage the federal line is {example:QC.income.42000.federalTax}.

### Is the abatement a refund I add back?

No. It has already reduced the federal tax in the estimate. Do not subtract it a second time from {example:QC.income.42000.totalTax}.

### Does this page include the Quebec Pension Plan?

No. Pension contributions on a paycheque are a payroll question. This page is the annual income tax.

### Is QST part of the bill?

No. QST is a sales tax. It is not added to Quebec income tax here.

### Can I file the Quebec return from this figure?

No. It is a {year_label} estimate for employment income, checked {updated}, with credits you did not enter left out.

### Does the wage leave the browser?

Only if you later ask for an email or a PDF.

## Official sources

- [Revenu Québec income tax return](https://www.revenuquebec.ca/en/citizens/income-tax-return/)
- [Canada Revenue Agency individual income tax rates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html)
- [Canada Revenue Agency important dates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/topics/important-dates-individuals.html)
