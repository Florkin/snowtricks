import "../css/forms.scss";
import "select2/dist/js/select2.min"
import Dropzone from "dropzone"

function getUploadedImages(url) {
    let response = [];
    $.ajax({
        url: url,
        type: "POST",
        async: false,
        success: function (data) {
            response = data;
        }
    })

    return response;
}

// Images dropzone
var _actionToDropZone = $(".file-dropzone").attr('data-upload-url');
var _getUploadedImages = $(".file-dropzone").attr('data-get-images-url');

Dropzone.autoDiscover = false;
var imgDropzone = new Dropzone(".file-dropzone", {
    url: _actionToDropZone,
    addRemoveLinks: true,
    thumbnailWidth: 250,
    thumbnailHeight: 250,
    thumbnailMethod: "crop",
    init: function () {
        let imagesPaths = getUploadedImages(_getUploadedImages);

        let myDropzone = this;

        for (var key in imagesPaths){
            let mockFile = {name: "thumb-" + key, size: 200 };
            myDropzone.displayExistingFile(mockFile, "/" + imagesPaths[key]);
        }

        // If you use the maxFiles option, make sure you adjust it to the
        // correct amount:
        // let fileCountOnServer = 2; // The number of files already uploaded
        // myDropzone.options.maxFiles = myDropzone.options.maxFiles - fileCountOnServer;
    }
});

// Category selector
$(document).ready(function () {
    $(".category-selector").select2({
        theme: "bootstrap4"
    });
});