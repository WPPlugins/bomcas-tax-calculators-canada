---
excerpt: A British Columbia paycheque estimate for {year_label} takes federal tax, provincial tax, CPP, and EI off the gross. July withholding is not the rate on the T1.
---

## Who this is for

This page estimates one British Columbia paycheque for an employee. It is not the annual return and it is not a remittance calendar. The employer’s remitter type, regular, quarterly, or accelerated, is not looked up. A claim code, a taxable benefit, and a bonus are not in the sample.

Pension contributions use the Canada Pension Plan employee rate of {rate:CPP.employee}, up to yearly maximum pensionable earnings of {money:CPP.ympe}. Employment insurance uses the employee rate of {rate:EI.employee}. Quebec’s pension plan and parental insurance are not used on a British Columbia cheque.

## How the calculation works

The annual tax is estimated from the year’s gross and then divided by the number of pays. Federal income tax and British Columbia income tax are withheld separately. CPP and EI come off the gross as employee amounts.

From {deadline:bc_payroll_july}, British Columbia withholding on the provincial portion uses {rate:BC.payroll_july} and a basic reduction of {money:BC.payroll_july_reduction}. The annual return still starts at {rate:BC.income.first}. If you are reconciling a T4 to a T1, use the annual bands, not the July withholding rate.

## Worked cheques

The first cheque is gross {example:BC.payroll.1200.gross}, paid on the sample’s biweekly frequency. Federal withholding is {example:BC.payroll.1200.federalPeriod} and British Columbia withholding is {example:BC.payroll.1200.provincialPeriod}. CPP is {example:BC.payroll.1200.cpp} and EI is {example:BC.payroll.1200.ei}. Parental insurance is {example:BC.payroll.1200.qpip}, which stays at zero outside Quebec. The estimated net is {example:BC.payroll.1200.net}.

The second cheque is gross {example:BC.payroll.2400.gross}. Federal withholding is {example:BC.payroll.2400.federalPeriod}, British Columbia withholding is {example:BC.payroll.2400.provincialPeriod}, and the net is {example:BC.payroll.2400.net}. Doubling the gross did not double every deduction in a straight line, because the annual tax and the pension ceiling are applied to the year and then split across the pays.

## Common mistakes

Calling British Columbia withholding “local” makes it sound like a city tax. It is provincial income tax withheld at source. Quoting the net of {example:BC.payroll.1200.net} as the annual tax understates the year: that net is one pay. Copying {rate:BC.payroll_july} onto the return mixes the July payroll table with the T1.

## Recent changes

The pension and employment-insurance factors, and the July withholding rate, are the {year_label} figures checked {updated}. A pay dated before {deadline:bc_payroll_july} does not use the July rate.

## Neighbouring jurisdictions

Alberta’s net on a gross of {example:AB.payroll.1200.gross} is {example:AB.payroll.1200.net}, compared with {example:BC.payroll.1200.net} in British Columbia. Alberta does not have the July withholding switch.

Yukon’s net on that gross is {example:YT.payroll.1200.net}. Yukon does not use the British Columbia reduction. Do not reuse a British Columbia net for a Yukon payroll account.

## Questions

### Is the net the amount I remit to the CRA?

No. {example:BC.payroll.1200.net} is the employee’s estimated take-home on that cheque. The remittance includes the withheld tax and both the employee and employer contributions, on the date your remitter type requires.

### Why is QPIP zero?

Quebec parental insurance is not part of a British Columbia cheque. The sample shows {example:BC.payroll.1200.qpip}.

### Does a higher gross double the tax?

Not exactly. The second cheque, gross {example:BC.payroll.2400.gross}, nets {example:BC.payroll.2400.net}. Compare that with two times the first net before you assume a straight line.

### Which rate goes on the T1?

The annual British Columbia bands, starting at {rate:BC.income.first}. The July rate of {rate:BC.payroll_july} is for withholding after {deadline:bc_payroll_july}.

### Can the form see my TD1 claim code?

No. The sample does not know extra credits on a TD1. Run it as an estimate, then adjust for the claim code your payroll actually uses.

## Official sources

- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
- [British Columbia income tax rates](https://www2.gov.bc.ca/gov/content/taxes/income-taxes/personal/tax-rates)
