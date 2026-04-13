<script>
  function gridAcoColModel() {
    return [{
        label: '',
        name: '',
        width: 80,
        align: 'left',
        sortable: false,
        clear: false,
        stype: 'input',
        searchable: false,
        searchoptions: {
          type: 'checkbox',
          clearSearch: false,
          dataInit: function(element) {
            $(element).removeClass('form-control')
            $(element).parent().removeClass('text-center').css({
              'text-align': 'left',
              'padding-left': '12px', // Dorong sedikit ke kanan
              'white-space': 'nowrap'
            });
            $(element).after("<span style='margin-left: 8px; font-weight: bold; vertical-align: middle; cursor: pointer;'>NO.</span>");
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
        formatter: function(value, rowOptions, rowData) {
          // 3. Mensejajarkan checkbox Baris Data di bawah (Gunakan padding yang sama persis)
          // DITULIS DALAM SATU BARIS TANPA ENTER
          return "<div style='white-space: nowrap; padding-left: 14px;'><input type='checkbox' name='aco_ids[]' value='" + rowData.id + "' onchange='checkboxHandler(this)' style='margin: 0; vertical-align: middle; cursor: pointer;'><span class='custom-rn-text' style='display: inline-block; vertical-align: middle; margin-left: 8px;'></span></div>";
        }
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
        // rownumbers: true,
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
          rows: 'rows'
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
          // activeGrid = $(this)
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

          let page = grid.jqGrid('getGridParam', 'page');
          let rowNum = grid.jqGrid('getGridParam', 'rowNum');
          let ids = grid.getDataIDs();

          // Hitung index awal berdasarkan halaman (jika rowNum 0 / All, set index ke 0)
          let startIndex = (rowNum == 0) ? 0 : (page - 1) * parseInt(rowNum);

          $.each(ids, function(index, id) {
            let currentNumber = startIndex + index + 1;
            // Tembak angka tersebut ke dalam span di masing-masing baris
            grid.find(`tr#${id} span.custom-rn-text`).text(currentNumber);
          });

          changeJqGridRowListText()

          $(document).unbind('keydown.detailGrid')
          setDetailGridBindKeys($(this))
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
      // .toolbarBindKeys()
      // .customBindKeys()
      .customPager()

    // loadClearFilter($('#acoGrid'))
    initGlobalSearch($('#acoGrid'), urlMaster)
  }
</script>