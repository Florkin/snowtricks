let element = $(".js-sticky-header")

function stick(element) {
    element.css({"position": "fixed", "top": 0, "z-index": 5000, "width": "100%"});

    $(window).on("scroll", function () {
        if ($(window).scrollTop() > 20) {
            element.addClass("is-sticked")
            $(".hide-sticky").hide(300);
        } else {
            element.removeClass("is-sticked")
            $(".hide-sticky").show(300);
        }
    })
}

$(document).ready(function () {
    stick(element)
    // $(".js-header-offset").css({"margin-top": element.outerHeight() + "px"})
})




