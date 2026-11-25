---
excerpt: RRSP room for {year_label} is a percentage of last year’s earned income, up to the dollar limit, minus the pension adjustment you enter. It is not FHSA room.
---

## What RRSP room is

Registered retirement savings plan room is the amount you can deduct for contributions, built from last year’s earned income. It is not a percentage of this year’s paycheque, and it is not the same as FHSA room or TFSA room.

For {year_label} the dollar cap in the rate file is {money:house.rrsp.limit}. The percentage is {rate:house.rrsp} of earned income. A pension adjustment from an employer plan reduces the room. Other lines on the notice of assessment, including a past-service pension adjustment, are not calculated here. You type the adjustment you already know.

## How the calculation works

Take last year’s earned income, multiply by {rate:house.rrsp}, and stop at {money:house.rrsp.limit}. Subtract the pension adjustment. Add unused room from earlier years if you type it. The result is the deduction limit this form estimates, not the notice itself.

A contribution above that limit is an excess. This page does not compute the excess-contribution tax.

## Worked examples

If earned income is high enough that {rate:house.rrsp} of it exceeds the cap, the new room before a pension adjustment is {money:house.rrsp.limit}. A pension adjustment of any amount you type comes off that cap. The form will not invent the adjustment from a T4.

If earned income is modest, the room is {rate:house.rrsp} of that income and the cap does not bite. Doubling the income does not double the room once you are already at {money:house.rrsp.limit}.

A dividend from your company is not earned income for this purpose. Paying yourself a dividend instead of a salary does not create this room.

## What is left out

The Home Buyers’ Plan repayment is not new room. An FHSA contribution is not an RRSP contribution. Spousal RRSP rules are not modelled. The form does not read a CRA account.

## Common mistakes

Using this year’s salary before the year has ended and treating the result as the official limit. Forgetting the pension adjustment. Adding TFSA room of {money:house.tfsa.limit} to the RRSP figure.

## Recent changes

The dollar limit of {money:house.rrsp.limit} is the {year_label} amount in the rate file, checked {updated}. The percentage {rate:house.rrsp} is the rate stored with it. A later announced limit is not guessed.

## Questions

### Is the cap the same in every province?

Yes. {money:house.rrsp.limit} is a federal dollar limit. Your province changes the tax you save, not this room.

### Does a dividend create room?

No. The percentage applies to earned income. A dividend is not earned income.

### Where does the pension adjustment come from?

From the employer plan, as reported to you. Type it. The form does not derive it.

### Can I contribute the room to an FHSA instead?

An FHSA has its own annual room of {money:house.fhsa.annual} and its own lifetime limit. It is not a relabelling of RRSP room.

### Is unused room included automatically?

Only if you enter it. The form does not store last year’s notice.

## Official sources

- [CRA RRSP dollar limits](https://www.canada.ca/en/revenue-agency/services/tax/registered-plans-administrators/pspa/mp-rrsp-dpsp-tfsa-limits-ympe.html)
