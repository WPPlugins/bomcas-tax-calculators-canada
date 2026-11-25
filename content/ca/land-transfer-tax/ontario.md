---
excerpt: Ontario land transfer tax for {year_label} uses the provincial table. Toronto’s tax stays off until you select it. A first-time claim is not assumed.
---

## What this tax is

Ontario land transfer tax is a provincial tax on certain registrations of land. It is not income tax and it is not harmonized sales tax. Toronto charges its own municipal land transfer tax on top, and only when that tax applies and you select it. This estimate uses the provincial table with the first-time refund left off and the Toronto line left off, unless you turn those boxes on.

British Columbia’s property transfer tax is a different statute. Quebec’s welcome tax is a third statute. Neither is estimated from these bands.

## How the calculation works

The price, or the value the statute uses, is split across the bands in the file. Only the slice inside a band is charged at that band’s rate. The whole price is not taxed at the top rate. A first-time homebuyer refund applies only when you establish that the buyer qualifies and you select it. A further tax on a high-value residential property applies only when that box is selected. If a price sits outside the municipal schedule the file will price, the Toronto amount is left unavailable rather than replaced with zero.

## Worked purchases

A price of {example:ON.land.650000.price}, with no first-time claim and no Toronto tax, draws provincial land transfer tax of {example:ON.land.650000.tax}. That is the general table only.

A price of {example:ON.land.920000.price} on the same settings draws {example:ON.land.920000.tax}. The increase is the extra slices of the bands, not a flat percentage of the first result. Turning Toronto on would add the municipal schedule. These two figures leave it off, so they are not a Toronto purchase.

## Refunds and municipal tax

The first-time refund in the file is a claim, not a default. A buyer who does not qualify does not get it by leaving the box on. Toronto’s municipal tax uses the schedule in the file for the date you select. A price the file treats as above what that schedule will calculate stays unavailable. Do not type zero and register the deed on that basis.

## Common mistakes

Using British Columbia’s tax of {example:BC.land.650000.tax} on an Ontario deed. That is the British Columbia result on the same price, with its own first-time box off. Assuming every Toronto purchase is included in {example:ON.land.650000.tax}. Forgetting that a refund you did not qualify for is not in the worked line, so the worked line is not your refund case.

## Recent changes

The provincial bands and the Toronto schedule the file contains were checked {updated}. A municipal change that is not in the file is not guessed. The year on the page is {year_label}.

## Neighbouring jurisdictions

British Columbia’s property transfer tax on {example:BC.land.650000.price} is {example:BC.land.650000.tax}, against Ontario’s {example:ON.land.650000.tax} on the provincial table alone. British Columbia’s further tax on a high residential value is its own box. Toronto’s municipal tax is not that box.

Quebec’s welcome tax uses brackets that start at {money:house.welcome.first} and {money:house.welcome.second}. It is collected as a municipal duty on a different statute. Do not run an Ontario deed through those brackets.

## Questions

### Is Toronto included in the worked prices?

No. {example:ON.land.650000.tax} and {example:ON.land.920000.tax} are the provincial table with Toronto off.

### Is the first-time refund automatic?

No. Select it only when the buyer qualifies. The worked prices leave it off.

### Is the whole price taxed at the top rate?

No. Each slice uses its own rate.

### What if the Toronto amount is unavailable?

The file will not invent it. Unavailable is not zero. Do not register on a zero you typed into the gap.

### Is this British Columbia property transfer tax?

No. British Columbia’s tax on the first price is {example:BC.land.650000.tax}.

### Is this a registration?

No. It is a {year_label} estimate, checked {updated}. The land registry and the lawyer’s statement govern the deed.

## Official sources

- [Ontario land transfer tax](https://www.ontario.ca/document/land-transfer-tax)
- [Municipal land transfer tax](https://www.toronto.ca/services-payments/property-taxes-utilities/municipal-land-transfer-tax-mltt/)
