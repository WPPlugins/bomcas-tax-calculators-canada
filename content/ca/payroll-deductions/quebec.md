---
excerpt: A Quebec paycheque for {year_label} uses the Quebec Pension Plan, Quebec EI, and parental insurance. This sample does not print a net, and a missing net is not zero.
---

## Who this is for

This page is about a regular Quebec pay. Quebec income tax is withheld under Revenu Québec’s guide, and the federal tables do not replace it. Where this sample cannot produce Quebec income-tax withholding, the line is left unavailable. Unavailable is not zero, and it is not a reason to remit nothing.

The pension contribution is the Quebec Pension Plan, not the Canada Pension Plan. Employment insurance uses the Quebec rate. The Quebec Parental Insurance Plan is a further employee amount. None of these is an Ontario health premium.

## How the calculation works

A regular payment can have both federal withholding and Quebec withholding, and they are not the same number. QPP is calculated on pensionable earnings. QPIP is not a second copy of EI. If the edition loaded for the year does not support the payment, the screen refuses the missing piece instead of borrowing another province’s net.

The Canada Pension Plan line prints {example:QC.payroll.1200.cpp} on the first sample because the contribution is QPP, not because the employee contributed nothing to a pension.

## Worked cheques

On gross {example:QC.payroll.1200.gross}, federal withholding is {example:QC.payroll.1200.federalPeriod}. QPP is {example:QC.payroll.1200.qpp}. Quebec EI is {example:QC.payroll.1200.ei}. QPIP is {example:QC.payroll.1200.qpip}. The net is not produced. Do not write zero in its place, and do not subtract these figures from an Ontario net and call the gap the Quebec cheque.

On gross {example:QC.payroll.2400.gross}, federal withholding is {example:QC.payroll.2400.federalPeriod}, QPP is {example:QC.payroll.2400.qpp}, Quebec EI is {example:QC.payroll.2400.ei}, and QPIP is {example:QC.payroll.2400.qpip}. The net is still not produced. Doubling the gross did not create a net the edition withheld.

## Common mistakes

Filling the blank net with zero and remitting as if no Quebec tax were due. The blank means this sample did not calculate it. Using Ontario’s net of {example:ON.payroll.1200.net} as the Quebec result. Adding CPP on top of QPP. Treating QPIP as a second EI premium beyond the Quebec EI amount already shown.

## Recent changes

The pension, EI, and QPIP factors are the {year_label} row checked {updated}. A TP-1015 edition that is not loaded is not approximated. British Columbia’s July payroll date is not a Quebec date.

## Neighbouring jurisdictions

Ontario’s net on gross {example:ON.payroll.1200.gross} is {example:ON.payroll.1200.net}, after CPP of {example:ON.payroll.1200.cpp} and EI of {example:ON.payroll.1200.ei}. That net is an Ontario cheque. It is not the amount by which Quebec “differs,” because Quebec’s net was not calculated. New Brunswick’s net on the same gross is {example:NB.payroll.1200.net}, and it is a New Brunswick cheque, not a substitute for TP-1015.

## Questions

### Why is there no net?

Because Quebec income-tax withholding was not produced for this sample. A missing net is not zero and it is not Ontario’s {example:ON.payroll.1200.net}.

### Is QPP the same as CPP?

No. QPP on the first gross is {example:QC.payroll.1200.qpp}. The CPP line is {example:QC.payroll.1200.cpp} because the plan is QPP.

### What is QPIP?

The Quebec Parental Insurance Plan. On the first gross it is {example:QC.payroll.1200.qpip}. It is not federal EI and it is not a second copy of the Quebec EI line.

### Can I remit zero until the net appears?

No. Unavailable means this screen will not invent the number. Withhold under the guide that applies.

### Does the larger gross unlock a net?

No. The second sample still has no net. It does show QPP of {example:QC.payroll.2400.qpp}.

### Is this the annual Quebec return?

No. It is a payroll sample for {year_label}, checked {updated}. The annual return is the income-tax page, where the abatement can be seen.

## Official sources

- [Revenu Québec TP-1015.F-V](https://www.revenuquebec.ca/en/online-services/forms-and-publications/current-details/tp-1015-f-v/)
- [Canada Revenue Agency payroll](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/payroll.html)
