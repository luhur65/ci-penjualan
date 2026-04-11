<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
  <table id="jqGrid"></table>
</div>


<?= $this->endSection() ?>


<?= $this->section('scripts'); ?>
<?= $this->include('role/modal'); ?>
<?= $this->include('aco/_grid'); ?>

<script>
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
  let sortname = 'rolename';
  let sortorder = 'asc';
  const GRID_PREF_KEY = 'role_master_grid';
  const urlMaster = '/roles';
  const masterGrid = '#jqGrid';
  const gridPager = '#jqGridPager';
  const detailGrid = '#detailItem';
  const accessRights = {
    add: <?= has_permission('role', 'create') ? 'true' : 'false' ?>,
    edit: <?= has_permission('role', 'update') ? 'true' : 'false' ?>,
    delete: <?= has_permission('role', 'delete') ? 'true' : 'false' ?>,
    report: <?= has_permission('role', 'report') ? 'true' : 'false' ?>,
    export: <?= has_permission('role', 'export') ? 'true' : 'false' ?>,
  };

  function getBaseColModel() {
    return [{
        label: 'ID',
        name: 'id',
        hidden: true,
        search: false,
        key: true,
      },
      {
        label: 'NAMA ROLE',
        name: 'rolename',
        width: 200
      },
      {
        label: 'MODIFIED BY',
        name: 'modifiedby',
        align: 'left',
        width: 100,
      },
      {
        label: 'UPDATED AT',
        name: 'updated_at',
        width: 200,
        formatter: "date",
        formatoptions: {
          srcformat: "ISO8601Long",
          newformat: "d-m-Y H:i:s"
        }
      },
      {
        label: 'CREATED AT',
        name: 'created_at',
        width: 200,
        formatter: "date",
        formatoptions: {
          srcformat: "ISO8601Long",
          newformat: "d-m-Y H:i:s"
        }
      }
    ];
  }

  GridPreferenceManager.configure({
    mode: 'server',
    serverUrl: API_URL + '/grid-preferences',
  });

  const savedPrefs = GridPreferenceManager.load(GRID_PREF_KEY);
  const finalColModel = GridPreferenceManager.apply(getBaseColModel(), savedPrefs);

  $(document).ready(function() {

    const grid = createJqGrid({
      gridId: masterGrid,
      pagerId: gridPager,
      url: urlMaster,
      shrinkToFit: false,
      page: page,
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
        sortname: sortname,
        sortorder: sortorder,
        rowNum: rowNum,
        resizeStop: function(newWidth, index) {
          const prefs = GridPreferenceManager.extract(masterGrid);
          GridPreferenceManager.save(GRID_PREF_KEY, prefs);
          console.log('[Grid] Preferensi tersimpan setelah resize.');
        },
        onSelectRow: function(id) {
          activeGrid = $(this)
          indexRow = $(this).jqGrid('getCell', id, 'rn') - 1
          limit = $(this).jqGrid('getGridParam', 'rowNum')
          page = $(this).jqGrid('getGridParam', 'page')
          // let rows = $(this).jqGrid('getGridParam', 'postData').limit
          // if (indexRow >= rows) indexRow = (indexRow - rows * (page - 1))
        },
        loadComplete: function(data) {
          changeJqGridRowListText();
          triggerClick = true;

          $(document).unbind('keydown')
          setCustomBindKeys($(this))

          let ids = $(this).getDataIDs();
          let selectedRowId = ids[0];

          if (ids.length > 0) {
            if (triggerClick) {

              if (id != '') {
                // Mencari indeks lokal menggunakan ID terdekat dari Backend
                let localIndex = parseInt($(masterGrid).jqGrid('getInd', id)) - 1;

                if (!isNaN(localIndex) && localIndex >= 0 && localIndex < ids.length) {
                  indexRow = localIndex;
                }
                // Jika getInd gagal, indexRow akan otomatis menggunakan nilai terakhirnya 
                // yang sudah merupakan indeks lokal (0-9).
                id = '';
              }

              // Amankan indexRow agar tidak melebihi jumlah data yang ada di halaman saat ini
              // (Mencegah error saat menghapus baris terakhir)
              if (indexRow >= ids.length) {
                indexRow = ids.length > 0 ? ids.length - 1 : 0;
              }

              selectedRowId = ids[indexRow];
              $(`${masterGrid} tr[id="${selectedRowId}"]`).click();
              triggerClick = false;

            } else {
              if (indexRow >= ids.length) indexRow = ids.length - 1;
              selectedRowId = ids[indexRow];
              $(masterGrid).setSelection(selectedRowId);
            }

            setHighlight($(this));
            ColumnSettingsManager.renderBadge(masterGrid);
          }
        }
      }
    })
    .loadClearFilter()
    .clearGlobalSearch()
    .customPager({
      buttons: [{
          id: 'add',
          innerHTML: '<i class="fa fa-plus"></i> ADD',
          class: 'btn btn-primary mr-1',
          shortcut: 'a',
          onClick: () => {
            createRole()
          }
        },
        {
          id: 'edit',
          innerHTML: '<i class="fa fa-pen"></i> EDIT',
          class: 'btn btn-success mr-1',
          shortcut: 'e',
          onClick: () => {
            selectedId = $("#jqGrid").jqGrid('getGridParam', 'selrow')
            updateRole(selectedId)
          }
        },
        {
          id: 'delete',
          innerHTML: '<i class="fa fa-trash"></i> DELETE',
          class: 'btn btn-danger mr-1',
          shortcut: 'd',
          onClick: () => {
            selectedId = $("#jqGrid").jqGrid('getGridParam', 'selrow')
            deleteRole(selectedId)
          }
        },
        {
          id: 'report',
          innerHTML: '<i class="fa fa-print"></i> REPORT',
          class: 'btn btn-info mr-1',
          shortcut: 'r',
          onClick: () => {
            // $('#rangeModal').data('action', 'report')
            // $('#rangeModal').find('button:submit').html(`Report`)
            // $('#rangeModal').modal('show')
          }
        },
        {
          id: 'export',
          innerHTML: '<i class="fa fa-file-export"></i> EXPORT',
          class: 'btn btn-warning mr-1',
          shortcut: 'x',
          onClick: () => {
            // $('#rangeModal').data('action', 'export')
            // $('#rangeModal').find('button:submit').html(`Export`)
            // $('#rangeModal').modal('show')
          }
        },
      ]
    })
    .permissions(accessRights);

    ColumnSettingsManager.init(masterGrid, GRID_PREF_KEY, getBaseColModel());

    // Setup Pintasan Keyboard Global
    setupKeyboardShortcuts();

  });

  $('#crudModal').on('shown.bs.modal', () => {
    let form = $('#crudForm')

    // setFormBindKeys(form)

    activeGrid = null

    // getMaxLength(form)
    initSelect2(form.find('select'), $('#crudModal'))
    // initDatepicker()
    // initLookup()

    $('#multiple')
    .select2({
      theme: 'bootstrap4',
      width: '100%',
    })

    let currentKey = draftManager.getKey();
    if (localStorage.getItem(currentKey)) {
      $('#btnGetLastData').show();
    } else {
      $('#btnGetLastData').hide();
    }

    draftManager.resume();
  })

  $('#crudModal').on('hidden.bs.modal', () => {
    activeGrid = '#jqGrid'
    selectedRows = []
    $('#crudModal').find('.modal-body').html(modalBody)
  })
</script>

<?= $this->endSection(); ?>