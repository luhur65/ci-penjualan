$.jgrid.extend({
	toolbarBindKeys: function (options) {
		var o = $.extend(
			{
				onEnter: null,
				onSpace: null,
				onLeftKey: null,
				onRightKey: null,
				onSelectRow: true,
				scrollingRows: true,
			},
			options || {}
		);

		this.each(function () {
			let element = this;

			$(element)
				.parents(".ui-jqgrid")
				.find("input")
				.on("keydown", function (event) {
					let currentPage = $(element).getGridParam("page");
					let lastPage = $(element).getGridParam("lastpage");

					var target = $(element).find("tr[tabindex=0]")[0],
						id,
						r,
						mind,
						expanded = element.p.treeReader.expanded_field;
					//check for arrow keys
					if (target) {
						var previd = element.p.selrow;
						mind =
							element.p._index[
								$.jgrid.stripPref(element.p.idPrefix, target.id)
							];
						if (
							event.keyCode === 37 ||
							event.keyCode === 38 ||
							event.keyCode === 39 ||
							event.keyCode === 40
						) {
							// up key
							if (event.keyCode === 38) {
								r = target.previousSibling;
								id = "";
								if (r && $(r).hasClass("jqgrow")) {
									if ($(r).is(":hidden")) {
										while (r) {
											r = r.previousSibling;
											if (!$(r).is(":hidden") && $(r).hasClass("jqgrow")) {
												id = r.id;
												break;
											}
										}
									} else {
										id = r.id;
									}
									$(element).jqGrid("setSelection", id, o.onSelectRow, event);
								}
								$(element).triggerHandler("jqGridKeyUp", [id, previd, event]);
								if ($.jgrid.isFunction(o.onUpKey)) {
									o.onUpKey.call(element, id, previd, event);
								}
								event.preventDefault();
							}
							//if key is down arrow
							if (event.keyCode === 40) {
								r = target.nextSibling;
								id = "";
								if (r && $(r).hasClass("jqgrow")) {
									if ($(r).is(":hidden")) {
										while (r) {
											r = r.nextSibling;
											if (!$(r).is(":hidden") && $(r).hasClass("jqgrow")) {
												id = r.id;
												break;
											}
										}
									} else {
										id = r.id;
									}
									$(element).jqGrid("setSelection", id, o.onSelectRow, event);
								}
								$(element).triggerHandler("jqGridKeyDown", [id, previd, event]);
								if ($.jgrid.isFunction(o.onDownKey)) {
									o.onDownKey.call(element, id, previd, event);
								}
								event.preventDefault();
							}
							// left
							if (event.keyCode === 37) {
								if (element.p.treeGrid && element.p.data[mind][expanded]) {
									$(target).find("div.treeclick").trigger("click");
								}
								$(element).triggerHandler("jqGridKeyLeft", [
									element.p.selrow,
									event,
								]);
								if ($.jgrid.isFunction(o.onLeftKey)) {
									o.onLeftKey.call(element, element.p.selrow, event);
								}
							}
							// right
							if (event.keyCode === 39) {
								if (element.p.treeGrid && !element.p.data[mind][expanded]) {
									$(target).find("div.treeclick").trigger("click");
								}
								$(element).triggerHandler("jqGridKeyRight", [
									element.p.selrow,
									event,
								]);
								if ($.jgrid.isFunction(o.onRightKey)) {
									o.onRightKey.call(element, element.p.selrow, event);
								}
							}
						}
						//check if enter was pressed on a grid or treegrid node
						else if (event.keyCode === 13) {
							$(element).triggerHandler("jqGridKeyEnter", [
								element.p.selrow,
								event,
							]);
							if ($.jgrid.isFunction(o.onEnter)) {
								o.onEnter.call(element, element.p.selrow, event);
							}
						} else if (event.keyCode === 32) {
							$(element).triggerHandler("jqGridKeySpace", [
								element.p.selrow,
								event,
							]);
							if ($.jgrid.isFunction(o.onSpace)) {
								o.onSpace.call(element, element.p.selrow, event);
							}
						}
					}

					// page up
					if (event.which == 33) {
						if (currentPage > 1) {
							$(element)
								.jqGrid("setGridParam", {
									page: $(element).getGridParam("page") - 1,
								})
								.trigger("reloadGrid");

							event.preventDefault();
						}

						$(element).jqGrid("setGridParam", {
							triggerClick: false,
						});
					}

					// page down
					if (event.which == 34) {
						if (currentPage !== lastPage) {
							$(element)
								.jqGrid("setGridParam", {
									page: $(element).getGridParam("page") + 1,
								})
								.trigger("reloadGrid");

							event.preventDefault();
						}

						$(element).jqGrid("setGridParam", {
							triggerClick: false,
						});
					}

					// end
					if (event.which == 35) {
						if (currentPage !== lastPage) {
							$(element)
								.jqGrid("setGridParam", {
									page: $(element).getGridParam("lastpage"),
								})
								.trigger("reloadGrid");

							event.preventDefault();
						}

						$(element).jqGrid("setGridParam", {
							triggerClick: false,
						});
					}

					// home
					if (event.which == 36) {
						if (currentPage > 1) {
							if (currentPage !== lastPage) {
								$(element)
									.jqGrid("setGridParam", {
										page: 1,
									})
									.trigger("reloadGrid");

								event.preventDefault();
							}
						}

						$(element).jqGrid("setGridParam", {
							triggerClick: false,
						});
					}
				});
		});

		return this;
	},
	customBindKeys: function () {
		this.each(function () {
			let element = this;

			$(element).on("keydown", function (event) {
				let currentPage = $(element).getGridParam("page");
				let lastPage = $(element).getGridParam("lastpage");

				// page up
				if (event.which == 33) {
					if (currentPage > 1) {
						$(element)
							.jqGrid("setGridParam", {
								page: $(element).getGridParam("page") - 1,
							})
							.trigger("reloadGrid");

						event.preventDefault();
					}

					$(element).jqGrid("setGridParam", {
						triggerClick: true,
					});
				}

				// page down
				if (event.which == 34) {
					if (currentPage !== lastPage) {
						$(element)
							.jqGrid("setGridParam", {
								page: $(element).getGridParam("page") + 1,
							})
							.trigger("reloadGrid");

						event.preventDefault();
					}

					$(element).jqGrid("setGridParam", {
						triggerClick: true,
					});
				}

				// end
				if (event.which == 35) {
					if (currentPage !== lastPage) {
						$(element)
							.jqGrid("setGridParam", {
								page: $(element).getGridParam("lastpage"),
							})
							.trigger("reloadGrid");

						event.preventDefault();
					}

					$(element).jqGrid("setGridParam", {
						triggerClick: true,
					});
				}

				// home
				if (event.which == 36) {
					if (currentPage > 1) {
						$(element)
							.jqGrid("setGridParam", {
								page: 1,
							})
							.trigger("reloadGrid");

						event.preventDefault();
					}

					$(element).jqGrid("setGridParam", {
						triggerClick: true,
					});
				}
			});
		});

		return this;
	},
	loadClearFilter: function () {
		return this.each(function () {
			let self = this;

			let element = $(`#gsh_${$.jgrid.jqID(this.id)}_rn`).html(
				$(
					`<div id='resetfilter' class='reset'><span id="resetdatafilter_${$(
						this
					).getGridParam("id")}" class='btn btn-default'> X </span></div>`
				)
			);

			element.click(function () {
				highlightSearch = "";

				$.fn.jqGrid.clearGlobalSearch.call(self);
				$.fn.jqGrid.clearFilterToolbar.call(self);

				$(self)
					.jqGrid("setGridParam", {
						search: false,
						postData: {
							filters: "",
						},
					})
					.trigger("reloadGrid");
			});
		});
	},
	clearFilter: function () {},
	clearFilterToolbar: function () {
		$(`#gview_${$(this).getGridParam("id")}`)
			.find('input[id*="gs_"]')
			.val("");
		$(`#gview_${$(this).getGridParam("id")}`)
			.find('select[id*="gs_"]')
			.val("")
			.trigger("change.select2");
		$(`#resetdatafilter_${$(this).getGridParam("id")}`).removeClass("active");

		return this;
	},
	globalSearch: function (options) {
		let settings = $.extend(
			{
				beforeSearch: function () {},
			},
			options || {}
		);

		let grid = $(this);

		/* Append global search textfield */
		$("#t_" + $.jgrid.jqID(grid[0].id)).html(
			$(
				`<form class="form-inline"><div class="form-group w-100 px-2" id="titlesearch"><label for="searchText" style="font-weight: normal !important;">Search : </label><input type="text" class="form-control form-control-sm global-search" id="${$.jgrid.jqID(
					grid[0].id
				)}_searchText" placeholder="Search" autocomplete="off"></div></form>`
			)
		);

		/* Handle textfield on input */
		$(document).on(
			"input",
			`#${$.jgrid.jqID(grid[0].id)}_searchText`,
			function () {
				delay(function () {
					let postData = grid.jqGrid("getGridParam", "postData");
					let colModel = grid.jqGrid("getGridParam", "colModel");
					let searchText = $(`#${$.jgrid.jqID(grid[0].id)}_searchText`).val();
					let l = colModel.length;
					let i;
					let cm;

					for (i = 0; i < l; i++) {
						cm = colModel[i];

						if (
							cm.search !== false &&
							(cm.stype === undefined ||
								cm.stype === "text" ||
								cm.stype === "select")
						) {
							grid.jqGrid("setGridParam", {
								postData: {
									filters: {
										[cm.name]: `cn:${searchText}`,
									},
								},
							});
						}
					}

					postData.filter_group = "OR";

					grid.jqGrid("setGridParam", {
						search: true,
					});

					settings.beforeSearch.call(grid);

					grid.trigger("reloadGrid", [
						{
							page: 1,
							current: true,
						},
					]);

					return false;
				}, 500);
			}
		);

		return this;
	},
	clearGlobalSearch: function () {
		$(`#${$(this).getGridParam("id")}_searchText`).val("");

		return this;
	},
	customPager: function (option = {}) {
		loadPagerHandler = function (element, grid) {
			$(element).html(`
			<button type="button" id="${
				grid.getGridParam().id
			}_firstPageButton" class="btn btn-sm hover-primary mr-2 d-flex">
				<span class="fas fa-step-backward"></span>
			</button>
	
			<button type="button" id="${
				grid.getGridParam().id
			}_previousPageButton" class="btn btn-sm hover-primary d-flex">
				<span class="fas fa-backward"></span>
			</button>
			
			<div class="d-flex align-items-center my-1 mx-3 justify-content-between gap-10">
				<span>Page</span>
				<input id="${grid.getGridParam().id}_pagerInput" class="pager-input" value="${
				grid.getGridParam().page
			}">
				<span id="${grid.getGridParam().id}_totalPage">of ${
				grid.getGridParam().lastpage
			}</span>
			</div>
	
			<button type="button" id="${
				grid.getGridParam().id
			}_nextPageButton" class="btn btn-sm hover-primary d-flex">
				<span class="fas fa-forward"></span>
			</button>
	
			<button type="button" id="${
				grid.getGridParam().id
			}_lastPageButton" class="btn btn-sm hover-primary ml-2 d-flex">
				<span class="fas fa-step-forward"></span>
			</button>
	
			<select id="${grid.getGridParam().id}_rowList" class="ml-2">
				${grid
					.getGridParam()
					.rowList.map((row, index) => {
						return `<option value="${row}">${row}</option>`;
					})
					.join("")}
			</select>
		`);

			$(document).on(
				"click",
				`#${grid.getGridParam().id}_firstPageButton`,
				function () {
					toFirstPage(grid);
				}
			);

			$(document).on(
				"click",
				`#${grid.getGridParam().id}_previousPageButton`,
				function () {
					toPreviousPage(grid);
				}
			);

			$(document).on(
				"click",
				`#${grid.getGridParam().id}_nextPageButton`,
				function () {
					toNextPage(grid);
				}
			);

			$(document).on(
				"click",
				`#${grid.getGridParam().id}_lastPageButton`,
				function () {
					toLastPage(grid);
				}
			);

			$(`#${grid.getGridParam().id}_pagerInput`).keydown(function (event) {
				if (event.which === 13) {
					jumpToPage(grid, $(this).val());
				}
			});

			$(`#${grid.getGridParam().id}_rowList`).change(function (event) {
				setPerPage(grid, $(this).val());
			});
		};

		toNextPage = function (grid) {
			let currentPage = grid.getGridParam().page;
			let lastPage = grid.getGridParam("lastpage");
			let nextPage = parseInt(currentPage) + 1;

			if (nextPage <= lastPage) {
				
				grid.setGridParam({
					page: nextPage,
					postData: {
						proses: "page",
					},
				}).trigger("reloadGrid");

			}
		};

		toLastPage = function (grid) {
			let lastPage = grid.getGridParam("lastpage");
			let currentPage = grid.getGridParam("page");

			if (currentPage < lastPage) {
				grid.trigger("reloadGrid", [
					{
						page: lastPage,
						postData: {
							proses: "page",
						},
					},
				]);
			}
		};

		toPreviousPage = function (grid) {
			let currentPage = grid.getGridParam().page;

			if (currentPage > 1) {
				grid.trigger("reloadGrid", [
					{
						page: parseInt(currentPage) - 1,
						postData: {
							proses: "page",
						},
					},
				]);
			}
		};

		toFirstPage = function (grid) {
			let currentPage = grid.getGridParam("page");

			if (currentPage > 1) {
				grid.setGridParam({
					page: 1,
					postData: {
						proses: "page",
					},
				}).trigger("reloadGrid");
			}
		};

		jumpToPage = function (grid, page) {
			grid.setGridParam({
				page: page,
				postData: {
					proses: "page",
				},
			}).trigger("reloadGrid");
		};

		setPerPage = function (grid, perPage) {
			grid
				.setGridParam({
					rowNum: perPage,
					page: 1,
					postData: {
						proses: "page",
					},
				})
				.trigger("reloadGrid");
		};

		loadPagerHandlerInfo = function (element, grid) {
			let page = grid.getGridParam().page;
			let totalPage = grid.getGridParam().lastpage;

			$(element).find(`#${grid.getGridParam().id}_pagerInput`).val(page);
			$(element)
				.find(`#${grid.getGridParam().id}_totalPage`)
				.text(`of ${totalPage}`);
		};

		loadPagerInfo = function (element, grid) {
			let params = grid.getGridParam();
			let recordCount = params.reccount;
			let page = params.page;
			let perPage = params.rowNum;
			let totalRecords = params.records;
			let firstRow = (page - 1) * perPage + 1;
			let lastRow = firstRow + recordCount - 1;

			$(element).html(`
			<div class="text-md-right">
				View  ${firstRow} - ${lastRow} of ${totalRecords}
			</div>
		`);
		};

		if (
			!$(`#gbox_${$(this).getGridParam().id}`).siblings(".grid-pager").length
		) {
			let grid = $(this);
			let pagerHandlerId = `${grid.getGridParam().id}PagerHandler`;
			let pagerInfoId = `${grid.getGridParam().id}InfoHandler`;
			let extndBtn = "";
			if (option.extndBtn) {
				option.extndBtn.forEach((element) => {
					extndBtn += `<div class="btn-group dropup  scrollable-menu">`;
					extndBtn += `<button type="button" class="${element.class}" data-toggle="dropdown" id="${element.id}">
					${element.innerHTML}
					</button>`;
					extndBtn += `<ul class="dropdown-menu" id="menu-approve" aria-labelledby="${element.id}">`;
					if (element.dropmenuHTML) {
						element.dropmenuHTML.forEach((dropmenuHTML) => {
							extndBtn += `<li><a class="dropdown-item" id='${dropmenuHTML.id}' href="#">${dropmenuHTML.text}</a></li>`;
							$(document).on("click", `#${dropmenuHTML.id}`, function (event) {
								event.stopImmediatePropagation();

								dropmenuHTML.onClick();
							});
						});
					}
					extndBtn += `</ul>`;
					extndBtn += "</div>";
				});
			}

			$(`#gbox_${$(this).getGridParam().id}`).after(`
				<div class="col-12 bg-white grid-pager overflow-x-hidden mt-2">
					<div class="row d-flex align-items-center text-center text-lg-left">
						<div class="col-12 col-lg-6">
							${
								typeof option.buttons !== "undefined"
									? option.buttons
											.map((button, index) => {
												let buttonElement = document.createElement("button");

												buttonElement.id =
													typeof button.id !== "undefined"
														? button.id
														: `customButton_${index}`;
												buttonElement.className = button.class;
												buttonElement.innerHTML = button.innerHTML;

												if (button.onClick) {
													$(document).on(
														"click",
														`#${buttonElement.id}`,
														function (event) {
															event.stopImmediatePropagation();

															button.onClick.call(grid);
															// button.onClick();
														}
													);
												}

												return buttonElement.outerHTML;
											})
											.join("")
									: ""
							}
								${extndBtn}
						</div>
						<div class="col-12 col-lg-6">
							<div class="row d-flex align-items-center justify-content-center justify-content-lg-end pr-2">
								<div id="${pagerHandlerId}" class="pager-handler d-flex align-items-center justify-content-center mx-2">
								</div>
								<div id="${pagerInfoId}" class="pager-info">
								</div>
							</div>
						</div>
					</div>
				</div>
				
			`);

			loadPagerHandler(`#${pagerHandlerId}`, grid);

			grid.bind("jqGridLoadComplete.jqGrid", function (event, data) {
				loadPagerHandlerInfo(`#${pagerHandlerId}`, grid);
				loadPagerInfo(`#${pagerInfoId}`, grid);
			});
		}

		return this;
	},
});
