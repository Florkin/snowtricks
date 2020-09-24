import "../css/forms.scss";
import "select2/dist/js/select2.min"
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

const routes = require('./fos_js_routes.json');
Routing.setRoutingData(routes);
import Dropzone from "dropzone";

Dropzone.autoDiscover = false;

function detectImgSize(file, src, callback) {
    let image = new Image();
    image.src = src;
    image.onload = function () {
        let result = {x: this.width, y: this.height};
        callback(result);
    };
}

function deleteFile(filename) {
    let deleteUrl = Routing.generate('ajax.picture.delete', {
        filename: filename,
    })
    let response
    $.ajax({
        url: deleteUrl,
        type: "DELETE",
        async: false,
        success: function (data) {
            response = data;
        }
    })

    return response;
}

function addTagForm($collectionHolder, filename) {
    // Get the data-prototype explained earlier
    let prototype = $collectionHolder.data('prototype');

    // get the new index
    let index = $collectionHolder.data('index');

    let newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);

    // Display the form in the page in an li, before the "Add a tag" link li
    $collectionHolder.append(newForm);
    $collectionHolder.find("input:last").attr("value", filename);
    $collectionHolder.data('index', index + 1);
}

let actionToDropZone = Routing.generate("ajax.picture.upload")

let imgDropzone = new Dropzone(".file-dropzone", {
    autoProcessQueue: true,
    parallelUploads: 10,
    url: actionToDropZone,
    paramName: "pictureFiles",
    addRemoveLinks: true,
    thumbnailWidth: 250,
    thumbnailHeight: 250,
    thumbnailMethod: "crop",
    maxFiles: 10,
    resizeMimeType: "image/webp",
    init: function () {
        $('ul.pictures').data('index', $('ul.pictures').find('input').length);
        this.on("success", function (file, filename) {
            file.index = $('ul.pictures').data('index');
            file.uniqfilename = filename;
            addTagForm($('ul.pictures'), filename)
        });
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
        $("#trick_pictures_" + file.index).remove();
        deleteFile(file.uniqfilename);
        file.previewElement.remove();
    }
});


// Category selector
$(document).ready(function () {
    $(".category-selector").select2({
        theme: "bootstrap4"
    });
});