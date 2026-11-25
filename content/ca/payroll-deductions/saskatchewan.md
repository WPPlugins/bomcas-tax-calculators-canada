---
excerpt: A Saskatchewan paycheque estimate for {year_label} withholds federal tax, Saskatchewan tax, CPP, and EI. The basic amount does not phase out the way Manitoba’s does.
---

## Who this is for

This estimate is one Saskatchewan paycheque for an employee. Saskatchewan tax withheld is provincial income tax. The basic personal amount of {bpa:SK} does not phase out in the income-tax row. That annual fact is not a separate box on the cheque.

Canada Pension Plan contributions use the employee rate of {rate:CPP.employee} up to pensionable earnings of {money:CPP.ympe}. Employment insurance uses {rate:EI.employee}. A claim code, a taxable benefit, and a bonus are not in the sample. The employer’s remitter type is not looked up.

## How the calculation works

The year’s tax is split across the pays. Federal withholding and Saskatchewan withholding are separate. CPP and EI come off the gross. There is no territorial or Quebec plan on this cheque.

## Worked cheques

Gross {example:SK.payroll.1200.gross} on the sample biweekly frequency has federal withholding of {example:SK.payroll.1200.federalPeriod} and Saskatchewan withholding of {example:SK.payroll.1200.provincialPeriod}. CPP is {example:SK.payroll.1200.cpp} and EI is {example:SK.payroll.1200.ei}. The estimated net is {example:SK.payroll.1200.net}.

Gross {example:SK.payroll.2400.gross} has federal withholding of {example:SK.payroll.2400.federalPeriod}, Saskatchewan withholding of {example:SK.payroll.2400.provincialPeriod}, and a net of {example:SK.payroll.2400.net}. The deductions did not all scale in a straight line, because the annual brackets and the contribution ceilings are applied to the year and then split across the pays.

## Common mistakes

Borrowing Manitoba’s phase-out and reducing the basic amount on a Saskatchewan payroll. Using {example:SK.payroll.1200.net} as the annual tax. Building a Quebec comparison from a net the Quebec sample does not print.

## Recent changes

The {year_label} factors were checked {updated}. British Columbia’s July date is not a Saskatchewan date.

## Neighbouring jurisdictions

Manitoba’s net on the same gross is {example:MB.payroll.1200.net}, against Saskatchewan’s {example:SK.payroll.1200.net}. Alberta’s net is {example:AB.payroll.1200.net}. Alberta’s supplemental credit is not part of Saskatchewan withholding.

## Questions


### Does the basic amount shrink?

Not in the Saskatchewan income-tax row. It stays {bpa:SK}. Manitoba’s phase-out is Manitoba’s.

### What is the provincial line?

Saskatchewan income tax withheld. On the first cheque it is {example:SK.payroll.1200.provincialPeriod}.

### Is the net the remittance?

No. The remittance includes employer portions and follows the remitter type.

### How does Alberta compare?

Alberta’s net on the same gross is {example:AB.payroll.1200.net}. Saskatchewan’s is {example:SK.payroll.1200.net}.

### Does doubling the gross double the net?

The second net is {example:SK.payroll.2400.net}. Check it rather than multiplying.

### Is this the return?

No. It is one pay for {year_label}, checked {updated}.

## Official sources

- [Saskatchewan personal income tax](https://www.saskatchewan.ca/residents/taxes-and-investments/personal-income-tax)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
