class DraftFormManager {
  constructor(formSelector, options = {}) {
    this.form = $(formSelector);
    this.isPaused = false;

    this.settings = {
      expiry: 1000 * 60 * 60,
      debug: false,
      detailExtractors: {},  // { namaDetail: function() { return [...] } }
      detailRestorers: {},   // { namaDetail: function(rows) { ... } }
      getKey: () => {
        const action = this.form.data('action') || 'default';
        const module = window.location.pathname.replace(/\//g, '_');
        return `draft_${module}_${action}`;
      },
      ...options
    };

    this.init();
  }

  pause()  { this.isPaused = true; }
  resume() { this.isPaused = false; }
  log(...args) { if (this.settings.debug) console.log('[DraftManager]', ...args); }
  getKey() { return this.settings.getKey(); }

  // ─── SAVE ────────────────────────────────────────────────────────
  save() {
    if (!this.form.length) return;
    if (this.isPaused) return;
    if (this.form.data('action') !== 'add') return;

    const master  = this._saveMaster();
    const details = this._saveDetails();

    const payload = { time: Date.now(), master, details };
    localStorage.setItem(this.getKey(), JSON.stringify(payload));
    this.log('Saved', payload);
  }

  _saveMaster() {
    const master = {};

    this.form.find('[name]').each(function() {
      const el   = $(this);
      const name = el.attr('name');
      if (!name || name.includes('[]')) return;

      // Skip field yang ada di dalam tbody detail
      // (tbody detail tidak punya data-detail-body, 
      //  tapi kita bisa skip berdasarkan extractor yang didaftarkan)
      master[name.replace('[]', '')] = el.val();
    });

    return master;
  }

  _saveDetails() {
    const details = {};
    const extractors = this.settings.detailExtractors;

    Object.keys(extractors).forEach(key => {
      try {
        details[key] = extractors[key]();
      } catch(e) {
        this.log('Gagal extract detail:', key, e);
        details[key] = [];
      }
    });

    return details;
  }

  // ─── RESTORE ─────────────────────────────────────────────────────
  restore() {
    const raw = localStorage.getItem(this.getKey());
    if (!raw) return;

    const payload = JSON.parse(raw);
    if (Date.now() - payload.time > this.settings.expiry) {
      this.clear();
      this.log('Draft expired');
      return;
    }

    this.pause();

    if (payload.master)  this._restoreMaster(payload.master);
    if (payload.details) this._restoreDetails(payload.details);

    this.resume();
    this.log('Restored', payload);
  }

  _restoreMaster(master) {
    Object.keys(master).forEach(name => {
      const el = this.form.find(`[name="${name}"], [name="${name}[]"]`).first();
      if (!el.length) return;
      el.val(master[name]).trigger('change');
    });
  }

  _restoreDetails(details) {
    const restorers = this.settings.detailRestorers;

    Object.keys(details).forEach(key => {
      if (typeof restorers[key] !== 'function') {
        this.log('Tidak ada restorer untuk:', key);
        return;
      }
      try {
        restorers[key](details[key]);
      } catch(e) {
        this.log('Gagal restore detail:', key, e);
      }
    });
  }

  // ─── UTILS ───────────────────────────────────────────────────────
  clear() {
    localStorage.removeItem(this.getKey());
    this.log('Draft cleared');
  }

  bindAutoSave() {
    const self = this;
    this.form.on('input change', '[name]', function() {
      self.save();
    });
  }

  // Dipanggil manual dari luar saat baris detail berubah
  // (karena baris generated tidak selalu trigger event di form)
  triggerSave() {
    this.save();
  }

  init() {
    this.bindAutoSave();
  }
}