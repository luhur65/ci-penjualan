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

      data.sortIndex = $('#jqGrid').getGridParam('sortname');
      data.sortOrder = $('#jqGrid').getGridParam('sortorder');
      data.filters = $('#jqGrid').getGridParam('postData').filters;
      data.indexRow = indexRow;
      data.page = page;
      data.limit = limit;

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
        // selectedRows = [];
        const id = response.data.id;

        $('#jqGrid').jqGrid('setGridParam', {
          page: 1
        }).trigger('reloadGrid');

      } catch (error) {
        if (error.status !== 422) {
          showDialog('error', error.responseJSON?.message || 'Terjadi kesalahan');
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
      modal.modal('show')

    } catch (error) {
      if (error.status !== 422) {
        showDialog('error', error.responseJSON?.message || 'Terjadi kesalahan');
      }

    } finally {
      $('.modal-loader').addClass('d-none')
    }
  }

  // function addMenu() {
  //   let modal = $('#crudModal')
  //   let form = modal.find('form')

  //   $('.modal-loader').removeClass('d-none')

  //   // setMaxLength(form)

  //   // initSelect2(form.find(`[name=menu_parent]`), modal)
  //   // initSelect2(form.find(`[name=controller]`), modal)

  //   Promise.all([
  //     setParentOptions(form),
  //     setControllerOptions(form)

  //   ]).then(() => {
  //     form.find('.is-invalid').removeClass('is-invalid')
  //     form.find('.invalid-feedback').remove()
  //     form.find('#btnSubmit').html('<i class="fa fa-check"></i> Save')
  //     form.trigger('reset')
  //     modal.find('form').data('action', 'add')
  //     modal.find('#crudModalTitle').text('Add Menu')
  //     modal.modal('show')

  //     $('.modal-loader').addClass('d-none')
  //   })
  // }

  // function createMenu(form) {
  //   $('#processingLoader').removeClass('d-none')

  //   $.ajax({
  //     url: `${API_URL}/menu`,
  //     method: 'POST',
  //     datatType: 'JSON',
  //     data: $(form).serializeArray(),
  //     success: (response) => {
  //       $(form).data('hasChanged', false)
  //       $(form).find('.is-invalid').removeClass('is-invalid')
  //       $(form).find('.invalid-feedback').remove()
  //       $(form).parents('.modal').modal('hide')

  //       // getPosition(response.data.id)
  //     },
  //     error: (error) => {
  //       $(form).find('.is-invalid').removeClass('is-invalid')
  //       $(form).find('.invalid-feedback').remove()

  //       const {
  //         status,
  //         responseJSON,
  //       } = error

  //       if (status !== 422) {
  //         showDialog('error', responseJSON?.message || 'Terjadi kesalahan');
  //       } else {
  //         setErrorMessages(form, responseJSON.errors)
  //       }
  //     }
  //   }).always(() => {
  //     $('#processingLoader').addClass('d-none')
  //   })
  // }

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

    try {
      await setParentOptions(form)
      await setControllerOptions(form)
      await showMenu(form, id)

      modal.modal('show')

    } catch (error) {
      if (error.status !== 422) {
        // console.log(error)
        showDialog('error', error.responseJSON?.message || 'Terjadi kesalahan');
      }

    } finally {
      $('.modal-loader').addClass('d-none')
    }
  }

  // function updateMenu(id) {
  //   let modal = $('#crudModal')
  //   let form = modal.find('form')

  //   $('.modal-loader').removeClass('d-none')

  //   Promise.all([
  //     setParentOptions(form),
  //     setControllerOptions(form)
  //   ]).then(() => {
  //     showMenu(id)
  //       .then((response) => {
  //         form.find('.is-invalid').removeClass('is-invalid')
  //         form.find('.invalid-feedback').remove()
  //         form.find('#btnSubmit').html('<i class="fa fa-check"></i> Save')
  //         form.trigger('reset')
  //         modal.find('form').data('action', 'edit')
  //         modal.find('#crudModalTitle').text('Edit Menu')
  //         modal.modal('show')

  //         $.each(response.data, (index, value) => {
  //           form.find(`[name="${index}"]`).val(value)
  //         })

  //         $('.modal-loader').addClass('d-none')
  //       })
  //   })
  // }

  // function updateMenu(form, id) {
  //   $('#processingLoader').removeClass('d-none')

  //   $.ajax({
  //     url: `${API_URL}/menu/${id}`,
  //     method: 'PATCH',
  //     dataType: 'JSON',
  //     contentType: 'application/json',
  //     data: JSON.stringify({
  //       name: $(form).find('[name=name]').val(),
  //       code: $(form).find('[name=code]').val(),
  //       icon: $(form).find('[name=icon]').val(),
  //       menu_parent: $(form).find('[name=menu_parent]').val(),
  //       controller: $(form).find('[name=controller]').val(),
  //     }),
  //     success: (response) => {
  //       $(form).data('hasChanged', false)
  //       $(form).find('.is-invalid').removeClass('is-invalid')
  //       $(form).find('.invalid-feedback').remove()
  //       $(form).parents('.modal').modal('hide')

  //       getPosition(response.data.id)
  //     },
  //     error: (error) => {
  //       $(form).find('.is-invalid').removeClass('is-invalid')
  //       $(form).find('.invalid-feedback').remove()

  //       const {
  //         status,
  //         responseJSON,
  //       } = error

  //       if (status == 422) {
  //         setErrorMessages(form, responseJSON.errors)
  //       } else {
  //         showDialog('error', error.statusText)
  //       }
  //     }
  //   }).always(() => {
  //     $('#processingLoader').addClass('d-none')
  //   })
  // }

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

    try {
      await setParentOptions(form)
      await setControllerOptions(form)
      await showMenu(form, id)

      modal.modal('show')

    } catch (error) {
      if (error.status !== 422) {
        // console.log(error)
        showDialog('error', error.responseJSON?.message || 'Terjadi kesalahan');
      }

    } finally {
      $('.modal-loader').addClass('d-none')
    }
  }

  // function deleteMenu(id) {
  //   let modal = $('#crudModal')
  //   let form = modal.find('form')

  //   $('.modal-loader').removeClass('d-none')
  //   form.trigger('reset')

  //   showMenu(id)
  //     .then((response) => {
  //       form.find('.is-invalid').removeClass('is-invalid')
  //       form.find('.invalid-feedback').remove()
  //       form.find('#btnSubmit').html('<i class="fa fa-check"></i> Delete')
  //       modal.find('form').data('action', 'delete')
  //       modal.find('#crudModalTitle').text('Delete Menu')
  //       modal.modal('show')

  //       $.each(response.data, (index, value) => {
  //         form
  //           .find(`[name="${index}"]`)
  //           .val(value)
  //           .attr('disabled', 'disabled')
  //           .addClass('bg-white state-delete')
  //       })

  //       $('.modal-loader').addClass('d-none')
  //     })
  // }

  // function destroyMenu(form, id) {
  //   $('#processingLoader').removeClass('d-none')

  //   $.ajax({
  //     url: `${API_URL}/menu/${id}`,
  //     method: 'DELETE',
  //     success: (response) => {
  //       $(form).data('hasChanged', false)
  //       $(form).find('.is-invalid').removeClass('is-invalid')
  //       $(form).find('.invalid-feedback').remove()
  //       $(form).parents('.modal').modal('hide')

  //       // Check if it was the last row in page
  //       if (grid.getGridParam('reccount') == 1) {
  //         grid.setGridParam({
  //           triggerClick: true,
  //           selectedIndex: grid.getGridParam('rowNum') - 1,
  //           page: grid.getGridParam('page') - 1
  //         }).trigger('reloadGrid')
  //       } else {
  //         grid.setGridParam({
  //           triggerClick: true,
  //         }).trigger('reloadGrid')
  //       }
  //     },
  //     error: (error) => {
  //       showDialog('error', error.statusText)
  //     }
  //   }).always(() => {
  //     $('#processingLoader').addClass('d-none')
  //   })
  // }

  // function setErrorMessages(form, errors) {
  //   $.each(errors, (index, value) => {
  //     $(form).find(`[name=${index}]`)
  //       .addClass('is-invalid')
  //       .after(`
  //         <div class="invalid-feedback">
  //           ${value}
  //         </div>
  //       `)
  //   })

  //   $(form).find('.is-invalid').first().focus()
  // }

  // function setMaxLength(form) {
  //   if (!$(form).data('hasMaxLength')) {
  //     $.ajax({
  //       url: `${API_URL}/menu/structure`,
  //       method: 'GET',
  //       dataType: 'JSON',
  //       success: (response) => {
  //         $.each(response.data, (index, row) => {
  //           $(form).find(`[name="${row.name}"]`).attr('maxlength', row.max_length)
  //         })

  //         $(form).data('hasMaxLength', true)
  //       }
  //     })
  //   }
  // }

  // function showMenu(id) {
  //   return new Promise((resolve, reject) => {
  //     $.ajax({
  //       url: `${API_URL}/menu/${id}`,
  //       method: 'GET',
  //       dataType: 'JSON',
  //       success: (response) => {
  //         resolve(response)
  //       },
  //       error: (error) => {
  //         showDialog('error', error.statusText)

  //         reject(error)
  //       }
  //     })
  //   })
  // }

  async function showMenu(form, menuId) {
    try {
      const response = await ajaxWithRefresh({
        url: `${API_URL}/menu/${menuId}`,
        method: 'GET',
        dataType: 'JSON'
      });

      // Populate form fields
      populateForm(form, response.data);

    } catch (error) {
      // Error handling
      const msg = error.responseJSON;
      showDialog('error', msg.messages.error);
      throw error; // biar bisa ditangkap di caller
    }
  }

  function getPosition(id) {
    $.ajax({
      url: `${API_URL}/menu/${id}/position`,
      dataType: 'JSON',
      data: grid.getGridParam('postData'),
      success: (response) => {
        let position = response.position
        let perPage = grid.getGridParam('rowNum')
        let page = Math.ceil(position / perPage)
        let row = position - ((page - 1) * perPage)

        grid.setGridParam({
          selectedIndex: row - 1,
          page: page
        }).trigger('reloadGrid')
      },
      error: (error) => {
        const {
          status
        } = error

        if (status == 404) {
          grid.setGridParam({
            selectedIndex: 0,
            page: 1
          }).trigger('reloadGrid')
        } else {
          showDialog('error', error.statusText)
        }
      }
    })
  }

  async function setParentOptions(relatedForm) {
    try {
      // Kosongkan select
      relatedForm.find('[name="menu_parent"]').empty();
      relatedForm.find('[name="menu_parent"]').append(
        new Option('-- Pilih Menu Parent --', '', false, true)
      ).trigger('change')

      // Ambil data roles dari API
      const response = await ajaxWithRefresh({
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
      const response = await ajaxWithRefresh({
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

  // function setControllerOptions(element) {
  //   return new Promise((resolve, reject) => {
  //     element.empty()
  //     element.append(
  //       new Option('-- Pilih Controller --', '', false, true)
  //     ).trigger('change')

  //     $.ajax({
  //       url: `${API_URL}/menu/controllers`,
  //       method: 'GET',
  //       dataType: 'JSON',
  //       data: {
  //         limit: 0
  //       },
  //       success: (response) => {
  //         response.data.forEach(menu => {
  //           let option = new Option(menu, menu)

  //           element.append(option).trigger('change')
  //         });

  //         resolve()
  //       }
  //     })
  //   })
  // }
</script>
<?= $this->endSection() ?>