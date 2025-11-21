$(document).ready(function () {
	let startDateElement = $('[name="start_date"]')
	let endDateElement = $('[name="end_date"]')
	let jurnalGroupElement = $('[name="jurnal_group_id"]');
	let cabangElement = $('[name="cabang_id"]');
	let form = $("#laporanRekapJurnalRange");

	initDatepicker(startDateElement);
	initDatepicker(endDateElement);
	initSelect2(jurnalGroupElement);
	initSelect2(cabangElement);

	setJurnalGroupOptions(jurnalGroupElement);
	setCabangOptions(cabangElement);

	$("#btnExport").on("click", function () {
		let element = $(this)
		let startDate = startDateElement.val()
		let endDate = endDateElement.val()
		let jurnalGroupId = jurnalGroupElement.val()
		let cabangId = cabangElement.val()
		let xhr = new XMLHttpRequest();

		element.attr('disabled', 'disabled')

		$(form).find(".is-invalid").removeClass("is-invalid");
		$(form).find(".invalid-feedback").remove();

		xhr.open(
			"GET",
			`${API_URL}/laporan-rekap-jurnal/export?start_date=${startDate}&end_date=${endDate}&jurnal_group_id=${jurnalGroupId}&cabang_id=${cabangId}`,
			true
		);
		xhr.responseType = "arraybuffer";

		xhr.onload = function (e) {
			if (this.status === 200) {
				if (this.response !== undefined) {
					let blob = new Blob([this.response], {
						type: "application/vnd.ms-excel"
					})
					let link = document.createElement('a')

					link.href = window.URL.createObjectURL(blob)
					link.download = `Laporan Rekap Jurnal.xlsx`
					link.click()
				}
			}

			if ([404, 422, 500].includes(this.status)) {
				showDialog('error', this.statusText)
			}

			element.removeAttr('disabled')
		}

		xhr.onerror = () => {
			submitButton.removeAttr('disabled')

			showDialog('error', 'Something wrong.')
		}

		xhr.send()
	});

	$("#btnReload").on("click", function () {
		let rangeFilters = {
			start_date: unFormatDate(form.find(`[name="start_date"]`).val()),
			end_date: unFormatDate(form.find(`[name="end_date"]`).val()),
			jurnal_group_id: form.find(`[name="jurnal_group_id"]`).val(),
			cabang_id: form.find(`[name="cabang_id"]`).val(),
		};

		$(form).find(".is-invalid").removeClass("is-invalid");
		$(form).find(".invalid-feedback").remove();

		$.jgrid.gridUnload("#laporanRekapJurnalGrid");

		getHeader(rangeFilters).then((gridHeaders) => {
			let colModel = gridHeaders.map((row) => {
				let colOptions = {
					name: row,
					index: row,
					sortable: false,
				};

				if (row.toLowerCase() == "date") {
					colOptions.formatter = "date";
					colOptions.formatoptions = {
						srcformat: "ISO8601Long",
						newformat: "d-m-Y",
					};
				} else {
					colOptions.formatter = currencyFormat;
					colOptions.align = "right";
				}

				return colOptions;
			});

			loadGrid($("#laporanRekapJurnalGrid"), colModel, rangeFilters);
		});
	});
});

function getHeader(rangeFilters) {
	return new Promise((resolve, reject) => {
		let form = $("#laporanRekapJurnalRange");

		$.ajax({
			url: `${API_URL}/laporan-rekap-jurnal/header`,
			method: "GET",
			dataType: "JSON",
			data: rangeFilters,
			success: (response) => {
				$(form).find(".is-invalid").removeClass("is-invalid");
				$(form).find(".invalid-feedback").remove();

				resolve(response.data);
			},
			error: (error) => {
				$(form).find(".is-invalid").removeClass("is-invalid");
				$(form).find(".invalid-feedback").remove();
				const { status, responseJSON } = error;

				if (status === 422) {
					setErrorMessages(form, responseJSON.errors);
				} else {
					showDialog("error", error.responseJSON.message);
				}
			},
		});
	});
}

function loadGrid(element, colModel, rangeFilters) {
	return element
		.jqGrid({
			url: `${API_URL}/laporan-rekap-jurnal`,
			datatype: "json",
			styleUI: "Bootstrap4",
			iconSet: "fontAwesome",
			styleUI: "Bootstrap4",
			colModel: colModel,
			postData: rangeFilters,
			jsonReader: {
				root: "data",
				total: "attributes.total_pages",
				records: "attributes.total_rows",
			},
			autowidth: true,
			shrinkToFit: false,
			height: 350,
			page: 1,
			rownumbers: true,
			rownumWidth: 45,
			rowNum: 10,
			rowList: [10, 20, 50, 0],
			toolbar: [true, "top"],
			viewrecords: true,
			selectedIndex: 0,
			triggerClick: true,
			serializeGridData: function (postData) {
				postData.sort_indexes = [postData.sort_index];
				postData.sort_orders = [postData.sort_order];

				delete postData.sort_index;
				delete postData.sort_order;

				return postData;
			},
			onSelectRow: function (id) {
				activeGrid = this;

				let limit = $(this).jqGrid("getGridParam", "postData").limit;
				let page = $(this).jqGrid("getGridParam", "page");
				let selectedIndex = $(this).jqGrid("getCell", id, "rn") - 1;

				if (selectedIndex >= limit)
					selectedIndex = selectedIndex - limit * (page - 1);

				$(this).jqGrid("setGridParam", {
					selectedIndex,
				});
			},
			loadComplete: function () {
				changeJqGridRowListText();

				$(this).parents(".ui-jqgrid").find("input").attr("autocomplete", "off");

				let selectedIndex = $(this).jqGrid("getGridParam").selectedIndex;

				if (selectedIndex > $(this).getDataIDs().length - 1) {
					selectedIndex = $(this).getDataIDs().length - 1;
				}

				if ($(this).jqGrid("getGridParam").triggerClick) {
					$(this)
						.find(`tr[id="${$(this).getDataIDs()[selectedIndex]}"]`)
						.click();

					$(this).jqGrid("setGridParam", {
						triggerClick: false,
					});
				} else {
					$(this).setSelection($(this).getDataIDs()[selectedIndex]);
				}

				setHighlight(this);
			},
		})
		.jqGrid("setLabel", "rn", "No.")
		.bindKeys()
		.toolbarBindKeys()
		.customBindKeys()
		.loadClearFilter()
		.customPager();
}

function setErrorMessages(form, errors) {
	let element;

	$.each(errors, (index, value) => {
		element = $(form).find(`[name=${index}]`);

		if (typeof element !== "undefined" && element.length > 0) {
			element.addClass("is-invalid");

			let elementSibling = element.siblings().last();

			if (elementSibling.length > 0) {
				elementSibling.after(`
					<div class="invalid-feedback">
					${value}
					</div>
			  `);
			} else {
				element.after(`
					<div class="invalid-feedback">
					${value}
					</div>
			  `);
			}
		}
	});

	$(form).find(".is-invalid").first().focus();
}

function setJurnalGroupOptions(element) {
	return new Promise((resolve, reject) => {
		element.empty();
		element
			.append(new Option("-- Pilih Jurnal Group --", "", false, true))
			.trigger("change");

		$.ajax({
			url: `${API_URL}/jurnal-group`,
			method: "GET",
			dataType: "JSON",
			data: {
				limit: 0,
			},
			success: (response) => {
				response.data.forEach((jurnalGroup) => {
					let option = new Option(jurnalGroup.description, jurnalGroup.id);

					element.append(option).trigger("change");
				});

				resolve();
			},
		});
	});
}

function setCabangOptions(element) {
	return new Promise((resolve, reject) => {
		element.empty();
		element
			.append(new Option("-- Pilih Cabang --", "", false, true))
			.trigger("change");

		$.ajax({
			url: `${API_URL}/cabang`,
			method: "GET",
			dataType: "JSON",
			data: {
				limit: 0,
			},
			success: (response) => {
				response.data.forEach((cabang) => {
					let option = new Option(`(${cabang.code}) ${cabang.name}`, cabang.id);

					element.append(option).trigger("change");
				});

				resolve();
			},
		});
	});
}
