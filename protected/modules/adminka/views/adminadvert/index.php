<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.sumoselect.min.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/sumoselect.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/filtercontroller.js');

Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/nouislider/nouislider.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/nouislider/nouislider.tooltips.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/nouislider/nouislider.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/wNumb.js');


$this->renderPartial('/default/_admin_menu');

?>

<h1 style="margin: 5px; font-size: 16px;">Админка: Работа с объявлениями</h1>

<div>
    <?php
    $this->renderPartial('_search_form', array(
        'rub_array'=>$rub_array,
        'mselector'=>$mselector,
        'm_id'=>$m_id,
        'props_sprav_sorted_array'=>$props_sprav_sorted_array,
        'rubriks_props_array'=>$rubriks_props_array,
        'rub_old_array'=>$rub_old_array,

        'col_on_page'=>$col_on_page,
        'page'=>$page,
        'col_pages'=>$col_pages


    ));

    ?>
</div>

<form name="search_adverts_form">
<table>
    <tr>
        <td class="f12b" style=""><input type="checkbox" id="checkall"></td>
        <td class="f12b" style="width: 8%;">Дата</td>
        <td class="f12b" style="width: 80%;">Объявление</td>
        <td class="f12b" style="width: 3%;">А</td>
        <td class="f12b" style="width: 3%;">В</td>
        <td class="f12b" style="width: 3%;">У</td>
        <td class="f12b" style="width: 3%;">У+</td>
    </tr>
    <?
    foreach ($adverts as $akey=>$aval)
    {
        $expired_tag = 0;
        if($aval['date_expire'] < time())
        {
            $expired_tag = 1;
        }
        //deb::dump($aval);
        ?>
        <tr class="trbotline" id="tradv_<?= $aval['n_id'];?>" style="background-color: #fafafa;">
            <td class="f11">
                <input type="checkbox" class="chadvert" name="advert[<?= $aval['n_id'];?>]" id="advert_<?= $aval['n_id'];?>" advid="<?= $aval['n_id'];?>">
                <img style="display: none;" id="loader_<?= $aval['n_id'];?>" src="/images/actions/loader.gif">
            </td>
            <td class="not_act" style="font-size: 11px;">
                <?= date('d-m-Y', $aval['date_add']);?><br>
                <?= date('H:i:s', $aval['date_add']);?>
                <?
                if($expired_tag == 1)
                {
                ?>
                <div style="background-color: #ddb602; ">
                    ПРОСРОЧЕНО
                </div>
                <?
                }
                ?>

                <?
                if($aval['deleted_tag'] == 1)
                {
                    ?>
                    <div style="background-color: #f00; text-align: center ">
                        УДАЛЕНО
                    </div>
                <?
                }
                ?>
            </td>

            <td class="not_text">
                <div class="not_rub"><?= $rubriks[$rubriks[$aval['r_id']]->parent_id]->name . " / " . $rubriks[$aval['r_id']]->name;?></div>

                <?
                $advert_page_url = "/".$aval['town_transname']."/".$rubriks_all_array[$aval['r_id']]->transname."/".$transliter->TranslitForUrl($aval['title'])."_".$aval['daynumber_id'];

                ?>
                <div class="not_title" id="advtitul_<?= $aval['n_id'];?>"><a target="_blank" class="baralink" href="<?= $advert_page_url;?>"><?= $aval['title'];?></a></div>

                <div class="not_desc"><?= $aval['notice_text'];?></div>

                <?
                if(count($props_array[$aval['n_id']]['photos']) > 0)
                {
                    $photoname = Notice::getPhotoName($props_array[$aval['n_id']]['photos'][0], "_thumb");
                    $curr_dir = Notice::getPhotoDir($photoname);
                ?>
                    <img width="100" src="/<?= Yii::app()->params['photodir'];?>/<?= $curr_dir;?>/<?= $photoname;?>">
                <?
                }
                ?>

                <div id="result_<?= $aval['n_id'];?>">

                </div>

                <?
                if(trim($aval['props_xml']) == '')
                {
                    ?>
                    <span style="background-color: #f00; color: #fff;">XML empty</span>
                <?
                }
                ?>

                <?

                if(intval($aval['cost']) == 0 && $aval['cost_nodisplay_tag'] == 0)
                {
                ?>
                    <span style="background-color: #f00; color: #fff;">Нулевая цена</span>
                <?
                }

                ?>

            </td>

            <td class="not_act">
            <?
                $fname = 'on';
                $title = 'деактивировать';
                if($aval['active_tag'] == 0)
                {
                    $fname = 'off';
                    $title = 'активировать';
                }
            ?>
                <img class="imgnot_act" n_id="<?= $aval['n_id'];?>" title="<?= $title;?>" src="/images/actions/<?= $fname;?>.gif">
            </td>

            <td class="not_ver">
                <?
                $fname = 'on';
                $title = 'отменить верификацию';
                if($aval['verify_tag'] == 0)
                {
                    $fname = 'off';
                    $title = 'верифицировать';
                }
                ?>
                <img class="imgnot_ver" n_id="<?= $aval['n_id'];?>" title="<?= $title;?>" src="/images/actions/<?= $fname;?>.gif">
            </td>

            <td class="not_del" >
                <?
                $fname = 'on';
                $title = 'отметить как удаленное';
                if($aval['deleted_tag'] == 1)
                {
                    $fname = 'off';
                    $title = 'отменить отметку об удалении';
                }
                ?>
                <img class="imgnot_del" n_id="<?= $aval['n_id'];?>" title="<?= $title;?>" src="/images/actions/<?= $fname;?>.gif">
            </td>

            <td class="not_delplus">
                <img class="imgnot_delplus" title="удалить навсегда" id="imgdel_<?= $aval['n_id'];?>" src="/images/actions/delete.gif" onclick="advert_del(<?= $aval['n_id'];?>);">
            </td>
        </tr>
    <?
    }
    ?>
</table>
</form>


<div id="advert_del" style="background-color: #eee; border: #aaa solid 1px; width: 250px; height: 100px; position: absolute; top: 50px; text-align: center; display: none;">
    <div>Объявление</div>
    <div style="font-weight: bold;" id="del_advert_name"></div>
    <div>будет удалено навсегда</div>

    <br>
    <span id="span_advkill" style="border: #aaa solid 1px; padding: 3px; cursor: pointer;" >&nbsp;Удалить&nbsp;</span>
    <span style="border: #aaa solid 1px; padding: 3px; cursor: pointer; margin-left: 50px;" onclick="$('#advert_del').css('display', 'none');">&nbsp;Отмена&nbsp;</span>

</div>



<?
Yii::app()->clientScript->registerCssFile('/css/abottom_menu.css');
?>
<style>
    #stickey_footer { /* This will make your footer stay where it is */
        background: none repeat scroll 0 0 #ddd;
        border: 1px solid rgba(0, 0, 0, 0.3);
        bottom: 0;
        font-family: Arial, Helvetica, sans-serif;
        height: auto;
        left: 50%;
        margin: 0 auto 0 -540px;
        padding: 0 70px;
        position: fixed;
        /*text-shadow: 1px 1px 1px #000000;*/
        width: 960px;
    }
    /* border curves */
    #stickey_footer {
        -moz-border-radius: 10px 10px 0px 0px;
        -webkit-border-radius: 10px 10px 0px 0px;
        border-radius: 10px 10px 0px 0px;
    }
    /* shadow for the footer*/
    #stickey_footer {
        -moz-box-shadow:0px 0px 5px #191919;
        -webkit-box-shadow:0px 0px 5px #191919;
        box-shadow:0px 0px 5px #191919;
    }

</style>

<div id="stickey_footer">
    <div style="">
        <div>Рубрика</div>

        <div style="margin-bottom: 10px;">
            <form name="panel_form" id="panel_form">

            <select class="panel_rubriks" id="panel_r_id" name="panel[r_id]" style="margin: 0px; width: 250px;">
                <option value="">--- выберите категорию  ---</option>
                <?
                foreach ($rub_array as $rkey=>$rval)
                {
                    $selected = " ";
                    if($rkey == intval($_SESSION['panel']['r_id']))
                    {
                        $selected = " selected ";
                    }
                    ?>
                    <option <?= $selected;?> disabled style="color:#000; font-weight: bold;" value="<?= $rval['parent']->r_id;?>"><?= $rval['parent']->name;?></option>
                    <?
                    foreach ($rval['childs'] as $ckey=>$cval)
                    {
                        $selected = " ";
                        if($ckey == intval($_SESSION['panel']['r_id']))
                        {
                            $selected = " selected ";
                        }
                        ?>
                        <option <?= $selected;?> value="<?= $cval->r_id;?>">&nbsp;<?= $cval->name;?></option>
                    <?
                    }
                }
                ?>

            </select>

            <div id="props_data" style="overflow: auto">
            <?
                Yii::app()->controller->actionGetPanelProps();
            ?>
            </div>
                <div style="color: #f00; margin-top: 5px; float: right;">
                Внимание! После нажатия этой кнопки все свойства у выбраных объявлений будут обнулены и перезаписаны свойствами, выбранными в этой панели!<br>
                </div>

                <div style="margin: 10px 10px; float: right;">

                    <input type="button" id="change_props" value="Изменить рубрику и свойства">
                </div>

            </form>

        </div>

    </div>
</div>




<script>

    function advert_del(n_id)
    {
        $('#span_advkill').unbind('click');
        $('#span_advkill').click(function(){

            $.ajax({
                type: 'POST',
                url: '<?= Yii::app()->createUrl('adminka/adminadvert/advert_kill');?>',
                data: 'n_id='+n_id,
                success: function(msg){
                    if(msg == 'del')
                    {
                        $('#advert_del').css('display', 'none');
                        $('#tradv_'+n_id).fadeOut(800);
                    }
                }
            });
        });

        $('#advert_del').css('display', 'block');
        $('#del_advert_name').html($('#advtitul_'+n_id).html());
        $('#advert_del').offset({
            left: $('#imgdel_'+n_id).offset().left-230,
            top: $('#imgdel_'+n_id).offset().top+16
        });

    }


    $('.imgnot_act').click(function (){
        //alert($(this).attr('src'));

        $(this).attr('src', '/images/actions/loader.gif');
        img = $(this);

        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/adminadvert/setadvert_act');?>',
            data: 'n_id='+$(this).attr('n_id'),
            success: function(msg){
                if(msg == 'act')
                {
                    img.attr('src', '/images/actions/on.gif');
                    img.attr('title', 'деактивировать');
                }
                if(msg == 'deact')
                {
                    img.attr('src', '/images/actions/off.gif');
                    img.attr('title', 'активировать');
                }
            }
        });

    });

    $('.imgnot_ver').click(function (){
        //alert($(this).attr('src'));

        $(this).attr('src', '/images/actions/loader.gif');
        img = $(this);

        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/adminadvert/setadvert_ver');?>',
            data: 'n_id='+$(this).attr('n_id'),
            success: function(msg){
                if(msg == 'act')
                {
                    img.attr('src', '/images/actions/on.gif');
                    img.attr('title', 'отменить верификацию');
                }
                if(msg == 'deact')
                {
                    img.attr('src', '/images/actions/off.gif');
                    img.attr('title', 'верифицировать');
                }
            }
        });

    });

    $('.imgnot_del').click(function (){
        //alert($(this).attr('src'));

        $(this).attr('src', '/images/actions/loader.gif');
        img = $(this);

        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/adminadvert/setadvert_del');?>',
            data: 'n_id='+$(this).attr('n_id'),
            success: function(msg){
                if(msg == 'act')
                {
                    img.attr('src', '/images/actions/on.gif');
                    img.attr('title', 'отметить как удаленное');
                }
                if(msg == 'deact')
                {
                    img.attr('src', '/images/actions/off.gif');
                    img.attr('title', 'отменить отметку об удалении');
                }
            }
        });

    });


    $('#checkall').change(function(){
        //$(this).attr('checked', true);
        if($(this).is(':checked') )
        {
            $('.chadvert').prop('checked', true);
        }
        else
        {
            $('.chadvert').prop('checked', false);
        }
    });


    $('#panel_r_id').change(function(){

        GetPanelProps();

    });


    function GetPanelProps()
    {
        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/adminadvert/getpanelprops');?>',
            data: $('#panel_form').serialize(),
            success: function(msg){
                $('#props_data').html(msg);
            }
        });

    }

    $('#change_props').click(function(){
        $('.chadvert:checked').each(function(i, obj){
            //console.log(obj);

            scroll_to_elem('advert_'+$(obj).attr('advid'), 1);
            $('#loader_'+$(obj).attr('advid')).css('display', 'block');


            $.ajax({
                async: false,
                dataType: 'json',
                type: 'POST',
                url: '<?= Yii::app()->createUrl('adminka/adminadvert/setnewprops');?>',
                data: $('#panel_form').serialize()+'&n_id='+$(obj).attr('advid'),
                success: function(msg){
                    if(msg['status'] == 'ok')
                    {
                        $('#advert_'+$(obj).attr('advid')).prop('checked', false);
                        $('#advert_'+$(obj).attr('advid')).attr('disabled', true);
                        $('#loader_'+$(obj).attr('advid')).css('display', 'none');
                        $('#result_'+$(obj).attr('advid')).html('<span style="background-color: #6cd114;">Операция выполнена успешно!</span>');
                    }
                    else
                    {
                        $('#loader_'+$(obj).attr('advid')).css('display', 'none');
                        $('#result_'+$(obj).attr('advid')).html('<span style="background-color: #f00;">Ошибка: '+msg['message']+'</span>');
                    }

                }
            });


        });
    });

    function scroll_to_elem(elem,speed) {
        if(document.getElementById(elem)) {
            var destination = jQuery('#'+elem).offset().top;
            jQuery("html,body").animate({scrollTop: destination}, speed);
        }
    }


</script>

























