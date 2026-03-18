$(document).ready(function () {
 
	grid = loadFakturPenjualanHeader($("#fakturpenjualanheaderGrid"));
    
	fakturPenjualanDetailGrid = loadFakturPenjualanDetailGrid($("#fakturpenjualandetailGrid"));

	if (!canAdd) $("#add").attr("disabled", "disabled");
	if (!canEdit) $("#edit").attr("disabled", "disabled");
	if (!canDelete) $("#delete").attr("disabled", "disabled");

	
});

function loadFakturPenjualanHeader(element) {
	return element
		.jqGrid({
			styleUI: "Bootstrap4",
			url: `${API_URL}/faktur-penjualan-header`,
			mtype: "GET",
			iconSet: "fontAwesome",
			datatype: "json",
			styleUI: "Bootstrap4",
			sortname: "nobukti",
			colModel: [
				{
					label: "ID",
					name: "id",
					width: "50px",
					hidden: true,
				},
				{
					label: "No. Bukti",
					name: "nobukti",
				},
				{
					label: "Tanggal",
					name: "invoicedate",
					formatter: "date",
					formatoptions: {
						srcformat: "ISO8601Long",
						newformat: "d-m-Y",
					},
				},
				{
					label: "Customer",
					name: "customer_name",
				},
				{
					label: "No PO",
					name: "nopo",
				},
				{
					label: "Ship To",
					name: "shipto",
				},
				{
					label: "Rate",
					name: "rate",
				},
				{
					label: "FOB",
					name: "fob",
				},
				{
					label: "Terms",
					name: "terms",
				},
				{
					label: "Fiscal Rate",
					name: "fiscalrate",
				},
				{
					label: "Ship Date",
					name: "shipdate",
					formatter: "date",
					formatoptions: {
						srcformat: "ISO8601Long",
						newformat: "d-m-Y",
					},
				},
                {
					label: "Ship Via",
					name: "shipvia",
				},
                {
					label: "Receivable Account",
					name: "receivableaccount",
				},
                {
					label: "Sales",
					name: "sales_name",
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
					loadFakturPenjualanDetailData(fakturPenjualanDetailGrid, id);
				}, 250);
			},
			loadComplete: function (data) {
				if (data.data.length === 0) {
					clearGridData(fakturPenjualanDetailGrid)
					abortGridLastRequest(fakturPenjualanDetailGrid)
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
					id: "add",
					innerHTML: '<i class="fa fa-plus"></i> Add',
					class: "btn btn-primary btn-sm mr-1",
					onClick: () => {
						addFakturPenjualan();
					},
				},
				{
					id: "edit",
					innerHTML: '<i class="fa fa-pen"></i> Edit',
					class: "btn btn-success btn-sm mr-1",
					onClick: () => {
						let selectedId = $(grid).jqGrid("getGridParam", "selrow");

						if (!selectedId) {
							showDialog("W", "Please select row");

							return false;
						}

						editFakturPenjualan(selectedId);
					},
				},
				{
					id: "delete",
					innerHTML: '<i class="fa fa-trash"></i> Delete',
					class: "btn btn-danger btn-sm mr-1",
					onClick: () => {
						let selectedId = $(grid).jqGrid("getGridParam", "selrow");

						if (!selectedId) {
							showDialog("W", "Please select row");

							return false;
						}

						deletefakturPenjualan(selectedId);
					},
				},
			],
		});
}

function loadJurnalHeaderData(grid) {

	grid
		.jqGrid("setGridParam", {
			url: `${API_URL}/faktur-penjualan-header`,
			mtype: "GET",
		})
		.trigger("reloadGrid");
}

function loadFakturPenjualanDetailGrid(element) {
	return element
		.jqGrid({
			datatype: "local",
			styleUI: "Bootstrap4",
			iconSet: "fontAwesome",
			styleUI: "Bootstrap4",
			sortname: "item_id",
			colModel: [
				{
					label: "ID",
					name: "id",
					width: "50px",
					hidden: true,
				},
				{
					label: "Item",
					name: "item_name",
				},
				{
					label: "Item Description",
					name: "itemdescription",
				},
				{
					label: "Qty",
					name: "qty",
				},
				{
					label: "Harga Satuan",
					name: "hargasatuan",
					align: "right",
					formatter: currencyFormat,
					summaryType: "sum",
					summaryTpl: '<span class="footer-total">Total</span>',
				},
				{
					label: "Amount",
					name: "amount",
					align: "right",
					formatter: currencyFormat,
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
			footerrow: true,
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

				// ordersPostData = $(this).jqGrid('getGridParam', 'postData')
                // // console.log(data);
    
                // sum = $(this).jqGrid("getCol", "amount", true, "sum")
				// console.log(sum)
    
                // $(this).jqGrid('footerData', 'set', {
                //     amount: sum,
                // }, true)
				// Mendapatkan data saat ini dari grid
				const gridData = $(this).jqGrid("getRowData");

				// Variabel untuk menyimpan total amount
				let totalAmount = 0;

				// Mengiterasi setiap baris data
				for (let i = 0; i < gridData.length; i++) {
				const rowData = gridData[i];
				
				// Mendapatkan nilai amount dari baris data
				const amount = parseFloat(rowData.amount.replace(/[^0-9.-]+/g,""));

				// Menambahkan nilai amount ke totalAmount
				totalAmount += amount;
				}
				
				if (data.attributes) {
	
				$(this).jqGrid("footerData", "set", {
				item_name: 'Total',
				amount: data.attributes.total,
				});
			}
			
	

				
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

function loadFakturPenjualanDetailData(fakturPenjualanDetailGrid, jurnalHeaderId) {
	abortGridLastRequest(fakturPenjualanDetailGrid)
	
	fakturPenjualanDetailGrid
		.jqGrid("setGridParam", {
			url: `${API_URL}/faktur-penjualan-detail?fakturpenjualan_id=${jurnalHeaderId}`,
			datatype: "json",
			mtype: "GET",
			page: 1,
		})
		.trigger("reloadGrid");
}
// function loadFakturPenjualanDetailData(fakturPenjualanDetailGrid, jurnalHeaderId) {
// 	abortGridLastRequest(fakturPenjualanDetailGrid);
	
// 	fakturPenjualanDetailGrid
// 		.jqGrid("setGridParam", {
// 			url: `${API_URL}/faktur-penjualan-detail?fakturpenjualan_id=${jurnalHeaderId}`,
// 			datatype: "json",
// 			mtype: "GET",
// 			page: 1,
// 		})
// 		.trigger("reloadGrid")
// 		.on("reloadGridComplete", function () {
// 			let data = fakturPenjualanDetailGrid.jqGrid("getGridParam", "data");
// 			let totalAmount = 0;
// 			if (data && data.length > 0) {
// 				totalAmount = data.reduce(function (sum, row) {
// 					return sum + parseFloat(row.amount);
// 				}, 0);
// 			}
// 			fakturPenjualanDetailGrid.jqGrid("footerData", "set", {
// 				amount: totalAmount,
// 			});
// 		});
// }
