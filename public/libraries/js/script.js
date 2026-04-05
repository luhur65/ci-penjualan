let activeGrid;

let sm_dekstop_1 = "50px";
let sm_dekstop_2 = "100px";
let sm_dekstop_3 = "150px";
let sm_dekstop_4 = "200px";
let md_dekstop_1 = "250px";
let md_dekstop_2 = "300px";
let md_dekstop_3 = "350px";
let md_dekstop_4 = "400px";
let lg_dekstop_1 = "450px";
let lg_dekstop_2 = "500px";
let lg_dekstop_3 = "550px";
let lg_dekstop_4 = "600px";

let sm_mobile_1 = "150px";
let sm_mobile_2 = "200px";
let sm_mobile_3 = "250px";
let sm_mobile_4 = "300px";
let md_mobile_1 = "350px";
let md_mobile_2 = "400px";
let md_mobile_3 = "450px";
let md_mobile_4 = "500px";
let lg_mobile_1 = "550px";
let lg_mobile_2 = "600px";
let lg_mobile_3 = "650px";
let lg_mobile_4 = "700px";

let sm_extendSize_1 = 50;
let sm_extendSize_2 = 100;
let sm_extendSize_3 = 150;
let sm_extendSize_4 = 200;
let md_extendSize_1 = 250;
let md_extendSize_2 = 300;
let md_extendSize_3 = 350;
let md_extendSize_4 = 400;
let lg_extendSize_1 = 450;
let lg_extendSize_2 = 500;
let lg_extendSize_3 = 550;
let lg_extendSize_4 = 600;

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
  

function setHighlight(grid) {
	let filters;
	let gridId;

	gridId = $(grid).getGridParam().id;
	filters = $(grid).jqGrid("getGridParam", "postData").filters;

	if (filters) {
		$.each(filters, (index, filter) => {
			let filterText = filter.split(":")[1];

			$(grid)
				.find(`tbody tr td[aria-describedby=${gridId}_${index}]`)
				.highlight(filterText);
		});
	}
}

// function initSelect2(element, dropdownParent = null) {
// 	let options = {
// 		width: "100%",
// 		theme: "bootstrap4",
// 		dropdownParent: dropdownParent,
// 	};

// 	$(element)
// 		.select2(options)
// 		.on("select2:open", function (event) {
// 			document.querySelector(".select2-search__field").focus();
// 		});
// }

function initSelect2(element, dropdownParent = null) {
	let options = {
		width: "100%",
		theme: "bootstrap4",
		dropdownParent: dropdownParent,
	};

	$(element)
		.select2(options)
		.on("select2:open", function () {
			setTimeout(() => {
				let searchField = document.querySelector(
					'.select2-container--open .select2-search__field'
				);
				if (searchField) searchField.focus();
			}, 0);
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

		if (indexes.length > 1) {
			element = form.find(`[name="${indexes[0]}[]"]`)[indexes[1]];
		} else {
			element = form.find(`[name="${indexes[0]}"]`)[0];
		}
		
		if ($(element).length > 0) {
			$(element).addClass("is-invalid");

			let errorElement = `<div class="invalid-feedback d-block">${error.toLowerCase()}</div>`;

			if ($(element).hasClass("select2-hidden-accessible")) {
				$(element).next(".select2-container").after(errorElement);
			} else {
				$(element).after(errorElement);
			}
		}
	});
 
	$(".is-invalid").first().focus();
}

/**
 * Set Home, End, PgUp, PgDn
 * to move grid page
 */
let topSelected = 0;
let bottomSelected = 12;
function setCustomBindKeys(grid) {
	setSidebarBindKeys();

	$(document).on("keydown", function (e) {
		if (!sidebarIsOpen && activeGrid) {
			if (
				e.keyCode == 33 ||
				e.keyCode == 34 ||
				e.keyCode == 35 ||
				e.keyCode == 36 ||
				e.keyCode == 38 ||
				e.keyCode == 40 ||
				e.keyCode == 13
			) {
				e.preventDefault();

				var gridIds = $(activeGrid).getDataIDs();
				var selectedRow = $(activeGrid).getGridParam("selrow");
				var currentPage = $(activeGrid).getGridParam("page");
				var lastPage = $(activeGrid).getGridParam("lastpage");
				var currentIndex = 0;
				var row = $(activeGrid).jqGrid("getGridParam", "postData").rows;

				for (var i = 0; i < gridIds.length; i++) {
					if (gridIds[i] == selectedRow) currentIndex = i;
				}

				if (triggerClick == false) {
					if (33 === e.keyCode) {
						if (currentPage > 1) {
							$(activeGrid)
								.jqGrid("setGridParam", {
									page: parseInt(currentPage) - 1,
								})
								.trigger("reloadGrid");

							triggerClick = true;
						}
						$(activeGrid).triggerHandler("jqGridKeyUp"),
							e.preventDefault();
					}
					if (34 === e.keyCode) {
						if (currentPage !== lastPage) {
							$(activeGrid)
								.jqGrid("setGridParam", {
									page: parseInt(currentPage) + 1,
								})
								.trigger("reloadGrid");

							triggerClick = true;
						}
						$(activeGrid).triggerHandler("jqGridKeyUp"),
							e.preventDefault();
					}
					if (35 === e.keyCode) {
						if (currentPage !== lastPage) {
							$(activeGrid)
								.jqGrid("setGridParam", {
									page: lastPage,
								})
								.trigger("reloadGrid");
							if (e.ctrlKey) {
								if (
									$(activeGrid).jqGrid(
										"getGridParam",
										"selrow"
									) !==
									$("#customer")
										.find(">tbody>tr.jqgrow")
										.filter(":last")
										.attr("id")
								) {
									$(activeGrid)
										.jqGrid(
											"setSelection",
											$(activeGrid)
												.find(">tbody>tr.jqgrow")
												.filter(":last")
												.attr("id")
										)
										.trigger("reloadGrid");
								}
							}

							triggerClick = true;
						}
						if (e.ctrlKey) {
							if (
								$(activeGrid).jqGrid(
									"getGridParam",
									"selrow"
								) !==
								$("#customer")
									.find(">tbody>tr.jqgrow")
									.filter(":last")
									.attr("id")
							) {
								$(activeGrid)
									.jqGrid(
										"setSelection",
										$(activeGrid)
											.find(">tbody>tr.jqgrow")
											.filter(":last")
											.attr("id")
									)
									.trigger("reloadGrid");
							}
						}
						$(activeGrid).triggerHandler("jqGridKeyUp"),
							e.preventDefault();
					}
					if (36 === e.keyCode) {
						if (currentPage > 1) {
							if (e.ctrlKey) {
								if (
									$(activeGrid).jqGrid(
										"getGridParam",
										"selrow"
									) !==
									$("#customer")
										.find(">tbody>tr.jqgrow")
										.filter(":first")
										.attr("id")
								) {
									$(activeGrid).jqGrid(
										"setSelection",
										$(activeGrid)
											.find(">tbody>tr.jqgrow")
											.filter(":first")
											.attr("id")
									);
								}
							}
							$(activeGrid)
								.jqGrid("setGridParam", {
									page: 1,
								})
								.trigger("reloadGrid");

							triggerClick = true;
						}
						$(activeGrid).triggerHandler("jqGridKeyUp"),
							e.preventDefault();
					}
					if (38 === e.keyCode) {
						if (currentIndex - 1 >= 0) {
							$(activeGrid)
								.resetSelection()
								.setSelection(gridIds[currentIndex - 1]);

							var selInRow = $(activeGrid).getGridParam("selrow");

							indexRowSelect = $(activeGrid).jqGrid(
								"getInd",
								selInRow
							);

							var currentRowHeight =
								$(activeGrid).getGridParam("rowHeight") || 26;

							var currentScrollTop = $(activeGrid)
								.closest(".ui-jqgrid-bdiv")
								.scrollTop();
							var recordScrollUp =
								$(activeGrid).getGridParam("reccount") - 10;
							if (indexRowSelect < recordScrollUp) {
								$(activeGrid)
									.closest(".ui-jqgrid-bdiv")
									.scrollTop(
										currentScrollTop - currentRowHeight - 2
									);
							}
						}
					}
					if (40 === e.keyCode) {
						if (currentIndex + 1 < gridIds.length) {
							$(activeGrid)
								.resetSelection()
								.setSelection(gridIds[currentIndex + 1]);
							var currentRowHeight =
								$(activeGrid).getGridParam("rowHeight") || 26;

							var selInRow = $(activeGrid).getGridParam("selrow");
							indexRowSelect = $(activeGrid).jqGrid(
								"getInd",
								selInRow
							);

							var currentScrollTop = $(activeGrid)
								.closest(".ui-jqgrid-bdiv")
								.scrollTop();

							var recordsAll =
								$(activeGrid).getGridParam("records");
							if (indexRowSelect > 12) {
								$(activeGrid)
									.closest(".ui-jqgrid-bdiv")
									.scrollTop(
										currentScrollTop + currentRowHeight + 2
									);
							}
						}
					}
					if (13 === e.keyCode) {
						let rowId = $(activeGrid).getGridParam("selrow");
						let ondblClickRowHandler = $(activeGrid).jqGrid(
							"getGridParam",
							"ondblClickRow"
						);

						if (ondblClickRowHandler) {
							ondblClickRowHandler.call($(activeGrid)[0], rowId);
						}
					}
				}

				$(".ui-jqgrid-bdiv").find("tbody").animate({
					scrollTop: 200,
				});
				// $(".table-success").position().top > 300;
			}
		}
	});
}


function setSidebarBindKeys() {
	$(document).on("keydown", (event) => {
		if (event.keyCode === 77 && event.altKey) {
			event.preventDefault();

			$("#sidebarButton").click();
		}

		if (sidebarIsOpen) {
			let allowedKeyCodes = [37, 38, 39, 40];

			if (allowedKeyCodes.includes(event.keyCode)) {
				event.preventDefault();

				$("#search").val("");

				if ($(".nav-link.active, .nav-link.hover").length <= 0) {
					$(".main-sidebar nav .nav-link").first().addClass("hover");
				}

				switch (event.keyCode) {
					case 37:
						setUpOneLevelMenu();

						break;
					case 38:
						setPreviousMenuHover();

						break;
					case 39:
						setDownOneLevelMenu();

						break;
					case 40:
						setNextMenuHover();

						break;
					default:
						break;
				}
			} else if (event.keyCode === 13) {
				let hoveredElement = $(".nav-link.hover");

				if (hoveredElement.length > 0) {
					if (hoveredElement.siblings("ul").length > 0) {
						setDownOneLevelMenu();
					} else {
						hoveredElement[0].click();
					}
				}
			}
		}
	});
}

function setNextMenuHover() {
	let currentElement = $(".nav-link.hover").first();

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.selected-link");
	}

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.active");
	}

	let nextElement = currentElement
		.parent(".nav-item")
		.next()
		.find(".nav-link")
		.first();

	if (nextElement.length > 0) {
		currentElement.removeClass("selected-link hover");
		nextElement.addClass("hover");
	}
}

function setPreviousMenuHover() {
	let currentElement = $(".nav-link.hover").first();

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.selected-link");
	}

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.active");
	}

	let nextElement = currentElement
		.parent(".nav-item")
		.prev()
		.find(".nav-link")
		.first();

	if (nextElement.length > 0) {
		currentElement.removeClass("selected-link hover");
		nextElement.addClass("hover");
	}
}

function setUpOneLevelMenu() {
	let currentElement = $(".nav-link.hover").first();

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.selected-link");
	}

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.active");
	}

	let upOneLevelElement = currentElement.parents().eq(2);

	if (upOneLevelElement.length > 0) {
		currentElement.removeClass("selected-link hover");
		upOneLevelElement.removeClass("menu-is-opening menu-open");
		upOneLevelElement.find(".nav-link").first().addClass("hover");
	}
}

function setDownOneLevelMenu() {
	let currentElement = $(".nav-link.hover").first();

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.selected-link");
	}

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.active");
	}

	let downOneLevelElement = currentElement
		.siblings("ul")
		.css({
			display: "",
		})
		.find(".nav-link")
		.first();

	if (downOneLevelElement.length > 0) {
		currentElement.removeClass("selected-link hover");
		currentElement.parent(".nav-item").addClass("menu-open");
		downOneLevelElement.addClass("hover");
	}
}

function fillSearchMenuInput() {
	let currentElement = $(".nav-link.hover").first();

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.selected-link");
	}

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.active");
	}

	$("#search").val(currentElement.attr("id"));
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

/**
 * FUNGSI PINTASAN KEYBOARD (ACCESSIBILITY)
 * Menangani kombinasi ALT+A, ALT+E, ALT+D dengan proteksi Hak Akses
 */
// function setupKeyboardShortcuts() {
// 	$(window).off('keydown.globalShortcut').on('keydown.globalShortcut', function (e) {


// 		// Pastikan pengguna tidak sedang mengetik di dalam input/textarea/select
// 		// agar ALT+A tidak terpicu saat mereka sedang mengisi form!
// 		let isInputActive = $(e.target).is('input, textarea, select');
// 		if (isInputActive) return;

// 		// Kombinasi ALT + A (ADD)
// 		if (e.altKey && e.key.toLowerCase() === 'a') {
// 			e.preventDefault(); // Cegah browser melakukan aksi default

// 			// Cek permission dari object accessRights global Anda
// 			if (typeof accessRights !== 'undefined' && accessRights.add) {
// 				console.log('Shortcut Terpicu: ALT + A (Add)');
// 				$('#add').click(); // Simulasikan klik tombol Add
// 			} else {
// 				if (typeof showDialog === "function") showDialog('error', 'Anda tidak memiliki akses untuk menambah data.');
// 			}
// 		}

// 		// Kombinasi ALT + E (EDIT)
// 		if (e.altKey && e.key.toLowerCase() === 'e') {
// 			e.preventDefault();

// 			if (typeof accessRights !== 'undefined' && accessRights.edit) {
// 				console.log('Shortcut Terpicu: ALT + E (Edit)');
// 				$('#edit').click(); // Simulasikan klik tombol Edit
// 			} else {
// 				if (typeof showDialog === "function") showDialog('error', 'Anda tidak memiliki akses untuk mengubah data.');
// 			}
// 		}

// 		// Kombinasi ALT + D (DELETE)
// 		if (e.altKey && e.key.toLowerCase() === 'd') {
// 			e.preventDefault();

// 			if (typeof accessRights !== 'undefined' && accessRights.delete) {
// 				console.log('Shortcut Terpicu: ALT + D (Delete)');
// 				$('#delete').click(); // Simulasikan klik tombol Delete
// 			} else {
// 				if (typeof showDialog === "function") showDialog('error', 'Anda tidak memiliki akses untuk menghapus data.');
// 			}
// 		}
// 	});
// }

function setCustomBindKeysLazy(grid) {
	setSidebarBindKeys();

	$(document).off("keydown.lazyBind").on("keydown.lazyBind", function (e) {
		if (!sidebarIsOpen && activeGrid) {
			let allowedKeys = [33, 34, 35, 36, 38, 40, 13];
			if (!allowedKeys.includes(e.keyCode)) return;

			let loader = $(activeGrid).data('lazyLoader');
			if (!loader) return;

			e.preventDefault();

			let gridIds = $(activeGrid).getDataIDs();
			let selectedRow = $(activeGrid).getGridParam("selrow");
			let currentIndex = gridIds.indexOf(selectedRow);

			let postData = $(activeGrid).jqGrid("getGridParam", "postData");
			let rowsPerPage = loader.rowsPerPage;
			let currentPage = loader.currentViewPage;
			let totalPages = loader.totalPages;

			if (loader.loading) return; // Mencegah spam keyboard yang membuat request numpuk

			// Helper function to focus row after jump
			let focusRow = (rowType) => {
				setTimeout(() => {
					let newIds = $(activeGrid).getDataIDs();
					if (newIds.length > 0) {
						let targetId = rowType === 'first' ? newIds[0] : newIds[newIds.length - 1];
						$(activeGrid).resetSelection().setSelection(targetId);

						let bDiv = $(activeGrid).closest(".ui-jqgrid-bdiv");
						if (rowType === 'first') {
							bDiv.scrollTop(0);
						} else {
							bDiv.scrollTop(bDiv[0].scrollHeight);
						}
					}
				}, 100);
			};

			// Page Up
			if (33 === e.keyCode) {
				if (currentPage > 1) {
					loader.loadGridData(postData, currentPage - 1, rowsPerPage, 'up', 'jump', () => focusRow('first'));
				}
				$(activeGrid).triggerHandler("jqGridKeyUp");
			}
			// Page Down
			if (34 === e.keyCode) {
				if (currentPage < totalPages) {
					loader.loadGridData(postData, currentPage + 1, rowsPerPage, 'down', 'jump', () => focusRow('first'));
				}
				$(activeGrid).triggerHandler("jqGridKeyUp");
			}
			// End
			if (35 === e.keyCode) {
				if (currentPage !== totalPages) {
					loader.loadGridData(postData, totalPages, rowsPerPage, 'down', 'jump', () => focusRow('last'));
				} else {
					focusRow('last');
				}
				$(activeGrid).triggerHandler("jqGridKeyUp");
			}
			// Home
			if (36 === e.keyCode) {
				if (currentPage > 1) {
					loader.loadGridData(postData, 1, rowsPerPage, 'down', 'jump', () => focusRow('first'));
				} else {
					focusRow('first');
				}
				$(activeGrid).triggerHandler("jqGridKeyUp");
			}
			// Up
			if (38 === e.keyCode) {
				if (currentIndex - 1 >= 0) {
					$(activeGrid).resetSelection().setSelection(gridIds[currentIndex - 1]);

					var selInRow = $(activeGrid).getGridParam("selrow");
					let indexRowSelect = $(activeGrid).jqGrid("getInd", selInRow);
					var currentRowHeight = $(activeGrid).getGridParam("rowHeight") || 26;
					var currentScrollTop = $(activeGrid).closest(".ui-jqgrid-bdiv").scrollTop();

					if (indexRowSelect < loader.totalRecord - 10) {
						$(activeGrid).closest(".ui-jqgrid-bdiv").scrollTop(currentScrollTop - currentRowHeight - 2);
					}
				} else {
					if (currentPage > 1) {
						loader.loadGridData(postData, currentPage - 1, rowsPerPage, 'up', 'jump', () => focusRow('last'));
					}
				}
			}
			// Down
			if (40 === e.keyCode) {
				if (currentIndex + 1 < gridIds.length) {
					$(activeGrid).resetSelection().setSelection(gridIds[currentIndex + 1]);

					var currentRowHeight = $(activeGrid).getGridParam("rowHeight") || 26;
					var selInRow = $(activeGrid).getGridParam("selrow");
					let indexRowSelect = $(activeGrid).jqGrid("getInd", selInRow);
					var currentScrollTop = $(activeGrid).closest(".ui-jqgrid-bdiv").scrollTop();

					if (indexRowSelect > 12) {
						$(activeGrid).closest(".ui-jqgrid-bdiv").scrollTop(currentScrollTop + currentRowHeight + 2);
					}
				} else {
					if (currentPage < totalPages) {
						loader.loadGridData(postData, currentPage + 1, rowsPerPage, 'down', 'jump', () => focusRow('first'));
					}
				}
			}
			// Enter
			if (13 === e.keyCode) {
				let rowId = $(activeGrid).getGridParam("selrow");
				let ondblClickRowHandler = $(activeGrid).jqGrid("getGridParam", "ondblClickRow");

				if (ondblClickRowHandler) {
					ondblClickRowHandler.call($(activeGrid)[0], rowId);
				}
			}
		}
	});
}

function setupKeyboardShortcuts() {

	const shortcutMap = {};
	$('[data-shortcut]').each(function () {
		const key = $(this).data('shortcut').toLowerCase();
		const right = $(this).data('right'); // opsional: 'add' | 'edit' | 'delete'
		shortcutMap[key] = {
			btn: $(this),
			right: right || null
		};
	});

	$(window).off('keydown.globalShortcut').on('keydown.globalShortcut', function (e) {

		if ($(e.target).is('input, textarea, select')) return;
		if (!e.altKey) return;

		const key = e.key.toLowerCase();
		const entry = shortcutMap[key];
		if (!entry) return;

		e.preventDefault();

		// Cek permission jika ada
		if (entry.right && typeof accessRights !== 'undefined') {
			if (!accessRights[entry.right]) {
				if (typeof showDialog === 'function') {
					showDialog('error', 'Anda tidak memiliki akses untuk aksi ini.');
				}
				return;
			}
		}

		entry.btn.click();
	});
}
