<?

?>


<script>
    // field_id - id поля формы (поле selector из таблицы rubriks_props)
    // parent_field_id - id поля формы родителя (поле selector из таблицы rubriks_props), '' - если родителя нет
    function get_props_list_autoload(field_id, parent_field_id)
    {
//        console.log(props_hierarhy[parent_field_id]);
        $('#'+field_id).autocomplete({
            source: function(request, response){

                parent_ps_id = 0;
                if (props_hierarhy[parent_field_id] !== undefined)
                {
                    parent_ps_id = $('#'+props_hierarhy[parent_field_id]['field_value_id']).val();
                    //alert(parent_ps_id);
                }

                $.ajax({
                    url: "/index.php?r=advert/getpropslist_autocomplete",
                    method: "post",
                    dataType: "json",
                    // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
                    data:{
                        field_id: field_id,
                        field_value: request.term,
                        parent_field_id: parent_field_id,
                        parent_ps_id: parent_ps_id
                    },
                    // обработка успешного выполнения запроса
                    success: function(data){
                        $('#ajax_debug').html(data);
                        // приведем полученные данные к необходимому формату и передадим в предоставленную функцию response
                        response($.map(data.props_list, function(item){
                            //console.log(item);
                            return{
                                label: item.value,
                                value: item.ps_id
                            }
                        }));
                    }


                });
            },
            focus: function( event, ui ) {
                $('#'+field_id).val( ui.item.label );
                return false;
            },
            select: function(event, ui) {
                /*ui.item будет содержать выбранный элемент*/
                //console.log(this.id);
                $(this).val( ui.item.label );
                $('#'+$(this).attr('id')+'-id').val( ui.item.value );

                $(this).css('display', 'none');
                $('#'+$(this).attr('id')+'-span').html(ui.item.label);
                $('#'+$(this).attr('id')+'-span').css('display', 'inline');

                //console.log(props_hierarhy[$(this).attr('id')]['childs_selector']);
                //alert($(this).attr('id'));

                if (props_hierarhy[$(this).attr('id')]['childs_selector'] !== undefined)
                {
                    $.each (props_hierarhy[$(this).attr('id')]['childs_selector'], function (index, value) {
                        //console.log(index+' = '+$('#'+index).attr('id'));
                        get_props_list_functions['f'+props_hierarhy[index]['vibor_type']](index, props_hierarhy[index]['parent_selector']);
                    });
                }


                return false;
            },

            minLength: 1
        });
    }

    function get_props_list_autoload_with_listitem(field_id, parent_field_id)
    {
        get_props_list_autoload(field_id, parent_field_id);
    }


        // field_id - id поля формы (поле selector из таблицы rubriks_props)
    // parent_field_id - id поля формы родителя (поле selector из таблицы rubriks_props), '' - если родителя нет
    function get_props_list_selector(field_id, parent_field_id)
    {
        parent_ps_id = 0;
        if (props_hierarhy[parent_field_id] !== undefined)
        {
            parent_ps_id = $('#'+props_hierarhy[parent_field_id]['field_value_id']).val();
        }

        $.ajax({
            url: "/index.php?r=advert/getpropslist_selector",
            method: "post",
            //dataType: "json",
            // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
            data:{
                field_id: field_id,
                parent_field_id: parent_field_id,
                parent_ps_id: parent_ps_id
            },
            // обработка успешного выполнения запроса
            success: function(data){
                //alert(data);

                $('#div_'+field_id).html(data);
            }


        });

    }

    function get_props_list_listitem(field_id, parent_field_id)
    {
        parent_ps_id = 0;
        if (props_hierarhy[parent_field_id] !== undefined)
        {
            parent_ps_id = $('#'+props_hierarhy[parent_field_id]['field_value_id']).val();
        }

        $.ajax({
            url: "/index.php?r=advert/getpropslist_listitem",
            method: "post",
            //dataType: "json",
            // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
            data:{
                field_id: field_id,
                parent_field_id: parent_field_id,
                parent_ps_id: parent_ps_id
            },
            // обработка успешного выполнения запроса
            success: function(data){
                //alert(data);

                $('#div_'+field_id+'_list').html(data);
            }
        });
    }


</script>