// --- JqGrid Lazy Loader ---
class JqGridLazyLoader {
  constructor(gridId, apiUrl, accessToken, options = {}) {
    this.gridId = gridId;
    this.grid = $(gridId);
    this.apiUrl = apiUrl || this.grid.jqGrid('getGridParam', 'url');
    this.accessToken = accessToken;

    this.page = 1;
    this.totalPages = 1;
    this.totalRecord = 0;
    this.loading = false;
    this.loadingQueue = [];
    this.cachedData = {};
    this.minPageLoaded = 1;
    this.maxPageLoaded = 1;
    this.lastScrollTop = 0;
    this.currentFilters = null;

    this.WINDOW_PAGES = options.windowPages || 3;
    this.rowsPerPage = options.rowsPerPage || 50;
    this.gapPage = options.gapPage || 30;

    this.currentViewPage = 1;
    this.prefetchedServerPages = new Set();

    this.loadingDirection = null; // 'up', 'down', atau null

    this.throttle = function (func, limit) {
      let inThrottle;
      let lastArgs;
      return function () {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
          func.apply(context, args);
          inThrottle = true;
          setTimeout(() => {
            inThrottle = false;
            if (lastArgs) {
              func.apply(context, lastArgs);
              lastArgs = null;
            }
          }, limit);
        } else {
          lastArgs = args;
        }
      }
    };

    this.setupLazyLoadScrollHandler();

    // Initial load
    let initialPostData = this.grid.jqGrid('getGridParam', 'postData');
    this.loadGridData(initialPostData, 1, this.rowsPerPage, 'down', 'page');

    if (typeof options.onInit === 'function') {
      options.onInit(this);
    }
  }

  logState(methodName) {
  }

  hasFilterChanged() {
    var rawNewFilters = this.grid.jqGrid('getGridParam', 'postData').filters;
    var newFilters = (rawNewFilters === undefined || rawNewFilters === null) ? "" : rawNewFilters;
    var oldFilters = (this.currentFilters === undefined || this.currentFilters === null) ? "" : this.currentFilters;

    if (this.currentFilters === null) {
      this.currentFilters = newFilters;
      return false;
    }

    if (oldFilters !== newFilters) {
      this.currentFilters = newFilters;
      return true;
    }
    return false;
  }

  resetGridState(resetFilters = false) {
    this.cachedData = {};
    this.minPageLoaded = 1;
    this.maxPageLoaded = 1;
    this.lastScrollTop = 0;
    this.loading = false;
    this.loadingQueue = [];

    if (resetFilters) {
      this.currentFilters = null;
    }

    this.grid.jqGrid('clearGridData');

    this.updateGridInfoFast();
  }

  processLoadingQueue(postData, rowsCount) {
    if (this.loadingQueue.length && !this.loading) {
      var serverPage = this.loadingQueue.shift();
      var pagesPerFetch = rowsCount === this.rowsPerPage ? 1 : 3;
      var virtualPage = (serverPage - 1) * pagesPerFetch + 1;

      this.loadGridData(postData, virtualPage, rowsCount, 'down', 'page');
    }
  }

  loadGridData(postData, pageNumber, rowsCount, direction = 'down', proses = 'page', callback = null, onlyCache = false) {
    var self = this;

    if (proses !== 'page' && !onlyCache && proses !== 'reload' && proses !== 'jump' && this.minPageLoaded > 0 && this.hasFilterChanged()) {
      this.resetGridState();
      pageNumber = 1;
      direction = 'down';
      proses = 'reload';
    }

    if (proses === 'reload') {
      this.resetGridState();
      this.minPageLoaded = pageNumber;
      this.maxPageLoaded = pageNumber;
      this.currentFilters = this.grid.jqGrid('getGridParam', 'postData').filters;
      this.loading = false;
    }

    if (proses === 'jump') {
      this.grid.clearGridData();
      // this.grid.parents('.ui-jqgrid-bdiv').find('.loading').show();

      this.currentFilters = this.grid.jqGrid('getGridParam', 'postData').filters;
      this.minPageLoaded = pageNumber;
      this.maxPageLoaded = pageNumber;
      this.lastScrollTop = 0;

      if (this.cachedData[pageNumber]) {
        this.renderFromCache(this.cachedData[pageNumber], 'jump', rowsCount, pageNumber);
        this.loadingDirection = null;
        if (callback) callback();
        return;
      }
    }

    if (this.cachedData[pageNumber] && proses === 'page') {
      if (!onlyCache) {
        this.renderFromCache(this.cachedData[pageNumber], direction, rowsCount, pageNumber);
      }
      this.loadingDirection = null;
      if (callback) callback();
      return;
    }

    let pagesPerFetch = (pageNumber === 1) ? 3 : 1;
    let serverPage = Math.ceil(pageNumber / pagesPerFetch);
    let limitToSend = rowsCount * pagesPerFetch;

    if (this.loading && !onlyCache) {
      if (!this.loadingQueue.includes(serverPage)) this.loadingQueue.push(serverPage);
      return;
    }

    if (onlyCache) {
      if (this.prefetchedServerPages.has(serverPage)) {
        return;
      }
      this.prefetchedServerPages.add(serverPage);
    }

    this.loading = true;
    if (!onlyCache) {
      this.loadingDirection = direction;
      this.updateGridInfoFast();
      this.grid.parents('.ui-jqgrid-bdiv').find('.loading').show();
    }

    var fullPostData = $.extend({}, postData, {
      page: serverPage,
      limit: limitToSend,
    });

    $.ajax({
      url: this.apiUrl,
      type: "GET",
      headers: {
        'Authorization': `Bearer ${this.accessToken}`
      },
      data: fullPostData,
      success: function (res) {
        self.grid.parents('.ui-jqgrid-bdiv').find('.loading').hide();

        self.totalRecord = (res.attributes && res.attributes.totalRows) || res.records || 0;
        self.totalPages = Math.ceil(self.totalRecord / rowsCount);

        let dataArray = res.data || [];
        if (!dataArray.length) {
          self.loading = false;
          return;
        }

        let startClientPage = (serverPage - 1) * pagesPerFetch + 1;
        for (let i = 0; i < pagesPerFetch; i++) {
          let currentVirtualPage = startClientPage + i;
          let startIdx = i * rowsCount;
          let endIdx = startIdx + parseInt(rowsCount);
          let chunkData = dataArray.slice(startIdx, endIdx);

          if (chunkData.length > 0) {
            self.cachedData[currentVirtualPage] = chunkData;
          }
        }

        if (!onlyCache) {
          if (self.cachedData[pageNumber]) {
            let renderDirection = (proses === 'jump') ? 'jump' : direction;
            self.renderFromCache(self.cachedData[pageNumber], renderDirection, rowsCount, pageNumber);
          }
        }

        if (proses === 'jump' && dataArray.length < rowsCount && serverPage > 1) {
          var bDiv = self.grid.parents(".ui-jqgrid-bdiv");
          var viewHeight = bDiv.height();
          var rowHeight = self.grid.find('tr.jqgrow').first().height() || 30;
          var contentHeight = dataArray.length * rowHeight;

          if (contentHeight < viewHeight) {
            setTimeout(() => {
              self.loadGridData(postData, serverPage - 1, rowsCount, 'up', 'page');
            }, 100);
          }
        }

        self.grid.jqGrid('setGridParam', {
          records: self.totalRecord
        });
        if (callback) callback();
      },
      error: function (xhr) {
      },
      complete: function () {
        self.loading = false;
        $('#processingLoader').addClass('d-none');
        var freshNextPostData = self.grid.jqGrid('getGridParam', 'postData');
        self.processLoadingQueue(freshNextPostData, rowsCount);
      }
    });
  }

  setupLazyLoadScrollHandler() {
    var self = this;
    var bDiv = this.grid.parents(".ui-jqgrid-bdiv");

    var throttledScroll = this.throttle(function () {
      var bDivEl = $(this);
      var scrollTop = bDivEl.scrollTop();
      var viewHeight = bDivEl.height();
      var tableHeight = bDivEl.find("table").height();
      var rowHeight = self.grid.find('tr[id]').height() || 30;
      var currentPostData = self.grid.jqGrid('getGridParam', 'postData');
      var detectedPage = self.detectCurrentViewPage();

      if (detectedPage !== self.currentViewPage) {
        self.currentViewPage = detectedPage;
        self.updateGridInfoFast();
      }

      if (self.lastScrollTop === 0 && scrollTop > rowHeight * 10) {
        self.lastScrollTop = scrollTop;
        return;
      }

      if (scrollTop > self.lastScrollTop && scrollTop + viewHeight >= tableHeight - (self.gapPage * rowHeight)) {
        var nextPage = self.maxPageLoaded + 1;
        if (nextPage <= self.totalPages) {
          if (self.cachedData[nextPage]) {
            self.renderFromCache(self.cachedData[nextPage], 'down', self.rowsPerPage, nextPage);

            var pagePlusOne = nextPage + 1;
            var pagePlusTwo = nextPage + 2;
            var targetPrefetch = null;

            if (!self.cachedData[pagePlusOne]) {
              targetPrefetch = pagePlusOne;
            } else if (!self.cachedData[pagePlusTwo]) {
              targetPrefetch = pagePlusTwo;
            }

            if (!self.loading && targetPrefetch && targetPrefetch <= self.totalPages && !self.cachedData[targetPrefetch]) {
              self.loadGridData(currentPostData, targetPrefetch, self.rowsPerPage, 'down', 'page', null, true);
            }
          } else {
            self.loadGridData(currentPostData, nextPage, self.rowsPerPage, 'down', 'page');
          }
        }
      }

      var triggerThreshold = self.gapPage * rowHeight;
      if (self.maxPageLoaded === self.minPageLoaded) {
        triggerThreshold = tableHeight * 0.8;
      }

      if (scrollTop < self.lastScrollTop && scrollTop <= triggerThreshold && self.minPageLoaded > 1) {
        var prevPage = self.minPageLoaded - 1;
        if (self.cachedData[prevPage]) {
          self.renderFromCache(self.cachedData[prevPage], 'up', self.rowsPerPage, prevPage);
        } else {
          self.loadGridData(currentPostData, prevPage, self.rowsPerPage, 'up', 'page');
        }
      }

      self.lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
      self.updateGridInfoFast();

    }, 150);

    bDiv.off("scroll.virtual").on("scroll.virtual", throttledScroll);
  }

  renderFromCache(data, direction, rowsPerPage, currentPage) {
    var self = this;
    var existingIds = this.grid.jqGrid('getDataIDs');

    if (direction === 'down') {
      let added = 0;
      data.forEach(row => {
        if (!existingIds.includes(row.id.toString())) {
          this.grid.jqGrid('addRowData', row.id, row, 'last');
          added++;
        }
      });
      let validPage = currentPage || this.maxPageLoaded;
      this.maxPageLoaded = Math.max(this.maxPageLoaded, validPage);
      this.currentViewPage = currentPage;
      this.trimGridRows('down', rowsPerPage);
    }

    if (direction === 'up') {
      var scrollDiv = this.grid.parents('.ui-jqgrid-bdiv');
      var prevScroll = scrollDiv.scrollTop();
      var addedCount = 0;

      for (let i = data.length - 1; i >= 0; i--) {
        if (!existingIds.includes(data[i].id.toString())) {
          this.grid.jqGrid('addRowData', data[i].id, data[i], 'first');
          addedCount++;
        }
      }
      this.minPageLoaded--;
      this.currentViewPage = currentPage;
      this.trimGridRows('up', rowsPerPage);

      if (addedCount > 0) {
        var rowHeight = this.grid.find('tr[id]').height() || 30;
        scrollDiv.scrollTop(prevScroll + (addedCount * rowHeight));
      }
    }

    if (direction === 'jump' || direction === 'reload') {

      this.grid.jqGrid('clearGridData');

      data.forEach(row => {
        this.grid.jqGrid('addRowData', row.id, row, 'last');
      });

      if (currentPage) {
        this.currentViewPage = parseInt(currentPage);
        this.minPageLoaded = this.currentViewPage;
        this.maxPageLoaded = this.currentViewPage;
        this.grid.jqGrid('setGridParam', {
          page: this.currentViewPage,
          records: this.totalRecord
        });
      }

      var bDiv = this.grid.parents('.ui-jqgrid-bdiv');
      bDiv.scrollTop(0);
      this.lastScrollTop = 0;
    }

    this.ensureValidSelection();
    if (typeof setHighlight === 'function') setHighlight(this.grid);
    this.refreshRowNumbers();

    this.loadingDirection = null;
    this.updateGridInfoFast();

    this.grid.parents('.ui-jqgrid-bdiv').find('.loading').hide();
  }

  trimGridRows(direction, rowsPerPage) {
    var ids = this.grid.jqGrid('getDataIDs');
    var maxRows = this.WINDOW_PAGES * rowsPerPage;

    if (ids.length <= maxRows) return;

    var excess = ids.length - maxRows;
    var pagesRemoved = Math.floor(excess / rowsPerPage);
    if (pagesRemoved < 1) pagesRemoved = 1;

    if (direction === 'down') {
      for (let i = 0; i < excess; i++) {
        this.grid.jqGrid('delRowData', ids[i]);
      }
      this.minPageLoaded += pagesRemoved;
    }

    if (direction === 'up') {
      for (let i = ids.length - 1; i >= ids.length - excess; i--) {
        this.grid.jqGrid('delRowData', ids[i]);
      }
      this.maxPageLoaded -= pagesRemoved;
    }

    this.refreshRowNumbers();
    this.ensureValidSelection();
  }

  refreshRowNumbers() {
    var ids = this.grid.jqGrid('getDataIDs');
    if (ids.length === 0) return;

    var lastValidNumber = 0;
    for (var i = 0; i < ids.length; i++) {
      var currentId = ids[i];
      var foundIdentity = false;
      var calculatedNumber = 0;

      for (var pageKey in this.cachedData) {
        if (this.cachedData.hasOwnProperty(pageKey)) {
          var pageData = this.cachedData[pageKey];
          var indexInPage = pageData.findIndex(row => row.id.toString() === currentId.toString());

          if (indexInPage !== -1) {
            var realPage = parseInt(pageKey);
            calculatedNumber = (realPage - 1) * this.rowsPerPage + indexInPage + 1;
            foundIdentity = true;
            lastValidNumber = calculatedNumber;
            break;
          }
        }
      }

      if (!foundIdentity) {
        if (lastValidNumber > 0) {
          calculatedNumber = lastValidNumber + 1;
        } else {
          var fallbackPage = this.minPageLoaded || 1;
          calculatedNumber = (fallbackPage - 1) * this.rowsPerPage + (i + 1);
        }
        lastValidNumber = calculatedNumber;
      }

      if (this.totalRecord > 0 && calculatedNumber > this.totalRecord) {
        calculatedNumber = this.totalRecord;
      }

      this.grid.jqGrid('setCell', currentId, 'rn', calculatedNumber);
    }
    this.updateGridInfoFast();
  }

  ensureValidSelection() {
    var ids = this.grid.jqGrid('getDataIDs');
    var sel = this.grid.jqGrid('getGridParam', 'selrow');
    if (!sel || !ids.includes(sel.toString())) {
      if (ids.length) {
        this.grid.jqGrid('setSelection', ids[0]);
      }
    }
  }

  updateGridInfoFast() {
    let gridIdPrefix = this.grid.attr('id');
    let infoEl = $(`#${gridIdPrefix}InfoHandler`);

    if (this.loading && this.loadingDirection) {
      infoEl.html('<i class="fa fa-spinner fa-spin"></i> Loading...');
      return;
    }

    if (this.totalRecord === 0) {
      infoEl.html('<i class="fa fa-spinner fa-spin"></i> Loading...');
      return;
    }

    var startRecord = (this.currentViewPage - 1) * this.rowsPerPage + 1;
    var actualRowsInPage = this.cachedData[this.currentViewPage]
      ? this.cachedData[this.currentViewPage].length
      : this.rowsPerPage;
    var endRecord = startRecord + actualRowsInPage - 1;

    if (endRecord > this.totalRecord) endRecord = this.totalRecord;

    infoEl.text(`View ${startRecord} - ${endRecord} of ${this.totalRecord}`);
  }

  detectCurrentViewPage() {
    var scrollDiv = this.grid.parents('.ui-jqgrid-bdiv');
    var scrollTop = scrollDiv.scrollTop();
    var rowHeight = this.grid.find('tr[id]').height() || 30;

    var visibleRowIndex = Math.floor(scrollTop / rowHeight);
    var recordNumber = (this.minPageLoaded - 1) * this.rowsPerPage + visibleRowIndex + 1;
    var page = Math.ceil(recordNumber / this.rowsPerPage);

    if (page < 1) page = 1;
    if (page > this.totalPages) page = this.totalPages;

    return page;
  }

  isReady() {
    return !this.loading && this.totalRecord > 0;
  }
}