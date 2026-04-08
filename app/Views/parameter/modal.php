<div class="modal modal-fullscreen" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="#" id="crudForm">
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

            <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  GROUP <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="grp" class="form-control">
              </div>
            </div>

            <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  SUBGROUP <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="subgrp" class="form-control">
              </div>
            </div>

            <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  NAMA PARAMETER <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="text" class="form-control">
              </div>
            </div>

            <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  KELOMPOK
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="kelompok" class="form-control">
              </div>
            </div>

            <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  TYPE
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="type" class="form-control">
              </div>
            </div>

            <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  DEFAULT
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="text" name="default" class="form-control">
              </div>
            </div>

            <div class="row form-group">
              <div class="col-12">
                <label class="col-form-label">MEMO</label>
                <table class="table table-bordered table-sm" id="tableMemo">
                  <thead>
                    <tr>
                      <th width="50px">AKSI</th>
                      <th>KEY <span class="text-danger">*</span></th>
                      <th>VALUE <span class="text-danger">*</span></th>
                    </tr>
                  </thead>
                  <tbody id="memoTbody">
                    <tr>
                      <td class="text-center"><button type="button" class="btn btn-danger btn-sm delete-row"><i class="fa fa-trash"></i></button></td>
                      <td><input type="text" name="key[]" class="form-control" value="MEMO" readonly></td>
                      <td>
                        <input type="hidden" name="color[]" value="">
                        <input type="text" name="value[]" class="form-control">
                      </td>
                    </tr>
                    <tr>
                      <td class="text-center"><button type="button" class="btn btn-danger btn-sm delete-row"><i class="fa fa-trash"></i></button></td>
                      <td><input type="text" name="key[]" class="form-control" value="SINGKATAN" readonly></td>
                      <td>
                        <input type="hidden" name="color[]" value="">
                        <input type="text" name="value[]" class="form-control">
                      </td>
                    </tr>
                    <tr>
                      <td class="text-center"><button type="button" class="btn btn-danger btn-sm delete-row"><i class="fa fa-trash"></i></button></td>
                      <td><input type="text" name="key[]" class="form-control" value="WARNA" readonly></td>
                      <td>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text p-0" style="width: 40px; overflow: hidden;">
                              <input type="color" name="color[]" class="border-0 m-0 p-0 color-picker" style="width: 100%; height: 100%;">
                            </span>
                          </div>
                          <input type="text" name="value[]" class="form-control color-text">
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-center"><button type="button" class="btn btn-danger btn-sm delete-row"><i class="fa fa-trash"></i></button></td>
                      <td><input type="text" name="key[]" class="form-control" value="WARNATULISAN" readonly></td>
                      <td>
                        <input type="hidden" name="color[]" value="">
                        <input type="text" name="value[]" class="form-control">
                      </td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td class="text-center">
                        <button type="button" class="btn btn-outline-primary btn-sm add-row"><i class="fa fa-plus"></i></button>
                      </td>
                      <td colspan="2"></td>
                    </tr>
                  </tfoot>
                </table>
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

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  let modalBody = $('#crudModal').find('.modal-body').html();

  $(document).ready(function() {

    let submitButton = $('#btnSubmit');
    let cancelButton = $('#btnCancel');

    // Add new row to MEMO table
    $(document).on('click', '.add-row', function() {
      let newRow = `
        <tr>
          <td class="text-center"><button type="button" class="btn btn-danger btn-sm delete-row"><i class="fa fa-trash"></i></button></td>
          <td><input type="text" name="key[]" class="form-control"></td>
          <td>
            <input type="hidden" name="color[]" value="">
            <input type="text" name="value[]" class="form-control">
          </td>
        </tr>
      `;
      $('#memoTbody').append(newRow);
    });

    // Delete row from MEMO table
    $(document).on('click', '.delete-row', function() {
      $(this).closest('tr').remove();
    });

    // Sync color picker with text input
    $(document).on('input', '.color-picker', function() {
      $(this).closest('.input-group').find('.color-text').val($(this).val());
    });

    // Sync text input with color picker
    $(document).on('input', '.color-text', function() {
      $(this).closest('.input-group').find('.color-picker').val($(this).val());
    });

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
        const name = el.attr('name');
        if (!name || name.trim() === '') return;

        let value = el.val();

        if (name.endsWith('[]')) {
          const cleanName = name.replace('[]', '');
          if (!data[cleanName]) data[cleanName] = [];
          data[cleanName].push(value);
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
          url = `${API_URL}/parameter`;
          break;
        case 'edit':
          method = 'PATCH';
          url = `${API_URL}/parameter/${userId}`;
          break;
        case 'delete':
          method = 'DELETE';
          url = `${API_URL}/parameter/${userId}`;
          break;
        default:
          method = 'POST';
          url = `${API_URL}/parameter`;
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

  });

  // function create
  async function createparameter() {

    let form = $('#crudForm')

    $('.modal-loader').removeClass('d-none')
    $('.rolediv').hide()
    form.trigger('reset')
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i>Save`)
    form.data('action', 'add')
    $('#crudModalTitle').text('Add parameter')
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    await setStatusAktifOptions(form);

    $('#crudModal').modal('show')
    $('.modal-loader').addClass('d-none')

  }

  // function update
  async function updateparameter(id) {

    const form = $('#crudForm');
    form.data('action', 'edit');
    form.trigger('reset');
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i> Save`);
    form.find(`.sometimes`).hide();
    $('#crudModalTitle').text('Edit parameter');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    $('.modal-loader').removeClass('d-none');

    try {
      // Tunggu semua async task selesai
      

      // Load detail grid
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
      // console.error(error);
      showDialog('error', getErrorMessage(error));
    } finally {
      $('.modal-loader').addClass('d-none');

    }

  }

  // function delete
  async function deleteparameter(id) {

    const form = $('#crudForm');
    form.data('action', 'delete');
    form.trigger('reset');
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i> Delete`);
    form.find('#btnSubmit').removeClass('btn-primary');
    form.find('#btnSubmit').addClass('btn-danger');
    form.find(`.sometimes`).hide();
    $('#crudModalTitle').text('Delete parameter');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    $('.modal-loader').removeClass('d-none');

    try {
      // Tunggu semua async task selesai
      

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

  async function showUser(form, userId) {
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

  }
</script>