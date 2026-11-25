/* Lead form, PDF download and on-site counts. Calculator maths stays in the page. */
(function () {
	"use strict";
	var cfg = window.BomcasGrowth || {};
	if (!cfg.ajax) {
		return;
	}

	function send(action, fields) {
		var body = new FormData();
		body.append("action", action);
		body.append("nonce", cfg.nonce || "");
		Object.keys(fields).forEach(function (key) {
			body.append(key, fields[key]);
		});
		return fetch(cfg.ajax, { method: "POST", credentials: "same-origin", body: body });
	}

	function pairs(root) {
		var out = [];
		var nodes = root.querySelectorAll("[data-results] dt");
		Array.prototype.forEach.call(nodes, function (dt) {
			var dd = dt.nextElementSibling;
			out.push({ label: dt.textContent || "", value: dd ? dd.textContent || "" : "" });
		});
		return out;
	}

	function lines(root) {
		return pairs(root).map(function (row) {
			return row.label + " " + row.value;
		});
	}

	document.querySelectorAll("[data-bomcas-calc]").forEach(function (root) {
		var calc = root.getAttribute("data-calc") || "";
		send("bomcas_stat", { event: "views", calculator: calc, page_id: String(cfg.page || 0) });
		var ran = false;
		root.addEventListener("input", function () {
			if (ran) {
				return;
			}
			ran = true;
			send("bomcas_stat", { event: "runs", calculator: calc, page_id: String(cfg.page || 0) });
			if (cfg.ga4) {
				var s = document.createElement("script");
				s.src = "https://www.googletagmanager.com/gtag/js?id=" + encodeURIComponent(cfg.ga4);
				s.async = true;
				document.head.appendChild(s);
				window.dataLayer = window.dataLayer || [];
				window.gtag = function () { window.dataLayer.push(arguments); };
				window.gtag("js", new Date());
				window.gtag("config", cfg.ga4);
				window.gtag("event", "bomcas_calculation", { calculator: calc });
			}
		});
		var form = root.querySelector("[data-lead-form]");
		if (form) {
			form.addEventListener("submit", function (event) {
				event.preventDefault();
				var data = new FormData(form);
				var fields = {
					person_name: data.get("person_name") || "",
					email: data.get("email") || "",
					phone: data.get("phone") || "",
					consent: form.querySelector("[name=consent]").checked ? "1" : "",
					company_website: data.get("company_website") || "",
					calculator: calc,
					page_id: String(cfg.page || 0),
					results: JSON.stringify(pairs(root))
				};
				Array.prototype.forEach.call(form.querySelectorAll("[name^='extra[']"), function (input) {
					fields[input.name] = input.value;
				});
				var note = form.querySelector("[data-lead-note]");
				if (note) {
					note.textContent = (cfg.strings && cfg.strings.working) || "Sending…";
				}
				send("bomcas_lead", fields).then(function (res) {
					if (note) {
						note.textContent = res.ok ? ((cfg.strings && cfg.strings.sent) || "Sent. Check your inbox.") : "Try again.";
					}
				});
			});
		}
		var issue = root.querySelector("[data-rate-issue]");
		if (issue) {
			issue.addEventListener("submit", function (event) {
				event.preventDefault();
				var data = new FormData(issue);
				send("bomcas_rate_issue", {
					rate_name: data.get("rate_name") || "",
					detail: data.get("detail") || "",
					email: data.get("email") || "",
					calculator: data.get("calculator") || calc,
					company_website: data.get("company_website") || ""
				}).then(function (res) {
					var note = issue.querySelector("[data-issue-note]");
					if (note) {
						note.textContent = res.ok ? "Sent." : "Try again.";
					}
				});
			});
		}
		var pdf = root.querySelector("[data-action='pdf']");
		if (pdf) {
			pdf.addEventListener("click", function () {
				send("bomcas_pdf", { title: document.title, lines: JSON.stringify(lines(root)) }).then(function (res) {
					return res.blob();
				}).then(function (blob) {
					var url = URL.createObjectURL(blob);
					var a = document.createElement("a");
					a.href = url;
					a.download = "tax-estimate.pdf";
					a.click();
					URL.revokeObjectURL(url);
				});
			});
		}
	});
}());
