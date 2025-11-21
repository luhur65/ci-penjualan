let sidebarIsOpen = false;

$(document).ready(function () {
	setSidebarBindKeys();

	$(document).on("click", "#sidebar-overlay", () => {
		$(document).trigger("sidebar:toggle");

		sidebarIsOpen = false;
	});

	$("#sidebarButton").click(function () {
		setTimeout(() => {
			$(document).trigger("sidebar:toggle");
		}, 0);

		$(".nav-treeview").each(function (i, el) {
			$(el).removeAttr("style");
		});
	});
});

$(document).on("sidebar:toggle", () => {
	if ($("body").hasClass("sidebar-collapse")) {
		sidebarIsOpen = false;

		$("#search").focusout();
		$("body").removeClass("no-scroll");
		$("body").removeClass("sidebar-open");
	} else if ($("body").hasClass("sidebar-open")) {
		sidebarIsOpen = true;

		$("body").removeClass("sidebar-collapse");
		$("body").addClass("no-scroll");

		if (detectDeviceType() == "desktop") {
			$("#search").focus();
		}
	}
});

$(".sidebars").click(function (e) {
	$("body").addClass("sidebar-open");
	e.preventDefault();
});

$(document).mouseup(function (e) {
	let container = $(".main-sidebar");

	if (!container.is(e.target) && container.has(e.target).length === 0) {
		if ($("body").hasClass("sidebar-open")) {
			$("body").removeClass("sidebar-open");
		}
	}
});

$("#search").on("input", function (e) {
	let code = $(this).val().toUpperCase();
	let element = $(`#${code}`);

	$(".sidebar .hover").removeClass("hover");

	if (code === "") {
		$(".selected").click().removeClass("selected");
	} else {
		if (element.hasClass("selected") || element.hasClass("selected-link")) {
			let prev = $(this).data("val");
			$(`#${prev}`).removeClass("selected").click();
			$(`#${prev}`).removeClass("active selected-link");
		} else {
			// if element has childs
			if (!element.siblings("ul").length) {
				let link = element.addClass("selected-link");

				$(document).on("keypress", function (e) {
					if (e.keyCode == 13) {
						if ($(link).hasClass("selected-link")) {
							$(link)[0].click();
						} else {
							return false;
						}
					}
				});
			} else {
				if (
					element.parent(".nav-item").hasClass("menu-is-opening menu-open") ||
					element.parent(".nav-item").hasClass("menu-open")
				) {
					element.addClass("selected");
				} else {
					element[0].click();
					element.addClass("selected");
				}
			}
		}
	}
});

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

/**
 * Will implement plugin concept
 */
// $.fn.sidebar = function(options) {
//     let defaults = {}

//     let settings = $.extend({}, defaults, options)

//     return this.each(function() {
//     })
// }
