let dropdowns = document.querySelectorAll(".js-dropdown")

if (dropdowns) {
    dropdowns.forEach(dropdown => {
        const btn = dropdown.children[0]
        const menu = dropdown.children[1]
        const menuItems = Array.from(menu.children)

        btn.addEventListener("click", () => {
            dropdown.classList.toggle("active")
            console.log(menu)
        })

        menuItems.forEach((item, index) => {
            const listBtn = item.children[0]

            listBtn.addEventListener("click", () => {
                const sortFilters = document.querySelectorAll("[data-filter-type='wpfSortBy'] input")

                if (sortFilters.length !== 0) {
                    sortFilters[index].click()
                }
                btn.children[0].innerHTML = listBtn.innerHTML
                dropdown.classList.toggle("active")
            })
            console.log()
            if (document.querySelectorAll("[data-filter-type='wpfSortBy'] input").length !== 0) {
                if (document.querySelectorAll("[data-filter-type='wpfSortBy'] input")[index].checked) {
                    btn.children[0].innerHTML = listBtn.innerHTML
                }
            }
        })
    })
}