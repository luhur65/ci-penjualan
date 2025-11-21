$(document).ready(function () {
	grid = loadSaldoGrid($("#saldoGrid"));

	if (!canEdit) $("#edit").attr("disabled", "disabled");
	if (!canDelete) $("#delete").attr("disabled", "disabled");
	if (!canImport) $("#import").attr("disabled", "disabled");
});

function loadSaldoGrid(element) {
	return element
		.jqGrid({
			url: `${API_URL}/saldo`,
			mtype: "GET",
			styleUI: "Bootstrap4",
			iconSet: "fontAwesome",
			datatype: "json",
			styleUI: "Bootstrap4",
			sortname: "id",
			colModel: [
				{
					label: "ID",
					name: "id",
					width: "50px",
					hidden: true,
					sortable: false,
					search: false,
				},
				{
					label: "Tahun",
					name: "tahun_text",
				},
				{
					label: "Kode Perkiraan",
					name: "perkiraan_code",
				},
				{
					label: "Nominal",
					name: "nominal",
					align: "right",
					formatter: currencyFormat,
				},
				{
					label: "Status Aktif",
					name: "status_aktif",
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
				console.log($(this).clearFilterToolbar());
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

						editSaldo(selectedId);
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

						deleteSaldo(selectedId);
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
