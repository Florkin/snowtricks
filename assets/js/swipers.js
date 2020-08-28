import "../css/swipers.scss";

import Swiper from 'swiper/swiper-bundle.esm.browser'

var imagesShowSwiper = new Swiper('.js-trick-show-images-swiper', {
    speed: 400,
    loop:true,

    // If we need pagination
    pagination: {
        el: '.swiper-pagination',
    },

    // Navigation arrows
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },

});

