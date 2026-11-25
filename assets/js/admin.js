(function () {
	"use strict";

	function ready(fn) {
		if (document.readyState !== "loading") {
			fn();
		} else {
			document.addEventListener("DOMContentLoaded", fn);
		}
	}

	ready(function () {
		var root = document.querySelector(".bomcas-admin");
		if (!root) {
			return;
		}
		var tips = (window.BomcasAdminTips && window.BomcasAdminTips.fields) || {};
		Array.prototype.forEach.call(root.querySelectorAll("label, .bomcas-fieldline, .bomcas-toggle, [data-tip]"), function (node) {
			if (node.querySelector(".bomcas-tip") || (node.parentNode && node.parentNode.querySelector(".bomcas-tip"))) {
				return;
			}
			if (node.closest && node.closest("thead")) {
				return;
			}
			var input = node.querySelector("input, select, textarea");
			var key = (input && input.getAttribute("name")) || "";
			var text = node.getAttribute("data-tip") || (key && tips[key]) || "";
			if (!text) {
				return;
			}
			var btn = document.createElement("button");
			btn.type = "button";
			btn.className = "bomcas-tip";
			btn.setAttribute("aria-label", (window.BomcasAdminTips && window.BomcasAdminTips.helpLabel) || "Help");
			btn.innerHTML = '<span aria-hidden="true">?</span><span class="bomcas-tip__bubble" role="tooltip"></span>';
			btn.querySelector(".bomcas-tip__bubble").textContent = text;
			node.appendChild(btn);
		});
		var toast = root.getAttribute("data-toast");
		if (toast) {
			var note = document.createElement("div");
			note.className = "bomcas-toast";
			note.setAttribute("role", "status");
			note.textContent = toast;
			document.body.appendChild(note);
			window.setTimeout(function () { note.remove(); }, 4000);
		}

		Array.prototype.forEach.call(root.querySelectorAll("table.bomcas-data"), enhanceTable);
		Array.prototype.forEach.call(root.querySelectorAll("[data-bomcas-confirm]"), function (button) {
			button.addEventListener("click", function (event) {
				var message = button.getAttribute("data-bomcas-confirm");
				var modal = document.getElementById("bomcas-confirm");
				if (!modal) {
					if (!window.confirm(message)) {
						event.preventDefault();
					}
					return;
				}
				event.preventDefault();
				modal.hidden = false;
				modal.querySelector("[data-confirm-text]").textContent = message;
				var go = modal.querySelector("[data-confirm-go]");
				go.onclick = function () {
					modal.hidden = true;
					button.closest("form").submit();
				};
			});
		});
		Array.prototype.forEach.call(root.querySelectorAll("[data-copy-btn]"), function (button) {
			button.addEventListener("click", function () {
				var field = button.parentNode.querySelector("[data-copy]");
				if (field && navigator.clipboard) {
					navigator.clipboard.writeText(field.value);
					button.textContent = "Copied";
				}
			});
		});
		var closer = document.querySelector("[data-confirm-cancel]");
		if (closer) {
			closer.addEventListener("click", function () {
				document.getElementById("bomcas-confirm").hidden = true;
			});
		}

		Array.prototype.forEach.call(root.querySelectorAll("[data-plan-pick]"), function (card) {
			card.addEventListener("click", function () {
				Array.prototype.forEach.call(root.querySelectorAll("[data-plan-pick]"), function (other) {
					other.classList.remove("is-selected");
					other.setAttribute("aria-pressed", "false");
				});
				card.classList.add("is-selected");
				card.setAttribute("aria-pressed", "true");
				var input = root.querySelector("[data-plan-input]");
				var interval = root.querySelector("[data-interval-input]");
				if (input) { input.value = card.getAttribute("data-plan"); }
				if (interval) { interval.value = card.getAttribute("data-interval"); }
				Array.prototype.forEach.call(root.querySelectorAll("[data-price]"), function (line) {
					var show = line.getAttribute("data-price") === card.getAttribute("data-plan") + ":" + card.getAttribute("data-interval");
					line.hidden = !show;
				});
			});
		});
	});

	function enhanceTable(table) {
		var rows = Array.prototype.slice.call(table.tBodies[0] ? table.tBodies[0].rows : []);
		if (!rows.length) {
			return;
		}
		var bar = document.createElement("div");
		bar.className = "bomcas-toolbar";
		var search = document.createElement("input");
		search.type = "search";
		search.placeholder = table.getAttribute("data-search") || "Search";
		bar.appendChild(search);
		table.parentNode.insertBefore(bar, table);
		var pageSize = 8;
		var page = 0;
		var matched = rows.slice();
		var status = document.createElement("span");
		function draw() {
			var q = search.value.toLowerCase();
			matched = rows.filter(function (row) {
				return row.textContent.toLowerCase().indexOf(q) !== -1;
			});
			var pages = Math.max(1, Math.ceil(matched.length / pageSize));
			if (page > pages - 1) {
				page = pages - 1;
			}
			var start = page * pageSize;
			var end = Math.min(matched.length, start + pageSize);
			rows.forEach(function (row) { row.hidden = true; });
			matched.slice(start, end).forEach(function (row) { row.hidden = false; });
			prev.disabled = page <= 0;
			next.disabled = page >= pages - 1;
			status.textContent = matched.length ? ("Showing " + (start + 1) + "-" + end + " of " + matched.length) : "Showing 0 of 0";
		}
		search.addEventListener("input", function () { page = 0; draw(); });
		Array.prototype.forEach.call(table.tHead.rows[0].cells, function (cell, index) {
			cell.tabIndex = 0;
			cell.addEventListener("click", function () { sort(index); });
			cell.addEventListener("keydown", function (event) {
				if (event.key === "Enter" || event.key === " ") { sort(index); }
			});
		});
		function sort(index) {
			rows.sort(function (a, b) {
				return a.cells[index].textContent.localeCompare(b.cells[index].textContent, undefined, { numeric: true });
			});
			rows.forEach(function (row) { table.tBodies[0].appendChild(row); });
			draw();
		}
		var pager = document.createElement("p");
		var prev = document.createElement("button");
		prev.type = "button";
		prev.className = "button";
		prev.textContent = "Previous";
		var next = document.createElement("button");
		next.type = "button";
		next.className = "button";
		next.textContent = "Next";
		prev.addEventListener("click", function () { page = Math.max(0, page - 1); draw(); });
		next.addEventListener("click", function () { page += 1; draw(); });
		pager.appendChild(prev);
		pager.appendChild(document.createTextNode(" "));
		pager.appendChild(next);
		pager.appendChild(document.createTextNode(" "));
		pager.appendChild(status);
		table.parentNode.insertBefore(pager, table.nextSibling);
		draw();
	}
})();
