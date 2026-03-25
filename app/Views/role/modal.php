<div class="modal modal-fullscreen" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="#" id="crudForm">
      <div class="modal-content">

        <div class="modal-header">
          <p class="modal-title" id="crudModalTitle"></p>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          </button>
        </div>
        <form action="" method="post">
          <div class="modal-body">
            <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2" style="display:none">
                <label class="col-form-label">ID</label>
              </div>
              <div class="col-12 col-sm-9 col-md-10" style="display:none">
                <input type="hidden" name="id" class="form-control" readonly>
              </div>
            </div>
            <!-- <input type="text" name="id" class="form-control" hidden> -->
            <!-- <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Nama Lengkap <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="fullname" class="form-control">
              </div>
            </div> -->
            <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Role Name <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="rolename" class="form-control">
              </div>
            </div>
            <!-- <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Email <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="email" class="form-control">
              </div>
            </div> -->
            <!-- <div class="row form-group sometimes">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Password <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <div class="input-group">
                  <input type="password" name="password" class="form-control password">
                  <div class="input-group-append">
                    <div class="input-group-text focusPass">
                      <span class="fas fa-eye toggle-password" toggle=".password"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
            <!-- <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Cabang <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="hidden" name="cabang_id">
                <input type="text" name="cabang" id="cabang" class="form-control cabang-lookup">
              </div>
            </div> -->
            <!-- <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Karyawan ID <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="hidden" name="karyawan_id">
                <input type="text" name="karyawan_id_nama" id="karyawan_id_nama" class="form-control lg-form karyawanhr_lookup">
              </div>
            </div> -->
            <!-- <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Status Karyawan <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <select name="karyawan_id" class="form-select select2bs4" style="width: 100%;">
                  <option value="">-- PILIH STATUS KARYAWAN --</option>
                </select>
              </div>
            </div> -->
            <!-- <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Dashboard
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="dashboard" class="form-control">
              </div>
            </div> -->

            <!-- <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Status Aktif <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="hidden" name="statusaktif">
                <input type="text" name="statusaktifnama" id="statusaktifnama" class="form-control lg-form statusaktif-lookup">

              </div>
            </div> -->
            <!-- <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Status Akses <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="hidden" name="statusakses">
                <input type="text" name="statusaksesnama" id="statusaksesnama" class="form-control lg-form statusakses-lookup">
              </div>
            </div> -->

            <!-- <div class="row form-group rolediv">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Role <span class="text-danger"></span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <select name="role_ids[]" id="multiple" class="select2bs4 form-control" multiple="multiple"></select>
              </div>
            </div> -->

            <div class="row form-group">
              <div class="col-12">

                <table id="acoGrid"></table>

              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-start">
            <button type="submit" id="btnSubmit" class="btn btn-primary">
              <i class="fa fa-check"></i>
              Save
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fa fa-times"></i>
              Cancel
            </button>
          </div>
        </form>
      </div>
    </form>
  </div>
</div>

<script>
  let modalBody = $('#crudModal').find('.modal-body').html();

  $(document).ready(function() {

    let submitButton = $('#btnSubmit');
    let cancelButton = $('#btnCancel');

    submitButton.click(async function(e) {
      e.preventDefault();

      let method;
      let url;
      let form = $('#crudForm');
      let action = form.data('action');
      let formData = form.serialize();
      let userId = form.find('[name=id]').val()

      // Ambil semua elemen dengan name
      const data = {};
      form.find('[name]').each(function() {
        const el = $(this);
        const name = el.attr('name').replace('[]', '');
        let value = el.val();

        if (Array.isArray(value)) {
          data[name] = value; // langsung jadi array
        } else {
          data[name] = value;
        }
      });

      data.acosIds = JSON.stringify(selectedRows);
      // const data = [];
      // form.find('[name]').each(function() {
      //   const el = $(this);
      //   const name = el.attr('name');
      //   let value = el.val();

      //   // Support multiple select / array input
      //   if (Array.isArray(value)) {
      //     value.forEach(v => data.push({
      //       name,
      //       value: v
      //     }));
      //   } else {
      //     data.push({
      //       name,
      //       value
      //     });
      //   }
      // });

      // Tambahkan selectedRows ACO
      // const dataAcos = {
      //   aco_ids: selectedRows
      // };
      // data.push({
      //   name: 'aco_ids',
      //   value: JSON.stringify(dataAcos)
      // });

      // Tambahkan grid info / tambahan
      data.page = parseInt($('#jqGrid').getGridParam('page'));
      data.limit = parseInt($('#jqGrid').getGridParam('rowNum'));
      data.sortIndex = $('#jqGrid').getGridParam('sortname');
      data.sortOrder = $('#jqGrid').getGridParam('sortorder');
      data.filters = $('#jqGrid').getGridParam('postData').filters;
      data.indexRow = indexRow;
      // data.push({
      //   name: 'sortIndex',
      //   value: $('#jqGrid').getGridParam('sortname')
      // });
      // data.push({
      //   name: 'sortOrder',
      //   value: $('#jqGrid').getGridParam('sortorder')
      // });
      // data.push({
      //   name: 'filters',
      //   value: $('#jqGrid').getGridParam('postData').filters
      // });
      // data.push({
      //   name: 'indexRow',
      //   value: indexRow
      // });
      // data.push({
      //   name: 'page',
      //   value: page
      // });
      // data.push({
      //   name: 'limit',
      //   value: limit
      // });

      // Tentukan URL & method sesuai action
      switch (action) {
        case 'add':
          method = 'POST';
          url = `${API_URL}/roles`;
          break;
        case 'edit':
          method = 'PATCH';
          url = `${API_URL}/roles/${userId}`;
          break;
        case 'delete':
          method = 'DELETE';
          url = `${API_URL}/roles/${userId}`;
          break;
        default:
          method = 'POST';
          url = `${API_URL}/roles`;
      }

      // console.log(action, url, method);

      // Disable button & loader
      $(this).attr('disabled', '');
      $('#processingLoader').removeClass('d-none');

      try {
        const response = await HttpManager.ajaxWithRefresh({
          url: url,
          method: method,
          dataType: 'JSON',
          contentType: 'application/json',
          data: JSON.stringify(data)
        });

        // Success handling
        form.trigger('reset');
        $('#crudModal').modal('hide');
        selectedRows = [];
        id = response.data.id;

        $('#jqGrid').jqGrid('setGridParam', {
          page: response.data.page
        }).trigger('reloadGrid');

      } catch (error) {
        if (error.status !== 422) {
          showDialog('error', UIManager.getErrorMessage(error));
        }

      } finally {
        $('#processingLoader').addClass('d-none');
        $(this).removeAttr('disabled');
      }


    });

    cancelButton.click(function() {
      $('#crudModal').find('.modal-body').html(modalBody);
    });

  });

  function clearSelectedRows() {
    selectedRows = []
    $('#acoGrid').trigger('reloadGrid')
  }

  async function selectAllRows() {
    const response = await ajaxWithRefresh({
      url: `${API_URL}/acos`,
      method: 'GET',
      dataType: 'JSON',
      data: {
        limit: 0,
        role_id: $('#crudForm').find('[name=id]').val()
      }
    });

    selectedRows = response.data.map((aco) => aco.id);

    $('#acoGrid').trigger('reloadGrid');
  }

  function checkboxHandler(element) {
    let value = $(element).val();

    if (element.checked) {
      selectedRows.push($(element).val())
      $(element).parents('tr').addClass('bg-light-blue')
    } else {
      $(element).parents('tr').removeClass('bg-light-blue')
      for (var i = 0; i < selectedRows.length; i++) {
        if (selectedRows[i] == value) {
          selectedRows.splice(i, 1);
        }
      }
    }
  }

  // function create
  function createRole() {

    let form = $('#crudForm')

    $('.modal-loader').removeClass('d-none')
    $('.rolediv').hide()
    form.trigger('reset')
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i>Save`)
    form.data('action', 'add')
    $('#crudModalTitle').text('Add Role')
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    $('#crudModal').modal('show')
    $('.modal-loader').addClass('d-none')

  }

  // function update
  async function updateRole(roleId) {

    const form = $('#crudForm');
    form.data('action', 'edit');
    form.trigger('reset');
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i> Save`);
    form.find(`.sometimes`).hide();
    $('#crudModalTitle').text('Edit Role');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    $('.modal-loader').removeClass('d-none');

    try {
      // Tunggu semua async task selesai
      // await setRoleOptions(form);
      await showRole(form, roleId);
      loadAcoGrid(roleId);

      // Load ACO grid
      $('#acoGrid').jqGrid('setGridParam', {
        url: `${API_URL}/acos`,
        postData: {
          role_id: roleId
        },
        datatype: "json"
      }).trigger('reloadGrid');

      // Tampilkan modal
      $('#crudModal').modal('show');
      $('.rolediv').show()

    } catch (error) {
      // console.error(error);
      showDialog('error', UIManager.getErrorMessage(error));
    } finally {
      $('.modal-loader').addClass('d-none');

    }

  }

  // function delete
  async function deleteRole(roleId) {

    const form = $('#crudForm');
    form.data('action', 'delete');
    form.trigger('reset');
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i> Delete`);
    form.find('#btnSubmit').removeClass('btn-primary');
    form.find('#btnSubmit').addClass('btn-danger');
    form.find(`.sometimes`).hide();
    $('#crudModalTitle').text('Delete User');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    $('.modal-loader').removeClass('d-none');

    try {
      // Tunggu semua async task selesai
      await showRole(form, roleId);


      // Tampilkan modal
      $('#crudModal').modal('show');
      $('.rolediv').show()

    } catch (error) {
      // console.error(error);
      showDialog('error', UIManager.getErrorMessage(error));
    } finally {
      $('.modal-loader').addClass('d-none');

    }

  }



  function initLookup() {

    // --- INSTANSIASI CLASS ---
    // new LookupComponent('.jenisorder-lookup', {
    //   title: 'Testing Lookup',
    //   endpoint: 'menu', // Endpoint API
    //   searching: ['keterangan'],

    //   // Hook sebelum request ke server
    //   beforeProcess: function() {
    //     // 'this' mengacu pada instance class, kita update properti postData
    //     // this.settings.postData = {
    //     //   Aktif: 'AKTIF',
    //     //   custom_filter: 'TEST'
    //     // };
    //   },

    //   // Saat data dipilih
    //   onSelectRow: (data, inputEl) => {
    //     // Kita pakai document.querySelector untuk ambil elemen lain (pengganti jquery selector)
    //     // Mengisi Input Hidden ID
    //     const idInput = document.querySelector('[name="jenisorder_id"]');
    //     if (idInput) idInput.value = data.id;

    //     // Mengisi Input Teks (Display)
    //     inputEl.value = data.keterangan;
    //   },

    //   // Saat tombol silang / cancel ditekan
    //   onCancel: (inputEl) => {
    //     // Class otomatis menyimpan nilai lama di 'this.currentValue'
    //     // Tapi karena kita passing element, kita kembalikan logic manualnya
    //     // (Note: Di class ini saya sudah handle logic revert valuenya sebenarnya)
    //   },

    //   // Saat input dihapus manual
    //   onClear: (inputEl) => {
    //     // Reset Hidden ID
    //     const idInput = document.querySelector('[name="jenisorder_id"]');
    //     if (idInput) idInput.value = '';

    //     // Reset Form Lain
    //     const upahId = document.querySelector('[name="upah_id"]');
    //     if (upahId) upahId.value = '';

    //     const upah = document.querySelector('[name="upah"]');
    //     if (upah) upah.value = '';

    //     inputEl.value = '';
    //   }
    // });

  }

  // async function setRoleOptions(relatedForm) {
  //   try {
  //     // Kosongkan select
  //     relatedForm.find('[name="role_ids[]"]').empty();

  //     // Ambil data roles dari API
  //     const response = await ajaxWithRefresh({
  //       url: `${API_URL}/role`,
  //       method: 'GET',
  //       dataType: 'JSON'
  //     });

  //     // Tambahkan option ke select
  //     response.data.forEach(role => {
  //       const option = new Option(role.rolename, role.id);
  //       relatedForm.find('[name="role_ids[]"]').append(option);
  //     });

  //     // Trigger change di akhir sekali saja
  //     relatedForm.find('[name="role_ids[]"]').trigger('change');

  //   } catch (error) {
  //     console.error('Error loading roles:', error);
  //     throw error; // agar bisa ditangkap di caller
  //   }
  // }


  async function showRole(form, roleId) {
    const response = await HttpManager.ajaxWithRefresh({
      url: `${API_URL}/roles/${roleId}`,
      method: 'GET',
      dataType: 'JSON'
    });

    // Populate form fields
    UIManager.populateForm(form, response.data);
    selectedRows = response.acos.map((aco) => aco.aco_id); // get aco_id from response

  }

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
      // {
      //   label: 'Status',
      //   name: 'status',
      //   width: (detectDeviceType() == "desktop") ? sm_dekstop_3 : sm_mobile_3,
      //   stype: 'select',
      //   searchoptions: {
      //     value: `<?php
                      //             $i = 1;

                      //             foreach ($data['combostatus'] as $status) :
                      //               echo "$status[param]:$status[parameter]";
                      //               if ($i !== count($data['combostatus'])) {
                      //                 echo ';';
                      //               }
                      //               $i++;
                      //             endforeach;

                      //             
                      ?>
      //   `,
      //     dataInit: function(element) {
      //       $(element).select2({
      //         width: 'resolve',
      //         theme: "bootstrap4"
      //       });
      //     }
      //   },
      // },
    ];
  }

  function loadAcoGrid(roleId) {

    let sortname = 'class';
    let sortorder = 'asc';

    $('#acoGrid')
      .jqGrid({
        styleUI: 'Bootstrap4',
        datatype: "local",
        // url: `${apiUrl}acos`,
        // postData: {
        //   role_id: roleId
        // },
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

          // $(document).unbind('keydown')
          // setCustomBindKeys($(this))
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