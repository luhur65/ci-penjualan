// let activeGrid;

let sm_dekstop_1 = "70px";
let sm_dekstop_2 = "100px";
let sm_dekstop_3 = "150px";
let sm_dekstop_4 = "200px";
let md_dekstop_1 = "250px";
let md_dekstop_2 = "300px";
let md_dekstop_3 = "350px";
let md_dekstop_4 = "400px";
let lg_dekstop_1 = "450px";
let lg_dekstop_2 = "500px";
let lg_dekstop_3 = "550px";
let lg_dekstop_4 = "600px";

let sm_mobile_1 = "150px";
let sm_mobile_2 = "200px";
let sm_mobile_3 = "250px";
let sm_mobile_4 = "300px";
let md_mobile_1 = "350px";
let md_mobile_2 = "400px";
let md_mobile_3 = "450px";
let md_mobile_4 = "500px";
let lg_mobile_1 = "550px";
let lg_mobile_2 = "600px";
let lg_mobile_3 = "650px";
let lg_mobile_4 = "700px";

let sm_extendSize_1 = 70;
let sm_extendSize_2 = 100;
let sm_extendSize_3 = 150;
let sm_extendSize_4 = 200;
let md_extendSize_1 = 250;
let md_extendSize_2 = 300;
let md_extendSize_3 = 350;
let md_extendSize_4 = 400;
let lg_extendSize_1 = 450;
let lg_extendSize_2 = 500;
let lg_extendSize_3 = 550;
let lg_extendSize_4 = 600;

// Disable jqGrid row hover globally
if (typeof $.jgrid !== 'undefined' && $.jgrid.defaults) {
    $.extend($.jgrid.defaults, { hoverrows: false });
}
// Remove table-hover class from Bootstrap UI defaults if it exists
if (typeof $.jgrid !== 'undefined' && $.jgrid.styleUI) {
    if ($.jgrid.styleUI.Bootstrap) {
        if ($.jgrid.styleUI.Bootstrap.table && $.jgrid.styleUI.Bootstrap.table.className) {
            $.jgrid.styleUI.Bootstrap.table.className = $.jgrid.styleUI.Bootstrap.table.className.replace('table-hover', '');
        }
    }
    if ($.jgrid.styleUI.Bootstrap4) {
        if ($.jgrid.styleUI.Bootstrap4.table && $.jgrid.styleUI.Bootstrap4.table.className) {
            $.jgrid.styleUI.Bootstrap4.table.className = $.jgrid.styleUI.Bootstrap4.table.className.replace('table-hover', '');
        }
    }
    if ($.jgrid.styleUI.Bootstrap5) {
        if ($.jgrid.styleUI.Bootstrap5.table && $.jgrid.styleUI.Bootstrap5.table.className) {
            $.jgrid.styleUI.Bootstrap5.table.className = $.jgrid.styleUI.Bootstrap5.table.className.replace('table-hover', '');
        }
    }
    
}

    // Inject CSS to hide disabled sort icons and align active arrows perfectly inline
    $('<style>th.ui-th-column .ui-grid-ico-sort.ui-disabled { display: none !important; } th.ui-th-column .s-ico { display: inline-block !important; } th.ui-th-column .ui-grid-ico-sort { position: static !important; margin-top: 0 !important; margin-left: 6px !important; font-size: 0.85em !important; transform: translateY(1px) !important; }</style>').appendTo('head');

$(document).ready(function () {
	replaceJqgridBootstrapIcon();

	$(document).on("show.bs.modal", ".modal", function () {
		const zIndex = 1040 + 10 * $(".modal:visible").length;
		$(this).css("z-index", zIndex);
		setTimeout(() =>
			$(".modal-backdrop")
				.not(".modal-stack")
				.css("z-index", zIndex - 1)
				.addClass("modal-stack")
		);
	});

	$(document).find("input").attr("autocomplete", "off");
	$(document).find("input, textarea").attr("spellcheck", "false");

	$(document).on(
		"input",
		`input[type="text"]:not([data-uppercase="false"])`,
		function () {
			$(this).val((index, value) => {
				return value.toUpperCase();
			});
		}
	);

	$(document).on("submit", "form", function () {
		$(this)
			.find('input[type="text"]:not([data-uppercase="false"])')
			.each(function () {
				$(this).val($(this).val().toUpperCase());
			});
	});


	$(document).on("shown.bs.modal", ".modal", function () {
		$(this).find("form [name]:not([readonly], [disabled])").first().focus();
		$(this).find("form").data("hasChanged", false);
	});

	$(document).on("hide.bs.modal", ".modal", function () {
		let form = $(this).find("form");

		if (form.data("hasChanged")) {
			let confirmClose = confirm(
				"You have unsaved changes. Are you sure to close form?"
			);

			if (!confirmClose) {
				return false;
			}

			form.data("hasChanged", false);

			return true;
		}
	});

	$(document).on("collapsed-done.lte.pushmenu", function () {
		focusToGrid();
	});

	$("#loader").addClass("d-none");

	$.fn.modal.Constructor.Default.backdrop = "static";

	openMenuParents()
});

window.onbeforeunload = () => {
	let hasUnsavedChanges = false;

	$("form").each((index, element) => {
		if ($(element).data("hasChanged")) {
			hasUnsavedChanges = true;
		}
	});

	if (hasUnsavedChanges) {
		return confirm("You have unsaved changes. Are you sure to close form?");
	}

	$("#loader").removeClass("d-none");
};

const delay = (function () {
	let timer = 0;

	return function (callback, ms) {
		clearTimeout(timer);
		timer = setTimeout(callback, ms);
	};
})();

function focusToGrid() {
	let selectedIndex = $(activeGrid).jqGrid("getGridParam")?.selectedIndex ?? 0;

	$(activeGrid).setGridParam({
		triggerClick: true,
	});

	$(activeGrid)
		.find(`tr[id="${$(activeGrid).getDataIDs()[selectedIndex]}"]`)
		.click();
}

function getAuthToken() {
    // Prioritas 1: PHP session via inline script
    if (typeof ACCESS_TOKEN !== 'undefined' && ACCESS_TOKEN && ACCESS_TOKEN !== 'null' && ACCESS_TOKEN !== '') {
        return ACCESS_TOKEN;
    }
    // Prioritas 2: localStorage (fallback)
    return localStorage.getItem('token') || localStorage.getItem('access_token');
}

// function showToastNotification(title, message, actionUrl) {
//     if (typeof Swal !== 'undefined') {
//         Swal.fire({
//             title: title,
//             text: message,
//             icon: 'info',
//             toast: true,
//             position: 'top-end',
//             showConfirmButton: true,
//             confirmButtonText: 'Download',
//             timer: 10000,
//             timerProgressBar: true,
//         }).then((result) => {
//             if (result.isConfirmed) {
// 								downloadFileWithAuth(actionUrl);
//             }
//         });
//     } else {
//         alert(title + "\\n" + message);
//         if (confirm("Download file?")) {
// 					downloadFileWithAuth(actionUrl);
//         }
//     }
// }

function showToastNotification(title, message, actionUrl, notifId = null) {
    // Hapus dialog lama jika ada
    $('#notif-download-dialog').remove();

    const dialog = $(`
        <div id="notif-download-dialog" title="${title}" class="text-center">
            <span class="fas fa-info-circle text-info" style="font-size:30px;"></span>
            <p class="mt-2">${message}</p>
        </div>
    `);

    $('body').append(dialog);

    dialog.dialog({
        modal: false,
        width: 350,
        position: { my: 'center', at: 'center', of: window },
        closeOnEscape: true,
        buttons: [
					{
							text: 'Download',
							class: 'btn btn-primary btn-sm',
							click: function () {
								$(this).dialog('close');
								downloadFileWithAuth(actionUrl);
									
								if (notifId) {
										$.ajax({
												url: `${API_URL}/notifications/read/${notifId}`,
												method: 'PATCH',
												headers: {
														'Authorization': `Bearer ${getAuthToken()}`
												}
										});
								}
							}
					},
					{
							text: 'Tutup',
							class: 'btn btn-secondary btn-sm',
							click: function () {
								$(this).dialog('close');
								return;
							}
					}
        ],
        close: function () {
					$(this).dialog('destroy').remove();
					return;
        }
    });

    // Auto close setelah 10 detik jika tidak direspon
    // setTimeout(() => {
    //     if (dialog.dialog('instance')) {
    //         dialog.dialog('close');
    //     }
    // }, 20000);
}

// Websocket setup for notifications
if (typeof io !== 'undefined') {
	
    const socketUrl = typeof SOCKET_URL !== 'undefined' ? SOCKET_URL : 'https://projects.karaya.site';
    const socket = io(socketUrl, {
			transports: ['websocket']
		}); // Use configurable socket URL

    socket.on('connect', () => {
        console.log('Connected to WebSocket server');
        // Retrieve user ID from localStorage or another global variable
        // This is a common pattern in SPAs or apps that store auth data client-side
        const userId = localStorage.getItem('user_id') || (typeof CURRENT_USER_ID !== 'undefined' ? CURRENT_USER_ID : null);
        if (userId) {
            socket.emit('join_room', userId);
        }
    });

    socket.on('notification', (data) => {
        console.log('Received notification:', data);
        if (data && data.title && data.message && data.action_url) {
            // Using base URL to properly resolve actionUrl for file download APIs
            // const baseUrl = typeof API_URL !== 'undefined' ? API_URL : '';
            // const actionUrl = `${baseUrl}/notifications/download/` + data.action_url.split('/').pop();
			const actionUrl = data.action_url;
            showToastNotification(data.title, data.message, actionUrl, data.id);

            // if (data.id) {
            //     $.ajax({
            //         url: `${API_URL}/notifications/read/${data.id}`,
            //         method: 'PATCH',
            //         headers: {
            //             'Authorization': `Bearer ${getAuthToken()}`
            //         }
            //     });
            // }
        }
    });

    socket.on('disconnect', () => {
        console.log('Disconnected from WebSocket server');
    });


	// =============================================
	// NOTIFICATION MANAGER
	// =============================================

	function loadUnreadNotifications() {
		$.ajax({
			url: `${API_URL}/notifications/unread`,
			method: 'GET',
			headers: { 'Authorization': `Bearer ${getAuthToken()}` },
			success: function (data) {
				renderNotifications(data);
			},
			error: function () {
				console.warn('Gagal load notifikasi');
			}
		});
	}

	function markNotifAsRead(notifId, itemEl) {
		$.ajax({
			url: `${API_URL}/notifications/read/${notifId}`,
			method: 'PATCH',
			headers: { 'Authorization': `Bearer ${getAuthToken()}` },
			success: function () {
				// Hapus dari dropdown setelah dibaca
				itemEl.next('.dropdown-divider').remove();
				itemEl.remove();

				// Update badge
				const remaining = $('#notifList .notif-item').length;
				if (remaining === 0) {
					$('#notifList').html('<div class="dropdown-item text-center text-muted">Tidak ada notifikasi</div>');
					$('#notifBadge').hide();
				} else {
					$('#notifBadge').text(remaining);
				}
			}
		});
	}
	
	function buildNotifItem(notif, createdAt = null) {
		return `
				<div class="dropdown-item d-flex align-items-start notif-item" 
						 style="white-space:normal; cursor:default;" 
						 data-id="${notif.id ?? ''}" 
						 data-url="${notif.action_url}">
						<i class="fas fa-file-excel text-success mt-1 mr-3" style="font-size:18px;"></i>
						<div class="flex-grow-1" style="min-width:0;">
								<div class="font-weight-bold">${notif.title}</div>
								<div class="text-muted small">${notif.message}</div>
								<div class="text-muted smaller">
										<i class="far fa-clock mr-1"></i>${formatRelativeTime(createdAt ?? notif.created_at)}
								</div>
						</div>
						<button class="btn btn-xs btn-download-notif ml-2" title="Download">
								<i class="fas fa-download"></i>
						</button>
				</div>
				<div class="dropdown-divider"></div>
		`;
	}

	function renderNotifications(notifications) {
		const list = $('#notifList');
		const badge = $('#notifBadge');
		const badgeText = $('#notifBadgeText');

		list.empty();

		if (!notifications || notifications.length === 0) {
			list.html('<div class="dropdown-item text-center text-muted" style="padding:20px;">Tidak ada notifikasi</div>');
			badge.hide();
			badgeText.hide();
			return;
		}

		const count = notifications.length;
		badge.text(count).show();
		badgeText.text(count + ' baru').show();

		notifications.forEach(notif => {
			list.append(buildNotifItem(notif));
		});
	}

	// Klik tombol download di dalam dropdown notifikasi
	$(document).on('click', '.btn-download-notif', function (e) {
		e.stopPropagation(); // cegah dropdown tertutup

		const item = $(this).closest('.notif-item');
		const notifId = item.data('id');
		const url = item.data('url');

		downloadFileWithAuth(url);
		markNotifAsRead(notifId, item);
	});

	// Load notifikasi saat pertama kali halaman dibuka
	$(document).ready(function () {
		if (typeof API_URL !== 'undefined') {
			loadUnreadNotifications();
		}
	});

	// Update notifikasi saat ada yang masuk via WebSocket
	socket.on('notification', (data) => {
		if (data && data.title && data.message && data.action_url) {
			showToastNotification(data.title, data.message, data.action_url);

			// Tambahkan ke dropdown tanpa reload
			addNotifToDropdown(data);
		}
	});


	function addNotifToDropdown(notif) {
		const list = $('#notifList');
		const badge = $('#notifBadge');

		list.find('.dropdown-item:not(.notif-item)').remove();

		list.prepend(buildNotifItem(notif, new Date().toISOString()));

		const count = $('#notifList .notif-item').length;
		badge.text(count).show();
	}

	function formatRelativeTime(dateStr) {
		if (!dateStr) return '';

		const date = new Date(dateStr);
		const now = new Date();
		const diff = Math.floor((now - date) / 1000); // selisih dalam detik

		if (diff < 60) return 'Baru saja';
		if (diff < 3600) return Math.floor(diff / 60) + ' menit yang lalu';
		if (diff < 86400) return Math.floor(diff / 3600) + ' jam yang lalu';
		if (diff < 86400 * 2) return 'Kemarin';
		if (diff < 86400 * 7) return Math.floor(diff / 86400) + ' hari yang lalu';

		// Lebih dari 7 hari — tampilkan tanggal lengkap
		return date.toLocaleDateString('id-ID', {
			day: '2-digit',
			month: 'long',
			year: 'numeric',
			hour: '2-digit',
			minute: '2-digit'
		});
	}
}

function downloadFileWithAuth(url) {
	console.log('=== DEBUG DOWNLOAD ===');
  console.log('1. URL yang akan didownload:', url);

	const token = getAuthToken();

	console.log('2. Token ada?', token ? 'YA (length: ' + token.length + ')' : 'TIDAK ADA');
	console.log('3. Token value (50 char pertama):', token ? token.substring(0, 50) + '...' : 'null');

	fetch(url, {
		method: 'GET',
		headers: {
			'Authorization': `Bearer ${token}`
		}
	})
		.then(response => {
			console.log('4. Response status:', response.status);
			console.log('5. Response ok?', response.ok);
			console.log('6. Response URL (final, setelah redirect):', response.url);
			console.log('7. Response type:', response.type);
			console.log('8. Content-Type:', response.headers.get('Content-Type'));
			console.log('9. Content-Disposition:', response.headers.get('Content-Disposition'));

			if (!response.ok) {
				// Baca body error untuk tahu alasan gagal
				return response.text().then(text => {
					console.error('10. Response body (error):', text);
					throw new Error('HTTP ' + response.status + ' — ' + text);
				});
			}

			// Ambil nama file dari Content-Disposition jika tersedia
			const disposition = response.headers.get('Content-Disposition');
			let fileName = url.split('/').pop(); // fallback dari URL
			if (disposition) {
				const match = disposition.match(/filename[^;=\n]*=(['"]?)([^\n]*)\1/);
				if (match?.[2]) fileName = match[2].trim();
			}

			console.log('10. Nama file yang akan didownload:', fileName);

			return response.blob().then(blob => {
				console.log('11. Blob size:', blob.size, 'bytes');
				console.log('12. Blob type:', blob.type);
				return { blob, fileName };
			});
		})
		.then(({ blob, fileName }) => {
			console.log('13. Membuat link download...');
			const blobUrl = window.URL.createObjectURL(blob);
			const a = document.createElement('a');
			a.href = blobUrl;
			a.download = fileName;
			document.body.appendChild(a);
			console.log('14. Klik link download...');
			a.click();
			a.remove();
			window.URL.revokeObjectURL(blobUrl);
			console.log('15. Download selesai!');
		})
		.catch(err => {
			console.error('=== DOWNLOAD GAGAL ===');
			console.error('Error:', err.message);
			if (typeof showDialog === 'function') {
				showDialog('error', 'Gagal mendownload file. Silakan coba lagi.');
			}
		});
}

function scrollGridSelectionIntoView(grid, rowId) {
	var $grid = $(grid);
	var escapedId = typeof $.jgrid !== 'undefined' ? $.jgrid.jqID(rowId) : $.escapeSelector(rowId);
	var rowEl = $grid.find("tr#" + escapedId)[0];

	if (rowEl) {
		rowEl.scrollIntoView({ behavior: 'auto', block: 'nearest', inline: 'nearest' });
	}
}

function changeJqGridRowListText() {
	$(document).find('select[id$="rowList"] option[value=0]').text("ALL");
}

function detectDeviceType() {
	const ua = navigator.userAgent;
	if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
		return "tablet";
	} else if (
		/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(
			ua
		)
	) {
		return "mobile";
	}
	return "desktop";
}

function showDialog(state, message, buttons = []) {
	let states = {
		success: {
			icon: "fa fa-check",
			color: "text-success",
		},
		error: {
			icon: "fa fa-exclamation-triangle",
			color: "text-danger",
		},
		warning: {
			icon: "fa fa-exclamation-triangle",
			color: "text-warning",
		},
		question: {
			icon: "fas fa-question-circle",
			color: "text-warning",
		},
		info: {
			icon: "fas fa-info-circle",
			color: "text-info",
		},
	};

	let element = $(`
		<div title="Pesan" class="text-center">
			<span class="${states[state].icon} ${states[state].color}" aria-hidden="true" style="font-size:25px;"></span>
			<p>${message}</p>
		</div>
	`);

	$("body").append(element);

	buttons.unshift({
		text: "Ok",
		click: function () {
			$(this).dialog("close");
		},
	});

	element.dialog({
		modal: true,
		buttons: buttons,
	});
}
  

function setHighlight(grid) {
	const gridId = $(grid).getGridParam('id');
	const postData = $(grid).jqGrid("getGridParam", "postData");

	if (!postData.filters) return;

	let filters;
	try {
		filters = JSON.parse(postData.filters);
	} catch (e) {
		return;
	}

	if (!filters.rules || filters.rules.length === 0) return;

	// Clear highlight dulu (penting!)
	$(grid).find("td").unhighlight();

	filters.rules.forEach(rule => {
		const field = rule.field;
		const text = rule.data;

		$(grid)
			.find(`td[aria-describedby="${gridId}_${field}"]`)
			.highlight(text);
	});
}

// function initSelect2(element, dropdownParent = null) {
// 	let options = {
// 		width: "100%",
// 		theme: "bootstrap4",
// 		dropdownParent: dropdownParent,
// 	};

// 	$(element)
// 		.select2(options)
// 		.on("select2:open", function (event) {
// 			document.querySelector(".select2-search__field").focus();
// 		});
// }

function initSelect2(element, dropdownParent = null) {
	let options = {
		width: "100%",
		theme: "bootstrap4",
		dropdownParent: dropdownParent,
	};

	$(element)
		.select2(options)
		.on("select2:open", function () {
			setTimeout(() => {
				let searchField = document.querySelector(
					'.select2-container--open .select2-search__field'
				);
				if (searchField) searchField.focus();
			}, 0);
		});
}

function initAutoNumeric(element) {
	let option = {
		digitGroupSeparator: ",",
		decimalCharacter: ".",
	};

	element.classList.add("text-right");
	element.dataset.autoNumeric = true;

	new AutoNumeric(element, option);
}

function initDatepicker(element) {
	if (!element.parent().hasClass("input-group")) {
		element.wrap(`
			<div class="input-group">
			</div>
		`);
	}

	element
		.datepicker({
			dateFormat: "dd-mm-yy",
			changeYear: true,
			changeMonth: true,
			assumeNearbyYear: true,
			showOn: "button",
			beforeShow: function (element) {
				$(element).css({
					position: "relative",
				});
			},
		})
		.inputmask({
			inputFormat: "dd-mm-yyyy",
			alias: "datetime",
		})
		.focusout(function (e) {
			let val = $(this).val();
			if (val.match("[a-zA-Z]") == null) {
				if (val.length == 8) {
					$(this)
						.inputmask({
							inputFormat: "dd-mm-yyyy",
						})
						.val([val.slice(0, 6), "20", val.slice(6)].join(""));
				}
			} else {
				$(this).focus();
			}
		});

	element
		.siblings(".ui-datepicker-trigger")
		.wrap(
			`
			<div class="input-group-append">
			</div>
		`
		)
		.addClass("btn btn-primary").html(`
			<i class="fa fa-calendar-alt"></i>
		`);

	element.on("keydown", function (event) {
		if (event.keyCode === 115) {
			if (element.datepicker("widget").not(":visible")) {
				element.datepicker("show");
			}
		}
	});
}

function unformatAutoNumeric(data) {
	// need to improve
	let autoNumericElements = $(".autonumeric");

	$.each(autoNumericElements, (index, autoNumericElement) => {
		let inputs = data.filter((row) => row.name == autoNumericElement.name);

		inputs.forEach((input, index) => {
			if (input.value !== "") {
				input.value = AutoNumeric.getNumber(autoNumericElement);
			}
		});
	});

	return data;
}

function formatDate(value) {
	let date = new Date(value);

	let seconds = date.getSeconds("default");
	let minutes = date.getMinutes("default");
	let hours = date.getHours("default");
	let day = date.getDate("default");
	let month = date.getMonth("default") + 1;
	let year = date.getFullYear("default");

	return `${day.toString().padStart(2, "0")}-${month
		.toString()
		.padStart(2, "0")}-${year}`;
}

function unFormatDate(value) {
	const [day, month, year] = value.split("-");

	return `${year}-${month}-${day}`;
}

function currencyFormat(value) {
	let result = parseFloat(value).toLocaleString("en-US", {
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
	});

	result = result.replace(/\./g, "*");
	result = result.replace(/,/g, ",");
	result = result.replace(/\*/g, ".");

	return result;
}

function currencyUnformat(value) {
	let result = parseFloat(value.replaceAll(",", ""));

	return result;
}

function openMenuParents() {
	let currentMenu = $("a.nav-link.active").first();
	let parents = currentMenu.parents("li.nav-item");

	parents.each((index, parent) => {
		$(parent).addClass("menu-open");
	});
}

$.fn.formBindKeys = function () {
	return this.each(function () {
		let form = $(this);
		let validElementSelector =
			"[name]:not(:hidden, [readonly], [disabled], .disabled), button:submit";
		let validKeyCodes = [13, 38, 13, 40, 13];

		form.on("keydown", validElementSelector, function (event) {
			let inputs = form.find(validElementSelector);
			var currentInput = $(this);
			var currentIndex = inputs.index(currentInput);
			var nextInput;

			if (event.ctrlKey && event.which === 13) {
				// ctrl + enter
				form.submit();
				return;
			} else if (event.which === 38 || (event.which === 13 && event.shiftKey)) {
				// arrow up or shift + enter
				nextInput = inputs.eq(
					currentIndex > 0 ? currentIndex - 1 : currentIndex
				);
			} else if (event.which === 40 || event.which === 13) {
				// arrow down or enter
				nextInput = inputs.eq(currentIndex + 1);
			}

			if (nextInput && nextInput.length) {
				event.preventDefault();
				nextInput.focus();
			}
		});
	});
};

function setGridLastRequest(grid, lastRequest) {
	grid.setGridParam({
		lastRequest
	})
}

function getGridLastRequest(grid) {
	return grid.getGridParam()?.lastRequest
}

function abortGridLastRequest(grid) {
	getGridLastRequest(grid)?.abort()
}

function clearGridData(grid) {
	grid.jqGrid('setGridParam', {
		datatype: 'local',
		data: []
	}).trigger('reloadGrid')
}

function setErrorMessages(form, errors) {
	$.each(errors, (index, error) => {
		let indexes = index.split(".");
		let element;

		if (indexes.length > 1) {
			element = form.find(`[name="${indexes[0]}[]"]`)[indexes[1]];
		} else {
			element = form.find(`[name="${indexes[0]}"]`)[0];
		}
		
		if ($(element).length > 0) {
			$(element).addClass("is-invalid");

			let errorElement = `<div class="invalid-feedback d-block">${error.toLowerCase()}</div>`;

			if ($(element).hasClass("select2-hidden-accessible")) {
				$(element).next(".select2-container").after(errorElement);
			} else {
				$(element).after(errorElement);
			}
		}
	});
 
	$(".is-invalid").first().focus();
}

function setCustomBindKeysLazy(grid) {
	$(grid).off("keydown");

	setSidebarBindKeys();

	// var ns = 'lazyBind_' + grid.replace(/[^a-zA-Z0-9]/g, '');
	$(document).off('keydown.lazyGrid').on('keydown.lazyGrid', function (e) {

		var isFromInput = $(e.target).is("input, textarea, select");
		var isPageKey = [33, 34, 35, 36].includes(e.keyCode);

		// Kalau dari input dan bukan page key → skip (biarkan toolbarBindKeys handle)
		if (sidebarIsOpen || (isFromInput && !isPageKey)) return;

		var $grid = $(grid);

		if (!activeGrid || activeGrid[0] !== $grid[0]) return;

		var allowedKeys = [33, 34, 35, 36, 38, 40, 13];
		if (!allowedKeys.includes(e.keyCode)) return;

		var loader = $grid.data('lazyLoader') ||
			(typeof lazyLoader !== 'undefined' ? lazyLoader : null);

		if (!loader) {
			return;
		}

		e.preventDefault();

		var postData = $grid.jqGrid("getGridParam", "postData");
		var rowsPerPage = loader.rowsPerPage;
		var currentPage = loader.currentViewPage;
		var totalPages = loader.totalPages;

		// $grid di-resolve fresh di atas — getDataIDs() pasti akurat
		var gridIds = $grid.getDataIDs();
		var selectedRow = $grid.getGridParam("selrow");
		var currentIndex = gridIds.indexOf(selectedRow);

		if (loader.loading) {
			return;
		}

		var focusRow = function (rowType) {
			setTimeout(function () {
				// Resolve fresh lagi di dalam setTimeout
				var $g = $(grid);
				var newIds = $g.getDataIDs();
				if (!newIds.length) return;

				var bDiv = $g.closest(".ui-jqgrid-bdiv");
				var targetId;

				if (rowType === 'first') {
					targetId = newIds[0];
					bDiv.scrollTop(0);
				} else if (rowType === 'last') {
					targetId = newIds[newIds.length - 1];
					bDiv.scrollTop(bDiv[0].scrollHeight);
				}

				if (targetId) {
					$g.resetSelection().setSelection(targetId);
				}
			}, 150);
		};

		var singleRowHeight = $grid.find('tr.jqgrow').first().height() || 30;
		var bDiv = $grid.closest(".ui-jqgrid-bdiv");
		var visibleRows = Math.floor(bDiv.height() / singleRowHeight) || 10;
		var safeIndex = Math.max(0, currentIndex);
		var firstDomAbsIndex = (currentPage - 1) * rowsPerPage;
		var currentAbsIndex = firstDomAbsIndex + safeIndex;
		var lastDomAbsIndex = firstDomAbsIndex + gridIds.length - 1;
		var totRec = parseInt($grid.getGridParam("records")) || (totalPages * rowsPerPage);

		// Page Up (33)
		if (e.keyCode === 33) {
			var targetAbsIndex = currentAbsIndex - visibleRows + 1;
			if (targetAbsIndex < 0) targetAbsIndex = 0;

			if (targetAbsIndex >= firstDomAbsIndex && targetAbsIndex <= lastDomAbsIndex) {
				var targetDomIdx = targetAbsIndex - firstDomAbsIndex;
				var targetId = gridIds[targetDomIdx];
				$grid.resetSelection().setSelection(targetId);
				if (typeof scrollGridSelectionIntoView !== "undefined") scrollGridSelectionIntoView($grid, targetId);
			} else if (currentPage > 1) {
				var targetPageLoad = Math.floor(targetAbsIndex / rowsPerPage) + 1;
				loader.loadGridData(postData, targetPageLoad, rowsPerPage, 'up', 'jump', function () {
					var newIds = $grid.getDataIDs();
					if (newIds.length > 0) {
						var targetDomIdx = targetAbsIndex - ((targetPageLoad - 1) * rowsPerPage);
						if (targetDomIdx < 0) targetDomIdx = 0;
						if (targetDomIdx >= newIds.length) targetDomIdx = newIds.length - 1;
						var targetId = newIds[targetDomIdx];
						$grid.resetSelection().setSelection(targetId);
						if (typeof scrollGridSelectionIntoView !== "undefined") scrollGridSelectionIntoView($grid, targetId);
					}
				});
			}
			$grid.triggerHandler("jqGridKeyUp");
		}

		// Page Down (34)
		if (e.keyCode === 34) {
			var targetAbsIndex = currentAbsIndex + visibleRows - 1;
			if (targetAbsIndex >= totRec) targetAbsIndex = totRec - 1;

			if (targetAbsIndex >= firstDomAbsIndex && targetAbsIndex <= lastDomAbsIndex) {
				var targetDomIdx = targetAbsIndex - firstDomAbsIndex;
				var targetId = gridIds[targetDomIdx];
				$grid.resetSelection().setSelection(targetId);
				if (typeof scrollGridSelectionIntoView !== "undefined") scrollGridSelectionIntoView($grid, targetId);
			} else if (currentPage < totalPages) {
				var targetPageLoad = Math.floor(targetAbsIndex / rowsPerPage) + 1;
				loader.loadGridData(postData, targetPageLoad, rowsPerPage, 'down', 'jump', function () {
					var newIds = $grid.getDataIDs();
					if (newIds.length > 0) {
						var targetDomIdx = targetAbsIndex - ((targetPageLoad - 1) * rowsPerPage);
						if (targetDomIdx < 0) targetDomIdx = 0;
						if (targetDomIdx >= newIds.length) targetDomIdx = newIds.length - 1;
						var targetId = newIds[targetDomIdx];
						$grid.resetSelection().setSelection(targetId);
						if (typeof scrollGridSelectionIntoView !== "undefined") scrollGridSelectionIntoView($grid, targetId);
					}
				});
			}
			$grid.triggerHandler("jqGridKeyUp");
		}

		// End (35)
		if (e.keyCode === 35) {
			if (currentPage !== totalPages) {
				loader.loadGridData(postData, totalPages, rowsPerPage, 'down', 'jump', function () { focusRow('last'); });
			} else {
				focusRow('last');
			}
			$grid.triggerHandler("jqGridKeyUp");
		}

		// Home (36)
		if (e.keyCode === 36) {
			if (currentPage > 1) {
				loader.loadGridData(postData, 1, rowsPerPage, 'up', 'jump', function () { focusRow('first'); });
			} else {
				focusRow('first');
			}
			$grid.triggerHandler("jqGridKeyUp");
		}

		// Arrow Up (38)
		if (e.keyCode === 38) {
			if (currentIndex - 1 >= 0) {
				$grid.resetSelection().setSelection(gridIds[currentIndex - 1]);
				if (typeof scrollGridSelectionIntoView !== "undefined") {
					scrollGridSelectionIntoView($grid, gridIds[currentIndex - 1]);
				}
			} else if (currentPage > 1) {
				loader.loadGridData(postData, currentPage - 1, rowsPerPage, 'up', 'jump');
			}
		}

		// Arrow Down (40)
		if (e.keyCode === 40) {
			if (currentIndex + 1 < gridIds.length) {
				$grid.resetSelection().setSelection(gridIds[currentIndex + 1]);
				if (typeof scrollGridSelectionIntoView !== "undefined") {
					scrollGridSelectionIntoView($grid, gridIds[currentIndex + 1]);
				}
			} else if (currentPage < totalPages) {
				loader.loadGridData(postData, currentPage + 1, rowsPerPage, 'down', 'jump');
			}
		}

		// Enter (13)
		if (e.keyCode === 13) {
			var rowId = $grid.getGridParam("selrow");
			var handler = $grid.jqGrid("getGridParam", "ondblClickRow");
			if (handler) handler.call($grid[0], rowId);
		}
	});
}

function setDetailGridBindKeys(grid) {
	$(document).off('keydown.detailGrid').on('keydown.detailGrid', function (e) {
		// Kalau activeGrid masih master, jangan proses
		if (activeGrid && activeGrid[0] === $(masterGrid)[0]) return;

		var isFromInput = $(e.target).is("input, textarea, select");
		if (isFromInput) return;

		var $grid = $(grid);
		var gridIds = $grid.getDataIDs();
		var selectedRow = $grid.getGridParam("selrow");
		var currentIndex = gridIds.indexOf(selectedRow);

		// Arrow Up
		if (e.keyCode === 38) {
			e.preventDefault();
			if (currentIndex > 0) {
				$grid.resetSelection().setSelection(gridIds[currentIndex - 1]);
				scrollGridSelectionIntoView($grid, gridIds[currentIndex - 1]);
			}
		}

		// Arrow Down
		if (e.keyCode === 40) {
			e.preventDefault();
			if (currentIndex < gridIds.length - 1) {
				$grid.resetSelection().setSelection(gridIds[currentIndex + 1]);
				scrollGridSelectionIntoView($grid, gridIds[currentIndex + 1]);
			}
		}

		// Page Up
		if (e.keyCode === 33) {
			e.preventDefault();
			let currentPage = $grid.getGridParam("page");
			if (currentPage > 1) {
				$grid.setGridParam({ page: currentPage - 1 }).trigger("reloadGrid");
			}
		}

		// Page Down
		if (e.keyCode === 34) {
			e.preventDefault();
			let currentPage = $grid.getGridParam("page");
			let lastPage = $grid.getGridParam("lastpage");
			if (currentPage < lastPage) {
				$grid.setGridParam({ page: currentPage + 1 }).trigger("reloadGrid");
			}
		}

		// Home
		if (e.keyCode === 36) {
			e.preventDefault();
			$grid.setGridParam({ page: 1 }).trigger("reloadGrid");
		}

		// End
		if (e.keyCode === 35) {
			e.preventDefault();
			let lastPage = $grid.getGridParam("lastpage");
			$grid.setGridParam({ page: lastPage }).trigger("reloadGrid");
		}
	});
}

/**
 * Set Home, End, PgUp, PgDn
 * to move grid page
 */
let topSelected = 0;
let bottomSelected = 12;
function setCustomBindKeys(grid) {
	if ($(grid).data('lazyLoader')) {
		setCustomBindKeysLazy(grid);
		return;
	}

	setSidebarBindKeys();

	$(document).off("keydown").on("keydown", function (e) {

		var isFromInput = $(e.target).is("input, textarea, select");
		var isPageKey = [33, 34, 35, 36].includes(e.keyCode);
	
		// Kalau dari input dan bukan page key → skip (biarkan toolbarBindKeys handle)
		if (sidebarIsOpen || (isFromInput && !isPageKey)) return;
		
		if (!sidebarIsOpen && activeGrid) {
			if ($(activeGrid).data('lazyLoader')) return;
			if (
				e.keyCode == 33 ||
				e.keyCode == 34 ||
				e.keyCode == 35 ||
				e.keyCode == 36 ||
				e.keyCode == 38 ||
				e.keyCode == 40 ||
				e.keyCode == 13
			) {
				e.preventDefault();

				var gridIds = $(activeGrid).getDataIDs();
				var selectedRow = $(activeGrid).getGridParam("selrow");
				var currentPage = $(activeGrid).getGridParam("page");
				var lastPage = $(activeGrid).getGridParam("lastpage");
				var currentIndex = 0;
				var row = $(activeGrid).jqGrid("getGridParam", "postData").rows;

				for (var i = 0; i < gridIds.length; i++) {
					if (gridIds[i] == selectedRow) currentIndex = i;
				}

				if (triggerClick == false) {
					if (33 === e.keyCode) {
						if (currentPage > 1) {
							$(activeGrid)
								.jqGrid("setGridParam", {
									page: parseInt(currentPage) - 1,
								})
								.trigger("reloadGrid");

							triggerClick = true;
						}
						$(activeGrid).triggerHandler("jqGridKeyUp"),
							e.preventDefault();
					}
					if (34 === e.keyCode) {
						if (currentPage !== lastPage) {
							$(activeGrid)
								.jqGrid("setGridParam", {
									page: parseInt(currentPage) + 1,
								})
								.trigger("reloadGrid");

							triggerClick = true;
						}
						$(activeGrid).triggerHandler("jqGridKeyUp"),
							e.preventDefault();
					}
					if (35 === e.keyCode) {
						if (currentPage !== lastPage) {
							$(activeGrid)
								.jqGrid("setGridParam", {
									page: lastPage,
								})
								.trigger("reloadGrid");
							if (e.ctrlKey) {
								if (
									$(activeGrid).jqGrid(
										"getGridParam",
										"selrow"
									) !==
									$("#customer")
										.find(">tbody>tr.jqgrow")
										.filter(":last")
										.attr("id")
								) {
									$(activeGrid)
										.jqGrid(
											"setSelection",
											$(activeGrid)
												.find(">tbody>tr.jqgrow")
												.filter(":last")
												.attr("id")
										)
										.trigger("reloadGrid");
								}
							}

							triggerClick = true;
						}
						if (e.ctrlKey) {
							if (
								$(activeGrid).jqGrid(
									"getGridParam",
									"selrow"
								) !==
								$("#customer")
									.find(">tbody>tr.jqgrow")
									.filter(":last")
									.attr("id")
							) {
								$(activeGrid)
									.jqGrid(
										"setSelection",
										$(activeGrid)
											.find(">tbody>tr.jqgrow")
											.filter(":last")
											.attr("id")
									)
									.trigger("reloadGrid");
							}
						}
						$(activeGrid).triggerHandler("jqGridKeyUp"),
							e.preventDefault();
					}
					if (36 === e.keyCode) {
						if (currentPage > 1) {
							if (e.ctrlKey) {
								if (
									$(activeGrid).jqGrid(
										"getGridParam",
										"selrow"
									) !==
									$("#customer")
										.find(">tbody>tr.jqgrow")
										.filter(":first")
										.attr("id")
								) {
									$(activeGrid).jqGrid(
										"setSelection",
										$(activeGrid)
											.find(">tbody>tr.jqgrow")
											.filter(":first")
											.attr("id")
									);
								}
							}
							$(activeGrid)
								.jqGrid("setGridParam", {
									page: 1,
								})
								.trigger("reloadGrid");

							triggerClick = true;
						}
						$(activeGrid).triggerHandler("jqGridKeyUp"),
							e.preventDefault();
					}
					if (38 === e.keyCode) {
						if (currentIndex - 1 >= 0) {
							$(activeGrid)
								.resetSelection()
								.setSelection(gridIds[currentIndex - 1]);

							if (typeof scrollGridSelectionIntoView !== "undefined") {
								scrollGridSelectionIntoView(activeGrid, gridIds[currentIndex - 1]);
							}
						}
					}
					if (40 === e.keyCode) {
						if (currentIndex + 1 < gridIds.length) {
							$(activeGrid)
								.resetSelection()
								.setSelection(gridIds[currentIndex + 1]);

							if (typeof scrollGridSelectionIntoView !== "undefined") {
								scrollGridSelectionIntoView(activeGrid, gridIds[currentIndex + 1]);
							}
						}
					}
					if (13 === e.keyCode) {
						let rowId = $(activeGrid).getGridParam("selrow");
						let ondblClickRowHandler = $(activeGrid).jqGrid(
							"getGridParam",
							"ondblClickRow"
						);

						if (ondblClickRowHandler) {
							ondblClickRowHandler.call($(activeGrid)[0], rowId);
						}
					}
				}
			}
		}
	});
}


function setSidebarBindKeys() {
	$(document).on("keydown", (event) => {
		if (event.keyCode === 77 && event.altKey) {
			event.preventDefault();

			$("#sidebarButton").click();
		}

		if (sidebarIsOpen) {
			let allowedKeyCodes = [37, 38, 39, 40];

			if (allowedKeyCodes.includes(event.keyCode)) {
				event.preventDefault();

				$("#search").val("");

				if ($(".nav-link.active, .nav-link.hover").length <= 0) {
					$(".main-sidebar nav .nav-link").first().addClass("hover");
				}

				switch (event.keyCode) {
					case 37:
						setUpOneLevelMenu();

						break;
					case 38:
						setPreviousMenuHover();

						break;
					case 39:
						setDownOneLevelMenu();

						break;
					case 40:
						setNextMenuHover();

						break;
					default:
						break;
				}
			} else if (event.keyCode === 13) {
				let hoveredElement = $(".nav-link.hover");

				if (hoveredElement.length > 0) {
					if (hoveredElement.siblings("ul").length > 0) {
						setDownOneLevelMenu();
					} else {
						hoveredElement[0].click();
					}
				}
			}
		}
	});
}

function setNextMenuHover() {
	let currentElement = $(".nav-link.hover").first();

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.selected-link");
	}

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.active");
	}

	let nextElement = currentElement
		.parent(".nav-item")
		.next()
		.find(".nav-link")
		.first();

	if (nextElement.length > 0) {
		currentElement.removeClass("selected-link hover");
		nextElement.addClass("hover");
	}
}

function setPreviousMenuHover() {
	let currentElement = $(".nav-link.hover").first();

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.selected-link");
	}

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.active");
	}

	let nextElement = currentElement
		.parent(".nav-item")
		.prev()
		.find(".nav-link")
		.first();

	if (nextElement.length > 0) {
		currentElement.removeClass("selected-link hover");
		nextElement.addClass("hover");
	}
}

function setUpOneLevelMenu() {
	let currentElement = $(".nav-link.hover").first();

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.selected-link");
	}

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.active");
	}

	let upOneLevelElement = currentElement.parents().eq(2);

	if (upOneLevelElement.length > 0) {
		currentElement.removeClass("selected-link hover");
		upOneLevelElement.removeClass("menu-is-opening menu-open");
		upOneLevelElement.find(".nav-link").first().addClass("hover");
	}
}

function setDownOneLevelMenu() {
	let currentElement = $(".nav-link.hover").first();

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.selected-link");
	}

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.active");
	}

	let downOneLevelElement = currentElement
		.siblings("ul")
		.css({
			display: "",
		})
		.find(".nav-link")
		.first();

	if (downOneLevelElement.length > 0) {
		currentElement.removeClass("selected-link hover");
		currentElement.parent(".nav-item").addClass("menu-open");
		downOneLevelElement.addClass("hover");
	}
}

function fillSearchMenuInput() {
	let currentElement = $(".nav-link.hover").first();

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.selected-link");
	}

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.active");
	}

	$("#search").val(currentElement.attr("id"));
}

function detectDeviceType() {
	const ua = navigator.userAgent;
	if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
		return "tablet";
	} else if (
		/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(
			ua
		)
	) {
		return "mobile";
	}
	return "desktop";
}

/**
 * FUNGSI PINTASAN KEYBOARD (ACCESSIBILITY)
 * Menangani kombinasi ALT+A, ALT+E, ALT+D dengan proteksi Hak Akses
 */
// function setupKeyboardShortcuts() {
// 	$(window).off('keydown.globalShortcut').on('keydown.globalShortcut', function (e) {


// 		// Pastikan pengguna tidak sedang mengetik di dalam input/textarea/select
// 		// agar ALT+A tidak terpicu saat mereka sedang mengisi form!
// 		let isInputActive = $(e.target).is('input, textarea, select');
// 		if (isInputActive) return;

// 		// Kombinasi ALT + A (ADD)
// 		if (e.altKey && e.key.toLowerCase() === 'a') {
// 			e.preventDefault(); // Cegah browser melakukan aksi default

// 			// Cek permission dari object accessRights global Anda
// 			if (typeof accessRights !== 'undefined' && accessRights.add) {
// 				console.log('Shortcut Terpicu: ALT + A (Add)');
// 				$('#add').click(); // Simulasikan klik tombol Add
// 			} else {
// 				if (typeof showDialog === "function") showDialog('error', 'Anda tidak memiliki akses untuk menambah data.');
// 			}
// 		}

// 		// Kombinasi ALT + E (EDIT)
// 		if (e.altKey && e.key.toLowerCase() === 'e') {
// 			e.preventDefault();

// 			if (typeof accessRights !== 'undefined' && accessRights.edit) {
// 				console.log('Shortcut Terpicu: ALT + E (Edit)');
// 				$('#edit').click(); // Simulasikan klik tombol Edit
// 			} else {
// 				if (typeof showDialog === "function") showDialog('error', 'Anda tidak memiliki akses untuk mengubah data.');
// 			}
// 		}

// 		// Kombinasi ALT + D (DELETE)
// 		if (e.altKey && e.key.toLowerCase() === 'd') {
// 			e.preventDefault();

// 			if (typeof accessRights !== 'undefined' && accessRights.delete) {
// 				console.log('Shortcut Terpicu: ALT + D (Delete)');
// 				$('#delete').click(); // Simulasikan klik tombol Delete
// 			} else {
// 				if (typeof showDialog === "function") showDialog('error', 'Anda tidak memiliki akses untuk menghapus data.');
// 			}
// 		}
// 	});
// }

function setupKeyboardShortcuts() {

	const shortcutMap = {};
	$('[data-shortcut]').each(function () {
		const key = $(this).data('shortcut').toLowerCase();
		const right = $(this).data('right'); // opsional: 'add' | 'edit' | 'delete'
		shortcutMap[key] = {
			btn: $(this),
			right: right || null
		};
	});

	$(window).off('keydown.globalShortcut').on('keydown.globalShortcut', function (e) {

		if ($(e.target).is('input, textarea, select')) return;
		if (!e.altKey) return;

		const key = e.key.toLowerCase();
		const entry = shortcutMap[key];
		if (!entry) return;

		e.preventDefault();

		// Cek permission jika ada
		if (entry.right && typeof accessRights !== 'undefined') {
			if (!accessRights[entry.right]) {
				if (typeof showDialog === 'function') {
					showDialog('error', 'Anda tidak memiliki akses untuk aksi ini.');
				}
				return;
			}
		}

		entry.btn.click();
	});

	// $(document).on('keydown.focusSwitch', function (e) {
	// 	// TAB untuk pindah fokus dari master ke detail yang aktif
	// 	if (e.keyCode === 9 && !e.shiftKey) {
	// 		let activeTabIndex = $("#tabs").tabs('option', 'active');
	// 		let activeTabId = $("#tabs .ui-tabs-panel").eq(activeTabIndex).attr('id');

	// 		if (activeGrid && activeGrid[0] === $(masterGrid)[0]) {
	// 			// Pindah dari master ke detail
	// 			e.preventDefault();
	// 			if (activeTabId === 'role-tab') {
	// 				activeGrid = $('#userRoleGrid');
	// 				let selrow = activeGrid.getGridParam('selrow') || activeGrid.getDataIDs()[0];
	// 				if (selrow) activeGrid.setSelection(selrow);
	// 			} else if (activeTabId === 'acl-tab') {
	// 				activeGrid = $('#userAclGrid');
	// 				let selrow = activeGrid.getGridParam('selrow') || activeGrid.getDataIDs()[0];
	// 				if (selrow) activeGrid.setSelection(selrow);
	// 			}
	// 		} else {
	// 			// Pindah balik dari detail ke master
	// 			e.preventDefault();
	// 			activeGrid = $(masterGrid);
	// 			let selrow = activeGrid.getGridParam('selrow') || activeGrid.getDataIDs()[0];
	// 			if (selrow) {
	// 				activeGrid.setSelection(selrow);
	// 				$(`${masterGrid} tr[id="${selrow}"]`).focus();
	// 			}
	// 		}
	// 	}
	// });
}


// Helper Function untuk sinkronisasi filter toolbar
function syncActiveFilterWithSelectedRow(grid, rowId) {
	let gridEl = $(grid);
	let activeEl = $(document.activeElement);

	// Cek apakah kursor benar-benar sedang berada di dalam baris pencarian (filter toolbar)
	if (activeEl.length && activeEl.closest('.ui-search-toolbar').length) {

		currentElement = $(".nav-link.active");
	}

	let upOneLevelElement = currentElement.parents().eq(2);

	if (upOneLevelElement.length > 0) {
		currentElement.removeClass("selected-link hover");
		upOneLevelElement.removeClass("menu-is-opening menu-open");
		upOneLevelElement.find(".nav-link").first().addClass("hover");
	}
}

function setDownOneLevelMenu() {
	let currentElement = $(".nav-link.hover").first();

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.selected-link");
	}

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.active");
	}

	let downOneLevelElement = currentElement
		.siblings("ul")
		.css({
			display: "",
		})
		.find(".nav-link")
		.first();

	if (downOneLevelElement.length > 0) {
		currentElement.removeClass("selected-link hover");
		currentElement.parent(".nav-item").addClass("menu-open");
		downOneLevelElement.addClass("hover");
	}
}

function fillSearchMenuInput() {
	let currentElement = $(".nav-link.hover").first();

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.selected-link");
	}

	if (currentElement.length <= 0) {
		currentElement = $(".nav-link.active");
	}

	$("#search").val(currentElement.attr("id"));
}

function detectDeviceType() {
	const ua = navigator.userAgent;
	if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
		return "tablet";
	} else if (
		/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(
			ua
		)
	) {
		return "mobile";
	}
	return "desktop";
}

/**
 * FUNGSI PINTASAN KEYBOARD (ACCESSIBILITY)
 * Menangani kombinasi ALT+A, ALT+E, ALT+D dengan proteksi Hak Akses
 */
// function setupKeyboardShortcuts() {
// 	$(window).off('keydown.globalShortcut').on('keydown.globalShortcut', function (e) {


// 		// Pastikan pengguna tidak sedang mengetik di dalam input/textarea/select
// 		// agar ALT+A tidak terpicu saat mereka sedang mengisi form!
// 		let isInputActive = $(e.target).is('input, textarea, select');
// 		if (isInputActive) return;

// 		// Kombinasi ALT + A (ADD)
// 		if (e.altKey && e.key.toLowerCase() === 'a') {
// 			e.preventDefault(); // Cegah browser melakukan aksi default

// 			// Cek permission dari object accessRights global Anda
// 			if (typeof accessRights !== 'undefined' && accessRights.add) {
// 				console.log('Shortcut Terpicu: ALT + A (Add)');
// 				$('#add').click(); // Simulasikan klik tombol Add
// 			} else {
// 				if (typeof showDialog === "function") showDialog('error', 'Anda tidak memiliki akses untuk menambah data.');
// 			}
// 		}

// 		// Kombinasi ALT + E (EDIT)
// 		if (e.altKey && e.key.toLowerCase() === 'e') {
// 			e.preventDefault();

// 			if (typeof accessRights !== 'undefined' && accessRights.edit) {
// 				console.log('Shortcut Terpicu: ALT + E (Edit)');
// 				$('#edit').click(); // Simulasikan klik tombol Edit
// 			} else {
// 				if (typeof showDialog === "function") showDialog('error', 'Anda tidak memiliki akses untuk mengubah data.');
// 			}
// 		}

// 		// Kombinasi ALT + D (DELETE)
// 		if (e.altKey && e.key.toLowerCase() === 'd') {
// 			e.preventDefault();

// 			if (typeof accessRights !== 'undefined' && accessRights.delete) {
// 				console.log('Shortcut Terpicu: ALT + D (Delete)');
// 				$('#delete').click(); // Simulasikan klik tombol Delete
// 			} else {
// 				if (typeof showDialog === "function") showDialog('error', 'Anda tidak memiliki akses untuk menghapus data.');
// 			}
// 		}
// 	});
// }

function setupKeyboardShortcuts() {

	const shortcutMap = {};
	$('[data-shortcut]').each(function () {
		const key = $(this).data('shortcut').toLowerCase();
		const right = $(this).data('right'); // opsional: 'add' | 'edit' | 'delete'
		shortcutMap[key] = {
			btn: $(this),
			right: right || null
		};
	});

	$(window).off('keydown.globalShortcut').on('keydown.globalShortcut', function (e) {

		if ($(e.target).is('input, textarea, select')) return;
		if (!e.altKey) return;

		const key = e.key.toLowerCase();
		const entry = shortcutMap[key];
		if (!entry) return;

		e.preventDefault();

		// Cek permission jika ada
		if (entry.right && typeof accessRights !== 'undefined') {
			if (!accessRights[entry.right]) {
				if (typeof showDialog === 'function') {
					showDialog('error', 'Anda tidak memiliki akses untuk aksi ini.');
				}
				return;
			}
		}

		entry.btn.click();
	});

	// $(document).on('keydown.focusSwitch', function (e) {
	// 	// TAB untuk pindah fokus dari master ke detail yang aktif
	// 	if (e.keyCode === 9 && !e.shiftKey) {
	// 		let activeTabIndex = $("#tabs").tabs('option', 'active');
	// 		let activeTabId = $("#tabs .ui-tabs-panel").eq(activeTabIndex).attr('id');

	// 		if (activeGrid && activeGrid[0] === $(masterGrid)[0]) {
	// 			// Pindah dari master ke detail
	// 			e.preventDefault();
	// 			if (activeTabId === 'role-tab') {
	// 				activeGrid = $('#userRoleGrid');
	// 				let selrow = activeGrid.getGridParam('selrow') || activeGrid.getDataIDs()[0];
	// 				if (selrow) activeGrid.setSelection(selrow);
	// 			} else if (activeTabId === 'acl-tab') {
	// 				activeGrid = $('#userAclGrid');
	// 				let selrow = activeGrid.getGridParam('selrow') || activeGrid.getDataIDs()[0];
	// 				if (selrow) activeGrid.setSelection(selrow);
	// 			}
	// 		} else {
	// 			// Pindah balik dari detail ke master
	// 			e.preventDefault();
	// 			activeGrid = $(masterGrid);
	// 			let selrow = activeGrid.getGridParam('selrow') || activeGrid.getDataIDs()[0];
	// 			if (selrow) {
	// 				activeGrid.setSelection(selrow);
	// 				$(`${masterGrid} tr[id="${selrow}"]`).focus();
	// 			}
	// 		}
	// 	}
	// });
}


// Helper Function untuk sinkronisasi filter toolbar
function syncActiveFilterWithSelectedRow(grid, rowId) {
	let gridEl = $(grid);
	let activeEl = $(document.activeElement);

	// Cek apakah kursor benar-benar sedang berada di dalam baris pencarian (filter toolbar)
	if (activeEl.length && activeEl.closest('.ui-search-toolbar').length) {

		// Ambil nama kolom dari atribut 'name' milik input pencarian tersebut
		let colName = activeEl.attr('name');

		if (colName) {
			let rowData = gridEl.jqGrid('getRowData', rowId);
			let rawContent = rowData[colName] || '';

			// Bersihkan dari sisa tag HTML (seperti span/badge)
			let cleanText = $('<div>').html(rawContent).text().trim();

			// Timpa teks HANYA pada kotak input yang sedang aktif ini saja
			activeEl.val(cleanText);
		}
	}
}

// Replace icon jqgrid bootstrap
function replaceJqgridBootstrapIcon() {
    setInterval(function() {
        if ($('.fa-caret-up').length || $('.fa-caret-down').length) {
            $('.fa-caret-up').removeClass('fa-caret-up').addClass('fa-arrow-up');
            $('.fa-caret-down').removeClass('fa-caret-down').addClass('fa-arrow-down');
        }
    }, 100);
}
