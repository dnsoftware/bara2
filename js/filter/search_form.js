function search_form_init(getregionlist_url, mestolistgenerate_url, getquerylist_url)
{
    $('#div_searchreg_name').offset({
        left: $('#mesto_id').offset().left-10,
        top: $('#mesto_id').offset().top-5
    });//$('#mesto_id').offset().left;

    $('#searchreg_name').autocomplete({
        position:{my:"left top", at:"left bottom"},
        minLength: 3,
        source: function(request, response){

            $.ajax({
                url: getregionlist_url,
                method: "post",
                dataType: "json",
                // параметры запроса, передаваемые на сервер:
                data:{
                    searchstr: request.term
                },
                // обработка успешного выполнения запроса
                success: function(data){
                    $('#ajax_debug').html(data);
                    // приведем полученные данные к необходимому формату и передадим в предоставленную функцию response
                    response($.map(data.reglist, function(item){
                        console.log(item);

                        return{
                            label: item.name_ru,
                            value: item.id
                        }

                    }));

                }


            });
        },
        focus: function( event, ui ) {
            //$('#searchreg_name').val( ui.item.label );
            return false;

        },
        select: function(event, ui) {
            /*ui.item будет содержать выбранный элемент*/
            //console.log(ui.item);
            //$('#searchreg_id').val(ui.item.value);

            $.ajax({
                url: mestolistgenerate_url,
                method: "post",
                dataType: "json",
                // параметры запроса, передаваемые на сервер:
                data:{
                    mesto_id: ui.item.value
                },
                // обработка успешного выполнения запроса
                success: function(data){
                    $('#mesto_id').html(data['data']);
                    $('#div_searchreg_name').css('display', 'none');
                }
            });


            return false;
        }

    });

    $('#searchquery').autocomplete({
        position:{my:"left top", at:"left bottom"},
        minLength: 2,
        source: function(request, response){

            $.ajax({
                url: getquerylist_url,
                method: "post",
                dataType: "json",
                // параметры запроса, передаваемые на сервер:
                data:{
                    searchstr: request.term
                },
                // обработка успешного выполнения запроса
                success: function(data){
                    $('.ui-autocomplete').css('overflow-x', 'hidden');
                    $('.ui-autocomplete').css('overflow-y', 'hidden');
                    // приведем полученные данные к необходимому формату и передадим в предоставленную функцию response
                    response($.map(data, function(item){
                        return{
                            label: item.snippet,
                            value: item.val
                        }

                    }));

                }


            });
        },
        focus: function( event, ui ) {
            //$('#searchreg_name').val( ui.item.label );
            //return false;

        },
        select: function(event, ui) {

        }

    }).data("ui-autocomplete")._renderItem = function( ul, item ) {
        return $( "<li>" )
            .attr( "data-value", item.value )
            .append( item.label )
            .appendTo( ul );
    }
}



function displaySearchReg()
{
    if($('#mesto_id').val() == 'other')
    {
        $('#div_searchreg_name').css('display', 'block');
        $('#searchreg_name').focus();
    }

}
