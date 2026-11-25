---
excerpt: An Ontario paycheque estimate for {year_label} withholds federal tax, Ontario tax, CPP, and EI. It is not the annual return and it does not use Quebec’s pension plan.
---

## Who this is for

This estimate is one Ontario paycheque for an employee. It is not a T1 and it does not tell you whether the employer is a regular, quarterly, or accelerated remitter. A TD1 claim code, a taxable benefit, and a bonus are outside the sample.

Ontario tax withheld here is provincial income tax, not a city tax. Canada Pension Plan contributions use the employee rate of {rate:CPP.employee} up to pensionable earnings of {money:CPP.ympe}. Employment insurance uses {rate:EI.employee}. Quebec Pension Plan and the Quebec Parental Insurance Plan are not part of an Ontario cheque.

## How the calculation works

The year’s tax is estimated and divided by the number of pays. Federal withholding and Ontario withholding are separate lines. Ontario’s annual tax starts at {rate:ON.income.first}. Surtax and the Ontario Health Premium can change the annual bill once basic Ontario tax or income crosses the thresholds in the rate file. A single paycheque does not itemize those annual adjustments the way the income-tax page does. Do not treat one cheque as the whole surtax story.

## Worked cheques

Gross {example:ON.payroll.1200.gross} on the sample biweekly frequency has federal withholding of {example:ON.payroll.1200.federalPeriod} and Ontario withholding of {example:ON.payroll.1200.provincialPeriod}. CPP is {example:ON.payroll.1200.cpp} and EI is {example:ON.payroll.1200.ei}. The estimated net is {example:ON.payroll.1200.net}.

Gross {example:ON.payroll.2400.gross} has federal withholding of {example:ON.payroll.2400.federalPeriod}, Ontario withholding of {example:ON.payroll.2400.provincialPeriod}, and a net of {example:ON.payroll.2400.net}. The deductions did not all scale in proportion to the gross, because the annual brackets and the contribution ceilings are applied to the year first.

## Common mistakes

Quebec uses the Quebec Pension Plan and parental insurance, so this Ontario sample does not include a Quebec net. Ontario tax on the cheque is provincial income tax. It is not a municipal wage tax. Using {example:ON.payroll.1200.net} as the tax on the annual return ignores that the figure is one pay, after the year’s tax was divided by the number of pays.

## Recent changes

Contribution rates and Ontario brackets are the {year_label} row checked {updated}. Ontario does not use British Columbia’s July withholding switch. A pay date does not by itself change the Ontario annual rate.

## Neighbouring jurisdictions

Manitoba’s net on that gross is {example:MB.payroll.1200.net}. Manitoba does not charge the Ontario Health Premium. Do not move an Ontario net onto a Manitoba payroll account when someone starts work across the boundary.

## Questions

### Is Ontario withholding a city tax?

No. It is Ontario income tax withheld at source. Toronto does not add a wage tax on this cheque.

### Why mention Quebec at all?

Quebec payroll uses a different pension plan and parental insurance. Those amounts are on the Quebec page.

### Does the health premium show on the cheque?

The health premium is an annual Ontario amount. This cheque shows Ontario withholding of {example:ON.payroll.1200.provincialPeriod}, not a labelled premium line. The income-tax page is where the premium scale is discussed.

### What do I remit, and when?

The remittance is not the employee’s net. It includes withheld tax and the employer portions, on the date fixed by the remitter type. This page does not look that type up.

### Will doubling the gross double the net?

The second sample gross is {example:ON.payroll.2400.gross} and the net is {example:ON.payroll.2400.net}. Check those two lines rather than multiplying the first net by two.

## Official sources

- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
- [Ontario income tax](https://www.ontario.ca/page/ontario-income-tax)
