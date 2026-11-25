---
excerpt: A Nova Scotia paycheque estimate for {year_label} withholds federal tax, Nova Scotia tax, CPP, and EI. The HST change is not a change to this cheque.
---

## Who this is for

This estimate is one Nova Scotia paycheque. Provincial withholding is Nova Scotia income tax. The basic amount on the annual table is the flat {bpa:NS}. The harmonized sales tax changed on {date:NS.sales.latest_from}. That change does not rewrite this paycheque.

Canada Pension Plan contributions use the employee rate of {rate:CPP.employee} up to pensionable earnings of {money:CPP.ympe}. Employment insurance uses {rate:EI.employee}. A claim code, a taxable benefit, and a bonus are not in the sample. The employer’s remitter type is not looked up.

## How the calculation works

The year’s income tax is estimated and split across pays. Federal and Nova Scotia withholding are separate. CPP and EI come off the gross. Do not phase out {bpa:NS} on the cheque just because the federal amount phases out.

## Worked cheques

Gross {example:NS.payroll.1200.gross} on the sample biweekly frequency has federal withholding of {example:NS.payroll.1200.federalPeriod} and Nova Scotia withholding of {example:NS.payroll.1200.provincialPeriod}. CPP is {example:NS.payroll.1200.cpp} and EI is {example:NS.payroll.1200.ei}. The estimated net is {example:NS.payroll.1200.net}.

Gross {example:NS.payroll.2400.gross} has federal withholding of {example:NS.payroll.2400.federalPeriod}, Nova Scotia withholding of {example:NS.payroll.2400.provincialPeriod}, and a net of {example:NS.payroll.2400.net}. The deductions did not all scale in a straight line, because the annual brackets and the contribution ceilings are applied to the year and then split across the pays.

## Common mistakes

Putting the sales-tax change into the payroll factors. Using Prince Edward Island’s basic amount. Treating {example:NS.payroll.1200.net} as the annual tax. Inventing a difference against a Quebec net that is not calculated.

## Recent changes

Payroll factors are the {year_label} row checked {updated}. The sales-tax change on {date:NS.sales.latest_from} stays on the sales-tax page.

## Neighbouring jurisdictions

Prince Edward Island’s net on the same gross is {example:PE.payroll.1200.net}, against Nova Scotia’s {example:NS.payroll.1200.net}. New Brunswick’s net is {example:NB.payroll.1200.net}. Those gaps follow the income-tax tables, not the fact that all three charge harmonized sales tax.

## Questions


### Does the HST cut change the cheque?

No. The cut on {date:NS.sales.latest_from} is a sales tax. This cheque withholds income tax of {example:NS.payroll.1200.provincialPeriod}.

### Is the basic amount phased out?

Not the Nova Scotia amount. It is a flat {bpa:NS} in the income-tax row.

### How does the Island compare?

Prince Edward Island’s net on this gross is {example:PE.payroll.1200.net}. Nova Scotia’s is {example:NS.payroll.1200.net}.

### What do I remit?

Not the net. Withheld tax and the employer shares, on the remitter date.

### Will a larger gross double the net?

The larger net is {example:NS.payroll.2400.net}.

### Is this the return?

No. It is one pay for {year_label}, checked {updated}.

## Official sources

- [Nova Scotia personal income tax](https://www.novascotia.ca/finance/en/home/taxation/tax101/personalincometax.aspx)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
