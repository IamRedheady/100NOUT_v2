var popupBg = $('.tg-form-modal');
var popup = $('.tg-form-modal-wrap');
$('.js-tg-form').on('submit', function (event) {
    event.stopPropagation();
    event.preventDefault();

    let form = this,
        submit = $('.tg-form-submit', form),
        data = new FormData(),
        files = $('input[type=file]')

    submit.disable = true;

    setTimeout(function () {
        submit.disabled = false;
    }, 10000);


    data.append( 'Имя', 		$('[name="fname"]', form).val() );
    data.append( 'Телефон', 		$('[name="ftel"]', form).val() );
    data.append( 'Описание', 		$('[name="ftext"]', form).val() );

    files.each(function (key, file) {
        let cont = file.files;
        if ( cont ) {
            $.each( cont, function( key, value ) {
                data.append( key, value );
            });
        }
    });


    $.ajax({
        url: 'https://100nout.by/wp-content/themes/nout/ajax.php',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        xhr: function() {
            let myXhr = $.ajaxSettings.xhr();

            if ( myXhr.upload ) {
                myXhr.upload.addEventListener( 'progress', function(e) {
                    if ( e.lengthComputable ) {
                        let percentage = ( e.loaded / e.total ) * 100;
                        percentage = percentage.toFixed(0);
                        $('.submit', form)
                            .html( percentage + '%' );
                    }
                }, false );
            }

            return myXhr;
        },
        error: function( jqXHR, textStatus ) {

        },
        complete: function() {
            popupBg.addClass('active');
            popup.addClass('active');
            form.reset()
        }
    });
    return false;
});
