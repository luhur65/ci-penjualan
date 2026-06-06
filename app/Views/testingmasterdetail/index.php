<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
  <!-- MASTER GRID: tbl_penjualan -->
  <table id="jqGrid"></table>
  <div id="jqGridPager"></div>

  <!-- DIVIDER -->
  <div class="mt-3 mb-1 d-flex align-items-center" id="detailGridHeader" style="display:none!important">
    <!-- <span class="font-weight-bold text-secondary mr-2">
      <i class="fa fa-list-ul"></i> Detail Penjualan:
    </span>
    <span id="detailGridInfo" class="badge badge-info ml-1"></span>
    <span id="detailGridTotal" class="badge badge-success ml-2"></span> -->
  </div>

  <!-- DETAIL GRID: tbl_penjualan_detail -->
  <table id="jqGridDetail"></table>
  <div id="jqGridDetailPager"></div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts'); ?>
<?= $this->include('testingmasterdetail/modal') ?>

<script>
  /* ============================================================
   *  GLOBAL STATE
   * ============================================================ */
  let indexRow = 0;
  let page = 1;
  let popup = "";
  let id = "";
  let triggerClick = true;
  let highlightSearch;
  let totalRecord;
  let limit = 10;
  let postData;
  let autoNumericElements = [];
  let rowNum = 50;
  let lazyLoader;
  let selectedId = null;
  let selectedRows = [];
  let activeGrid = null;
  let sortname = 'no_bukti';
  let sortorder = 'asc';
  const GRID_PREF_KEY        = 'testingmasterdetail_master_grid';
  const GRID_PREF_KEY_DETAIL = 'testingmasterdetail_detail_grid';
  const urlMaster  = '/testingmasterdetail';
  const masterGrid = '#jqGrid';
  const gridPager  = '#jqGridPager';

  // Detail grid state
  let selectedMasterId     = null;
  let selectedDetailId     = null;
  let detailLazyLoader     = null;
  let detailIndexRow       = 0;
  let detailPage           = 1;
  let detailSortname       = 'nama_barang';
  let detailSortorder      = 'asc';
  const detailGrid  = '#jqGridDetail';
  const detailPager = '#jqGridDetailPager';

  const accessRights = {
    add:    <?= has_permission('testingmasterdetail', 'create') ? 'true' : 'false' ?>,
    edit:   <?= has_permission('testingmasterdetail', 'update') ? 'true' : 'false' ?>,
    delete: <?= has_permission('testingmasterdetail', 'delete') ? 'true' : 'false' ?>,
    report: <?= has_permission('testingmasterdetail', 'report') ? 'true' : 'false' ?>,
    export: <?= has_permission('testingmasterdetail', 'export') ? 'true' : 'false' ?>,
  };

  /* ============================================================
   *  MASTER COLUMN MODEL
   * ============================================================ */
  function getBaseColModel() {
    return [
      {
        label: 'ID',
        name: 'id',
        align: 'left',
        width: 70,
        search: false,
        hidden: true
      },
      {
        label: 'NO. BUKTI',
        name: 'no_bukti',
        align: 'left',
        width: 150
      },
      {
        label: 'TANGGAL',
        name: 'tgl_bukti',
        align: 'center',
        width: 120,
        formatter: 'date',
        formatoptions: { srcformat: 'Y-m-d', newformat: 'd-m-Y' }
      },
      {
        label: 'PELANGGAN',
        name: 'nama_pelanggan',
        align: 'left',
        width: 200
      },
      {
        label: 'MODIFIED BY',
        name: 'modifiedby',
        align: 'left',
        width: 130
      },
      {
        label: 'CREATED AT',
        name: 'created_at',
        align: 'center',
        width: 160,
        formatter: 'date',
        formatoptions: { srcformat: 'ISO8601Long', newformat: 'd-m-Y H:i:s' }
      },
      {
        label: 'UPDATED AT',
        name: 'updated_at',
        align: 'center',
        width: 160,
        formatter: 'date',
        formatoptions: { srcformat: 'ISO8601Long', newformat: 'd-m-Y H:i:s' }
      },
    ];
  }

  /* ============================================================
   *  DETAIL COLUMN MODEL
   * ============================================================ */
  function getDetailColModel() {
    return [
      {
        label: 'ID',
        name: 'id',
        align: 'left',
        width: 70,
        search: false,
        hidden: true
      },
      {
        label: 'NAMA BARANG',
        name: 'nama_barang',
        align: 'left',
        width: 220
      },
      {
        label: 'QTY',
        name: 'qty',
        align: 'right',
        width: 80,
        formatter: 'integer',
        formatoptions: { thousandsSeparator: '.' }
      },
      {
        label: 'HARGA',
        name: 'harga',
        align: 'right',
        width: 140,
        formatter: 'currency',
        formatoptions: { prefix: 'Rp ', decimalSeparator: ',', thousandsSeparator: '.', decimalPlaces: 0 }
      },
      {
        label: 'SUBTOTAL',
        name: 'subtotal',
        align: 'right',
        width: 160,
        formatter: 'currency',
        formatoptions: { prefix: 'Rp ', decimalSeparator: ',', thousandsSeparator: '.', decimalPlaces: 0 }
      },
      {
        label: 'MODIFIED BY',
        name: 'modifiedby',
        align: 'left',
        width: 120
      },
      {
        label: 'CREATED AT',
        name: 'created_at',
        align: 'center',
        width: 150,
        formatter: 'date',
        formatoptions: { srcformat: 'ISO8601Long', newformat: 'd-m-Y H:i:s' }
      },
    ];
  }

  /* ============================================================
   *  GRID PREFERENCE SETUP
   * ============================================================ */
  GridPreferenceManager.configure({
    mode: 'server',
    serverUrl: API_URL + '/grid-preferences',
  });

  /* ============================================================
   *  DOCUMENT READY
   * ============================================================ */
  $(document).ready(async function() {

    // -----------------------------------------------------------
    // MASTER GRID
    // -----------------------------------------------------------
    const savedPrefs   = await GridPreferenceManager.load(GRID_PREF_KEY);
    const finalColModel = GridPreferenceManager.apply(getBaseColModel(), savedPrefs);

    const grid = createJqGrid({
      gridId:  masterGrid,
      pagerId: gridPager,
      url:     urlMaster,
      page:    page,
      colModel: finalColModel,
      lazyLoad: true,
      lazyLoadOptions: {
        rowsPerPage: 50,
        windowPages: 3,
        gapPage: 30,
        onInit: function(instance) {
          lazyLoader = instance;
        }
      },
      clearGlobalSearch: function(gridEl) {
        const searchInputId = `#${$.jgrid.jqID(gridEl[0].id)}_searchText`;
        $(searchInputId).val('');
      },
      options: {
        sortname:  sortname,
        sortorder: sortorder,
        rowNum:    rowNum,
        resizeStop: function(newWidth, index) {
          const prefs = GridPreferenceManager.extract(masterGrid);
          GridPreferenceManager.save(GRID_PREF_KEY, prefs);
        },
        onSelectRow: function(rowid) {
          activeGrid      = $(this);
          selectedId      = $(masterGrid).jqGrid('getCell', rowid, 'id');
          selectedMasterId = selectedId;
          page            = $(masterGrid).jqGrid('getGridParam', 'page');
          indexRow        = $(masterGrid).jqGrid('getCell', rowid, 'rn') - 1;
          let lim         = $(masterGrid).jqGrid('getGridParam', 'postData').limit;
          if (indexRow >= lim) indexRow = (indexRow - lim * (page - 1));

          // Load detail grid when master row selected
          loadDetailGrid(selectedMasterId);
        },
        loadComplete: function(data) {
          changeJqGridRowListText();
          triggerClick = true;

          $(this).off('keydown.lazygrid');
          setCustomBindKeysLazy(masterGrid);

          let ids = $(this).getDataIDs();
          let selectedRowId = ids[0];

          if (ids.length > 0) {
            if (triggerClick) {
              if (id != '') {
                let localIndex = parseInt($(masterGrid).jqGrid('getInd', id)) - 1;
                if (!isNaN(localIndex) && localIndex >= 0 && localIndex < ids.length) {
                  indexRow = localIndex;
                }
                id = '';
              }

              if (indexRow >= ids.length) {
                indexRow = ids.length > 0 ? ids.length - 1 : 0;
              }

              selectedRowId = ids[indexRow];
              $(`${masterGrid} tr[id="${selectedRowId}"]`).click();
              $(`${masterGrid} tr[id="${selectedRowId}"]`).focus();
              triggerClick = false;

            } else {
              if (indexRow >= ids.length) indexRow = ids.length - 1;
              selectedRowId = ids[indexRow];
              $(masterGrid).setSelection(selectedRowId);
              $(`${masterGrid} tr[id="${selectedRowId}"]`).focus();
            }

            setHighlight($(this));
            ColumnSettingsManager.renderBadge(masterGrid);
          }
        }
      },
    })
    .toolbarBindKeys()
    .loadClearFilter()
    .clearGlobalSearch()
    .customPager({
      buttons: [
        {
          id: 'add',
          innerHTML: '<i class="fa fa-plus"></i> ADD',
          class: 'btn btn-primary btn-md mr-1',
          shortcut: 'a',
          right: 'add',
          onClick: () => {
            createtestingmasterdetail();
          }
        },
        {
          id: 'edit',
          innerHTML: '<i class="fa fa-pen"></i> EDIT',
          class: 'btn btn-success btn-md mr-1',
          shortcut: 'e',
          right: 'edit',
          onClick: () => {
            selectedId = $("#jqGrid").jqGrid('getGridParam', 'selrow');
            updatetestingmasterdetail(selectedId);
          }
        },
        {
          id: 'delete',
          innerHTML: '<i class="fa fa-trash"></i> DELETE',
          class: 'btn btn-danger btn-md mr-1',
          shortcut: 'd',
          right: 'delete',
          onClick: () => {
            selectedId = $("#jqGrid").jqGrid('getGridParam', 'selrow');
            deletetestingmasterdetail(selectedId);
          }
        },
        {
          id: 'report',
          innerHTML: '<i class="fa fa-print"></i> REPORT',
          class: 'btn btn-info btn-md mr-1',
          shortcut: 'r',
          right: 'report',
          onClick: () => {}
        },
        {
          id: 'export',
          innerHTML: '<i class="fa fa-file-export"></i> EXPORT',
          class: 'btn btn-warning btn-md mr-1',
          shortcut: 'x',
          right: 'export',
          onClick: () => {
            exportMaster();
          }
        },
      ]
    })
    .permissions(accessRights);

    // Drag and Drop Column - Master
    $(`${masterGrid}`).closest('.ui-jqgrid-view')
      .find('thead tr.ui-jqgrid-labels')
      .sortable({
        items: 'th:not(:first-child)',
        cursor: 'grabbing',
        opacity: 0.7,
        stop: function(event, ui) {
          const prefs = GridPreferenceManager.extractFromDom(masterGrid);
          GridPreferenceManager.save(GRID_PREF_KEY, prefs);
        }
      });

    ColumnSettingsManager.init(masterGrid, GRID_PREF_KEY, getBaseColModel());

    // -----------------------------------------------------------
    // DETAIL GRID
    // -----------------------------------------------------------
    await initDetailGrid();

    // Setup Pintasan Keyboard Global
    setupKeyboardShortcuts();

  }); // END document.ready

  /* ============================================================
   *  DETAIL GRID - INIT
   * ============================================================ */
  async function initDetailGrid() {
    const savedDetailPrefs    = await GridPreferenceManager.load(GRID_PREF_KEY_DETAIL);
    const finalDetailColModel = GridPreferenceManager.apply(getDetailColModel(), savedDetailPrefs);

    // URL awal placeholder - akan diupdate saat master row dipilih
    const detailInitUrl = `${urlMaster}/__placeholder__/detail`;

    createJqGrid({
      gridId:  detailGrid,
      pagerId: detailPager,
      url:     detailInitUrl,
      page:    1,
      colModel: finalDetailColModel,
      lazyLoad: true,
      lazyLoadOptions: {
        rowsPerPage: 50,
        windowPages: 3,
        gapPage: 30,
        onInit: function(instance) {
          detailLazyLoader = instance;
          // Jangan load data dulu - tunggu master dipilih
          instance.initialized = true;
          instance.paused = true; // Flag custom untuk pause auto-load
        }
      },
      clearGlobalSearch: function(gridEl) {
        const searchInputId = `#${$.jgrid.jqID(gridEl[0].id)}_searchText`;
        $(searchInputId).val('');
      },
      options: {
        sortname:  detailSortname,
        sortorder: detailSortorder,
        rowNum:    50,
        height:    250,
        resizeStop: function(newWidth, index) {
          const prefs = GridPreferenceManager.extract(detailGrid);
          GridPreferenceManager.save(GRID_PREF_KEY_DETAIL, prefs);
        },
        onSelectRow: function(rowid) {
          selectedDetailId = $(detailGrid).jqGrid('getCell', rowid, 'id');
          detailPage       = $(detailGrid).jqGrid('getGridParam', 'page');
          detailIndexRow   = $(detailGrid).jqGrid('getCell', rowid, 'rn') - 1;
        },
        loadComplete: function(data) {
          let ids = $(this).getDataIDs();
          if (ids.length > 0) {
            const firstId = ids[0];
            $(detailGrid).setSelection(firstId, true);
            selectedDetailId = $(detailGrid).jqGrid('getCell', firstId, 'id');
          }

          // Update info badge
          const totalRows = $(detailGrid).jqGrid('getGridParam', 'records') || 0;
          $('#detailGridInfo').text(`${totalRows} item`);

          // Hitung total dari data yang ada
          let totalHarga = 0;
          if (data && data.attributes && data.attributes.total) {
            totalHarga = data.attributes.total;
            $('#detailGridTotal').text('Total: Rp ' + Number(totalHarga).toLocaleString('id-ID'));
          }
        }
      },
    })
    .toolbarBindKeys()
    .clearGlobalSearch()
    .customPager({
      buttons: [
        {
          id: 'detail_add',
          innerHTML: '<i class="fa fa-plus"></i> ADD ITEM',
          class: 'btn btn-primary btn-sm mr-1',
          right: 'add',
          onClick: () => {
            if (!selectedMasterId) {
              showDialog('warning', 'Pilih data penjualan terlebih dahulu!');
              return;
            }
            createDetailItem();
          }
        },
        {
          id: 'detail_edit',
          innerHTML: '<i class="fa fa-pen"></i> EDIT ITEM',
          class: 'btn btn-success btn-sm mr-1',
          right: 'edit',
          onClick: () => {
            selectedDetailId = $(detailGrid).jqGrid('getGridParam', 'selrow');
            updateDetailItem(selectedDetailId);
          }
        },
        {
          id: 'detail_delete',
          innerHTML: '<i class="fa fa-trash"></i> DELETE ITEM',
          class: 'btn btn-danger btn-sm mr-1',
          right: 'delete',
          onClick: () => {
            selectedDetailId = $(detailGrid).jqGrid('getGridParam', 'selrow');
            deleteDetailItem(selectedDetailId);
          }
        },
        {
          id: 'detail_export',
          innerHTML: '<i class="fa fa-file-export"></i> EXPORT',
          class: 'btn btn-warning btn-sm mr-1',
          right: 'export',
          onClick: () => {
            exportDetail();
          }
        },
      ]
    })
    .permissions(accessRights);

    // Drag and drop detail columns
    $(`${detailGrid}`).closest('.ui-jqgrid-view')
      .find('thead tr.ui-jqgrid-labels')
      .sortable({
        items: 'th:not(:first-child)',
        cursor: 'grabbing',
        opacity: 0.7,
        stop: function(event, ui) {
          const prefs = GridPreferenceManager.extractFromDom(detailGrid);
          GridPreferenceManager.save(GRID_PREF_KEY_DETAIL, prefs);
        }
      });

    ColumnSettingsManager.init(detailGrid, GRID_PREF_KEY_DETAIL, getDetailColModel());
  }

  /* ============================================================
   *  LOAD DETAIL GRID berdasarkan Master ID
   * ============================================================ */
  function loadDetailGrid(masterId) {
    if (!masterId) return;

    $('#detailGridHeader').show();

    const newDetailUrl = API_URL + `${urlMaster}/${masterId}/detail`;

    // Update URL di lazy loader instance
    if (detailLazyLoader) {
      detailLazyLoader.apiUrl = newDetailUrl; // property yang benar adalah apiUrl
      detailLazyLoader.paused = false;

      const postData = $(detailGrid).jqGrid('getGridParam', 'postData') || {};
      detailLazyLoader.resetGridState(false);
      detailLazyLoader.loadGridData(postData, 1, detailLazyLoader.rowsPerPage, 'down', 'jump');
    } else {
      // Fallback non-lazy
      $(detailGrid).jqGrid('setGridParam', {
        url: newDetailUrl,
        page: 1
      }).trigger('reloadGrid', [{ page: 1 }]);
    }
  }

  /* ============================================================
   *  EXPORT FUNCTIONS
   * ============================================================ */
  function exportMaster() {
    const params = $(masterGrid).jqGrid('getGridParam', 'postData') || {};
    const qs = new URLSearchParams({
      ...params,
      rows: 999999,
      page: 1
    }).toString();
    window.open(`${API_URL}/testingmasterdetail/export?${qs}`, '_blank');
  }

  function exportDetail() {
    if (!selectedMasterId) {
      showDialog('warning', 'Pilih data penjualan terlebih dahulu!');
      return;
    }
    const params = $(detailGrid).jqGrid('getGridParam', 'postData') || {};
    const qs = new URLSearchParams({ ...params, rows: 999999, page: 1 }).toString();
    window.open(`${API_URL}/testingmasterdetail/${selectedMasterId}/detail/export?${qs}`, '_blank');
  }

  /* ============================================================
   *  MODAL SHOW / HIDE
   * ============================================================ */
  $('#crudModal').on('shown.bs.modal', () => {
    let form = $('#crudForm');
    activeGrid = null;
    initSelect2(form.find('select'), $('#crudModal'));
    draftManager.resume();
  });

  $('#crudModal').on('hidden.bs.modal', () => {
    activeGrid = $(masterGrid);
    selectedRows = [];
    $('#crudModal').find('.modal-body').html(modalBody);
  });

  $('#detailModal').on('shown.bs.modal', () => {
    let form = $('#detailForm');
    initSelect2(form.find('select'), $('#detailModal'));
  });

  $('#detailModal').on('hidden.bs.modal', () => {
    $('#detailModal').find('.modal-body').html(detailModalBody);
  });

</script>

<?= $this->endSection(); ?>