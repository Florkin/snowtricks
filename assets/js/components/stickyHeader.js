let element = $(".js-sticky-header")

function stick(element) {
    element.css({"position": "fixed", "top": 0, "z-index": 1000, "width": "100%"});

    $(window).on("scroll", function () {
        if ($(window).scrollTop() > 50) {
            element.addClass("is-sticked")
        } else {
            element.removeClass("is-sticked")
        }
    })
}

stick(element)

