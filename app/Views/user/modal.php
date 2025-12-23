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
              <div class="col-12 col-sm-9 col-md-10">
                <input type="hidden" name="id" class="form-control" readonly>
              </div>
            </div>
            <!-- <input type="text" name="id" class="form-control" hidden> -->
            <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Nama Lengkap <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="fullname" class="form-control">
              </div>
            </div>
            <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Username <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="username" class="form-control">
              </div>
            </div>
            <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Email <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="email" class="form-control">
              </div>
            </div>
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

            <div class="row form-group rolediv">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Role <span class="text-danger"></span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <select name="role_ids[]" id="multiple" class="select2bs4 form-control" multiple="multiple"></select>
              </div>
            </div>

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
  let modalBody = $('#crudModal').find('.modal-body').html()

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
      const data = [];
      form.find('[name]').each(function() {
        const el = $(this);
        const name = el.attr('name');
        let value = el.val();

        // Support multiple select / array input
        if (Array.isArray(value)) {
          value.forEach(v => data.push({
            name,
            value: v
          }));
        } else {
          data.push({
            name,
            value
          });
        }
      });

      // Tambahkan selectedRows ACO
      // const dataAcos = {
      //   aco_ids: selectedRows
      // };
      // data.push({
      //   name: 'aco_ids',
      //   value: JSON.stringify(dataAcos)
      // });

      // Tambahkan grid info / tambahan
      data.push({
        name: 'sortIndex',
        value: $('#jqGrid').getGridParam('sortname')
      });
      data.push({
        name: 'sortOrder',
        value: $('#jqGrid').getGridParam('sortorder')
      });
      data.push({
        name: 'filters',
        value: $('#jqGrid').getGridParam('postData').filters
      });
      data.push({
        name: 'indexRow',
        value: indexRow
      });
      data.push({
        name: 'page',
        value: page
      });
      data.push({
        name: 'limit',
        value: limit
      });

      // Tentukan URL & method sesuai action
      switch (action) {
        case 'add':
          method = 'POST';
          url = `${API_URL}/users`;
          break;
        case 'edit':
          method = 'PATCH';
          url = `${API_URL}/users/${userId}`;
          break;
        case 'delete':
          method = 'DELETE';
          url = `${API_URL}/users/${userId}`;
          break;
        default:
          method = 'POST';
          url = `${API_URL}/users`;
      }

      console.log(action, url, method);

      // Disable button & loader
      $(this).attr('disabled', '');
      $('#processingLoader').removeClass('d-none');

      try {
        const response = await ajaxWithRefresh({
          url: url,
          method: method,
          dataType: 'JSON',
          data: data
        });

        // Success handling
        form.trigger('reset');
        $('#crudModal').modal('hide');
        selectedRows = [];
        const id = response.data.id;

        grid.trigger('reloadGrid', {
          page: response.data.page
        });
        // $('#userRoleGrid').trigger('reloadGrid', {
        //   postData: {
        //     proses: 'reload'
        //   }
        // });
        // $('#userAclGrid').trigger('reloadGrid', {
        //   postData: {
        //     proses: 'reload'
        //   }
        // });

        if (response.data.grp === 'FORMAT') updateFormat(response.data);

      } catch (error) {
        if (error.status !== 422) {
          showDialog('error', error.responseJSON?.message || 'Terjadi kesalahan');
          // $('.is-invalid').removeClass('is-invalid');
          // $('.invalid-feedback').remove();
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

  // function create
  function createUser() {

    let form = $('#crudForm')

    $('.modal-loader').removeClass('d-none')
    $('.rolediv').hide()
    form.trigger('reset')
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i>Save`)
    form.data('action', 'add')
    $('#crudModalTitle').text('Add User')

    $('#crudModal').modal('show')
    $('.modal-loader').addClass('d-none')

  }

  // function update
  async function updateUser(userId) {

    const form = $('#crudForm');
    form.data('action', 'edit');
    form.trigger('reset');
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i> Save`);
    form.find(`.sometimes`).hide();
    $('#crudModalTitle').text('Edit User');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    $('.modal-loader').removeClass('d-none');

    try {
      // Tunggu semua async task selesai
      await setRoleOptions(form);
      await showUser(form, userId);

      // Load ACO grid
      // $('#acoGrid').jqGrid('setGridParam', {
      //   url: `${apiUrl}acos/getuseracl`,
      //   postData: {
      //     user_id: userId
      //   },
      //   datatype: "json"
      // }).trigger('reloadGrid');

      // Tampilkan modal
      $('#crudModal').modal('show');
      $('.rolediv').show()

    } catch (error) {
      console.error(error);
    } finally {
      $('.modal-loader').addClass('d-none');
      
    }

  }

  function initLookup() {

    // --- INSTANSIASI CLASS ---
    new LookupComponent('.jenisorder-lookup', {
      title: 'Testing Lookup',
      endpoint: 'menu', // Endpoint API
      searching: ['keterangan'],

      // Hook sebelum request ke server
      beforeProcess: function() {
        // 'this' mengacu pada instance class, kita update properti postData
        // this.settings.postData = {
        //   Aktif: 'AKTIF',
        //   custom_filter: 'TEST'
        // };
      },

      // Saat data dipilih
      onSelectRow: (data, inputEl) => {
        // Kita pakai document.querySelector untuk ambil elemen lain (pengganti jquery selector)
        // Mengisi Input Hidden ID
        const idInput = document.querySelector('[name="jenisorder_id"]');
        if (idInput) idInput.value = data.id;

        // Mengisi Input Teks (Display)
        inputEl.value = data.keterangan;
      },

      // Saat tombol silang / cancel ditekan
      onCancel: (inputEl) => {
        // Class otomatis menyimpan nilai lama di 'this.currentValue'
        // Tapi karena kita passing element, kita kembalikan logic manualnya
        // (Note: Di class ini saya sudah handle logic revert valuenya sebenarnya)
      },

      // Saat input dihapus manual
      onClear: (inputEl) => {
        // Reset Hidden ID
        const idInput = document.querySelector('[name="jenisorder_id"]');
        if (idInput) idInput.value = '';

        // Reset Form Lain
        const upahId = document.querySelector('[name="upah_id"]');
        if (upahId) upahId.value = '';

        const upah = document.querySelector('[name="upah"]');
        if (upah) upah.value = '';

        inputEl.value = '';
      }
    });

  }

  async function setRoleOptions(relatedForm) {
    try {
      // Kosongkan select
      relatedForm.find('[name="role_ids[]"]').empty();

      // Ambil data roles dari API
      const response = await ajaxWithRefresh({
        url: `${API_URL}/role`,
        method: 'GET',
        dataType: 'JSON'
      });

      // Tambahkan option ke select
      response.data.forEach(role => {
        const option = new Option(role.rolename, role.id);
        relatedForm.find('[name="role_ids[]"]').append(option);
      });

      // Trigger change di akhir sekali saja
      relatedForm.find('[name="role_ids[]"]').trigger('change');

    } catch (error) {
      console.error('Error loading roles:', error);
      throw error; // agar bisa ditangkap di caller
    }
  }


  async function showUser(form, userId) {
    try {
      const response = await ajaxWithRefresh({
        url: `${API_URL}/users/${userId}`,
        method: 'GET',
        dataType: 'JSON'
      });

      // Populate form fields
      populateForm(form, response.data);

      // Populate roles
      const roleIds = response.roles.map(role => role.role_id);
      form.find(`[name="role_ids[]"]`).val(roleIds).trigger('change');

      console.log(roleIds);

    } catch (error) {
      // Error handling
      const msg = error.responseJSON;
      showDialog('error', msg.messages.error);
      throw error; // biar bisa ditangkap di caller
    }
  }
</script>