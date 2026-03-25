<div class="modal modal-fullscreen" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="crudForm">
      <div class="modal-content">
        <div class="modal-header">
          <p class="modal-title" id="crudModalTitle"></p>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2" style="display: none;">
              <label class="col-form-label">ID</label>
            </div>
            <div class="col-12 col-sm-9 col-md-10" style="display: none;">
              <input type="hidden" name="id" class="form-control" readonly>
            </div>
          </div>
          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">
                Nama Menu <span class="text-danger">*</span>
              </label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <input type="text" name="menuname" class="form-control">
            </div>
          </div>
          <div class="row form-group">
            <div class="col-12 col-md-2">
              <label class="col-form-label">
                PENGURUTAN
              </label>
            </div>
            <div class="col-12 col-md-10">
              <input type="text" name="menu_seq" class="form-control numbernoseparate">
            </div>
          </div>
          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">
                Icon
              </label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <input type="text" name="menu_icon" class="form-control" data-uppercase="false">
            </div>
          </div>
          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">
                Menu Parent
              </label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <select name="menu_parent" id="menu_parent" class="select2bs4 form-select"></select>
            </div>
          </div>
          <div class="row form-group sometimes_link">
            <div class="col-12 col-md-2">
              <label class="col-form-label">
                LINK
              </label>
            </div>
            <div class="col-12 col-md-10">
              <input type="text" name="menu_exe" class="form-control sometimes_link">
            </div>
          </div>
          <div class="row form-group controller">
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">
                Controller <span class="text-danger">*</span>
              </label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <select name="controller" id="controller" class="select2bs4 form-select"></select>
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

<?= $this->section('scripts') ?>
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
      let menuId = form.find('[name=id]').val()

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
          url = `${API_URL}/menu`;
          break;
        case 'edit':
          method = 'PATCH';
          url = `${API_URL}/menu/${menuId}`;
          break;
        case 'delete':
          method = 'DELETE';
          url = `${API_URL}/menu/${menuId}`;
          break;
        default:
          method = 'POST';
          url = `${API_URL}/menu`;
      }

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

    })

    cancelButton.click(function() {
      $('#crudModal').find('.modal-body').html(modalBody);
    });


  })

  async function createMenu() {
    const form = $('#crudForm');
    const modal = $('#crudModal');

    $('.modal-loader').removeClass('d-none')

    try {
      await setParentOptions(form)
      await setControllerOptions(form)

      form.find('.is-invalid').removeClass('is-invalid')
      form.find('.invalid-feedback').remove()
      form.find('#btnSubmit').html('<i class="fa fa-check"></i> Save')
      form.trigger('reset')
      form.data('action', 'add')
      modal.find('#crudModalTitle').text('Add Menu')
      $('.is-invalid').removeClass('is-invalid');
      $('.invalid-feedback').remove();
      modal.modal('show')

    } catch (error) {
      if (error.status !== 422) {
        showDialog('error', UIManager.getErrorMessage(error));
      }

    } finally {
      $('.modal-loader').addClass('d-none')
    }
  }

  async function updateMenu(id) {
    const form = $('#crudForm');
    const modal = $('#crudModal');
    form.trigger('reset')
    $('.modal-loader').removeClass('d-none')
    form.find('.is-invalid').removeClass('is-invalid')
    form.find('.invalid-feedback').remove()
    form.find('#btnSubmit').html('<i class="fa fa-check"></i> Save')
    form.data('action', 'edit')
    modal.find('#crudModalTitle').text('Edit Menu')
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    try {
      await setParentOptions(form)
      await setControllerOptions(form)
      await showMenu(form, id)

      modal.modal('show')

    } catch (error) {
      if (error.status !== 422) {
        showDialog('error', UIManager.getErrorMessage(error));
      }

    } finally {
      $('.modal-loader').addClass('d-none')
    }
  }

  async function deleteMenu(id) {
    const form = $('#crudForm');
    const modal = $('#crudModal');
    form.trigger('reset')
    $('.modal-loader').removeClass('d-none')
    form.find('.is-invalid').removeClass('is-invalid')
    form.find('.invalid-feedback').remove()
    form.find('#btnSubmit').html('<i class="fa fa-check"></i> Delete')
    form.data('action', 'delete')
    modal.find('#crudModalTitle').text('Delete Menu')
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    try {
      await setParentOptions(form)
      await setControllerOptions(form)
      await showMenu(form, id)

      modal.modal('show')

    } catch (error) {
      if (error.status !== 422) {
        showDialog('error', UIManager.getErrorMessage(error));
      }

    } finally {
      $('.modal-loader').addClass('d-none')
    }
  }

  async function showMenu(form, menuId) {
    const response = await HttpManager.ajaxWithRefresh({
      url: `${API_URL}/menu/${menuId}`,
      method: 'GET',
      dataType: 'JSON'
    });

    // Populate form fields
    UIManager.populateForm(form, response.data);

  }

  async function setParentOptions(relatedForm) {
    try {
      // Kosongkan select
      relatedForm.find('[name="menu_parent"]').empty();
      relatedForm.find('[name="menu_parent"]').append(
        new Option('-- Pilih Menu Parent --', '', false, true)
      ).trigger('change')

      // Ambil data roles dari API
      const response = await HttpManager.ajaxWithRefresh({
        url: `${API_URL}/menu/parents`,
        method: 'GET',
        dataType: 'JSON'
      });

      // Tambahkan option ke select
      response.data.forEach(response => {
        const option = new Option(response.menu, response.id);
        relatedForm.find('[name="menu_parent"]').append(option);
      });

      // Trigger change di akhir sekali saja
      relatedForm.find('[name="menu_parent"]').trigger('change');

    } catch (error) {
      console.error('Error loading roles:', error);
      throw error; // agar bisa ditangkap di caller
    }
  }

  async function setControllerOptions(relatedForm) {
    try {
      // Kosongkan select
      relatedForm.find('[name="controller"]').empty();
      relatedForm.find('[name="controller"]').append(
        new Option('-- Pilih Controller --', '', false, true)
      ).trigger('change')

      // Ambil data roles dari API
      const response = await HttpManager.ajaxWithRefresh({
        url: `${API_URL}/menu/controllers`,
        method: 'GET',
        dataType: 'JSON'
      });

      // Tambahkan option ke select
      response.data.forEach(response => {
        const option = new Option(response.class, response.class);
        relatedForm.find('[name="controller"]').append(option);
      });

      // Trigger change di akhir sekali saja
      relatedForm.find('[name="controller"]').trigger('change');

    } catch (error) {
      console.error('Error loading roles:', error);
      throw error; // agar bisa ditangkap di caller
    }
  }
  
</script>
<?= $this->endSection() ?>