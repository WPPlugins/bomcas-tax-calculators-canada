---
excerpt: Take-home pay in Nova Scotia for {year_label} uses the flat basic personal amount and the income-tax brackets. The HST change does not rewrite the salary.
---

## What this estimate is

This is the year’s net on a Nova Scotia salary. The basic amount is the flat {bpa:NS}. It does not follow the federal phase-out. The harmonized rate changed on {date:NS.sales.latest_from}. That is a sales tax.

The annual table starts at {rate:NS.income.first}. Employee pension contributions use {rate:CPP.employee} up to {money:CPP.ympe}, except where the province uses the Quebec Pension Plan instead. Employment insurance uses the employee rate. A claim code, a taxable benefit, and a bonus are not in the sample. This is not a pay stub from the payroll page. The payroll page starts from one gross cheque. This page starts from a yearly salary.

## How the calculation works

Income tax for the year is estimated on the salary. Only the enhanced portion of CPP or QPP is deductible. EI premiums are not deductible. The employee pension contribution and EI still come off the cheque. The amount left is the estimated net for the year. Dividing that net by the number of pays is a way to picture it. It is not the remittance and it is not the tax withheld on the payroll sample, because the inputs are different.

Do not lower the net because HST fell. Do not phase out {bpa:NS} by copying the federal rule.

## Worked salaries

A salary of {example:NS.salary.64000.amount} has estimated tax of {example:NS.salary.64000.tax} and an estimated net of {example:NS.salary.64000.net}. Spread over 26 pays, that net is about {example:NS.salary.64000.perPeriod} a pay. Benefits the employer pays are not in the salary.

A salary of {example:NS.salary.112000.amount} has estimated tax of {example:NS.salary.112000.tax} and a net of {example:NS.salary.112000.net}, about {example:NS.salary.112000.perPeriod} across 26 pays. The tax did not rise in proportion to the salary, because the brackets are not flat.

## What is left out

The Canada child benefit, the GST credit, and provincial benefit programs are not added. A spouse, tuition, and donations are not assumed. Employer health tax, where a province charges it, is the employer’s levy. It is not subtracted again from this employee net.

## Common mistakes

Reading {example:NS.salary.64000.net} as the paycheque on the payroll page. Those pages use different inputs. Quoting the 26-pay slice as the tax. Forgetting that a bonus or a taxable benefit would change the year. Comparing the net with a Quebec payroll net. The Quebec payroll sample does not produce a net.

## Recent changes

Brackets and contribution rates are the {year_label} row checked {updated}.

## Neighbouring jurisdictions

Prince Edward Island’s net on the first salary is {example:PE.salary.64000.net}. Nova Scotia’s is {example:NS.salary.64000.net}. New Brunswick’s is {example:NB.salary.64000.net}.

## Questions


### Does the HST cut raise take-home pay on this page?

No. This page does not subtract sales tax. The net is {example:NS.salary.64000.net}.

### Does the basic amount phase out?

Not this one. It stays {bpa:NS}.

### How does the Island compare?

Its net on the same salary is {example:PE.salary.64000.net}.

### What is the tax on the higher salary?

{example:NS.salary.112000.tax}.

### Is 26 pays the payroll cheque?

No. The payroll page uses a gross cheque. This slice is {example:NS.salary.64000.perPeriod}.

### Is this a filing?

No. It is a {year_label} estimate, checked {updated}.

## Official sources

- [Nova Scotia personal income tax](https://www.novascotia.ca/finance/en/home/taxation/tax101/personalincometax.aspx)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
