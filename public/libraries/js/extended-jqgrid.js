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
				.off("keydown.toolbar")
				.on("keydown.toolbar", function (event) {
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
								var ids = $(element).jqGrid('getDataIDs');
								var selId = $(element).jqGrid('getGridParam', 'selrow');
								var currIndex = ids.indexOf(selId);

								if (currIndex > 0) {
									var prevId = ids[currIndex - 1];
									$(element).jqGrid("setSelection", prevId, o.onSelectRow, event);

									indexRow = currIndex - 1; // Update variabel global indexRow Anda

									if (typeof scrollGridSelectionIntoView !== "undefined") {
										scrollGridSelectionIntoView(element, prevId);
									}
								}
								event.preventDefault();
								event.stopPropagation();
							}
							//if key is down arrow
							if (event.keyCode === 40) {
								var ids = $(element).jqGrid('getDataIDs');
								var selId = $(element).jqGrid('getGridParam', 'selrow');
								var currIndex = ids.indexOf(selId);

								if (currIndex < ids.length - 1) {
									var nextId = ids[currIndex + 1];
									$(element).jqGrid("setSelection", nextId, o.onSelectRow, event);

									// Update indexRow agar loadComplete tidak menarik balik posisinya
									indexRow = currIndex + 1;

									if (typeof scrollGridSelectionIntoView !== "undefined") {
										scrollGridSelectionIntoView(element, nextId);
									}
								}
								event.preventDefault();
								event.stopPropagation();
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

					var loader = $(element).data("lazyLoader") || (typeof lazyLoader !== "undefined" ? lazyLoader : null);
					var hasLoader = loader !== null && loader !== undefined;
					if (hasLoader && loader.loading) return;

					var focusRow = function (rowType) {
						setTimeout(function () {
							var $g = $(element);
							var newIds = $g.getDataIDs();
							if (!newIds.length) return;

							var bDiv = $g.closest(".ui-jqgrid-bdiv");
							var targetId;

							if (rowType === 'first') {
								targetId = newIds[0];
								bDiv.scrollTop(0);
							} else if (rowType === 'last') {
								targetId = newIds[newIds.length - 1];
								bDiv.scrollTop(bDiv[0].scrollHeight);
							}

							if (targetId) {
								$g.resetSelection().setSelection(targetId);
								// $g.find(`tr#${targetId}`).focus();
								if (!$(document.activeElement).is("input, textarea, select")) {
									$g.find(`tr#${targetId}`).focus();
								}
							}
						}, 150);
					};

					// page up
					if (event.which == 33) {
						console.log("ketekan toolbarbindkeys page up")
						if (hasLoader) {
							if (loader.currentViewPage > 1) {
								loader.loadGridData($(element).jqGrid("getGridParam", "postData"), loader.currentViewPage - 1, loader.rowsPerPage, 'up', 'jump', function () { focusRow('first'); });
							}
						} else {
							if (currentPage > 1) {
								$(element).jqGrid("setGridParam", { page: $(element).getGridParam("page") - 1 }).trigger("reloadGrid");
							}
						}
						$(element).jqGrid("setGridParam", { triggerClick: true });
						event.preventDefault();
						event.stopImmediatePropagation();

						var $thisInput = $(this);
						setTimeout(() => { $thisInput.focus(); }, 200);
					}

					// page down
					if (event.which == 34) {
						console.log("ketekan toolbarbindkeys page down")
						if (hasLoader) {
							if (loader.currentViewPage < loader.totalPages) {
								loader.loadGridData($(element).jqGrid("getGridParam", "postData"), loader.currentViewPage + 1, loader.rowsPerPage, 'down', 'jump', function () { focusRow('first'); });
							}
						} else {
							if (currentPage !== lastPage) {
								$(element).jqGrid("setGridParam", { page: $(element).getGridParam("page") + 1 }).trigger("reloadGrid");
							}
						}
						$(element).jqGrid("setGridParam", { triggerClick: true });
						event.preventDefault();
						event.stopImmediatePropagation();

						var $thisInput = $(this);
						setTimeout(() => { $thisInput.focus(); }, 200);
					}

					// end
					if (event.which == 35) {
						console.log("ketekan toolbarbindkeys end")
						if (hasLoader) {
							if (loader.currentViewPage !== loader.totalPages) {
								loader.loadGridData($(element).jqGrid("getGridParam", "postData"), loader.totalPages, loader.rowsPerPage, 'down', 'jump', function () { focusRow('last'); });
							} else {
								focusRow('last');
							}
						} else {
							if (currentPage !== lastPage) {
								$(element).jqGrid("setGridParam", { page: lastPage }).trigger("reloadGrid");
							}
							let ids = $(element).getDataIDs();
							if (ids.length > 0) {
								$(element).jqGrid("setSelection", ids[ids.length - 1], true, event);
								if (typeof scrollGridSelectionIntoView !== "undefined") scrollGridSelectionIntoView(element, ids[ids.length - 1]);
							}
						}
						$(element).jqGrid("setGridParam", { triggerClick: true });
						event.preventDefault();
						event.stopImmediatePropagation();

						var $thisInput = $(this);
						setTimeout(() => { $thisInput.focus(); }, 200);
					}

					// home
					if (event.which == 36) {
						console.log("ketekan toolbarbindkeys home")
						if (hasLoader) {
							if (loader.currentViewPage > 1) {
								loader.loadGridData($(element).jqGrid("getGridParam", "postData"), 1, loader.rowsPerPage, 'up', 'jump', function () { focusRow('first'); });
							} else {
								focusRow('first');
							}
						} else {
							if (currentPage > 1) {
								$(element).jqGrid("setGridParam", { page: 1 }).trigger("reloadGrid");
							}
							let ids = $(element).getDataIDs();
							if (ids.length > 0) {
								$(element).jqGrid("setSelection", ids[0], true, event);
								if (typeof scrollGridSelectionIntoView !== "undefined") scrollGridSelectionIntoView(element, ids[0]);
							}
						}
						$(element).jqGrid("setGridParam", { triggerClick: true });
						event.preventDefault();
						event.stopImmediatePropagation();

						var $thisInput = $(this);
						setTimeout(() => { $thisInput.focus(); }, 200);
					}
				});
		});

		return this;
	},
	// customBindKeys: function (options) {
	// 	var o = $.extend(
	// 		{
	// 			onEnter: null,
	// 			onSpace: null,
	// 			onLeftKey: null,
	// 			onRightKey: null,
	// 			onSelectRow: true,
	// 			scrollingRows: true,
	// 		},
	// 		options || {}
	// 	);

	// 	this.each(function () {
	// 		let element = this;

	// 		$(element).off("keydown.custom").on("keydown.custom", function (event) {
	// 			if ($(event.target).is("input, textarea, select")) {
	// 				return;
	// 			}

	// 			let currentPage = $(element).getGridParam("page");
	// 			let lastPage = $(element).getGridParam("lastpage");

	// 			var loader = $(element).data("lazyLoader") || (typeof lazyLoader !== "undefined" ? lazyLoader : null);
	// 			var hasLoader = loader !== null && loader !== undefined;
	// 			if (hasLoader && loader.loading) return;

	// 			var focusRow = function (rowType) {
	// 				setTimeout(function () {
	// 					var $g = $(element);
	// 					var newIds = $g.getDataIDs();
	// 					if (!newIds.length) return;

	// 					var bDiv = $g.closest(".ui-jqgrid-bdiv");
	// 					var targetId;

	// 					if (rowType === 'first') {
	// 						targetId = newIds[0];
	// 						bDiv.scrollTop(0);
	// 					} else if (rowType === 'last') {
	// 						targetId = newIds[newIds.length - 1];
	// 						bDiv.scrollTop(bDiv[0].scrollHeight);
	// 					}

	// 					if (targetId) {
	// 						$g.resetSelection().setSelection(targetId);
	// 						$g.find(`tr#${targetId}`).focus();
	// 					}
	// 				}, 150);
	// 			};

	// 			// page up
	// 			if (event.which == 33) {
	// 				console.log("ketekan customBindkeys page up")
	// 				if (hasLoader) {
	// 					if (loader.currentViewPage > 1) {
	// 						loader.loadGridData($(element).jqGrid("getGridParam", "postData"), loader.currentViewPage - 1, loader.rowsPerPage, 'up', 'jump', function () { focusRow('first'); });
	// 					}
	// 				} else {
	// 					if (currentPage > 1) {
	// 						$(element).jqGrid("setGridParam", { page: $(element).getGridParam("page") - 1 }).trigger("reloadGrid");
	// 					}
	// 				}
	// 				$(element).jqGrid("setGridParam", { triggerClick: true });
	// 				event.preventDefault();
	// 			}

	// 			// page down
	// 			if (event.which == 34) {
	// 				console.log("ketekan customBindkeys page down")
	// 				if (hasLoader) {
	// 					if (loader.currentViewPage < loader.totalPages) {
	// 						loader.loadGridData($(element).jqGrid("getGridParam", "postData"), loader.currentViewPage + 1, loader.rowsPerPage, 'down', 'jump', function () { focusRow('first'); });
	// 					}
	// 				} else {
	// 					if (currentPage !== lastPage) {
	// 						$(element).jqGrid("setGridParam", { page: $(element).getGridParam("page") + 1 }).trigger("reloadGrid");
	// 					}
	// 				}
	// 				$(element).jqGrid("setGridParam", { triggerClick: true });
	// 				event.preventDefault();
	// 			}

	// 			// end
	// 			if (event.which == 35) {
	// 				console.log("ketekan customBindkeys end")
	// 				if (hasLoader) {
	// 					if (loader.currentViewPage !== loader.totalPages) {
	// 						loader.loadGridData($(element).jqGrid("getGridParam", "postData"), loader.totalPages, loader.rowsPerPage, 'down', 'jump', function () { focusRow('last'); });
	// 					} else {
	// 						focusRow('last');
	// 					}
	// 				} else {
	// 					if (currentPage !== lastPage) {
	// 						$(element).jqGrid("setGridParam", { page: lastPage }).trigger("reloadGrid");
	// 					}
	// 					let ids = $(element).getDataIDs();
	// 					if (ids.length > 0) {
	// 						$(element).jqGrid("setSelection", ids[ids.length - 1], true, event);
	// 						if (typeof scrollGridSelectionIntoView !== "undefined") scrollGridSelectionIntoView(element, ids[ids.length - 1]);
	// 					}
	// 				}
	// 				$(element).jqGrid("setGridParam", { triggerClick: true });
	// 				event.preventDefault();
	// 			}

	// 			// home
	// 			if (event.which == 36) {
	// 				console.log("ketekan customBindkeys home")
	// 				if (hasLoader) {
	// 					if (loader.currentViewPage > 1) {
	// 						loader.loadGridData($(element).jqGrid("getGridParam", "postData"), 1, loader.rowsPerPage, 'up', 'jump', function () { focusRow('first'); });
	// 					} else {
	// 						focusRow('first');
	// 					}
	// 				} else {
	// 					if (currentPage > 1) {
	// 						$(element).jqGrid("setGridParam", { page: 1 }).trigger("reloadGrid");
	// 					}
	// 					let ids = $(element).getDataIDs();
	// 					if (ids.length > 0) {
	// 						$(element).jqGrid("setSelection", ids[0], true, event);
	// 						if (typeof scrollGridSelectionIntoView !== "undefined") scrollGridSelectionIntoView(element, ids[0]);
	// 					}
	// 				}
	// 				$(element).jqGrid("setGridParam", { triggerClick: true });
	// 				event.preventDefault();
	// 			}



	// 			// arrow down
	// 			// if (event.which == 40) {
	// 			// 	if (hasLoader && loader.loading) { event.preventDefault(); return; }

	// 			// 	let selrow = $(element).jqGrid('getGridParam', 'selrow');
	// 			// 	if (selrow) {
	// 			// 		let target = $(element).find(`tr#${selrow}`)[0];
	// 			// 		let r = target.nextSibling;
	// 			// 		let id = "";

	// 			// 		if (r && $(r).hasClass("jqgrow")) {
	// 			// 			if ($(r).is(":hidden")) {
	// 			// 				while (r) {
	// 			// 					r = r.nextSibling;
	// 			// 					if (!$(r).is(":hidden") && $(r).hasClass("jqgrow")) {
	// 			// 						id = r.id;
	// 			// 						break;
	// 			// 					}
	// 			// 				}
	// 			// 			} else {
	// 			// 				id = r.id;
	// 			// 			}
	// 			// 		}

	// 			// 		if (id) {
	// 			// 			// Skenario 1: Normal (Pindah baris)
	// 			// 			$(element).jqGrid('setSelection', id, true, event);
	// 			// 			let targetRow = $(element).find(`tr#${id}`)[0];
	// 			// 			if (targetRow) {
	// 			// 				targetRow.focus({ preventScroll: true });
	// 			// 				targetRow.scrollIntoView({ block: 'nearest', inline: 'nearest' });
	// 			// 			}
	// 			// 		} else {
	// 			// 			// Skenario 2: Mentok Bawah
	// 			// 			if (hasLoader && loader.maxPageLoaded < loader.totalPages) {
	// 			// 				let targetPage = loader.maxPageLoaded + 1;
	// 			// 				loader.loadGridData(
	// 			// 					$(element).jqGrid("getGridParam", "postData"),
	// 			// 					targetPage,
	// 			// 					loader.rowsPerPage,
	// 			// 					'down',
	// 			// 					'jump',
	// 			// 					function () {
	// 			// 						setTimeout(function () {
	// 			// 							let newIds = $(element).getDataIDs();
	// 			// 							if (newIds.length > 0) {
	// 			// 								let firstId = newIds[0];
	// 			// 								$(element).jqGrid('setSelection', firstId, true, event);

	// 			// 								let bDiv = $(element).closest(".ui-jqgrid-bdiv");
	// 			// 								bDiv.scrollTop(0);
	// 			// 								loader.lastScrollTop = 0;

	// 			// 								let targetRow = $(element).find(`tr#${firstId}`)[0];
	// 			// 								if (targetRow) targetRow.focus({ preventScroll: true });
	// 			// 							}
	// 			// 						}, 150);
	// 			// 					}
	// 			// 				);
	// 			// 			}
	// 			// 		}
	// 			// 	}
	// 			// 	event.preventDefault();
	// 			// }
				

	// 			// arrow up
	// 			if (event.which == 38) {
	// 				let selrow = $(element).jqGrid('getGridParam', 'selrow');
	// 				if (selrow) {
	// 					let target = $(element).find(`tr#${selrow}`)[0];
	// 					let r = target.previousSibling;
	// 					let id = "";

	// 					if (r && $(r).hasClass("jqgrow")) {
	// 						if ($(r).is(":hidden")) {
	// 							while (r) {
	// 								r = r.previousSibling;
	// 								if (!$(r).is(":hidden") && $(r).hasClass("jqgrow")) {
	// 									id = r.id;
	// 									break;
	// 								}
	// 							}
	// 						} else {
	// 							id = r.id;
	// 						}

	// 						$(element).jqGrid('setSelection', id, true, event);


	// 						// Atur posisi scroll menggunakan fungsi custom
	// 						if (typeof scrollGridSelectionIntoView !== "undefined") {
	// 							scrollGridSelectionIntoView(element, id);
	// 						}
	// 					}
	// 				}
	// 				event.preventDefault();
	// 			}

	// 			// arrow down
	// 			if (event.which == 40) {
	// 				var selrow = $(element).jqGrid('getGridParam', 'selrow');
	// 				if (!selrow) { event.preventDefault(); return; }

	// 				var currentRow = $(element).find(`tr#${selrow}`)[0];
	// 				if (!currentRow) { event.preventDefault(); return; }

	// 				var r = currentRow.nextSibling;
	// 				var id = "";

	// 				while (r) {
	// 					if ($(r).hasClass("jqgrow") && !$(r).is(":hidden")) {
	// 						id = r.id;
	// 						break;
	// 					}
	// 					r = r.nextSibling;
	// 				}

	// 				if (id) {
	// 					$(element).jqGrid('setSelection', id, false, event);

	// 					// ✅ Scroll manual: pastikan row selalu kelihatan
	// 					var bDiv = $(element).closest(".ui-jqgrid-bdiv");
	// 					var targetRow = $(element).find(`tr#${id}`)[0];
	// 					if (targetRow) {
	// 						var bDivTop = bDiv.offset().top;
	// 						var bDivBottom = bDivTop + bDiv.height();
	// 						var rowTop = $(targetRow).offset().top;
	// 						var rowBottom = rowTop + $(targetRow).height();

	// 						// Kalau row di bawah viewport → scroll ke bawah
	// 						if (rowBottom > bDivBottom) {
	// 							bDiv.scrollTop(bDiv.scrollTop() + (rowBottom - bDivBottom));
	// 						}
	// 						// Kalau row di atas viewport → scroll ke atas
	// 						else if (rowTop < bDivTop) {
	// 							bDiv.scrollTop(bDiv.scrollTop() - (bDivTop - rowTop));
	// 						}
	// 					}
	// 				}

	// 				event.preventDefault();

	// 			}


	// 			// // arrow up
  //       // if (event.which == 38) {

  //       //   let selrow = $(element).jqGrid('getGridParam', 'selrow');
  //       //   let ids = $(element).getDataIDs();
  //       //   let index = ids.indexOf(selrow);
  //       //   if (index > 0) {
	// 			// 		let targetId = ids[index - 1];
  //       //     $(element).jqGrid('setSelection', targetId, true, event);
	// 			// 		$(element).find(`tr#${targetId}`).focus();
  //       //     if (typeof scrollGridSelectionIntoView !== "undefined") {
	// 			// 			console.log('harusnya si stay dulu ya diatas')
  //       //       scrollGridSelectionIntoView(element, targetId);
  //       //     }
  //       //   }
  //       //   event.preventDefault();
  //       // }

  //       // // arrow down
  //       // if (event.which == 40) {
  //       //   let selrow = $(element).jqGrid('getGridParam', 'selrow');
  //       //   let ids = $(element).getDataIDs();
  //       //   let index = ids.indexOf(selrow);
  //       //   if (index < ids.length - 1) {
	// 			// 		let targetId = ids[index + 1];
  //       //     $(element).jqGrid('setSelection', targetId, true, event);
	// 			// 		$(element).find(`tr#${targetId}`).focus();
  //       //     if (typeof scrollGridSelectionIntoView !== "undefined") {
	// 			// 			console.log('harusnya si stay dulu ya dibawah')
  //       //       scrollGridSelectionIntoView(element, targetId);
  //       //     }
  //       //   }
  //       //   event.preventDefault();
  //       // }
	// 		});
	// 	});

	// 	return this;
	// },
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

				const gridEl = $(self);

				let postData = gridEl.jqGrid("getGridParam", "postData") || {};
				postData.filters = "";
				postData._search = false;
				delete postData.filter_group;

				gridEl.jqGrid("setGridParam", {
					search: false,
					postData: postData,
				});

				const loader = gridEl.data('lazyLoader');
				if (loader) {
					loader.loadGridData(postData, 1, loader.rowsPerPage, 'down', 'reload');
				} else {
					gridEl.trigger("reloadGrid");
				}
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
			let isLazy = grid.data('lazyLoader') !== undefined;

			if (isLazy) {
				// Kosongkan area navigasi sepenuhnya
				$(element).html('');
				return;
			}

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
			let isLazy = grid.data('lazyLoader') !== undefined;

			if (isLazy) return;

			let page = grid.getGridParam().page;
			let totalPage = grid.getGridParam().lastpage;

			$(element).find(`#${grid.getGridParam().id}_pagerInput`).val(page);
			$(element)
				.find(`#${grid.getGridParam().id}_totalPage`)
				.text(`of ${totalPage}`);
		};

		loadPagerInfo = function (element, grid) {
			let isLazy = grid.data('lazyLoader') !== undefined;
			if (isLazy) return;

			let params = grid.getGridParam();
			let recordCount = params.reccount;
			let page = params.page;
			let perPage = params.rowNum;
			let totalRecords = params.records;
			let firstRow = (page - 1) * perPage + 1;
			let lastRow = firstRow + recordCount - 1;

			if (isLazy) {
				// Tampilan khusus untuk mode Lazy Loading
				$(element).html(`
					<div class="text-md-right font-weight-bold">
						Total Data : ${totalRecords}
					</div>
				`);
			} else {
				$(element).html(`
					<div class="text-md-right">
						View  ${firstRow} - ${lastRow} of ${totalRecords}
					</div>
				`);
			}
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
				<div class="bg-white grid-pager overflow-x-hidden mt-2">
					<div class="row d-flex align-items-center text-center text-lg-left">
						<div class="col-12 col-lg-6">
							${
								typeof option.buttons !== "undefined"
									? option.buttons
											.map((button, index) => {
												let buttonElement = document.createElement("button");

												buttonElement.id = typeof button.id !== "undefined" ? button.id : `customButton_${index}`;
												buttonElement.className = button.class;

												let temp = document.createElement("div");
												temp.innerHTML = button.innerHTML;

												// ambil text node terakhir
												let textNode = [...temp.childNodes].find(n => n.nodeType === 3);

												if (textNode && button.shortcut) {
													let text = textNode.nodeValue;
													let index = text.toLowerCase().indexOf(button.shortcut.toLowerCase());

													if (index !== -1) {
														textNode.nodeValue = '';
														let before = document.createTextNode(text.substring(0, index));
														let underline = document.createElement("u");
														underline.textContent = text[index];
														let after = document.createTextNode(text.substring(index + 1));

														temp.appendChild(before);
														temp.appendChild(underline);
														temp.appendChild(after);
													}
												}

												buttonElement.innerHTML = temp.innerHTML;

												if (button.shortcut) {
													buttonElement.setAttribute('data-shortcut', button.shortcut);
												}

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
								<div id="${pagerInfoId}" class="pager-info mr-2">
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
	permissions: function(permissions) {
		// Loop akan otomatis membaca kunci (add, edit, dll) dan nilainya (true/false)
		for (const [buttonId, hasAccess] of Object.entries(permissions)) {
			if (hasAccess) {
				$(`#${buttonId}`).show();
			} else {
				$(`#${buttonId}`).hide();
			}
		}	
		return this;
	}
});
