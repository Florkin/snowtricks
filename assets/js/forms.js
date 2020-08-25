import "../css/forms.scss";

// Make category selector the better
import "select2/dist/js/select2.min"

$(document).ready(function() {
    $(".category-selector").select2({
        theme: "bootstrap4"
    });
});