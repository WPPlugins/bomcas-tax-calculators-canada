---
excerpt: Old Age Security recovery tax for {year_label} is a share of income above the year’s threshold. The repayment cannot exceed the pension you received.
---

## What the recovery tax is

Old Age Security is a federal pension. If net income is high enough, part of it is repaid through the recovery tax. The rate in the file is {rate:house.oas} of income above the threshold. For {year_label} that threshold is {money:house.oas.threshold}. The repayment stops once it has recovered the OAS you actually received. It does not become a tax on the rest of your income.

## How the calculation works

Net income minus {money:house.oas.threshold}, multiplied by {rate:house.oas}, is the recovery before the cap. The cap is the OAS pension you type. If the formula is larger than the pension, the result is the pension, not the larger number. Income at or under the threshold produces no recovery.

This is not the Guaranteed Income Supplement, and it is not CPP.

## Worked examples

Income at the threshold of {money:house.oas.threshold} does not trigger a repayment. Income just above it repays {rate:house.oas} of the excess, until the OAS received is used up.

A large income with a small OAS pension repays at most the pension. People who apply {rate:house.oas} to their whole income, with no cap, invent a tax the recovery does not charge.

Someone who does not receive OAS has nothing to repay. Do not run the formula as if a pension were paid.

## Common mistakes

Using employment income when the test is net income. Forgetting the cap. Confusing this repayment with a clawback of CPP. Applying a provincial surtax on top of the recovery inside this form. Provincial tax on other income is a separate return.

## Recent changes

The threshold of {money:house.oas.threshold} is the {year_label} amount checked {updated}. It is indexed. A threshold from an earlier year will misstate the excess.

## Questions

### What rate is used?

{rate:house.oas} of net income above {money:house.oas.threshold}, and never more than the OAS received.

### Does every province use a different threshold?

No. The threshold is federal. Provincial tax on your other income is not this recovery.

### Is the Guaranteed Income Supplement included?

No. This estimate is the OAS recovery tax only.

### Can the repayment exceed the pension?

No. That is why the form asks for the OAS you received.

### Is income below the threshold taxed under this rule?

No. There is no recovery until income passes {money:house.oas.threshold}.

## Official sources

- [Old Age Security recovery tax](https://www.canada.ca/en/services/benefits/publicpensions/old-age-security/recovery-tax.html)
