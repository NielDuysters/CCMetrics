// File containing generic Javascript functions

// Executed when page is loaded
$(document).ready(function() {
    // Checking all opunits in top-tab and mark the active opunit
    $("#dashboard-options ul li").each(function() {
        set_active_tt(this);
    });

    // Checking all pages in left-tab and mark the active page
    $("#dashboard-options ul li").each(function() {
        set_active_lt(this);
    });


    // Custom select boxes
    $(".select").each(function() {
        let length = $(this).width();
        $(this).find(".options").width(length);
    });
    $(".select").click(function() {
        if ($(this).hasClass("disabled")) {
            return;
        }

        $(this).find(".options").slideToggle()
        $(this).find(".selected").css("background-color", "#F2F2F2");

        let arrow = $(this).find(".selected").find(".arrow");
        if (arrow.hasClass("arrow-down")) {
            $(arrow).removeClass("arrow-down");
            $(arrow).addClass("arrow-up");
        } else {
            $(arrow).removeClass("arrow-up");
            $(arrow).addClass("arrow-down");
        }
    });
    $(".select .option").click(function() {
        $(this).parent().parent().find(".selected").find("span").html($(this).html());
        $(this).parent().parent().find(".value").val($(this).attr('data-value')).trigger("change");
        $(this).parent().parent().find(".name").fadeIn();
        $(this).parent().parent().find(".selected").addClass("after");
        $(this).parent().parent().find(".selected").find(".arrow").addClass("colored");
    });

});

function set_option_update_effects(e) {
    $(e).find(".option").click(function() {
        $(this).parent().parent().find(".selected").find("span").html($(this).html());
        $(this).parent().parent().find(".value").val($(this).attr('data-value')).trigger("change");
        $(this).parent().parent().find(".name").fadeIn();
        $(this).parent().parent().find(".selected").addClass("after");
        $(this).parent().parent().find(".selected").find(".arrow").addClass("colored");
    });
}


// Function to get current page (organisation/metrics/...)
function get_page() {
    var sPath = window.location.pathname;
    var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

    return sPage;
}

// Function to get active op unit
function get_op_unit() {
    let url_params = new URLSearchParams(window.location.search);
    let opunit = url_params.get('opunit');

    return opunit;
}

// Function to set active opunit in top tab
function set_active_tt(el) {
    let opunit_id = get_op_unit();
    if (opunit_id != undefined) {
        if ($(el).find("a").attr('id') == opunit_id) {
            $(el).find("a").addClass("active");
        }
    }
}

// Function to set active page in left tab
function set_active_lt(el) {
    let tabname = $(el).find(".outer .bg .inner a").html();
    if (tabname != undefined) {
        tabname = tabname.toLowerCase().replace(/\s/g, '');
    }


    if (tabname + ".php" == get_page()) {
        $(el).addClass("active");
    }
}
