const dropdowns = document.querySelectorAll(".js-dropdown")

if (dropdowns) {
    dropdowns.forEach(dropdown => {
        const btn = dropdown.children[0]
        const menu = dropdown.children[1]
        const menuItems = Array.from(menu.children)

        btn.addEventListener("click", () => {
            dropdown.classList.toggle("active")
            console.log(menu)
        })

        menuItems.forEach(item => {
            const listBtn = item.children[0]
            listBtn.addEventListener("click", () => {
                btn.children[0].innerHTML = listBtn.innerHTML
                dropdown.classList.toggle("active")
            })
        })
    })
}