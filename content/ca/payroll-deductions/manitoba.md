---
excerpt: A Manitoba paycheque estimate for {year_label} withholds federal tax, Manitoba tax, CPP, and EI. The basic-amount phase-out is an annual rule, not a line on one cheque.
---

## Who this is for

This estimate is one Manitoba paycheque. Manitoba tax on it is provincial income tax. The basic personal amount phases out between {money:MB.bpa.from} and {money:MB.bpa.to} on the annual return. One cheque does not show that phase-out as its own deduction. A city does not add a wage tax here.

Canada Pension Plan contributions use the employee rate of {rate:CPP.employee} up to pensionable earnings of {money:CPP.ympe}. Employment insurance uses {rate:EI.employee}. A claim code, a taxable benefit, and a bonus are not in the sample. The employer’s remitter type is not looked up.

## How the calculation works

Annual tax is estimated from the year’s gross, then divided by the number of pays. Federal and Manitoba withholding are separate lines. CPP and EI are the employee amounts. The phase-out of the basic amount matters when annual income reaches {money:MB.bpa.from}. The sample cheques are not that annual income by themselves.

## Worked cheques

Gross {example:MB.payroll.1200.gross} on the sample biweekly frequency has federal withholding of {example:MB.payroll.1200.federalPeriod} and Manitoba withholding of {example:MB.payroll.1200.provincialPeriod}. CPP is {example:MB.payroll.1200.cpp} and EI is {example:MB.payroll.1200.ei}. The estimated net is {example:MB.payroll.1200.net}.

Gross {example:MB.payroll.2400.gross} has federal withholding of {example:MB.payroll.2400.federalPeriod}, Manitoba withholding of {example:MB.payroll.2400.provincialPeriod}, and a net of {example:MB.payroll.2400.net}. The deductions did not all scale in a straight line, because the annual brackets and the contribution ceilings are applied to the year and then split across the pays.

## Common mistakes

Quoting {example:MB.payroll.1200.net} as the year’s tax. Assuming the basic amount has already phased out because the cheque looks ordinary. It has not, at the annual incomes the phase-out requires. Inventing a Quebec net to compare against. The Quebec sample leaves the net uncalculated.

## Recent changes

Rates are the {year_label} row checked {updated}. Manitoba does not use British Columbia’s July switch on {deadline:bc_payroll_july}.

## Neighbouring jurisdictions

Saskatchewan’s net on the same gross is {example:SK.payroll.1200.net}. Manitoba’s is {example:MB.payroll.1200.net}. Saskatchewan does not phase its basic amount the way Manitoba does. Ontario’s net is {example:ON.payroll.1200.net}. Ontario’s health premium is an annual Ontario amount, not a Manitoba payroll tax.

## Questions


### Does the phase-out show on this cheque?

The phase-out starts at annual income of {money:MB.bpa.from}. This cheque shows Manitoba withholding of {example:MB.payroll.1200.provincialPeriod}, not a labelled phase-out.

### Is Manitoba tax a city tax?

No. It is provincial income tax.

### How does Saskatchewan compare?

On the same gross, Saskatchewan’s net is {example:SK.payroll.1200.net} and Manitoba’s is {example:MB.payroll.1200.net}.

### What is remitted?

Withheld tax plus the employer portions, on the remitter’s date. Not the employee’s net.

### Does the larger gross double the net?

No. The larger net is {example:MB.payroll.2400.net}.

### Is this a T1?

No. It is one {year_label} pay, checked {updated}.

## Official sources

- [Manitoba personal income taxes](https://www.gov.mb.ca/finance/personal/ptaxes.html)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
