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
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">ID</label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <input type="text" name="id" class="form-control" readonly>
            </div>
          </div>
          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">
                Name <span class="text-danger">*</span>
              </label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <input type="text" name="name" class="form-control">
            </div>
          </div>
          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">
                Code <span class="text-danger">*</span>
              </label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <input type="text" name="code" class="form-control">
            </div>
          </div>
          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">
                Icon <span class="text-danger">*</span>
              </label>
            </div> 
            <div class="col-12 col-sm-9 col-md-10">
              <input type="text" name="icon" class="form-control" data-uppercase="false">
            </div>
          </div>
          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">
                Parent
              </label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <select name="parent_id" class="form-control"></select>
            </div>
          </div>
          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">
                Controller
              </label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <select name="controller" class="form-control"></select>
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
  $(document).ready(function() {
    let modal = $('#crudModal')
    let form = modal.find('form')

    form.formBindKeys()

    form.on('input', function() {
      form.data('hasChanged', true)
    })

    form.on('submit', function(event) {
      event.preventDefault()

      let action = $(this).data('action')

      if (action == 'add') {
        createMenu(this)
      } else if (action == 'edit') {
        let id = $(this).find('[name=id]').val()

        updateMenu(this, id)
      } else if (action == 'delete') {
        let id = $(this).find('[name=id]').val()

        destroyMenu(this, id)
      }
    })

    modal
      .on('hide.bs.modal', function() {
        form
          .find('[disabled].bg-white.state-delete')
          .removeAttr('disabled')
          .removeClass('bg-white state-delete')
      })
      .on('hidden.bs.modal', function() {
        focusToGrid()
      })
  })

  function addMenu() {
    let modal = $('#crudModal')
    let form = modal.find('form')

    $('.modal-loader').removeClass('d-none')

    // setMaxLength(form)

    // initSelect2(form.find(`[name=parent_id]`), modal)
    // initSelect2(form.find(`[name=controller]`), modal)

    Promise.all([
      // setParentOptions(form.find(`[name=parent_id]`)),
      // setControllerOptions(form.find(`[name=controller]`))
    ]).then(() => {
      form.find('.is-invalid').removeClass('is-invalid')
      form.find('.invalid-feedback').remove()
      form.find('#btnSubmit').html('<i class="fa fa-check"></i> Save')
      form.trigger('reset')
      modal.find('form').data('action', 'add')
      modal.find('#crudModalTitle').text('Add Menu')
      modal.modal('show')

      $('.modal-loader').addClass('d-none')
    })
  }

  function createMenu(form) {
    $('#processingLoader').removeClass('d-none')

    $.ajax({
      url: `${API_URL}/menu`,
      method: 'POST',
      datatType: 'JSON',
      data: $(form).serializeArray(),
      success: (response) => {
        $(form).data('hasChanged', false)
        $(form).find('.is-invalid').removeClass('is-invalid')
        $(form).find('.invalid-feedback').remove()
        $(form).parents('.modal').modal('hide')

        getPosition(response.data.id)
      },
      error: (error) => {
        $(form).find('.is-invalid').removeClass('is-invalid')
        $(form).find('.invalid-feedback').remove()

        const {
          status,
          responseJSON,
        } = error

        if (status == 422) {
          setErrorMessages(form, responseJSON.errors)
        } else {
          showDialog('error', error.statusText)
        }
      }
    }).always(() => {
      $('#processingLoader').addClass('d-none')
    })
  }

  function editMenu(id) {
    let modal = $('#crudModal')
    let form = modal.find('form')

    $('.modal-loader').removeClass('d-none')

    setMaxLength(form)

    initSelect2(form.find(`[name=parent_id]`), modal)
    initSelect2(form.find(`[name=controller]`), modal)

    Promise.all([
      setParentOptions(form.find(`[name=parent_id]`)),
      setControllerOptions(form.find(`[name=controller]`))
    ]).then(() => {
      showMenu(id)
        .then((response) => {
          form.find('.is-invalid').removeClass('is-invalid')
          form.find('.invalid-feedback').remove()
          form.find('#btnSubmit').html('<i class="fa fa-check"></i> Save')
          form.trigger('reset')
          modal.find('form').data('action', 'edit')
          modal.find('#crudModalTitle').text('Edit Menu')
          modal.modal('show')

          $.each(response.data, (index, value) => {
            form.find(`[name="${index}"]`).val(value)
          })

          $('.modal-loader').addClass('d-none')
        })
    })
  }

  function updateMenu(form, id) {
    $('#processingLoader').removeClass('d-none')

    $.ajax({
      url: `${API_URL}/menu/${id}`,
      method: 'PATCH',
      dataType: 'JSON',
      contentType: 'application/json',
      data: JSON.stringify({
        name: $(form).find('[name=name]').val(),
        code: $(form).find('[name=code]').val(),
        icon: $(form).find('[name=icon]').val(),
        parent_id: $(form).find('[name=parent_id]').val(),
        controller: $(form).find('[name=controller]').val(),
      }),
      success: (response) => {
        $(form).data('hasChanged', false)
        $(form).find('.is-invalid').removeClass('is-invalid')
        $(form).find('.invalid-feedback').remove()
        $(form).parents('.modal').modal('hide')

        getPosition(response.data.id)
      },
      error: (error) => {
        $(form).find('.is-invalid').removeClass('is-invalid')
        $(form).find('.invalid-feedback').remove()

        const {
          status,
          responseJSON,
        } = error

        if (status == 422) {
          setErrorMessages(form, responseJSON.errors)
        } else {
          showDialog('error', error.statusText)
        }
      }
    }).always(() => {
      $('#processingLoader').addClass('d-none')
    })
  }

  function deleteMenu(id) {
    let modal = $('#crudModal')
    let form = modal.find('form')

    $('.modal-loader').removeClass('d-none')

    showMenu(id)
      .then((response) => {
        form.find('.is-invalid').removeClass('is-invalid')
        form.find('.invalid-feedback').remove()
        form.find('#btnSubmit').html('<i class="fa fa-check"></i> Delete')
        form.trigger('reset')
        modal.find('form').data('action', 'delete')
        modal.find('#crudModalTitle').text('Delete Menu')
        modal.modal('show')

        $.each(response.data, (index, value) => {
          form
            .find(`[name="${index}"]`)
            .val(value)
            .attr('disabled', 'disabled')
            .addClass('bg-white state-delete')
        })

        $('.modal-loader').addClass('d-none')
      })
  }

  function destroyMenu(form, id) {
    $('#processingLoader').removeClass('d-none')

    $.ajax({
      url: `${API_URL}/menu/${id}`,
      method: 'DELETE',
      success: (response) => {
        $(form).data('hasChanged', false)
        $(form).find('.is-invalid').removeClass('is-invalid')
        $(form).find('.invalid-feedback').remove()
        $(form).parents('.modal').modal('hide')

        // Check if it was the last row in page
        if (grid.getGridParam('reccount') == 1) {
          grid.setGridParam({
            triggerClick: true,
            selectedIndex: grid.getGridParam('rowNum') - 1,
            page: grid.getGridParam('page') - 1
          }).trigger('reloadGrid')
        } else {
          grid.setGridParam({
            triggerClick: true,
          }).trigger('reloadGrid')
        }
      },
      error: (error) => {
        showDialog('error', error.statusText)
      }
    }).always(() => {
      $('#processingLoader').addClass('d-none')
    })
  }

  function setErrorMessages(form, errors) {
    $.each(errors, (index, value) => {
      $(form).find(`[name=${index}]`)
        .addClass('is-invalid')
        .after(`
          <div class="invalid-feedback">
            ${value}
          </div>
        `)
    })

    $(form).find('.is-invalid').first().focus()
  }

  function setMaxLength(form) {
    if (!$(form).data('hasMaxLength')) {
      $.ajax({
        url: `${API_URL}/menu/structure`,
        method: 'GET',
        dataType: 'JSON',
        success: (response) => {
          $.each(response.data, (index, row) => {
            $(form).find(`[name="${row.name}"]`).attr('maxlength', row.max_length)
          })

          $(form).data('hasMaxLength', true)
        }
      })
    }
  }

  function showMenu(id) {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: `${API_URL}/menu/${id}`,
        method: 'GET',
        dataType: 'JSON',
        success: (response) => {
          resolve(response)
        },
        error: (error) => {
          showDialog('error', error.statusText)

          reject(error)
        }
      })
    })
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

  function setParentOptions(element) {
    return new Promise((resolve, reject) => {
      element.empty()
      element.append(
        new Option('-- Pilih Parent --', '', false, true)
      ).trigger('change')

      $.ajax({
        url: `${API_URL}/menu`,
        method: 'GET',
        dataType: 'JSON',
        data: {
          limit: 0,
          filter_group: 'AND',
          filters: {
            ['aco_id']: `eq:null`
          }
        },
        success: (response) => {
          response.data.forEach(menu => {
            let option = new Option(menu.name, menu.id)

            element.append(option).trigger('change')
          });

          resolve()
        }
      })
    })
  }

  function setControllerOptions(element) {
    return new Promise((resolve, reject) => {
      element.empty()
      element.append(
        new Option('-- Pilih Controller --', '', false, true)
      ).trigger('change')

      $.ajax({
        url: `${API_URL}/menu/controller`,
        method: 'GET',
        dataType: 'JSON',
        data: {
          limit: 0
        },
        success: (response) => {
          response.data.forEach(menu => {
            let option = new Option(menu, menu)

            element.append(option).trigger('change')
          });

          resolve()
        }
      })
    })
  }
</script>
<?= $this->endSection() ?>