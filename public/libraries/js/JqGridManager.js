class JqGridManager {
  constructor(config = {}) {
    if (!config.gridId || !config.pagerId || !config.url || !config.colModel || !config.page) {
      console.error("gridId, pagerId, url, colModel, dan page wajib diisi!");
      return null;
    }

    const defaultOptions = {
      url: API_URL + config.url,
      mtype: "GET",
      datatype: "JSON",
      styleUI: 'Bootstrap4',
      iconSet: 'fontAwesome',
      colModel: config.colModel,
      autowidth: false,
      height: 320,
      rowNum: 10,
      rownumWidth: 45,
      rowList: [10, 20, 30],
      toolbar: [true, "top"],
      rownumbers: true,
      sortname: 'id',
      sortable: true,
      sortorder: 'asc',
      viewrecords: true,
      gridview: true,
      page: config.page || 1,
      pager: config.pagerId,
      jsonReader: {
        root: 'data',
        total: 'attributes.totalPages',
        records: 'attributes.totalRows',
      },
      loadBeforeSend: function(jqXHR) {
        jqXHR.setRequestHeader('Authorization', `Bearer ${ACCESS_TOKEN}`);
        // Assuming setGridLastRequest is preserved or handled locally
        JqGridManager.setGridLastRequest($(this), jqXHR);
      }
    };

    const finalOptions = $.extend(true, {}, defaultOptions, config.options || {});
    this.grid = $(config.gridId).jqGrid(finalOptions);

    this.grid.jqGrid("setLabel", "rn", "No.");
    this.grid.jqGrid('filterToolbar', {
      stringResult: true,
      searchOnEnter: false,
      defaultSearch: 'cn',
      groupOp: 'AND',
      disabledKeys: [17, 33, 34, 35, 36, 37, 38, 39, 40],
      beforeSearch: () => {
        JqGridManager.abortGridLastRequest(this.grid);
        $('#left-nav').find(`button:not(#add)`).attr('disabled', 'disabled');
      },
    });

    JqGridManager.initGlobalSearch(this.grid, config);

    if (config.lazyLoad) {
      this.grid.jqGrid('setGridParam', {
        datatype: 'local',
        rowNum: config.lazyLoadOptions?.rowsPerPage || 50,
        pgbuttons: false,
        pgtext: null,
        viewrecords: false
      });

      $(config.pagerId).hide();

      let lazyLoader = new JqGridLazyLoader(
        config.gridId,
        API_URL + config.url,
        ACCESS_TOKEN,
        config.lazyLoadOptions || {}
      );

      this.grid.data('lazyLoader', lazyLoader);
    }
  }

  static setHighlight(grid) {
    let stringFilters;
    let filters;
    let gridId;

    stringFilters = grid.getGridParam("postData").filters;

    if (stringFilters) {
      filters = JSON.parse(stringFilters);
    }

    gridId = $(grid).getGridParam().id;

    if (filters) {
      filters.rules.forEach((rule) => {
        $(grid)
          .find(`tbody tr td[aria-describedby=${gridId}_${rule.field}]`)
          .each(function() {
            if ($(this).find(".badge").length === 0) {
              $(this).highlight(rule.data);
            }
          });
      });
    }
  }

  static clearColumnSearch(grid) {
    grid.jqGrid("clearFilterToolbar");
    grid.jqGrid("setGridParam", {
      postData: {
        filters: ""
      }
    });
  }

  static initGlobalSearch(grid, config) {
    const url = config.url;
    $("#t_" + $.jgrid.jqID(grid[0].id)).html(
      $(
        `<div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between w-100 px-2 py-1">
            <form class="form-inline">
                <div class="form-group w-100 px-2" id="titlesearch">
                    <label for="searchText" style="font-weight: normal !important;">Search : </label>
                    <input type="text" class="form-control form-control-sm global-search" id="${$.jgrid.jqID(grid[0].id)}_searchText" placeholder="Search" autocomplete="off">
                </div>
            </form>
            <div class="px-2 d-flex align-items-center">
                <div id="searchDetail_${$.jgrid.jqID(grid[0].id)}" class="px-2"></div>
                <div id="infoContainer_${$.jgrid.jqID(grid[0].id)}" class="px-2"></div>
            </div>
        </div>`
      )
    );

    $(document).on("input", `#${$.jgrid.jqID(grid[0].id)}_searchText`, function() {
      // Delay implementation
      if (JqGridManager.searchTimeout) clearTimeout(JqGridManager.searchTimeout);
      JqGridManager.searchTimeout = setTimeout(() => {
        JqGridManager.abortGridLastRequest(grid);
        JqGridManager.clearColumnSearch(grid);

        let postData = grid.jqGrid("getGridParam", "postData");
        let colModel = grid.jqGrid("getGridParam", "colModel");

        let rules = [];
        let searchText = $(`#${$.jgrid.jqID(grid[0].id)}_searchText`).val();

        if (typeof addedRules !== 'undefined' && addedRules) rules.push(addedRules);

        colModel.forEach(cm => {
          if (cm.search !== false && (!cm.stype || cm.stype === "text" || cm.stype === "select")) {
            rules.push({
              field: cm.name,
              op: "cn",
              data: searchText.toUpperCase(),
            });
          }
        });

        postData.filters = JSON.stringify({
          groupOp: "OR",
          rules: rules
        });

        grid.jqGrid("setGridParam", { search: true });
        grid.trigger("reloadGrid", [{ page: 1, current: true }]);
        return false;
      }, 500);
    });
  }

  static setGridLastRequest(grid, lastRequest) {
    grid.setGridParam({ lastRequest });
  }

  static getGridLastRequest(grid) {
    return grid.getGridParam()?.lastRequest;
  }

  static abortGridLastRequest(grid) {
    JqGridManager.getGridLastRequest(grid)?.abort();
  }
}
