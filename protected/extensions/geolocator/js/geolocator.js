function getregion_js(getregionlist_url, setregconfirmyes_url)
{
    $("#current_geoname").click(
        function()
        {
            if($('#region_change').css('visibility') == 'visible')
            {
                $('#region_change').css('visibility', 'hidden');
            }
            else
            {
                $('#region_change').css('visibility', 'visible');
                $('#geo_region_name').focus();
            }
        }

    );


    $('#geo_region_name').autocomplete({
        position:{my:"left top", at:"left bottom"},
        minLength: 3,
        source: function(request, response){

            $.ajax({
                url: getregionlist_url,
                method: "post",
                dataType: "json",
                // ��������� �������, ������������ �� ������:
                data:{
                    searchstr: request.term
                },
                // ��������� ��������� ���������� �������
                success: function(data){
                    $('#ajax_debug').html(data);
                    // �������� ���������� ������ � ������������ ������� � ��������� � ��������������� ������� response
                    response($.map(data.reglist, function(item){
                        console.log(item);

                        if(item.id == 0)
                        {
                            return{
                                //label: '<i><b>'+item.name_ru+'</b></i>',
                                label: item.name_ru,
                                value: item.id
                            }
                        }
                        else
                        {
                            return{
                                label: item.name_ru,
                                value: item.id
                            }
                        }

                    }));

                    // ��� ���������� ��������� ��������� ������
                    $(".ui-menu-item").each(function(){
                        var htmlString = $(this).html().replace(/&lt;/g, '<');
                        htmlString = htmlString.replace(/&gt;/g, '>');
                        $(this).html(htmlString);
                    });

                }


            });
        },
        focus: function( event, ui ) {
            //$('#geo_region_name').val( ui.item.label );
            return false;

        },
        select: function(event, ui) {
            /*ui.item ����� ��������� ��������� �������*/
            //console.log(ui.item);
            $('#geo_region_id').val(ui.item.value);
            $('#form_region_change').submit();

            return false;
        }

    });

    $('.reg_confirm_yes').click(function(){
        $.ajax({
            url: setregconfirmyes_url,
            method: "post",
            data:{},
            // ��������� ��������� ���������� �������
            success: function(data){
                $('#div_reg_confirm').css('display', 'none');
                //location.href = '/';
            }
        });
    });


    $('#reg_confirm_no').click(function(){

        $('#div_reg_confirm').css('display', 'none');
        $('#region_change').css('visibility', 'visible');
        $('#geo_region_name').focus();

    });
}