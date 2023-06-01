import '../scss/app.scss';

/* Your JS Code goes here */
import IMask from 'imask'
const obmenTel = document.querySelector("input[placeholder='Номер телефона*']")

const maskOptions = {
    mask: '+{375} (00) 000-00-00'
};
const mask = IMask(obmenTel, maskOptions);


// Elements
import "./elements/dropdown";
import "./elements/intro";
import "./elements/menu";
import "./elements/product";
import "./elements/catalog";
import "./elements/cartP";
import "./elements/order";
import "./elements/popup";
