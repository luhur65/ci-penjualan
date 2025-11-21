$(document).ready(function () {
	let tahunElement = $('[name="tahun_id"]');
	let jurnalGroupElement = $('[name="jurnal_group_id"]');
	let cabangElement = $('[name="cabang_id"]');
	let form = $("#laporanJurnalRange");

	initSelect2(tahunElement);
	initSelect2(jurnalGroupElement);
	initSelect2(cabangElement);

	setTahunOptions(tahunElement);
	setJurnalGroupOptions(jurnalGroupElement);
	setCabangOptions(cabangElement);

	grid = loadGrid($("#laporanJurnalGrid"));

	$("#btnExport").on("click", function () {
		let element = $(this)
		let tahunId = form.find(`[name="tahun_id"]`).val()
		let jurnalGroupId = form.find(`[name="jurnal_group_id"]`).val()
		let cabangId = form.find(`[name="cabang_id"]`).val()
		let xhr = new XMLHttpRequest();

		element.attr('disabled', 'disabled')

		$(form).find(".is-invalid").removeClass("is-invalid");
		$(form).find(".invalid-feedback").remove();

		xhr.open(
			"GET",
			`${API_URL}/laporan-jurnal/export?tahun_id=${tahunId}&jurnal_group_id=${jurnalGroupId}&cabang_id=${cabangId}`,
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
					link.download = `Laporan Jurnal.xlsx`
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
			tahun_id: form.find(`[name="tahun_id"]`).val(),
			jurnal_group_id: form.find(`[name="jurnal_group_id"]`).val(),
			cabang_id: form.find(`[name="cabang_id"]`).val(),
			proses: "reload",
		};

		loadDataHeader("laporan-jurnal", rangeFilters);
	});
});

function loadDataHeader(url, additional = null) {
	let data = {
	  tgldari: $("#tgldariheader").val(),
	  tglsampai: $("#tglsampaiheader").val(),
	  proses: "reload",
	};
  
	data = {
	  ...data,
	  ...additional,
	};
   
	  $("#laporanJurnalGrid").jqGrid("setGridParam", {
		  url: `${API_URL}/${url}`,
		  datatype: "json",
		  postData: data,
		  page: 1,
	  }).trigger('reloadGrid');
  
	  // Clear validation errors
	  $('.is-invalid').removeClass('is-invalid');
	  $('.invalid-feedback').remove();
  }
  

function loadGrid(element) {
	return element
		.jqGrid({
			datatype: "local",
			styleUI: "Bootstrap4",
			iconSet: "fontAwesome",
			styleUI: "Bootstrap4",
			colModel: [
				{
					label: "ID",
					name: "id",
					width: 80,
				},
				{
					label: "Date",
					name: "date",
					width: 100,
					formatter: "date",
					formatoptions: {
						srcformat: "ISO8601Long",
						newformat: "d-m-Y",
					},
				},
				{
					label: "No. Bukti",
					name: "no_bukti",
					width: 100,
				},
				{
					label: "Description",
					name: "description",
					width: 200,
				},
				{
					label: "Nominal",
					name: "nominal",
					align: "right",
					width: 150,
					formatter: currencyFormat,
				},
				{
					label: "Kode Perkiraan Debet",
					name: "perkiraan_debet_code",
					width: 150,
				},
				{
					label: "Nama Perkiraan Debet",
					name: "perkiraan_debet_name",
					width: 200,
				},
				{
					label: "Kode Perkiraan Kredit",
					name: "perkiraan_kredit_code",
					width: 150,
				},
				{
					label: "Nama Perkiraan Kredit",
					name: "perkiraan_kredit_name",
					width: 200,
				},
			],
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
			footerrow: true,
			userDataOnFooter: true,
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
			loadComplete: function (data) {
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

				if (data.attributes?.total_nominal) {
					$(this).jqGrid(
						"footerData",
						"set",
						{
							id: "Total:",
							nominal: data.attributes.total_nominal,
						},
						true
					);
				}
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

function setTahunOptions(element) {
	return new Promise((resolve, reject) => {
		element.empty();
		element
			.append(new Option("-- Pilih Tahun --", "", false, true))
			.trigger("change");

		$.ajax({
			url: `${API_URL}/tahun`,
			method: "GET",
			dataType: "JSON",
			data: {
				limit: 0,
			},
			success: (response) => {
				response.data.forEach((tahun) => {
					let option = new Option(tahun.tahun, tahun.id);

					element.append(option).trigger("change");
				});

				resolve();
			},
		});
	});
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
