// import Swiper bundle with all modules installed
import Swiper from 'swiper/bundle';

// import styles bundle
import 'swiper/css/bundle';

if (document.querySelector(".prp")) {
    // Плавная анимация элементов
    let isScrolling = false;

    window.addEventListener("scroll", throttleScroll, false);

    function throttleScroll(e) {
        if (isScrolling == false) {
            window.requestAnimationFrame(function () {
                scrolling(e);
                isScrolling = false;
            });
        }
        isScrolling = true;
    }

    document.addEventListener("DOMContentLoaded", scrolling, false);

    // Элементы
    const prpHeader = document.querySelector(".js-prp-header")
    const prpWatchContainer = document.querySelector(".prp__intro")

    function scrolling(e) {
        if (!isPartiallyVisible(prpWatchContainer)) {
            prpHeader.classList.add("active")
        } else {
            prpHeader.classList.remove("active")
        }
    }

    function isPartiallyVisible(el) {
        var elementBoundary = el.getBoundingClientRect();

        var top = elementBoundary.top;
        var bottom = elementBoundary.bottom;
        var height = elementBoundary.height;

        return ((top + height >= 0) && (height + window.innerHeight >= bottom));
    }

    function isFullyVisible(el) {
        var elementBoundary = el.getBoundingClientRect();

        var top = elementBoundary.top;
        var bottom = elementBoundary.bottom;

        return ((top >= 0) && (bottom <= window.innerHeight));
    }

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
        zoom: {
            maxRatio: 3,
        },
        navigation: {
            nextEl: '.js-prp-slider-big .swiper-button-next',
            prevEl: '.js-prp-slider-big .swiper-button-prev',
        },
        // Responsive breakpoints
        breakpoints: {
            // when window width is >= 320px
            960: {
                zoom: false
            }
        }
    });

    const prpBigSliderToggle = document.querySelectorAll(".js-prp-slider-toggle")
    const prpBigSliderWrap = document.querySelector(".js-prp-big-slider-wrap")
    const body = document.querySelector("body")

    prpBigSliderToggle.forEach(btn => {
        btn.addEventListener("click", () => {
            // if (window.innerWidth > 960) {
            prpBigSliderWrap.classList.toggle("active")
            body.classList.toggle("scroll-disabled")
            // }
        })
    });

    prpBigSliderWrap.addEventListener("click", (e) => {
        console.log(e.target)
        if (e.target.classList.contains("active") || e.target.classList.contains("swiper-slide")) {
            prpBigSliderWrap.classList.toggle("active")
            body.classList.toggle("scroll-disabled")
        }
    })

    const bigSlides = document.querySelectorAll(".js-prp-slider-big .swiper-zoom-container")

    if (window.innerWidth > 960) {
        function initZoom(e) {
            if (e.target.classList.contains("initZoom")) {
                zoomOut(e)
            } else {
                e.target.classList.add("initZoom")
                e.currentTarget.style.backgroundPosition = '50% ' + '50%';
            }
        }

        function zoom(e) {
            if (e.target.classList.contains("initZoom")) {
                var x, y;
                var zoomer = e.currentTarget;

                let offsetX = e.offsetX;

                let offsetY = e.offsetY;

                x = offsetX / zoomer.offsetWidth * 100;
                y = offsetY / zoomer.offsetHeight * 100;
                zoomer.style.backgroundPosition = x + '% ' + y + '%';
            }
        }
        function zoomOut(e) {
            var zoomer = e.currentTarget;
            zoomer.style.backgroundPosition = '10000% -10000%';
            e.target.classList.remove("initZoom")
        }

        bigSlides.forEach(picture => {
            picture.addEventListener("click", (e) => {
                initZoom(e)
            })

            picture.addEventListener("mousemove", (e) => {
                zoom(e)
            })

            picture.addEventListener("mouseleave", (e) => {
                zoomOut(e)
            })
        })
    }
}