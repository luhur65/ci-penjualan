class DraftFormManager {
  constructor(formSelector, options = {}) {
    this.form = $(formSelector);
    this.isPaused = false; // Mencegah reset form agar tidak tersimpan sebagai draft kosong

    this.settings = {
      expiry: 1000 * 60 * 60, // Default 1 jam
      debug: false,
      getKey: () => {
        const action = this.form.data('action') || 'default';
        const module = window.location.pathname.replace(/\//g, '_');
        return `draft_${module}_${action}`;
      },
      ...options
    };

    this.init();
  }

  pause() { this.isPaused = true; }
  resume() { this.isPaused = false; }

  log(...args) {
    if (this.settings.debug) {
      // console.log('[DraftManager]', ...args);
    }
  }

  getKey() {
    return this.settings.getKey();
  }

  save() {
    if (!this.form.length) return;
    if (this.isPaused) return;
    if (this.form.data('action') !== 'add') return; // Hanya simpan di mode Add!

    const data = {};
    this.form.find('[name]').each(function () {
      const el = $(this);
      let name = el.attr('name');

      if (!name) return;
      name = name.replace('[]', '');
      let value = el.val();

      if (el.prop('multiple')) {
        value = value || [];
      }

      data[name] = value;
    });

    const payload = {
      time: Date.now(),
      data
    };

    localStorage.setItem(this.getKey(), JSON.stringify(payload));
    this.log('Saved', payload);
  }

  restore() {
    const raw = localStorage.getItem(this.getKey());
    if (!raw) return;

    const payload = JSON.parse(raw);

    if (Date.now() - payload.time > this.settings.expiry) {
      this.clear();
      this.log('Expired draft removed');
      return;
    }

    const data = payload.data;

    this.pause(); // Pause auto-save agar tidak terjadi looping saat data dimasukkan kembali

    Object.keys(data).forEach(name => {
      const el = this.form.find(`[name="${name}"], [name="${name}[]"]`);
      if (!el.length) return;

      let value = data[name];

      if (el.prop('multiple')) {
        el.val(Array.isArray(value) ? value : [value]).trigger('change');
      } else {
        el.val(value).trigger('change');
      }
    });

    this.resume();
    this.log('Restored', data);
  }

  clear() {
    localStorage.removeItem(this.getKey());
    this.log('Cleared draft');
  }

  bindAutoSave() {
    const self = this;
    this.form.on('input change', '[name]', function () {
      self.save();
    });
  }

  init() {
    this.bindAutoSave();
  }
}