<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
  <div class="col-12">
    <table id="jqGrid"></table>
    <div id="jqGridPager"></div>
  </div>
</div>

<?= $this->include('menu/modal') ?>

<?= $this->endSection() ?>
<?= $this->section('scripts'); ?>

<script>
  let indexRow = 0;
  let page = 1;
  let popup = "";
  let id = "";
  let triggerClick = true;
  let highlightSearch;
  let totalRecord
  let limit
  let postData
  let autoNumericElements = []
  let rowNum = 10
  let sortname = 'menukode';
  let sortorder = 'asc';
  const urlMaster = '/menu';
  const masterGrid = '#jqGrid';
  const gridPager = '#jqGridPager';
  const detailGrid = '#detailItem';

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
        label: 'NAMA MENU',
        name: 'menuname',
        width: 250,
        align: 'left',
        formatter: (value, options, rowData) => {

          // Hitung level berdasarkan jumlah titik
          let level = rowData.menukode.split('.').length - 1

          let indent = ''

          for (let i = 0; i < level; i++) {
            indent += '· '
          }

          return indent + value
        }
      },
      {
        label: 'MENU PARENT',
        name: 'menu_parent',
        stype: 'select',
        // searchoptions: {
        //   value: `
        //           $i = 1;

        //           foreach ($data['combo'] as $status):
        //             echo "$status[id]:$status[menuparent]";
        //             if ($i !== count($data['combo'])) {
        //               echo ';';
        //             }
        //             $i++;
        //           endforeach;

        //           
        //                 `,
        //   dataInit: function(element) {
        //     $(element).select2({
        //       width: 'resolve',
        //       theme: "bootstrap4"
        //     }).on("select2:open", function(e) {
        //       setTimeout(() => {
        //         document
        //           .querySelector(".select2-search__field")
        //           .focus();
        //       }, 10);
        //     });
        //   }
        // },
      },
      {
        label: 'MENU ICON',
        name: 'menu_icon',
        align: 'left'
      },
      {
        label: 'HEADER MENU',
        name: 'aco_id',
        align: 'left'
      },
      {
        label: 'LINK',
        name: 'link',
        align: 'left'
      },
      {
        label: 'MENU EXE',
        name: 'menuexe',
        align: 'left'
      },
      {
        label: 'KODE MENU',
        name: 'menukode',
        align: 'left'
      },
      {
        label: 'CREATED AT',
        name: 'created_at',
        align: 'right',
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
        formatter: "date",
        formatoptions: {
          srcformat: "ISO8601Long",
          newformat: "d-m-Y H:i:s"
        }
      },
    ]
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
          var page = $(this).jqGrid("getGridParam", "page");
          var last = $(this).jqGrid("getGridParam", "lastpage");
          if (page > last) return false;

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

    // tombol tambah
    grid.jqGrid('navButtonAdd', '#jqGridPager', {
      caption: ' Tambah',
      buttonicon: 'fa-fw fa-plus-circle',
      onClickButton: function() {
        createMenu();

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
        updateMenu(selectedId);

      },
      position: 'last',
      title: 'Edit',
      id: "EditHeader",
      cursor: "pointer",
    });

    // tombol hapus
    grid.jqGrid('navButtonAdd', '#jqGridPager', {
      caption: 'Hapus',
      buttonicon: 'fa-fw fa-trash-alt',
      onClickButton: function() {
        selectedId = $("#jqGrid").jqGrid('getGridParam', 'selrow')
        deleteMenu(selectedId);

      },
      position: 'last',
      title: 'Delete',
      id: "DeleteHeader",
      cursor: "pointer",
    });

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