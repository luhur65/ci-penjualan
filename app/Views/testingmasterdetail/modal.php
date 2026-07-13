<!-- ============================================================
     MASTER MODAL - Add/Edit/Delete Penjualan (dengan tabel detail inline)
     ============================================================ -->
<div class="modal modal-fullscreen" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <form method="#" id="crudForm">
      <div class="modal-content">

        <div class="modal-header">
          <p class="modal-title mx-2" id="crudModalTitle"></p>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- ID (hidden) -->
          <input type="hidden" name="id">

          <div class="row">
            <!-- ===== KIRI: Header Penjualan ===== -->
            <div class="col-12">

              <!-- NO. BUKTI (auto-generated, readonly) -->
              <div class="form-group row">
                <label class="col-sm-4 col-form-label">No. Bukti</label>
                <div class="col-sm-8">
                  <div class="input-group">
                    <input type="text" name="no_bukti" id="no_bukti" class="form-control" readonly
                      placeholder="Otomatis..." maxlength="100" style="background:#f8f9fa;">
                    <div class="input-group-append">
                      <span class="input-group-text text-muted" title="Nomor otomatis">
                        <i class="fa fa-magic"></i> AUTO
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- TANGGAL BUKTI -->
              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Tanggal <span class="text-danger">*</span></label>
                <div class="col-sm-8">
                  <input type="text" name="tgl_bukti" id="tgl_bukti" class="form-control datepicker">
                </div>
              </div>

              <!-- PELANGGAN -->
              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Pelanggan <span class="text-danger">*</span></label>
                <div class="col-sm-8">
                  <input type="hidden" name="pelanggan_id" id="pelanggan_id">
                  <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control" placeholder="Ketik atau Pilih Pelanggan">
                </div>
              </div>

              <!-- GRAND TOTAL -->
              <div class="form-group row mt-3">
                <label class="col-sm-4 col-form-label font-weight-bold">Grand Total</label>
                <div class="col-sm-8">
                  <div class="form-control-plaintext font-weight-bold text-success h5" id="grandTotalPreview">Rp 0</div>
                </div>
              </div>

              <!-- DELETE PREVIEW -->
              <div class="delete-preview" style="display:none">
                <div class="alert alert-danger mt-2">
                  <i class="fa fa-exclamation-triangle"></i>
                  <strong>Perhatian!</strong> Hapus data ini akan menghapus semua item detail terkait!
                  <br><strong id="deletePreviewNoBukti"></strong>
                </div>
              </div>

            </div>

            <!-- ===== KANAN: Tabel Detail Inline ===== -->
            <div class="col-12">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <!-- <label class="font-weight-bold mb-0"><i class="fa fa-list-ul text-primary"></i> Item Penjualan</label> -->
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahItem">
                  <i class="fa fa-plus"></i> Tambah Baris
                </button>
              </div>

              <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-sm table-bordered table-hover" id="tableItemDetail">
                  <thead class="thead-light sticky-top">
                    <tr>
                      <th style="width:35px" class="text-center">No</th>
                      <th>Nama Barang</th>
                      <th style="width:80px" class="text-center">Qty</th>
                      <th style="width:130px" class="text-right">Harga</th>
                      <th style="width:140px" class="text-right">Subtotal</th>
                      <th style="width:40px" class="text-center">-</th>
                    </tr>
                  </thead>
                  <tbody id="tbodyItemDetail">
                    <!-- baris pertama otomatis ada -->
                  </tbody>
                </table>
              </div>

              <div class="text-muted small mt-1" id="infoItemCount"></div>
            </div>
          </div><!-- /.row -->
        </div><!-- /.modal-body -->

        <div class="modal-footer justify-content-start">
          <button type="submit" id="btnSubmit" class="btn btn-primary">
            <i class="fa fa-check"></i> Save
          </button>
          <button type="button" class="btn btn-outline-primary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
        </div>

      </div>
    </form>
  </div>
</div>

<!-- ============================================================
     DETAIL MODAL - Edit Single Item (dari detail grid di bawah)
     ============================================================ -->
<div class="modal" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="#" id="detailForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailModalTitle">Item Detail</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id"           id="detail_id">
          <input type="hidden" name="penjualan_id" id="detail_penjualan_id">

          <div class="row form-group">
            <div class="col-3"><label class="col-form-label">Nama Barang <span class="text-danger">*</span></label></div>
            <div class="col-9"><input type="text" name="nama_barang" id="nama_barang" class="form-control" maxlength="255"></div>
          </div>
          <div class="row form-group">
            <div class="col-3"><label class="col-form-label">Qty <span class="text-danger">*</span></label></div>
            <div class="col-9"><input type="text" name="qty" id="qty" class="form-control text-right" value="1"></div>
          </div>
          <div class="row form-group">
            <div class="col-3"><label class="col-form-label">Harga <span class="text-danger">*</span></label></div>
            <div class="col-9">
              <input type="text" name="harga" id="harga" class="form-control text-right" placeholder="0">
            </div>
          </div>
          <div class="row form-group">
            <div class="col-3"><label class="col-form-label">Subtotal</label></div>
            <div class="col-9"><div class="form-control-plaintext font-weight-bold text-success" id="subtotalPreview">Rp 0</div></div>
          </div>
          <div class="detail-delete-preview" style="display:none">
            <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Yakin ingin menghapus item ini?</div>
          </div>
        </div>
        <div class="modal-footer justify-content-start">
          <button type="submit" id="btnDetailSubmit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<style>
/* tabel item inline */
#tableItemDetail tbody td { vertical-align: middle; padding: 3px 5px; }
.item-row-number { font-weight: 600; color: #6c757d; }
.btn-hapus-baris { line-height:1; padding: 2px 6px; }
</style>

<script>
  let draftManager;
  let modalBody       = $('#crudModal').find('.modal-body').html();
  let detailModalBody = $('#detailModal').find('.modal-body').html();

  draftManager = new DraftFormManager('#crudForm', {
    debug: false,
    expiry: 1000 * 60 * 60 * 2,
    detailExtractors: {
      items: function() { return getItemsFromTable(); }
    },
    detailRestorers: {
      items: function(dataArray) { populateItemTable(dataArray); }
    }
  });


  /* ============================================================
   * TABEL DETAIL INLINE - Builder
   * ============================================================ */
  function buildItemRow(no, data = {}) {
    const disabled = data._readonly ? 'readonly disabled' : '';
    const namaBarang = data.nama_barang || '';
    const qty        = data.qty  || '';
    const harga      = data.harga || '';
    const subtotal   = (parseFloat(qty) || 0) * (parseFloat(harga) || 0);

    const tr = document.createElement('tr');
    tr.dataset.rowIndex = no;
    tr.innerHTML = `
      <td class="text-center item-row-number">${no}</td>
      <td>
        <input type="hidden" class="item-id" value="${data.id || ''}">
        <input type="text"   class="form-control form-control-sm item-nama-barang" placeholder="nama barang" value="${namaBarang}" ${disabled} maxlength="255">
      </td>
      <td><input type="text" class="form-control form-control-sm item-qty text-right" placeholder="0" value="${qty}" ${disabled}></td>
      <td><input type="text" class="form-control form-control-sm item-harga text-right" placeholder="0" value="${harga}" ${disabled}></td>
      <td class="text-right item-subtotal text-success font-weight-bold" style="white-space:nowrap">${subtotal ? currencyFormat(subtotal, 'Rp ') : '-'}</td>
      <td class="text-center">
        ${disabled ? '' : `<button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris" title="Hapus baris">
          <i class="fa fa-times"></i>
        </button>`}
      </td>
    `;

    // Event: qty / harga berubah → update subtotal & grand total
    const qtyInput   = tr.querySelector('.item-qty');
    const hargaInput = tr.querySelector('.item-harga');
    const subCell    = tr.querySelector('.item-subtotal');

    function recalcRow() {
      const q = currencyUnformat(qtyInput.value)   || 0;
      const h = currencyUnformat(hargaInput.value) || 0;
      const s = q * h;
      subCell.textContent = s ? currencyFormat(s, 'Rp ') : '-';
      recalcGrandTotal();
      if(typeof draftManager !== 'undefined') draftManager.triggerSave();
    }
    qtyInput.addEventListener('input',   recalcRow);
    hargaInput.addEventListener('input', recalcRow);
    
    const namaInput = tr.querySelector('.item-nama-barang');
    namaInput.addEventListener('input', function() {
      if(typeof draftManager !== 'undefined') draftManager.triggerSave();
    });

    // Event: hapus baris
    const btnHapus = tr.querySelector('.btn-hapus-baris');
    if (btnHapus) {
      btnHapus.addEventListener('click', function() {
        const tbody = document.getElementById('tbodyItemDetail');
        if (tbody.rows.length <= 1) {
          // kosongkan baris terakhir daripada hapus
          qtyInput.value   = '';
          hargaInput.value = '';
          tr.querySelector('.item-nama-barang').value = '';
          subCell.textContent = '-';
          recalcGrandTotal();
          if(typeof draftManager !== 'undefined') draftManager.triggerSave();
          return;
        }
        tr.remove();
        renumberRows();
        recalcGrandTotal();
        if(typeof draftManager !== 'undefined') draftManager.triggerSave();
      });
    }

    // init autonumeric
    if (!disabled) {
      new AutoNumeric(qtyInput, {
        digitGroupSeparator: ',',
        decimalCharacter: '.',
        decimalPlaces: 0
      });
      initAutoNumeric(hargaInput);
    }

    return tr;
  }

  function renumberRows() {
    const rows = document.querySelectorAll('#tbodyItemDetail tr');
    rows.forEach((row, i) => {
      const numCell = row.querySelector('.item-row-number');
      if (numCell) numCell.textContent = i + 1;
      row.dataset.rowIndex = i + 1;
    });
    document.getElementById('infoItemCount').textContent =
      rows.length ? `${rows.length} baris item` : '';
  }

  function recalcGrandTotal() {
    let total = 0;
    document.querySelectorAll('#tbodyItemDetail tr').forEach(row => {
      const q = currencyUnformat(row.querySelector('.item-qty')?.value)   || 0;
      const h = currencyUnformat(row.querySelector('.item-harga')?.value) || 0;
      total += q * h;
    });
    document.getElementById('grandTotalPreview').textContent = currencyFormat(total, 'Rp ');
  }

  function getItemsFromTable() {
    const items = [];
    document.querySelectorAll('#tbodyItemDetail tr').forEach(row => {
      const id    = row.querySelector('.item-id')?.value || '';
      const nama  = (row.querySelector('.item-nama-barang')?.value || '').trim();
      const qty   = currencyUnformat(row.querySelector('.item-qty')?.value)   || 0;
      const harga = currencyUnformat(row.querySelector('.item-harga')?.value) || 0;
      if (nama || qty || harga) items.push({ id, nama_barang: nama, qty, harga });
    });
    return items;
  }

  function clearItemTable() {
    const tbody = document.getElementById('tbodyItemDetail');
    tbody.innerHTML = '';
    tbody.appendChild(buildItemRow(1));
    recalcGrandTotal();
    renumberRows();
  }

  function populateItemTable(items = [], readonly = false) {
    const tbody = document.getElementById('tbodyItemDetail');
    tbody.innerHTML = '';
    if (!items || items.length === 0) {
      tbody.appendChild(buildItemRow(1));
    } else {
      items.forEach((item, i) => {
        tbody.appendChild(buildItemRow(i + 1, { ...item, _readonly: readonly }));
      });
    }
    recalcGrandTotal();
    renumberRows();
  }

  function initLookup() {
    $('#nama_pelanggan').lookupV6({
      title: 'Pilih Pelanggan',
      lookupKey: 'pelanggan',
      lookupName: 'nama_pelanggan',
      searching: ['nama_pelanggan'],
      filterToolbar: true,
      onSelectRow: (rowData, element) => {
        $('#pelanggan_id').val(rowData.id).trigger('change');
        element.val(rowData.nama_pelanggan).trigger('change');
      },
      onClear: (element) => {
        $('#pelanggan_id').val('').trigger('change');
        element.val('').trigger('change');
      }
    });
  }

  /* ============================================================
   * EVENT: Tombol Tambah Baris
   * ============================================================ */
  $(document).ready(function() {

    // Init AutoNumeric pada modal detail
    new AutoNumeric(document.getElementById('qty'), {
      digitGroupSeparator: ',',
      decimalCharacter: '.',
      decimalPlaces: 0
    });
    initAutoNumeric(document.getElementById('harga'));

    $(document).on('click', '#btnTambahItem', function() {
      const tbody = document.getElementById('tbodyItemDetail');
      const no    = tbody.rows.length + 1;
      tbody.appendChild(buildItemRow(no));
      renumberRows();
      if(typeof draftManager !== 'undefined') draftManager.triggerSave();
      // Fokus ke nama barang baris baru
      $(tbody.lastElementChild).find('.item-nama-barang').focus();
    });

    // =====================================================
    // MASTER FORM SUBMIT
    // =====================================================
    $('#btnSubmit').click(async function(e) {
      e.preventDefault();

      const form   = $('#crudForm');
      const action = form.data('action');
      const masterId = form.find('[name=id]').val();

      const data = {
        no_bukti:     form.find('[name=no_bukti]').val(),
        tgl_bukti:    unFormatDate(form.find('[name=tgl_bukti]').val()),
        pelanggan_id: form.find('[name=pelanggan_id]').val(),
        page:         parseInt($('#jqGrid').getGridParam('page')),
        limit:        parseInt($('#jqGrid').getGridParam('rowNum')),
        sortIndex:    $('#jqGrid').getGridParam('sortname'),
        sortOrder:    $('#jqGrid').getGridParam('sortorder'),
        filters:      $('#jqGrid').getGridParam('postData').filters,
        indexRow:     indexRow,
      };

      // Ambil items dari tabel inline (hanya saat add/edit, bukan delete)
      if (action !== 'delete') {
        data.items = getItemsFromTable();
      }

      let method, url;
      switch (action) {
        case 'add':    method = 'POST';   url = `${API_URL}/testingmasterdetail`;           break;
        case 'edit':   method = 'PATCH';  url = `${API_URL}/testingmasterdetail/${masterId}`; break;
        case 'delete': method = 'DELETE'; url = `${API_URL}/testingmasterdetail/${masterId}`; break;
        default:       method = 'POST';   url = `${API_URL}/testingmasterdetail`;
      }

      $(this).attr('disabled', '');
      $('#processingLoader').removeClass('d-none');

      try {
        const response = await ajaxWithRefresh({
          url, method, dataType: 'JSON', contentType: 'application/json',
          data: JSON.stringify(data)
        });

        if (typeof draftManager !== 'undefined' && action === 'add') draftManager.clear();

        form.trigger('reset');
        $('#crudModal').modal('hide');
        selectedRows = [];
        id = response.data.id;

        const payload = response.data;
        const grd     = $('#jqGrid');
        const loader  = grd.data('lazyLoader');

        if (loader) {
          const postDataParam = grd.jqGrid('getGridParam', 'postData');
          loader.resetGridState(false);

          if (action === 'delete') {
            const targetId    = payload.id || '';
            const targetIndex = payload.offset % loader.rowsPerPage;
            loader.loadGridData(postDataParam, payload.page, loader.rowsPerPage, 'down', 'jump', function() {
              setTimeout(() => {
                const ids     = grd.getDataIDs();
                const finalId = targetId || ids[Math.min(targetIndex, ids.length - 1)];
                if (!finalId) return;
                grd.jqGrid('setSelection', finalId, true);
                scrollToRow(grd, finalId);
              }, 100);
            });
          } else {
            loader.loadGridData(postDataParam, payload.page, loader.rowsPerPage, 'down', 'jump', function() {
              setTimeout(() => {
                grd.jqGrid('setSelection', payload.id, true);
                scrollToRow(grd, payload.id);
                // Reload detail grid jika master yang baru ini dipilih
                if (selectedMasterId === masterId || action === 'add') {
                  loadDetailGrid(payload.id);
                }
              }, 100);
            });
          }
        } else {
          grd.jqGrid('setGridParam', { page: response.data.page }).trigger('reloadGrid');
        }

      } catch (error) {
        if (error.status !== 422) showDialog('error', getErrorMessage(error));
      } finally {
        $('#processingLoader').addClass('d-none');
        $(this).removeAttr('disabled');
      }
    });

    // =====================================================
    // DETAIL MODAL FORM SUBMIT (edit single item dari detail grid)
    // =====================================================
    $('#btnDetailSubmit').click(async function(e) {
      e.preventDefault();

      const form     = $('#detailForm');
      const action   = form.data('action');
      const detailId = form.find('[name=id]').val();

      const data = {
        penjualan_id: selectedMasterId,
        nama_barang:  form.find('[name=nama_barang]').val(),
        qty:          currencyUnformat(form.find('[name=qty]').val()),
        harga:        currencyUnformat(form.find('[name=harga]').val()),
        page:         parseInt($(detailGrid).getGridParam('page')),
        limit:        parseInt($(detailGrid).getGridParam('rowNum')),
        sortIndex:    $(detailGrid).getGridParam('sortname'),
        sortOrder:    $(detailGrid).getGridParam('sortorder'),
      };

      let method, url;
      switch (action) {
        case 'add':    method = 'POST';   url = `${API_URL}/testingmasterdetail/detail`;           break;
        case 'edit':   method = 'PATCH';  url = `${API_URL}/testingmasterdetail/detail/${detailId}`; break;
        case 'delete': method = 'DELETE'; url = `${API_URL}/testingmasterdetail/detail/${detailId}`; break;
        default:       method = 'POST';   url = `${API_URL}/testingmasterdetail/detail`;
      }

      $(this).attr('disabled', '');
      $('#processingLoader').removeClass('d-none');

      try {
        const response = await ajaxWithRefresh({
          url, method, dataType: 'JSON', contentType: 'application/json',
          data: JSON.stringify(data)
        });

        form.trigger('reset');
        $('#detailModal').modal('hide');

        const payload = response.data;
        const dgrd    = $(detailGrid);
        const dloader = dgrd.data('lazyLoader');

        if (dloader) {
          const postDataParam = dgrd.jqGrid('getGridParam', 'postData');
          dloader.resetGridState(false);
          if (action === 'delete') {
            const targetIndex = payload.offset % dloader.rowsPerPage;
            dloader.loadGridData(postDataParam, payload.page, dloader.rowsPerPage, 'down', 'jump', function() {
              setTimeout(() => {
                const ids = dgrd.getDataIDs();
                const finalId = payload.id || ids[Math.min(targetIndex, ids.length - 1)];
                if (finalId) dgrd.jqGrid('setSelection', finalId, true);
              }, 100);
            });
          } else {
            dloader.loadGridData(postDataParam, payload.page, dloader.rowsPerPage, 'down', 'jump', function() {
              setTimeout(() => { dgrd.jqGrid('setSelection', payload.id, true); }, 100);
            });
          }
        } else {
          dgrd.jqGrid('setGridParam', { page: response.data.page }).trigger('reloadGrid');
        }

      } catch (error) {
        if (error.status !== 422) showDialog('error', getErrorMessage(error));
      } finally {
        $('#processingLoader').addClass('d-none');
        $(this).removeAttr('disabled');
      }
    });

    // Live subtotal preview di detail modal
    $('#qty, #harga').on('input', function() {
      const q = parseFloat($('#qty').val())   || 0;
      const h = parseFloat($('#harga').val()) || 0;
      $('#subtotalPreview').text(currencyFormat(q * h, 'Rp '));
    });

    document.getElementById('crudModal').addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && document.activeElement && document.activeElement.tagName === 'INPUT') {
        e.stopPropagation();
        document.activeElement.blur();
        $('#crudModal').modal('hide');
      }
    }, true);

    // Save draft before modal closes
    $('#crudModal').on('hide.bs.modal', function() {
      if (document.activeElement) {
        $(document.activeElement).blur();
      }
      if (typeof draftManager !== 'undefined') draftManager.triggerSave();
    });

    // Reset item table saat modal ditutup
    $('#crudModal').on('hidden.bs.modal', function() {
      clearItemTable();
      $('#grandTotalPreview').text('Rp 0');
    });

  }); // END document.ready

  /* ============================================================
   * MASTER CRUD FUNCTIONS
   * ============================================================ */
  async function createtestingmasterdetail() {
    const form = $('#crudForm');

    draftManager.pause();
    form.trigger('reset');
    form.find('[name=id]').val('');
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i> Save`).removeClass('btn-danger').addClass('btn-primary');
    form.data('action', 'add');
    $('#crudModalTitle').text('Tambah Penjualan');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('.delete-preview').hide();

    clearItemTable();

    // Set no_bukti field readonly & tampilkan loading
    const noBuktiInput = form.find('[name=no_bukti]');
    noBuktiInput.prop('readonly', true).val('Memuat...');

    $('.modal-loader').removeClass('d-none');

    // Fetch next number
    try {
      const nextNoResp = await ajaxWithRefresh({ url: `${API_URL}/testingmasterdetail/nextnumber`, method: 'GET', dataType: 'JSON' });
      noBuktiInput.val(nextNoResp.next_number || '');
    } catch (e) {
      noBuktiInput.val('');
    }

    const today = new Date().toISOString().split('T')[0];
    form.find('[name=tgl_bukti]').val(formatDate(today));

    if (localStorage.getItem(draftManager.getKey())) draftManager.restore();

    $('#crudModal').modal('show');
    $('.modal-loader').addClass('d-none');
  }

  async function updatetestingmasterdetail(rowid) {
    if (!rowid) { showDialog('warning', 'Pilih data yang akan diedit!'); return; }

    const masterId = $(masterGrid).jqGrid('getCell', rowid, 'id');
    if (!masterId) { showDialog('warning', 'Gagal mendapatkan ID data!'); return; }

    const form = $('#crudForm');
    form.data('action', 'edit');
    form.trigger('reset');
    form.find('#btnSubmit').html(`<i class="fa fa-save"></i> Save`).removeClass('btn-danger').addClass('btn-primary');
    $('#crudModalTitle').text('Edit Penjualan');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('.delete-preview').hide();
    clearItemTable();

    $('.modal-loader').removeClass('d-none');

    try {
      const response = await ajaxWithRefresh({ url: `${API_URL}/testingmasterdetail/${masterId}`, method: 'GET', dataType: 'JSON' });

      if (response.tgl_bukti) {
        response.tgl_bukti = formatDate(response.tgl_bukti);
      }

      populateForm(form, response);

      // no_bukti readonly pada mode edit
      form.find('[name=no_bukti]').prop('readonly', true);

      // Load detail items yang sudah ada
      const detailResp = await ajaxWithRefresh({
        url: `${API_URL}/testingmasterdetail/${masterId}/detail?page=1&rows=500&sidx=created_at&sord=asc`,
        method: 'GET',
        dataType: 'JSON'
      });
      const existingItems = (detailResp.data || []).map(d => ({
        id:          d.id,
        nama_barang: d.nama_barang,
        qty:         d.qty,
        harga:       d.harga,
      }));
      populateItemTable(existingItems);

      $('#crudModal').modal('show');

    } catch (error) {
      showDialog('error', getErrorMessage(error));
    } finally {
      $('.modal-loader').addClass('d-none');
    }
  }

  async function deletetestingmasterdetail(rowid) {
    if (!rowid) { showDialog('warning', 'Pilih data yang akan dihapus!'); return; }

    const masterId = $(masterGrid).jqGrid('getCell', rowid, 'id');
    if (!masterId) { showDialog('warning', 'Gagal mendapatkan ID data!'); return; }

    const form = $('#crudForm');
    form.data('action', 'delete');
    form.trigger('reset');
    form.find('#btnSubmit').html(`<i class="fa fa-trash"></i> Delete`).removeClass('btn-primary').addClass('btn-danger');
    $('#crudModalTitle').text('Hapus Penjualan');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    $('.modal-loader').removeClass('d-none');

    try {
      const response = await ajaxWithRefresh({ url: `${API_URL}/testingmasterdetail/${masterId}`, method: 'GET', dataType: 'JSON' });

      if (response.tgl_bukti) {
        response.tgl_bukti = formatDate(response.tgl_bukti);
      }

      populateForm(form, response);
      $('#deletePreviewNoBukti').text(response.no_bukti ?? '');
      $('.delete-preview').show();

      // Load detail items (readonly) untuk preview saat delete
      const detailResp = await ajaxWithRefresh({
        url: `${API_URL}/testingmasterdetail/${masterId}/detail?page=1&rows=500&sidx=created_at&sord=asc`,
        method: 'GET',
        dataType: 'JSON'
      });
      const existingItems = (detailResp.data || []).map(d => ({
        nama_barang: d.nama_barang,
        qty:         d.qty,
        harga:       d.harga,
        _readonly:   true,
      }));
      populateItemTable(existingItems, true);

      $('#crudModal').modal('show');

    } catch (error) {
      showDialog('error', getErrorMessage(error));
    } finally {
      $('.modal-loader').addClass('d-none');
    }
  }

  /* ============================================================
   * DETAIL CRUD FUNCTIONS (dari detail grid di bawah, bukan inline)
   * ============================================================ */
  function createDetailItem() {
    const form = $('#detailForm');
    form.data('action', 'add');
    form.trigger('reset');
    form.find('#btnDetailSubmit').html(`<i class="fa fa-save"></i> Save`).removeClass('btn-danger').addClass('btn-primary');
    $('#detailModalTitle').text('Tambah Item Detail');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('.detail-delete-preview').hide();
    form.find('#detail_penjualan_id').val(selectedMasterId);
    $('#subtotalPreview').text('Rp 0');
    $('#detailModal').modal('show');
  }

  async function updateDetailItem(rowid) {
    if (!rowid) { showDialog('warning', 'Pilih item yang akan diedit!'); return; }
    const detailId = $(detailGrid).jqGrid('getCell', rowid, 'id');
    if (!detailId) { showDialog('warning', 'Gagal mendapatkan ID item!'); return; }

    const form = $('#detailForm');
    form.data('action', 'edit');
    form.trigger('reset');
    form.find('#btnDetailSubmit').html(`<i class="fa fa-save"></i> Save`).removeClass('btn-danger').addClass('btn-primary');
    $('#detailModalTitle').text('Edit Item Detail');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('.detail-delete-preview').hide();

    try {
      const response = await ajaxWithRefresh({
        url: `${API_URL}/testingmasterdetail/detail/${detailId}`, method: 'GET', dataType: 'JSON'
      });
      form.find('#detail_id').val(response.id);
      form.find('#detail_penjualan_id').val(response.penjualan_id);
      form.find('#nama_barang').val(response.nama_barang);
      AutoNumeric.getAutoNumericElement(form.find('#qty')[0]).set(response.qty);
      AutoNumeric.getAutoNumericElement(form.find('#harga')[0]).set(response.harga);
      const sub = (response.qty || 0) * (response.harga || 0);
      $('#subtotalPreview').text(currencyFormat(sub, 'Rp '));
      $('#detailModal').modal('show');
    } catch (error) { showDialog('error', getErrorMessage(error)); }
  }

  async function deleteDetailItem(rowid) {
    if (!rowid) { showDialog('warning', 'Pilih item yang akan dihapus!'); return; }
    const detailId = $(detailGrid).jqGrid('getCell', rowid, 'id');
    if (!detailId) { showDialog('warning', 'Gagal mendapatkan ID item!'); return; }

    const form = $('#detailForm');
    form.data('action', 'delete');
    form.trigger('reset');
    form.find('#btnDetailSubmit').html(`<i class="fa fa-trash"></i> Delete`).removeClass('btn-primary').addClass('btn-danger');
    $('#detailModalTitle').text('Hapus Item Detail');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    try {
      const response = await ajaxWithRefresh({
        url: `${API_URL}/testingmasterdetail/detail/${detailId}`, method: 'GET', dataType: 'JSON'
      });
      form.find('#detail_id').val(response.id);
      form.find('#detail_penjualan_id').val(response.penjualan_id);
      form.find('#nama_barang').val(response.nama_barang);
      AutoNumeric.getAutoNumericElement(form.find('#qty')[0]).set(response.qty);
      AutoNumeric.getAutoNumericElement(form.find('#harga')[0]).set(response.harga);
      const sub = (response.qty || 0) * (response.harga || 0);
      $('#subtotalPreview').text(formatRupiah(sub));
      $('.detail-delete-preview').show();
      $('#detailModal').modal('show');
    } catch (error) { showDialog('error', getErrorMessage(error)); }
  }


</script>