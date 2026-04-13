<script>
  // 1. Definisi Kolom ACL
  function getBaseColModelUserAcl() {
    return [{
        label: 'CLASS',
        name: 'class',
        width: (detectDeviceType() == "desktop") ? sm_dekstop_3 : sm_mobile_3,
      },
      {
        label: 'METHOD',
        name: 'method',
        width: (detectDeviceType() == "desktop") ? sm_dekstop_3 : sm_mobile_3,
      },
      {
        label: 'KETERANGAN',
        name: 'keterangan',
        width: (detectDeviceType() == "desktop") ? sm_dekstop_4 : sm_mobile_4,
      },
      {
        label: 'MODIFIED BY',
        name: 'modifiedby',
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
    ];
  }

  // 2. Fungsi Pembangun Kerangka Tabel ACL
  function loadUserAclGrid() {
    const gridId = '#userAclGrid';
    const pagerId = '#userAclPager';
    const namaMenuUserAcl = 'userAclDetail';
    const baseColModelUserAcl = getBaseColModelUserAcl();

    const grid = createJqGrid({
        gridId: gridId,
        pagerId: pagerId,
        url: 'local-init', // KUNCI: URL palsu agar satpam createJqGrid lolos
        page: 1,
        colModel: baseColModelUserAcl,
        options: {
          datatype: 'local', // KUNCI: Jangan nembak API dulu
          idPrefix: 'userAclGrid_',
          height: 350,
          rowNum: 10,
          rowList: [10, 20, 50, 0],
          sortname: 'class', // Default sort ke kolom class
          sortorder: 'asc',
          prmNames: {
            sort: 'sidx',
            order: 'sord',
            rows: 'rows',
            limit: 'limit'
          },
          resizeStop: function(newWidth, index) {
            // (Opsional) Logika simpan lebar kolom
          },
          onSelectRow: function(id) {
            // activeGrid = $(this);
            indexRow = $(this).jqGrid('getCell', id, 'rn') - 1;
            page = $(this).jqGrid('getGridParam', 'page');
            let rows = $(this).jqGrid('getGridParam', 'postData').limit || 10;
            if (indexRow >= rows) indexRow = (indexRow - rows * (page - 1));
          },
          loadComplete: function(data) {
            changeJqGridRowListText();
            
            $(document).off('keydown.detailGrid');
            setDetailGridBindKeys($(this));

            if (typeof initResize === "function") initResize($(this));

            if (triggerClick) {
              if (id != '') {
                indexRow = parseInt($(gridId).jqGrid('getInd', id)) - 1;
                $(`${gridId} [id="${$(gridId).getDataIDs()[indexRow]}"]`).click();
                id = '';
              } else if (indexRow != undefined) {
                $(`${gridId} [id="${$(gridId).getDataIDs()[indexRow]}"]`).click();
              }

              if ($(gridId).getDataIDs()[indexRow] == undefined) {
                $(`${gridId} [id="` + $(gridId).getDataIDs()[0] + `"]`).click();
              }

              triggerClick = false;
            } else {
              $(gridId).setSelection($(gridId).getDataIDs()[indexRow]);
            }

            setHighlight($(this));
          }
        }
      })
      .loadClearFilter()
      .clearGlobalSearch()
      .customPager({
        buttons: [] // Kosongkan tombol pager jika tidak diperlukan
      });

    // Logika Sortable Kolom
    $(document).ready(function() {
      $(gridId).parents('.ui-jqgrid-view').find("thead tr.ui-jqgrid-labels").sortable({
        stop: function(event, ui) {
          // Logika simpan posisi kolom
        }
      });
    });

    $("#resetColModel").on("click", function() {
      if ($(this).attr("data-grid-id") == "userAclGrid") {
        newResetColumns(baseColModelUserAcl, $(gridId), namaMenuUserAcl);
      }
    });
  }

  // 3. Fungsi Eksekusi Tembak API
  function loadUserAclData(userId) {
    let grid = $('#userAclGrid');

    let currentUrl = grid.jqGrid('getGridParam', 'url');
    let targetUrl = `${API_URL}/users/${userId}/acls`;

    // Jika URL-nya sama persis dan grid tidak kosong, berarti data sudah dimuat. Batal tembak API!
    if (currentUrl === targetUrl && grid.jqGrid('getGridParam', 'records') !== undefined) {
      return;
    }

    // Cegat request lama agar tidak tumpang tindih
    abortGridLastRequest(grid);

    grid.setGridParam({
      url: targetUrl,
      datatype: 'json',
      page: 1
    }).trigger('reloadGrid');
  }
</script>