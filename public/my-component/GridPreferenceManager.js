/**
 * GridPreferenceManager — Fix request headers (Authorization + baseURL)
 * 
 * Perubahan dari versi sebelumnya:
 *   1. serverUrl sekarang pakai APP_URL yang sudah ada di app.php (bukan path hardcode)
 *   2. Semua request menyertakan Authorization: Bearer <token>
 *   3. Tambah helper _getAuthHeader() agar token selalu fresh (tidak stale)
 */

const GridPreferenceManager = (function () {

  const CONFIG = {
    localPrefix: 'grid_pref_',
    serverUrl: `${typeof APP_URL !== 'undefined' ? APP_URL.replace(/\/$/, '') : ''}/api/grid-preferences`,
    debounceMs: 800,
    mode: 'server',   // 'local' | 'server'
  };

  // ─── Ambil token terbaru dari variabel global di app.php ──────
  // ACCESS_TOKEN sudah dideklarasikan di app.php:
  //   let ACCESS_TOKEN = `<?= session()->get('accessToken') ?>`;
  function _getAuthHeader() {
    const token = typeof ACCESS_TOKEN !== 'undefined' ? ACCESS_TOKEN : '';
    return token ? { 'Authorization': `Bearer ${token}` } : {};
  }

  function debounce(fn, delay) {
    let timer;
    return function (...args) {
      clearTimeout(timer);
      timer = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  // ─── localStorage ─────────────────────────────────────────────
  function localKey(gridName) {
    return CONFIG.localPrefix + gridName;
  }

  function saveLocal(gridName, prefs) {
    try {
      localStorage.setItem(localKey(gridName), JSON.stringify(prefs));
    } catch (e) {
      console.warn('[GridPref] Gagal simpan localStorage:', e);
    }
  }

  function loadLocal(gridName) {
    try {
      const raw = localStorage.getItem(localKey(gridName));
      return raw ? JSON.parse(raw) : null;
    } catch (e) {
      console.warn('[GridPref] Gagal baca localStorage:', e);
      return null;
    }
  }

  function clearLocal(gridName) {
    localStorage.removeItem(localKey(gridName));
  }

  // ─── Server ───────────────────────────────────────────────────
  async function saveServer(gridName, prefs) {
    try {
      const res = await fetch(CONFIG.serverUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          ..._getAuthHeader(),   // ← Authorization: Bearer <token>
        },
        body: JSON.stringify({ grid_name: gridName, preferences: prefs }),
      });

      if (!res.ok) {
        console.warn('[GridPref] Gagal simpan ke server:', res.status, res.statusText);
      }
    } catch (e) {
      console.warn('[GridPref] Gagal simpan ke server:', e);
    }
  }

  async function loadServer(gridName) {
    try {
      const url = `${CONFIG.serverUrl}?grid_name=${encodeURIComponent(gridName)}`;
      const res = await fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          ..._getAuthHeader(),   // ← Authorization: Bearer <token>
        },
      });

      if (!res.ok) return null;

      const json = await res.json();
      return json.preferences || null;

    } catch (e) {
      console.warn('[GridPref] Gagal baca dari server:', e);
      return null;
    }
  }

  async function deleteServer(gridName) {
    try {
      const res = await fetch(`${CONFIG.serverUrl}/${encodeURIComponent(gridName)}`, {
        method: 'DELETE',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          ..._getAuthHeader(),   // ← Authorization: Bearer <token>
        },
      });

      if (!res.ok) {
        console.warn('[GridPref] Gagal delete dari server:', res.status);
      }
    } catch (e) {
      console.warn('[GridPref] Gagal delete dari server:', e);
    }
  }

  // ─── Ekstrak dari grid ────────────────────────────────────────
  function extractFromGrid(gridSelector) {
    const colModel = $(gridSelector).jqGrid('getGridParam', 'colModel');
    return colModel.map((col, index) => ({
      name: col.name,
      width: col.width,
      hidden: col.hidden || false,
      order: index,
    }));
  }

  // ─── Merge ke colModel ────────────────────────────────────────
  function applyToColModel(baseColModel, savedPrefs) {
    if (!savedPrefs || !savedPrefs.length) return baseColModel;

    const prefMap = {};
    savedPrefs.forEach(p => { prefMap[p.name] = p; });

    const updated = baseColModel.map(col => {
      const pref = prefMap[col.name];
      if (!pref) return col;
      return {
        ...col,
        width: pref.width !== undefined ? pref.width : col.width,
        hidden: pref.hidden !== undefined ? pref.hidden : (col.hidden || false),
      };
    });

    updated.sort((a, b) => {
      const oA = prefMap[a.name] ? prefMap[a.name].order : 9999;
      const oB = prefMap[b.name] ? prefMap[b.name].order : 9999;
      return oA - oB;
    });

    return updated;
  }

  // ─── Listener resizeStop ──────────────────────────────────────
  function attachListeners(gridSelector, gridName, onSaved) {
    const grid = $(gridSelector);

    const debouncedSave = debounce(async function () {
      const prefs = extractFromGrid(gridSelector);
      saveLocal(gridName, prefs);
      if (CONFIG.mode === 'server') await saveServer(gridName, prefs);
      if (typeof onSaved === 'function') onSaved(prefs);
    }, CONFIG.debounceMs);

    const originalResizeStop = grid.jqGrid('getGridParam', 'resizeStop');
    grid.jqGrid('setGridParam', {
      resizeStop: function (newWidth, index) {
        if (typeof originalResizeStop === 'function') {
          originalResizeStop.call(this, newWidth, index);
        }
        debouncedSave();
      }
    });
  }

  // ─── Reset ────────────────────────────────────────────────────
  async function reset(gridName) {
    clearLocal(gridName);
    if (CONFIG.mode === 'server') {
      await deleteServer(gridName);
    }
  }

  // ─── API Publik ───────────────────────────────────────────────
  return {
    configure(options) {
      // Kalau serverUrl di-override dari luar, hormati itu
      Object.assign(CONFIG, options);
    },

    async load(gridName) {
      const local = loadLocal(gridName);
      if (local) return local;

      if (CONFIG.mode === 'server') {
        const serverPrefs = await loadServer(gridName);
        if (serverPrefs) saveLocal(gridName, serverPrefs);
        return serverPrefs;
      }
      return null;
    },

    async save(gridName, prefs) {
      saveLocal(gridName, prefs);
      if (CONFIG.mode === 'server') await saveServer(gridName, prefs);
    },

    apply: applyToColModel,
    extract: extractFromGrid,
    attach: attachListeners,
    reset,
    _localKey: localKey,
  };

})();