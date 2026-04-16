class LookupRegistry {

  static get(settings, inputWidth = 0) {
    // Kalau tidak pakai lookupKey, return config langsung dari settings
    if (!settings.lookupKey) {
      const required = ['url', 'column', 'sortname', 'sortorder'];
      const missing = required.filter(k => settings[k] == null);
      if (missing.length) {
        throw new Error(`[LookupRegistry] ${missing.join(', ')} wajib diisi`);
      }
      return {
        url: settings.url,
        sortname: settings.sortname || 'id',
        sortorder: settings.sortorder || 'asc',
        column: settings.column,
        filterPostData: settings.postData || {}
      };
    }

    const config = LookupRegistry._resolve(settings, inputWidth);
    if (!config) {
      throw new Error(`[LookupRegistry] lookupKey "${settings.lookupKey}" tidak ditemukan`);
    }
    return config;
  }

  // =====================================================
  // PRIVATE — resolve config berdasarkan lookupKey
  // =====================================================
  static _resolve(settings, inputWidth) {
    const width = LookupRegistry._calcWidth(settings, inputWidth);
    const postData = settings.postData || {};

    const catalog = LookupRegistry._catalog(width, postData);
    return catalog[settings.lookupKey] || null;
  }

  static _calcWidth(settings, inputWidth) {
    const label = settings.labelColumn;
    if (detectDeviceType() === 'desktop' && label === false) {
      return inputWidth - 2.1;
    }
    const selector = $(`#${settings.lookupName}`);
    return selector.parents('.input-group').outerWidth() + 'px';
  }

  // =====================================================
  // CATALOG — semua definisi lookup
  // Dipisah method sendiri agar mudah ditambah/diedit
  // =====================================================
  static _catalog(width, postData) {
    // const jenisKendaraan = postData.statusjeniskendaraan || '';
    // const urlUpahsupir = jenisKendaraan === 'TANGKI'
    //   ? 'upahsupirtangki/get'
    //   : 'upahsupirrincian/get';

    return {

      parameterAllV4: {
        url: `${API_URL}/parameters`,
        sortname: 'grp',
        sortorder: 'asc',
        column: [
          { label: 'ID', name: 'id', hidden: true, search: false },
          { label: 'GROUP', name: 'grp', width: (detectDeviceType() == "desktop") ? md_dekstop_1 : md_mobile_1 },
          { label: 'SUB GROUP', name: 'subgrp', width: (detectDeviceType() == "desktop") ? md_dekstop_1 : md_mobile_1 },
          { label: 'KELOMPOK', name: 'kelompok', width: (detectDeviceType() == "desktop") ? md_dekstop_1 : md_mobile_1 },
          { label: 'TEXT', name: 'text', width: (detectDeviceType() == "desktop") ? md_dekstop_1 : md_mobile_1 },
        ],
        filterPostData: {
          grp: '',
          subgrp: '',
          filters: '',
          isLookup: true,
          tipeData: 'JSON'
        }
      },

      parameterMemoV4: {
        url: `${API_URL}/parameter/lookup`,
        sortname: "text",
        sortorder: "asc",
        column: [
          {
            label: "ID",
            name: "id",
            width: "50px",
            hidden: true,
            sortable: false,
            search: false,
          },

          {
            label: 'TEXT',
            name: 'text',
            width: width,
          },
          {
            label: "memo",
            name: "memo",
            hidden: true,
            sortable: false,
            search: false,
          },
        ],
        filterPostData: {
          grp: '',
          subgrp: '',
          filters: '',
          isLookup: true,
          tipeData: 'JSON'
        }
      },

      parameterV4: {
        url: `${API_URL}/parameters`,
        sortname: 'text',
        sortorder: 'asc',
        column: [
          { label: 'ID', name: 'id', hidden: true, search: false },
          { label: 'TEXT', name: 'text', width: width },
          { label: 'memo', name: 'memo', hidden: true, search: false },
        ],
        filterPostData: {
          grp: '',
          subgrp: '',
          isLookup: true,
          tipeData: 'JSON'
        }
      },

      cabangV4: {
        url: `${API_URL}/cabang`,
        sortname: 'namacabang',
        sortorder: 'asc',
        column: [
          { label: 'ID', name: 'id', hidden: true, search: false },
          { label: 'Nama Cabang', name: 'namacabang', width: width },
          { label: 'Kode Cabang', name: 'kodecabang', width: width },
        ],
        filterPostData: {
          aktif: '',
          isLookup: true,
          tipeData: 'JSON'
        }
      },

      userV4: {
        url: `${API_URL}/users`,
        sortname: 'fullname',
        sortorder: 'asc',
        column: [
          { label: 'ID', name: 'id', hidden: true, search: false },
          { label: 'FULLNAME', name: 'fullname', width: width },
          { label: 'USERNAME', name: 'username', width: width },
        ],
        filterPostData: {
          isLookup: true,
          tipeData: 'JSON'
        }
      },

      // ← tambahkan entry baru di sini
      // namaBaruV4: { url, sortname, column, filterPostData }

    };
  }

  // =====================================================
  // HELPER — daftar semua key yang tersedia (untuk debug)
  // =====================================================
  static listKeys() {
    return Object.keys(LookupRegistry._catalog('', {}));
  }

  // =====================================================
  // STATIC — registrasi lookup baru dari luar
  // Berguna kalau ada modul yang mau daftar lookup sendiri
  // =====================================================
  static register(key, configFn) {
    if (LookupRegistry._custom == null) {
      LookupRegistry._custom = {};
    }
    LookupRegistry._custom[key] = configFn;
  }
}

LookupRegistry._custom = null;