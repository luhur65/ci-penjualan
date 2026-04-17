<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
  <div class="row mb-3">
    <div class="col-12">
      <table id="jqGrid"></table>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-body" style="min-height: 529px">
          <div id="tabs" style="font-size:12px">
            <ul class="dejavu">
              <li><a href="#role-tab">Role</a></li>
              <li><a href="#acl-tab">Acl</a></li>
            </ul>
            <div id="role-tab">
              <table id="userRoleGrid"></table>
            </div>
            <div id="acl-tab">
              <table id="userAclGrid"></table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?= $this->endSection() ?>


<?= $this->section('scripts'); ?>
<?= $this->include('user/modal'); ?>
<?= $this->include('user/role/_grid'); ?>
<?= $this->include('user/acl/_grid'); ?>
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
  let activeGrid = null;
  let sortname = 'fullname';
  let sortorder = 'asc';
  const GRID_PREF_KEY = 'user_master_grid';
  const urlMaster = '/users';
  const masterGrid = '#jqGrid';
  const gridPager = '#jqGridPager';
  const detailGrid = '#detailItem';
  const accessRights = {
    add: <?= has_permission('user', 'create') ? 'true' : 'false' ?>,
    edit: <?= has_permission('user', 'update') ? 'true' : 'false' ?>,
    delete: <?= has_permission('user', 'delete') ? 'true' : 'false' ?>,
    report: <?= has_permission('user', 'report') ? 'true' : 'false' ?>,
    export: <?= has_permission('user', 'export') ? 'true' : 'false' ?>
  };
  const tabLoaders = {
    'role-tab': loadUserRoleData,
    'acl-tab': loadUserAclData,
  }

  function getBaseColModel() {
    return [{
        label: 'ID',
        name: 'id',
        hidden: true,
        search: false,
        key: true,
      },
      {
        label: 'STATUS AKTIF',
        name: 'statusaktif',
        align: 'center',
        width: (detectDeviceType() == "desktop") ? sm_dekstop_1 : sm_mobile_1,
        stype: 'select',
        searchoptions: {
          value: "<?= combo_status('STATUS AKTIF', 'STATUS AKTIF'); ?>",
          dataInit: function(element) {
            $(element).select2({
              width: '100%',
              theme: "bootstrap4"
            }).on("select2:open", function(e) {
              setTimeout(() => {
                document
                  .querySelector(".select2-search--dropdown .select2-search__field")
                  .focus();
              }, 20);
            });
          }
        },
        formatter: (value, options, rowData) => {
          let statusAktif = JSON.parse(value)

          if (!statusAktif) {
            return ''
          }

          let formattedValue = $(`
              <div class="badge" style="background-color: ${statusAktif.WARNA}; color: ${statusAktif.WARNATULISAN};">
              <span>${statusAktif.SINGKATAN}</span>
              </div>
          `)

          return formattedValue[0].outerHTML
        },
        cellattr: (rowId, value, rowObject) => {
          let statusAktif = JSON.parse(rowObject.statusaktif)
          if (!statusAktif) {
            return ` title=""`
          }

          return ` title="${statusAktif.MEMO}"`
        }
      },
      {
        label: 'NAMA LENGKAP',
        name: 'fullname',
        width: (detectDeviceType() == "desktop") ? md_dekstop_2 : sm_mobile_2,
      },
      {
        label: 'NAMA PENGGUNA',
        name: 'username',
        width: (detectDeviceType() == "desktop") ? md_dekstop_2 : sm_mobile_2,
      },
      {
        label: 'EMAIL',
        name: 'email',
        width: (detectDeviceType() == "desktop") ? md_dekstop_2 : sm_mobile_2,
      },
      {
        label: 'MODIFIED BY',
        name: 'modifiedby',
        align: 'left',
        width: (detectDeviceType() == "desktop") ? sm_dekstop_3 : sm_mobile_3,
      },
      {
        label: 'UPDATED AT',
        name: 'updated_at',
        width: (detectDeviceType() == "desktop") ? sm_dekstop_4 : sm_mobile_4,
        formatter: "date",
        formatoptions: {
          srcformat: "ISO8601Long",
          newformat: "d-m-Y H:i:s"
        }
      },
      {
        label: 'CREATED AT',
        name: 'created_at',
        width: (detectDeviceType() == "desktop") ? sm_dekstop_4 : sm_mobile_3,
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


  $(document).ready(async function() {
    $("#tabs").tabs({
      activate: function(event, ui) {
        let selectedMasterId = $(masterGrid).jqGrid('getGridParam', 'selrow');
        if (!selectedMasterId) return;

        // 1. Dapatkan ID Tab yang baru saja dibuka oleh pengguna
        let activeTabId = ui.newPanel.attr('id');

        // 2. [EKSEKUSI MODULAR]: Panggil fungsinya dari kamus!
        if (tabLoaders[activeTabId]) {
          tabLoaders[activeTabId](selectedMasterId);
        }
      }
    });

    const savedPrefs = await GridPreferenceManager.load(GRID_PREF_KEY);
    const finalColModel = GridPreferenceManager.apply(getBaseColModel(), savedPrefs);

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
            selectedId = $(masterGrid).jqGrid('getCell', id, 'id')
            page = $(masterGrid).jqGrid('getGridParam', 'page')
            indexRow = $(masterGrid).jqGrid('getCell', id, 'rn') - 1
            let limit = $(masterGrid).jqGrid('getGridParam', 'postData').limit
            if (indexRow >= limit) indexRow = (indexRow - limit * (page - 1))

            let activeTabIndex = $("#tabs").tabs('option', 'active')
            //Dapatkan ID HTML dari panel tab yang aktif tersebut (misal: "role-tab")
            let activeTabId = $("#tabs .ui-tabs-panel").eq(activeTabIndex).attr('id');
            if (tabLoaders[activeTabId]) {
              tabLoaders[activeTabId](id);
            }

            // syncActiveFilterWithSelectedRow(this, id);

            // ini yang benar (global position)
            // let localIndex = $(masterGrid).jqGrid('getInd', selectedId) - 1
            // let indexRow = ((page - 1) * limit) + localIndex + 1
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
        }
      })
      .toolbarBindKeys()
      .loadClearFilter()
      .customPager({
        buttons: [{
            id: 'add',
            innerHTML: '<i class="fa fa-plus"></i> ADD',
            class: 'btn btn-primary mr-1',
            shortcut: 'a',
            right: 'add',
            onClick: () => createUser()
          },
          {
            id: 'edit',
            innerHTML: '<i class="fa fa-pen"></i> EDIT',
            class: 'btn btn-success mr-1',
            shortcut: 'e',
            right: 'edit',
            onClick: () => updateUser($("#jqGrid").jqGrid('getGridParam', 'selrow'))
          },
          {
            id: 'delete',
            innerHTML: '<i class="fa fa-trash"></i> DELETE',
            class: 'btn btn-danger mr-1',
            shortcut: 'd',
            right: 'delete',
            onClick: () => deleteUser($("#jqGrid").jqGrid('getGridParam', 'selrow'))
          },
          {
            id: 'report',
            innerHTML: '<i class="fa fa-print"></i> REPORT',
            class: 'btn btn-info mr-1',
            shortcut: 'r',
            right: 'report',
            onClick: () => {}
          },
          {
            id: 'export',
            innerHTML: '<i class="fa fa-file-export"></i> EXPORT',
            class: 'btn btn-warning mr-1',
            shortcut: 'x',
            right: 'export',
            onClick: () => exportExcel()
          },
        ]
      })
      .permissions(accessRights);
    
    // Drag and Drop Column
    $(`${masterGrid}`).closest('.ui-jqgrid-view')
    .find('thead tr.ui-jqgrid-labels')
    .sortable({
      items: 'th:not(:first-child)', // skip kolom pertama (biasanya rn/checkbox)
      cursor: 'grabbing',
      opacity: 0.7,
      stop: function(event, ui) {
        const prefs = GridPreferenceManager.extractFromDom(masterGrid);
        GridPreferenceManager.save(GRID_PREF_KEY, prefs);
        console.log('[Grid] Urutan kolom tersimpan setelah drag:', prefs);
      }
    });
    
    ColumnSettingsManager.init(masterGrid, GRID_PREF_KEY, getBaseColModel());

    loadUserRoleGrid();
    loadUserAclGrid();

    // Setup Pintasan Keyboard Global
    setupKeyboardShortcuts();


    // let gridScrollActive = true;
    // const gridBody = $(masterGrid).closest('.ui-jqgrid-bdiv')[0];

    // function onWheelOutsideGrid(e) {
    //     const isOverGrid = $(e.target).closest('.ui-jqgrid-bdiv, .ui-jqgrid').length > 0;
    //     if (isOverGrid) return;

    //     e.preventDefault();
    //     gridBody.scrollTop += e.deltaY;
    // }

    // // Pasang saat pertama load
    // window.addEventListener('wheel', onWheelOutsideGrid, { passive: false });

    // // Lepas saat klik di luar grid
    // $(document).on('click.gridScroll', function(e) {
    //     const isOutsideGrid = $(e.target).closest('.ui-jqgrid').length === 0;
    //     if (isOutsideGrid) {
    //         window.removeEventListener('wheel', onWheelOutsideGrid);
    //     }
    // });

    // // Pasang lagi saat klik di dalam grid
    // $(masterGrid).closest('.ui-jqgrid').on('click.gridScroll', function() {
    //     // Lepas dulu biar tidak dobel
    //     window.removeEventListener('wheel', onWheelOutsideGrid);
    //     window.addEventListener('wheel', onWheelOutsideGrid, { passive: false });
    // });

  });

  $('#crudModal').on('hidden.bs.modal', () => {
    activeGrid = $(masterGrid)

    $('#crudModal').find('.modal-body').html(modalBody)
  })

  $('#crudModal').on('shown.bs.modal', () => {
    let form = $('#crudForm')

    // setFormBindKeys(form)

    activeGrid = null

    // getMaxLength(form)
    initSelect2(form.find('select'), $('#crudModal'))
    // initDatepicker()
    initLookup()

    $('#multiple')
      .select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('#crudModal')
      })

    draftManager.resume();


  })
</script>

<?= $this->endSection(); ?>