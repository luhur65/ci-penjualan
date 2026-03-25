class UIManager {
  static populateForm(form, data, specialFields = []) {
    $.each(data, (key, value) => {
      const element = form.find(`[name="${key}"]`);
      if (!element.length) return;

      if (element.is('select') || element.is('input[type="checkbox"]')) {
        element.val(value).trigger('change');
      } else {
        element.val(value);
      }

      if (specialFields.includes(key)) {
        element.data('current-value', value);
      }
    });
  }

  static getErrorMessage(error) {
    if (!error) return 'Unknown error';
    if (error.status === 0) return 'Koneksi kamu offline!';
    if (error.responseJSON) {
      return error.responseJSON.message || error.responseJSON.messages?.error || 'Terjadi kesalahan pada server';
    }
    if (error.responseText) return error.responseText;
    if (error.message) return error.message;
    return 'Terjadi kesalahan';
  }

  static PermissionButton(accessRights) {
    for (const [buttonId, hasAccess] of Object.entries(accessRights)) {
      if (hasAccess) {
        $(`#${buttonId}`).show();
      } else {
        $(`#${buttonId}`).hide();
      }
    }
  }

  /**
   * FUNGSI PINTASAN KEYBOARD (ACCESSIBILITY)
   * Menangani kombinasi ALT+A, ALT+E, ALT+D dengan proteksi Hak Akses
   */
  static setupKeyboardShortcuts() {
    $(document).on('keydown', function (e) {
      // Pastikan pengguna tidak sedang mengetik di dalam input/textarea/select
      // agar ALT+A tidak terpicu saat mereka sedang mengisi form!
      let isInputActive = $(e.target).is('input, textarea, select');
      if (isInputActive) return;

      // Kombinasi ALT + A (ADD)
      if (e.altKey && e.key.toLowerCase() === 'a') {
        e.preventDefault(); // Cegah browser melakukan aksi default

        // Cek permission dari object accessRights global Anda
        if (typeof accessRights !== 'undefined' && accessRights.add) {
          console.log('Shortcut Terpicu: ALT + A (Add)');
          $('#add').click(); // Simulasikan klik tombol Add
        } else {
          if (typeof showDialog === "function") showDialog('error', 'Anda tidak memiliki akses untuk menambah data.');
        }
      }

      // Kombinasi ALT + E (EDIT)
      if (e.altKey && e.key.toLowerCase() === 'e') {
        e.preventDefault();

        if (typeof accessRights !== 'undefined' && accessRights.edit) {
          console.log('Shortcut Terpicu: ALT + E (Edit)');
          $('#edit').click(); // Simulasikan klik tombol Edit
        } else {
          if (typeof showDialog === "function") showDialog('error', 'Anda tidak memiliki akses untuk mengubah data.');
        }
      }

      // Kombinasi ALT + D (DELETE)
      if (e.altKey && e.key.toLowerCase() === 'd') {
        e.preventDefault();

        if (typeof accessRights !== 'undefined' && accessRights.delete) {
          console.log('Shortcut Terpicu: ALT + D (Delete)');
          $('#delete').click(); // Simulasikan klik tombol Delete
        } else {
          if (typeof showDialog === "function") showDialog('error', 'Anda tidak memiliki akses untuk menghapus data.');
        }
      }
    });
  }
}