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

  <!-- Bootstrap Datepicker -->
  <!-- <link rel="stylesheet" href="<?= base_url('public/libraries/bootstrap/dist/css/bootstrap.min.css') ?>"> -->

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
  <script src="<?= base_url('public/my-component/GridPreferenceManager.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/my-component/ColumnSettingsManager.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/my-component/JqGridVirtualDOM.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/my-component/DraftFormManager.js?version=' . config('App')->version) ?>"></script>
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

    var navigatorStorage = navigator.storage;

    navigatorStorage.estimate().then(({
      quota,
      usage
    }) => {
      console.log(`Used: ${usage} bytes`);
      console.log(`Total available: ${quota} bytes`);
    });

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

      const isLazy = config.lazyLoad === true;
      const userBeforeSearch = config.beforeSearch || function() {};

      const defaultOptions = {
        url: isLazy ? '' : API_URL + config.url,
        mtype: "GET",
        datatype: isLazy ? "local" : "JSON",
        styleUI: 'Bootstrap4',
        iconSet: 'fontAwesome',
        colModel: config.colModel,
        autowidth: config.autowidth ?? false,
        shrinkToFit: config.shrinkToFit ?? false,
        height: config.height ?? 375,
        rowNum: isLazy ? (config.lazyLoadOptions?.rowsPerPage || 50) : 10,
        pgtext: isLazy ? null : "{0}",
        pgbuttons: isLazy ? false : true,
        rownumWidth: 45,
        rowList: isLazy ? [] : [10, 20, 30],
        toolbar: [true, "top"],
        rownumbers: true,
        sortname: 'id',
        sortable: true,
        sortorder: 'asc',
        viewrecords: !isLazy,
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
          setGridLastRequest($(this), jqXHR);
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

      if (isLazy) {
        // $(config.pagerId).hide();

        // Buat instance LazyLoader
        let lazyLoader = new JqGridLazyLoader(
          config.gridId,
          API_URL + config.url,
          ACCESS_TOKEN,
          config.lazyLoadOptions || {}
        );

        // Simpan instance-nya ke memori elemen grid agar bisa dipanggil dari filterToolbar
        grid.data('lazyLoader', lazyLoader);
      }

      // default filter toolbar
      grid.jqGrid("setLabel", "rn", "No.");
      grid.jqGrid('filterToolbar', {
        stringResult: true,
        searchOnEnter: false,
        defaultSearch: 'cn',
        groupOp: 'AND',
        disabledKeys: [17, 33, 34, 35, 36, 37, 38, 39, 40],

        beforeSearch: function() {
          const gridEl = $(this);

          let postData = gridEl.jqGrid('getGridParam', 'postData') || {};

          if (!postData.filters || postData.filters === '') {
            delete postData.filters;
            postData._search = false;
          }

          // custom param TANPA overwrite
          if (config.extraPostData) {
            const extra = typeof config.extraPostData === 'function' ?
              config.extraPostData() :
              config.extraPostData;

            postData = {
              ...postData,
              ...extra
            };
          }

          // simpan balik
          gridEl.jqGrid('setGridParam', {
            page: 1,
            postData: postData
          });

          // clear global search kalau ada
          if (config.clearGlobalSearch) {
            config.clearGlobalSearch(gridEl);
          }

          // HANDLE LAZY
          if (isLazy) {
            let loader = gridEl.data('lazyLoader');
            if (loader) {
              loader.loadGridData(postData, 1, loader.rowsPerPage, 'down', 'reload');
            }
            return true;
          }

          // call custom user logic terakhir
          return userBeforeSearch.call(this);
        }
      });

      $.jgrid.extend({
        // Kita tidak lagi butuh pageNumber atau rowIndex lokal. Kita hanya butuh offset absolut!
        jumpToAbsoluteOffset: function(absoluteOffset, rowIdToSelect) {
          return this.each(function() {
            var grid = $(this);

            // 1. Dapatkan tinggi 1 baris (Misal 30px atau 32px, sesuaikan dengan aslinya)
            // Kita ambil dari baris pertama yang ada di layar sebagai sampel
            var rowHeight = grid.find('tr.jqgrow').first().height() || 32;

            // 2. KALKULASI MATEMATIKA (Tanpa perlu mencari elemen <tr>)
            // Piksel target = posisi absolut dikali tinggi baris
            var targetPixel = absoluteOffset * rowHeight;

            // 3. LOMPATAN BUTA! Putar scrollbar langsung ke target piksel.
            grid.parents('.ui-jqgrid-bdiv').scrollTop(targetPixel);

            // 4. BIAKAN MESIN BEKERJA
            // Saat scrollTop berubah, VirtualScrollController (CCTV) akan otomatis mendeteksi, 
            // menembak AJAX ke Backend, dan merender 50 baris di koordinat tersebut.

            // 5. TUNGGU & SOROT
            // Kita beri jeda sejenak (misal 500ms) agar AJAX selesai dan elemen <tr> dirender, 
            // baru kita berikan efek sorotan biru (setSelection).
            setTimeout(() => {
              if (rowIdToSelect) {
                grid.jqGrid('setSelection', rowIdToSelect, true);
              }
            }, 800); // Sesuaikan durasi timeout dengan rata-rata kecepatan API Anda
          });
        }
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

          if (lazyLoading) {
            grid.jqGrid('clearGridData');

            let loader = grid.data('lazyLoader');
            if (loader) {
              loader.loadGridData(postData, 1, loader.rowsPerPage, 'down', 'reload');
            }
            return true;

          } else {
            grid.trigger("reloadGrid", [{
              page: 1,
              current: true
            }]);
          }
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
    // $(document).on("input blur", ".ui-search-toolbar input", function() {
    //   let val = $(this).val();

    //   if (!val) return;

    //   val = val
    //     .replace(/&nbsp;/gi, " ") // HTML nbsp
    //     .replace(/\u00A0/g, " ") // unicode nbsp
    //     .replace(/[\u200B-\u200D]/g, "") // zero width chars
    //     .replace(/\uFEFF/g, "") // BOM
    //     .replace(/\s+/g, " ") // multiple space -> single
    //     .trim(); // remove leading/trailing space

    //   $(this).val(val);
    // });

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
  </script>
  <?= $this->renderSection('scripts') ?>
</body>

</html>