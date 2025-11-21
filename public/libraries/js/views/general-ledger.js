$(document).ready(function () {
  let tahunElement = $('[name="tahun_id"]');
  let kodePerkiraanAwalElement = $('[name="kode_perkiraan_awal"]');
  let kodePerkiraanAkhirElement = $('[name="kode_perkiraan_akhir"]');
  let form = $("#generalLedgerRange");

  initSelect2(tahunElement);
  initSelect2(kodePerkiraanAwalElement);
  initSelect2(kodePerkiraanAkhirElement);

  setTahunOptions(tahunElement);

  tahunElement.on("change", function () {
    let tahunId = $(this).val();

    setPerkiraanOptions(kodePerkiraanAwalElement, tahunId);
    setPerkiraanOptions(kodePerkiraanAkhirElement, tahunId);
  });

  grid = loadGrid($("#generalLedgerGrid"));

  $("#btnExport").on("click", function () {
    let element = $(this);
    let tahunId = form.find(`[name="tahun_id"]`).val();
    let kodePerkiraanAwal = form.find(`[name="kode_perkiraan_awal"]`).val();
    let kodePerkiraanAkhir = form.find(`[name="kode_perkiraan_akhir"]`).val();
    let xhr = new XMLHttpRequest();

    element.attr("disabled", "disabled");

    $(form).find(".is-invalid").removeClass("is-invalid");
    $(form).find(".invalid-feedback").remove();

    xhr.open(
      "GET",
      `${API_URL}/general-ledger/export?tahun_id=${tahunId}&kode_perkiraan_awal=${kodePerkiraanAwal}&kode_perkiraan_akhir=${kodePerkiraanAkhir}`,
      true
    );
    xhr.responseType = "arraybuffer";

    xhr.onload = function (e) {
      if (this.status === 200) {
        if (this.response !== undefined) {
          let blob = new Blob([this.response], {
            type: "application/vnd.ms-excel",
          });
          let link = document.createElement("a");

          link.href = window.URL.createObjectURL(blob);
          link.download = `Laporan General Ledger.xlsx`;
          link.click();
        }
      }

      if ([404, 422, 500].includes(this.status)) {
        showDialog("error", this.statusText);
      }

      element.removeAttr("disabled");
    };

    xhr.onerror = () => {
      submitButton.removeAttr("disabled");

      showDialog("error", "Something wrong.");
    };

    xhr.send();
  });

  $("#btnReload").on("click", function () {
    let rangeFilters = {
      tahun_id: form.find(`[name="tahun_id"]`).val(),
      kode_perkiraan_awal: form.find(`[name="kode_perkiraan_awal"]`).val(),
      kode_perkiraan_akhir: form.find(`[name="kode_perkiraan_akhir"]`).val(),
      proses: "reload",
    };

    // Call loadDataHeader
    loadDataHeader("general-ledger", rangeFilters);
  });
});


function loadDataHeader(url, additional = null) {
  let data = {
    tgldari: $("#tgldariheader").val(),
    tglsampai: $("#tglsampaiheader").val(),
    proses: "reload",
  };

  data = {
    ...data,
    ...additional,
  };
 
	$("#generalLedgerGrid").jqGrid("setGridParam", {
		url: `${API_URL}/${url}`,
		datatype: "json",
		postData: data,
		page: 1,
	}).trigger('reloadGrid');

	// Clear validation errors
	$('.is-invalid').removeClass('is-invalid');
	$('.invalid-feedback').remove();
}

function loadGrid(element) {
  return element
    .jqGrid({
      datatype: "local",
      styleUI: "Bootstrap4",
      iconSet: "fontAwesome",
      styleUI: "Bootstrap4",
      colModel: [
        {
          label: "ID",
          name: "id",
          width: 80,
        },
        {
          label: "Tanggal",
          name: "tgl",
          width: 150,
          formatter: "date",
          formatoptions: {
            srcformat: "ISO8601Long",
            newformat: "d-m-Y",
          },
        },
        {
          label: "No. Bukti",
          name: "no_bukti",
          width: 150,
        },
        {
          label: "Kode Perkiraan",
          name: "code_perkiraan",
          width: 150,
        },
        {
          label: "Keterangan",
          name: "keterangan",
          width: 150,
        },
        {
          label: "Ref",
          name: "ref",
          width: 150,
        },
        {
          label: "Debet",
          name: "nominal_debet",
          width: 200,
          align: "right",
          formatter: currencyFormat,
        },
        {
          label: "Kredit",
          name: "nominal_kredit",
          width: 200,
          align: "right",
          formatter: currencyFormat,
        },
        {
          label: "Saldo",
          name: "saldo",
          width: 200,
          align: "right",
          formatter: currencyFormat,
        },
      ],
      jsonReader: {
        root: "data",
        total: "attributes.total_pages",
        records: "attributes.total_rows",
      },
      autowidth: true,
      shrinkToFit: false,
      height: 350,
      page: 1,
      rownumbers: true,
      rownumWidth: 45,
      rowNum: 10,
      rowList: [10, 20, 50, 0],
      toolbar: [true, "top"],
      viewrecords: true,
      selectedIndex: 0,
      triggerClick: true,
      serializeGridData: function (postData) {
        postData.sort_indexes = [postData.sort_index];
        postData.sort_orders = [postData.sort_order];

        delete postData.sort_index;
        delete postData.sort_order;

        return postData;
      },
      onSelectRow: function (id) {
        activeGrid = this;

        let limit = $(this).jqGrid("getGridParam", "postData").limit;
        let page = $(this).jqGrid("getGridParam", "page");
        let selectedIndex = $(this).jqGrid("getCell", id, "rn") - 1;

        if (selectedIndex >= limit)
          selectedIndex = selectedIndex - limit * (page - 1);

        $(this).jqGrid("setGridParam", {
          selectedIndex,
        });
      },
      loadComplete: function () {
        changeJqGridRowListText();

        $(this).parents(".ui-jqgrid").find("input").attr("autocomplete", "off");

        let selectedIndex = $(this).jqGrid("getGridParam").selectedIndex;

        if (selectedIndex > $(this).getDataIDs().length - 1) {
          selectedIndex = $(this).getDataIDs().length - 1;
        }

        if ($(this).jqGrid("getGridParam").triggerClick) {
          $(this)
            .find(`tr[id="${$(this).getDataIDs()[selectedIndex]}"]`)
            .click();

          $(this).jqGrid("setGridParam", {
            triggerClick: false,
          });
        } else {
          $(this).setSelection($(this).getDataIDs()[selectedIndex]);
        }

        setHighlight(this);
      },
    })
    .jqGrid("setLabel", "rn", "No.")
    .bindKeys()
    .toolbarBindKeys()
    .customBindKeys()
    .loadClearFilter()
    .customPager();
}

function setErrorMessages(form, errors) {
  let element;

  $.each(errors, (index, value) => {
    element = $(form).find(`[name=${index}]`);

    if (typeof element !== "undefined" && element.length > 0) {
      element.addClass("is-invalid");

      let elementSibling = element.siblings().last();

      if (elementSibling.length > 0) {
        elementSibling.after(`
					<div class="invalid-feedback">
					${value}
					</div>
			  `);
      } else {
        element.after(`
					<div class="invalid-feedback">
					${value}
					</div>
			  `);
      }
    }
  });

  $(form).find(".is-invalid").first().focus();
}

function setJurnalGroupOptions(element) {
  return new Promise((resolve, reject) => {
    element.empty();
    element
      .append(new Option("-- Pilih Jurnal Group --", "", false, true))
      .trigger("change");

    $.ajax({
      url: `${API_URL}/jurnal-group`,
      method: "GET",
      dataType: "JSON",
      data: {
        limit: 0,
      },
      success: (response) => {
        response.data.forEach((jurnalGroup) => {
          let option = new Option(jurnalGroup.description, jurnalGroup.id);

          element.append(option).trigger("change");
        });

        resolve();
      },
    });
  });
}

function setCabangOptions(element) {
  return new Promise((resolve, reject) => {
    element.empty();
    element
      .append(new Option("-- Pilih Cabang --", "", false, true))
      .trigger("change");

    $.ajax({
      url: `${API_URL}/cabang`,
      method: "GET",
      dataType: "JSON",
      data: {
        limit: 0,
      },
      success: (response) => {
        response.data.forEach((cabang) => {
          let option = new Option(`(${cabang.code}) ${cabang.name}`, cabang.id);

          element.append(option).trigger("change");
        });

        resolve();
      },
    });
  });
}

function setTahunOptions(element) {
  return new Promise((resolve, reject) => {
    element.empty();
    element.append(new Option("-- Pilih Tahun --", "", false, true));

    $.ajax({
      url: `${API_URL}/tahun`,
      method: "GET",
      dataType: "JSON",
      data: {
        limit: 0,
      },
      success: (response) => {
        response.data.forEach((tahun) => {
          let option = new Option(tahun.tahun, tahun.id);

          element.append(option);
        });

        resolve();
      },
    });
  });
}

function setPerkiraanOptions(element, tahunId) {
  return new Promise((resolve, reject) => {
    element.empty();
    element
      .append(new Option("-- Pilih Perkiraan --", "", false, true))
      .trigger("change");

    $.ajax({
      url: `${API_URL}/perkiraan`,
      method: "GET",
      dataType: "JSON",
      data: {
        limit: 0,
        filters: {
          tahun_id: `eq:${tahunId}`,
        },
      },
      success: (response) => {
        response.data.forEach((perkiraan) => {
          let option = new Option(
            `(${perkiraan.code}) ${perkiraan.name}`,
            perkiraan.code
          );

          element.append(option).trigger("change");
        });

        resolve();
      },
    });
  });
}
