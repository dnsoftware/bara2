<?

?>


<script>
    // field_id - id поля формы (поле selector из таблицы rubriks_props)
    // parent_field_id - id поля формы родителя (поле selector из таблицы rubriks_props), '' - если родителя нет
    function get_props_list_autoload(field_id, parent_field_id, n_id, parent_ps_id)
    {
        if (parent_ps_id === undefined) {
            parent_ps_id = 0;
        }

        //console.log(field_id);
        $('#'+field_id +'-display').autocomplete({
            source: function(request, response){

                /*
                parent_ps_id = 0;
                if (props_hierarhy[parent_field_id] !== undefined)
                {
                    parent_ps_id = $('#'+props_hierarhy[parent_field_id]['field_value_id']).val();
                    //alert(parent_ps_id);
                }
                */

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
                $('#'+field_id +'-display').val( ui.item.label );
                return false;
            },
            select: function(event, ui) {
                /*ui.item будет содержать выбранный элемент*/
                //console.log(this.id);
                $(this).val( ui.item.label );
                //$('#'+$(this).attr('id')+'-id').val( ui.item.value );
                $('#'+$(this).attr('inputfield')).val( ui.item.value );

                $(this).css('display', 'none');
                //$('#'+$(this).attr('id')+'-span').html(ui.item.label);
                //$('#'+$(this).attr('id')+'-span').css('display', 'inline');
                $('#'+$(this).attr('inputfield')+'-span').html(ui.item.label);
                $('#'+$(this).attr('inputfield')+'-span').css('display', 'inline');

                $('#div_'+$(this).attr('inputfield')+'_list').css('display', 'none');


                //console.log(props_hierarhy[$(this).attr('id')]['childs_selector']);
                //alert($(this).attr('id'));
                ChangeRelateProps($('#'+field_id), n_id);

                return false;
            },

            minLength: 1
        });
    }

    function get_props_list_autoload_with_listitem(field_id, parent_field_id, n_id, parent_ps_id)
    {
        if (parent_ps_id === undefined) {
            parent_ps_id = 0;
        }

        get_props_list_autoload(field_id, parent_field_id, n_id, parent_ps_id);

        get_props_list_listitem(field_id, parent_field_id, n_id, parent_ps_id);

    }


        // field_id - id поля формы (поле selector из таблицы rubriks_props)
    // parent_field_id - id поля формы родителя (поле selector из таблицы rubriks_props), '' - если родителя нет
    function get_props_list_selector(field_id, parent_field_id, n_id, parent_ps_id)
    {
        if (parent_ps_id === undefined) {
            parent_ps_id = 0;
        }
        /*
        parent_ps_id = 0;
        if (props_hierarhy[parent_field_id] !== undefined)
        {
            parent_ps_id = $('#'+props_hierarhy[parent_field_id]['field_value_id']).val();
        }
        */

        props_load_stack_count++;
        $.ajax({
            url: "/index.php?r=advert/getpropslist_selector",
            method: "post",
            //dataType: "json",
            // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
            data:{
                field_id: field_id,
                parent_field_id: parent_field_id,
                parent_ps_id: parent_ps_id,
                n_id: n_id
            },
            // обработка успешного выполнения запроса
            success: function(data){
                $('#div_'+field_id).html(data);
                props_load_stack_count--;
            }


        });

    }

    function get_props_list_listitem(field_id, parent_field_id, n_id, parent_ps_id)
    {
        if (parent_ps_id === undefined) {
            parent_ps_id = 0;
        }
        /*
        parent_ps_id = 0;
        if (props_hierarhy[parent_field_id] !== undefined)
        {
            parent_ps_id = $('#'+props_hierarhy[parent_field_id]['field_value_id']).val();
        }
        */

        props_load_stack_count++;
        $.ajax({
            url: "/index.php?r=advert/getpropslist_listitem",
            method: "post",
            //dataType: "json",
            // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
            data:{
                field_id: field_id,
                parent_field_id: parent_field_id,
                parent_ps_id: parent_ps_id,
                n_id: n_id
            },
            // обработка успешного выполнения запроса
            success: function(data){
                $('#div_'+field_id+'_list').html(data);

                //alert(data);
                //alert(field_id+' - '+$('#'+field_id).val());
                if($('#'+field_id).val() > 0)
                {
                    $('#'+field_id+'-span').css('display', 'inline');
                    $('#'+field_id+'-display').css('display', 'none');
                    $('#div_'+field_id+'_list').css('display', 'none');
                }
                props_load_stack_count--;

            }
        });
    }

    function get_props_list_checkbox(field_id, parent_field_id, n_id, parent_ps_id)
    {
        if (parent_ps_id === undefined) {
            parent_ps_id = 0;
        }
        /*
        parent_ps_id = 0;
        if (props_hierarhy[parent_field_id] !== undefined)
        {
            parent_ps_id = $('#'+props_hierarhy[parent_field_id]['field_value_id']).val();
        }
        */

        props_load_stack_count++;
        $.ajax({
            url: "/index.php?r=advert/getpropslist_checkbox",
            method: "post",
            //dataType: "json",
            // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
            data:{
                field_id: field_id,
                parent_field_id: parent_field_id,
                parent_ps_id: parent_ps_id,
                n_id: n_id
            },
            // обработка успешного выполнения запроса
            success: function(data){
                //alert(data);

                $('#div_'+field_id+'_list').html(data);
                props_load_stack_count--;

                return 1;
            }
        });
    }

    function get_props_list_radio(field_id, parent_field_id, n_id, parent_ps_id)
    {
        if (parent_ps_id === undefined) {
            parent_ps_id = 0;
        }
        /*
        parent_ps_id = 0;
        if (props_hierarhy[parent_field_id] !== undefined)
        {
            parent_ps_id = $('#'+props_hierarhy[parent_field_id]['field_value_id']).val();
        }
        */

        props_load_stack_count++;
        $.ajax({
            url: "/index.php?r=advert/getpropslist_radio",
            method: "post",
            //dataType: "json",
            // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
            data:{
                field_id: field_id,
                parent_field_id: parent_field_id,
                parent_ps_id: parent_ps_id,
                n_id: n_id
            },
            // обработка успешного выполнения запроса
            success: function(data){

                $('#div_'+field_id+'_list').html(data);
                props_load_stack_count--;

            }
        });
    }

    function get_props_list_string(field_id, parent_field_id, n_id, parent_ps_id)
    {
        if (parent_ps_id === undefined) {
            parent_ps_id = 0;
        }
        /*
        parent_ps_id = 0;
        if (props_hierarhy[parent_field_id] !== undefined)
        {
            parent_ps_id = $('#'+props_hierarhy[parent_field_id]['field_value_id']).val();
        }
        */

        props_load_stack_count++;
        $.ajax({
            url: "/index.php?r=advert/getpropslist_string",
            method: "post",
            //dataType: "json",
            // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
            data:{
                field_id: field_id,
                parent_field_id: parent_field_id,
                parent_ps_id: parent_ps_id,
                n_id: n_id
            },
            // обработка успешного выполнения запроса
            success: function(data){

                $('#div_'+field_id+'_field').html(data);
                props_load_stack_count--;

            }
        });
    }

    function get_props_list_photoblock(field_id, parent_field_id, n_id, parent_ps_id)
    {
        props_load_stack_count++;
        $.ajax({
            url: "/index.php?r=advert/getpropslist_photoblock",
            method: "post",
            //dataType: "json",
            // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
            data:{
                field_id: field_id,
                parent_field_id: parent_field_id,
                parent_ps_id: parent_ps_id,
                n_id: n_id
            },
            // обработка успешного выполнения запроса
            success: function(data){

                $('#div_'+field_id+'_photoblock').html(data);
                props_load_stack_count--;

            }
        });
    }

</script>