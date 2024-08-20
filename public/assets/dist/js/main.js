if (typeof name === "undefined" || typeof state === "undefined") {
    let name;
    let state;
}
function escapeHtml(str) {
    const htmlEntities = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#x27;",
        "/": "&#x2F;",
    };
    try {
        return str.replace(/[&<>"'/]/g, function (match) {
            return htmlEntities[match];
        });
    } catch (e) {
        return str;
    }
}

$("body")
    .find('input[type = "number"]')
    .each(function () {
        $(this).on("input", function () {
            let int = parseInt($(this).val());
            if ($(this).val() === "") {
                $(this).val(int);
            }
        });
    });
function selectEvent($this) {
    const val = $this.val();
    $this.children().each(function () {
        $(this).removeAttr("selected");
        if ($(this).val() == val) {
            $(this).attr("selected", true);
        }
    });
}
$("body")
    .find("select")
    .on("change", function () {
        selectEvent($(this));
    });

function click(id) {
    // $("#" + id).click();
}

function isInt(value) {
    var x;
    if (isNaN(value)) {
        return false;
    }
    x = parseFloat(value);
    return (x | 0) === x;
}

function get_default() {
    $("select")
        .find(".editable")
        .each(function () {
            if ($(this).val() === "") {
                $(this).remove();
            }
        });
}

function mySeries(name = "", id = "", cloneId = "", count = "") {
    var ser_name = $("#series_" + id).val();
    var reg_name = $("#region_" + id).val();
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        url: "../view/instance_ajax.php",
        type: "POST",
        data: {
            reg_name: reg_name,
            ser_name: ser_name,
            count: count,
            name: name,
            cloneId: cloneId,
            id: id,
        },
        success: function (data) {
            $(`#inst_div_${id}`).html(data);
        },
    });
}

// $(document).on("keydown", "form", function (event) {
//     return event.key != "Enter";
// });

function change_mgmt(elem, mng_elem, func) {
    $("#est_div")
        .find(elem)
        .each(function () {
            $(this).on(func, function () {
                if ($(this).val() === "") {
                    $(mng_elem).removeAttr("checked");
                } else if ($(this).val() === "NA") {
                    $(mng_elem).removeAttr("checked");
                } else {
                    $(mng_elem).attr("checked", "true");
                }
            });
        });
}

function validate_input(check_class) {
    $(check_class).on("input", function () {
        if ($(this).prop("checked")) {
            $(this)
                .parent()
                .find('input[type="number"]')
                .attr("required", "true");
            let id = $(this).parent().find("select").prop("id");
            if ($("#" + id + " option").length > 1) {
                if ($("#" + id).val() === "") {
                    $("#" + id).attr("required", "true");
                }
            }
        } else {
            $(this)
                .parent()
                .find('input[type="number"]')
                .val(0)
                .removeAttr("required");
            $(this).parent().find("select").removeAttr("required");
        }
    });
}

$("input[type=number]").on("mousewheel", function (e) {
    $(this).blur();
});

$(".net-drop").on("change", function () {
    if ($(this).val() === "") {
        $(this)
            .parent()
            .parent()
            .find('input[type="number"]')
            .each(function () {
                $(this).removeAttr("required", true);
            });
    } else {
        $(this)
            .parent()
            .parent()
            .find('input[type="number"]')
            .each(function () {
                $(this).attr("required", true);
            });
    }
});

$(".agent-type").on("change", function () {
    // console.log($(this));
    if ($(this).val() === "customized") {
        $(this)
            .parent()
            .find('input[type = "number"]', function () {});
    }
});
function remove_arrow() {
    $(".form-group")
        .find("select")
        .each(function () {
            try {
                if ($(this).find("option").length < 2) {
                    $(this).addClass("remove_arrow ");
                }
            } catch (e) {
                // $(this).addClass("remove_arrow ");
            }
        });
}

$(".remove_arrow").each(function () {
    $(this).click(function (event) {
        event.preventDefault();
    });
});

function add_vm(name, id, list_id) {
    var count_of_virtual_machine =
        parseInt($("#count_of_virtual_machine_" + name).val()) + 1;
    $("#count_of_virtual_machine_" + name).val(count_of_virtual_machine);
    countID = name + "" + count_of_virtual_machine;
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        url: "/Ajax/VirtualMachine",
        data: {
            name: name,
            id: countID,
            list_id: list_id,
        },
        dataType: "TEXT",
        success: function (response) {
            $("#virtual_machine_" + name).append(response);
        },
    });
}

function addBlockStorage(name, id) {
    var count_of_block_storage =
        parseInt($("#count_of_block_storage_" + name).val()) + 1;
    $("#count_of_block_storage_" + name).val(count_of_block_storage);
    countID = name + "" + count_of_block_storage;

    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        url: "/Ajax/BlockStorage",
        data: {
            name: name,
            id: countID,
            type: $("#count_of_block_storage_" + name).val() > 1 ? "Ajax" : "",
        },
        dataType: "TEXT",
        success: function (response) {
            $("#block-storage_" + name).append(response);
        },
    });
}

function add_estmt(list_id) {
    let count_of = 1;
    var count_of_est = parseInt($("#count_of_est").val()) + 1;
    $("#count_of_est").val(count_of_est);

    var count_id = count_of_est + "" + count_of;
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        url: "/Ajax/Estimate",
        data: {
            name: count_of_est,
            id: count_id,
            list_id: list_id,
        },
        dataType: "TEXT",
        success: function (response) {
            $("#myTab").append(response);
        },
    });
}

$(document).ready(function () {
    let width = window.innerWidth;
    if (width <= 767) {
        $(".check").addClass("float-right");
    } else {
        $(".check").removeClass("float-right");
    }

    if (width <= 576) {
        // console.log(width)
        if ($(".nav-item").hasClass("d-none")) {
            // console.log(width)
            $(".nav-item").removeClass("d-none");
        }
    }

    $(window).resize(function () {
        if (isZooming() === "zooming" || isZooming() === "resizing") {
            let width = window.innerWidth;
            if (width <= 767) {
                $(".check").addClass("float-right");
            } else {
                $(".check").removeClass("float-right");
            }

            if (width < 576) {
                if ($(".nav-item").hasClass("d-none")) {
                    // console.log(width);
                    $(".nav-item").removeClass("d-none");
                }
            }
        }
    });
    let px_ratio =
        window.devicePixelRatio ||
        window.screen.availWidth / document.documentElement.clientWidth;
    function isZooming() {
        var newPx_ratio =
            window.devicePixelRatio ||
            window.screen.availWidth / document.documentElement.clientWidth;
        if (newPx_ratio != px_ratio) {
            px_ratio = newPx_ratio;
            return "zooming";
        } else {
            return "resizing";
        }
    }
});

function changeOnInput(lable, input, defaultInput = "") {
    if ($(input).val() === "") {
        $(lable).html(defaultInput);
    } else {
        $(lable).html(escapeHtml($(input).val()));
    }
    $(input).on("input", function () {
        if ($(this).val() != "") {
            $(lable).html(escapeHtml($(this).val()));
        } else {
            $(lable).html(defaultInput);
        }
    });
}

$(document).ready(function () {
    $("#notification-bell").on("click", function (e) {
        e.stopPropagation();
        $("#notification-box").toggleClass("show");
    });
    $(document).on("click", function (e) {
        if (
            !$("#notification-bell").is(e.target) &&
            !$("#notification-box").has(e.target).length
        ) {
            $("#notification-box").removeClass("show");
        }
        if (!$(".aside-bar").is(e.target) && !$("aside").has(e.target).length) {
            $("body").addClass("sidebar-collapse");
        }
    });
    $(window).on("scroll", function () {
        if ($(window).scrollTop() > 0) {
            $("#navbar").removeClass("navbar-light");
        } else {
            $("#navbar").addClass("navbar-light");
        }
    });
});
function mode() {
    const isDarkMode = $("#mode").prop("checked");
    const body = $("body");
    const main = $(".Main");
    const nav = $("nav");
    const aside = $("aside");
    const modeIcon = $("#mode").parent().find("#modeIcon");
    const navFuncs = $(".nav-funcs");
    const stDarkClass = "st_dark";
    const stLightClass = "st_light";
    const darkClass = "dark";
    const lightClass = "light";
    const darkModeStyles = `
        <style class="${stDarkClass}">::placeholder { color: #eee; }</style>
    `;
    const lightModeStyles = `
        <style class="${stLightClass}">::placeholder { color: grey; }</style>
    `;

    if (isDarkMode) {
        nav.removeClass("navbar-light").addClass("navbar-dark");
        aside
            .removeClass("sidebar-light-primary")
            .addClass("sidebar-dark-primary");
        body.add(main).css("background", "rgb(48 64 81)");
        $("div, select, input, label, table tr td, table tr th, p, span")
            .removeClass(lightClass)
            .addClass(darkClass);

        if (body.find(`.${stDarkClass}`).length < 1) {
            body.append(darkModeStyles);
        }

        $("section").removeClass(lightClass).addClass(darkClass);
        $(".except").removeClass(darkClass);
        $(`.${stLightClass}`).remove();
        $(".mytabs").removeClass(darkClass);

        modeIcon.attr("title", "Turn to Light Mode");
        navFuncs.removeClass("text-dark").addClass("text-light");
    } else {
        nav.addClass("navbar-light").removeClass("navbar-dark");
        aside
            .removeClass("sidebar-dark-primary")
            .addClass("sidebar-light-primary");
        body.add(main).css("background", "#f4f6f9");

        $(`.${stDarkClass}`).remove();
        if (body.find(`.${stLightClass}`).length < 1) {
            body.append(lightModeStyles);
        }

        $("div, select, input, label, table tr td, table tr th, span, p")
            .removeClass(darkClass)
            .addClass(lightClass);

        $(".mytabs, .Main").removeClass(lightClass);
        $("section").removeClass(darkClass).addClass(lightClass);
        $(".except").removeClass(lightClass);

        modeIcon.attr("title", "Turn to Dark Mode");
        navFuncs.removeClass("text-light").addClass("text-dark");
    }
}

$("#mode").on("input", function () {
    localStorage.setItem("mode", $(this).prop("checked"));
    mode();
});

$(document).ready(function () {
    if (localStorage.getItem("mode") === "true") {
        $("#mode").attr("checked", "true");
        mode();
    } else {
        // console.log(localStorage.getItem('mode'))
        mode();
    }
});

$(".nav-link").on("click", function () {
    $(".nav-link").removeClass("active");
    $(this).addClass("active");
});

function rgbToHex(rgb) {
    var parts = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    delete parts[0];
    for (var i = 1; i <= 3; ++i) {
        parts[i] = parseInt(parts[i]).toString(16);
        if (parts[i].length === 1) {
            parts[i] = "0" + parts[i];
        }
    }
    return parts.join("").toUpperCase();
}

function Base64Encode($objstr) {
    const utf8Bytes = new TextEncoder().encode($objstr); // Encode to UTF-8
    const base64String = btoa(String.fromCharCode(...utf8Bytes)); // Convert to base64
    return base64String;
}
function cloneVM($this) {
    const $parent = $this.parent().parent();
    // console.log($parent);
    const name = $this.data("name");
    const id = parseInt($this.data("id"));
    var count_of_virtual_machine =
        parseInt($(`#count_of_virtual_machine_${name}`).val()) + 1;

    $(`#count_of_virtual_machine_${name}`).val(count_of_virtual_machine);
    countID = name + "" + count_of_virtual_machine;

    const section = $parent.clone();

    section.find("*").each(function () {
        const regEx = new RegExp(`_${id}`);
        if ($(this).prop("id").match(regEx)) {
            let elemId = $(this)
                .prop("id")
                .replace(regEx, `_${id + 1}`);
            $(this).attr("id", elemId);
        }
        if ($(this).prop("name")) {
            if ($(this).prop("name").match(regEx)) {
                let elemName = $(this)
                    .prop("name")
                    .replace(regEx, `_${id + 1}`);
                $(this).attr("name", elemName);
            }
        }
    });

    section.find("select").attr("onchange", "selectEvent($(this))");

    // console.log(section)
    $(`#virtual_machine_${name}`).append(section);
}

function cloneEst($this) {
    // e.preventDefault();

    const $parent = $this.parent().parent().parent();

    let count_of_est = parseInt($("#count_of_est").val()) + 1;
    $("#count_of_est").val(count_of_est);

    const count_id = `${count_of_est}1`;

    const section = $parent.clone();
    section.attr("id", `est_div_${count_id}`);

    section.find("*").each(function () {
        let elemID = $(this).prop("id");
        let ID = elemID.match(/_\d+$/);
        let newID = ID ? ID[0].split("") : [];
        newID[1] = count_of_est;
        $(this).attr("id", elemID.replace(/_\d+$/, `${newID.join("")}`));
        if ($(this).data("id")) {
            $(this).data("id", count_id);
        }
        if ($(this).data("name")) {
            $(this).data("name", count_of_est);
        }
        if ($(this).prop("for")) {
            let lblFor = $(this).prop("for").split("_");
            let newFor = lblFor[lblFor.length - 1].split("");
            newFor[0] = count_of_est;
            lblFor[lblFor.length - 1] = newFor.join("");
            $(this).attr("for", lblFor.join("_"));
        }
        if ($(this).prop("name")) {
            let elemName = $(this).prop("name").split("");
            elemName[0] = count_of_est;
            $(this).attr("name", elemName.join(""));
        }
        if ($(this).prop("id") == "add-estmt") {
            $(this)
                .attr("id", `rem-estmt_${count_id}`)
                .val("×")
                .addClass("rem-est")
                .attr(
                    "onclick",
                    `$(this).parent().parent().remove(); $('#count_of_est').val(parseInt($('#count_of_est').val()) - 1);`
                );
        }

        if ($(this).attr("aria-controls")) {
            let control = $(this).attr("aria-controls").split("_");
            if (control[control.length - 1].length > 1) {
                let new_cont = control[control.length - 1].split("");
                new_cont[0] = count_of_est;
                control[control.length - 1] = new_cont.join("");
            } else {
                control[control.length - 1] = count_of_est;
            }

            $(this)
                .attr("aria-controls", control.join("_"))
                .attr("href", `#${control.join("_")}`);
        }
    });
    section.find("select").attr("onchange", "selectEvent($(this))");
    section.find(`.head-btn`).attr(
        "onclick",
        `
  $('#estmt_collapse_${count_of_est}1').hasClass('hiddenDiv') ? $('#estmt_collapse_${count_of_est}1').removeClass('hiddenDiv'):$('#estmt_collapse_${count_of_est}1').addClass('hiddenDiv');
`
    );
    $(`#myTab`).append(section);
}

function addLineItemsToDropdownMenu(list_id) {
    $(".dropdown-menu").html("");
    $(".EstmtName").each(function () {
        let $name =
            escapeHtml($(this).val()) == ""
                ? "Your Estimate"
                : escapeHtml($(this).val());
        $(".dropdown-menu").append(
            `<div class="dropdown-item" style = "cursor: pointer;" data-id = "${$(
                this
            ).data("id")}" data-name = "${$(this).data(
                "name"
            )}" onclick = "addProductAjax($(this),${list_id})" >${$name}</div>`
        );
    });
}

function addProductAjax($this, list_id) {
    const id = $this.data("id");
    const name = $this.data("name");
    const $parent = $this.parent().parent().parent();
    if ($parent.data("prod") == "vm") {
        add_vm(name, id, list_id);
        return;
    } else if ($parent.data("prod") === "block_strg_type") {
        addBlockStorage(name, id);
        return;
    }

    const prodAjax = ($prod, $category) => {
        if (
            $(`#est_div_${id} #${$category}_${id} .row #${$prod}_${id}`)
                .length > 0
        ) {
            return Promise.resolve(false);
        }

        if (!$prod || !$category) {
            // console.log([$prod, $category]);
        }

        let req;
        if ($(`#est_div_${id} #${$category}_${id}`).length > 0) {
            req = "prod";
        } else {
            req = "group";
        }
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                url: "/Ajax/ProductAjax",
                type: "POST",
                dataType: "TEXT",
                data: {
                    id: id,
                    prod: $prod,
                    name: name,
                    category: $category,
                    request: req,
                },
                success: function (response) {
                    if (req === "prod") {
                        $(`#est_div_${id}`)
                            .find(`#${$category}_${id} .collapse .main-row`)
                            .append(response);
                    } else {
                        $(`#est_div_${id} .tab`).append(response);
                    }
                    resolve(true);
                },
                error: function (error) {
                    console.error(error);
                    resolve(false); // or reject(error) if you want to handle errors differently
                },
            });
        });
    };

    const processAssociatedProducts = async (res) => {
        await prodAjax($parent.data("prod"), $parent.data("category"));
        if (res.length > 0) {
            let Response = Array.isArray(res[0]) ? res[0] : res;
            for (const key of Object.keys(Response)) {
                let success = await prodAjax(
                    Response[key].default_int,
                    Response[key].primary_category
                );
                if (success) {
                    // Optionally do something if the AJAX call was successful
                } else {
                    // Optionally handle failure
                }
            }
        }
    };

    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        url: "/Ajax/AssociativeProductAjax",
        type: "POST",
        dataType: "TEXT",
        data: {
            prod: $parent.data("prod"),
        },
        success: function (response) {
            const res = JSON.parse(response);
            processAssociatedProducts(res);
        },
        error: function (error) {
            console.error(error);
        },
    });
}

$(".remove").click(function () {
    $(this).parent().parent().remove();
});
function makeValuesUnique(obj) {
    const values = Object.values(obj);
    const counts = {};
    const updatedValues = values.map((value) => {
        if (counts[value]) {
            counts[value]++;
            return `${value} ${counts[value]}`;
        } else {
            counts[value] = 1;
            return value;
        }
    });

    return Object.fromEntries(
        Object.keys(obj).map((key, index) => [key, updatedValues[index]])
    );
}

function showProdsOnCategory($this) {
    let category = $this.prop("id");
    $("#SearchedProducts").attr("hidden", "true");
    $("#search_product").val("");
    $(".product").each(function () {
        if ($(this).data("category") === category) {
            $(this).removeAttr("hidden");
        } else {
            $(this).attr("hidden", "true");
        }
    });
}

var base64Image = `data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIIAAABjCAYAAABXG9jaAAAACXBIWXMAAC4jAAAuIwF4pT92AAAG2WlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDggNzkuMTY0MDM2LCAyMDE5LzA4LzEzLTAxOjA2OjU3ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgMjEuMCAoV2luZG93cykiIHhtcDpDcmVhdGVEYXRlPSIyMDIwLTA5LTI5VDE3OjI3OjQzKzA1OjMwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAyMS0wMi0xN1QxNzo1MzoxOCswNTozMCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAyMS0wMi0xN1QxNzo1MzoxOCswNTozMCIgZGM6Zm9ybWF0PSJpbWFnZS9wbmciIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0ZGFkNzc5YS0wYmM5LTk2NDEtOGE3YS03NDQyMjQwNTBlNmIiIHhtcE1NOkRvY3VtZW50SUQ9ImFkb2JlOmRvY2lkOnBob3Rvc2hvcDo4MzIyOTVmNS1lNDI4LTdkNDctYTAzYS02Mjc4ZTc2NGU5NDgiIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo3MGNkM2I4NC02YzAwLTZiNGQtYWQ4Mi03ZjgxYmY4MmEyMzEiPiA8cGhvdG9zaG9wOlRleHRMYXllcnM+IDxyZGY6QmFnPiA8cmRmOmxpIHBob3Rvc2hvcDpMYXllck5hbWU9IsKuIiBwaG90b3Nob3A6TGF5ZXJUZXh0PSLCriIvPiA8L3JkZjpCYWc+IDwvcGhvdG9zaG9wOlRleHRMYXllcnM+IDx4bXBNTTpIaXN0b3J5PiA8cmRmOlNlcT4gPHJkZjpsaSBzdEV2dDphY3Rpb249ImNyZWF0ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6NzBjZDNiODQtNmMwMC02YjRkLWFkODItN2Y4MWJmODJhMjMxIiBzdEV2dDp3aGVuPSIyMDIwLTA5LTI5VDE3OjI3OjQzKzA1OjMwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgMjEuMCAoV2luZG93cykiLz4gPHJkZjpsaSBzdEV2dDphY3Rpb249ImNvbnZlcnRlZCIgc3RFdnQ6cGFyYW1ldGVycz0iZnJvbSBhcHBsaWNhdGlvbi92bmQuYWRvYmUucGhvdG9zaG9wIHRvIGltYWdlL3BuZyIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6NGRhZDc3OWEtMGJjOS05NjQxLThhN2EtNzQ0MjI0MDUwZTZiIiBzdEV2dDp3aGVuPSIyMDIxLTAyLTE3VDE3OjUzOjE4KzA1OjMwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgMjEuMCAoV2luZG93cykiIHN0RXZ0OmNoYW5nZWQ9Ii8iLz4gPC9yZGY6U2VxPiA8L3htcE1NOkhpc3Rvcnk+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hw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`;

function convertTablesToExcel(tables, type, sheetNames, FileName) {
    sheetNames = makeValuesUnique(sheetNames);

    // console.log(sheetNames);
    const password = "PC";
    let workbook = new ExcelJS.Workbook();
    let p = 1;
    // console.log(tables)

    tables.forEach(function (table, index) {
        let keySheet = "sheet" + p;
        let worksheet = workbook.addWorksheet(sheetNames[keySheet]);
        let orderNo = worksheet.orderNo;
        let startRow = 1;
        let endRow = 100;
        let startCol = 1;
        let endCol = 26; // A to Z corresponds to 26 columns

        // Password Protection
        worksheet.protection = {
            sheet: true,
            password: password,
            allowEdit: false,
        };

        for (let row = startRow; row <= endRow; row++) {
            for (let col = startCol; col <= endCol; col++) {
                let cellAddress = String.fromCharCode(64 + col) + row;
                let cell = worksheet.getCell(cellAddress);
                cell.fill = {
                    type: "pattern",
                    pattern: "solid",
                    fgColor: {
                        argb: "ffffff", // Set the desired color code (e.g., red)
                    },
                };
            }
        }
        const imageId = workbook.addImage({
            base64: base64Image,
            extension: "png",
            tl: {
                col: 1,
                row: 1,
            },
            br: {
                col: 2,
                row: 2,
            },
        });
        worksheet.addImage(imageId, "A2:B5");
        let rows = table.querySelectorAll("tr");
        let rowCount = rows.length;

        let cellMap = new Map();

        for (let i = 0; i < rowCount; i++) {
            let row = rows[i];
            let cells = row.querySelectorAll("td, th");
            let cellCount = cells.length;

            let cellIndex = 0;

            for (let j = 0; j < cellCount; j++) {
                let cell = cells[j];
                let value = cell.innerText;
                let colspan = parseInt(cell.getAttribute("colspan")) || 1;

                // skip Cells
                if (type === "Shareable") {
                    if (cell.classList.contains("unshareable")) {
                        continue;
                    }
                }

                if (cell.classList.contains("noExl")) {
                    continue;
                }

                if (
                    cellMap.has(i + 1) &&
                    cellMap.get(i + 1).has(cellIndex + 1)
                ) {
                    cellIndex++;
                    continue;
                }

                let worksheetCell = worksheet.getCell(i + 1, cellIndex + 1);

                if (!cell.classList.contains("noBorder")) {
                    worksheetCell.border = {
                        top: { style: "thin" },
                        left: { style: "thin" },
                        bottom: { style: "thin" },
                        right: { style: "thin" },
                    };
                }

                worksheetCell.value = value;

                let computedStyle = window.getComputedStyle(cell);
                worksheetCell.font = {
                    bold: computedStyle.fontWeight === "bold",
                    italic: computedStyle.fontStyle === "italic",
                    underline: computedStyle.textDecorationLine === "underline",
                };
                worksheetCell.alignment = {
                    vertical: computedStyle.verticalAlign,
                    horizontal: computedStyle.textAlign,
                };
                //   Preserve Colors
                let bgColor = computedStyle.backgroundColor;
                if (bgColor === "rgb(52, 58, 64)") {
                    bgColor = "rgb(255,255,255)";
                }
                if (
                    bgColor !== "rgba(0, 0, 0, 0)" &&
                    bgColor !== "transparent"
                ) {
                    worksheetCell.fill = {
                        type: "pattern",
                        pattern: "solid",
                        fgColor: { argb: rgbToHex(bgColor) },
                    };
                }

                if (colspan > 1) {
                    let endColumnIndex = cellIndex + colspan - 1;
                    worksheet.mergeCells(
                        i + 1,
                        cellIndex + 1,
                        i + 1,
                        endColumnIndex + 1
                    );
                    cellIndex += colspan;

                    for (let k = cellIndex - colspan + 2; k <= cellIndex; k++) {
                        if (!cellMap.has(i + 1)) {
                            cellMap.set(i + 1, new Set());
                        }
                        cellMap.get(i + 1).add(k);
                    }
                } else {
                    cellIndex++;
                }
            }
        }
        if (sheetNames[keySheet] === "Summary Sheet") {
            worksheet.orderNo = 1;
        } else {
            worksheet.orderNo = orderNo + 1;
        }
        p++;
    });
    workbook.xlsx.writeBuffer().then(function (buffer) {
        let blob = new Blob([buffer], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        });
        saveAs(blob, "ESDS Estimate - " + FileName + ".xlsx");
    });
}

function AJAX(Data, rel = true) {
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        url: "../model/modelRateCard.php",
        type: "POST",
        dataType: "TEXT",
        data: Data,
        success: function (response) {
            console.log(response);
        },
        complete: function () {
            if (rel) {
                window.location.reload();
            }
        },
    });
}

function INR(number) {
    let decimal = (number - Math.floor(number)).toString();
    let money = Math.floor(number);
    let length = money.toString().length;
    let delimiter = "";
    money = money.toString().split("").reverse().join("");
    for (let i = 0; i < length; i++) {
        if ((i === 3 || (i > 3 && (i - 1) % 2 === 0)) && i !== length) {
            delimiter += ",";
        }
        delimiter += money[i];
    }
    let result = delimiter.split("").reverse().join("");
    decimal = decimal.replace(/0\./i, ".");
    decimal = decimal.substring(0, 3);
    if (decimal !== "0") {
        result = result + decimal;
    }

    if (isNaN(number) || result.match(/NaN/)) {
        return;
    }
    return "₹ " + result;
}

function del(id) {
    let action = confirm("Are you Sure wanted to delete this ?");
    if (action) {
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url: "../model/saveToDB.php",
            dataType: "TEXT",
            data: {
                action: "Delete",
                id: id,
            },
            success: function (response) {
                alert("Deleted the Estimate");

                window.location.href = "index.php?all";
            },
        });
    } else {
        window.location.href = "index.php?all";
    }
}
