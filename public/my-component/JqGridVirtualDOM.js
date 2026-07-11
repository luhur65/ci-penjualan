// --- JqGrid Lazy Loader ---
class LazyGridIDB {
  static _db = null;
  static IDB_NAME    = 'jqgrid_cache';
  static IDB_VERSION = 1;
  static IDB_STORE   = 'pages';

  static open() {
    if (LazyGridIDB._db) return Promise.resolve(LazyGridIDB._db);
    return new Promise((resolve, reject) => {
      const req = indexedDB.open(LazyGridIDB.IDB_NAME, LazyGridIDB.IDB_VERSION);
      req.onupgradeneeded = (e) => {
        const db = e.target.result;
        if (!db.objectStoreNames.contains(LazyGridIDB.IDB_STORE)) {
          const store = db.createObjectStore(LazyGridIDB.IDB_STORE, { keyPath: 'key' });
          store.createIndex('moduleName', 'moduleName', { unique: false });
          store.createIndex('timestamp',  'timestamp',  { unique: false });
        }
      };
      req.onsuccess = (e) => {
        LazyGridIDB._db = e.target.result;
        LazyGridIDB._db.onversionchange = () => {
          LazyGridIDB._db.close();
          LazyGridIDB._db = null;
        };
        resolve(LazyGridIDB._db);
      };
      req.onerror = (e) => reject(e.target.error);
    });
  }

  static tx(mode, callback) {
    return LazyGridIDB.open().then(db =>
      new Promise((resolve, reject) => {
        try {
          const tx = db.transaction(LazyGridIDB.IDB_STORE, mode);
          const store = tx.objectStore(LazyGridIDB.IDB_STORE);
          callback(store, resolve, reject);
          tx.onerror = (e) => reject(e.target.error);
        } catch (e) {
          reject(e);
        }
      })
    );
  }

  static savePage(moduleName, pageNumber, data, filterKey) {
    const key = LazyGridUtils.buildCacheKey(moduleName, pageNumber, filterKey);
    const entry = { key, moduleName, pageNumber, filterKey, data, timestamp: Date.now() };
    return LazyGridIDB.tx('readwrite', (store, resolve) => {
      const req = store.put(entry);
      req.onsuccess = () => resolve();
      req.onerror = () => {
        LazyGridIDB.clearModule(moduleName).then(() =>
          LazyGridIDB.tx('readwrite', (s2, res2) => {
            const r2 = s2.put(entry);
            r2.onsuccess = () => res2();
            r2.onerror = () => res2();
          })
        ).then(resolve).catch(resolve);
      };
    }).catch(e => console.warn('[IDB] savePage error:', e));
  }

  static loadPage(moduleName, pageNumber, filterKey, maxAgeMs) {
    const key = LazyGridUtils.buildCacheKey(moduleName, pageNumber, filterKey);
    return LazyGridIDB.tx('readonly', (store, resolve) => {
      const req = store.get(key);
      req.onsuccess = (e) => {
        const entry = e.target.result;
        if (!entry) { resolve(null); return; }
        if (Date.now() - entry.timestamp > maxAgeMs) {
          LazyGridIDB.open().then(db =>
            db.transaction(LazyGridIDB.IDB_STORE, 'readwrite')
              .objectStore(LazyGridIDB.IDB_STORE).delete(key)
          );
          resolve(null);
          return;
        }
        resolve(entry.data);
      };
      req.onerror = () => resolve(null);
    }).catch(() => null);
  }

  static clearModule(moduleName) {
    return LazyGridIDB.open().then(db =>
      new Promise(resolve => {
        const tx = db.transaction(LazyGridIDB.IDB_STORE, 'readwrite');
        const store = tx.objectStore(LazyGridIDB.IDB_STORE);
        const index = store.index('moduleName');
        const req = index.openCursor(IDBKeyRange.only(moduleName));
        req.onsuccess = (e) => {
          const cursor = e.target.result;
          if (!cursor) { resolve(); return; }
          cursor.delete();
          cursor.continue();
        };
        req.onerror = () => resolve();
      })
    ).catch(e => console.warn('[IDB] clearModule error:', e));
  }
}

class LazyGridUtils {
  static buildFilterKey(filters) {
    const str = (filters == null) ? '' : String(filters);
    let hash = 5381;
    for (let i = 0; i < str.length; i++) {
      hash = ((hash << 5) + hash) + str.charCodeAt(i);
      hash = hash & hash;
    }
    return (hash >>> 0).toString(16);
  }

  static buildCacheKey(moduleName, pageNumber, filterKey) {
    return moduleName + '_p' + pageNumber + '_f' + filterKey;
  }

  static getModuleNameFromApi(apiUrl) {
    if (!apiUrl) return 'unknown_module';
    const path = apiUrl.split('?')[0].replace(/^https?:\/\/[^\/]+/, '');
    return path.replace(/[^a-zA-Z0-9]/g, '_').replace(/^_+|_+$/g, '');
  }
}

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

    if (typeof options.onInit === 'function') {
      options.onInit(this);
    }

    // Initial load
    if (!this.paused) {
      let initialPostData = this.grid.jqGrid('getGridParam', 'postData');
      this.loadGridData(initialPostData, 1, this.rowsPerPage, 'down', 'reload');
    }
  }

  logState(methodName) {
  }

  hasFilterChanged() {
    var postData = this.grid.jqGrid('getGridParam', 'postData');
    var rawNewFilters = postData.filters;
    var sidx = postData.sidx || '';
    var sord = postData.sord || '';
    
    var newFilters = ((rawNewFilters === undefined || rawNewFilters === null) ? "" : rawNewFilters) + '|' + sidx + '|' + sord;
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
    if (this.prefetchedServerPages) {
      this.prefetchedServerPages.clear();
    }

    if (resetFilters) {
      this.currentFilters = null;
    }

    if (this.apiUrl) {
      const moduleName = LazyGridUtils.getModuleNameFromApi(this.apiUrl);
      LazyGridIDB.clearModule(moduleName);
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

  triggerPrefetchAhead(basePage, postData) {
    var self = this;
    var prefetchAhead = 3;
    for (let ahead = 1; ahead <= prefetchAhead; ahead++) {
      let target = basePage + ahead;
      if (self.totalPages && target > self.totalPages) break;
      if (self.cachedData[target]) continue;
      if (self.prefetchedServerPages.has(target)) continue;

      ((pg, delay) => {
        const filterKey = LazyGridUtils.buildFilterKey(self.currentFilters);
        const moduleName = LazyGridUtils.getModuleNameFromApi(self.apiUrl);
        
        LazyGridIDB.loadPage(moduleName, pg, filterKey, 15 * 60 * 1000).then(function(idbData) {
          if (idbData && idbData.data && idbData.data.length > 0) return;
          if (self.cachedData[pg] || self.prefetchedServerPages.has(pg)) return;
          
          self.prefetchedServerPages.add(pg);
          setTimeout(function() {
            self.loadGridData(postData, pg, self.rowsPerPage, 'down', 'page', null, true);
          }, delay);
        });
      })(target, (ahead - 1) * 300);
    }
  }

  loadGridData(postData, pageNumber, rowsCount, direction = 'down', proses = 'page', callback = null, onlyCache = false) {
    if (this.paused) return;

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
      var pd = this.grid.jqGrid('getGridParam', 'postData');
      this.currentFilters = ((pd.filters || "") + '|' + (pd.sidx || '') + '|' + (pd.sord || ''));
      this.loading = false;
    }

    if (proses === 'jump') {
      this.grid.clearGridData();
      // this.grid.parents('.ui-jqgrid-bdiv').find('.loading').show();

      var pd = this.grid.jqGrid('getGridParam', 'postData');
      this.currentFilters = ((pd.filters || "") + '|' + (pd.sidx || '') + '|' + (pd.sord || ''));
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

    const filterKey = LazyGridUtils.buildFilterKey(this.currentFilters);
    const moduleName = LazyGridUtils.getModuleNameFromApi(this.apiUrl);
    const maxAgeMs = 15 * 60 * 1000; // 15 menit cache

    const handleSuccess = (res) => {
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
      
      if (!onlyCache && direction === 'down') {
         self.triggerPrefetchAhead(serverPage, postData);
      }
    };

    const handleComplete = (xhr, textStatus) => {
      if (textStatus === 'abort') return;
      self.loading = false;
      $('#processingLoader').addClass('d-none');
      var freshNextPostData = self.grid.jqGrid('getGridParam', 'postData');
      self.processLoadingQueue(freshNextPostData, rowsCount);
    };

    const fetchFromServer = () => {
      if (typeof abortGridLastRequest === 'function') {
        abortGridLastRequest(self.grid);
      }

      var jqXHR = $.ajax({
        url: self.apiUrl,
        type: "GET",
        headers: {
          'Authorization': `Bearer ${self.accessToken}`
        },
        data: fullPostData,
        success: function (res) {
          if (res.data && res.data.length > 0) {
            LazyGridIDB.savePage(moduleName, serverPage, res, filterKey);
          }
          handleSuccess(res);
        },
        error: function (xhr) {
        },
        complete: handleComplete
      });

      if (typeof setGridLastRequest === 'function') {
        setGridLastRequest(self.grid, jqXHR);
      }
    };

    // --- IDB CACHE INTERCEPT ---
    if (proses === 'page') {
      LazyGridIDB.loadPage(moduleName, serverPage, filterKey, maxAgeMs).then(idbData => {
        if (idbData && idbData.data && idbData.data.length > 0) {
          handleSuccess(idbData);
          handleComplete(null, 'success');
        } else {
          fetchFromServer();
        }
      });
    } else {
      fetchFromServer();
    }
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
          } else {
            self.loadGridData(currentPostData, nextPage, self.rowsPerPage, 'down', 'page');
          }

          self.triggerPrefetchAhead(nextPage, currentPostData);
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

      self.lastScrollTop = bDivEl.scrollTop() <= 0 ? 0 : bDivEl.scrollTop();
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

    var scrollDiv = this.grid.parents('.ui-jqgrid-bdiv');
    var prevScroll = scrollDiv.scrollTop();
    var rowHeight = this.grid.find('tr[id]').height() || 30;

    if (direction === 'down') {
      for (let i = 0; i < excess; i++) {
        this.grid.jqGrid('delRowData', ids[i]);
      }
      this.minPageLoaded += pagesRemoved;
      
      // Adjust scroll position after removing top rows to prevent visual jump
      scrollDiv.scrollTop(prevScroll - (excess * rowHeight));
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