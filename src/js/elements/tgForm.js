var popupBg = $(".tg-form-modal");
var popup = $(".tg-form-modal-wrap");
$(".js-tg-form").on("submit", function (event) {
  event.stopPropagation();
  event.preventDefault();

  var button = document.getElementById("disbutton");

  button.disabled = true;
  button.value = "Подождите 10 секунд...";
  setTimeout(function () {
    button.disabled = false;
    button.value = "Отправить на оценку";
  }, 10000);

  let form = this,
    submit = $(".tg-form-submit", form),
    data = new FormData(),
    files = $("input[type=file]");

  data.append("Имя", $('[name="fname"]', form).val());
  data.append("Телефон", $('[name="ftel"]', form).val());
  data.append("Описание", $('[name="ftext"]', form).val());
  if ($('[name="fcall-time"]', form).val()) {
    data.append(
      $('[name="fhidden-text"]', form).val(),
      $('[name="fcall-time"]', form).val(),
    );
  }

  data.forEach((value, key) => {
    console.log(`${key}: ${value}`);
  });

  files.each(function (key, file) {
    let cont = file.files;
    if (cont) {
      $.each(cont, function (key, value) {
        data.append(key, value);
      });
    }
  });

  $.ajax({
    url: "https://100nout.by/wp-content/themes/nout/ajax.php",
    type: "POST",
    data: data,
    cache: false,
    dataType: "json",
    processData: false,
    contentType: false,
    xhr: function () {
      let myXhr = $.ajaxSettings.xhr();

      if (myXhr.upload) {
        myXhr.upload.addEventListener(
          "progress",
          function (e) {
            if (e.lengthComputable) {
              let percentage = (e.loaded / e.total) * 100;
              percentage = percentage.toFixed(0);
              $(".submit", form).html(percentage + "%");
            }
          },
          false,
        );
      }

      return myXhr;
    },
    error: function (jqXHR, textStatus) {},
    complete: function () {
      popupBg.addClass("active");
      popup.addClass("active");
      form.reset();
    },
  });
  return false;
});

let popupBgv = document.querySelector(".tg-form-modal");
let popupv = document.querySelector(".tg-form-modal-wrap");
let closePopupButton = document.querySelector(".close_modal");
if (popupBgv) {
  closePopupButton.addEventListener("click", () => {
    popupBgv.classList.remove("active");
    popupv.classList.remove("active");
  });

  document.addEventListener("click", (e) => {
    if (e.target === popupBgv) {
      popupBgv.classList.remove("active");
      popupv.classList.remove("active");
    }
  });
}
