import "../css/loadmore.scss";

import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import SmoothScroll from "smooth-scroll";
let scroll = new SmoothScroll;

const routes = require('./fos_js_routes.json');
Routing.setRoutingData(routes);
const axios = require('axios').default;

async function loadMoreItems(page, categoryID) {
    axios.get(Routing.generate("ajax.chatposts.loadmore", {page: page, trick_id: trickID}))
        .then(function (response) {
            addToHtml(response.data).then(function () {
                focusAndAnimate();
            });
        })
        .catch(function (error) {
            console.error(error)
        });
}

async function addToHtml(data) {
    if (data.isLast) {
        loadMoreBtn.parentNode.removeChild(loadMoreBtn);
    }
    let container = document.getElementById("chat-messages-container");
    let htmlObj = $.parseHTML(data.html)
    let i = 0
    htmlObj.forEach(function (elem) {
        // Add id to new element to make anchor
        if (elem.nodeType == 1) {
            if (i == 0) {
                elem.id = "postpage-" + (page - 1)
                i++
            }
            elem.style.opacity = "0";
            elem.classList.add("post-page-" + (page - 1));
        }

        // append elements to HTML
        container.append(elem);
    })
}

// focus to first elem of new page
async function focusAndAnimate() {
    scroll.animateScroll(
        document.getElementById("postpage-" + (page-1)),
        null,
        {
            speed: 500,
            easing: 'easeOutCubic',
            offset: 100,
            speedAsDuration: true
        }
    );
    let i = 0;
    let elems = document.getElementsByClassName("post-page-" + (page-1));
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
let loadMoreBtn = document.getElementById("js-load-more-chatposts");
let trickID  = loadMoreBtn.getAttribute("data-trick");

loadMoreBtn.addEventListener("click", function () {
    loadMoreItems(page, trickID).then(function () {
        page++
    });
})

