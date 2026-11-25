/* BOMCAS Tax Calculators – Canada — Lite calculation engine.
 * Annual income tax, sales tax and mortgage payment.
 * No network calls. No storage of inputs.
 */
(function () {
	"use strict";
	/* The server prints rate data in a data attribute. Copy it into an inert JSON node for the UI scripts. */
	function hydrate() {
		Array.prototype.forEach.call(document.querySelectorAll("[data-bomcas-payload]"), function (root) {
			var has = false;
			Array.prototype.forEach.call(root.children, function (child) {
				if (child.tagName === "SCRIPT" && child.getAttribute("type") === "application/json") {
					has = true;
				}
			});
			if (has) {
				return;
			}
			var node = document.createElement("script");
			node.type = "application/json";
			node.textContent = root.getAttribute("data-bomcas-payload") || "{}";
			root.appendChild(node);
		});
	}
	window.BomcasCalcHydrate = hydrate;
	hydrate();
	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", hydrate);
	}
})();
(function (root) {
	"use strict";

	function round2(n) {
		if (!isFinite(n)) {
			return 0;
		}
		var sign = n < 0 ? -1 : 1;
		return sign * (Math.round((Math.abs(n) + 1e-8) * 100) / 100);
	}

	function num(v) {
		var n = typeof v === "number" ? v : parseFloat(v);
		return isFinite(n) ? n : 0;
	}

	function bracketTax(income, brackets) {
		var tax = 0;
		var prev = 0;
		var i;
		for (i = 0; i < brackets.length; i++) {
			var cap = brackets[i].upto == null ? income : brackets[i].upto;
			var upper = Math.min(income, cap);
			var slice = Math.max(0, upper - prev);
			tax += slice * brackets[i].rate;
			prev = cap;
			if (brackets[i].upto == null || income <= brackets[i].upto) {
				break;
			}
		}
		return tax;
	}

	function bpaAmount(rec, netIncome) {
		var ns = rec.ns_bpa;
		if (ns && ns.mode === "income") {
			if (netIncome <= ns.from) {
				return ns.max;
			}
			if (netIncome >= ns.to) {
				return ns.min;
			}
			return round2(ns.max - (netIncome - ns.from) * ns.rate);
		}
		var b = rec.bpa;
		if (b.phase_from == null || netIncome <= b.phase_from) {
			return b.max;
		}
		if (netIncome >= b.phase_to) {
			return b.min;
		}
		var span = b.phase_to - b.phase_from;
		var extra = b.max - b.min;
		return round2(b.max - (netIncome - b.phase_from) * (extra / span));
	}

	function annualContributions(employment, spec, quebec) {
		var pen = Math.max(0, Math.min(employment, spec.ympe) - spec.exemption);
		var rate = quebec ? spec.qpp_rate : spec.rate;
		var max = quebec ? spec.qpp_max : spec.max;
		var contrib = round2(Math.min(pen * rate, max));
		var second = 0;
		if (spec.yampe && employment > spec.ympe && spec.second_rate) {
			var band = Math.min(employment, spec.yampe) - spec.ympe;
			second = round2(Math.min(band * spec.second_rate, spec.second_max));
		}
		var creditBase;
		var deduction;
		var baseRate = quebec ? spec.qpp_base_rate : spec.base_rate;
		if (spec.credit_mode !== "split" || baseRate == null) {
			creditBase = contrib;
			deduction = second;
		} else {
			var baseMax = quebec ? spec.qpp_base_max : spec.base_max;
			var base = round2(Math.min(contrib * (baseRate / rate), baseMax));
			creditBase = base;
			deduction = round2((contrib - base) + second);
		}
		var eiRate = quebec ? spec.ei_qc_rate : spec.ei_rate;
		var eiMaxEarn = spec.ei_max_earnings;
		var eiCap = quebec ? spec.ei_qc_max : spec.ei_max;
		var ei = round2(Math.min(employment, eiMaxEarn) * eiRate);
		ei = round2(Math.min(ei, eiCap));
		var qpip = 0;
		if (quebec && spec.qpip_rate) {
			qpip = round2(Math.min(employment, spec.qpip_max_earnings) * spec.qpip_rate);
			qpip = round2(Math.min(qpip, spec.qpip_max));
		}
		return {
			cpp: quebec ? 0 : contrib,
			qpp: quebec ? contrib : 0,
			cpp2: second,
			ei: ei,
			qpip: qpip,
			creditBase: creditBase,
			deduction: deduction
		};
	}

	function healthPremium(income) {
		if (income <= 20000) {
			return 0;
		}
		var steps = [
			{ upto: 36000, base: 0, rate: 0.06, over: 20000, cap: 300 },
			{ upto: 48000, base: 300, rate: 0.06, over: 36000, cap: 450 },
			{ upto: 72000, base: 450, rate: 0.25, over: 48000, cap: 600 },
			{ upto: 200000, base: 600, rate: 0.25, over: 72000, cap: 750 },
			{ upto: Infinity, base: 750, rate: 0.25, over: 200000, cap: 900 }
		];
		var i;
		for (i = 0; i < steps.length; i++) {
			if (income <= steps[i].upto) {
				return round2(Math.min(steps[i].cap, steps[i].base + steps[i].rate * (income - steps[i].over)));
			}
		}
		return 0;
	}

	function surtaxOf(basic, tiers) {
		var s = 0;
		var i;
		for (i = 0; i < tiers.length; i++) {
			if (basic > tiers[i].over) {
				s += tiers[i].rate * (basic - tiers[i].over);
			}
		}
		return s;
	}

	/**
	 * Annual personal tax estimate.
	 * input: employment, selfEmployment, other, eligibleDividends, nonEligibleDividends,
	 *        capitalGains, rrsp, fhsa, taxPaid, province
	 * yearData: one personal_tax year object
	 * options.skipContributions: taxable income is already net of the CPP additional deduction
	 * options.creditBase, options.ei, options.qpip, options.grossEmployment: payroll override
	 */
	function taxAnnual(input, yearData, options) {
		options = options || {};
		var provCode = input.province || "BC";
		var fed = yearData.jurisdictions && yearData.jurisdictions.FED;
		var prov = yearData.jurisdictions && yearData.jurisdictions[provCode];
		if (!fed || !prov) {
			return {
				province: provCode,
				federalTax: null,
				provincialTax: null,
				totalTax: null,
				partialEstimate: true,
				omissions: ["jurisdiction data"]
			};
		}
		var employment = Math.max(0, num(input.employment));
		var selfEmp = Math.max(0, num(input.selfEmployment));
		var other = Math.max(0, num(input.other));
		var elig = Math.max(0, num(input.eligibleDividends));
		var nonElig = Math.max(0, num(input.nonEligibleDividends));
		var gains = Math.max(0, num(input.capitalGains));
		var rrsp = Math.max(0, num(input.rrsp));
		var fhsa = Math.max(0, num(input.fhsa));
		var quebec = provCode === "QC";
		var contrib = options.skipContributions
			? {
				cpp: 0, qpp: 0, cpp2: 0, ei: num(options.ei), qpip: num(options.qpip),
				creditBase: num(options.creditBase), deduction: 0
			}
			: annualContributions(employment, yearData.cpp_ei, quebec);
		var div = yearData.dividends;
		var eligTaxable = elig * (1 + div.eligible_grossup);
		var nonEligTaxable = nonElig * (1 + div.non_eligible_grossup);
		var gainsTaxable = gains * yearData.capital_gains_inclusion;
		var cashIncome = employment + selfEmp + other + elig + nonElig + gains;
		var grossed = employment + selfEmp + other + eligTaxable + nonEligTaxable + gainsTaxable;
		var workers = 0;
		if (quebec && yearData.quebec_workers && options.taxableIncome == null) {
			workers = Math.min(yearData.quebec_workers.max, employment * yearData.quebec_workers.rate);
		}
		var netForBpa = options.taxableIncome != null
			? num(options.taxableIncome)
			: Math.max(0, grossed - rrsp - fhsa - contrib.deduction - workers);
		var taxable = netForBpa;
		var fedBpa = bpaAmount(fed, netForBpa);
		var provBpa = bpaAmount(prov, netForBpa);
		var creditRate = fed.credit_rate;
		var basicFed = bracketTax(taxable, fed.brackets);
		var grossEmployment = options.grossEmployment != null ? num(options.grossEmployment) : employment;
		var cea = Math.min(fed.cea || 0, grossEmployment);
		var k1 = creditRate * fedBpa;
		var k2 = creditRate * (contrib.creditBase + contrib.ei + contrib.qpip);
		var k4 = creditRate * cea;
		var dtc = eligTaxable * div.federal_eligible_credit_on_taxable + nonEligTaxable * div.federal_non_eligible_credit_on_taxable;
		var basicFederal = Math.max(0, basicFed - k1 - k2 - k4 - dtc);
		var abatement = quebec ? 0.165 * basicFederal : 0;
		var federalTax = Math.max(0, basicFederal - abatement);

		var basicProv = bracketTax(taxable, prov.brackets);
		var k1p = prov.credit_rate * provBpa;
		var k2p = prov.credit_rate * (contrib.creditBase + contrib.ei + (quebec ? contrib.qpip : 0));
		var k4p = prov.cea ? prov.credit_rate * Math.min(prov.cea, grossEmployment) : 0;
		var provAfterCredits = Math.max(0, basicProv - k1p - k2p - k4p);
		if (prov.ab_k5p) {
			var k5base = k1p + k2p;
			if (k5base > prov.ab_k5p.threshold) {
				provAfterCredits = Math.max(0, provAfterCredits - (k5base - prov.ab_k5p.threshold) * prov.ab_k5p.multiplier);
			}
		}
		var surtax = 0;
		if (prov.on_surtax) {
			surtax = surtaxOf(provAfterCredits, prov.on_surtax);
		}
		if (prov.pe_surtax && provAfterCredits > prov.pe_surtax.over) {
			surtax += prov.pe_surtax.rate * (provAfterCredits - prov.pe_surtax.over);
		}
		var health = prov.on_health ? healthPremium(taxable) : 0;
		var reduction = 0;
		if (prov.bc_reduction) {
			var red = prov.bc_reduction;
			var bc;
			if (taxable <= red.threshold) {
				bc = Math.min(provAfterCredits, red.basic);
			} else if (taxable >= red.upper) {
				bc = 0;
			} else {
				bc = Math.max(0, red.basic - (taxable - red.threshold) * red.rate);
				bc = Math.min(provAfterCredits, bc);
			}
			reduction = bc;
		}
		if (prov.on_reduction_basic != null) {
			var before = provAfterCredits + surtax;
			var candidate = Math.min(before, (2 * prov.on_reduction_basic) - before);
			reduction = Math.max(0, candidate);
		}
		var provincialKnown = !options.omitProvincial;
		var provincialTax = provincialKnown ? Math.max(0, provAfterCredits + surtax + health - reduction) : null;
		var totalTax = Math.max(0, federalTax + provincialTax - num(input.taxPaid));
		var owing = totalTax;
		var refund = 0;
		if (owing < 0) {
			refund = -owing;
			owing = 0;
		}

		var marginal = null;
		var marginalGains = null;
		var marginalElig = null;
		if (!options.noMarginal) {
			var plus = taxAnnual(Object.assign({}, input, { employment: employment + 100, taxPaid: 0 }), yearData, { noMarginal: true }).totalTax;
			var here = taxAnnual(Object.assign({}, input, { taxPaid: 0 }), yearData, { noMarginal: true }).totalTax;
			marginal = (plus - here) / 100;
			var gPlus = taxAnnual(Object.assign({}, input, { capitalGains: gains + 100, taxPaid: 0 }), yearData, { noMarginal: true }).totalTax;
			marginalGains = (gPlus - here) / 100;
			var dPlus = taxAnnual(Object.assign({}, input, { eligibleDividends: elig + 100, taxPaid: 0 }), yearData, { noMarginal: true }).totalTax;
			marginalElig = (dPlus - here) / 100;
		}

		return {
			province: provCode,
			year: yearData.tax_year,
			cashIncome: round2(cashIncome),
			taxableIncome: round2(taxable),
			federalTax: round2(federalTax),
			provincialTax: provincialTax == null ? null : round2(provincialTax),
			totalTax: provincialTax == null ? null : round2(federalTax + provincialTax),
			balance: provincialTax == null ? null : round2((federalTax + provincialTax) - num(input.taxPaid)),
			refund: provincialTax == null ? null : round2(refund),
			owing: provincialTax == null ? null : round2(owing),
			afterTax: provincialTax == null ? null : round2(cashIncome - (federalTax + provincialTax)),
			averageRate: provincialTax == null || cashIncome <= 0 ? null : (federalTax + provincialTax) / cashIncome,
			marginalRate: marginal,
			marginalCapitalGains: marginalGains,
			marginalEligibleDividend: marginalElig,
			federalBpa: round2(fedBpa),
			provincialBpa: round2(provBpa),
			contributions: contrib,
			workersDeduction: round2(workers),
			partialEstimate: true,
			quebecProvincialWithholding: options.omitProvincial ? "not_calculated" : null,
			dividendCreditIsFederalOnly: true,
			omissions: [
				"spouse and dependant amounts",
				"age, pension and disability amounts",
				"tuition, medical expenses and charitable donations",
				"provincial dividend tax credits",
				"self-employed CPP or QPP",
				"Quebec solidarity credit and other refundable credits",
				"northern and caregiver amounts"
			],
			lines: {
				basicFederal: round2(basicFed),
				federalCredits: round2(k1 + k2 + k4 + dtc),
				abatement: round2(abatement),
				basicProvincial: round2(basicProv),
				surtax: round2(surtax),
				health: round2(health),
				reduction: round2(reduction)
			}
		};
	}

	function salesOnDate(salesData, province, isoDate) {
		var rows = salesData.jurisdictions[province];
		if (!rows) {
			return null;
		}
		var i;
		for (i = 0; i < rows.length; i++) {
			if (isoDate >= rows[i].from && isoDate <= rows[i].to) {
				return rows[i];
			}
		}
		return null;
	}

	function salesTax(amount, segment, direction) {
		var gst = segment.gst || 0;
		var pst = segment.pst || 0;
		var hst = segment.hst || 0;
		var qst = segment.qst || 0;
		var rate = gst + pst + hst + qst;
		var base;
		var gross;
		if (direction === "remove") {
			base = rate === 0 ? amount : amount / (1 + rate);
			gross = amount;
		} else {
			base = amount;
			gross = amount * (1 + rate);
		}
		base = round2(base);
		var parts = {
			gst: round2(base * gst),
			pst: round2(base * pst),
			hst: round2(base * hst),
			qst: round2(base * qst)
		};
		var tax = round2(parts.gst + parts.pst + parts.hst + parts.qst);
		return {
			base: base,
			tax: tax,
			total: round2(base + tax),
			rate: rate,
			parts: parts,
			label: segment.label,
			entered: round2(amount)
		};
	}

	function mortgagePayment(input) {
		var price = Math.max(0, num(input.price));
		var down = Math.max(0, num(input.down));
		var principal = Math.max(0, price - down);
		var annual = Math.max(0, num(input.annualRate));
		var years = Math.max(1, num(input.amortYears) || 25);
		var perYear = num(input.paymentsPerYear) || 12;
		var compound = input.compounding || "semi-annual";
		var j = compound === "monthly" ? (annual / perYear) : (Math.pow(1 + annual / 2, 2 / perYear) - 1);
		var n = Math.round(years * perYear);
		var payment = j === 0 ? principal / n : principal * j / (1 - Math.pow(1 + j, -n));
		var total = payment * n;
		return {
			principal: round2(principal),
			payment: round2(payment),
			payments: n,
			periodicRate: j,
			interest: round2(total - principal),
			total: round2(total),
			compounding: compound
		};
	}

	root.BomcasCalc = {
		round2: round2,
		bracketTax: bracketTax,
		bpaAmount: bpaAmount,
		annualContributions: annualContributions,
		taxAnnual: taxAnnual,
		salesOnDate: salesOnDate,
		salesTax: salesTax,
		mortgagePayment: mortgagePayment
	};
})(typeof window !== "undefined" ? window : globalThis);
