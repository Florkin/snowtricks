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

/**
 * Dropzone Images
 */
if ($(".file-dropzone").length) {
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
            let myDropzone = this;

            // get already uploaded images
            let imagesPaths = [];
            let fileNames = [];
            $("#trick_pictures").find("input").each(function () {
                imagesPaths.push("uploads/images/tricks/" + $(this).attr("value"));
            })
            for (let key in imagesPaths) {
                let mockFile = {index: key, name: key, size: 200};
                myDropzone.displayExistingFile(mockFile, "/" + imagesPaths[key]);
            }

            $('ul.pictures').data('index', $('ul.pictures').find('input').length + $('#trick_pictures').find('input').length);
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
            if (file.uniqfilename) {
                deleteFile(file.uniqfilename);
            }
            file.previewElement.remove();
        }
    });
}


/**
 * Category selector
 */
$(document).ready(function () {
    $(".category-selector").select2({
        theme: "bootstrap4",
        width: "100%"
    });
});

/**
 * Handle videos CollectionType form
 */

function deleteButton() {
    $(".js-delete-video").on("click", function () {
        $(this).closest(".trick-video-field").remove();
    })
}

// add-collection-widget.js
$(document).ready(function () {
    $('.js-add-video-btn').click(function (e) {
        var list = $($(this).attr('data-list-selector'));
        // Try to find the counter of the list or use the length of the list
        var counter = list.data('widget-counter') || list.children().length;

        // grab the prototype template
        var newWidget = list.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);
        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);

        // create a new list element and add it to the list
        var newElem = $(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);

        deleteButton()
    });

    deleteButton()
});

$("input[type=file]").change(function () {
    let fieldVal = $(this).val();
    fieldVal = fieldVal.replace("C:\\fakepath\\", "");

    if (fieldVal != undefined || fieldVal != "") {
        $(this).next(".custom-file-label").html(fieldVal);
    }

});
