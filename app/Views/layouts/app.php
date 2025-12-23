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
  <link rel="stylesheet" href="<?= base_url('public/libraries/jqgrid/582/css/ui.jqgrid-bootstrap4.css') ?>" />

  <!-- Jquery UI -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/jquery-ui/1.13.1/jquery-ui.min.css') ?>">

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

  <!-- Highlight -->
  <script src="<?= base_url('public/libraries/js/highlight.js') ?>"></script>

  <!-- Bootstrap 4 -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/bootstrap/js/bootstrap.min.js') ?>"></script>

  <!-- ChartJS -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/chart.js/Chart.min.js') ?>"></script>

  <!-- overlayScrollbars -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>

  <!-- AdminLTE App -->
  <script src="<?= base_url('public/libraries/adminlte/dist/js/adminlte.js') ?>"></script>

  <!-- Select2 -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/select2/js/select2.min.js') ?>"></script>

  <!-- JqGrid -->
  <script src="<?= base_url('public/libraries/jqgrid/582/js/i18n/grid.locale-en.js') ?>" type="text/javascript"></script>
  <script src="<?= base_url('public/libraries/jqgrid/582/js/jquery.jqGrid.min.js') ?>" type="text/javascript"></script>

  <!-- Autonumeric -->
  <script src="<?= base_url('public/libraries/autonumeric/4.5.4/autonumeric.min.js') ?>" type="text/javascript"></script>

  <!-- Inputmask -->
  <script src="<?= base_url('public/libraries/inputmask/5.0.6/jquery.inputmask.min.js') ?>" type="text/javascript"></script>

  <!-- jQuery UI -->
  <script src="<?= base_url('public/libraries/jquery-ui/1.13.1/jquery-ui.min.js') ?>"></script>

  <!-- Dropzone -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/dropzone/min/dropzone.min.js') ?>"></script>

  <script src="<?= base_url('public/libraries/js/extended-jqgrid.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/libraries/js/navbar.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/libraries/js/sidebar.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/libraries/js/script.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/my-component/LookupComponent.js?version=' . config('App')->version) ?>"></script>
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
        xhr.setRequestHeader('Authorization', `'Bearer ${ACCESS_TOKEN}`);
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

        success: function(response) {
          // Berhasil mendapatkan token baru
          ACCESS_TOKEN = response.access_token;
        },
        error: function(xhr, status, error) {
          console.log("Gagal memperbarui token:", error);
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
    function ajaxWithRefresh(options) {
      return $.ajax(options).catch(error => {
        if (error.status === 401) {
          return handleTokenExpired(options); // ini mengembalikan Promise baru
        }
        return Promise.reject(error); // error selain 401 tetap reject
      });
    }


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
        autowidth: true,
        // height: 'auto',
        height: 350,
        rowNum: 10,
        // rowList: [10, 20, 30],
        rownumbers: true,
        sortname: 'id',
        sortorder: 'asc',
        viewrecords: true,
        gridview: true,
        page: config.page || 1,
        pager: config.pagerId,
        scroll: true, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
        emptyrecords: 'Scroll to bottom to retrieve new page', // the message will be displayed at the bottom 
        jsonReader: {
          root: 'data',
          total: 'attributes.totalPages',
          records: 'attributes.totalRows',
        },
        loadBeforeSend: function(jqXHR) {
          // if ($(this).jqGrid("getGridParam", "page") > $(this).jqGrid("getGridParam", "lastpage")) {
          //   return false;
          // }
          jqXHR.setRequestHeader('Authorization', `Bearer ${ACCESS_TOKEN}`);
        }
      };

      // Merge default options + user config
      const finalOptions = $.extend(true, {}, defaultOptions, config.options || {});

      $.jgrid.extend({
        nextPageIfPossible: function() {
          var grid = this[0];
          var p = grid.p;

          // Jika sudah di lastpage, JANGAN PERNAH naikan page
          if (p.page >= p.lastpage) {
            return false;
          }

          // default behaviour
          p.page++;
          $(grid).trigger("reloadGrid");
        }
      });

      const grid = $(config.gridId).jqGrid(finalOptions);

      // default nav
      grid.jqGrid('navGrid', config.pagerId, {
        edit: false,
        add: false,
        del: false,
        search: false,
        refresh: false
      });

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
          // clearGlobalSearch($('#jqGrid'))
        },
      });

      // Tambahkan Global Search otomatis
      initGlobalSearch(grid, config);

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
      // console.log("#t_" + $.jgrid.jqID(grid[0].id))
      $(".ui-jqgrid-titlebar").html(
        $(`<form class="form-inline">
            <div class='ui-jqgrid-titlebar'>
              <label for="${$.jgrid.jqID(
                    grid[0].id
                    )}_searchText" style="font-weight: normal !important;">
                  Search :
                  <input type="text" class="form-control form-control-sm global-search ml-3" id="${$.jgrid.jqID(
                    grid[0].id
                    )}_searchText" placeholder="Search" autocomplete="off">
              </label>
            </div>
          </form>`)
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
  </script>
  <?= $this->renderSection('scripts') ?>
</body>

</html>