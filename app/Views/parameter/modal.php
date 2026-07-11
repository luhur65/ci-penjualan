<div class="modal modal-fullscreen" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="#" id="crudForm" action="" method="post">
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
              <input type="hidden" name="type">
              <input type="text" name="grup" id="grup" class="form-control parameter-lookup">
            </div>
          </div>

          <div class="row form-group">
            <div class="col-12 col-sm-3 col-md-2">
              <label class="col-form-label">
                DEFAULT
              </label>
            </div>
            <div class="col-12 col-sm-9 col-md-10">
              <input type="hidden" name="default">
              <input type="text" name="defaulttext" id="defaulttext" class="form-control lg-form default-lookup">
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
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text p-0" style="width: 40px; overflow: hidden;">
                            <input type="color" name="color[]" class="border-0 m-0 p-0 color-picker" style="width: 100%; height: 100%;" value="#FFFFFF">
                          </span>
                        </div>
                        <input type="text" name="value[]" class="form-control color-text" value="#FFFFFF">
                      </div>
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
          <button type="button" class="btn btn-outline-primary" data-dismiss="modal">
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

  function isValidHex(color) {
    return /^#([0-9A-F]{3}){1,2}$/i.test(color);
  }

  draftManager = new DraftFormManager('#crudForm', {
    detailExtractors: {
      'memoList': function() {
        const rows = [];
        $('#memoTbody tr').each(function() {
          const row = {};
          $(this).find('[name]').each(function() {
            const nameAttr = $(this).attr('name');
            if (nameAttr) {
              const name = nameAttr.replace('[]', '');
              row[name] = $(this).val();
            }
          });
          // Hanya push jika baris tidak kosong
          if (Object.keys(row).length > 0) rows.push(row);
        });
        return rows;
      }
    },
    detailRestorers: {
      'memoList': function(rows) {
        rows.forEach(function(rowData) {
          const isDefault = ['MEMO', 'SINGKATAN', 'WARNA', 'WARNATULISAN'].includes(rowData.key);

          if (!isDefault) {
            addMemoRow(rowData);
          } else {
            // Cari baris default yang sudah ada berdasarkan value key-nya
            $('#memoTbody tr').each(function() {
              const row = $(this);
              if (row.find('[name="key[]"]').val() === rowData.key) {
                row.find('[name="value[]"]').val(rowData.value);
                row.find('[name="color[]"]').val(rowData.color);
              }
            });
          }
        });
      }
    }
  });

  function addMemoRow(data = {}) {
    let newRow = $(`
    <tr>
      <td class="text-center">
        <button type="button" class="btn btn-danger btn-sm delete-row">
          <i class="fa fa-trash"></i>
        </button>
      </td>
      <td>
        <input type="text" name="key[]" class="form-control" value="${data.key || ''}">
      </td>
      <td>
        <input type="hidden" name="color[]" value="${data.color || ''}">
        <input type="text" name="value[]" class="form-control" value="${data.value || ''}">
      </td>
    </tr>
  `);

    $('#memoTbody').append(newRow);

    // Jika ada warna, trigger perubahan jika diperlukan oleh plugin lain
    if (data.color) {
      newRow.find('[name="color[]"]').trigger('change');
    }
  }

  $(document).ready(function() {


    let submitButton = $('#btnSubmit');
    let cancelButton = $('#btnCancel');

    // Add new row to MEMO table
    $(document).on('click', '.add-row', function() {
      addMemoRow();
    });

    // Delete row from MEMO table
    $(document).on('click', '.delete-row', function() {
      $(this).closest('tr').remove();
      if (typeof draftManager !== 'undefined') draftManager.triggerSave();
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
      let id = form.find('[name=id]').val();

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

      // Susun memo dari key[] dan value[]
      const keys = data.key || [];
      const values = data.value || [];
      const colors = data.color || [];
      const memo = {};
      keys.forEach((k, i) => {
        if (!k) return;
        if (k === 'WARNA' || k === 'WARNATULISAN') {
          memo[k] = colors[i] || '';
        } else {
          memo[k] = values[i] || '';
        }
      });
      data.memo = memo;

      // Hapus field array yang tidak perlu dikirim
      delete data.key;
      delete data.value;
      delete data.color;

      // Tambahkan grid info
      data.page = parseInt($('#jqGrid').getGridParam('page')) || 1;
      data.limit = parseInt($('#jqGrid').getGridParam('rowNum')) || 10;
      data.sortIndex = $('#jqGrid').getGridParam('sortname');
      data.sortOrder = $('#jqGrid').getGridParam('sortorder');
      data.filters = $('#jqGrid').getGridParam('postData').filters;
      data.indexRow = indexRow;

      // Tentukan URL & method
      switch (action) {
        case 'add':
          method = 'POST';
          url = `${API_URL}/parameters`;
          break;
        case 'edit':
          method = 'PATCH';
          url = `${API_URL}/parameters/${id}`;
          break;
        case 'delete':
          method = 'DELETE';
          url = `${API_URL}/parameters/${id}`;
          break;
        default:
          method = 'POST';
          url = `${API_URL}/parameters`;
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
        if (error.status === 422) {
          const errors = error.responseJSON?.errors ?? {};
          $.each(errors, (field, message) => {
            if (field.startsWith('memo.')) {
              const key = field.replace('memo.', '');
              $('#memoTbody tr').each(function() {
                if ($(this).find('[name="key[]"]').val() === key) {
                  const input = $(this).find('[name="value[]"]');
                  input.addClass('is-invalid');
                  if (!input.next('.invalid-feedback').length) {
                    input.after(`<div class="invalid-feedback">${message}</div>`);
                  }
                }
              });
            }
          });
        } else {
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

    let form = $('#crudForm');

    draftManager.pause();

    $('.modal-loader').removeClass('d-none')
    $('.rolediv').hide()
    form.trigger('reset')
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i>Save`)
    form.data('action', 'add')
    $('#crudModalTitle').text('Add parameter')
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
      await showParameter(form, id);

      // Tampilkan modal
      $('#crudModal').modal('show');
      $('.rolediv').show()

    } catch (error) {
      console.error(error);
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
      await showParameter(form, id);


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

    $('.parameter-lookup').lookupV6({
      title: 'Lookup Type',
      lookupKey: 'parameterAllV4',
      lookupName: 'parameterLookup',
      typeData: 'JSON',
      data: [],
      searching: ['grp', 'subgrp', 'kelompok', 'text'],
      filterToolbar: true,
      onSelectRow: (parameter, element) => {
        $('#crudForm [name="type"]').val(parameter.id).trigger('change');
        element.val(parameter.grp);
        element.data('currentValue', element.val());
      },
      onClear: (element) => {
        element.val('')
        $('#crudForm [name="type"]').val('').trigger('change');
        element.data('currentValue', element.val())
      },
      onCancel: (element) => {
        if (element.val() !== element.data('currentValue')) {
          $('#crudForm [name="default"]').val('');
        }
      }
    });

    $('.default-lookup').lookupV6({
      title: 'Lookup Default',
      lookupKey: 'parameterMemoV4',
      lookupName: 'parameterLookup',
      typeData: 'LOCAL',
      data: parameterLookupData,
      searching: ['text'],
      labelColumn: false,
      postData: {
        grp: 'STATUS DEFAULT PARAMETER',
      },
      onSelectRow: (parameter, element) => {
        $('#crudForm [name="default"]').val(parameter.text);
        element.val(parameter.text);
        element.data('currentValue', element.val())
      },
      onClear: (element) => {
        element.val('')
        $('#crudForm [name="default"]').val('');
        element.data('currentValue', element.val())
      },
      onCancel: (element) => {
        if (element.val() !== element.data('currentValue')) {
          $('#crudForm [name="default"]').val('');
        }
      }
    });
  }


  async function showParameter(form, parameterId) {
    const response = await ajaxWithRefresh({
      url: `${API_URL}/parameters/${parameterId}`,
      method: 'GET',
      dataType: 'JSON'
    });

    // Populate form fields
    populateForm(form, response.data, [
      'grup',
      'defaulttext',
    ]);

    if (response.data.memo) {
      const memo = JSON.parse(response.data.memo);
      $.each(memo, (key, value) => {
        $('#memoTbody tr').each(function() {
          if ($(this).find('[name="key[]"]').val() === key) {
            $(this).find('[name="value[]"]').val(value);
            $(this).find('[name="color[]"]').val(value);
            $(this).find('.color-picker').val(value);
            $(this).find('.color-text').val(value);
          }
        });
      });
    }

  }

  // const originalVal = $.fn.val;

  // $.fn.val = function(value) {
  //   if (value === '') {
  //     console.trace('ADA YANG MENGOSONGKAN FIELD:', this.attr('name'), this);
  //   }
  //   return originalVal.apply(this, arguments);
  // };
</script>