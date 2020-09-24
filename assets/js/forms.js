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

function addTagForm($collectionHolder, value) {
    // Get the data-prototype explained earlier
    let prototype = $collectionHolder.data('prototype');

    // get the new index
    let index = $collectionHolder.data('index');

    let newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);
    $collectionHolder.data('value', value);

    // Display the form in the page in an li, before the "Add a tag" link li
    $collectionHolder.append(newForm);
    $collectionHolder.find("input:last").attr("value", value);
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
        this.on("success", function(file, response) {
            response.forEach(function (e) {
                let $collectionHolder = $('ul.pictures');
                $collectionHolder.data('index', $collectionHolder.find('input').length);
                addTagForm($collectionHolder, e)
            })
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

    }
});



// Category selector
$(document).ready(function () {
    $(".category-selector").select2({
        theme: "bootstrap4"
    });
});