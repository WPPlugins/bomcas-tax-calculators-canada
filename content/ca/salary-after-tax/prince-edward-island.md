---
excerpt: Take-home pay on Prince Edward Island for {year_label} is the yearly salary after Island income tax, CPP, and EI. It is not Nova Scotia’s net and it is not HST.
---

## What this estimate is

This is Island take-home pay. The annual rate starts at {rate:PE.income.first} and the basic amount is {bpa:PE}. Harmonized sales tax is not deducted from the salary.

The annual table starts at {rate:PE.income.first}. Employee pension contributions use {rate:CPP.employee} up to {money:CPP.ympe}, except where the province uses the Quebec Pension Plan instead. Employment insurance uses the employee rate. A claim code, a taxable benefit, and a bonus are not in the sample. This is not a pay stub from the payroll page. The payroll page starts from one gross cheque. This page starts from a yearly salary.

## How the calculation works

Income tax for the year is estimated on the salary. Only the enhanced portion of CPP or QPP is deductible. EI premiums are not deductible. The employee pension contribution and EI still come off the cheque. The amount left is the estimated net for the year. Dividing that net by the number of pays is a way to picture it. It is not the remittance and it is not the tax withheld on the payroll sample, because the inputs are different.

A reader who uses Nova Scotia’s basic amount of {bpa:NS} will not match this net.

## Worked salaries

A salary of {example:PE.salary.64000.amount} has estimated tax of {example:PE.salary.64000.tax} and an estimated net of {example:PE.salary.64000.net}. Spread over 26 pays, that net is about {example:PE.salary.64000.perPeriod} a pay. Benefits the employer pays are not in the salary.

A salary of {example:PE.salary.112000.amount} has estimated tax of {example:PE.salary.112000.tax} and a net of {example:PE.salary.112000.net}, about {example:PE.salary.112000.perPeriod} across 26 pays. The tax did not rise in proportion to the salary, because the brackets are not flat.

## What is left out

The Canada child benefit, the GST credit, and provincial benefit programs are not added. A spouse, tuition, and donations are not assumed. Employer health tax, where a province charges it, is the employer’s levy. It is not subtracted again from this employee net.

## Common mistakes

Reading {example:PE.salary.64000.net} as the paycheque on the payroll page. Those pages use different inputs. Quoting the 26-pay slice as the tax. Forgetting that a bonus or a taxable benefit would change the year. Comparing the net with a Quebec payroll net. The Quebec payroll sample does not produce a net.

## Recent changes

Brackets and contribution rates are the {year_label} row checked {updated}.

## Neighbouring jurisdictions

Nova Scotia’s net on the first salary is {example:NS.salary.64000.net}, against the Island’s {example:PE.salary.64000.net}. New Brunswick’s net is {example:NB.salary.64000.net}.

## Questions


### Is this Nova Scotia’s calculator?

No. The basic amounts are {bpa:PE} and {bpa:NS}. The first salary’s net here is {example:PE.salary.64000.net}.

### Is HST removed from pay?

No.

### What is the higher net?

{example:PE.salary.112000.net}, after tax of {example:PE.salary.112000.tax}.

### What does 26 pays mean?

The year’s net divided by 26, about {example:PE.salary.64000.perPeriod} on the first salary. It is not the remittance.

### Are benefits included?

Only if they were in the salary you treat as the input. The sample does not add a benefit.

### Is this a filing?

No. It is a {year_label} estimate, checked {updated}.

## Official sources

- [Prince Edward Island personal income tax](https://www.princeedwardisland.ca/en/information/finance/personal-income-tax)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
