---
excerpt: Take-home pay in Saskatchewan for {year_label} subtracts income tax, CPP, and EI from the yearly salary. The basic amount does not follow Manitoba’s phase-out.
---

## What this estimate is

This is the year’s net on a Saskatchewan salary. The basic personal amount is {bpa:SK} and it does not phase out in the income-tax row. Provincial sales tax is not part of take-home pay.

The annual table starts at {rate:SK.income.first}. Employee pension contributions use {rate:CPP.employee} up to {money:CPP.ympe}, except where the province uses the Quebec Pension Plan instead. Employment insurance uses the employee rate. A claim code, a taxable benefit, and a bonus are not in the sample. This is not a pay stub from the payroll page. The payroll page starts from one gross cheque. This page starts from a yearly salary.

## How the calculation works

Income tax for the year is estimated on the salary. Only the enhanced portion of CPP or QPP is deductible. EI premiums are not deductible. The employee pension contribution and EI still come off the cheque. The amount left is the estimated net for the year. Dividing that net by the number of pays is a way to picture it. It is not the remittance and it is not the tax withheld on the payroll sample, because the inputs are different.

Do not import Manitoba’s phase-out between {money:MB.bpa.from} and {money:MB.bpa.to}. That rule is not Saskatchewan’s.

## Worked salaries

A salary of {example:SK.salary.64000.amount} has estimated tax of {example:SK.salary.64000.tax} and an estimated net of {example:SK.salary.64000.net}. Spread over 26 pays, that net is about {example:SK.salary.64000.perPeriod} a pay. Benefits the employer pays are not in the salary.

A salary of {example:SK.salary.112000.amount} has estimated tax of {example:SK.salary.112000.tax} and a net of {example:SK.salary.112000.net}, about {example:SK.salary.112000.perPeriod} across 26 pays. The tax did not rise in proportion to the salary, because the brackets are not flat.

## What is left out

The Canada child benefit, the GST credit, and provincial benefit programs are not added. A spouse, tuition, and donations are not assumed. Employer health tax, where a province charges it, is the employer’s levy. It is not subtracted again from this employee net.

## Common mistakes

Reading {example:SK.salary.64000.net} as the paycheque on the payroll page. Those pages use different inputs. Quoting the 26-pay slice as the tax. Forgetting that a bonus or a taxable benefit would change the year. Comparing the net with a Quebec payroll net. The Quebec payroll sample does not produce a net.

## Recent changes

Brackets and contribution rates are the {year_label} row checked {updated}.

## Neighbouring jurisdictions

Manitoba’s net on the first salary is {example:MB.salary.64000.net}. Saskatchewan’s is {example:SK.salary.64000.net}. Alberta’s is {example:AB.salary.64000.net}. Alberta’s supplemental credit is not a Saskatchewan credit.

## Questions


### Does the basic amount shrink?

Not on this row. It stays {bpa:SK}.

### Is provincial sales tax in the net?

No. The net of {example:SK.salary.64000.net} is after income tax and employee contributions.

### How does Manitoba compare?

Manitoba’s net on the same salary is {example:MB.salary.64000.net}.

### Why quote 26 pays?

To show the year’s net as a pay. It is about {example:SK.salary.64000.perPeriod} on the first salary. It is not a payroll-page cheque.

### What is the tax on the higher salary?

{example:SK.salary.112000.tax}.

### Is this a filing?

No. It is a {year_label} estimate, checked {updated}.

## Official sources

- [Saskatchewan personal income tax](https://www.saskatchewan.ca/residents/taxes-and-investments/personal-income-tax)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
