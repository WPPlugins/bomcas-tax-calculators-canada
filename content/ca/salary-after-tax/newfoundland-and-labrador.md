---
excerpt: Take-home pay in Newfoundland and Labrador for {year_label} uses the provincial brackets on a yearly salary. Labrador is not a second table. It is not one paycheque.
---

## What this estimate is

This is the year’s net for the province. The top rate is {rate:NL.income.top}. It applies to the slice above the last threshold, not to the whole salary. HST is not subtracted here.

The annual table starts at {rate:NL.income.first}. Employee pension contributions use {rate:CPP.employee} up to {money:CPP.ympe}, except where the province uses the Quebec Pension Plan instead. Employment insurance uses the employee rate. A claim code, a taxable benefit, and a bonus are not in the sample. This is not a pay stub from the payroll page. The payroll page starts from one gross cheque. This page starts from a yearly salary.

## How the calculation works

Income tax for the year is estimated on the salary. Only the enhanced portion of CPP or QPP is deductible. EI premiums are not deductible. The employee pension contribution and EI still come off the cheque. The amount left is the estimated net for the year. Dividing that net by the number of pays is a way to picture it. It is not the remittance and it is not the tax withheld on the payroll sample, because the inputs are different.

Do not multiply {example:NL.salary.112000.amount} by the top rate and call that the tax. The tax the engine returns is {example:NL.salary.112000.tax}.

## Worked salaries

A salary of {example:NL.salary.64000.amount} has estimated tax of {example:NL.salary.64000.tax} and an estimated net of {example:NL.salary.64000.net}. Spread over 26 pays, that net is about {example:NL.salary.64000.perPeriod} a pay. Benefits the employer pays are not in the salary.

A salary of {example:NL.salary.112000.amount} has estimated tax of {example:NL.salary.112000.tax} and a net of {example:NL.salary.112000.net}, about {example:NL.salary.112000.perPeriod} across 26 pays. The tax did not rise in proportion to the salary, because the brackets are not flat.

## What is left out

The Canada child benefit, the GST credit, and provincial benefit programs are not added. A spouse, tuition, and donations are not assumed. Employer health tax, where a province charges it, is the employer’s levy. It is not subtracted again from this employee net.

## Common mistakes

Reading {example:NL.salary.64000.net} as the paycheque on the payroll page. Those pages use different inputs. Quoting the 26-pay slice as the tax. Forgetting that a bonus or a taxable benefit would change the year. Comparing the net with a Quebec payroll net. The Quebec payroll sample does not produce a net.

## Recent changes

Brackets and contribution rates are the {year_label} row checked {updated}.

## Neighbouring jurisdictions

Nova Scotia’s net on the first salary is {example:NS.salary.64000.net}. This province’s is {example:NL.salary.64000.net}. The sales-tax rates also differ after {date:NS.sales.latest_from}, and that sales-tax fact is not the salary gap.

## Questions


### Does the top rate cover the whole salary?

No. Tax on the higher salary is {example:NL.salary.112000.tax}, not the salary times {rate:NL.income.top}.

### Is Labrador separate?

Not in this table.

### Is HST in the net?

No. The net is {example:NL.salary.64000.net}.

### How does Nova Scotia compare?

Its net on the same salary is {example:NS.salary.64000.net}.

### What is the 26-pay picture?

About {example:NL.salary.64000.perPeriod} on the first salary. It is not a remittance.

### Is this a filing?

No. It is a {year_label} estimate, checked {updated}.

## Official sources

- [Newfoundland and Labrador personal income tax](https://www.gov.nl.ca/fin/tax-programs-incentives/personal/income/)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
