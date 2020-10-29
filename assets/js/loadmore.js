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

async function loadMoreItems(page, loadType, categoryID) {
    let route;
    let container;
    if (loadType === "chatpost") {
        route = Routing.generate("ajax.loadmore.chatposts", {page: page, trick_id: trickID});
        container = document.getElementById("js-chat-messages-container");
    } else if (loadType === "trick") {
        route = Routing.generate("ajax.loadmore.tricks", {page: page, category_id: categoryID});
        container = document.getElementById("js-tricks-container");
    }

    axios.get(route)
        .then(function (response) {
            addToHtml(response.data, container, loadType).then(function () {
                focusAndAnimate(loadType);
            });
        })
        .catch(function (error) {
            console.error(error)
        });
}

async function addToHtml(data, container, loadType) {
    if (data.isLast) {
        loadMoreBtn.remove();
    }
    let htmlObj = $.parseHTML(data.html)
    let i = 0
    htmlObj.forEach(function (elem) {
        // Add id to new element to make anchor
        if (elem.nodeType == 1) {
            if (i == 0) {
                elem.id = loadType + "page-" + (page - 1)
                i++
            }
            elem.style.opacity = "0";
            elem.classList.add(loadType + "-page-" + (page - 1));
        }

        // append elements to HTML
        container.append(elem);
    })
}

// focus to first elem of new page
async function focusAndAnimate(loadType) {
    let i = 0;
    let elems = document.getElementsByClassName(loadType + "-page-" + (page - 1));
    scroll.animateScroll(
        document.getElementById(loadType + "page-" + (page - 1)),
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
let loadMoreBtn = $("#js-load-more-btn");
let categoryID = loadMoreBtn.data("category");
let trickID  = loadMoreBtn.data("trick");

loadMoreBtn.on("click", function () {
    let loadType = $(this).data("load")
    loadMoreItems(page, loadType, categoryID).then(function () {
        page++
    });
})