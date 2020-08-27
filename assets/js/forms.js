import "../css/forms.scss";

// Make category selector the better
import "select2/dist/js/select2.min"
import "bootstrap-fileinput/js/fileinput"

$(document).ready(function() {
    $(".category-selector").select2({
        theme: "bootstrap4"
    });

    $(".file-dropzone").fileinput();
});