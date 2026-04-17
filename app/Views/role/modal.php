<div class="modal modal-fullscreen" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="#" id="crudForm">
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
                Role Name <span class="text-danger">*</span>
              </label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <input type="text" name="rolename" class="form-control">
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
          <button type="button" class="btn btn-outline-primary" data-dismiss="modal">
            <i class="fa fa-times"></i>
            Cancel
          </button>
          <!-- <button type="button" id="btnGetLastData" class="btn btn-info ml-auto" style="display: none;">
            <i class="fa fa-history"></i> Last Data
          </button> -->
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
      let roleId = form.find('[name=id]').val()

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
          url = `${API_URL}/roles/${roleId}`;
          break;
        case 'delete':
          method = 'DELETE';
          url = `${API_URL}/roles/${roleId}`;
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
      draftManager.restore();
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
    // daftar lookup
    
  }


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