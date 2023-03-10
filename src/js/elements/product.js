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

const prpSlider = new Swiper(".js-prp-slider", {
    loop: true,
    spaceBetween: 0,
    slidesPerView: 1,
    watchSlidesProgress: true,
    thumbs: {
        swiper: prpThumbs,
    },
});