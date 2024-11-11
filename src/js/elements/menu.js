const menu1Items = document.querySelectorAll(".js-menu-level-1");
const menu2Items = document.querySelectorAll(".js-menu-level-2");
const menu1List = document.querySelectorAll(".js-menu-sub-level-1");
const menu2List = document.querySelectorAll(".js-menu-sub-level-2");
const menuSubWrap1 = document.querySelector(".js-menu__sub-wrap-level-1");
const menuSubWrap2 = document.querySelector(".js-menu__sub-wrap-level-2");
const menuBannerBase = document.querySelector(".js-menu-banner");
const mobileMenuBtn = document.querySelectorAll(".js-mobile-menu-btn");
const menuTop = document.querySelector(".js-menu-top");
const mobileTitle1 = document.querySelectorAll(".js-menu-title-1");
const mobileTitle2 = document.querySelectorAll(".js-menu-title-2");
const menuToggle = document.querySelectorAll(".js-menu-toggle");
const menu = document.querySelector(".js-menu");
const page = document.querySelector("body");

if (menu) {
  const closeMenu = () => {
    menu.classList.add("quit");
    page.classList.remove("scroll-disabled");
    setTimeout(() => {
      menu.classList.remove("quit");
      menu.classList.remove("active");

      menuToggle.forEach((btn) => {
        btn.classList.remove("active");
      });
      menu1Items.forEach((item, i) => {
        item.classList.remove("active");
        menu1List[i].classList.remove("active");
      });
      menu2Items.forEach((item, i) => {
        item.classList.remove("active");
        menu2List[i].classList.remove("active");
      });
      menuTop.classList.remove("active");
      if (window.innerWidth < 960) {
        menuSubWrap1.classList.remove("active");
      } else {
        menu1Items[0].classList.add("active");
        menuSubWrap1.classList.add("active");
        menu1List[0].classList.add("active");
      }
      menuBannerBase.classList.remove("long");
      menuSubWrap2.classList.remove("active");
    }, 500);
  };

  menuToggle.forEach((btn) => {
    btn.addEventListener("click", () => {
      if (btn.classList.contains("active")) {
        closeMenu();
      } else {
        btn.classList.add("active");
        menu.classList.add("active");
        page.classList.add("scroll-disabled");
      }
    });
  });

  menu.addEventListener("click", (e) => {
    if (
      e.target.classList.contains("menu") ||
      e.target.classList.contains("menu__inner")
    ) {
      closeMenu();
    }
  });

  if (window.innerWidth > 960) {
    menu1Items[0].classList.add("active");
    menuSubWrap1.classList.add("active");
    menu1List[0].classList.add("active");

    const clearItems = () => {
      menu1Items.forEach((item, i) => {
        item.classList.remove("active");
        menu1List[i].classList.remove("active");
      });
    };

    menu1Items.forEach((item, index) => {
      item.addEventListener("mouseenter", () => {
        clearItems();
        item.classList.add("active");
        menu1List[index].classList.add("active");
        menuSubWrap2.classList.remove("active");
        menuBannerBase.classList.remove("long");
      });
    });

    const clearItems2 = () => {
      menu2Items.forEach((item, i) => {
        item.classList.remove("active");
        menu2List[i].classList.remove("active");
      });
    };

    menu2Items.forEach((item, index) => {
      item.addEventListener("mouseenter", () => {
        const childCount = menu2List[index].children[1].childElementCount;
        if (childCount !== 0) {
          clearItems2();
          item.classList.add("active");
          menu2List[index].classList.add("active");
          menuSubWrap2.classList.add("active");
          menuBannerBase.classList.add("long");
        } else {
          menuSubWrap2.classList.remove("active");
          menuBannerBase.classList.remove("long");
          clearItems2();
        }
      });
    });
  } else {
    menu1Items[0].classList.remove("active");
    menuSubWrap1.classList.remove("active");
    menu1List[0].classList.remove("active");

    menu1Items.forEach((item, index) => {
      item.addEventListener("click", (e) => {
        e.preventDefault();
        menu1List[index].classList.add("active");
        menuSubWrap1.classList.add("active");
      });
    });

    const clearItems = () => {
      menuSubWrap1.classList.add("quit");

      setTimeout(() => {
        menu1Items.forEach((item, i) => {
          item.classList.remove("active");
          menu1List[i].classList.remove("active");
        });

        menuSubWrap1.classList.remove("active");
        menuSubWrap1.classList.remove("quit");
      }, 500);
    };

    mobileTitle1.forEach((btn) => {
      btn.addEventListener("click", clearItems);
    });

    const clearItems2 = () => {
      menuSubWrap2.classList.add("quit");
      setTimeout(() => {
        menu2Items.forEach((item, i) => {
          item.classList.remove("active");
          menu2List[i].classList.remove("active");
        });

        menuSubWrap2.classList.remove("active");
        menuSubWrap2.classList.remove("quit");
      }, 500);
    };

    mobileTitle2.forEach((btn) => {
      btn.addEventListener("click", clearItems2);
    });

    menu2Items.forEach((item, index) => {
      item.addEventListener("click", (e) => {
        const childCount = menu2List[index].children[1].childElementCount;
        if (childCount !== 0) {
          e.preventDefault();
          menu2List[index].classList.add("active");
          menuSubWrap2.classList.add("active");
        }
      });
    });

    mobileMenuBtn.forEach((btn) => {
      btn.addEventListener("click", () => {
        if (menuTop.classList.contains("active")) {
          menuTop.classList.add("quit");
          setTimeout(() => {
            menuTop.classList.remove("active");
            menuTop.classList.remove("quit");
          }, 500);
        } else {
          menuTop.classList.add("active");
        }
      });
    });
  }

  window.addEventListener(
    "resize",
    function (event) {
      if (window.innerWidth > 960) {
        menu1Items[0].classList.add("active");
        menuSubWrap1.classList.add("active");
        menu1List[0].classList.add("active");
      } else {
        menu1Items[0].classList.remove("active");
        menuSubWrap1.classList.remove("active");
        menu1List[0].classList.remove("active");
      }
    },
    true,
  );

  document.addEventListener('DOMContentLoaded', () => {
    jQuery('.mobile-menu-accordion__toggle').on('click', function(event) {
      event.preventDefault();

      $(this).closest('.mobile-menu-accordion')?.find('.mobile-menu-accordion__subnav')?.slideToggle();
    })
  })
}
