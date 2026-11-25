---
excerpt: Take-home pay in Ontario for {year_label} is a yearly salary after Ontario tax, which can include surtax and the health premium. It is not a city tax.
---

## What this estimate is

This is the year’s net on an Ontario salary. Surtax starts when Ontario tax passes {money:ON.surtax.0}. The health premium is an annual amount, up to {money:ON.health.5.cap} on the top band in the file. Toronto does not add a wage tax.

The annual table starts at {rate:ON.income.first}. Employee pension contributions use {rate:CPP.employee} up to {money:CPP.ympe}, except where the province uses the Quebec Pension Plan instead. Employment insurance uses the employee rate. A claim code, a taxable benefit, and a bonus are not in the sample. This is not a pay stub from the payroll page. The payroll page starts from one gross cheque. This page starts from a yearly salary.

## How the calculation works

Income tax for the year is estimated on the salary. Only the enhanced portion of CPP or QPP is deductible. EI premiums are not deductible. The employee pension contribution and EI still come off the cheque. The amount left is the estimated net for the year. Dividing that net by the number of pays is a way to picture it. It is not the remittance and it is not the tax withheld on the payroll sample, because the inputs are different.

A paycheque from the payroll page shows Ontario withholding for one gross. It does not wear a label for the health premium. This salary page is the year. Do not add the premium a second time if the annual tax already includes what the engine calculates.

## Worked salaries

A salary of {example:ON.salary.64000.amount} has estimated tax of {example:ON.salary.64000.tax} and an estimated net of {example:ON.salary.64000.net}. Spread over 26 pays, that net is about {example:ON.salary.64000.perPeriod} a pay. Benefits the employer pays are not in the salary.

A salary of {example:ON.salary.112000.amount} has estimated tax of {example:ON.salary.112000.tax} and a net of {example:ON.salary.112000.net}, about {example:ON.salary.112000.perPeriod} across 26 pays. The tax did not rise in proportion to the salary, because the brackets are not flat.

## What is left out

The Canada child benefit, the GST credit, and provincial benefit programs are not added. A spouse, tuition, and donations are not assumed. Employer health tax, where a province charges it, is the employer’s levy. It is not subtracted again from this employee net.

## Common mistakes

Reading {example:ON.salary.64000.net} as the paycheque on the payroll page. Those pages use different inputs. Quoting the 26-pay slice as the tax. Forgetting that a bonus or a taxable benefit would change the year. Comparing the net with a Quebec payroll net. The Quebec payroll sample does not produce a net.

## Recent changes

Brackets and contribution rates are the {year_label} row checked {updated}.

## Neighbouring jurisdictions

Quebec’s net on this salary screen is {example:QC.salary.64000.net}. That figure exists on the salary engine. It is not the Quebec payroll net, which the payroll sample does not produce. Do not mix the two pages. Manitoba’s salary net is {example:MB.salary.64000.net}. Manitoba phases a basic amount. Ontario uses surtax and a health premium instead.

## Questions


### Is the health premium a separate subtraction I should make?

The premium is part of annual Ontario tax. The net already reflects the tax the engine calculated. Do not subtract {money:ON.health.5.cap} on top of {example:ON.salary.64000.net} unless you have decided the engine left it out. This screen’s tax line is {example:ON.salary.64000.tax}.

### Is Toronto extra?

No. There is no city wage tax in this estimate.

### Why is the Quebec salary net not the Quebec payroll net?

The salary page and the payroll page answer different questions. The payroll sample for Quebec does not print a net. The salary figure is {example:QC.salary.64000.net} on a yearly salary, and it is not a cheque.

### What is the higher salary’s net?

{example:ON.salary.112000.net}, after tax of {example:ON.salary.112000.tax}.

### Does 26 pays equal the remittance?

No. It pictures the employee’s net.

### Is this a T1?

No. It is a {year_label} estimate, checked {updated}.

## Official sources

- [Ontario income tax](https://www.ontario.ca/page/ontario-income-tax)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
