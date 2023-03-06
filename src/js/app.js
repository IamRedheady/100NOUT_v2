import '../scss/app.scss';

/* Your JS Code goes here */

// Elements
import "./elements/dropdown";
import "./elements/intro";
import "./elements/menu";
const changePhotos = (e, productClass) => {
    if (window.innerWidth > 1000) {
        const slides = document.querySelectorAll(`.${productClass} .swiper-slide`)

        const currentArray = slides
        const currentWidth = e.target.offsetWidth
        const currentCursorPosition = e.layerX
        const currentLayers = currentArray.length
        const currentLayerWidth = currentWidth / currentArray.length
        const dots = document.querySelectorAll(`.${productClass} .swiper-pagination-bullet`)

        for (let i = 0; i < currentLayers; i++) {
            if (currentCursorPosition > currentLayerWidth * i && currentCursorPosition < currentLayerWidth * (i + 1)) {
                dots[i].click()
                console.log(i)
            }
        }
    }
}

