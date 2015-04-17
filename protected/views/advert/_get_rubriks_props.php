<?

?>


<script>
    function get_props_list(field_id, parent_field_id)
    {
        $('#'+field_id).autocomplete({
            source: function(request, response){
                //console.log(request);
                $.ajax({
                    url: "/index.php?r=advert/getpropslist",
                    method: "post",
                    dataType: "json",
                    // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
                    data:{
                        field_id: field_id,
                        field_value: request.term,
                        parent_field_id: parent_field_id,
                        parent_ps_id: $('#'+parent_field_id+'-id').val()
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


                return false;
            },

            minLength: 1
        });
    }


</script>