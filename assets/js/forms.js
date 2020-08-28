import "../css/forms.scss";
import "select2/dist/js/select2.min"
import Dropzone from "dropzone"

// Images dropzone
var _actionToDropZone = $(".file-dropzone").attr('data-upload-url');

Dropzone.autoDiscover = false;
var myDropzone = new Dropzone(".file-dropzone", {
    url: _actionToDropZone
});

// Category selector
$(document).ready(function () {
    $(".category-selector").select2({
        theme: "bootstrap4"
    });
});