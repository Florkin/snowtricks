import "../css/swipers.scss";

import Swiper from 'swiper/swiper-bundle.esm.browser'

var imagesShowSwiperThumbs = new Swiper('.js-trick-show-images-swiper-thumbs', {
    spaceBetween: 10,
    slidesPerView: 6,
    loop: true,
    freeMode: true,
    loopedSlides: 6,
    watchSlidesVisibility: true,
    watchSlidesProgress: true,
});

var imagesShowSwiper = new Swiper('.js-trick-show-images-swiper', {
    speed: 400,
    loop: true,

    // If we need pagination
    pagination: {
        el: '.swiper-pagination',
    },

    // Navigation arrows
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },

    thumbs: {
        swiper: imagesShowSwiperThumbs,
    },
});



