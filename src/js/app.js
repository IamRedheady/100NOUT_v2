import '../scss/app.scss';

/* Your JS Code goes here */
import IMask from 'imask'
const obmenTel = document.querySelector("input[placeholder='Номер телефона*']")

const maskOptions = {
    mask: '+{375} (00) 000-00-00'
};
const mask = IMask(obmenTel, maskOptions);


let tg = {
    token: "5360345876:AAEyM_sckiSJyjD3ci4QLtPfstR4Uxn9tDk",
    chat_id: "-1001933568755"
}
function sendMessage(event) {
    const url = `https://api.telegram.org/bot${tg.token}/sendMessage`

    const tgForm = document.querySelector('.tg-form')
    const name = tgForm.querySelector('[name="fname"]'),
        tel = tgForm.querySelector('[name="ftel"]'),
        txt = tgForm.querySelector('[name="ftext"]');

    const msg = `Заявка со страницы скупки 100nout.by! 

Имя: ${name.value}
Телефон: ${tel.value}
Сообщение: ${txt.value}

Отправлено со страницы ${window.location.href}
`

    const obj = {
        chat_id: tg.chat_id,
        text: msg
    };

    const xht = new XMLHttpRequest();
    xht.open("POST", url, true);
    xht.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    xht.send(JSON.stringify(obj));
}
const formSubmit = document.querySelector(".js-tg-form");

formSubmit.addEventListener('submit', function (e) {
    e.preventDefault();
    sendMessage();
});

// Elements
import "./elements/dropdown";
import "./elements/intro";
import "./elements/menu";
import "./elements/product";
import "./elements/catalog";
import "./elements/cartP";
import "./elements/order";
import "./elements/popup";
