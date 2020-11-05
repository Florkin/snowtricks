function ajaxFilter(data) {
    $.ajax()
}

$("#difficulty_select_filter").on("select2:select", function () {
    let id_array = [];
    $(this).select2("data").forEach(function (e) {
        id_array.push(e.id);
    });
})

$("#difficulty_select_filter").on("select2:clear", function () {

})