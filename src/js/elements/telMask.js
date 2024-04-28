import IMask from 'imask';

const obmenTel = document.querySelector("input[placeholder='Номер телефона*']");

if (obmenTel) {
  const maskOptions = {
    mask: '+{375} (00) 000-00-00',
  };
  IMask(obmenTel, maskOptions);
}
