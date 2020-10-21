import "../css/loadmore.scss";

;
import SmoothScroll from "smooth-scroll";

let scroll = new SmoothScroll;

import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

const routes = require('./fos_js_routes.json');
Routing.setRoutingData(routes);
const axios = require('axios').default;

axios.interceptors.request.use(function (config) {
    let loader = document.getElementById("loader");
    loader.classList.remove("d-none");
    return config;
}, function (error) {
    // Do something with request error
    return Promise.reject(error);
});

async function addToHtml(data) {
    if (data.isLast) {
        loadMoreBtn.parentNode.removeChild(loadMoreBtn);
    }
    let container = document.getElementById("js-tricks-container");
    let htmlObj = $.parseHTML(data.html)
    let i = 0
    htmlObj.forEach(function (elem) {
        // Add id to new element to make anchor
        if (elem.nodeType == 1) {
            if (i == 0) {
                elem.id = "page-" + (page - 1)
                i++
            }
            elem.style.opacity = "0";
            elem.classList.add("trick-page-" + (page - 1));
        }

        // append elements to HTML
        container.append(elem);
    })
}

async function loadMoreItems(page, categoryID) {
    axios.get(Routing.generate("ajax.loadmore", {page: page, category_id: categoryID}))
        .then(function (response) {
            addToHtml(response.data).then(function () {
                focusAndAnimate();
            });
        })
        .catch(function (error) {
            console.error(error)
        });
}

// focus to first elem of new page
async function focusAndAnimate() {
    let i = 0;
    let elems = document.getElementsByClassName("trick-page-" + (page - 1));
    scroll.animateScroll(
        document.getElementById("page-" + (page - 1)),
        null,
        {
            speed: 500,
            easing: 'easeOutCubic',
            offset: 100,
            speedAsDuration: true
        }
    );
    elems.forEach(function (e) {
        setTimeout(function () {
            e.style.opacity = "1";
        }, i * 75)

        i++;
    })

    // Loader remove
    let loader = document.getElementById("loader");
    loader.classList.add("d-none");
}

let page = 2;
let loadMoreBtn = document.getElementById("js-load-more-btn");
let categoryID = loadMoreBtn.getAttribute("data-category");

loadMoreBtn.addEventListener("click", function () {
    loadMoreItems(page, categoryID).then(function () {
        page++
    });
})