import Swiper from 'swiper/swiper-bundle.esm.browser'

let imagesShowSwiperThumbs = new Swiper('.js-trick-show-images-swiper-thumbs', {
    spaceBetween: 10,
    centeredSlides: true,
    slidesPerView: 5,
    touchRatio: 0.2,
    slideToClickedSlide: true,
    loop: true,
});

let imagesShowSwiper = new Swiper('.js-trick-show-images-swiper', {
    speed: 400,
    loop: true,
    // pagination: {
    //     el: '.swiper-pagination',
    // },

    thumbs: {
        swiper: imagesShowSwiperThumbs,
    },

    // Navigation arrows
    // navigation: {
    //     nextEl: '.swiper-button-next',
    //     prevEl: '.swiper-button-prev',
    // },
});





