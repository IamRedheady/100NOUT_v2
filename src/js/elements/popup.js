const popup = document.querySelectorAll(".js-popup");

if (popup.length !== 0) {
  const popupCloseBtn = document.querySelectorAll(".js-popup-btn");
  const body = document.querySelector("body");

  popupCloseBtn.forEach((btn) => {
    btn.addEventListener("click", () => {
      popup.forEach((el) => {
        if (el.getAttribute("data-popup") === btn.getAttribute("data-popup")) {
          if (el.classList.contains("active")) {
            el.classList.add("quit");
            body.classList.remove("scroll-disabled");
            setTimeout(() => {
              el.classList.remove("quit");
              el.classList.remove("active");
            }, 500);
          } else {
            body.classList.add("scroll-disabled");
            el.classList.add("active");
          }
        }
        el.addEventListener("click", (e) => {
          if (e.target.classList.contains("active")) {
            el.classList.add("quit");
            body.classList.remove("scroll-disabled");
            setTimeout(() => {
              el.classList.remove("quit");
              el.classList.remove("active");
            }, 500);
          }
        });
      });
    });
  });

  const minskTime = new Date().toLocaleString("en-US", {
    timeZone: "Europe/Minsk",
  });
  const currentHour = new Date(minskTime).getHours();

  // Проверяем, если текущее время от 21:00 до 09:00
  if (currentHour >= 21 || currentHour < 9) {
    // Ваш код здесь
    const timeWarnings = document.querySelectorAll(".popup__time-warning");
    // Добавляем класс show к каждому из найденных элементов
    timeWarnings.forEach(function (element) {
      element.classList.add("popup__time-warning--show");
    });
  }
}
