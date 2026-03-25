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
}