---
excerpt: Take-home pay in Quebec for {year_label} is a yearly salary after Quebec tax, the federal abatement, and Quebec pension contributions. It is not the blank payroll net.
---

## What this estimate is

This is the year’s net on a Quebec salary. The abatement of {rate:QC.abatement} reduces basic federal tax. The pension plan is the Quebec Pension Plan, not CPP stacked on top. The payroll page’s refusal to print a net is a different screen. A number here does not fill that blank.

The annual table starts at {rate:QC.income.first}. Employee pension contributions use {rate:CPP.employee} up to {money:CPP.ympe}, except where the province uses the Quebec Pension Plan instead. Employment insurance uses the employee rate. A claim code, a taxable benefit, and a bonus are not in the sample. This is not a pay stub from the payroll page. The payroll page starts from one gross cheque. This page starts from a yearly salary.

## How the calculation works

Income tax for the year is estimated on the salary. Only the enhanced portion of CPP or QPP is deductible. EI premiums are not deductible. The employee pension contribution and EI still come off the cheque. The amount left is the estimated net for the year. Dividing that net by the number of pays is a way to picture it. It is not the remittance and it is not the tax withheld on the payroll sample, because the inputs are different.

Do not take Ontario’s salary net and adjust it by the Ontario health premium. Quebec does not charge that premium. Do not add QST to take-home pay.

## Worked salaries

A salary of {example:QC.salary.64000.amount} has estimated tax of {example:QC.salary.64000.tax} and an estimated net of {example:QC.salary.64000.net}. Spread over 26 pays, that net is about {example:QC.salary.64000.perPeriod} a pay. Benefits the employer pays are not in the salary.

A salary of {example:QC.salary.112000.amount} has estimated tax of {example:QC.salary.112000.tax} and a net of {example:QC.salary.112000.net}, about {example:QC.salary.112000.perPeriod} across 26 pays. The tax did not rise in proportion to the salary, because the brackets are not flat.

## What is left out

The Canada child benefit, the GST credit, and provincial benefit programs are not added. A spouse, tuition, and donations are not assumed. Employer health tax, where a province charges it, is the employer’s levy. It is not subtracted again from this employee net.

## Common mistakes

Reading {example:QC.salary.64000.net} as the paycheque on the payroll page. Those pages use different inputs. Quoting the 26-pay slice as the tax. Forgetting that a bonus or a taxable benefit would change the year. Comparing the net with a Quebec payroll net. The Quebec payroll sample does not produce a net.

## Recent changes

Brackets and contribution rates are the {year_label} row checked {updated}.

## Neighbouring jurisdictions

Ontario’s net on the same salary is {example:ON.salary.64000.net}. Quebec’s is {example:QC.salary.64000.net}. Ontario federal tax does not receive the Quebec abatement. New Brunswick’s net is {example:NB.salary.64000.net}, with no abatement and no QPP.

## Questions


### Does this fill in the blank Quebec payroll net?

No. The payroll sample left the net uncalculated. This page’s net of {example:QC.salary.64000.net} is a yearly salary, not that cheque.

### Is the abatement already in the tax?

Yes. Do not subtract {rate:QC.abatement} again from the net.

### Is QST part of take-home pay?

No. QST is a sales tax.

### What is the higher salary’s tax?

{example:QC.salary.112000.tax}, leaving {example:QC.salary.112000.net}.

### Are QPP and CPP both taken?

No. The Quebec plan is the pension contribution. Do not withhold CPP as well on the same earnings.

### Is this a filing?

No. It is a {year_label} estimate, checked {updated}.

## Official sources

- [Revenu Québec income tax return](https://www.revenuquebec.ca/en/citizens/income-tax-return/)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
