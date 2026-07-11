class LookupV6 {
  // =========================================================
  // CONSTRUCTOR
  // =========================================================
  constructor(element, options) {
    this.element = $(element);
    this.inputId = this.element.attr('id');
    this.containerId = `lv6-container-${this.inputId}`;
    this.gridId = `lv6-grid-${this.inputId}`;
    this.NS = `lookupV6_${this.inputId}`;

    this.settings = $.extend({}, LookupV6.defaults, options);

    this.isOpen = false;
    this.selectedId = null;
    this.currentValue = '';
    this._isToolbarSearching = false;

    this._init();
  }

  // =========================================================
  // DEFAULT OPTIONS
  // =========================================================
  static get defaults() {
    return {
      title: 'Lookup',
      url: null,
      data: [],
      typeData: 'JSON',
      colModel: [],
      sortname: '',
      searching: [],
      filterToolbar: false,
      extendSize: 0,
      postData: {},
      onSelectRow: (rowData, element) => { },
      onClear: (element) => { },
      onCancel: (element) => { },
    };
  }

  // =========================================================
  // INIT
  // =========================================================
  _init() {
    this._buildUI();
    this._bindEvents();
    this._toggleClearBtn();
  }

  // =========================================================
  // BUILD UI (Murni untuk Display, Tanpa Hidden Input)
  // =========================================================
  _buildUI() {
    if (!this.element.parent().hasClass('input-group')) {
      this.element.wrap('<div class="input-group"></div>');
    }
    this.inputGroup = this.element.parent();
    this.inputGroup.css('align-items', 'stretch');
    this.element.css('padding-right', '32px');

    this.inputGroup.append(`
        <button type="button" class="btn position-absolute lv6-clear text-secondary"
            style="right: 42px; top: 50%; transform: translateY(-50%); z-index: 99; display: none; padding: 0; background: transparent; border: none;">
            <i class="fa fa-times-circle text-danger" style="font-size: 15px;"></i>
        </button>
        <div class="input-group-append" style="display: flex;">
            <button class="btn btn-easyui lv6-toggle" type="button" 
                style="padding-top: 0; padding-bottom: 0; padding-left: 12px; padding-right: 12px; margin: 0; border: 1px solid #ced4da; border-left: 0; display: flex; align-items: center; justify-content: center; box-sizing: border-box;">
                <i class="fas fa-sort-down" style="font-size: 16px; margin-bottom: 4px;"></i>
            </button>
        </div>
    `);

    this.btnClear = this.inputGroup.find('.lv6-clear');
    this.btnToggle = this.inputGroup.find('.lv6-toggle');
  }

  // =========================================================
  // BIND EVENTS
  // =========================================================
  _bindEvents() {
    this._isPasting = false;

    this.element.on(`change.${this.NS}`, () => {
      const val = this.element.val();
      this.currentValue = val;
      this.element.data('currentValue', val);
      this._toggleClearBtn();
    });

    this.element.on(`input.${this.NS}`, this._debounce(() => {
      if (this._isPasting) return;
      const val = this.element.val().trim();
      this._toggleClearBtn();
      this.open(val);
    }, 300));

    this.element.on(`paste.${this.NS}`, () => {
      this._isPasting = true;
      this._isPasteHandled = false;

      if (this.isOpen) {
        this.close();
      }

      setTimeout(() => {
        const pasteValue = this.element.val().trim();
        if (!pasteValue) {
          this._isPasting = false;
          return;
        }

        this._isPasteHandled = true;
        this._handlePaste(pasteValue);

        setTimeout(() => {
          this._isPasting = false;
        }, 400);
      }, 50);
    });

    this.btnToggle.on('click', (e) => {
      e.preventDefault();
      if (this.isOpen) {
        this.close();
      } else {
        this.open('');
        this.element.focus();
      }
    });

    this.btnClear.on('click', () => {
      this.element.val('');
      this._toggleClearBtn();
      this.settings.onClear(this.element);
      this.close();
    });

    this.element.on(`keydown.${this.NS}`, (e) => {
      this._handleKeydown(e);
    });

    $(document).on(`mousedown.${this.NS}`, (e) => {
      const container = this._getContainer();
      if (!container.length) return;

      const insideInput = this.inputGroup.is(e.target) || this.inputGroup.has(e.target).length;
      const insideContainer = container.is(e.target) || container.has(e.target).length;

      if (!insideInput && !insideContainer) {
        const insideGrid = $(e.target).closest(`#${this.gridId}`).length > 0 ||
          $(e.target).closest('.ui-jqgrid').length > 0;

        if (insideGrid) return;

        const val = this.element.val().trim();
        if (val && !this.selectedId) {
          this._handlePaste(val);
        } else if (!val) {
          this.close();
          this._toggleClearBtn();
          this.settings.onCancel(this.element);
        } else {
          this.close();
        }
      }
    });

    this.element.on(`blur.${this.NS}`, () => {
      setTimeout(() => {
        if (this.selectedId) return;
        if (this._isPasteHandled) {
          this._isPasteHandled = false;
          return;
        }
        if (this._pasteNotFound) return;
        if (this.isOpen) return;

        if (this.element.val().trim() && !this.currentValue) {
          this._handlePaste(this.element.val().trim());
        }
      }, 200);
    });
  }

  // =========================================================
  // HANDLE PASTE
  // =========================================================
  _handlePaste(searchValue) {
    const isLocal = this.settings.typeData === 'LOCAL';
    if (isLocal) {
      this._handlePasteLocal(searchValue);
    } else {
      this._handlePasteJSON(searchValue);
    }
  }

  _handlePasteLocal(searchValue) {
    const config = LookupRegistry.get(this.settings);
    const equalField = config?.filterPostData?.equalField
      || config?.sortname
      || this.settings.colModel.find(c => !c.hidden)?.name
      || 'id';

    let result = null;

    if (Array.isArray(this.settings.data) && this.settings.data.length > 0) {
      result = this.settings.data.find(row =>
        (row[equalField] || '').toString().toLowerCase() === searchValue.toLowerCase()
      );

      if (!result) {
        result = this.settings.data.find(row =>
          (row[equalField] || '').toString().toLowerCase().includes(searchValue.toLowerCase())
        );
      }
    }

    if (!result) {
      showDialog('warning', `DATA ${searchValue.toUpperCase()} TIDAK DITEMUKAN`);
      return;
    }

    const displayCol = this.settings.colModel.find(c => !c.hidden);
    const displayVal = displayCol ? (result[displayCol.name] || '') : '';

    this.element.val(displayVal);
    this.currentValue = displayVal;
    this.element.data('currentValue', displayVal);
    this.settings.onSelectRow(result, this.element);
    this._toggleClearBtn();
    this.close();
  }

  _handlePasteJSON(searchValue) {
    const config = LookupRegistry.get(this.settings);
    const equalField = config?.filterPostData?.equalField || config?.sortname || 'id';
    const url = this.settings.url || config?.url;

    const rules = [{
      field: equalField,
      op: 'cn',
      data: searchValue.toUpperCase()
    }];

    const postData = {
      ...config?.filterPostData,
      ...this.settings.postData,
      filters: JSON.stringify({ groupOp: 'EQUAL', rules }),
    };

    $('#processingLoader').removeClass('d-none');

    $.ajax({
      url: url,
      method: 'GET',
      dataType: 'JSON',
      headers: {
        Authorization: `Bearer ${ACCESS_TOKEN}`
      },
      data: postData,
      success: (response) => {
        if (!response.data || response.data.length === 0) {
          showDialog('warning', `DATA ${searchValue.toUpperCase()} TIDAK DITEMUKAN`);
          return;
        }

        const result = response.data[0];
        const displayCol = this.settings.colModel.find(c => !c.hidden);
        const displayVal = displayCol ? (result[displayCol.name] || '') : '';

        this.element.val(displayVal);
        this.currentValue = displayVal;
        this.element.data('currentValue', displayVal);
        this.settings.onSelectRow(result, this.element);
        this._toggleClearBtn();
        this.close();
      },
      error: (error) => {
        showDialog('warning', `Terjadi kesalahan: ${error.statusText || 'Unknown error'}`);
        this.element.val('');
        this._toggleClearBtn();
      }
    }).always(() => {
      $('#processingLoader').addClass('d-none');
    });
  }

  // =========================================================
  // KEYBOARD HANDLER
  // =========================================================
  _handleKeydown(e) {
    if (!this.isOpen) return;

    const grid = this._getGrid();
    if (!grid.length) return;

    const ids = grid.getDataIDs();
    const sel = grid.getGridParam('selrow');
    const idx = ids.indexOf(sel);

    const isFilterToolbar = $(e.target).closest('.ui-search-toolbar').length > 0 ||
      $(e.target).closest('.ui-jqgrid-hdiv').length > 0;

    const keyMap = {
      38: () => { // Arrow Up
        if (idx > 0) {
          const newId = ids[idx - 1];
          grid.setSelection(newId);
          this._scrollToSelected();
          // if (typeof this._previewSelectedRow === 'function') {
          //   this._previewSelectedRow(newId);
          // }
        }
        if (!isFilterToolbar) {
          this.element.focus();
        }
      },
      40: () => { // Arrow Down
        if (idx < ids.length - 1) {
          const newId = ids[idx + 1];
          grid.setSelection(newId);
          this._scrollToSelected();
          // if (typeof this._previewSelectedRow === 'function') {
          //   this._previewSelectedRow(newId);
          // }
        }
        if (!isFilterToolbar) {
          this.element.focus();
        }
      },
      13: () => { // Enter
        if (sel) this._selectRow(sel);
      },
      27: () => { // Escape
        this.close();
        this.element.val('');
        this._toggleClearBtn();
        this.settings.onCancel(this.element);
      },
      33: () => { },
      34: () => { },
      35: () => { },
      36: () => { },
    };

    if (keyMap[e.keyCode]) {
      e.preventDefault();
      e.stopPropagation();
      keyMap[e.keyCode]();
    }
  }

  // =========================================================
  // OPEN DROPDOWN (Rebuild)
  // =========================================================
  open(searchValue) {
    this.selectedId = null;
    LookupV6.closeAll(this.inputId);

    this._destroyContainer();

    $(document).off(`keydown.lv6_${this.inputId}`).on(`keydown.lv6_${this.inputId}`, (e) => {
      if (!this.isOpen) return;
      this._handleKeydown(e);
    });

    $(window).off(`resize.lv6_${this.inputId}`).on(`resize.lv6_${this.inputId}`, this._debounce(() => {
      if (this.isOpen) {
        this._repositionContainer();
      }
    }, 100));

    const modal = this.inputGroup.closest('.modal-body, .modal-content');
    if (modal.length) {
      modal.off(`scroll.lv6_${this.inputId}`).on(`scroll.lv6_${this.inputId}`, () => {
        this._repositionContainer();
      });
    }

    const container = this._buildContainer();
    this._buildGrid(container, searchValue);
    this._repositionContainer();

    this.isOpen = true;
  }

  // =========================================================
  // CLOSE DROPDOWN (Destroy)
  // =========================================================
  close() {
    $(document).off(`keydown.lv6_${this.inputId}`);
    $(window).off(`resize.lv6_${this.inputId}`);

    const modal = this.inputGroup.closest('.modal-body, .modal-content');
    if (modal.length) {
      modal.off(`scroll.lv6_${this.inputId}`);
    }

    this._destroyContainer();
    this.isOpen = false;
  }

  // =========================================================
  // DESTROY COMPONENT TOTAL
  // =========================================================
  destroy() {
    this.element.off(`.${this.NS}`);
    $(document).off(`.${this.NS}`);
    this.close();

    this.element.unwrap();
    this.btnClear.remove();
    this.btnToggle.remove();
    this.element.removeData('lookupV6');
  }

  // =========================================================
  // DESTROY CONTAINER DOM
  // =========================================================
  _destroyContainer() {
    const grid = this._getGrid();
    if (grid.length) {
      if (typeof abortGridLastRequest === 'function') {
        abortGridLastRequest(grid);
      }
    }
    const container = this._getContainer();
    if (container.length) {
      container.remove();
    }
  }

  // =======================================================
  // REPOSITION CONTAINER
  // =======================================================
  _repositionContainer() {
    const container = this._getContainer();
    if (!container.length) return;

    const inputOffset = this.inputGroup.offset();
    const top = inputOffset.top + this.inputGroup.outerHeight();
    const left = inputOffset.left;

    const inputWidth = this.inputGroup[0].getBoundingClientRect().width;
    const extendSize = parseInt(this.settings.extendSize || 0, 10);
    const dropWidth = inputWidth + extendSize;

    container.css({
      position: 'fixed',
      top: `${top}px`,
      left: `${left}px`,
      width: `${dropWidth}px`,
      minWidth: `${dropWidth}px`,
      maxWidth: `${dropWidth}px`,
    });

    const grid = this._getGrid();
    if (grid.length) {
      grid.jqGrid('setGridWidth', dropWidth - 2);
    }
  }

  // =========================================================
  // BUILD CONTAINER
  // =========================================================
  _buildContainer() {
    const inputWidth = this.inputGroup[0].getBoundingClientRect().width;
    const extendSize = parseInt(this.settings.extendSize || 0, 10);
    const dropWidth = inputWidth + extendSize;

    const insertTarget = $('body');
    const inputOffset = this.inputGroup.offset();
    const top = inputOffset.top + this.inputGroup.outerHeight();
    const left = inputOffset.left;

    const container = $(`
        <div id="${this.containerId}" class="lv6-container"
            style="position:fixed; z-index:99999; background:#fff;
                   box-sizing:border-box; border:1px solid #ced4da;
                   border-radius:0 0 4px 4px;
                   box-shadow:0 4px 8px rgba(0,0,0,0.15);
                   width:${dropWidth}px; min-width:${dropWidth}px; max-width:${dropWidth}px;
                   top:${top}px; left:${left}px; overflow:hidden;">
            
            <table id="${this.gridId}" class="lv6-grid"></table>
            
            <div class="lv6-loading" style="display:none; padding:10px 15px; background:#eef2f5; border-top:1px solid #ced4da; align-items:center;">
                <i class="fas fa-circle-notch fa-spin text-secondary mr-2" style="font-size: 14px;"></i>
                <span style="font-size:12px; font-weight:600; color:#495057; letter-spacing:0.5px;">LOADING DATA...</span>
            </div>
            
        </div>
    `).appendTo(insertTarget);

    container.on('focusin', function (e) {
      e.stopPropagation();
    });

    return container;
  }

  // =========================================================
  // BUILD JQGRID
  // =========================================================
  _buildGrid(container, searchValue) {
    this._lastSearch = searchValue;
    const gridEl = container.find(`#${this.gridId}`);
    const loadingEl = container.find('.lv6-loading');
    const config = LookupRegistry.get(this.settings, this.inputGroup.outerWidth());

    const isLocal = this.settings.typeData === 'LOCAL';
    const colModel = this.settings.colModel.length > 0
      ? this.settings.colModel
      : config.column;

    const url = this.settings.url || config.url;
    const sortname = this.settings.sortname || config.sortname || 'id';
    const sortorder = this.settings.sortorder || config.sortorder || 'asc';

    const mergedPostData = {
      ...config.filterPostData,
      ...this.settings.postData
    };

    gridEl.jqGrid({
      styleUI: 'Bootstrap4',
      iconSet: 'fontAwesome',
      datatype: isLocal ? 'local' : 'json',
      url: isLocal ? null : url,
      data: isLocal ? this._getFilteredData(searchValue) : [],
      postData: isLocal ? {} : this._buildPostData(searchValue, mergedPostData),
      colModel: colModel,
      sortname: sortname,
      sortorder: sortorder,
      sortable: true,
      height: 280,
      rowNum: isLocal ? 10000 : 20,
      autowidth: true,
      shrinkToFit: false,
      viewrecords: true,
      toolbar: [true, 'top'],
      prmNames: {
        sort: 'sortIndex',
        order: 'sortOrder',
        rows: 'limit'
      },
      jsonReader: {
        root: 'data',
        total: 'attributes.totalPages',
        records: 'attributes.totalRows',
      },
      loadBeforeSend: (jqXHR) => {
        loadingEl.css('display', 'flex');
        jqXHR.setRequestHeader('Authorization', `Bearer ${ACCESS_TOKEN}`);
        if (typeof setGridLastRequest === 'function') {
          setGridLastRequest(gridEl, jqXHR);
        }
      },
      onSelectRow: (id) => {
        this.selectedId = id;
      },
      onCellSelect: (id) => {
        this.selectedId = id;
        this._selectRow(id);
      },
      ondblClickRow: (id) => {
        this._selectRow(id);
      },
      gridComplete: () => {
        loadingEl.hide();

        const ids = gridEl.getDataIDs();
        if (ids.length > 0) {
          gridEl.setSelection(ids[0]);
        }

        const toolbarId = `t_${this.gridId}`;
        if ($(`#${toolbarId} label`).length === 0) {
          $(`#${toolbarId}`).append(`
              <label style="font-weight:normal; padding-left:10px; padding-top:1px;">
                  ${this.settings.title || 'Lookup'}
              </label>
          `);
        }

        if (this.settings.labelColumn === false) {
          gridEl.closest('.ui-jqgrid').find('.ui-jqgrid-hdiv').hide();
        }

        this._applyHighlight(gridEl);
      }
    });

    if (this.settings.filterToolbar) {
      gridEl.jqGrid('filterToolbar', {
        stringResult: true,
        searchOnEnter: false,
        defaultSearch: 'cn',
        groupOp: 'AND',
        beforeSearch: () => {
          if (typeof abortGridLastRequest === 'function') {
            abortGridLastRequest(gridEl);
          }
          this._isToolbarSearching = true;
          this._lastSearch = '';
          this.element.val('');
          this._toggleClearBtn();
        },
        afterSearch: () => {
          this._isToolbarSearching = false;
          if (typeof setHighlight === 'function') {
            setHighlight(gridEl);
          }
        }
      });

      if (this.settings.typeData === 'LOCAL') {
        const gridColModel = gridEl.jqGrid('getGridParam', 'colModel');
        gridColModel.forEach(cm => {
          const gsInput = $(`#gs_${cm.name}`);
          gsInput.on('input', () => {
            const filtered = this._filterToolbarLocal(gridColModel);
            gridEl.clearGridData()
              .setGridParam({ data: filtered })
              .trigger('reloadGrid');
          });
        });
      }
    }
  }

  // =========================================================
  // FILTER LOCAL DATA VIA TOOLBAR
  // =========================================================
  _filterToolbarLocal(colModel) {
    let filteredData = [...this.settings.data];

    colModel.forEach(cm => {
      const searchField = $(`#gs_${cm.name}`).val();
      if (!searchField || cm.search === false) return;

      const regex = new RegExp(`(${searchField})`, 'gi');
      filteredData = filteredData
        .filter(row => {
          const val = (row[cm.name] || '').toString().toLowerCase();
          return val.includes(searchField.toLowerCase());
        })
        .map(row => {
          const val = (row[cm.name] || '').toString();
          return {
            ...row,
            [cm.name]: val.replace(regex, `<span class="highlight">$1</span>`)
          };
        });
    });

    return filteredData;
  }

  // =========================================================
  // PREVIEW ROW — Autocomplete saat navigasi keyboard
  // =========================================================
  _previewSelectedRow(id) {
    const stripHtml = (str) => {
      if (!str) return '';
      // Decode entity dulu
      const decoded = $('<textarea>').html(str).val();
      // Baru strip tag
      return $('<div>').html(decoded).text().trim();
    };

    // ← Selalu ambil dari data original, bukan dari grid
    let rawRowData = null;

    if (Array.isArray(this.settings.data) && this.settings.data.length > 0) {
      rawRowData = this.settings.data.find(row => row.id == id);
    }

    if (!rawRowData) {
      // Fallback ke grid tapi strip HTML
      const gridData = this._getGrid().getRowData(id);
      rawRowData = {};
      Object.keys(gridData).forEach(key => {
        rawRowData[key] = stripHtml(gridData[key]);
      });
    }

    const displayCol = this.settings.colModel.find(c => !c.hidden);
    const rawVal = displayCol ? (rawRowData[displayCol.name] ?? '') : '';
    const displayVal = stripHtml(rawVal);

    this.element.val(displayVal);
  }

  // =========================================================
  // SELECT ROW
  // =========================================================
  _selectRow(id) {
    const grid = this._getGrid();

    const stripHtml = (str) => {
      if (!str || typeof str !== 'string') return str;
      return $('<textarea>').html(str).val()  // decode entity
        .replace(/<\/?[^>]+(>|$)/gi, '')    // strip tag
        .trim();
    };

    let rawRowData = null;

    if (Array.isArray(this.settings.data) && this.settings.data.length > 0) {
      rawRowData = this.settings.data.find(row => row.id == id);
    }

    if (!rawRowData) {
      const gridRowData = grid.getRowData(id);
      // ← Strip HTML dari semua field
      rawRowData = {};
      Object.keys(gridRowData).forEach(key => {
        rawRowData[key] = stripHtml(gridRowData[key]);
      });
    }

    const displayCol = this.settings.colModel.find(c => !c.hidden);
    const rawVal = displayCol ? (rawRowData[displayCol.name] ?? '') : '';
    const displayVal = stripHtml(rawVal);

    this.element.val(displayVal).trigger('change');
    this.currentValue = displayVal;
    this.element.data('currentValue', displayVal);

    this.settings.onSelectRow(rawRowData, this.element);
    this._toggleClearBtn();
    this.close();
  }

  // =========================================================
  // SET VALUE (Untuk Injeksi Manual)
  // =========================================================
  setValue(text) {
    this.element.val(text);
    this.currentValue = text;
    this.element.data('currentValue', text);
    this._toggleClearBtn();
  }

  // =========================================================
  // APPLY HIGHLIGHT
  // =========================================================
  _applyHighlight(gridEl) {
    gridEl.find('tbody td').each(function () {
      const cell = $(this);
      cell.find('span.highlight').each(function () {
        $(this).replaceWith($(this).text());
      });
      this.normalize();
    });

    const gridId = gridEl.attr('id');
    const searchText = this._lastSearch || '';

    if (searchText) {
      const escapedText = searchText.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      const regex = new RegExp(`(${escapedText})`, 'gi');

      const fields = this.settings.searching.length > 0
        ? this.settings.searching
        : this.settings.colModel
          .filter(c => c.search !== false && !c.hidden)
          .map(c => c.name);

      fields.forEach(field => {
        gridEl.find(`tbody td[aria-describedby="${gridId}_${field}"]`).each(function () {
          const cell = $(this);
          const text = cell.text();
          if (text.toLowerCase().includes(searchText.toLowerCase())) {
            cell.html(text.replace(regex, `<span class="highlight">$1</span>`));
          }
        });
      });
    }
    else if (this.settings.filterToolbar) {
      const postData = gridEl.jqGrid('getGridParam', 'postData');

      if (postData && postData.filters) {
        try {
          const filters = JSON.parse(postData.filters);

          if (filters.rules && filters.rules.length > 0) {
            filters.rules.forEach(rule => {
              const field = rule.field;
              const gsValue = rule.data;

              if (gsValue) {
                const escapedText = gsValue.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const regex = new RegExp(`(${escapedText})`, 'gi');

                gridEl.find(`tbody td[aria-describedby="${gridId}_${field}"]`).each(function () {
                  const cell = $(this);
                  const text = cell.text();
                  if (text.toLowerCase().includes(gsValue.toLowerCase())) {
                    cell.html(text.replace(regex, `<span class="highlight">$1</span>`));
                  }
                });
              }
            });
          }
        } catch (e) {
          console.error('Gagal memproses filter highlight:', e);
        }
      }
    }
  }

  // =========================================================
  // AUTO SELECT FIRST ROW
  // =========================================================
  _autoSelectFirst() {
    const grid = this._getGrid();
    if (!grid.length) return;

    const ids = grid.getDataIDs();
    if (ids.length > 0) {
      this._selectRow(ids[0]);
      this._toggleClearBtn();
    } else {
      this.close();
      this.settings.onCancel(this.element);
    }
  }

  // =========================================================
  // HELPERS
  // =========================================================
  _getContainer() {
    return $(`#${this.containerId}`);
  }

  _getGrid() {
    return $(`#${this.gridId}`);
  }

  _toggleClearBtn() {
    this.element.val()
      ? this.btnClear.show()
      : this.btnClear.hide();
  }

  _scrollToSelected() {
    const grid = this._getGrid();
    if (!grid.length) return;

    const selrow = grid.getGridParam('selrow');
    if (!selrow) return;
    const row = grid.find(`tr#${selrow}`)[0];
    if (row) row.scrollIntoView({ block: 'nearest' });
  }

  _getFilteredData(searchValue) {
    if (!searchValue) return this.settings.data;

    const fields = this.settings.searching.length > 0
      ? this.settings.searching
      : this.settings.colModel
        .filter(c => c.search !== false && !c.hidden)
        .map(c => c.name);

    return this.settings.data.filter(row =>
      fields.some(field => {
        const val = (row[field] || '').toString().toUpperCase();
        return val.includes(searchValue.toUpperCase());
      })
    );
  }

  _buildPostData(searchValue, mergedPostData) {
    if (!searchValue) return { ...mergedPostData };

    const fields = this.settings.searching.length > 0
      ? this.settings.searching
      : this.settings.colModel
        .filter(c => c.search !== false && !c.hidden)
        .map(c => c.name);

    const rules = fields.map(field => ({
      field, op: 'cn', data: searchValue.toUpperCase()
    }));

    return {
      ...mergedPostData,
      filters: JSON.stringify({ groupOp: 'OR', rules }),
      _search: true
    };
  }

  _debounce(fn, delay) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  // =========================================================
  // STATIC
  // =========================================================
  static closeAll(exceptInputId = null) {
    $('.lv6-container').each(function () {
      const id = $(this).attr('id');
      if (exceptInputId && id === `lv6-container-${exceptInputId}`) return;

      const targetInputId = id.replace('lv6-container-', '');
      const instance = $(`#${targetInputId}`).data('lookupV6');
      if (instance) {
        instance.close();
      } else {
        $(this).remove();
      }
    });
  }
}

// =========================================================
// JQUERY BRIDGE
// =========================================================
$.fn.lookupV6 = function (options) {
  return this.each(function () {
    if (!$(this).data('lookupV6')) {
      const instance = new LookupV6(this, options);
      $(this).data('lookupV6', instance);
    }
  });
};

$.fn.getLookupV6 = function () {
  return $(this).data('lookupV6');
};