/**
 * ColumnSettingsManager v4 — UI-only, delegasi storage ke GridPreferenceManager
 * ─────────────────────────────────────────────────────────────────────────────
 * Tanggung jawab file ini HANYA:
 *   - Render tombol & panel dropdown di toolbar
 *   - Menangani interaksi user (toggle, drag, filter)
 *   - Meminta GridPreferenceManager untuk save/load
 *   - Meminta jqGrid untuk apply perubahan
 *
 * Yang TIDAK dilakukan file ini:
 *   - Menyentuh localStorage secara langsung
 *   - Menyentuh server secara langsung
 *   - Menyimpan state kolom di key-nya sendiri
 *
 * Syarat: GridPreferenceManager harus sudah di-load sebelum file ini.
 */

const ColumnSettingsManager = (function () {

  // ─── State internal UI ────────────────────────────────────────
  let _gridSelector = '';
  let _prefKey = '';
  let _baseColModel = [];
  let _activeGridId = null;

  // Kolom sistem jqGrid — tidak boleh di-hide/reorder oleh user
  const LOCKED_COLS = ['id', 'rn', 'cb', 'subgrid'];

  // ─── Delegasi ke GridPreferenceManager ───────────────────────

  /**
   * Simpan state lengkap ke GridPreferenceManager.
   * GridPreferenceManager yang akan urus localStorage + server.
   * State yang disimpan adalah gabungan: hidden (dari panel UI) + width & order
   * (dari jqGrid saat ini) — satu sumber kebenaran.
   */
  function _saveViaGPM(colStates) {
    if (typeof GridPreferenceManager === 'undefined') {
      console.warn('[ColVis] GridPreferenceManager tidak ditemukan, skip save.');
      return;
    }

    // Ambil lebar & urutan kolom dari grid saat ini
    const cm = $(_gridSelector).jqGrid('getGridParam', 'colModel');

    // Buat map name→hidden dari panel UI (yang baru diubah user)
    const hiddenMap = {};
    colStates.forEach(s => { hiddenMap[s.name] = s.hidden; });

    // Gabungkan: urutan dari panel (colStates), lebar dari grid
    const merged = colStates.map((s, idx) => {
      const gridCol = cm.find(c => c.name === s.name);
      return {
        name: s.name,
        hidden: s.hidden,
        width: gridCol ? gridCol.width : 100,
        order: idx,
      };
    });

    // Simpan via GPM — dia yang urus cache + server
    GridPreferenceManager.save(_prefKey, merged);
  }

  /**
   * Muat state dari GridPreferenceManager (sinkron dari cache localStorage).
   * Kalau belum ada, ambil dari state grid saat ini.
   */
  function _loadViaGPM() {
    if (typeof GridPreferenceManager === 'undefined') {
      return _getCurrentColStateFromGrid();
    }

    // GPM.load() adalah async (server), tapi cache lokal bisa dibaca sinkron
    // lewat cara ini: baca langsung dari localStorage dengan key GPM
    try {
      const key = (GridPreferenceManager._localKey || (() => `grid_pref_${_prefKey}`))(_prefKey);
      const raw = localStorage.getItem(key);
      if (raw) {
        const parsed = JSON.parse(raw);
        // Filter hanya field yang dibutuhkan panel
        return parsed
          .filter(c => !LOCKED_COLS.includes(c.name))
          .map(c => ({ name: c.name, hidden: c.hidden || false }));
      }
    } catch { /* fallthrough */ }

    return _getCurrentColStateFromGrid();
  }

  /** Ambil state kolom dari jqGrid sebagai fallback */
  function _getCurrentColStateFromGrid() {
    const cm = $(_gridSelector).jqGrid('getGridParam', 'colModel');
    return cm
      .filter(c => !LOCKED_COLS.includes(c.name))
      .map(c => ({ name: c.name, hidden: c.hidden || false }));
  }

  // ─── Apply ke jqGrid ─────────────────────────────────────────

  function _applyVisibility(colStates) {
    colStates.forEach(({ name, hidden }) => {
      if (LOCKED_COLS.includes(name)) return;
      hidden
        ? $(_gridSelector).jqGrid('hideCol', name)
        : $(_gridSelector).jqGrid('showCol', name);
    });
  }

  /**
   * Terapkan urutan kolom ke jqGrid menggunakan remapColumns.
   * colStates sudah dalam urutan yang diinginkan (hasil drag dari panel).
   */
  function _applyOrder(colStates) {
    const grid = $(_gridSelector);
    const cm = grid.jqGrid('getGridParam', 'colModel');
    const stateNames = colStates.map(s => s.name);

    let perm = [];
    let stateIdx = 0;

    for (let i = 0; i < cm.length; i++) {
      const colName = cm[i].name;
      if (!stateNames.includes(colName)) {
        // Kolom sistem (id, rn, dll) → tetap di posisinya
        perm.push(i);
      } else {
        const targetName = stateNames[stateIdx];
        const currentIdx = cm.findIndex(c => c.name === targetName);
        perm.push(currentIdx);
        stateIdx++;
      }
    }

    grid.jqGrid('remapColumns', perm, true);
  }

  // ─── Badge ───────────────────────────────────────────────────

  function renderBadge(gridSelector) {
    const cm = $(gridSelector).jqGrid('getGridParam', 'colModel');
    const hidden = cm.filter(c => !LOCKED_COLS.includes(c.name) && c.hidden).length;
    const gridId = $(gridSelector).getGridParam('id');
    const badge = document.getElementById(`colvis_badge_${gridId}`);
    if (!badge) return;
    badge.textContent = hidden;
    badge.style.display = hidden > 0 ? 'inline-flex' : 'none';
  }

  function _getLabel(colName) {
    const found = _baseColModel.find(c => c.name === colName);
    return found ? (found.label || colName) : colName;
  }

  // ─── HTML ─────────────────────────────────────────────────────

  function _buildItemHTML(col) {
    const isHidden = col.hidden;
    return `
      <div class="colvis-item ${isHidden ? 'colvis-item--hidden' : ''}"
           data-col="${col.name}" data-visible="${!isHidden}">
        <span class="colvis-drag" title="Geser untuk reorder">⠿</span>
        <label class="colvis-toggle" onclick="event.stopPropagation()">
          <input type="checkbox" ${!isHidden ? 'checked' : ''}
                 onchange="ColumnSettingsManager._onToggle(this,'${col.name}')">
          <span class="colvis-track"></span>
          <span class="colvis-thumb"></span>
        </label>
        <span class="colvis-label">${_getLabel(col.name)}</span>
      </div>`;
  }

  function _buildPanel(gridId, colStates) {
    document.getElementById(`colvis_panel_${gridId}`)?.remove();

    const valid = colStates.filter(c => !LOCKED_COLS.includes(c.name));
    const el = document.createElement('div');
    el.className = 'colvis-panel';
    el.id = `colvis_panel_${gridId}`;
    el.innerHTML = `
      <div class="colvis-header">
        <span class="colvis-title">Atur Kolom</span>
        <div class="colvis-header-actions">
          <button class="colvis-link" onclick="ColumnSettingsManager._showAll()">Tampilkan semua</button>
          <span style="color:#ccc;font-size:10px">|</span>
          <button class="colvis-link colvis-link--muted"
                  onclick="ColumnSettingsManager._hideAll()">Sembunyikan semua</button>
        </div>
      </div>
      <div class="colvis-search-wrap">
        <input class="colvis-search" type="text" placeholder="Cari kolom..."
               id="colvis_search_${gridId}"
               oninput="ColumnSettingsManager._filterItems()">
      </div>
      <div class="colvis-list" id="colvis_list_${gridId}">
        ${valid.map(c => _buildItemHTML(c)).join('')}
        <div class="colvis-item" style="opacity:.35;pointer-events:none">
          <span class="colvis-drag" style="opacity:0">⠿</span>
          <label class="colvis-toggle">
            <input type="checkbox" checked disabled>
            <span class="colvis-track"></span>
            <span class="colvis-thumb"></span>
          </label>
          <span class="colvis-label">ID</span>
          <span class="colvis-locked-badge">system</span>
        </div>
      </div>
      <div class="colvis-footer">
        <div style="display:flex;align-items:center;gap:8px">
          <button class="colvis-btn-reset"
                  onclick="ColumnSettingsManager._reset()">Reset</button>
          <span class="colvis-status" id="colvis_status_${gridId}"></span>
        </div>
        <button class="colvis-btn-apply"
                onclick="ColumnSettingsManager._apply()">Terapkan</button>
      </div>`;

    // ← Kunci: append ke body, bukan ke toolbar
    document.body.appendChild(el);

    // Aktifkan drag-and-drop (jQuery UI Sortable)
    if (typeof jQuery !== 'undefined' && jQuery.ui?.sortable) {
      $(`#colvis_list_${gridId}`).sortable({
        items: '.colvis-item:not([style*="pointer-events"])',
        handle: '.colvis-drag',
        axis: 'y',
        containment: 'parent',
        tolerance: 'pointer',
      });
    }

    return el;
  }

  // ─── Posisi panel di bawah tombol ────────────────────────────

  function _positionPanel(gridId) {
    const trigger = document.getElementById(`colvis_trigger_${gridId}`);
    const panel = document.getElementById(`colvis_panel_${gridId}`);
    if (!trigger || !panel) return;
 
    const rect = trigger.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
    const panelW = 260;
    const margin = 8;
 
    let left = rect.right + scrollLeft - panelW;
    if (left < margin) left = margin;
 
    const spaceBelow = window.innerHeight - rect.bottom;
    const panelH = panel.offsetHeight || 320;
    let top;
    if (spaceBelow < panelH + 8 && rect.top > panelH + 8) {
      top = rect.top + scrollTop - panelH - 6;
    } else {
      top = rect.bottom + scrollTop + 6;
    }
 
    panel.style.left = `${left}px`;
    panel.style.top = `${top}px`;
  }

  // ─── CSS ──────────────────────────────────────────────────────

  function _injectStyles() {
    if (document.getElementById('colvis-styles')) return;
    const css = `
      .colvis-btn {
        display:inline-flex;align-items:center;gap:5px;
        height:30px;padding:0 10px;font-size:12px;
        color:#555;background:#fff;
        border:1px solid #ced4da;border-radius:4px;
        cursor:pointer;white-space:nowrap;
        transition:background .15s,color .15s;font-family:inherit;
      }
      .colvis-btn:hover{background:#f8f9fa}
      .colvis-btn.colvis-btn--active{border-color:#007bff;color:#007bff;background:#e8f0ff}

      .colvis-badge{
        display:inline-flex;align-items:center;justify-content:center;
        background:#007bff;color:#fff;
        border-radius:99px;font-size:10px;font-weight:600;
        min-width:16px;height:16px;padding:0 4px;
      }

      .colvis-panel{
        display:none;position:absolute;z-index:99999;width:260px;
        background:#fff;border:1px solid #ddd;border-radius:8px;
        box-shadow:0 8px 24px rgba(0,0,0,.12);
        font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
        font-size:12px;
      }
      .colvis-panel.colvis-panel--open{display:block}

      .colvis-header{
        display:flex;align-items:center;justify-content:space-between;
        padding:10px 13px 8px;border-bottom:1px solid #f0f0f0;
      }
      .colvis-title{font-size:12px;font-weight:600;color:#333}
      .colvis-header-actions{display:flex;gap:5px;align-items:center}
      .colvis-link{font-size:11px;color:#007bff;background:none;border:none;cursor:pointer;padding:0;font-family:inherit}
      .colvis-link--muted{color:#888}
      .colvis-link:hover{text-decoration:underline}

      .colvis-search-wrap{padding:7px 10px;border-bottom:1px solid #f0f0f0}
      .colvis-search{
        width:100%;height:28px;padding:0 8px;font-size:11px;
        border:1px solid #ddd;border-radius:5px;
        background:#fafafa;color:#333;outline:none;font-family:inherit;
      }
      .colvis-search:focus{border-color:#007bff;background:#fff}

      .colvis-list{max-height:230px;overflow-y:auto;padding:4px 0}
      .colvis-list::-webkit-scrollbar{width:4px}
      .colvis-list::-webkit-scrollbar-thumb{background:#ddd;border-radius:2px}

      .colvis-item{
        display:flex;align-items:center;gap:9px;
        padding:6px 13px;user-select:none;transition:background .1s;
      }
      .colvis-item:hover{background:#f8f9fa}
      .colvis-item--hidden{opacity:.45}

      .colvis-drag{color:#adb5bd;font-size:13px;cursor:grab;flex-shrink:0}
      .colvis-drag:active{cursor:grabbing;color:#007bff}

      .colvis-toggle{position:relative;width:30px;height:17px;flex-shrink:0;cursor:pointer;margin:0}
      .colvis-toggle input{display:none}
      .colvis-track{
        display:block;width:30px;height:17px;
        background:#dee2e6;border-radius:99px;transition:background .2s;cursor:pointer;
      }
      .colvis-toggle input:checked+.colvis-track{background:#007bff}
      .colvis-thumb{
        position:absolute;top:2px;left:2px;width:13px;height:13px;
        background:#fff;border-radius:50%;
        transition:left .2s;pointer-events:none;
        box-shadow:0 1px 3px rgba(0,0,0,.2);
      }
      .colvis-toggle input:checked~.colvis-thumb{left:15px}

      .colvis-label{font-size:12px;color:#333;flex:1;font-weight:500}
      .colvis-locked-badge{
        font-size:9px;color:#6c757d;background:#e9ecef;
        border:1px solid #ced4da;border-radius:3px;padding:1px 4px;
      }

      .colvis-footer{
        display:flex;align-items:center;justify-content:space-between;
        padding:8px 13px;border-top:1px solid #f0f0f0;
        background:#fafafa;border-radius:0 0 8px 8px;
      }
      .colvis-status{font-size:11px;color:#6c757d}
      .colvis-btn-apply{
        height:27px;padding:0 12px;font-size:12px;font-weight:600;
        background:#007bff;color:#fff;border:none;border-radius:5px;
        cursor:pointer;font-family:inherit;
      }
      .colvis-btn-apply:hover{background:#0069d9}
      .colvis-btn-reset{
        height:27px;padding:0 9px;font-size:12px;
        background:none;color:#6c757d;
        border:1px solid #ced4da;border-radius:5px;cursor:pointer;font-family:inherit;
      }
      .colvis-btn-reset:hover{background:#e2e6ea}
    `;
    const style = document.createElement('style');
    style.id = 'colvis-styles';
    style.textContent = css;
    document.head.appendChild(style);
  }

  // ─── Inject tombol ke toolbar ─────────────────────────────────

  function _injectButton(gridSelector) {
    const gridId = $(gridSelector).getGridParam('id');
    const toolbar = document.getElementById(`t_${gridId}`);
    if (!toolbar) {
      console.warn('[ColVis] Toolbar #t_' + gridId + ' tidak ditemukan.');
      return;
    }

    // Cari container kanan toolbar
    const flexEl = toolbar.querySelector('.d-flex.align-items-center');
    if (flexEl) flexEl.classList.remove('d-flex');
    const rightContainer = toolbar.querySelector('.align-items-center');
    if (!rightContainer) {
      console.warn('[ColVis] Tidak menemukan container kanan di toolbar.');
      return;
    }

    const wrap = document.createElement('div');
    wrap.style.cssText = 'display:flex;align-items:center;margin-right:2px';
    wrap.innerHTML = `
      <button class="colvis-btn" id="colvis_trigger_${gridId}"
              onclick="ColumnSettingsManager._togglePanel('${gridId}')">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round" style="flex-shrink:0">
          <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/>
          <line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/>
          <line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
          <line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/>
          <line x1="17" y1="16" x2="23" y2="16"/>
        </svg>
        <span class="colvis-badge" id="colvis_badge_${gridId}" style="display:none">0</span>
        <span style="font-size:9px;margin-left:1px">▾</span>
      </button>`;

    // Tempatkan di sebelah kiri input search
    const searchForm = rightContainer.closest('.d-flex')?.querySelector('form.form-inline .input-group');
    if (searchForm) {
      const prepend = document.createElement('div');
      prepend.className = 'input-group-prepend';
      prepend.style.marginRight = '0';
      prepend.appendChild(wrap);
      searchForm.prepend(prepend);
    } else {
      const searchDetail = rightContainer.querySelector(`#searchDetail_${gridId}`);
      searchDetail
        ? rightContainer.insertBefore(wrap, searchDetail)
        : rightContainer.prepend(wrap);
    }

    // Klik di luar → tutup (capture phase agar tidak bentrok stopPropagation jqGrid)
    document.addEventListener('click', function (e) {
      if (!_activeGridId) return;
      const trigger = document.getElementById(`colvis_trigger_${_activeGridId}`);
      const panel = document.getElementById(`colvis_panel_${_activeGridId}`);
      if (!trigger?.contains(e.target) && !panel?.contains(e.target)) {
        _closePanel(_activeGridId);
      }
    }, true);

    window.addEventListener('scroll', () => { if (_activeGridId) _positionPanel(_activeGridId); }, true);
    window.addEventListener('resize', () => { if (_activeGridId) _positionPanel(_activeGridId); });
  }

  // ─── Sync urutan panel mengikuti colModel grid saat ini ──────
  function _syncPanelToGrid(gridId) {
    const list = document.getElementById(`colvis_list_${gridId}`);
    if (!list) return;

    // Ambil urutan kolom aktual dari jqGrid sekarang
    const cm = $(_gridSelector).jqGrid('getGridParam', 'colModel');
    const gridOrder = cm
      .filter(c => !LOCKED_COLS.includes(c.name))
      .map(c => c.name);

    // Ambil semua item yang ada di panel (kecuali item sistem/locked)
    const locked = list.querySelector('[style*="pointer-events"]');
    const itemMap = {};
    list.querySelectorAll('.colvis-item:not([style*="pointer-events"])').forEach(el => {
      itemMap[el.dataset.col] = el;
    });

    // Lepas semua item dari DOM (tapi jangan hapus — visibility state-nya dipertahankan)
    Object.values(itemMap).forEach(el => el.remove());

    // Pasang ulang sesuai urutan dari grid, pertahankan visibility state yg ada
    gridOrder.forEach(colName => {
      const el = itemMap[colName];
      if (el) list.insertBefore(el, locked);
    });

    // Kolom yang ada di panel tapi tidak ada di gridOrder (misal kolom baru) → taruh terakhir
    Object.keys(itemMap).forEach(colName => {
      if (!gridOrder.includes(colName)) {
        const el = itemMap[colName];
        if (el && !el.parentNode) list.insertBefore(el, locked);
      }
    });
  }

  // ─── Toggle panel ─────────────────────────────────────────────

  function _togglePanel(gridId) {
    const panel = document.getElementById(`colvis_panel_${gridId}`);
    const trigger = document.getElementById(`colvis_trigger_${gridId}`);
    if (!panel || !trigger) return;

    const isOpen = panel.classList.contains('colvis-panel--open');
    if (_activeGridId && _activeGridId !== gridId) _closePanel(_activeGridId);

    if (isOpen) {
      _closePanel(gridId);
    } else {
      panel.classList.add('colvis-panel--open');
      trigger.classList.add('colvis-btn--active');
      _activeGridId = gridId;

      // ← Sync urutan panel ke grid sebelum ditampilkan
      _syncPanelToGrid(gridId);

      _positionPanel(gridId);

      const s = document.getElementById(`colvis_search_${gridId}`);
      if (s) { s.value = ''; _filterItems(); }
    }
  }

  function _closePanel(gridId) {
    document.getElementById(`colvis_panel_${gridId}`)?.classList.remove('colvis-panel--open');
    document.getElementById(`colvis_trigger_${gridId}`)?.classList.remove('colvis-btn--active');
    if (_activeGridId === gridId) _activeGridId = null;
  }

  // ─── Interaksi panel ─────────────────────────────────────────

  function _onToggle(checkbox, colName) {
    const gridId = _activeGridId; if (!gridId) return;
    const item = checkbox.closest('.colvis-item');
    item.dataset.visible = checkbox.checked;
    item.classList.toggle('colvis-item--hidden', !checkbox.checked);
    _updateStatus(gridId, _collectState(gridId));
  }

  function _collectState(gridId) {
    return [...document.querySelectorAll(
      `#colvis_list_${gridId} .colvis-item:not([style*="pointer-events"])`
    )].map(item => ({
      name: item.dataset.col,
      hidden: item.dataset.visible !== 'true',
    }));
  }

  function _updateStatus(gridId, colStates) {
    const h = colStates.filter(c => c.hidden).length;
    const s = document.getElementById(`colvis_status_${gridId}`);
    if (s) s.textContent = h > 0 ? `${h} kolom tersembunyi` : 'Semua kolom tampil';
    const b = document.getElementById(`colvis_badge_${gridId}`);
    if (b) { b.textContent = h; b.style.display = h > 0 ? 'inline-flex' : 'none'; }
  }

  function _showAll() {
    const g = _activeGridId; if (!g) return;
    document.querySelectorAll(`#colvis_list_${g} .colvis-item:not([style*="pointer-events"])`).forEach(item => {
      const cb = item.querySelector('input');
      if (cb) cb.checked = true;
      item.dataset.visible = 'true';
      item.classList.remove('colvis-item--hidden');
    });
    _updateStatus(g, _collectState(g));
  }

  function _hideAll() {
    const g = _activeGridId; if (!g) return;
    document.querySelectorAll(`#colvis_list_${g} .colvis-item:not([style*="pointer-events"])`).forEach(item => {
      const cb = item.querySelector('input');
      if (cb) cb.checked = false;
      item.dataset.visible = 'false';
      item.classList.add('colvis-item--hidden');
    });
    _updateStatus(g, _collectState(g));
  }

  function _filterItems() {
    const g = _activeGridId; if (!g) return;
    const q = (document.getElementById(`colvis_search_${g}`)?.value || '').toLowerCase();
    document.querySelectorAll(`#colvis_list_${g} .colvis-item`).forEach(item => {
      const label = item.querySelector('.colvis-label')?.textContent.toLowerCase() || '';
      item.style.display = label.includes(q) ? '' : 'none';
    });
  }

  // ─── Terapkan ────────────────────────────────────────────────

  function _apply() {
    const g = _activeGridId; if (!g) return;
    const states = _collectState(g);

    // 1. Terapkan urutan (drag-and-drop) ke jqGrid
    _applyOrder(states);

    // 2. Terapkan visibility ke jqGrid
    _applyVisibility(states);

    // 3. Simpan via GridPreferenceManager (dia urus localStorage + server)
    _saveViaGPM(states);

    _updateStatus(g, states);
    _closePanel(g);
  }

  // ─── Reset ke default ─────────────────────────────────────────

  function _reset() {
    const g = _activeGridId; if (!g) return;

    // Ambil definisi default dari baseColModel
    const initialState = _baseColModel
      .filter(c => !LOCKED_COLS.includes(c.name))
      .map(c => ({ name: c.name, hidden: c.hidden || false }));

    // Bangun ulang panel dengan urutan & visibility default
    _buildPanel(g, initialState);

    // Terapkan ke grid
    _applyOrder(initialState);
    _applyVisibility(initialState);

    // Hapus dari GridPreferenceManager — dia yang urus clear localStorage + server
    if (typeof GridPreferenceManager !== 'undefined') {
      GridPreferenceManager.reset(_prefKey);
    }

    _updateStatus(g, initialState);
    _positionPanel(g); // Re-posisi panel setelah rebuild
    document.getElementById(`colvis_panel_${g}`)?.classList.add('colvis-panel--open');
    document.getElementById(`colvis_trigger_${g}`)?.classList.add('colvis-btn--active');
    _activeGridId = g;
  }

  // ─── API Publik ───────────────────────────────────────────────

  return {
    /**
     * Init — panggil SETELAH clearGlobalSearch() dan SETELAH GridPreferenceManager.init()
     *
     * @param {string} gridSelector  '#jqGrid'
     * @param {string} prefKey       sama persis dengan key yang dipakai GridPreferenceManager
     * @param {Array}  baseColModel  dari getBaseColModel()
     */
    init(gridSelector, prefKey, baseColModel) {
      _gridSelector = gridSelector;
      _prefKey = prefKey;
      _baseColModel = baseColModel;

      _injectStyles();
      _injectButton(gridSelector);

      // Buat panel — baca state dari GPM (via cache localStorage)
      const gridId = $(gridSelector).getGridParam('id');
      const savedState = _loadViaGPM();
      _buildPanel(gridId, savedState);
      _updateStatus(gridId, savedState);
      renderBadge(gridSelector);
    },

    /** Panggil di loadComplete untuk sync badge setelah grid reload */
    renderBadge,

    // Handler publik (dipanggil dari HTML yang digenerate)
    _togglePanel,
    _onToggle,
    _showAll,
    _hideAll,
    _filterItems,
    _apply,
    _reset,
  };

})();