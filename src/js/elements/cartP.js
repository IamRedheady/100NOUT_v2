const cartP = document.querySelector(".js-cartP")
const cartPBtn = document.querySelectorAll(".js-cartP-toggle")
const body = document.querySelector("body")

cartP.addEventListener("click", (e) => {
    if (e.target.classList.contains("active")) {
        cartP.classList.add("quit")
        setTimeout(() => {
            cartP.classList.remove("quit")
            cartP.classList.remove("active")
            body.classList.remove("scroll-disabled")
        }, 500);
    }
})

cartPBtn.forEach(btn => {
    btn.addEventListener("click", () => {
        cartP.classList.add("quit")
        setTimeout(() => {
            cartP.classList.remove("quit")
            cartP.classList.remove("active")
            body.classList.remove("scroll-disabled")
        }, 500);
    })
})
