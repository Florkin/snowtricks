let element = $(".js-sticky-header")

function stick(element) {
    element.css({"position": "fixed", "top": 0, "z-index": 5000, "width": "100%"});

    $(window).on("scroll", function () {
        if ($(window).scrollTop() > 5) {
            element.addClass("is-sticked")
            $(".hide-sticky").hide(200);
        } else {
            element.removeClass("is-sticked")
            $(".hide-sticky").show(200);
        }
    })
}


stick(element)
// $(".js-header-offset").css({"margin-top": element.outerHeight() + "px"})





