let activeGrid;

$(document).ready(function () {
	$(document).on("show.bs.modal", ".modal", function () {
		const zIndex = 1040 + 10 * $(".modal:visible").length;
		$(this).css("z-index", zIndex);
		setTimeout(() =>
			$(".modal-backdrop")
				.not(".modal-stack")
				.css("z-index", zIndex - 1)
				.addClass("modal-stack")
		);
	});

	$(document).find("input").attr("autocomplete", "off");
	$(document).find("input, textarea").attr("spellcheck", "false");

	$(document).on(
		"input",
		`input[type="text"]:not([data-uppercase="false"])`,
		function () {
			$(this).val((index, value) => {
				return value.toUpperCase();
			});
		}
	);

	$(document).on("submit", "form", function () {
		$(this)
			.find('input[type="text"]:not([data-uppercase="false"])')
			.each(function () {
				$(this).val($(this).val().toUpperCase());
			});
	});


	$(document).on("shown.bs.modal", ".modal", function () {
		$(this).find("form [name]:not([readonly], [disabled])").first().focus();
		$(this).find("form").data("hasChanged", false);
	});

	$(document).on("hide.bs.modal", ".modal", function () {
		let form = $(this).find("form");

		if (form.data("hasChanged")) {
			let confirmClose = confirm(
				"You have unsaved changes. Are you sure to close form?"
			);

			if (!confirmClose) {
				return false;
			}

			form.data("hasChanged", false);

			return true;
		}
	});

	$(document).on("collapsed-done.lte.pushmenu", function () {
		focusToGrid();
	});

	$("#loader").addClass("d-none");

	$.fn.modal.Constructor.Default.backdrop = "static";

	openMenuParents()
});

window.onbeforeunload = () => {
	let hasUnsavedChanges = false;

	$("form").each((index, element) => {
		if ($(element).data("hasChanged")) {
			hasUnsavedChanges = true;
		}
	});

	if (hasUnsavedChanges) {
		return confirm("You have unsaved changes. Are you sure to close form?");
	}

	$("#loader").removeClass("d-none");
};

const delay = (function () {
	let timer = 0;

	return function (callback, ms) {
		clearTimeout(timer);
		timer = setTimeout(callback, ms);
	};
})();

function focusToGrid() {
	let selectedIndex = $(activeGrid).jqGrid("getGridParam")?.selectedIndex ?? 0;

	$(activeGrid).setGridParam({
		triggerClick: true,
	});

	$(activeGrid)
		.find(`tr[id="${$(activeGrid).getDataIDs()[selectedIndex]}"]`)
		.click();
}

function changeJqGridRowListText() {
	$(document).find('select[id$="rowList"] option[value=0]').text("ALL");
}

function detectDeviceType() {
	const ua = navigator.userAgent;
	if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
		return "tablet";
	} else if (
		/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(
			ua
		)
	) {
		return "mobile";
	}
	return "desktop";
}

function showDialog(state, message, buttons = []) {
	let states = {
		success: {
			icon: "fa fa-check",
			color: "text-success",
		},
		error: {
			icon: "fa fa-exclamation-triangle",
			color: "text-danger",
		},
		question: {
			icon: "fas fa-question-circle",
			color: "text-warning",
		},
		info: {
			icon: "fas fa-info-circle",
			color: "text-info",
		},
	};

	let element = $(`
		<div title="Pesan" class="text-center">
			<span class="${states[state].icon} ${states[state].color}" aria-hidden="true" style="font-size:25px;"></span>
			<p>${message}</p>
		</div>
	`);

	$("body").append(element);

	buttons.unshift({
		text: "Ok",
		click: function () {
			$(this).dialog("close");
		},
	});

	element.dialog({
		modal: true,
		buttons: buttons,
	});
}

// function setHighlight(grid) {
// 	let filters;
// 	let gridId;

// 	gridId = $(grid).getGridParam().id;
// 	filters = $(grid).jqGrid("getGridParam", "postData").filters;

// 	if (filters) {
// 		$.each(filters, (index, filter) => {
// 			let filterText = filter.split(":")[1];

// 			$(grid)
// 				.find(`tbody tr td[aria-describedby=${gridId}_${index}]`)
// 				.highlight(filterText);
// 		});
// 	}
// }

function initSelect2(element, dropdownParent = null) {
	let options = {
		width: "100%",
		theme: "bootstrap4",
		dropdownParent: dropdownParent,
	};

	$(element)
		.select2(options)
		.on("select2:open", function (event) {
			document.querySelector(".select2-search__field").focus();
		});
}

function initAutoNumeric(element) {
	let option = {
		digitGroupSeparator: ",",
		decimalCharacter: ".",
	};

	element.classList.add("text-right");
	element.dataset.autoNumeric = true;

	new AutoNumeric(element, option);
}

function initDatepicker(element) {
	if (!element.parent().hasClass("input-group")) {
		element.wrap(`
			<div class="input-group">
			</div>
		`);
	}

	element
		.datepicker({
			dateFormat: "dd-mm-yy",
			changeYear: true,
			changeMonth: true,
			assumeNearbyYear: true,
			showOn: "button",
			beforeShow: function (element) {
				$(element).css({
					position: "relative",
				});
			},
		})
		.inputmask({
			inputFormat: "dd-mm-yyyy",
			alias: "datetime",
		})
		.focusout(function (e) {
			let val = $(this).val();
			if (val.match("[a-zA-Z]") == null) {
				if (val.length == 8) {
					$(this)
						.inputmask({
							inputFormat: "dd-mm-yyyy",
						})
						.val([val.slice(0, 6), "20", val.slice(6)].join(""));
				}
			} else {
				$(this).focus();
			}
		});

	element
		.siblings(".ui-datepicker-trigger")
		.wrap(
			`
			<div class="input-group-append">
			</div>
		`
		)
		.addClass("btn btn-primary").html(`
			<i class="fa fa-calendar-alt"></i>
		`);

	element.on("keydown", function (event) {
		if (event.keyCode === 115) {
			if (element.datepicker("widget").not(":visible")) {
				element.datepicker("show");
			}
		}
	});
}

function unformatAutoNumeric(data) {
	// need to improve
	let autoNumericElements = $(".autonumeric");

	$.each(autoNumericElements, (index, autoNumericElement) => {
		let inputs = data.filter((row) => row.name == autoNumericElement.name);

		inputs.forEach((input, index) => {
			if (input.value !== "") {
				input.value = AutoNumeric.getNumber(autoNumericElement);
			}
		});
	});

	return data;
}

function formatDate(value) {
	let date = new Date(value);

	let seconds = date.getSeconds("default");
	let minutes = date.getMinutes("default");
	let hours = date.getHours("default");
	let day = date.getDate("default");
	let month = date.getMonth("default") + 1;
	let year = date.getFullYear("default");

	return `${day.toString().padStart(2, "0")}-${month
		.toString()
		.padStart(2, "0")}-${year}`;
}

function unFormatDate(value) {
	const [day, month, year] = value.split("-");

	return `${year}-${month}-${day}`;
}

function currencyFormat(value) {
	let result = parseFloat(value).toLocaleString("en-US", {
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
	});

	result = result.replace(/\./g, "*");
	result = result.replace(/,/g, ",");
	result = result.replace(/\*/g, ".");

	return result;
}

function currencyUnformat(value) {
	let result = parseFloat(value.replaceAll(",", ""));

	return result;
}

function openMenuParents() {
	let currentMenu = $("a.nav-link.active").first();
	let parents = currentMenu.parents("li.nav-item");

	parents.each((index, parent) => {
		$(parent).addClass("menu-open");
	});
}

$.fn.formBindKeys = function () {
	return this.each(function () {
		let form = $(this);
		let validElementSelector =
			"[name]:not(:hidden, [readonly], [disabled], .disabled), button:submit";
		let validKeyCodes = [13, 38, 13, 40, 13];

		form.on("keydown", validElementSelector, function (event) {
			let inputs = form.find(validElementSelector);
			var currentInput = $(this);
			var currentIndex = inputs.index(currentInput);
			var nextInput;

			if (event.ctrlKey && event.which === 13) {
				// ctrl + enter
				form.submit();
				return;
			} else if (event.which === 38 || (event.which === 13 && event.shiftKey)) {
				// arrow up or shift + enter
				nextInput = inputs.eq(
					currentIndex > 0 ? currentIndex - 1 : currentIndex
				);
			} else if (event.which === 40 || event.which === 13) {
				// arrow down or enter
				nextInput = inputs.eq(currentIndex + 1);
			}

			if (nextInput && nextInput.length) {
				event.preventDefault();
				nextInput.focus();
			}
		});
	});
};

function setGridLastRequest(grid, lastRequest) {
	grid.setGridParam({
		lastRequest
	})
}

function getGridLastRequest(grid) {
	return grid.getGridParam()?.lastRequest
}

function abortGridLastRequest(grid) {
	getGridLastRequest(grid)?.abort()
}

function clearGridData(grid) {
	grid.jqGrid('setGridParam', {
		datatype: 'local',
		data: []
	}).trigger('reloadGrid')
}

function setErrorMessages(form, errors) {
	$.each(errors, (index, error) => {
		let indexes = index.split(".");
		let element;

		// Menangani elemen array seperti checkboxes atau select multiple
		if (indexes.length > 1) {
			element = form.find(`[name="${indexes[0]}[]"]`)[indexes[1]];  // Array index handling
		} else {
			element = form.find(`[name="${indexes[0]}"]`)[0];
		}

		if ($(element).length > 0 && !$(element).is(":hidden")) {
			$(element).addClass("is-invalid");

			// Menambahkan invalid-feedback di dalam parent yang sesuai
			$(`
         <div class="invalid-feedback">
            ${error.toLowerCase()}
          </div>
      `).appendTo($(element).parent());  // Pastikan parent yang benar

		} else {
			return showDialog('error', error);
		}
	});

	$(".is-invalid").first().focus();
}
