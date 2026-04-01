<script>

function gridAcoColModel() {
    return [{
        label: '',
        name: '',
        width: 40,
        align: 'center',
        sortable: false,
        clear: false,
        stype: 'input',
        searchable: false,
        searchoptions: {
          type: 'checkbox',
          clearSearch: false,
          dataInit: function(element) {
            $(element).removeClass('form-control')
            $(element).parent().addClass('text-center')
            $(element).on('click', function() {
              $(element).attr('disabled', true)
              if ($(this).is(':checked')) {
                selectAllRows()
              } else {
                clearSelectedRows()
              }
            })
          }
        },
        formatter: (value, rowOptions, rowData) => {
          return `<input type="checkbox" name="aco_ids[]" value="${rowData.id}" onchange="checkboxHandler(this)">`
        },
      },
      {
        label: 'CLASS',
        name: 'class',
        align: 'left',
        width: 150,
        // width: (detectDeviceType() == "desktop") ? lg_dekstop_3 : lg_mobile_3,
      },
      {
        label: 'METHOD',
        name: 'method',
        align: 'left',
        width: 150,
        // width: (detectDeviceType() == "desktop") ? md_dekstop_2 : md_mobile_2,
      },
      {
        label: 'KETERANGAN',
        name: 'keterangan',
        align: 'left',
        width: 200,
        // width: (detectDeviceType() == "desktop") ? md_dekstop_2 : md_mobile_2,
      },
      {
        label: 'Nama',
        name: 'nama',
        width: 200,
        // width: (detectDeviceType() == "desktop") ? lg_dekstop_2 : lg_mobile_2,
      },
    ];
  }

  function loadAcoGrid(id) {

    let sortname = 'class';
    let sortorder = 'asc';

    $('#acoGrid')
      .jqGrid({
        styleUI: 'Bootstrap4',
        datatype: "local",
        colModel: gridAcoColModel(),
        autowidth: true,
        shrinkToFit: false,
        height: 350,
        rownumbers: true,
        rownumWidth: 45,
        rowList: [10, 20, 50, 0],
        rowNum: 10,
        page: 1,
        sortname: sortname,
        sortorder: sortorder,
        viewrecords: true,
        prmNames: {
          sort: 'sortIndex',
          order: 'sortOrder',
          rows: 'limit'
        },
        jsonReader: {
          root: 'data',
          total: 'attributes.totalPages',
          records: 'attributes.totalRows',
        },
        loadBeforeSend: function(jqXHR) {
          jqXHR.setRequestHeader('Authorization', `Bearer ${ACCESS_TOKEN}`)

          setGridLastRequest($(this), jqXHR)
        },
        onSelectRow: function(id, status, event) {
          activeGrid = $(this)
          indexRow = $(this).jqGrid('getCell', id, 'rn') - 1
          page = $(this).jqGrid('getGridParam', 'page')
          let rows = $(this).jqGrid('getGridParam', 'postData').limit
          if (indexRow >= rows) indexRow = (indexRow - rows * (page - 1))
        },
        ondblClickRow: function(id, status, event) {
          $(this).find(`tr#${id}`).find(`[name="aco_ids[]"]`).click()
        },
        loadComplete: function(data) {
          let grid = $(this)

          changeJqGridRowListText()

          $(document).unbind('keydown')
          setCustomBindKeys($(this))
          // initResize($(this))

          $.each(selectedRows, function(key, value) {
            $(grid).find('tbody tr').each(function(row, tr) {
              if ($(this).find(`td input:checkbox`).val() == value) {
                $(this).addClass('bg-light-blue')
                $(this).find(`td input:checkbox`).prop('checked', true)
              }
            })
          });

          if (triggerClick) {
            if (id != '') {
              indexRow = parseInt($('#acoGrid').jqGrid('getInd', id)) - 1
              $(`#acoGrid [id="${$('#acoGrid').getDataIDs()[indexRow]}"]`).click()
              id = ''
            } else if (indexRow != undefined) {
              $(`#acoGrid [id="${$('#acoGrid').getDataIDs()[indexRow]}"]`).click()
            }

            if ($('#acoGrid').getDataIDs()[indexRow] == undefined) {
              $(`#acoGrid [id="` + $('#acoGrid').getDataIDs()[0] + `"]`).click()
            }

            triggerClick = false
          } else {
            $('#acoGrid').setSelection($('#acoGrid').getDataIDs()[indexRow])
          }
          $('#gs_').attr('disabled', false)
          setHighlight($(this))
        }
      })
      .jqGrid('filterToolbar', {
        stringResult: true,
        searchOnEnter: false,
        defaultSearch: 'cn',
        groupOp: 'AND',
        disabledKeys: [17, 33, 34, 35, 36, 37, 38, 39, 40],
        beforeSearch: function() {
          abortGridLastRequest($(this))

          clearGlobalSearch($('#acoGrid'))
        },
      })
      .customPager()

    // loadClearFilter($('#acoGrid'))
    initGlobalSearch($('#acoGrid'), urlMaster)
  }


</script>