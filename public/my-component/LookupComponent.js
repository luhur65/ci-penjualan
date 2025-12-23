/**
 * LookupComponent (Vanilla JS Version)
 * Menggantikan ketergantungan jQuery Plugin
 */
class LookupComponent {

  constructor(selector, options) {
    this.inputElement = document.querySelector(selector);

    // Jika elemen tidak ditemukan, stop.
    if (!this.inputElement) {
      console.error(`LookupComponent: Element ${selector} not found.`);
      return;
    }

    // 1. Default Settings (Abstract Properties)
    const defaults = {
      title: 'Lookup Data',
      endpoint: '',
      searching: ['name'],
      postData: {},

      // Abstract Methods (Callbacks)
      beforeProcess: () => { },
      onSelectRow: (data, el) => { },
      onCancel: (el) => { },
      onClear: (el) => { }
    };

    // Merge options user dengan defaults
    this.settings = { ...defaults, ...options };

    // Internal State
    this.timeout = null;
    this.wrapper = null;
    this.listContainer = null;
    this.currentValue = this.inputElement.value; // Simpan nilai awal

    // Inisialisasi UI dan Events
    this._initUI();
    this._bindEvents();
  }

  // --- Private Methods (Logika Dapur) ---

  _initUI() {
    // Bungkus input dengan wrapper agar posisi dropdown relative
    this.wrapper = document.createElement('div');
    this.wrapper.style.position = 'relative';
    this.inputElement.parentNode.insertBefore(this.wrapper, this.inputElement);
    this.wrapper.appendChild(this.inputElement);

    // Buat struktur Dropdown
    const listId = `lookup-list-${Math.random().toString(36).substr(2, 9)}`;
    const dropdownHTML = `
            <div id="${listId}" class="lookup-dropdown" style="display:none;">
                <div class="lookup-header">
                    <small>${this.settings.title}</small>
                    <span class="lookup-close" style="cursor:pointer; float:right;">&times;</span>
                </div>
                <ul class="lookup-items"></ul>
                <div class="lookup-loading" style="display:none; padding:10px; text-align:center;">Loading...</div>
            </div>
        `;

    // Inject HTML ke wrapper
    this.wrapper.insertAdjacentHTML('beforeend', dropdownHTML);

    // Cache elemen list
    this.dropdown = this.wrapper.querySelector('.lookup-dropdown');
    this.resultsList = this.wrapper.querySelector('.lookup-items');
    this.loadingIndicator = this.wrapper.querySelector('.lookup-loading');
    this.closeBtn = this.wrapper.querySelector('.lookup-close');
  }

  _bindEvents() {
    // Event Typing (Input)
    this.inputElement.addEventListener('input', (e) => {
      const val = e.target.value;

      // Handle Clear
      if (val === '') {
        this.settings.onClear(this.inputElement);
        this._hideDropdown();
        return;
      }

      // Debounce (Delay search)
      clearTimeout(this.timeout);
      this.timeout = setTimeout(() => {
        this._fetchData(val);
      }, 300);
    });

    // Event Focus (Simpan nilai saat ini)
    this.inputElement.addEventListener('focus', () => {
      this.currentValue = this.inputElement.value;
    });

    // Event Close Button
    this.closeBtn.addEventListener('click', () => {
      this._hideDropdown();
      this.settings.onCancel(this.inputElement);
    });

    // Event Click Outside (Tutup dropdown jika klik di luar)
    document.addEventListener('click', (e) => {
      if (!this.wrapper.contains(e.target)) {
        this._hideDropdown();
      }
    });
  }

  async _fetchData(query) {
    // Jalankan hook beforeProcess (Context 'this' adalah class instance)
    this.settings.beforeProcess.call(this);

    this.loadingIndicator.style.display = 'block';
    this.resultsList.innerHTML = '';
    this.dropdown.style.display = 'block';

    try {
      // Siapkan Query Params
      const params = new URLSearchParams({
        ...this.settings.postData, // Spread postData yang mungkin diubah di beforeProcess
        search: query,
        // search_columns dikirim sebagai JSON string atau array tergantung backend
        search_columns: JSON.stringify(this.settings.searching)
      });

      // Ganti BASE_URL sesuai konfigurasi global kamu
      const response = await fetch(`${window.location.origin}/api/${this.settings.endpoint}?${params}`);
      const result = await response.json();

      // Asumsi response backend formatnya: { data: [...] } atau langsung [...]
      const data = result.data || result;

      this._renderList(data);

    } catch (error) {
      console.error('Lookup Error:', error);
      this.resultsList.innerHTML = '<li style="padding:10px; color:red;">Error loading data</li>';
    } finally {
      this.loadingIndicator.style.display = 'none';
    }
  }

  _renderList(data) {
    this.resultsList.innerHTML = '';

    if (!data || data.length === 0) {
      this.resultsList.innerHTML = '<li class="no-data" style="padding:10px; color:#999;">Tidak ada data</li>';
      return;
    }

    data.forEach(item => {
      const li = document.createElement('li');
      li.style.padding = '8px 12px';
      li.style.cursor = 'pointer';
      li.style.borderBottom = '1px solid #f1f1f1';

      // Hover effect manual via JS (opsional, bisa via CSS)
      li.onmouseover = () => li.style.backgroundColor = '#f8f9fa';
      li.onmouseout = () => li.style.backgroundColor = 'transparent';

      // Teks yang tampil (Priority: Keterangan -> Kolom Searching -> ID)
      let label = item[this.settings.searching[0]];
      if (item.keterangan) label = item.keterangan;

      li.textContent = label;

      // Event Select Row
      li.addEventListener('click', () => {
        this.settings.onSelectRow(item, this.inputElement);
        this.currentValue = this.inputElement.value; // Update current value
        this._hideDropdown();
      });

      this.resultsList.appendChild(li);
    });
  }

  _hideDropdown() {
    this.dropdown.style.display = 'none';
  }
}