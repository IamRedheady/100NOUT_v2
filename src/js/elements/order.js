import IMask from "imask";
const order = document.querySelector(".ordering");

if (order) {
  const inputDelivery = document.querySelectorAll(
    "input[name='orderDelivery']",
  );

  const paymentInputsChange = document.querySelectorAll(
    "input[name='orderPayment']",
  );

  let inputDeliveryChecked = 0;

  const deliveryExpress = document.querySelector(".js-express");
  const deliveryAddressInput = document.querySelector(".js-address-input");
  const deliveryAddressResultContainer =
    document.querySelector(".js-address-result");
  const deliveryInputName = document.querySelector(".js-input-name");
  const deliveryInputEmail = document.querySelector(".js-input-email");
  const deliveryInputTel = document.querySelector(".js-input-tel");
  const deliveryAddressContainer = document.querySelector(
    ".js-address-container",
  );
  const deliveryMapContainer = document.querySelector(".js-map-container");
  const deliveryInputLast = document.querySelectorAll(".js-last-val");

  // Подстановка значений в строку адреса
  const url =
    "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";
  const token = "aec17f27346c017b9ae336b0be9bc49462649666";

  let city = [];
  let street = [];
  let houses = [];

  let answers = NaN;

  const clearResults = (selector) => {
    document.querySelectorAll(selector).forEach((item) => {
      item.remove();
    });
  };

  const clearAddress = () => {
    const billingAddress = document.querySelector(
      "input[name='billing_address_1']",
    );
    const addressValues = document.querySelectorAll("input[name='address']");
    addressValues.forEach((input) => {
      input.value = "";
    });
    billingAddress.value = "";
  };

  const clearComment = () => {
    const billingComment = document.querySelector(
      "textarea[name='order_comments']",
    );
    billingComment.value = "";
  };

  const loadAnswers = (e) => {
    const input = e.target;
    // Очистка
    clearResults(".js-address-sugg");

    deliveryAddressResultContainer.classList.remove("active");

    if (input.value !== "") {
      // По минску
      let query = input.value;
      if (inputDeliveryChecked === 0) {
        query = "Минск " + input.value;
      } else if (inputDelivery > 1) {
        query = input.value;
      }

      const options = {
        method: "POST",
        mode: "cors",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          Authorization: "Token " + token,
        },
        body: JSON.stringify({
          query: query,
          locations: { country: "Беларусь" },
        }),
      };

      fetch(url, options)
        .then((response) => response.json())
        .then((result) => (answers = result))
        .catch((error) => console.log("error", error));

      if (answers) {
        answers.suggestions.forEach((sug) => {
          let streetVal = sug.data.street;
          let houseVal = sug.data.house;
          let city = "";

          if (inputDeliveryChecked !== 0) {
            city = sug.data.city + ", ";
          }

          if (streetVal !== null && houseVal !== null) {
            deliveryAddressResultContainer.classList.add("active");
            deliveryAddressResultContainer.insertAdjacentHTML(
              "afterbegin",
              `
                            <button class="btn btn-secondary js-address-sugg text">${city}ул. ${streetVal} д. ${houseVal}</button>
                            `,
            );

            document.querySelectorAll(".js-address-sugg").forEach((btn) => {
              btn.addEventListener("click", () => {
                input.value = btn.innerText;
                clearResults(".js-address-sugg");
                deliveryAddressResultContainer.classList.remove("active");
              });
            });
          }
        });
      }
    }
  };

  const errorCheck = (element) => {
    const parentElement = element.offsetParent;
    if (
      parentElement.classList.contains("required") &&
      element.classList.contains("error")
    ) {
      element.classList.remove("error");
      element.nextElementSibling.remove();
    }
  };

  //  Адрес
  deliveryAddressInput.addEventListener("keyup", (e) => {
    errorCheck(deliveryAddressInput);
    loadAnswers(e);
  });

  const onCheckInputAddress = () => {
    let deliveryAddressPlaceholder = "Город, улица, номер дома";

    if (inputDeliveryChecked === 0) {
      deliveryAddressPlaceholder = "Улица, номер дома";
    }

    deliveryAddressInput.setAttribute(
      "placeholder",
      deliveryAddressPlaceholder,
    );
  };

  const checkAddress = () => {
    if (deliveryAddressInput.value.length < 4) {
      if (inputDeliveryChecked !== 1) {
        let warning = "Заполните адрес доставки для оформления заказа";

        if (deliveryAddressInput.value.length > 0) {
          warning = "Введите корректный адрес";
        }

        if (!deliveryAddressInput.classList.contains("error")) {
          deliveryAddressInput.offsetParent.insertAdjacentHTML(
            "beforeend",
            `<span class='text text-sm'>${warning}</span>`,
          );
        }
        deliveryAddressInput.classList.add("error");

        return false;
      }
    } else {
      return true;
    }
  };

  // Express по Минску

  // const checkExpress = () => {
  const inputExpress = deliveryExpress.children[0];
  inputExpress.addEventListener("change", () => {
    clearAddress();
    clearComment();
    if (inputExpress.checked) {
      inputDeliveryChecked = 4;
      const formDeliveryInput = document.querySelectorAll(
        "input.shipping_method",
      )[inputDeliveryChecked];
      if (formDeliveryInput) {
        formDeliveryInput.click();
      }
    } else {
      inputDeliveryChecked = 0;
      const formDeliveryInput = document.querySelectorAll(
        "input.shipping_method",
      )[inputDeliveryChecked];
      if (formDeliveryInput) {
        formDeliveryInput.click();
      }
    }
  });
  // }

  // Имя
  deliveryInputName.addEventListener("keyup", () => {
    errorCheck(deliveryInputName);
  });

  const checkName = () => {
    if (deliveryInputName.value.length === 0) {
      let warning = "Заполните поле";

      if (!deliveryInputName.classList.contains("error")) {
        deliveryInputName.offsetParent.insertAdjacentHTML(
          "beforeend",
          `<span class='text text-sm'>${warning}</span>`,
        );
      }

      deliveryInputName.classList.add("error");

      return false;
    } else {
      return true;
    }
  };

  // Email
  deliveryInputEmail.addEventListener("keyup", () => {
    errorCheck(deliveryInputEmail);
  });

  const checkEmail = () => {
    const validRegex =
      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (!deliveryInputEmail.value.match(validRegex)) {
      let warning = "Неверный Email";

      if (!deliveryInputEmail.classList.contains("error")) {
        deliveryInputEmail.offsetParent.insertAdjacentHTML(
          "beforeend",
          `<span class='text text-sm'>${warning}</span>`,
        );
      }

      deliveryInputEmail.classList.add("error");

      return false;
    } else {
      return true;
    }
  };

  // Tel
  deliveryInputTel.addEventListener("keyup", () => {
    errorCheck(deliveryInputTel);
    console.log(deliveryInputTel.value[6]);
  });

  const maskOptions = {
    mask: "+{375} (00) 000-00-00",
  };
  const mask = IMask(deliveryInputTel, maskOptions);

  const checkTel = () => {
    const checkS = Number(
      deliveryInputTel.value[6] + deliveryInputTel.value[7],
    );
    console.log(checkS);
    if (
      deliveryInputTel.value.length < 19 ||
      (checkS !== 29 && checkS !== 33 && checkS !== 44 && checkS !== 25)
    ) {
      let warning =
        "Номер телефона должен быть в формате +37529XXXXXXX, +37533XXXXXXX, +37544XXXXXXX, +37525XXXXXXX";

      if (!deliveryInputTel.classList.contains("error")) {
        deliveryInputTel.offsetParent.insertAdjacentHTML(
          "beforeend",
          `<span class='text text-sm'>${warning}</span>`,
        );
      }

      deliveryInputTel.classList.add("error");

      return false;
    } else {
      return true;
    }
  };

  // Смена методов доставки
  inputDelivery.forEach((input, index) => {
    input.addEventListener("change", () => {
      if (input.checked) {
        console.log(input);
        if (input.value === "0") {
          deliveryExpress.classList.remove("disable");
        } else {
          deliveryExpress.classList.add("disable");
          inputExpress.checked = false;
        }

        if (input.value === "1") {
          console.log(input.value);
          deliveryMapContainer.classList.remove("hide");
          deliveryAddressContainer.classList.add("hide");
          paymentInputsChange.forEach((paymentInput) => {
            console.log(paymentInput);
            paymentInput.parentNode.parentNode.classList.remove(
              "ordering__input-wrapper--hide",
            );
          });
        } else {
          deliveryMapContainer.classList.add("hide");
          deliveryAddressContainer.classList.remove("hide");
          //Скрытие методов оплаты
          paymentInputsChange.forEach((paymentInput) => {
            console.log(paymentInput.value);
            if (paymentInput.value == "1" || paymentInput.value == "2") {
              console.log(paymentInput);
              paymentInput.parentNode.parentNode.classList.add(
                "ordering__input-wrapper--hide",
              );
            }
          });
        }

        if (input.value === "3") {
          deliveryAddressContainer.classList.add("last");
          deliveryInputLast.forEach((el, index) => {
            if (index === 1) {
              el.children[0].setAttribute("placeholder", "Индекс");
            }
            el.classList.add("required");
          });
        } else {
          deliveryAddressContainer.classList.remove("last");
          deliveryInputLast.forEach((el, index) => {
            el.classList.remove("required");
            if (index === 1) {
              el.children[0].setAttribute("placeholder", "Комментарий курьеру");
            }
          });
        }

        inputDeliveryChecked = Number(input.value);
        const formDeliveryInput = document.querySelectorAll(
          "input.shipping_method",
        )[inputDeliveryChecked];
        clearAddress();
        if (formDeliveryInput) {
          formDeliveryInput.click();
        }
        onCheckInputAddress();
      }
    });
  });

  inputDelivery[0].click();

  const paymentInputsHidden = document.querySelectorAll(
    ".js-company-container",
  );

  function collectInputData(element) {
    let inputs = element.querySelectorAll("input, textarea");
    let data = "";

    inputs.forEach((input) => {
      if (
        (input.type === "radio" || input.type === "checkbox") &&
        !input.checked
      ) {
        return;
      }

      let placeholder = input.getAttribute("placeholder");
      let value = input.value;

      if (placeholder && value) {
        data += `\n${placeholder} - ${value};\n`;
      }
    });

    return data;
  }

  paymentInputsChange.forEach((input, index) => {
    input.addEventListener("change", () => {
      console.log(input);
      if (input.checked) {
        if (input.value == "1" || input.value == "2") {
          console.log("Рассрочка выбрана");
          inputDelivery.forEach((input) => {
            if (input.value == "1") {
              input.click(); // Устанавливаем checked для инпута с value="1"
            } else {
              input.parentNode.classList.add("ordering__label--hide");
            }
          });
        } else {
          inputDelivery.forEach((input) => {
            input.parentNode.classList.remove("ordering__label--hide");
          });
        }
        if (paymentInputsHidden) {
          paymentInputsHidden.forEach((hiddenContent) => {
            hiddenContent.classList.add("hide");
            const paymentInputsChange =
              hiddenContent.querySelectorAll(".required input");
            paymentInputsChange.forEach((input) => {
              console.log(input);
              input.removeAttribute("required"); // Удаляем атрибут required
            });
          });
          let hideContent = input.parentElement.parentElement.querySelector(
            ".js-company-container",
          );
          if (hideContent) {
            hideContent.classList.remove("hide");
            const paymentInputsChange =
              hideContent.querySelectorAll(".required input");
            paymentInputsChange.forEach((input) => {
              input.setAttribute("required", ""); // Удаляем атрибут required
            });
            //console.log(collectInputData(hideContent));
          }
        }
      }
    });
  });

  const smScroll = () => {
    const scrollTarget = document.querySelector('input[name="orderContacts"]');
    const topOffset = 200;
    const elementPosition = scrollTarget.getBoundingClientRect().top;
    const offsetPosition = elementPosition - topOffset;

    window.scrollBy({
      top: offsetPosition,
      behavior: "smooth",
    });
  };

  const submitOrder = () => {
    const getAddressValues = () => {
      const addressValues = document.querySelectorAll("input[name='address']");

      const address = Array.from(addressValues)
        .filter((item) => {
          if (
            item.value !== "" &&
            item.getAttribute("placeholder") !== "Комментарий курьеру"
          ) {
            return item;
          }
        })
        .map((item) => {
          if (item.getAttribute("placeholder") === "Улица, номер дома") {
            return "Минск" + " " + item.value;
          } else if (
            item.getAttribute("placeholder") === "Город, улица, номер дома"
          ) {
            return item.value;
          } else {
            return item.getAttribute("placeholder") + " " + item.value;
          }
        })
        .join(", ");

      return address;
    };

    const getContactsValues = () => {
      const contactsValues = document.querySelectorAll(
        "input[name='orderContacts']",
      );

      const contacts = Array.from(contactsValues)
        .filter((item) => {
          if (item.value !== "") {
            return item;
          }
        })
        .map((item) => {
          return {
            name: item.getAttribute("placeholder"),
            value: item.value,
          };
        });

      return contacts;
    };

    const loadAddress = () => {
      const billingAddress = document.querySelector(
        "input[name='billing_address_1']",
      );
      billingAddress.value = getAddressValues();
    };

    const loadContacts = () => {
      const name = getContactsValues().find((item) => item.name === "Имя");
      const surname = getContactsValues().find(
        (item) => item.name === "Фамилия",
      );
      const tel = getContactsValues().find(
        (item) => item.name === "Номер телефона",
      );
      const email = getContactsValues().find((item) => item.name === "E-mail");

      const billingName = document.querySelector(
        "input[name='billing_first_name']",
      );
      const billingSurname = document.querySelector(
        "input[name='billing_last_name']",
      );
      const billingPhone = document.querySelector(
        "input[name='billing_phone']",
      );
      const billingEmail = document.querySelector(
        "input[name='billing_email']",
      );

      billingName.value = name.value;
      if (surname) {
        billingSurname.value = surname.value;
      }
      billingEmail.value = email.value;
      billingPhone.value = tel.value;
    };

    const loadComment = () => {
      const comment = document.querySelector(
        "input[placeholder='Комментарий курьеру']",
      );
      const billingComment = document.querySelector(
        "textarea[name='order_comments']",
      );

      let currentVal = "";

      if (comment.value !== "") {
        currentVal += `Комментарий курьеру:
                ${comment.value};`;
      }

      const paymentInputs = document.querySelectorAll(
        "input[name='orderPayment']",
      );

      paymentInputs.forEach((input) => {
        if (input.checked) {
          if (input.value === "0") {
            currentVal += `
Метод оплаты – Наличными;
                        `;
          }
          if (input.value === "1") {
            currentVal += `
Метод оплаты – Рассрочка;
                        `;
          }
          if (input.value === "2") {
            currentVal += `
Метод оплаты – Безналичный расчет;
                        `;
          }
          if (input.value === "3") {
            currentVal += `
Метод оплаты – Безналичный расчет для юридических лиц;
                        `;
            let hideContent = input.parentElement.parentElement.querySelector(
              ".js-company-container",
            );
            if (hideContent) {
              currentVal += collectInputData(hideContent);
            }
          }
        }
      });

      const productSku = document.querySelectorAll(
        ".js-order-cart-item .ordering__cart-sku-num",
      );
      productSku.forEach((sku) => {
        if (sku.parentNode.nextElementSibling) {
          const dropdown =
            sku.parentNode.nextElementSibling.querySelector(
              ".js-dropdown-value",
            );
          currentVal += `
Товар ${sku.innerHTML} – ${dropdown.innerText};`;
        }
      });

      billingComment.value = currentVal;
    };

    const billingSubBtn = document.querySelector(
      "button[name='woocommerce_checkout_place_order']",
    );

    if (
      inputDeliveryChecked === 0 ||
      inputDeliveryChecked === 2 ||
      inputDeliveryChecked === 4
    ) {
      const checkers = [checkAddress(), checkName(), checkEmail(), checkTel()];

      if (!checkers.includes(false)) {
        loadContacts();
        loadAddress();
        loadComment();
        localStorage.clear();
        billingSubBtn.click();
      } else {
        smScroll();
      }
    }

    if (inputDeliveryChecked === 1) {
      const checkers = [checkName(), checkEmail(), checkTel()];

      if (!checkers.includes(false)) {
        loadComment();
        loadContacts();
        localStorage.clear();
        billingSubBtn.click();
      } else {
        smScroll();
      }
    }

    if (inputDeliveryChecked === 3) {
      const checkers = [checkName(), checkEmail(), checkTel(), checkAddress()];

      if (!checkers.includes(false)) {
        loadContacts();
        loadAddress();
        loadComment();
        localStorage.clear();
        billingSubBtn.click();
      } else {
        smScroll();
      }
    }
  };

  // Отправка формы
  const submitBtn = document.querySelector(".js-order-submit");
  submitBtn.addEventListener("click", submitOrder);
}
