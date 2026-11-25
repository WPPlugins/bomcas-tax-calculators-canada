---
excerpt: A Nunavut paycheque for {year_label} withholds federal tax, Nunavut tax, CPP, and EI. A small territorial line can be the right result. It is not a missing deduction.
---

## Who this is for

This estimate is one Nunavut paycheque. Territorial withholding is Nunavut income tax. The annual table starts at {rate:NU.income.first} and the basic amount is {bpa:NU}. A small territorial line can be the correct result of those two facts. It is not a blank the form forgot to fill.

Canada Pension Plan contributions use the employee rate of {rate:CPP.employee} up to pensionable earnings of {money:CPP.ympe}. Employment insurance uses {rate:EI.employee}. A claim code, a taxable benefit, and a bonus are not in the sample. The employer’s remitter type is not looked up.

## How the calculation works

The year’s tax is divided by the number of pays. Federal withholding is often the larger income-tax line, because the federal brackets are not Nunavut’s brackets. CPP and EI still come off the gross. Northern deductions are not assumed.

## Worked cheques

Gross {example:NU.payroll.1200.gross} on the sample biweekly frequency has federal withholding of {example:NU.payroll.1200.federalPeriod} and Nunavut withholding of {example:NU.payroll.1200.provincialPeriod}. CPP is {example:NU.payroll.1200.cpp} and EI is {example:NU.payroll.1200.ei}. The estimated net is {example:NU.payroll.1200.net}.

Gross {example:NU.payroll.2400.gross} has federal withholding of {example:NU.payroll.2400.federalPeriod}, Nunavut withholding of {example:NU.payroll.2400.provincialPeriod}, and a net of {example:NU.payroll.2400.net}. The deductions did not all scale in a straight line, because the annual brackets and the contribution ceilings are applied to the year and then split across the pays.

## Common mistakes

Retyping the gross because territorial tax looks low. The first cheque’s territorial line is {example:NU.payroll.1200.provincialPeriod}. Using the Northwest Territories’ basic amount of {bpa:NT}. Adding a sales tax the territory does not charge. Comparing the net with a Quebec net that was not calculated.

## Recent changes

Factors are the {year_label} row checked {updated}.

## Neighbouring jurisdictions

The Northwest Territories’ net on the same gross is {example:NT.payroll.1200.net}, against Nunavut’s {example:NU.payroll.1200.net}. The territories both charge GST only. Their payroll results still differ because the income-tax tables differ. Yukon’s net is {example:YT.payroll.1200.net}.

## Questions


### Why is Nunavut tax smaller than federal tax?

The annual rate starts at {rate:NU.income.first} and the basic amount is {bpa:NU}. Federal withholding on the first cheque is {example:NU.payroll.1200.federalPeriod}. Territorial withholding is {example:NU.payroll.1200.provincialPeriod}.

### Is a small line a bug?

No. Read both income-tax lines before you change the gross.

### How does the other territory compare?

The Northwest Territories’ net is {example:NT.payroll.1200.net}. Nunavut’s is {example:NU.payroll.1200.net}.

### What is remitted?

Withheld tax and the employer shares. Not the net of {example:NU.payroll.1200.net}.

### Does the second gross double the net?

No. That net is {example:NU.payroll.2400.net}.

### Is this the return?

No. It is one {year_label} pay, checked {updated}.

## Official sources

- [Nunavut Department of Finance](https://www.gov.nu.ca/finance)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
