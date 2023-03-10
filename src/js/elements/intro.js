// import Swiper bundle with all modules installed
import Swiper from 'swiper/bundle';

// import styles bundle
import 'swiper/css/bundle';

// Intro Slider
const introSlider = new Swiper('.intro-slider .swiper-container', {
    slidesPerView: 1,
    effect: 'fade',
    autoplay: {
        delay: 5000,
    },
    fadeEffect: {
        crossFade: true
    },
    centeredSlides: true,
    loop: true,
    navigation: {
        nextEl: '.intro-slider .swiper-button-next',
        prevEl: '.intro-slider .swiper-button-prev',
    },
    pagination: {
        el: '.intro-slider .swiper-pagination',
        clickable: true
    },
});

