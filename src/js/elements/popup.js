const popup = document.querySelectorAll(".js-popup");
const body = document.querySelector("body");
if (popup.length !== 0) {
  const popupCloseBtn = document.querySelectorAll(".js-popup-btn");
  popupCloseBtn.forEach((btn) => {
    btn.addEventListener("click", () => {
      console.log(btn.getAttribute("data-popup"));
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
    const timeWarningsText = document.querySelectorAll(
      ".popup__time-warning-hidden",
    );
    // Добавляем класс show к каждому из найденных элементов
    timeWarningsText.forEach(function (element) {
      element.value = "Удобное время для звонка: ";
    });
  }
}

const popupTg = document.querySelector(".js-popup-tg");
const popupTgContainer = popupTg.querySelector(".popup__container");
if (popupTg) {
  popupTg.addEventListener("click", function (event) {
    // Проверяем, произошел ли клик на контейнере
    const isClickInside =
      popupTgContainer.contains(event.target) || popupTg === event.target;

    // Если клик не на js-popup и не на popup__container, удаляем класс active
    if (isClickInside) {
      popupTg.classList.add("quit");
      body.classList.remove("scroll-disabled");
      setTimeout(() => {
        popupTg.classList.remove("quit");
        popupTg.classList.remove("active");
      }, 500);
    }
  });
}
// Функция для проверки наличия куки
function checkCookie() {
  const cookieName = "popupTg"; // Имя куки, которую мы проверяем
  const cookies = document.cookie.split("; ").reduce((acc, cookie) => {
    const [name, value] = cookie.split("=");
    acc[name] = value;
    return acc;
  }, {});

  return cookies[cookieName] !== undefined;
}

// Функция для установки куки
function setCookie(name, value, days) {
  const expires = new Date(Date.now() + days * 864e5).toUTCString();
  document.cookie =
    name +
    "=" +
    encodeURIComponent(value) +
    "; expires=" +
    expires +
    "; path=/";
}

// Функция для показа модального окна
function showModal() {
  body.classList.add("scroll-disabled");
  popupTg.classList.add("active");
  setCookie("popupTg", "true", 1);
}

setTimeout(function () {
  if (!checkCookie()) {
    showModal();
  }
}, 30000);
