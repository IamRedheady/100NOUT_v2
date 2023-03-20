// import Swiper bundle with all modules installed
import Swiper from 'swiper/bundle';

// import styles bundle
import 'swiper/css/bundle';

const prpThumbs = new Swiper(".js-prp-thumbs", {
    direction: "vertical",
    spaceBetween: 14,
    slidesPerView: 5,
    freeMode: true
});

const prpBigThumbs = new Swiper(".js-prp-thumbs-big", {
    spaceBetween: 14,
    slidesPerView: 5,
    freeMode: true
});

const prpSlider = new Swiper(".js-prp-slider", {
    loop: true,
    spaceBetween: 0,
    slidesPerView: 1,
    watchSlidesProgress: true,
    thumbs: {
        swiper: prpThumbs,
    },
    navigation: {
        nextEl: '.js-prp-slider .swiper-button-next',
        prevEl: '.js-prp-slider .swiper-button-prev',
    },

});

const prpSliderBig = new Swiper(".js-prp-slider-big", {
    loop: true,
    spaceBetween: 0,
    slidesPerView: 1,
    watchSlidesProgress: true,
    thumbs: {
        swiper: prpBigThumbs,
    },
    navigation: {
        nextEl: '.js-prp-slider-big .swiper-button-next',
        prevEl: '.js-prp-slider-big .swiper-button-prev',
    },

});

const prpBigSliderToggle = document.querySelectorAll(".js-prp-slider-toggle")
const prpBigSliderWrap = document.querySelector(".js-prp-big-slider-wrap")
const body = document.querySelector("body")

prpBigSliderToggle.forEach(btn => {
    btn.addEventListener("click", () => {
        if (window.innerWidth > 960) {
            prpBigSliderWrap.classList.toggle("active")
            body.classList.toggle("scroll-disabled")
        }
    })
});

prpBigSliderWrap.addEventListener("click", (e) => {
    console.log(e.target)
    if (e.target.classList.contains("active") || e.target.classList.contains("swiper-slide")) {
        prpBigSliderWrap.classList.toggle("active")
        body.classList.toggle("scroll-disabled")
    }
})