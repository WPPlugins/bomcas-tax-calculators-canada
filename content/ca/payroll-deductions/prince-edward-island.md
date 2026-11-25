---
excerpt: A Prince Edward Island paycheque for {year_label} withholds federal tax, Island income tax, CPP, and EI. It is not Nova Scotia’s sales-tax change. It is one pay.
---

## Who this is for

This estimate is one Prince Edward Island paycheque. The provincial line is Island income tax, starting from the annual rate of {rate:PE.income.first}. It is not harmonized sales tax and it is not Nova Scotia withholding.

Canada Pension Plan contributions use the employee rate of {rate:CPP.employee} up to pensionable earnings of {money:CPP.ympe}. Employment insurance uses {rate:EI.employee}. A claim code, a taxable benefit, and a bonus are not in the sample. The employer’s remitter type is not looked up.

## How the calculation works

Annual tax is divided by the pays. Federal and Island withholding are separate. CPP and EI are the employee amounts. A shop’s HST rate never enters this calculation.

## Worked cheques

Gross {example:PE.payroll.1200.gross} on the sample biweekly frequency has federal withholding of {example:PE.payroll.1200.federalPeriod} and Prince Edward Island withholding of {example:PE.payroll.1200.provincialPeriod}. CPP is {example:PE.payroll.1200.cpp} and EI is {example:PE.payroll.1200.ei}. The estimated net is {example:PE.payroll.1200.net}.

Gross {example:PE.payroll.2400.gross} has federal withholding of {example:PE.payroll.2400.federalPeriod}, Prince Edward Island withholding of {example:PE.payroll.2400.provincialPeriod}, and a net of {example:PE.payroll.2400.net}. The deductions did not all scale in a straight line, because the annual brackets and the contribution ceilings are applied to the year and then split across the pays.

## Common mistakes

Using Nova Scotia’s flat basic amount of {bpa:NS} in place of {bpa:PE}. Adding HST to the gross-to-net math. Reading {example:PE.payroll.1200.net} as the year’s tax. Subtracting a Quebec net the engine did not produce.

## Recent changes

The {year_label} row was checked {updated}. {date:NS.sales.latest_from} is not an Island payroll date.

## Neighbouring jurisdictions

Nova Scotia’s net on the same gross is {example:NS.payroll.1200.net}. The Island’s is {example:PE.payroll.1200.net}. New Brunswick’s is {example:NB.payroll.1200.net}. Compare income-tax withholding. Do not compare sales-tax rates and call the result a paycheque.

## Questions


### Is the provincial line HST?

No. It is Island income tax of {example:PE.payroll.1200.provincialPeriod}.

### Does Nova Scotia’s rate cut apply?

Not to this cheque. The cut is a sales tax dated {date:NS.sales.latest_from}.

### What is the remittance?

Withheld tax and employer portions. The employee’s net of {example:PE.payroll.1200.net} is not the remittance.

### How does Nova Scotia’s net compare?

It is {example:NS.payroll.1200.net} on the same gross. The Island’s is {example:PE.payroll.1200.net}.

### Does the second gross double the net?

No. That net is {example:PE.payroll.2400.net}.

### Is this a T1?

No. It is one {year_label} pay, checked {updated}.

## Official sources

- [Prince Edward Island personal income tax](https://www.princeedwardisland.ca/en/information/finance/personal-income-tax)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
