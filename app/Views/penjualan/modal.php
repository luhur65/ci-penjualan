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

            <!-- <div class="row form-group">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Status Aktif <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <input type="hidden" name="statusaktif">
                <input type="text" name="statusaktiftext" id="statusaktiftext" class="form-control lg-form">

              </div>
            </div> -->
            <div class="row form-group statusaktif">
              <div class="col-12 col-sm-3 col-md-2">
                <label class="col-form-label">
                  Status Aktif <span class="text-danger">*</span>
                </label>
              </div>
              <div class="col-12 col-sm-9 col-md-10">
                <select name="statusaktif" id="statusaktif" class="select2bs4 form-select"></select>
              </div>
            </div>

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

                <!-- <table id="acoGrid"></table> -->

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
          url = `${API_URL}/penjualan`;
          break;
        case 'edit':
          method = 'PATCH';
          url = `${API_URL}/penjualan/${userId}`;
          break;
        case 'delete':
          method = 'DELETE';
          url = `${API_URL}/penjualan/${userId}`;
          break;
        default:
          method = 'POST';
          url = `${API_URL}/penjualan`;
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

        $('#jqGrid').jqGrid('setGridParam', {
          page: response.data.page
        }).trigger('reloadGrid');
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

        // if (response.data.grp === 'FORMAT') updateFormat(response.data);

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
  async function createpenjualan() {

    let form = $('#crudForm')

    $('.modal-loader').removeClass('d-none')
    $('.rolediv').hide()
    form.trigger('reset')
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i>Save`)
    form.data('action', 'add')
    $('#crudModalTitle').text('Add penjualan')
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    await setStatusAktifOptions(form);

    $('#crudModal').modal('show')
    $('.modal-loader').addClass('d-none')

  }

  // function update
  async function updatepenjualan(id) {

    const form = $('#crudForm');
    form.data('action', 'edit');
    form.trigger('reset');
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i> Save`);
    form.find(`.sometimes`).hide();
    $('#crudModalTitle').text('Edit penjualan');
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
  async function deletepenjualan(id) {

    const form = $('#crudForm');
    form.data('action', 'delete');
    form.trigger('reset');
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i> Delete`);
    form.find('#btnSubmit').removeClass('btn-primary');
    form.find('#btnSubmit').addClass('btn-danger');
    form.find(`.sometimes`).hide();
    $('#crudModalTitle').text('Delete penjualan');
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