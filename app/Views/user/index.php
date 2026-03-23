<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
  <table id="jqGrid"></table>
</div>


<?= $this->endSection() ?>


<?= $this->section('scripts'); ?>
<?= $this->include('user/modal'); ?>

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
  let rowNum = 10;
  let selectedId = null;
  let selectedRows = [];
  let sortname = 'fullname';
  let sortorder = 'asc';
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

  function getBaseColModel() {
    return [{
        label: 'ID',
        name: 'id',
        hidden: true,
        search: false,
        key: true,
      },
      {
        label: 'NAMA LENGKAP',
        name: 'fullname',
        width: 200
      },
      {
        label: 'NAMA PENGGUNA',
        name: 'username',
        width: 200
      },
      {
        label: 'EMAIL',
        name: 'email',
        width: 250,
      },
      {
        label: 'STATUS AKTIF',
        name: 'statusaktif',
        width: 150,
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

  $(document).ready(function() {

    const grid = createJqGrid({
        gridId: masterGrid,
        pagerId: gridPager,
        url: urlMaster,
        page: page,
        colModel: getBaseColModel(),
        lazyLoad: true,
        lazyLoadOptions: {
          rowsPerPage: 50,
          windowPages: 3,
          gapPage: 30
        },
        options: {
          sortname: sortname,
          sortorder: sortorder,
          rowNum: 50,
          // caption: "Data User",
          onSelectRow: function(id) {
            activeGrid = $(this)
            page = $(masterGrid).jqGrid('getGridParam', 'page')
            limit = $(masterGrid).jqGrid('getGridParam', 'rowNum')
            indexRow = $(masterGrid).jqGrid('getInd', id) - 1;

            // 🔥 ini yang benar (global position)
            // let localIndex = $(masterGrid).jqGrid('getInd', selectedId) - 1
            // let indexRow = ((page - 1) * limit) + localIndex + 1
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

              setTimeout(() => {
                // $(`${masterGrid} tr[id="${selectedRowId}"]`).focus();
              }, 50);
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
            onClick: () => {
              createUser()
            }
          },
          {
            id: 'edit',
            innerHTML: '<i class="fa fa-pen"></i> EDIT',
            class: 'btn btn-success mr-1',
            onClick: () => {
              selectedId = $("#jqGrid").jqGrid('getGridParam', 'selrow')
              updateUser(selectedId)
            }
          },
          {
            id: 'delete',
            innerHTML: '<i class="fa fa-trash"></i> DELETE',
            class: 'btn btn-danger mr-1',
            onClick: () => {
              selectedId = $("#jqGrid").jqGrid('getGridParam', 'selrow')
              deleteUser(selectedId)
            }
          },
          {
            id: 'report',
            innerHTML: '<i class="fa fa-print"></i> REPORT',
            class: 'btn btn-info mr-1',
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
            onClick: () => {
              // $('#rangeModal').data('action', 'export')
              // $('#rangeModal').find('button:submit').html(`Export`)
              // $('#rangeModal').modal('show')
            }
          },
        ]
      })
      .permissions(accessRights);

    // grid.jqGrid({
    //   url: API_URL + urlMaster,
    //   mtype: "GET",
    //   styleUI: 'Bootstrap4',
    //   iconSet: 'fontAwesome',
    //   datatype: "JSON",
    //   colModel: getBaseColModel(),
    //   cmTemplate: {
    //     required: true
    //   },
    //   autowidth: true,
    //   height: 'auto',
    //   rowNum: 10,
    //   rowList: [10, 20, 30],
    //   rownumbers: true,
    //   sortname: sortname,
    //   viewrecords: true,
    //   gridview: true,
    //   sortorder: sortorder,
    //   caption: "Data User",
    //   pager: "#jqGridPager",
    //   jsonReader: {
    //     root: 'data',
    //     total: 'attributes.totalPages',
    //     records: 'attributes.totalRows',
    //   },
    //   // onSelectRow: function(id) {
    //   //   jQuery("#detailItem").jqGrid('setGridParam', {
    //   //     url: "penjualan/" + id + "/detail",
    //   //     page: 1
    //   //   });
    //   //   jQuery("#detailItem").trigger('reloadGrid');

    //   // },
    //   loadBeforeSend: function(jqXHR) {
    //     jqXHR.setRequestHeader('Authorization', `Bearer ${ACCESS_TOKEN}`);
    //     // setGridLastRequest($(this), jqXHR);
    //   },
    //   gridComplete: function(response) {
    //     const ids = $(this).jqGrid('getDataIDs');
    //     console.log(ids);

    //     // if (selectId) {
    //     //   selectRow(selectId);
    //     //   detailTable(selectId);

    //     // } else {
    //     //   selectRow(ids[0]);
    //     //   detailTable(ids[0]);

    //     // }

    //     // Highlight pencarian
    //     // higligthPencarian($(this));
    //     // Setup navigasi untuk grid master
    //     // initializeGridNavigation(masterGrid);

    //   }
    // });

    // setting default untuk seluruh action bawaan jqgrid
    // grid.jqGrid('navGrid', '#jqGridPager', {
    //   edit: false,
    //   add: false,
    //   del: false,
    //   search: false,
    //   refresh: false
    // });

    // grid.jqGrid('filterToolbar', {
    //   autosearch: true,
    //   stringResult: true,
    //   searchOnEnter: false,
    //   defaultSearch: "cn",
    //   multipleSearch: true,
    //   beforeSearch: function() {
    //     const postData = $(this).getGridParam("postData");
    //     delete postData.global_search;

    //     $(this).setGridParam({
    //       search: true,
    //       page: 1,
    //       postData: {
    //         _search: true,
    //       }
    //     }).trigger('reloadGrid');

    //   }

    // });



  });

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
      })
  })

</script>

<?= $this->endSection(); ?>