$(document).ready(function () {
	grid = loadJurnalHeaderGrid($("#jurnalHeaderGrid"));
	jurnalDetailGrid = loadJurnalDetailGrid($("#jurnalDetailGrid"));

	if (!canEdit) $("#edit").attr("disabled", "disabled");
	if (!canDelete) $("#delete").attr("disabled", "disabled");
	if (!canImport) $("#import").attr("disabled", "disabled");

	let tahunForm = $("#tahunForm");
	let tahunElement = $('[name="tahun_id"]');

	initSelect2(tahunElement);

	setTahunOptions(tahunElement).then(() => {
		tahunElement.val(1).trigger("change.select2");
		tahunForm.trigger("submit");
	});

	tahunForm.on("submit", function (event) {
		event.preventDefault();

		let tahunId = $(this).find('[name="tahun_id"]').val();

		if (tahunId) {
			loadJurnalHeaderData(grid, tahunId);
		}
	});
});

function loadJurnalHeaderGrid(element) {
	return element
		.jqGrid({
			styleUI: "Bootstrap4",
			iconSet: "fontAwesome",
			datatype: "json",
			styleUI: "Bootstrap4",
			sortname: "no_bukti",
			colModel: [
				{
					label: "ID",
					name: "id",
					width: "50px",
					hidden: true,
				},
				{
					label: "Tanggal",
					name: "date",
					formatter: "date",
					formatoptions: {
						srcformat: "ISO8601Long",
						newformat: "d-m-Y",
					},
				},
				{
					label: "No. Bukti",
					name: "no_bukti",
				},
				{
					label: "Keterangan",
					name: "description",
				},
				{
					label: "Nominal",
					name: "nominal",
					align: "right",
					formatter: currencyFormat,
				},
			],
			prmNames: {
				sort: "sort_index",
				order: "sort_order",
				rows: "limit",
			},
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

				if (postData.sort_index == "group") {
					postData.sort_indexes = [postData.sort_index, "sub_group"];
					postData.sort_orders = [postData.sort_order, postData.sort_order];
				}

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

				delay(function () {
					loadJurnalDetailData(jurnalDetailGrid, id);
				}, 250);
			},
			loadComplete: function (data) {
				if (data.data.length === 0) {
					clearGridData(jurnalDetailGrid)
					abortGridLastRequest(jurnalDetailGrid)
				}
				
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
		.jqGrid("filterToolbar", {
			stringResult: true,
			searchOnEnter: false,
			defaultSearch: "cn",
			groupOp: "AND",
			beforeSearch: function () {
				$(this).clearGlobalSearch();

				let filters = JSON.parse($(this).getGridParam("postData").filters);

				if (filters.rules.length) {
					filters.rules.forEach((rule) => {
						$(this).jqGrid("setGridParam", {
							postData: {
								filter_group: "AND",
								filters: {
									[rule.field]: `${rule.op}:${rule.data}`,
								},
							},
						});
					});
				} else {
					delete $(this).getGridParam("postData").filters;
				}
			},
		})
		.globalSearch({
			beforeSearch: function () {
				$(this).clearFilterToolbar();
			},
		})
		.bindKeys()
		.toolbarBindKeys()
		.customBindKeys()
		.loadClearFilter()
		.customPager({
			buttons: [
				{
					id: "edit",
					innerHTML: '<i class="fa fa-pen"></i> Edit',
					class: "btn btn-success btn-sm mr-1",
					onClick: () => {
						let selectedId = $(grid).jqGrid("getGridParam", "selrow");

						if (!selectedId) {
							showDialog("info", "Please select row");

							return false;
						}

						editJurnalHeader(selectedId);
					},
				},
				{
					id: "delete",
					innerHTML: '<i class="fa fa-trash"></i> Delete',
					class: "btn btn-danger btn-sm mr-1",
					onClick: () => {
						let selectedId = $(grid).jqGrid("getGridParam", "selrow");

						if (!selectedId) {
							showDialog("info", "Please select row");

							return false;
						}

						deleteJurnalHeader(selectedId);
					},
				},
				{
					id: "deleteKhusus",
					innerHTML: '<i class="fa fa-trash"></i> Delete Khusus',
					class: "btn btn-danger btn-sm mr-1",
					onClick: () => {
						showDeleteKhususModal();
					},
				},
				{
					id: "import",
					innerHTML: '<i class="fas fa-file-import"></i> Import',
					class: "btn btn-info btn-sm mr-1",
					onClick: () => {
						showImportModal();
					},
				},
			],
		});
}

function loadJurnalHeaderData(grid, tahunId) {
	grid
		.jqGrid("setGridParam", {
			url: `${API_URL}/jurnal-header?tahun_id=${tahunId}`,
			mtype: "GET",
		})
		.trigger("reloadGrid");
}

function loadJurnalDetailGrid(element) {
	return element
		.jqGrid({
			datatype: "local",
			styleUI: "Bootstrap4",
			iconSet: "fontAwesome",
			styleUI: "Bootstrap4",
			sortname: "no_bukti",
			colModel: [
				{
					label: "ID",
					name: "id",
					width: "50px",
					hidden: true,
				},
				{
					label: "Keterangan",
					name: "description",
				},
				{
					label: "Nominal",
					name: "nominal",
					align: "right",
					formatter: currencyFormat,
				},
				{
					label: "Kode Perkiraan Debet",
					name: "perkiraan_debet_code",
				},
				{
					label: "Nama Perkiraan Debet",
					name: "perkiraan_debet_name",
				},
				{
					label: "Kode Perkiraan Kredit",
					name: "perkiraan_kredit_code",
				},
				{
					label: "Nama Perkiraan Debet",
					name: "perkiraan_kredit_name",
				},
				{
					label: "Created At",
					name: "created_at",
					formatter: "date",
					formatoptions: {
						srcformat: "ISO8601Long",
						newformat: "d-m-Y H:i:s",
					},
				},
				{
					label: "Updated At",
					name: "updated_at",
					formatter: "date",
					formatoptions: {
						srcformat: "ISO8601Long",
						newformat: "d-m-Y H:i:s",
					},
				},
			],
			prmNames: {
				sort: "sort_index",
				order: "sort_order",
				rows: "limit",
			},
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
			triggerClick: false,
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
			loadBeforeSend: function(jqXHR) {
				setGridLastRequest($(this), jqXHR)
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
		.jqGrid("filterToolbar", {
			stringResult: true,
			searchOnEnter: false,
			defaultSearch: "cn",
			groupOp: "AND",
			beforeSearch: function () {
				$(this).clearGlobalSearch();

				let filters = JSON.parse($(this).getGridParam("postData").filters);

				if (filters.rules.length) {
					filters.rules.forEach((rule) => {
						$(this).jqGrid("setGridParam", {
							postData: {
								filter_group: "AND",
								filters: {
									[rule.field]: `${rule.op}:${rule.data}`,
								},
							},
						});
					});
				} else {
					delete $(this).getGridParam("postData").filters;
				}
			},
		})
		.globalSearch({
			beforeSearch: function () {
				$(this).clearFilterToolbar();
			},
		})
		.bindKeys()
		.toolbarBindKeys()
		.customBindKeys()
		.loadClearFilter()
		.customPager();
}

function loadJurnalDetailData(jurnalDetailGrid, jurnalHeaderId) {
	abortGridLastRequest(jurnalDetailGrid)
	
	jurnalDetailGrid
		.jqGrid("setGridParam", {
			url: `${API_URL}/jurnal-detail?jurnal_header_id=${jurnalHeaderId}`,
			datatype: "json",
			mtype: "GET",
			page: 1,
		})
		.trigger("reloadGrid");
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
