// document.documentElement.scrollHeight - высота веб-документа;
// aside.offsetHeight - высота элемента
var aside = document.querySelector('aside'),
    t0 = aside.getBoundingClientRect().top - document.documentElement.getBoundingClientRect().top,
    t1 = document.documentElement.scrollHeight - 0 - aside.offsetHeight;

function asideScroll() {
    if (window.pageYOffset > t1) {
        aside.className = 'stop';
        aside.style.top = t1 - t0 + 'px';
    } else {
        aside.className = (t0 < window.pageYOffset ? 'prilip' : '');
        aside.style.top = '0';
    }
}
window.addEventListener('scroll', asideScroll, false);



function filter_index_init(addtofavorit_url, setsortmode_url)
{
    $('.favorit_button, .favoritstar').click(function(){
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
                    //fbut.html('В избранном');
                    fbut.css('background-image', 'url("/images/favorit_yellow.png")');
                }
                else
                {
                    //fbut.html('В избранное');
                    fbut.css('background-image', 'url("/images/favorit.png")');
                }

            }
        });

    });

    $('#display_otheradverts').click(function(){
        if($('.titulhide').css('display') == 'none')
        {
            $('.titulhide').css('display', 'table-row');
            $('#paginator').css('display', 'block');
            $(this).html('Свернуть');
        }
        else
        {
            $('.titulhide').css('display', 'none');
            $(this).html('Развернуть');
        }
    });


    $('#filtersort').change(function(){
        $('#sort').val($(this).val());

        $.ajax({
            url: setsortmode_url,
            method: "post",
            dataType: 'json',
            async: false,
            data:{
                m: 1
            },
            // обработка успешного выполнения запроса
            success: function(data){

            }
        });

        $('#form_filter').submit();

    });
}
