<div style="font-size: 12px;">
<span id="current_geoname" style="text-decoration: none; cursor: pointer; color: #008cc3;">
<?
    $region_name = '';
    if(Yii::app()->request->cookies['geo_mytown_name']->value != '')
    {
        $region_name = Yii::app()->request->cookies['geo_mycountry_name']->value .", ". Yii::app()->request->cookies['geo_mytown_name']->value;
        //echo $region_name;
    }
    else
    if(Yii::app()->request->cookies['geo_myregion_name']->value != '')
    {
        $region_name = Yii::app()->request->cookies['geo_mycountry_name']->value .", ". Yii::app()->request->cookies['geo_myregion_name']->value;
        //echo $region_name;
    }
    else
    if (Yii::app()->request->cookies['geo_mycountry_name']->value != '')
    {
        $region_name = Yii::app()->request->cookies['geo_mycountry_name']->value;
        //echo $region_name;
    }
    else
    {
        $region_name = "Регион не определен";
    }

    if(Yii::app()->request->cookies['geo_mytown_handchange_tag']->value == 1)
    {
        echo $region_name;
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

    <input type="hidden" name="reg_confirm_tag" id="reg_confirm_tag" value="1">

    </form>
</div>

<?
if(Yii::app()->request->cookies['region_confirm_tag']->value == 0)
{
?>
<div id="div_reg_confirm" style="position: absolute; width: 330px; margin-left: 100px; margin-top: 0px; z-index: 1; background-color: #f00; color: #fff; padding: 8px; text-align: center; border-radius: 5px;">

    <div style="float: right; color: #fff; cursor: pointer; font-size: 14px; margin-top: -8px; margin-right: -4px; clear: both;" class="reg_confirm_yes">x</div>

    <?

    if(!isset(Yii::app()->request->cookies['region_confirm_tag']))
    {
        $cookie = new CHttpCookie('region_confirm_tag', 0);
        $cookie->expire = time() + 86400*30*12;
        Yii::app()->request->cookies['region_confirm_tag'] = $cookie;
    }

    //deb::dump(Yii::app()->request->cookies['region_confirm_tag']->value);
    //deb::dump($cookie['mytown']);
    //deb::dump($cookie['myregion']);
    //deb::dump($cookie['mycountry']);
    ?>
    <div style="font-size: 12px;">
    <?
    if($region_name != '' && $region_name != "Регион не определен")
    {
    ?>
        <div style="margin-bottom: 5px;">
            <div style="font-weight: bold; margin-bottom: 10px;">Выберите город или регион для фильтрации<br>
            объявлений по территориальному признаку.</div>
            Мы определили Ваш регион как <span style="font-weight: bold; font-style: italic;"><?= $region_name;?></span>:
        </div>

        <span id="reg_confirm_yes" class="reg_confirm_yes" style="cursor: pointer; border-bottom: #fff solid 1px; font-weight: bold; font-size: 16px;">Да, правильно</span>
        <span id="reg_confirm_no" style="margin-left: 30px; cursor: pointer; border-bottom: #fff solid 1px;font-weight: bold; font-size: 16px;">Нет, неправильно</span>
    <?
    }
    else
    {
    ?>
        <div style="margin-bottom: 5px;">
        Ваш регион не определен!
        </div>
        <span id="reg_confirm_no" style="cursor: pointer; border-bottom: #fff solid 1px; font-weight: bold;  font-size: 16px;">Указать регион</span>
    <?
    }
    ?>
    </div>

</div>
<?
}
?>

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

    $('.reg_confirm_yes').click(function(){
        $.ajax({
            url: "<?= Yii::app()->createUrl('/site/setregconfirmyes');?>",
            method: "post",
            data:{},
            // обработка успешного выполнения запроса
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

</script>