---
excerpt: A Northwest Territories paycheque estimate for {year_label} withholds federal tax, territorial tax, CPP, and EI. Nunavut’s table is not this cheque.
---

## Who this is for

This estimate is one paycheque in the Northwest Territories. Territorial withholding is territorial income tax, not a settlement tax and not GST. The annual table starts at {rate:NT.income.first}. Northern deductions the form does not ask for are not in the cheque.

Canada Pension Plan contributions use the employee rate of {rate:CPP.employee} up to pensionable earnings of {money:CPP.ympe}. Employment insurance uses {rate:EI.employee}. A claim code, a taxable benefit, and a bonus are not in the sample. The employer’s remitter type is not looked up.

## How the calculation works

Annual tax is split across the pays. Federal and territorial withholding are separate. CPP and EI are employee amounts. The absence of a territorial sales tax does not remove income-tax withholding.

## Worked cheques

Gross {example:NT.payroll.1200.gross} on the sample biweekly frequency has federal withholding of {example:NT.payroll.1200.federalPeriod} and Northwest Territories withholding of {example:NT.payroll.1200.provincialPeriod}. CPP is {example:NT.payroll.1200.cpp} and EI is {example:NT.payroll.1200.ei}. The estimated net is {example:NT.payroll.1200.net}.

Gross {example:NT.payroll.2400.gross} has federal withholding of {example:NT.payroll.2400.federalPeriod}, Northwest Territories withholding of {example:NT.payroll.2400.provincialPeriod}, and a net of {example:NT.payroll.2400.net}. The deductions did not all scale in a straight line, because the annual brackets and the contribution ceilings are applied to the year and then split across the pays.

## Common mistakes

Using Nunavut’s basic amount of {bpa:NU}. Dropping territorial tax because GST is the only sales tax. Treating {example:NT.payroll.1200.net} as the year’s tax. Inventing a Quebec difference from a missing net.

## Recent changes

The {year_label} factors were checked {updated}. There is no July territorial switch like British Columbia’s {deadline:bc_payroll_july}.

## Neighbouring jurisdictions

Nunavut’s net on the same gross is {example:NU.payroll.1200.net}. The Northwest Territories’ net is {example:NT.payroll.1200.net}. Nunavut’s first income-tax rate is {rate:NU.income.first}, with a basic amount of {bpa:NU}. Yukon’s net on that gross is {example:YT.payroll.1200.net}. Yukon’s basic amount follows the federal amount.

## Questions


### Is there territorial tax on the cheque?

Yes. It is {example:NT.payroll.1200.provincialPeriod} on the first sample. GST-only sales tax does not cancel it.

### Is this Nunavut’s table?

No. Nunavut’s net on the same gross is {example:NU.payroll.1200.net}.

### Are northern deductions included?

No. The form does not ask for them.

### What is remitted?

Withheld tax and employer portions. The net is the employee’s, not the remittance.

### Does the larger gross double the net?

The larger net is {example:NT.payroll.2400.net}.

### Is this a T1?

No. It is one {year_label} pay, checked {updated}.

## Official sources

- [Northwest Territories income tax](https://www.fin.gov.nt.ca/en/services/income-tax)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
