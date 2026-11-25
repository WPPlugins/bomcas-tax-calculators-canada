---
excerpt: Take-home pay in Manitoba for {year_label} is a yearly salary after income tax, CPP, and EI. The basic-amount phase-out is an annual rule. It is not one paycheque.
---

## What this estimate is

This is Manitoba take-home pay for the year. The basic personal amount phases out from {money:MB.bpa.from} to {money:MB.bpa.to}. Retail sales tax is not subtracted from salary.

The annual table starts at {rate:MB.income.first}. Employee pension contributions use {rate:CPP.employee} up to {money:CPP.ympe}, except where the province uses the Quebec Pension Plan instead. Employment insurance uses the employee rate. A claim code, a taxable benefit, and a bonus are not in the sample. This is not a pay stub from the payroll page. The payroll page starts from one gross cheque. This page starts from a yearly salary.

## How the calculation works

Income tax for the year is estimated on the salary. Only the enhanced portion of CPP or QPP is deductible. EI premiums are not deductible. The employee pension contribution and EI still come off the cheque. The amount left is the estimated net for the year. Dividing that net by the number of pays is a way to picture it. It is not the remittance and it is not the tax withheld on the payroll sample, because the inputs are different.

A salary under the phase-out start keeps the full basic amount in the income-tax calculation. Do not reduce the net by hand for a phase-out the income has not reached.

## Worked salaries

A salary of {example:MB.salary.64000.amount} has estimated tax of {example:MB.salary.64000.tax} and an estimated net of {example:MB.salary.64000.net}. Spread over 26 pays, that net is about {example:MB.salary.64000.perPeriod} a pay. Benefits the employer pays are not in the salary.

A salary of {example:MB.salary.112000.amount} has estimated tax of {example:MB.salary.112000.tax} and a net of {example:MB.salary.112000.net}, about {example:MB.salary.112000.perPeriod} across 26 pays. The tax did not rise in proportion to the salary, because the brackets are not flat.

## What is left out

The Canada child benefit, the GST credit, and provincial benefit programs are not added. A spouse, tuition, and donations are not assumed. Employer health tax, where a province charges it, is the employer’s levy. It is not subtracted again from this employee net.

## Common mistakes

Reading {example:MB.salary.64000.net} as the paycheque on the payroll page. Those pages use different inputs. Quoting the 26-pay slice as the tax. Forgetting that a bonus or a taxable benefit would change the year. Comparing the net with a Quebec payroll net. The Quebec payroll sample does not produce a net.

## Recent changes

Brackets and contribution rates are the {year_label} row checked {updated}.

## Neighbouring jurisdictions

Saskatchewan’s net on {example:SK.salary.64000.amount} is {example:SK.salary.64000.net}. Manitoba’s is {example:MB.salary.64000.net}. Saskatchewan does not use Manitoba’s phase-out. Ontario’s net on that salary is {example:ON.salary.64000.net}. Ontario’s health premium is an Ontario annual amount, not a Manitoba deduction you copy across.

## Questions


### Does the phase-out apply to the first salary?

Only if that salary reaches {money:MB.bpa.from}. The first sample is {example:MB.salary.64000.amount}. Read the net of {example:MB.salary.64000.net} rather than phasing the basic amount out yourself.

### Is retail sales tax taken off the salary?

No. It is a sales tax.

### How does Saskatchewan compare?

Its net on the same salary is {example:SK.salary.64000.net}.

### Is the 26-pay amount the remittance?

No. It is the employee’s net divided by 26. The employer remits on a different schedule.

### What moves the higher salary’s tax?

The brackets. Tax on it is {example:MB.salary.112000.tax}.

### Is this a T1?

No. It is a {year_label} estimate, checked {updated}.

## Official sources

- [Manitoba personal income taxes](https://www.gov.mb.ca/finance/personal/ptaxes.html)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
