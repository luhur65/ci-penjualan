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
            <button type="button" id="btnGetLastData" class="btn btn-info ml-auto" style="display: none;">
              <i class="fa fa-history"></i> Last Data
            </button>
          </div>
        </form>
      </div>
    </form>
  </div>
</div>

<script>
  let draftManager;
  let modalBody = $('#crudModal').find('.modal-body').html();

  $(document).ready(function() {

    draftManager = new DraftFormManager('#crudForm', {
      debug: true,
      expiry: 1000 * 60 * 60 * 24
    });

    let submitButton = $('#btnSubmit');
    let cancelButton = $('#btnCancel');
    let getLastDataButton = $('#btnGetLastData');

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

      // Tambahkan grid info / tambahan
      data.page = parseInt($('#jqGrid').getGridParam('page'));
      data.limit = parseInt($('#jqGrid').getGridParam('rowNum'));
      data.sortIndex = $('#jqGrid').getGridParam('sortname');
      data.sortOrder = $('#jqGrid').getGridParam('sortorder');
      data.filters = $('#jqGrid').getGridParam('postData').filters;
      data.indexRow = indexRow;

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
        const response = await ajaxWithRefresh({
          url: url,
          method: method,
          dataType: 'JSON',
          contentType: 'application/json',
          data: JSON.stringify(data)
        });

        if (action === 'add') {
          draftManager.clear();
          getLastDataButton.hide();
        }

        // Success handling
        form.trigger('reset');
        $('#crudModal').modal('hide');
        selectedRows = [];
        id = response.data.id;
        let payload = response.data;
        let grid = $('#jqGrid');
        let loader = grid.data('lazyLoader');

        if (loader) {
          let postData = grid.jqGrid('getGridParam', 'postData');

          if (action === 'delete') {
            id = payload.id || '';
            indexRow = payload.offset % loader.rowsPerPage;

            loader.loadGridData(postData, payload.page, loader.rowsPerPage, 'down', 'jump', function() {
              setTimeout(() => {
                let ids = grid.getDataIDs();
                let bDiv = grid.parents('.ui-jqgrid-bdiv');
                let targetId = payload.id || ids[Math.min(indexRow, ids.length - 1)];

                if (!targetId) return;

                grid.jqGrid('setSelection', targetId, true);

                let rowEl = grid.find(`tr#${targetId}`);
                if (rowEl.length > 0) {
                  let scrollPos = rowEl.position().top + bDiv.scrollTop() -
                    (bDiv.height() / 2) +
                    (rowEl.height() / 2);
                  bDiv.scrollTop(scrollPos);
                }
              }, 100);
            });
          } else {
            // ADD / EDIT
            loader.loadGridData(postData, payload.page, loader.rowsPerPage, 'down', 'jump', function() {
              setTimeout(() => {
                grid.jqGrid('setSelection', payload.id, true);

                let bDiv = grid.parents('.ui-jqgrid-bdiv');
                let selectedRow = grid.find(`tr#${payload.id}`);

                if (selectedRow.length > 0) {
                  let scrollPos = selectedRow.position().top + bDiv.scrollTop() -
                    (bDiv.height() / 2) +
                    (selectedRow.height() / 2);
                  bDiv.scrollTop(scrollPos);
                }
              }, 100);
            });
          }
        } else {
          grid.jqGrid('setGridParam', {
            page: response.data.page
          }).trigger('reloadGrid');
        }

      } catch (error) {
        if (error.status !== 422) {
          showDialog('error', getErrorMessage(error));
        }

      } finally {
        $('#processingLoader').addClass('d-none');
        $(this).removeAttr('disabled');
      }


    });

    cancelButton.click(function() {
      $('#crudModal').find('.modal-body').html(modalBody);
    });

    getLastDataButton.click(function() {
      draftManager.restore();

      // (Opsional) Sembunyikan tombol setelah draf berhasil dimuat
      $(this).hide();
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

    draftManager.pause();

    $('.modal-loader').removeClass('d-none')
    $('.rolediv').hide()
    form.trigger('reset')
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i>Save`)
    form.data('action', 'add')
    $('#crudModalTitle').text('Add Role')
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    // menampilkan tombol
    if (localStorage.getItem(draftManager.getKey())) {
      $('#btnGetLastData').show();
    } else {
      $('#btnGetLastData').hide();
    }

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
        datatype: "json"
      }).trigger('reloadGrid');

      // Tampilkan modal
      $('#crudModal').modal('show');
      $('.rolediv').show()

    } catch (error) {
      // console.error(error);
      showDialog('error', getErrorMessage(error));
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
      showDialog('error', getErrorMessage(error));
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
    const response = await ajaxWithRefresh({
      url: `${API_URL}/roles/${roleId}`,
      method: 'GET',
      dataType: 'JSON'
    });

    // Populate form fields
    populateForm(form, response.data);
    selectedRows = response.acos.map((aco) => aco.aco_id); // get aco_id from response

  }

</script>