---
excerpt: Northwest Territories sales tax for {year_label} is GST only. A zero provincial line is the law in this table, not a missing rate and not Alberta’s page.
---

## What this tax is

The Northwest Territories do not levy a territorial sales tax. On a standard taxable supply the tax is the federal goods and services tax at {rate:NT.sales.gst}. This is not the Alberta page, even though Alberta also has no provincial sales tax. Income tax is still charged in the territory and is not added to the receipt. The estimate is a general supply, not a vehicle.

## How the calculation works

The pre-tax price is multiplied by the GST rate. Provincial, harmonized, and Quebec components are zero because they are not charged. A supplier in British Columbia who delivers a supply that is taxed in British Columbia still charges British Columbia PST. A supply that is taxed in the Northwest Territories does not pick up that PST merely because the goods came from the south.

## Worked examples

On {example:NT.sales.2500.amount} the GST is {example:NT.sales.2500.gst}. Provincial sales tax is {example:NT.sales.2500.pst}. The tax is {example:NT.sales.2500.tax} and the amount with tax is {example:NT.sales.2500.total}.

On {example:NT.sales.18000.amount} the tax is {example:NT.sales.18000.tax}. The second price did not create a territorial tax. There is no bracket that switches on as the invoice grows.

## What is left out

A vehicle is on the vehicle page. The sales table does not add a territorial registration tax. Exempt supplies are not taxed. A community’s fee is not looked up from a postal code.

## Common mistakes

Adding British Columbia PST because the freight originated in Calgary. The sample British Columbia tax on this price is {example:BC.sales.2500.tax}, which includes PST. That figure is for a supply taxed in British Columbia. Treating the zero provincial line as a crashed form. Using the Alberta page’s wording as if the territories had no income tax. They do. It is just not this receipt.

## Recent changes

The GST rate of {rate:NT.sales.gst} is the {year_label} row checked {updated}. The territory did not adopt Nova Scotia’s harmonized-tax change.

## Neighbouring jurisdictions

Nunavut also charges GST only. Its tax on the sample price is {example:NU.sales.2500.tax}. The sales result matches. The income-tax tables do not. Nunavut’s first income-tax rate is {rate:NU.income.first}. Do not put that rate on an invoice.

British Columbia charges GST and PST. Its tax on the sample is {example:BC.sales.2500.tax}. Place of supply decides which page you are on.

## Questions

### Is a zero provincial tax a bug?

No. The territories do not levy one. GST of {example:NT.sales.2500.gst} is the tax on the first price.

### Is this the Alberta calculator?

No. The GST arithmetic matches. Residency, payroll, and income tax do not. Open this page for a supply taxed here.

### Should I add British Columbia PST?

Only when the supply is taxed in British Columbia. Their tax on this price is {example:BC.sales.2500.tax}.

### Are vehicles included?

No. Registration is not inside this GST figure.

### Does the income-tax rate belong on the receipt?

No. Territorial income tax is a different page.

### Is this a return?

No. It is the {year_label} GST rate, checked {updated}.

## Official sources

- [Canada Revenue Agency GST/HST rates](https://www.canada.ca/en/revenue-agency/services/tax/businesses/topics/gst-hst-businesses/charge-collect-which-rate.html)
- [Northwest Territories finance](https://www.fin.gov.nt.ca/en/services/taxation)
