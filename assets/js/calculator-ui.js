/* Lite calculator UI. Reads and writes the URL fragment only. Never calls fetch. */
(function () {
	"use strict";
	var core = window.BomcasCalc;
	if (!core) {
		return;
	}

	function readHash() {
		var out = {};
		var raw = (window.location.hash || "").replace(/^#/, "");
		if (!raw) {
			return out;
		}
		raw.split("&").forEach(function (part) {
			var bits = part.split("=");
			if (bits[0]) {
				out[decodeURIComponent(bits[0])] = decodeURIComponent(bits[1] || "");
			}
		});
		return out;
	}

	function writeHash(map) {
		var parts = Object.keys(map).filter(function (k) { return map[k] !== "" && map[k] != null; }).map(function (k) {
			return encodeURIComponent(k) + "=" + encodeURIComponent(map[k]);
		});
		var next = parts.length ? "#" + parts.join("&") : "";
		if (window.history && window.history.replaceState) {
			window.history.replaceState(null, "", window.location.pathname + window.location.search + next);
		} else {
			window.location.hash = next;
		}
	}

	function money(n) {
		var root = document.querySelector("[data-bomcas-calc]");
		var loc = root && root.getAttribute("data-locale") ? root.getAttribute("data-locale") : "en-CA";
		var cur = root && root.getAttribute("data-currency") ? root.getAttribute("data-currency") : "CAD";
		return Number(n || 0).toLocaleString(loc, { style: "currency", currency: cur });
	}

	function pct(n) {
		return (n * 100).toFixed(2) + "%";
	}

	function field(root, name) {
		return root.querySelector("[data-field='" + name + "']");
	}

	function val(root, name) {
		var el = field(root, name);
		return el ? el.value : "";
	}

	function bind(root) {
		var kind = root.getAttribute("data-calc");
		var hash = readHash();
		Array.prototype.forEach.call(root.querySelectorAll("[data-field]"), function (el) {
			if (hash[el.getAttribute("data-field")] != null && hash[el.getAttribute("data-field")] !== "") {
				el.value = hash[el.getAttribute("data-field")];
			}
			el.addEventListener("input", function () { render(root); });
			el.addEventListener("change", function () { render(root); });
		});
		var printBtn = root.querySelector("[data-action='print']");
		if (printBtn) {
			printBtn.addEventListener("click", function () { window.print(); });
		}
		var linkBtn = root.querySelector("[data-action='link']");
		if (linkBtn) {
			linkBtn.addEventListener("click", function () {
				render(root);
				var url = window.location.href;
				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(url);
				}
				var note = root.querySelector("[data-link-note]");
				if (note) {
					note.textContent = "Link copied. The inputs sit in the fragment after # and are not sent to the server.";
				}
			});
		}
		var resetBtn = root.querySelector("[data-action='reset']");
		if (resetBtn) {
			resetBtn.addEventListener("click", function () {
				Array.prototype.forEach.call(root.querySelectorAll("[data-field]"), function (el) {
					el.value = el.getAttribute("data-default") || "";
				});
				render(root);
			});
		}
		render(root, true);
	}

	function collect(root) {
		var map = {};
		Array.prototype.forEach.call(root.querySelectorAll("[data-field]"), function (el) {
			map[el.getAttribute("data-field")] = el.value;
		});
		writeHash(map);
		return map;
	}

	function yearData(root, year) {
		var blob = JSON.parse(root.querySelector("script[type='application/json']").textContent);
		if (blob.years[year]) {
			return blob.years[year];
		}
		var keys = Object.keys(blob.years);
		return blob.years[keys[keys.length - 1]];
	}

	function render(root, skipHash) {
		var kind = root.getAttribute("data-calc");
		var map = skipHash ? (function () {
			var m = {};
			Array.prototype.forEach.call(root.querySelectorAll("[data-field]"), function (el) {
				m[el.getAttribute("data-field")] = el.value;
			});
			return m;
		})() : collect(root);
		var out = root.querySelector("[data-results]");
		if (!out) {
			return;
		}
		if (kind === "sales-tax") {
			renderSales(root, map, out);
		} else if (kind === "income-tax") {
			renderIncome(root, map, out);
		} else if (kind === "mortgage-payment") {
			renderMortgage(root, map, out);
		}
		root.dispatchEvent(new CustomEvent("bomcas-calculated", { bubbles: true, detail: { kind: kind, map: map } }));
	}

	function renderSales(root, map, out) {
		var data = JSON.parse(root.querySelector("script[type='application/json']").textContent);
		var segment = core.salesOnDate(data, map.province || "BC", map.date || "2026-11-08");
		if (!segment) {
			out.innerHTML = "<p>No rate is available for that date.</p>";
			return;
		}
		var result = core.salesTax(parseFloat(map.amount || "0"), segment, map.direction || "add");
		out.innerHTML = ""
			+ row("Before tax", money(result.base))
			+ row("GST", money(result.parts.gst))
			+ row("PST / RST", money(result.parts.pst))
			+ row("HST", money(result.parts.hst))
			+ row("QST", money(result.parts.qst))
			+ row("Tax", money(result.tax))
			+ row("Total", money(result.total));
	}

	function renderIncome(root, map, out) {
		var data = yearData(root, map.year || "2026");
		var result = core.taxAnnual({
			employment: map.employment,
			selfEmployment: map.selfEmployment,
			other: map.other,
			eligibleDividends: map.eligibleDividends,
			nonEligibleDividends: map.nonEligibleDividends,
			capitalGains: map.capitalGains,
			rrsp: map.rrsp,
			fhsa: map.fhsa,
			taxPaid: map.taxPaid,
			province: map.province || "BC"
		}, data);
		out.innerHTML = ""
			+ row("Taxable income", money(result.taxableIncome))
			+ row("Federal tax, after credits", money(result.federalTax))
			+ row("Provincial tax, after credits", money(result.provincialTax))
			+ row("Total tax", money(result.totalTax))
			+ (result.workersDeduction ? row("Quebec deduction for workers (employment income only)", money(result.workersDeduction)) : "")
			+ row("Average rate", pct(result.averageRate))
			+ row("Marginal rate on ordinary income", result.marginalRate == null ? "—" : pct(result.marginalRate))
			+ row("After-tax income", money(result.afterTax))
			+ (map.taxPaid === "" || map.taxPaid == null ? "" : row("Balance (positive means owing)", money(result.balance)));
		var steps = root.querySelector("[data-steps]");
		if (steps) {
			steps.textContent = "Canadian federal tax before credits " + money(result.lines.basicFederal)
				+ ". Credits " + money(result.lines.federalCredits)
				+ (map.province === "QC" ? ". Quebec abatement " + money(result.lines.abatement) : "")
				+ ". Provincial surtax " + money(result.lines.surtax)
				+ ", health premium " + money(result.lines.health)
				+ ", reduction " + money(result.lines.reduction) + ".";
		}
	}

	function renderMortgage(root, map, out) {
		var result = core.mortgagePayment({
			price: map.price,
			down: map.down,
			annualRate: (parseFloat(map.annualRate || "0") / 100),
			amortYears: map.amortYears,
			paymentsPerYear: map.paymentsPerYear,
			compounding: map.compounding
		});
		out.innerHTML = ""
			+ row("Principal", money(result.principal))
			+ row("Payment", money(result.payment))
			+ row("Number of payments", String(result.payments))
			+ row("Interest over the amortization", money(result.interest));
	}

	function row(label, value) {
		return "<div class='bomcas-result'><dt>" + label + "</dt><dd>" + value + "</dd></div>";
	}

	function boot() {
		if (window.BomcasCalcHydrate) {
			window.BomcasCalcHydrate();
		}
		Array.prototype.forEach.call(document.querySelectorAll("[data-bomcas-calc]"), bind);
	}
	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", boot);
	} else {
		boot();
	}
})();
document.addEventListener("click", function (event) {
	var button = event.target.closest("[data-bomcas-print]");
	if (!button) {
		return;
	}
	event.preventDefault();
	window.print();
});
