import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

const routes = require('./fos_js_routes.json');
Routing.setRoutingData(routes);
const axios = require('axios').default;

async function addToHtml(data) {
    let container = document.getElementById("js-tricks-container");
    let htmlObj = $.parseHTML(data.html)
    let i = 0
    htmlObj.forEach(function (elem) {
        // Add id to new element to make anchor
        if (i == 0) {
            if (elem.nodeType == 1) {
                elem.id = "first-of-page-" + (page-1)
                i++
            }
        }
        // append elements to HTML
        container.append(elem);
    })
}

async function loadMoreItems(page) {
    axios.post(Routing.generate("ajax.loadmore", {page: page}))
        .then(function (response) {
            addToHtml(response.data).then(function () {
                // focus to first elem of new page
                let elem = document.getElementById("first-of-page-" + page);
                location.href = "#first-of-page-" + page;
            });
        })
        .catch(function (error) {
            console.error(error)
        });
}

let page = 2;
let loadMoreBtn = document.getElementById("js-load-more-btn");

loadMoreBtn.addEventListener("click", function () {
    loadMoreItems(page).then(function () {
        page++
    });
})