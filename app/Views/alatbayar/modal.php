<div class="modal modal-fullscreen" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="#" id="crudForm">
      <div class="modal-content">

        <div class="modal-header">
          <p class="modal-title mx-2" id="crudModalTitle"></p>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          </button>
        </div>

        <div class="modal-body">
          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2" style="display:none">
              <label class="col-form-label">ID</label>
            </div>
            <div class="col-12 col-sm-9 col-md-10" style="display:none">
              <input type="hidden" name="id" class="form-control" readonly>
            </div>
          </div>

          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">
                Nama Alat Bayar <span class="text-danger">*</span>
              </label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <input type="text" name="nama" class="form-control" maxlength="100">
            </div>
          </div>

          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">
                Keterangan
              </label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <textarea name="keterangan" class="form-control" rows="3" maxlength="255"></textarea>
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


      </div>
    </form>
  </div>
</div>

<script>
  let draftManager;
  let modalBody = $('#crudModal').find('.modal-body').html();

  draftManager = new DraftFormManager('#crudForm', {
    debug: true,
    expiry: 1000 * 60 * 60 * 24
  });

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
      let alatbayarId = form.find('[name=id]').val()

      // Ambil semua elemen dengan name
      const data = {};
      form.find('[name]').not('.ui-jqgrid [name]').each(function() {
        const el = $(this);
        const name = el.attr('name').replace('[]', '');
        let value = el.val();

        if (Array.isArray(value)) {
          data[name] = value; // langsung jadi array
        } else {
          data[name] = value;
        }
      });

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
          url = `${API_URL}/alatbayar`;
          break;
        case 'edit':
          method = 'PATCH';
          url = `${API_URL}/alatbayar/${alatbayarId}`;
          break;
        case 'delete':
          method = 'DELETE';
          url = `${API_URL}/alatbayar/${alatbayarId}`;
          break;
        default:
          method = 'POST';
          url = `${API_URL}/alatbayar`;
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

        if (typeof draftManager !== 'undefined' && action === 'add') {
          draftManager.clear();
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
          loader.resetGridState(false);

          if (action === 'delete') {
            let targetId = payload.id || '';
            let targetIndex = payload.offset % loader.rowsPerPage;

            loader.loadGridData(postData, payload.page, loader.rowsPerPage, 'down', 'jump', function() {
              setTimeout(() => {
                let ids = grid.getDataIDs();
                let finalId = targetId || ids[Math.min(targetIndex, ids.length - 1)];
                if (!finalId) return;

                grid.jqGrid('setSelection', finalId, true);
                scrollToRow(grid, finalId);
              }, 100);
            });
          } else {
            // ADD / EDIT
            loader.loadGridData(postData, payload.page, loader.rowsPerPage, 'down', 'jump', function() {
              setTimeout(() => {
                grid.jqGrid('setSelection', payload.id, true);
                scrollToRow(grid, payload.id);
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

  });

  // function create
  async function createalatbayar() {

    let form = $('#crudForm')

    draftManager.pause();

    $('.modal-loader').removeClass('d-none')
    $('.rolediv').hide()
    form.trigger('reset')
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i>Save`)
    form.data('action', 'add')
    $('#crudModalTitle').text('Add alatbayar')
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    form.find('input, textarea').prop('readonly', false);

    await setStatusAktifOptions(form);

    // menampilkan tombol
    if (localStorage.getItem(draftManager.getKey())) {
      draftManager.restore();
    }

    $('#crudModal').modal('show')
    $('.modal-loader').addClass('d-none')

  }

  // function update
  async function updatealatbayar(id) {

    const form = $('#crudForm');
    form.data('action', 'edit');
    form.trigger('reset');
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i> Save`);
    form.find(`.sometimes`).hide();
    $('#crudModalTitle').text('Edit alatbayar');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    form.find('input, textarea').prop('readonly', false);

    $('.modal-loader').removeClass('d-none');

    try {
      // Tunggu semua async task selesai
      const response = await ajaxWithRefresh({
        url: `${API_URL}/alatbayar/${id}`,
        method: 'GET',
        dataType: 'JSON'
      });

      form.find('[name="id"]').val(response.id);
      form.find('[name="nama"]').val(response.nama);
      form.find('[name="keterangan"]').val(response.keterangan);      // Tampilkan modal
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
  async function deletealatbayar(id) {

    const form = $('#crudForm');
    form.data('action', 'delete');
    form.trigger('reset');
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i> Delete`);
    form.find('#btnSubmit').removeClass('btn-primary');
    form.find('#btnSubmit').addClass('btn-danger');
    form.find(`.sometimes`).hide();
    $('#crudModalTitle').text('Delete alatbayar');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    $('.modal-loader').removeClass('d-none');

    try {
      // Tunggu semua async task selesai
      const response = await ajaxWithRefresh({
        url: `${API_URL}/alatbayar/${id}`,
        method: 'GET',
        dataType: 'JSON'
      });

      form.find('[name="id"]').val(response.id);
      form.find('[name="nama"]').val(response.nama).prop('readonly', true);
      form.find('[name="keterangan"]').val(response.keterangan).prop('readonly', true);

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
    // daftar lookup
    

  }

  async function setRoleOptions(relatedForm) {
    try {
      // Kosongkan select
      relatedForm.find('[name="role_ids[]"]').empty();

      // Ambil data roles dari API
      const response = await ajaxWithRefresh({
        url: `${API_URL}/roles`,
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

  async function setStatusAktifOptions(relatedForm) {
    try {
      // Kosongkan select
      relatedForm.find('[name="statusaktif"]').empty();
      // relatedForm.find('[name="statusaktif"]').append(
      //   new Option('-- PILIH STATUS AKTIF --', '', false, true)
      // ).trigger('change')

      // Ambil data roles dari API
      const response = await ajaxWithRefresh({
        url: `${API_URL}/parameter/lookup`,
        method: 'GET',
        dataType: 'JSON',
        data: {
          grp: 'STATUS AKTIF',
          subgrp: 'STATUS AKTIF'
        }
      });

      // Tambahkan option ke select
      response.data.forEach(response => {
        const option = new Option(response.text, response.id);
        relatedForm.find('[name="statusaktif"]').append(option);
      });

      // Trigger change di akhir sekali saja
      relatedForm.find('[name="statusaktif"]').trigger('change');

    } catch (error) {
      console.error('Error loading roles:', error);
      throw error; // agar bisa ditangkap di caller
    }
  }

  async function showalatbayar(form, alatbayarId) {
    const response = await ajaxWithRefresh({
      url: `${API_URL}/alatbayar/${alatbayarId}`,
      method: 'GET',
      dataType: 'JSON'
    });

    // Populate form fields
    populateForm(form, response.data);

    // Populate roles
    // const roleIds = response.roles.map(role => role.role_id);
    // form.find(`[name="role_ids[]"]`).val(roleIds).trigger('change');

  }
</script>