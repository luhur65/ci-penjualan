<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
  <table id="jqGrid"></table>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts'); ?>
<?= $this->include('error/modal') ?>

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
  let activeGrid = null;
  let sortname = 'kodeerror';
  let sortorder = 'asc';
  const GRID_PREF_KEY = 'error_master_grid';
  const urlMaster = '/error';
  const masterGrid = '#jqGrid';
  const gridPager = '#jqGridPager';

  const accessRights = {
    add: <?= has_permission('error', 'create') ? 'true' : 'false' ?>,
    edit: <?= has_permission('error', 'update') ? 'true' : 'false' ?>,
    delete: <?= has_permission('error', 'delete') ? 'true' : 'false' ?>,
    report: <?= has_permission('error', 'report') ? 'true' : 'false' ?>,
    export: <?= has_permission('error', 'export') ? 'true' : 'false' ?>,
  };

  function getBaseColModel() {
    return [{
        label: 'ID',
        name: 'id',
        align: 'right',
        width: '70px',
        search: false,
        hidden: true
      },
      {
        label: 'KODE ERROR',
        name: 'kodeerror',
        align: 'left',
        width: 150
      },
      {
        label: 'KETERANGAN',
        name: 'keterangan',
        align: 'left',
        width: 150
      },
      {
        label: 'MODIFIED BY',
        name: 'modifiedby',
        align: 'left',
        width: 150
      },
      {
        label: 'CREATED AT',
        name: 'created_at',
        align: 'right',
        width: 200,
        formatter: "date",
        formatoptions: {
          srcformat: "ISO8601Long",
          newformat: "d-m-Y H:i:s"
        }
      },
      {
        label: 'UPDATED AT',
        name: 'updated_at',
        align: 'right',
        width: 200,
        formatter: "date",
        formatoptions: {
          srcformat: "ISO8601Long",
          newformat: "d-m-Y H:i:s"
        }
      },
    ];
  }

  GridPreferenceManager.configure({
    mode: 'server',
    serverUrl: API_URL + '/grid-preferences',
  });

  $(document).ready(async function() {

    const savedPrefs = await GridPreferenceManager.load(GRID_PREF_KEY);
    const finalColModel = GridPreferenceManager.apply(getBaseColModel(), savedPrefs);

    const grid = createJqGrid({
        gridId: masterGrid,
        pagerId: gridPager,
        url: urlMaster,
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
            selectedId = $(masterGrid).jqGrid('getCell', id, 'id')
            page = $(masterGrid).jqGrid('getGridParam', 'page')
            indexRow = $(masterGrid).jqGrid('getCell', id, 'rn') - 1
            let limit = $(masterGrid).jqGrid('getGridParam', 'postData').limit
            if (indexRow >= limit) indexRow = (indexRow - limit * (page - 1))
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
        buttons: [{
            id: 'add',
            innerHTML: '<i class="fa fa-plus"></i> ADD',
            class: 'btn btn-primary btn-md mr-1',
            shortcut: 'a',
            right: 'add',
            onClick: () => {
              createerror()
            }
          },
          {
            id: 'edit',
            innerHTML: '<i class="fa fa-pen"></i> EDIT',
            class: 'btn btn-success btn-md mr-1',
            shortcut: 'e',
            right: 'edit',
            onClick: () => {
              selectedId = $("#jqGrid").jqGrid('getGridParam', 'selrow')
              updateerror(selectedId)
            }
          },
          {
            id: 'delete',
            innerHTML: '<i class="fa fa-trash"></i> DELETE',
            class: 'btn btn-danger btn-md mr-1',
            shortcut: 'd',
            right: 'delete',
            onClick: () => {
              selectedId = $("#jqGrid").jqGrid('getGridParam', 'selrow')
              deleteerror(selectedId)
            }
          },
          {
            id: 'report',
            innerHTML: '<i class="fa fa-print"></i> REPORT',
            class: 'btn btn-info btn-md mr-1',
            shortcut: 'r',
            right: 'report',
            onClick: () => {

            }
          },
          {
            id: 'export',
            innerHTML: '<i class="fa fa-file-export"></i> EXPORT',
            class: 'btn btn-warning btn-md mr-1',
            shortcut: 'x',
            right: 'export',
            onClick: () => {

            }
          },
        ]
      })
      .permissions(accessRights);

    // Drag and Drop Column
    $(`${masterGrid}`).closest('.ui-jqgrid-view')
      .find('thead tr.ui-jqgrid-labels')
      .sortable({
        items: 'th:not(:first-child)',
        cursor: 'grabbing',
        opacity: 0.7,
        stop: function(event, ui) {
          const prefs = GridPreferenceManager.extractFromDom(masterGrid);
          GridPreferenceManager.save(GRID_PREF_KEY, prefs);
          console.log('[Grid] Urutan kolom tersimpan setelah drag:', prefs);
        }
      });

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

    draftManager.resume();
  })

  $('#crudModal').on('hidden.bs.modal', () => {
    activeGrid = $(masterGrid)
    selectedRows = []
    $('#crudModal').find('.modal-body').html(modalBody)
  })
</script>

<?= $this->endSection(); ?>