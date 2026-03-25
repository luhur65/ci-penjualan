<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
  <table id="jqGrid"></table>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts'); ?>
<?= $this->include('penjualan/modal') ?>

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
  let sortname = 'menukode';
  let sortorder = 'asc';

  const urlMaster = '/penjualan';
  const masterGrid = '#jqGrid';
  const gridPager = '#jqGridPager';

  const accessRights = {
    add: <?= has_permission('penjualan', 'create') ? 'true' : 'false' ?>,
    edit: <?= has_permission('penjualan', 'update') ? 'true' : 'false' ?>,
    delete: <?= has_permission('penjualan', 'delete') ? 'true' : 'false' ?>,
    report: <?= has_permission('penjualan', 'report') ? 'true' : 'false' ?>,
    export: <?= has_permission('penjualan', 'export') ? 'true' : 'false' ?>,
  };

  function getBaseColModel() {
    return [
      {
        label: 'ID',
        name: 'id',
        align: 'right',
        width: '70px',
        search: false,
        hidden: true
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

  $(document).ready(function() {

    const grid = createJqGrid({
      gridId: masterGrid,
      pagerId: gridPager,
      url: urlMaster,
      page: page,
      colModel: getBaseColModel(),
      options: {
          sortname: sortname,
          sortorder: sortorder,
          rowNum: rowNum,
          onSelectRow: function(id) {
            activeGrid = $(this)
            indexRow = $(this).jqGrid('getCell', id, 'rn') - 1
            limit = $(this).jqGrid('getGridParam', 'rowNum')
            page = $(this).jqGrid('getGridParam', 'page')
            // let rows = $(this).jqGrid('getGridParam', 'postData').limit
            // if (indexRow >= rows) indexRow = (indexRow - rows * (page - 1))
          },
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
    })
    .loadClearFilter()
    .clearGlobalSearch()
    .customPager({
      buttons: [
        {
          id: 'add',
          innerHTML: '<i class="fa fa-plus"></i> ADD',
          class: 'btn btn-primary btn-md mr-1',
          onClick: () => {
            createpenjualan()
          }
        },
        {
          id: 'edit',
          innerHTML: '<i class="fa fa-pen"></i> EDIT',
          class: 'btn btn-success btn-md mr-1',
          onClick: () => {
            selectedId = $("#jqGrid").jqGrid('getGridParam', 'selrow')
            updatepenjualan(selectedId)
          }
        },
        {
          id: 'delete',
          innerHTML: '<i class="fa fa-trash"></i> DELETE',
          class: 'btn btn-danger btn-md mr-1',
          onClick: () => {
            selectedId = $("#jqGrid").jqGrid('getGridParam', 'selrow')
            deletepenjualan(selectedId)
          }
        },
        {
          id: 'report',
          innerHTML: '<i class="fa fa-print"></i> REPORT',
          class: 'btn btn-info btn-md mr-1',
          onClick: () => {
            
          }
        },
        {
          id: 'export',
          innerHTML: '<i class="fa fa-file-export"></i> EXPORT',
          class: 'btn btn-warning btn-md mr-1',
          onClick: () => {
            
          }
        },
      ]
    })
    .permissions(accessRights);

  });

  $('#crudModal').on('shown.bs.modal', () => {
    let form = $('#crudForm')

    // setFormBindKeys(form)

    activeGrid = null

    // getMaxLength(form)
    initSelect2(form.find('select'), $('#crudModal'))
    // initDatepicker()
    // initLookup()
  })

</script>

<?= $this->endSection(); ?>