---
excerpt: A Newfoundland and Labrador paycheque estimate for {year_label} withholds federal tax, provincial tax, CPP, and EI. Labrador is not a separate payroll table.
---

## Who this is for

This estimate is one paycheque for the province, including Labrador. The provincial line is Newfoundland and Labrador income tax. The annual table starts at {rate:NL.income.first} and has a high top rate that a single modest cheque does not apply to the whole year’s income. Harmonized sales tax is not withheld here.

Canada Pension Plan contributions use the employee rate of {rate:CPP.employee} up to pensionable earnings of {money:CPP.ympe}. Employment insurance uses {rate:EI.employee}. A claim code, a taxable benefit, and a bonus are not in the sample. The employer’s remitter type is not looked up.

## How the calculation works

The year’s tax is estimated, then divided by the number of pays. Federal and provincial withholding are separate. CPP and EI come off the gross. The top annual rate of {rate:NL.income.top} is not a flat rate on this cheque.

## Worked cheques

Gross {example:NL.payroll.1200.gross} on the sample biweekly frequency has federal withholding of {example:NL.payroll.1200.federalPeriod} and Newfoundland and Labrador withholding of {example:NL.payroll.1200.provincialPeriod}. CPP is {example:NL.payroll.1200.cpp} and EI is {example:NL.payroll.1200.ei}. The estimated net is {example:NL.payroll.1200.net}.

Gross {example:NL.payroll.2400.gross} has federal withholding of {example:NL.payroll.2400.federalPeriod}, Newfoundland and Labrador withholding of {example:NL.payroll.2400.provincialPeriod}, and a net of {example:NL.payroll.2400.net}. The deductions did not all scale in a straight line, because the annual brackets and the contribution ceilings are applied to the year and then split across the pays.

## Common mistakes

Multiplying the cheque by the top income-tax rate. Splitting Labrador onto a different payroll table this file does not have. Using the net of {example:NL.payroll.1200.net} as the T1. Comparing it with an uncalculated Quebec net.

## Recent changes

Factors are the {year_label} row checked {updated}. Nova Scotia’s sales-tax date does not change this cheque.

## Neighbouring jurisdictions

Nova Scotia’s net on the same gross is {example:NS.payroll.1200.net}, against {example:NL.payroll.1200.net} here. Both provinces charge harmonized sales tax at different current rates. That sales-tax gap is not what these two nets are measuring. They measure income-tax withholding, CPP, and EI.

## Questions


### Is Labrador withheld differently?

Not in this table. The provincial amount on the first cheque is {example:NL.payroll.1200.provincialPeriod}.

### Does the top rate apply to the cheque?

The annual top rate is {rate:NL.income.top}, on the top slice of annual income. This cheque is the year’s tax divided by the pays, not that rate times the gross.

### Is HST on the cheque?

No. HST is a sales tax.

### How does Nova Scotia compare?

Nova Scotia’s net is {example:NS.payroll.1200.net}. This province’s is {example:NL.payroll.1200.net}.

### What is remitted?

Withheld tax and employer shares, on the remitter’s date. Not {example:NL.payroll.1200.net}.

### Is this the annual return?

No. It is one {year_label} pay, checked {updated}.

## Official sources

- [Newfoundland and Labrador personal income tax](https://www.gov.nl.ca/fin/tax-programs-incentives/personal/income/)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
