$(document).ready(function () {
	let tahunElement = $('[name="tahun_id"]');
	let form = $("#neracaLajurRange");

	initSelect2(tahunElement);

	setTahunOptions(tahunElement);

	grid = loadGrid($("#neracaLajurGrid"));

	$("#btnExport").on("click", function () {
		let element = $(this);
		let tahunId = form.find(`[name="tahun_id"]`).val();
		let xhr = new XMLHttpRequest();

		element.attr("disabled", "disabled");

		$(form).find(".is-invalid").removeClass("is-invalid");
		$(form).find(".invalid-feedback").remove();

		xhr.open("GET", `${API_URL}/neraca-lajur/export?tahun_id=${tahunId}`, true);
		xhr.responseType = "arraybuffer";

		xhr.onload = function (e) {
			if (this.status === 200) {
				if (this.response !== undefined) {
					let blob = new Blob([this.response], {
						type: "application/vnd.ms-excel",
					});
					let link = document.createElement("a");

					link.href = window.URL.createObjectURL(blob);
					link.download = `Laporan Neraca Lajur.xlsx`;
					link.click();
				}
			}

			if ([404, 422, 500].includes(this.status)) {
				showDialog("error", this.statusText);
			}

			element.removeAttr("disabled");
		};

		xhr.onerror = () => {
			submitButton.removeAttr("disabled");

			showDialog("error", "Something wrong.");
		};

		xhr.send();
	});

	$("#btnReload").on("click", function () {
		let rangeFilters = {
			tahun_id: form.find(`[name="tahun_id"]`).val(),
		};

		$(form).find(".is-invalid").removeClass("is-invalid");
		$(form).find(".invalid-feedback").remove();

		grid
			.jqGrid("setGridParam", {
				url: `${API_URL}/neraca-lajur`,
				datatype: "json",
				postData: rangeFilters,
				loadError: function (error) {
					const { status, responseJSON } = error;

					if (status === 422) {
						setErrorMessages(form, responseJSON.errors);
					} else {
						showDialog("error", error.responseJSON.message);
					}
				},
			})
			.trigger("reloadGrid");
	});
});

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
					label: "Kode Perkiraan",
					name: "kode_perkiraan",
					width: 150,
				},
				{
					label: "Nama Perkiraan",
					name: "nama_perkiraan",
					width: 150,
				},
				{
					label: "Saldo Awal",
					name: "saldo_awal",
					width: 150,
					align: "right",
					formatter: currencyFormat,
				},
				{
					label: "Mutasi Debet",
					name: "mutasi_debet",
					width: 200,
					align: "right",
					formatter: currencyFormat,
				},
				{
					label: "Mutasi Kredit",
					name: "mutasi_kredit",
					width: 200,
					align: "right",
					formatter: currencyFormat,
				},
				{
					label: "Saldo Akhir",
					name: "saldo_akhir",
					width: 200,
					align: "right",
					formatter: currencyFormat,
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

				if (data.data) {
					$(this).jqGrid(
						"footerData",
						"set",
						{
							id: "Total:",
							saldo_awal: data.data[0].total_saldo_awal,
							mutasi_debet: data.data[0].total_mutasi_debet,
							mutasi_kredit: data.data[0].total_mutasi_kredit,
							saldo_akhir: data.data[0].total_saldo_akhir,
							laba_rugi_debet: data.data[0].total_laba_rugi_debet,
							laba_rugi_kredit: data.data[0].total_laba_rugi_kredit,
							neraca_debet: data.data[0].total_neraca_debet,
							neraca_kredit: data.data[0].total_neraca_kredit,
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
