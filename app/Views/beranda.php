<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
  <div class="col-12">
    <table id="jqGrid"></table>
    <div id="jqGridPager"></div>
  </div>
</div>


<?= $this->endSection() ?>


<?= $this->section('scripts'); ?>
<?= $this->include('components/user/modal'); ?>

<script>
  let indexRow = 0;
  let page = 1;
  let popup = "";
  let id = "";
  let triggerClick = true;
  let highlightSearch;
  let totalRecord;
  let limit;
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

  function getBaseColModel() {
    return [{
        label: 'ID',
        name: 'id',
        hidden: true,
        key: true,
        width: 30
      },
      {
        label: 'NAMA LENGKAP',
        name: 'fullname',
        width: 100
      },
      {
        label: 'NAMA PENGGUNA',
        name: 'username',
        width: 100
      },
      {
        label: 'EMAIL',
        name: 'email',
        width: 100,
      },
      {
        label: 'UPDATED AT',
        name: 'updated_at',
        width: 100,
        formatter: "date",
        formatoptions: {
          srcformat: "ISO8601Long",
          newformat: "d-m-Y H:i:s"
        }
      },
      {
        label: 'CREATED AT',
        name: 'created_at',
        width: 100,
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
      options: {
        sortname: sortname,
        sortorder: sortorder,
        rowNum: rowNum,
        caption: "Data User",
        onSelectRow: function(id) {
          activeGrid = $(this)
          indexRow = $(this).jqGrid('getCell', id, 'rn') - 1
          page = $(this).jqGrid('getGridParam', 'page')
          let rows = $(this).jqGrid('getGridParam', 'postData').limit
          if (indexRow >= rows) indexRow = (indexRow - rows * (page - 1))
        },
        gridComplete: function(data) {
          console.log("Grid selesai load");

          if (indexRow > $(this).getDataIDs().length - 1) {
            indexRow = $(this).getDataIDs().length - 1;
          }

          // set selection
          $(this).setSelection($(this).getDataIDs()[indexRow])

          // highlight pencarian
          setHighlight($(this));
        }
      }
    });

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

    // tombol tambah
    grid.jqGrid('navButtonAdd', '#jqGridPager', {
      caption: 'Tambah',
      buttonicon: 'fa-fw fa-plus-circle',
      onClickButton: function() {
        createUser();

      },
      position: 'first',
      title: 'Add',
      id: "AddHeader",
      cursor: "pointer",
    });

    // tombol edit
    grid.jqGrid('navButtonAdd', '#jqGridPager', {
      caption: 'Ubah',
      buttonicon: 'fa-fw fa-pencil-alt',
      onClickButton: function() {
        selectedId = $("#jqGrid").jqGrid('getGridParam', 'selrow')
        updateUser(selectedId);

      },
      position: 'last',
      title: 'Edit',
      id: "EditHeader",
      cursor: "pointer",
    });

  });

  $('#crudModal').on('shown.bs.modal', () => {
    let form = $('#crudForm')

    // setFormBindKeys(form)

    activeGrid = null

    // getMaxLength(form)
    initSelect2(form.find('.select2bs4'), true)
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