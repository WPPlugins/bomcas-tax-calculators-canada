---
excerpt: An Alberta paycheque estimate for {year_label} withholds federal tax, Alberta tax, CPP, and EI. It is one pay, not the annual return, and it is not a sales-tax receipt.
---

## Who this is for

This estimate is one Alberta paycheque for an employee. Alberta tax withheld here is provincial income tax. It is not a municipal wage tax, and it is not the annual return. The annual table starts at {rate:AB.income.first}. The supplemental credit on the income-tax page is not itemized as its own line on this cheque.

Canada Pension Plan contributions use the employee rate of {rate:CPP.employee} up to pensionable earnings of {money:CPP.ympe}. Employment insurance uses {rate:EI.employee}. A claim code, a taxable benefit, and a bonus are not in the sample. The employer’s remitter type is not looked up.

## How the calculation works

The year’s tax is estimated and divided by the number of pays. Federal withholding and Alberta withholding are separate. CPP and EI come off the gross as employee amounts. There is no Quebec parental insurance on an Alberta cheque, so that line stays at zero when the engine prints zero.

## Worked cheques

Gross {example:AB.payroll.1200.gross} on the sample biweekly frequency has federal withholding of {example:AB.payroll.1200.federalPeriod} and Alberta withholding of {example:AB.payroll.1200.provincialPeriod}. CPP is {example:AB.payroll.1200.cpp} and EI is {example:AB.payroll.1200.ei}. The estimated net is {example:AB.payroll.1200.net}.

Gross {example:AB.payroll.2400.gross} has federal withholding of {example:AB.payroll.2400.federalPeriod}, Alberta withholding of {example:AB.payroll.2400.provincialPeriod}, and a net of {example:AB.payroll.2400.net}. The deductions did not all scale in a straight line, because the annual brackets and the contribution ceilings are applied to the year and then split across the pays.

## Common mistakes

Using the net of {example:AB.payroll.1200.net} as the tax on the T1. That net is one pay. Calling Alberta withholding a city tax. Comparing it with a Quebec net. The Quebec sample does not produce a net, so there is nothing to subtract from {example:AB.payroll.1200.net}.

## Recent changes

Contribution rates and Alberta brackets are the {year_label} row checked {updated}. Alberta does not switch rates on British Columbia’s July payroll date of {deadline:bc_payroll_july}.

## Neighbouring jurisdictions

British Columbia’s net on the same gross is {example:BC.payroll.1200.net}, against Alberta’s {example:AB.payroll.1200.net}. From {deadline:bc_payroll_july}, British Columbia withholding can use {rate:BC.payroll_july}. Alberta does not. Saskatchewan’s net on that gross is {example:SK.payroll.1200.net}. Saskatchewan’s income-tax table is not Alberta’s supplemental credit.

## Questions


### Is Alberta withholding a city tax?

No. It is Alberta income tax withheld at source. The amount on the first cheque is {example:AB.payroll.1200.provincialPeriod}.

### Why is Quebec not a dollar difference?

The Quebec sample does not print a net. Do not treat a missing net as zero and do not subtract it from {example:AB.payroll.1200.net}.

### What do I remit?

The remittance includes withheld tax and the employer portions, on the date fixed by the remitter type. The employee’s net is not the remittance.

### Does July change Alberta?

No. The July rate of {rate:BC.payroll_july} is British Columbia’s. Alberta’s annual table still starts at {rate:AB.income.first}.

### Will the second gross double the net?

The second net is {example:AB.payroll.2400.net}. Read it. Do not multiply the first net by two.

### Is this the annual return?

No. It is one pay for {year_label}, checked {updated}.

## Official sources

- [Alberta personal income tax](https://www.alberta.ca/personal-income-tax)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
