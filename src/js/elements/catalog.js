const filterBtn = document.querySelectorAll(".js-filter-toggle")
const filterWrapper = document.querySelector(".js-catalog-filter")
const body = document.querySelector("body")

filterBtn.forEach(btn => {
    btn.addEventListener("click", () => {
        filterWrapper.classList.toggle("active")
        body.classList.toggle("scroll-disabled")
    })
})