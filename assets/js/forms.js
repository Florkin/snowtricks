
// Make category selector the right height
$(function () {
    $(".category-selector").attr("size",$(".category-selector option").length + $(".category-selector optgroup").length);
    $(".category-selector").css("overflow", "hidden");
});