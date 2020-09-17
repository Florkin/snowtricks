import "../css/forms.scss";
import "select2/dist/js/select2.min"
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

const routes = require('./fos_js_routes.json');
Routing.setRoutingData(routes);

import Dropzone from "dropzone"

function ajaxHandleRequest(requestType, url, id, data) {
    let response = [];

    if (requestType == "removeImage") {
        $.ajax({
            url: url + "/" + id,
            type: "DELETE",
            async: false,
            success: function (data) {
                response = data;
            }
        })
    } else {
        $.ajax({
            url: url,
            type: "POST",
            async: false,
            data: data,
            success: function (data) {
                response = data;
            }
        })
    }

    return response;
}

let id = $(".file-dropzone").attr('data-trick-id')
let autoProcess = true;
let addRemoveLinks = true;
// If adding new instance, not updating
if (typeof (id) == "undefined") {
    autoProcess = false;
    addRemoveLinks = false;
}

let _actionToDropZone = Routing.generate("ajax.trick.img.upload");
let _addInstance = Routing.generate("ajax.trick.new");
let _getUploadedImages;
let _removeImage;

if (typeof (id) != "undefined") {
    _actionToDropZone = Routing.generate("ajax.trick.img.upload", {id: id});
    _getUploadedImages = Routing.generate("ajax.get.uploaded.images", {id: id});
    _removeImage = Routing.generate("ajax.remove.image", {id: id});
}

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

let imgDropzone = new Dropzone(".file-dropzone", {
    autoProcessQueue: autoProcess,
    parallelUploads: 10,
    url: _actionToDropZone,
    paramName: "pictureFiles",
    addRemoveLinks: addRemoveLinks,
    thumbnailWidth: 250,
    thumbnailHeight: 250,
    thumbnailMethod: "crop",
    maxFiles: 10,
    resizeMimeType: "image/webp",
    init: function () {
        let myDropzone = this;
        // If updating existing instance
        if (typeof (id) != "undefined") {
            imagesPaths = ajaxHandleRequest("getUploadedImages", _getUploadedImages);

            for (let key in imagesPaths) {
                let mockFile = {name: key, size: 200};
                myDropzone.displayExistingFile(mockFile, "/" + imagesPaths[key]);
            }

            let fileCountOnServer = Object.keys(imagesPaths).length; // The number of files already uploaded
            myDropzone.options.maxFiles = myDropzone.options.maxFiles - fileCountOnServer;
            // Else if we are adding new instance, we have to create instance before process image upload
        } else {
            $("form[name='trick']").on("submit", function (e) {
                e.preventDefault();
                let result = ajaxHandleRequest("addNewInstance", _addInstance, null, $(this).serialize());

                if (result.status == "success") {
                    let id = result.id;
                    myDropzone.options.url += "/" + id;
                    myDropzone.processQueue()
                }
                myDropzone.on('queuecomplete', function (file) {
                    window.location.href = Routing.generate("admin.trick.index");
                })
            })
        }
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
        // If updating existing instance
        if (typeof (id) != "undefined") {
            if (file.status != "error") {
                ajaxHandleRequest("removeImage", _removeImage, file.name)
            }
            file.previewElement.remove();
        }
    }
});

// Category selector
$(document).ready(function () {
    $(".category-selector").select2({
        theme: "bootstrap4"
    });
});