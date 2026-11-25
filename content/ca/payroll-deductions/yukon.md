---
excerpt: A Yukon paycheque for {year_label} withholds federal tax, Yukon tax, CPP, and EI. The basic amount follows the federal amount, phase-out included. It is one pay.
---

## Who this is for

This estimate is one Yukon paycheque. Yukon tax withheld is territorial income tax. The basic personal amount follows the federal amount of {bpa:YT} and phases out from {money:YT.bpa.from}. A single cheque does not show that phase-out as a labelled line. British Columbia PST is not a payroll deduction.

Canada Pension Plan contributions use the employee rate of {rate:CPP.employee} up to pensionable earnings of {money:CPP.ympe}. Employment insurance uses {rate:EI.employee}. A claim code, a taxable benefit, and a bonus are not in the sample. The employer’s remitter type is not looked up.

## How the calculation works

Annual federal tax and Yukon tax are estimated and divided by the pays. CPP and EI come off the gross. The phase-out does not begin until annual income reaches {money:YT.bpa.from}. Do not apply it to a modest cheque by hand.

## Worked cheques

Gross {example:YT.payroll.1200.gross} on the sample biweekly frequency has federal withholding of {example:YT.payroll.1200.federalPeriod} and Yukon withholding of {example:YT.payroll.1200.provincialPeriod}. CPP is {example:YT.payroll.1200.cpp} and EI is {example:YT.payroll.1200.ei}. The estimated net is {example:YT.payroll.1200.net}.

Gross {example:YT.payroll.2400.gross} has federal withholding of {example:YT.payroll.2400.federalPeriod}, Yukon withholding of {example:YT.payroll.2400.provincialPeriod}, and a net of {example:YT.payroll.2400.net}. The deductions did not all scale in a straight line, because the annual brackets and the contribution ceilings are applied to the year and then split across the pays.

## Common mistakes

Copying British Columbia’s July rate of {rate:BC.payroll_july} onto a Yukon cheque. Phasing the basic amount out below {money:YT.bpa.from}. Using {example:YT.payroll.1200.net} as the T1. Subtracting a Quebec net the sample does not print.

## Recent changes

The {year_label} factors were checked {updated}. British Columbia’s July date of {deadline:bc_payroll_july} is not Yukon’s.

## Neighbouring jurisdictions

British Columbia’s net on the same gross is {example:BC.payroll.1200.net}, against Yukon’s {example:YT.payroll.1200.net}. British Columbia has its own basic amount and a July withholding rate. The Northwest Territories’ net is {example:NT.payroll.1200.net}. Their basic amount does not follow the federal phase-out.

## Questions


### Does the federal phase-out hit this cheque?

The phase-out starts at annual income of {money:YT.bpa.from}. This cheque is not that test. Yukon withholding on it is {example:YT.payroll.1200.provincialPeriod}.

### Is the July British Columbia rate used?

No. That rate is {rate:BC.payroll_july} from {deadline:bc_payroll_july}, for British Columbia.

### How does British Columbia’s net compare?

It is {example:BC.payroll.1200.net} on the same gross. Yukon’s is {example:YT.payroll.1200.net}.

### What is remitted?

Withheld tax and employer portions, on the remitter’s date.

### Does the larger gross double the net?

The larger net is {example:YT.payroll.2400.net}.

### Is this a T1?

No. It is one {year_label} pay, checked {updated}.

## Official sources

- [Yukon income tax](https://yukon.ca/en/income-tax)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
