<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= getenv('app.name') ?: config('app.name') ?> | Dharma Bakti Situmorang</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">

  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/dist/css/adminlte-customized.min.css?version=' . config('App')->version) ?>">

  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">

  <!-- Select2 -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/plugins/select2/css/select2.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">

  <!-- JqGrid -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/jqgrid/570/css/ui.jqgrid-bootstrap4.css') ?>" />

  <!-- Jquery UI -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/jquery-ui/cupertino/jquery-ui.min.css') ?>">

  <!-- Dropzone -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/plugins/dropzone/min/dropzone.min.css') ?>">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/css/pager.css?version=' . config('App')->version) ?>">
  <link rel="stylesheet" href="<?= base_url('public/libraries/css/sidebar.css?version=' . config('App')->version) ?>">
  <link rel="stylesheet" href="<?= base_url('public/libraries/css/style.css?version=' . config('App')->version) ?>">
</head>

<body class="hold-transition sidebar-collapse layout-fixed">
  <div class="modal-loader d-none">
    <div class="modal-loader-content d-flex align-items-center justify-content-center">
      <img src="<?= base_url('public/libraries/images/loading-blue.gif?version=' . config('App')->version) ?>" rel="preload">
      Loading...
    </div>
  </div>

  <div class="loader" id="loader">
    <img src="<?= base_url('public/libraries/images/hour-glass.gif?version=' . config('App')->version) ?>" rel="preload">
    <span>Loading</span>
  </div>

  <div class="processing-loader d-none" id="processingLoader">
    <img src="<?= base_url('public/libraries/images/loading-color.gif?version=' . config('App')->version) ?>" rel="preload">
    <span>Processing</span>
  </div>

  <div class="wrapper">
    <?= $this->include('layouts/navbar') ?>
    <?= $this->include('layouts/sidebar') ?>

    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item active">
                  <?= ucfirst(service('uri')->getSegment(1)) ?>
                </li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <section class="content">
        <?= $this->renderSection('content') ?>
      </section>
    </div>

    <footer class="main-footer">
      <strong>Design &copy; by <a href="#">DHARMA BAKTI SITUMORANG</a>.</strong>
    </footer>
  </div>

  <!-- jQuery -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/jquery/jquery.min.js') ?>"></script>

  <!-- jQuery UI -->
  <script src="<?= base_url('public/libraries/jquery-ui/1.13.1/jquery-ui.min.js') ?>"></script>

  <!-- Highlight -->
  <script src="<?= base_url('public/libraries/js/highlight.js') ?>"></script>

  <!-- Bootstrap 4 -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

  <!-- ChartJS -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/chart.js/Chart.min.js') ?>"></script>

  <!-- overlayScrollbars -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>

  <!-- AdminLTE App -->
  <script src="<?= base_url('public/libraries/adminlte/dist/js/adminlte.js') ?>"></script>

  <!-- Select2 -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/select2/js/select2.min.js') ?>"></script>

  <!-- JqGrid -->
  <script src="<?= base_url('public/libraries/jqgrid/570/js/i18n/grid.locale-en.js') ?>" type="text/javascript"></script>
  <script src="<?= base_url('public/libraries/jqgrid/570/js/jquery.jqGrid.min.js') ?>" type="text/javascript"></script>

  <!-- Autonumeric -->
  <script src="<?= base_url('public/libraries/autonumeric/4.5.4/autonumeric.min.js') ?>" type="text/javascript"></script>

  <!-- Inputmask -->
  <script src="<?= base_url('public/libraries/inputmask/5.0.6/jquery.inputmask.min.js') ?>" type="text/javascript"></script>

  <!-- jQuery UI -->
  <script src="<?= base_url('public/libraries/jquery-ui/1.13.1/jquery-ui.min.js') ?>"></script>

  <!-- Dropzone -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/dropzone/min/dropzone.min.js') ?>"></script>

  <!-- Socket IO -->
  <!-- <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script> -->

  <script src="<?= base_url('public/libraries/js/extended-jqgrid.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/libraries/js/navbar.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/libraries/js/sidebar.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/libraries/js/script.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/my-component/LookupComponent.js?version=' . config('App')->version) ?>"></script>
  <!-- <script src="<?= base_url('public/my-component/Socket.js?version=' . config('App')->version) ?>"></script> -->
  <!-- Custom JS -->
  <script>
    const APP_URL = `<?= base_url() ?>`
    const API_URL = `<?= config('Api')->apiURL ?>`
    let ACCESS_TOKEN = `<?= session()->get('accessToken') ?>`;
    let addedRules = null;
    let isRefreshing = false; // Flag untuk mendeteksi apakah refresh sedang berlangsung
    let refreshSubscribers = []; // Menyimpan request yang menunggu token baru
    let lastGridRequest = null;

    // Handler Global 401 Error
    $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
      // Simpan error callback asli (kalau ada)
      let originalError = options.error;

      options.error = function(jqXHR, textStatus, errorThrown) {
        if (jqXHR.status === 401) {
          console.log("Intercept 401, refresh token jalan...");

          // Jangan panggil originalError → cegah popup unauthorized
          handleTokenExpired(originalOptions);
          return;
        }

        // Kalau bukan 401, teruskan ke handler asli
        if (typeof originalError === "function") {
          originalError(jqXHR, textStatus, errorThrown);
        }
      };
    });

    // Interceptor: Setup global handler untuk semua request AJAX
    $.ajaxSetup({
      beforeSend: function(xhr) {
        // Sertakan access token di setiap request
        xhr.setRequestHeader('Authorization', `Bearer ${ACCESS_TOKEN}`);
      },
      statusCode: {
        422: function(error) {
          if ($('#crudForm').length > 0 && !$('#crudForm').is(":hidden")) {

            $('.is-invalid').removeClass('is-invalid')
            $('.invalid-feedback').remove()

            setErrorMessages($('#crudForm'), error.responseJSON.errors);
          }
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {

        // showDialog(jqXHR,errorThrown)
        // Tangkap error koneksi (network error / server down)
        if (textStatus === 'timeout') {

          console.error(new Error(
            'Request timeout: server tidak merespon dalam waktu yang ditentukan.',
            'ajax-setup'));
          // captureError('Timeout: Server tidak merespon dalam 10 detik', 'ajax-setup');
        } else if (textStatus === 'error' && jqXHR.status === 0) {
          console.error(new Error('Network error: Gagal koneksi ke server (server mati / offline)',
            'ajax-setup'));
          // captureError('Koneksi gagal: Kemungkinan server tidak tersedia.', 'ajax-setup');
        } else {
          // Error umum lainnya
          console.error(`Error AJAX: ${textStatus} - ${errorThrown}`);
          // captureError(`Error AJAX: ${textStatus} - ${errorThrown}`, 'ajax-setup');
        }
      }
    });

    // Fungsi untuk logout user
    function logoutUser() {
      window.location.href = `${APP_URL}login`; // redirect ke halaman login
    }

    // Fungsi khusus untuk memperbarui access token dengan refresh token
    function refreshAccessToken() {
      return $.ajax({
        url: `${APP_URL}refresh`, // Endpoint untuk refresh token
        type: "get",
        dataType: "json",
        success: function(response) {
          // Berhasil mendapatkan token baru
          ACCESS_TOKEN = response.access_token;
        },
        error: function(xhr, status, error) {
          console.log("Gagal memperbarui token:", error);
          // kalau 401, paksa logout
          if (xhr.status === 401) {
            logoutUser();
          }
        }
      });
    }

    // Fungsi untuk menangani token yang kadaluarsa
    function handleTokenExpired(req) {
      return new Promise((resolve, reject) => {
        refreshSubscribers.push((newToken) => {
          req.headers = req.headers || {};
          req.headers['Authorization'] = `Bearer ${newToken}`;
          $.ajax(req).done(resolve).fail(reject);
        });

        if (!isRefreshing) {
          isRefreshing = true;

          refreshAccessToken()
            .done(function(newToken) {
              ACCESS_TOKEN = newToken.access_token;
              isRefreshing = false;

              refreshSubscribers.forEach(cb => cb(ACCESS_TOKEN));
              refreshSubscribers = [];
            })
            .fail(function() {
              isRefreshing = false;
              logoutUser();
            });
        }
      });
    }


    // Fungsi untuk menyimpan dan mengulang request terakhir yang gagal
    function retryLastRequest(req) {

      // Ulangi request
      $.ajax(req);
    }

    // Untuk refresh token async await
    function ajaxWithRefresh(options, slowThreshold = 2000) { // threshold dalam ms
      const start = performance.now();

      return $.ajax(options)
        .then(response => {
          const duration = performance.now() - start;
          if (duration > slowThreshold) {
            console.warn(`Request lambat: ${duration.toFixed(0)} ms`, options.url);
          }
          return response;
        })
        .catch(error => {
          const duration = performance.now() - start;
          if (duration > slowThreshold) {
            console.warn(`Request lambat (error): ${duration.toFixed(0)} ms`, options.url);
          }

          if (error.status === 401) {
            return handleTokenExpired(options);
          }

          return Promise.reject(error);
        });
    }
    // function ajaxWithRefresh(options) {
    //   return $.ajax(options).catch(error => {
    //     if (error.status === 401) {
    //       return handleTokenExpired(options); // ini mengembalikan Promise baru
    //     }
    //     return Promise.reject(error); // error selain 401 tetap reject
    //   });
    // }


    function createJqGrid(config = {}) {
      if (!config.gridId || !config.pagerId || !config.url || !config.colModel || !config.page) {
        console.error("gridId, pagerId, url, colModel, dan page wajib diisi!");
        return;
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
        // scroll: true, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
        // emptyrecords: 'Scroll to bottom to retrieve new page', // the message will be displayed at the bottom 
        jsonReader: {
          root: 'data',
          total: 'attributes.totalPages',
          records: 'attributes.totalRows',
        },
        loadBeforeSend: function(jqXHR) {
          jqXHR.setRequestHeader('Authorization', `Bearer ${ACCESS_TOKEN}`);
          setGridLastRequest($(this), jqXHR)
        }
      };

      // Merge default options + user config
      const finalOptions = $.extend(true, {}, defaultOptions, config.options || {});

      const grid = $(config.gridId).jqGrid(finalOptions);

      // default nav
      // grid.jqGrid('navGrid', config.pagerId, {
      //   edit: false,
      //   add: false,
      //   del: false,
      //   search: false,
      //   refresh: false
      // });

      // default filter toolbar
      grid.jqGrid("setLabel", "rn", "No.");
      grid.jqGrid('filterToolbar', {
        // autosearch: true,
        stringResult: true,
        searchOnEnter: false,
        defaultSearch: 'cn',
        groupOp: 'AND',
        disabledKeys: [17, 33, 34, 35, 36, 37, 38, 39, 40],
        beforeSearch: function() {
          abortGridLastRequest($(this))
          $('#left-nav').find(`button:not(#add)`).attr('disabled', 'disabled')
        },
      });

      // Tambahkan Global Search otomatis
      initGlobalSearch(grid, config);

      // Integrasi Virtual Scroll / Lazy Loader Manager jika dikonfigurasi
      if (config.lazyLoad) {
        // Kita timpa option grid jika menggunakan lazyLoad
        // Matikan default paging auto ajax jqGrid
        grid.jqGrid('setGridParam', {
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

        grid.data('lazyLoader', lazyLoader);
      }

      return grid;
    }

    function setHighlight(grid) {
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
              // Check if the cell contains a badge element
              if ($(this).find(".badge").length === 0) {
                $(this).highlight(rule.data);
              }
            });
          // .highlight(rule.data);
        });
      }
    }

    // Clear filter kolom
    function clearColumnSearch(grid) {
      grid.jqGrid("clearFilterToolbar");
      grid.jqGrid("setGridParam", {
        postData: {
          filters: ""
        }
      });
    }

    function initGlobalSearch(grid, config) {
      // const lazyLoading = config.lazyLoading || false;
      const url = config.url;

      // Tambahkan HTML kolom search
      $("#t_" + $.jgrid.jqID(grid[0].id)).html(
        $(
          `
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between w-100 px-2 py-1">
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

      // Event input
      $(document).on("input", `#${$.jgrid.jqID(grid[0].id)}_searchText`, function() {

        delay(function() {
          abortGridLastRequest(grid);
          clearColumnSearch(grid);

          let postData = grid.jqGrid("getGridParam", "postData");
          let colModel = grid.jqGrid("getGridParam", "colModel");

          let rules = [];
          let searchText = $(`#${$.jgrid.jqID(grid[0].id)}_searchText`).val();

          if (addedRules) rules.push(addedRules);

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

          grid.jqGrid("setGridParam", {
            search: true
          });

          // if (lazyLoading) {
          //   grid.jqGrid('clearGridData');

          //   loadGridData(
          //     grid[0].id,
          //     API_URL + url,
          //     ACCESS_TOKEN,
          //     postData,
          //     1,
          //     50,
          //     'down',
          //     'reload',
          //     () => setHighlight(grid)
          //   );

          // } else {
          grid.trigger("reloadGrid", [{
            page: 1,
            current: true
          }]);
          // }
          return false;
        }, 500);
      });
    }

    /**
     * Populate form fields dari data object
     * @param {jQuery} form - form element
     * @param {Object} data - object key:value untuk diisi ke form
     * @param {Array} specialFields - array field yang perlu simpan data-current-value
     */
    function populateForm(form, data, specialFields = []) {
      $.each(data, (key, value) => {
        const element = form.find(`[name="${key}"]`);
        if (!element.length) return;

        if (element.is('select') || element.is('input[type="checkbox"]')) {
          element.val(value).trigger('change');
        } else {
          element.val(value);
        }

        // Simpan current value jika ada di specialFields
        if (specialFields.includes(key)) {
          element.data('current-value', value);
        }
      });
    }

    /**
     * Ambil pesan error yang paling manusiawi
     * @param {Object} error - error object
     * @returns {string} - pesan error
     */
    function getErrorMessage(error) {
      if (!error) return 'Unknown error';

      if (error.status === 0) {
        return 'Koneksi kamu offline!';
      }

      if (error.responseJSON) {
        return (
          error.responseJSON.message ||
          error.responseJSON.messages?.error ||
          'Terjadi kesalahan pada server'
        );
      }

      if (error.responseText) {
        return error.responseText;
      }

      if (error.message) {
        return error.message;
      }

      return 'Terjadi kesalahan';
    }


    /** 
     * Membersihkan input search toolbar dari karakter khusus
     * @param {jQuery} grid - grid element
     * @param {Object} data - object key:value untuk diisi ke form
     * @param {Array} specialFields - array field yang perlu simpan data-current-value
     */
    $(document).on("input blur", ".ui-search-toolbar input", function() {
      let val = $(this).val();

      if (!val) return;

      val = val
        .replace(/&nbsp;/gi, " ") // HTML nbsp
        .replace(/\u00A0/g, " ") // unicode nbsp
        .replace(/[\u200B-\u200D]/g, "") // zero width chars
        .replace(/\uFEFF/g, "") // BOM
        .replace(/\s+/g, " ") // multiple space -> single
        .trim(); // remove leading/trailing space

      $(this).val(val);
    });

    /**
     * Hide or show button based on access rights
     * @param {Object} accessRights - object key:value for button ids and access rights
     */
    function PermissionButton(accessRights) {
      // Loop akan otomatis membaca kunci (add, edit, dll) dan nilainya (true/false)
      for (const [buttonId, hasAccess] of Object.entries(accessRights)) {
        if (hasAccess) {
          $(`#${buttonId}`).show();
        } else {
          $(`#${buttonId}`).hide();
        }
      }
    }

    // --- JqGrid Lazy Loader ---
    class JqGridLazyLoader {
      constructor(gridId, apiUrl, accessToken, options = {}) {
        this.gridId = gridId;
        this.grid = $(gridId);
        this.apiUrl = apiUrl;
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
            this.loading = true;
            this.grid.jqGrid('clearGridData');
            this.currentFilters = this.grid.jqGrid('getGridParam', 'postData').filters;
            this.minPageLoaded = pageNumber;
            this.maxPageLoaded = pageNumber;
            if (typeof this.lastScrollTop !== 'undefined') this.lastScrollTop = 0;
            this.loading = false;
        }

        if (this.cachedData[pageNumber] && proses === 'page') {
            if (!onlyCache) {
                this.renderFromCache(this.cachedData[pageNumber], direction, rowsCount, pageNumber);
            }
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
            if (this.prefetchedServerPages.has(serverPage)) return;
            this.prefetchedServerPages.add(serverPage);
        }

        this.loading = true;
        if (!onlyCache) $('#processingLoader').removeClass('d-none'); // Default loader di app.php

        var fullPostData = $.extend({}, postData, {
            page: serverPage,
            limit: limitToSend,
            sortIndex: this.grid.jqGrid('getGridParam', 'sortname'),
            sortOrder: this.grid.jqGrid('getGridParam', 'sortorder'),
            filters: this.grid.jqGrid('getGridParam', 'postData').filters
        });

        $.ajax({
            url: this.apiUrl,
            type: "GET",
            headers: { 'Authorization': `Bearer ${this.accessToken}` },
            data: fullPostData,
            success: function (res) {
                $('#processingLoader').addClass('d-none');

                // Adapter untuk jsonReader di app.php (attributes.totalRows)
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
                        self.renderFromCache(self.cachedData[pageNumber], direction, rowsCount, pageNumber);
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

                self.grid.jqGrid('setGridParam', { records: self.totalRecord });
                if (callback) callback();
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
        var existingIds = this.grid.jqGrid('getDataIDs');

        if (direction === 'down') {
            data.forEach(row => {
                if (!existingIds.includes(row.id.toString())) {
                    this.grid.jqGrid('addRowData', row.id, row, 'last');
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
                this.minPageLoaded = parseInt(currentPage);
                this.maxPageLoaded = parseInt(currentPage);
                this.grid.jqGrid('setGridParam', { page: parseInt(currentPage) });
                this.grid.jqGrid('setGridParam', { records: this.totalRecord });
            } else {
                this.currentViewPage = this.minPageLoaded || 1;
                this.minPageLoaded = this.maxPageLoaded;
                this.grid.jqGrid('setGridParam', { records: this.totalRecord });
                this.grid.jqGrid('setGridParam', { page: this.minPageLoaded });
            }
        }

        this.ensureValidSelection();
        if (typeof setHighlight === 'function') setHighlight(this.grid);
        this.refreshRowNumbers();
        this.updateGridInfoFast();
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
        var startRecord = (this.currentViewPage - 1) * this.rowsPerPage + 1;
        var actualRowsInPage = this.cachedData[this.currentViewPage] ? this.cachedData[this.currentViewPage].length : this.rowsPerPage;
        var endRecord = startRecord + actualRowsInPage - 1;

        if (this.totalRecord > 0 && endRecord > this.totalRecord) {
            endRecord = this.totalRecord;
        }

        // Dinamis target info handler
        let pagerId = this.grid.jqGrid('getGridParam', 'pager');
        if(pagerId) {
            // Update info container di pager
            let gridIdPrefix = this.grid.attr('id');
            $(`#${gridIdPrefix}InfoHandler`).text(`View ${startRecord} - ${endRecord} of ${this.totalRecord}`);
        }
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
    }
  </script>
  <?= $this->renderSection('scripts') ?>
</body>

</html>