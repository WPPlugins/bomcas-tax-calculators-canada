---
excerpt: Take-home pay in British Columbia for {year_label} subtracts income tax, CPP, and EI from an annual salary. It is not a pay stub and it ignores bonuses.
---

## Who this is for

This page turns an annual salary into an estimate of what remains after {year_label} income tax and the employee Canada Pension Plan and employment-insurance amounts. It is for someone paid in British Columbia, without a taxable benefit, union dues, or a bonus. It does not know the claim code on a TD1.

## How the calculation works

The salary is taxed under the federal brackets and the British Columbia brackets. British Columbia starts at {rate:BC.income.first} and the top rate is {rate:BC.income.top}. The basic personal amount is {bpa:BC}. The tax reduction for modest incomes is applied the same way as on the income-tax page. Employee pension contributions use {rate:CPP.employee} up to pensionable earnings of {money:CPP.ympe}. Only the enhanced portion of that contribution is deductible. EI premiums, at {rate:EI.employee}, are not deductible. Both still come off the cheque.

The net can be spread over 26 pays so you can compare an offer with a paycheque. That division is not the withholding on pay number one if the employer uses a different claim code. A bonus is taxed under a different payroll method and is not in this salary.

## Worked salaries

A salary of {example:BC.salary.64000.amount} has estimated tax of {example:BC.salary.64000.tax}. The amount left for the year is {example:BC.salary.64000.net}, about {example:BC.salary.64000.perPeriod} if that net is spread over 26 pays.

A salary of {example:BC.salary.112000.amount} has estimated tax of {example:BC.salary.112000.tax} and {example:BC.salary.112000.net} left, or about {example:BC.salary.112000.perPeriod} over 26 pays. The extra salary does not keep the same share, because more of it is taxed in higher bands and the tax reduction does less work.

## Credits and what is left out

The basic personal amount and the British Columbia tax reduction are in the estimate. The Canada child benefit, the GST credit, and the BC renter’s credit are not subtracted from the salary and not added to the net. A spouse amount is not included.

## Common mistakes

Reading {example:BC.salary.64000.net} as the net for every British Columbia salary ignores the second example, which leaves {example:BC.salary.112000.net}. Dividing the annual tax by 26 and calling it the tax on a bonus mixes two payroll methods. Using the July withholding rate of {rate:BC.payroll_july} as the tax on the salary confuses a paycheque table with this annual estimate.

## Recent changes

The salary uses the {year_label} brackets and contribution rates checked {updated}. The July payroll rate remains a withholding rule for pay dates on or after {deadline:bc_payroll_july}, not a change to the salary bands.

## Neighbouring jurisdictions

Alberta’s net on {example:AB.salary.64000.amount} is {example:AB.salary.64000.net}, against {example:BC.salary.64000.net} in British Columbia. Alberta’s first rate is {rate:AB.income.first} and it has no provincial sales tax, which does not change this salary comparison except to remind you not to mix a sales-tax saving into take-home pay.

Yukon’s net on that salary is {example:YT.salary.64000.net}. Yukon’s basic amount follows the federal amount of {bpa:YT}, not {bpa:BC}.

## Questions

### Is the 26-pay figure my actual cheque?

Only if you are paid 26 times and nothing else is deducted. {example:BC.salary.64000.perPeriod} is the annual net divided by 26, not a payroll register.

### Does the tax reduction apply to both salaries?

It depends on income. The reduction is for modest incomes and phases out. The higher salary, {example:BC.salary.112000.amount}, does not get the same reduction as the lower one.

### Are CPP and EI in the tax line?

The tax line is income tax. CPP and EI are employee contributions taken off before the net of {example:BC.salary.64000.net}. They are not provincial income tax.

### What about a bonus?

A bonus is not in {example:BC.salary.64000.amount}. Payroll withholds a bonus differently. Add it only in a payroll calculation that is built for a bonus.

### Can I compare this with Alberta by subtracting PST?

No. PST is not part of take-home pay on this page. Compare the nets: {example:BC.salary.64000.net} in British Columbia and {example:AB.salary.64000.net} in Alberta.

## Official sources

- [Canada Revenue Agency individual income tax rates](https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/canadian-income-tax-rates-individuals-current-previous-years.html)
- [British Columbia income tax rates](https://www2.gov.bc.ca/gov/content/taxes/income-taxes/personal/tax-rates)
