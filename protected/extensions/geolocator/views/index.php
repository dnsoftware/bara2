<div style="font-size: 12px;">
<span id="current_geoname" style="text-decoration: none; cursor: pointer; color: #008cc3;">
<?
    if(Yii::app()->request->cookies['geo_mytown_name']->value != '')
    {
        echo Yii::app()->request->cookies['geo_mycountry_name']->value .", ". Yii::app()->request->cookies['geo_mytown_name']->value;
    }
    else
    if(Yii::app()->request->cookies['geo_myregion_name']->value != '')
    {
       echo Yii::app()->request->cookies['geo_mycountry_name']->value .", ". Yii::app()->request->cookies['geo_myregion_name']->value;
    }
    else
    if (Yii::app()->request->cookies['geo_mycountry_name']->value != '')
    {
        echo Yii::app()->request->cookies['geo_mycountry_name']->value;
    }
    else
    {
        echo "Регион не определен";
    }

//deb::dump($_COOKIE);

?>
    <i class="regselect_arrow"></i>
</span>


</div>

<div id="region_change" style="">
    <form id="form_region_change" action="<?= Yii::app()->createUrl('/filter/setregioncookie');?>" method="post" onsubmit="if($('#geo_region_id').val() == '') return false;">
    <input type="hidden" name="region_id" id="geo_region_id" value="" >
    <input type="text" name="region_name" id="geo_region_name" value="" style="width: 335px;" placeholder="начните набирать название своего города или региона">
    </form>
</div>

<style>
    .ui-autocomplete
    {
        max-height: 400px; overflow-y: auto; overflow-x: visible;
        font-size: 12px;
    }
</style>

<script>
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
                url: "<?= Yii::app()->createUrl('/filter/getregionlist');?>",
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

                    // Для стилизации отдельных элементов списка
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
            /*ui.item будет содержать выбранный элемент*/
            //console.log(ui.item);
            $('#geo_region_id').val(ui.item.value);
            $('#form_region_change').submit();

            return false;
        }

    });


</script>