const filterBtn = document.querySelectorAll(".js-filter-toggle")
const filterWrapper = document.querySelector(".js-catalog-filter")
const body = document.querySelector("body")

filterBtn.forEach(btn => {
    btn.addEventListener("click", () => {
        filterWrapper.classList.toggle("active")
        body.classList.toggle("scroll-disabled")
    })
})

// import Swiper bundle with all modules installed
import Swiper from 'swiper/bundle';

const preList = new Swiper(".js-swiper-pre-list", {
    spaceBetween: 15,
    mousewheel: true,
    slidesPerView: "auto",
    navigation: {
        nextEl: '.js-swiper-pre-list .swiper-button-next',
        prevEl: '.js-swiper-pre-list .swiper-button-prev',
    },

});
