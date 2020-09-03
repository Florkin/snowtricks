import "../css/forms.scss";
import "select2/dist/js/select2.min"
import Dropzone from "dropzone"

function ajaxHandleRequest(requestType, url, id) {
    let response = [];

    if (requestType == "getUploadedImages") {
        $.ajax({
            url: url,
            type: "POST",
            async: false,
            success: function (data) {
                response = data;
            }
        })
    }

    if (requestType == "removeImage") {
        $.ajax({
            url: url + "/" + id,
            type: "DELETE",
            async: false,
            success: function (data) {
                response = data;
            }
        })
    }

    return response;
}

// Images dropzone
var _actionToDropZone = $(".file-dropzone").attr('data-upload-url');
var _getUploadedImages = $(".file-dropzone").attr('data-get-images-url');
var _removeImage = $(".file-dropzone").attr('data-remove-images-url');

Dropzone.autoDiscover = false;
let imagesPaths

function detectImgSize(file, src, callback) {
    let image = new Image();
    image.src = src;
    image.onload = function () {
        let result = {x: this.width, y: this.height};
        callback(result);
    };
}

var imgDropzone = new Dropzone(".file-dropzone", {
    url: _actionToDropZone,
    paramName: "file",
    addRemoveLinks: true,
    thumbnailWidth: 250,
    thumbnailHeight: 250,
    thumbnailMethod: "crop",
    maxFiles: 10,
    resizeMimeType: "image/webp",
    init: function () {
        imagesPaths = ajaxHandleRequest("getUploadedImages", _getUploadedImages);
        let myDropzone = this;

        for (var key in imagesPaths) {
            let mockFile = {name: key, size: 200};
            myDropzone.displayExistingFile(mockFile, "/" + imagesPaths[key]);
        }

        let fileCountOnServer = Object.keys(imagesPaths).length; // The number of files already uploaded
        myDropzone.options.maxFiles = myDropzone.options.maxFiles - fileCountOnServer;
    },
    accept: function (file, done) {
        let reader = new FileReader();
        reader.onload = (function (entry) {
            detectImgSize(file, entry.target.result, function (result) {
                if (result.x < 1280 || result.y < 720) {
                    done("Image must be at least 1280 x 720");
                } else {
                    done();
                }
            });
        });

        reader.readAsDataURL(file);
    },
    removedfile: function (file) {
        if (file.status != "error") {
            ajaxHandleRequest("removeImage", _removeImage, file.name)
        }
        file.previewElement.remove();
    }
});

// Category selector
$(document).ready(function () {
    $(".category-selector").select2({
        theme: "bootstrap4"
    });
});