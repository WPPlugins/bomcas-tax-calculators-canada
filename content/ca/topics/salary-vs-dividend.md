---
excerpt: Salary versus dividend for {year_label} compares owner pay using the dividend rates in the file. A year that is not in that file is not estimated, and Quebec is off.
---

## What the comparison is

A private-company owner can take profit as salary, as dividends, or as a mix. Salary is employment income. It creates RRSP room and is subject to Canada Pension Plan. A dividend is not employment income. It is taxed with the gross-up and the dividend tax credit, and it does not create pensionable earnings.

This comparison uses the dividend rates labelled in the rate file for the year that file names. If you need a year that is not in the file, the estimate is not produced. Quebec is turned off, because the Quebec dividend credit and abatement are a different calculation and this screen does not pretend to run them.

## How the calculation works

Start with corporate profit before owner pay. Salary reduces that profit and is taxed in the owner’s hands as employment income, after the personal brackets for the province you select. A non-eligible dividend is paid from profit that has already been taxed in the company at the small-business rate, then grossed up and credited on the personal return. An eligible dividend uses the general-rate pool and a different gross-up and credit.

The screen does not file a T2 or a T1. It does not know a passive-income grind on the small-business limit, a tax on split income, or a capital dividend. Those are outside the comparison.

## Worked examples

Take a profit that is paid entirely as salary to an owner in a province the screen supports. The personal tax is the employment-income tax for that province in {year_label}, and the company has deducted the salary. CPP applies to the salary up to pensionable earnings of {money:CPP.ympe} at the employee rate of {rate:CPP.employee}, and the company pays the employer half as well.

Take the same profit paid as a non-eligible dividend. There is no CPP on the dividend. The personal tax uses the non-eligible gross-up and credit in the file, not the employment brackets alone. The company does not deduct the dividend the way it deducts salary, because the profit was taxed in the company first.

A mix is not the average of those two answers. Salary uses up the basic personal amount and the low brackets first. Dividends stacked on top of a large salary are taxed at the marginal dividend rate, not at the average rate of the all-salary case.

## Credits and programs

The basic personal amount still applies to the person’s taxable income. The dividend tax credit is the relief on the dividend, not a second basic personal amount. RRSP room is created by the salary, at {rate:house.rrsp} of earned income up to the {year_label} dollar limit of {money:house.rrsp.limit}, minus a pension adjustment. A dividend does not create that room.

## Common mistakes

Comparing a dividend with a salary while forgetting the corporate tax already paid on the dividend. Ignoring employer CPP when the salary looks cheaper on the personal return alone. Running the screen for Quebec and trusting a blank or a refusal as if it were a zero tax. Using a year the file does not label.

## Recent changes

The gross-up and the federal and provincial dividend credits move with legislation. This screen stays on the year label stored with those rates, checked {updated}. It does not relabel them as {year_label} if the file itself names a different year.

## How provinces differ

The personal brackets and the provincial dividend tax credit differ. Ontario and British Columbia are not the same credit. Alberta’s first personal rate is {rate:AB.income.first}; British Columbia’s is {rate:BC.income.first}. Those rates affect the salary side. The dividend side uses the credit in the file for the province you picked. Quebec is not estimated here.

## Questions

### Why is Quebec off?

The Quebec abatement and the Quebec dividend tax credit are not in this screen. A number for Quebec would be an invention. The screen refuses that estimate instead.

### Does a dividend create RRSP room?

No. Salary does, up to the {year_label} limit of {money:house.rrsp.limit} at {rate:house.rrsp} of earned income, after the pension adjustment. A dividend does not.

### Is the cheaper option always a dividend?

No. Salary uses personal credits and creates pensionable earnings and RRSP room. CPP is a cost, not just a deduction. The comparison has to include the corporate tax and the personal tax together.

### Can I use a year the file does not list?

No. The screen will not relabel old dividend rates as a new year.

### What about a capital dividend?

A capital dividend from the capital dividend account is a different distribution. It is not the eligible or non-eligible dividend this comparison prices.

### Is this advice to pay a salary or a dividend?

No. It is an estimate from the rates in the file so you can see the tax difference. Split income, the small-business limit, and your own payroll facts are not in the model.

## Official sources

- [Canada Revenue Agency corporation tax rates](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/corporations/corporation-tax-rates.html)
- [Canada Revenue Agency RRSP limits](https://www.canada.ca/en/revenue-agency/services/tax/registered-plans-administrators/pspa/mp-rrsp-dpsp-tfsa-limits-ympe.html)
