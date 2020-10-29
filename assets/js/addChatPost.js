import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

const routes = require('./fos_js_routes.json');
Routing.setRoutingData(routes);

$(".js-chat-form").on("submit", function (e) {
    e.preventDefault();
    let formData = {
        'message': $('#chat_message').val(),
        'trick_id': $('#trick_id').val()
    };

    let action = Routing.generate("ajax.chatposts.new");

    // process the form
    $.ajax({
        type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
        url: action, // the url where we want to POST
        data: formData, // our data object
        dataType: 'json',
        encode: true
    })
        // using the done promise callback
        .done(function (data) {
            let htmlObj = $.parseHTML(data.html);
            htmlObj.forEach(function (elem) {
                if (elem.nodeType == 1) {
                    elem.classList.add("opacity-animate");
                    elem.style.display = "none";
                    $('#js-chat-messages-container').prepend(elem)
                    $('.opacity-animate').slideDown();
                    $('#chat_message').val('')
                }
            })
        });
})