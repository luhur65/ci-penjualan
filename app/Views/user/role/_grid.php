<script>
  function getBaseColModelUserRole() {
    return [{
        label: 'ROLE',
        name: 'rolename',
        width: (detectDeviceType() == "desktop") ? sm_dekstop_3 : sm_mobile_3,
      },
      {
        label: 'MODIFIED BY',
        name: 'modifiedby',
        width: (detectDeviceType() == "desktop") ? sm_dekstop_4 : sm_mobile_4,
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

  function loadUserRoleGrid() {
    const gridId = '#userRoleGrid';
    const pagerId = '#userRolePager'; // Sesuaikan jika ID pager Anda berbeda
    const namaMenuUserRole = 'userRoleDetail';
    const baseColModelUserRole = getBaseColModelUserRole();
    // const userPreferencesUserRole = fetchUserPreferencesFromServer(authUserId); // Pastikan authUserId tersedia global

    // let savedOrderUserRole = userPreferencesUserRole[namaMenuUserRole] || [];
    // const colModelUserRole = savedOrderUserRole.length > 0 ? reorderColModel(baseColModelUserRole, savedOrderUserRole) : baseColModelUserRole;

    // 1. Eksekusi Template Baru
    const grid = createJqGrid({
        gridId: gridId,
        pagerId: pagerId,
        url: 'local-init', // Kosongkan dulu, akan diisi saat baris Master diklik
        page: 1,
        colModel: baseColModelUserRole,
        // lazyLoad: true, // Nyalakan jika Anda ingin sub-tabel ini juga pakai Virtual Scroll
        // lazyLoadOptions: { rowsPerPage: 50 },
        options: {
          datatype: 'local', // KUNCI: Jangan nembak API sebelum Master menyuruhnya
          idPrefix: 'userRoleGrid_', // Mencegah bentrok ID HTML
          height: 350,
          rowNum: 10,
          rowList: [10, 20, 50, 0],
          sortname: 'rolename',
          sortorder: 'asc',
          prmNames: {
            sort: 'sidx',
            order: 'sord',
            rows: 'rows',
            limit: 'limit'
          },
          resizeStop: function(newWidth, index) {
            // const currentColModel = $(gridId).jqGrid('getGridParam', 'colModel');
            // const preferences = currentColModel.map(col => ({
            //   name: col.name,
            //   width: col.width + 'px'
            // }));
            // uploadUserPreferencesToServer(authUserId, namaMenuUserRole, preferences);
          },
          onSelectRow: function(id) {
            activeGrid = $(this);
            indexRow = $(this).jqGrid('getCell', id, 'rn') - 1;
            page = $(this).jqGrid('getGridParam', 'page');
            let rows = $(this).jqGrid('getGridParam', 'postData').rows || 10;
            if (indexRow >= rows) indexRow = (indexRow - rows * (page - 1));
          },
          loadComplete: function(data) {
            changeJqGridRowListText();

            $(document).unbind('keydown');
            setCustomBindKeys($(this));

            if (typeof initResize === "function") initResize($(this));

            // Logika Trigger Click Lama Anda
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
        buttons: []
      });


    // 3. Logika Sortable Kolom (Tetap dipertahankan)
    $(document).ready(function() {
      // Mengincar thead milik tabel spesifik agar tidak bentrok dengan masterGrid
      $(gridId).parents('.ui-jqgrid-view').find("thead tr.ui-jqgrid-labels").sortable({
        stop: function(event, ui) {
          const currentColModel = $(gridId).jqGrid('getGridParam', 'colModel');
          const preferences = currentColModel.map(col => ({
            name: col.name,
            width: col.width + 'px'
          }));
          uploadUserPreferencesToServer(authUserId, namaMenuUserRole, preferences);
        }
      });
    });

    $("#resetColModel").on("click", function() {
      if ($(this).attr("data-grid-id") == "userRoleGrid") {
        newResetColumns(baseColModelUserRole, $(gridId), namaMenuUserRole);
      }
    });
  }

  function loadUserRoleData(userId) {
    let grid = $('#userRoleGrid');

    // [OPTIMASI CACHE]: Cek apakah data yang tampil saat ini sudah milik user tersebut?
    // Ambil URL saat ini yang ada di Grid
    let currentUrl = grid.jqGrid('getGridParam', 'url');
    let targetUrl = `${API_URL}/users/${userId}/roles`;

    // Jika URL-nya sama persis dan grid tidak kosong, berarti data sudah dimuat. Batal tembak API!
    if (currentUrl === targetUrl && grid.jqGrid('getGridParam', 'records') !== undefined) return;

    // Cegat request lama agar tidak tumpang tindih
    abortGridLastRequest(grid);

    // [OPSIONAL]: Jika pakai LazyLoader di detail, reset cache
    // let loader = grid.data('lazyLoader');
    // if (loader) loader.resetGridState();

    grid.setGridParam({
      url: targetUrl,
      datatype: 'json',
      page: 1
    }).trigger('reloadGrid');
  }
</script>