Galleria.loadTheme('/js/galleria/themes/classic/galleria.classic.js');
Galleria.run('#galleria', {
    width: 684,
    height: 484,
    //imageCrop: 'landscape',
    lightbox: true,
    //overlayBackground: '#ffffff'
    showImagenav: true,
    showinfo: false,
    carousel: false,
    thumbPosition: 'center',
    extend: function() {
        var gallery = this; // "this" is the gallery instance
        $('#gallery_fullview').prependTo('.galleria-container');

        $('#gallery_fullview').click(function() {
            gallery.openLightbox();
        });

        // Задание стиля для активного превью
        $('.galleria-image').click(function(){
            $('.galleria-image').css('border', '#fff solid 2px');
            $(this).css('border', '#999 solid 2px');

            $('.galleria-images > .galleria-image').css('border', '0px');

        });

    }

});


$('#abuse_button').click(function(){

    $('#abuse_window').css('display', 'block');
    $('#abuse_window').offset({
        left: $('#abuse_button').offset().left - 10,
        top: $('#abuse_button').offset().top - 123
    });

});


function share_and_abuse(getabuseform_url, getshareform_url, addtofavorit_url)
{

    $('.abuse_quick, .abuse_other').click( function(event){
        item = $(this);
        event.preventDefault(); // выключaем стaндaртную рoль элементa
        $('#modal_abusecaptcha_overlay').fadeIn(400, // снaчaлa плaвнo пoкaзывaем темную пoдлoжку
            function(){
                if(item.attr('abusetype') != 'other_abuse')
                {
                    $('#modal_abusecaptcha').css('height', '140px');
                }
                else
                {
                    $('#modal_abusecaptcha').css('height', '230px');
                }

                $.ajax({
                    url: getabuseform_url,
                    method: "post",
                    data:{
                        n_id: item.attr('abuse_n_id'),
                        class: item.attr('abuseclass'),
                        type: item.attr('abusetype')
                    },
                    // обработка успешного выполнения запроса
                    success: function(data){
                        $('#modal_abusecaptcha_content').html(data);

                    }
                });

                $('#abuse_window').css('display', 'none');

                $('#modal_abusecaptcha')
                    .css('display', 'block') // убирaем у мoдaльнoгo oкнa display: none;
                    .animate({opacity: 1, top: '50%'}, 200); // плaвнo прибaвляем прoзрaчнoсть oднoвременнo сo съезжaнием вниз

            });
    });


    $('#favorit_button').click(function(){
        fbut = $(this);

        $.ajax({
            url: addtofavorit_url,
            method: "post",
            dataType: 'json',
            data:{
                n_id: fbut.attr('advert_id')
            },
            // обработка успешного выполнения запроса
            success: function(data){
                $('#favorit_count').html(data['count']);
                if(data['status'] == 'add')
                {
                    $('#favorit_button').html('В избранном');
                }
                else
                {
                    $('#favorit_button').html('В избранное');
                }

            }
        });

    });



    /* Зaкрытие мoдaльнoгo oкнa, тут делaем тo же сaмoе нo в oбрaтнoм пoрядке */
    $('#modal_abusecaptcha_close, #modal_abusecaptcha_overlay').click( function(){ // лoвим клик пo крестику или пoдлoжке
        $('#modal_abusecaptcha')
            .animate({opacity: 0, top: '45%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
            function(){ // пoсле aнимaции
                $(this).css('display', 'none'); // делaем ему display: none;
                $('#modal_abusecaptcha_overlay').fadeOut(400); // скрывaем пoдлoжку
            }
        );
    });


    /****************************** Поделиться*********************************/

    $('#share_button').click( function(event){
        item = $(this);
        event.preventDefault(); // выключaем стaндaртную рoль элементa
        $('#modal_share_overlay').fadeIn(400, // снaчaлa плaвнo пoкaзывaем темную пoдлoжку
            function(){

                $.ajax({
                    url: getshareform_url,
                    method: "post",
                    data:{
                        n_id: item.attr('share_n_id')
                    },
                    // обработка успешного выполнения запроса
                    success: function(data){
                        $('#modal_share_content').html(data);

                    }
                });

                $('#modal_share')
                    .css('display', 'block') // убирaем у мoдaльнoгo oкнa display: none;
                    .animate({opacity: 1, top: '50%'}, 200); // плaвнo прибaвляем прoзрaчнoсть oднoвременнo сo съезжaнием вниз

            });
    });

    /* Зaкрытие мoдaльнoгo oкнa, тут делaем тo же сaмoе нo в oбрaтнoм пoрядке */
    $('#modal_share_close, #modal_share_overlay').click( function(){ // лoвим клик пo крестику или пoдлoжке
        $('#modal_share')
            .animate({opacity: 0, top: '45%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
            function(){ // пoсле aнимaции
                $(this).css('display', 'none'); // делaем ему display: none;
                $('#modal_share_overlay').fadeOut(400); // скрывaем пoдлoжку
            }
        );
    });


    // Закрытие таблицы валют по клику за пределами таблицы
    $(document).click(function(event) {
        //valute_symbol
        //console.log($(event.target).closest("#valute_symbol").length);
        if($(event.target).closest("#valute_symbol").length)
        {
            return;
        }

        if ($(event.target).closest("#div_valute_change").length)
        {
            return;
        }

        $("#div_valute_change").css('display', 'none');

        if ($(event.target).closest("#abuse_button").length)
        {
            return;
        }
        if ($(event.target).closest("#abuse_window").length)
        {
            return;
        }

        $("#abuse_window").css('display', 'none');


        event.stopPropagation();
    });

    // Позиционирование таблицы валют
    $('#div_valute_change').offset({
        left: $('#valute_symbol').offset().left-125,
        top: $('#valute_symbol').offset().top + $('#valute_symbol').height() + 5
    });


}
