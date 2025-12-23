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
          let totalParent = rowData.menukode.length

          for (let i = 0; i < totalParent - 1; i++) {
            value = `· ${value}`
          }

          return value
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
        addMenu();

      },
      position: 'first',
      title: 'Add',
      id: "AddHeader",
      cursor: "pointer",
    });

  });
</script>

<?= $this->endSection(); ?>