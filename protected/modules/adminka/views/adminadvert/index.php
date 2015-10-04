<?php
/* @var $this AdminadvertController */

$this->breadcrumbs=array(
	'Adminadvert',
);
?>

<h1 style="margin: 5px; font-size: 16px;">Админка: Работа с объявлениями</h1>

<table>
    <tr>
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
        ?>
        <tr class="trbotline" id="tradv_<?= $aval->n_id;?>" style="background-color: #fafafa;">
            <td class="f11">
                <?= date('d-m-Y', $aval->date_add);?><br>
                <?= date('H:i:s', $aval->date_add);?>
            </td>

            <td class="not_text">
                <div class="not_rub"><?= $rubriks[$rubriks[$aval->r_id]->parent_id]->name . " / " . $rubriks[$aval->r_id]->name;?></div>

                <div class="not_title" id="advtitul_<?= $aval->n_id;?>"><?= $aval->title;?></div>

                <div class="not_desc"><?= $aval->notice_text;?></div>

                <?
                if(count($props_array[$aval->n_id]['photos']) > 0)
                {
                    ?>
                    <img width="100" src="/photos/<?= Notice::getPhotoName($props_array[$aval->n_id]['photos'][0], "_thumb");?>">
                <?
                }
                ?>
            </td>

            <td class="not_act">
            <?
                $fname = 'on';
                $title = 'деактивировать';
                if($aval->active_tag == 0)
                {
                    $fname = 'off';
                    $title = 'активировать';
                }
            ?>
                <img class="imgnot_act" n_id="<?= $aval->n_id;?>" title="<?= $title;?>" src="/images/actions/<?= $fname;?>.gif">
            </td>

            <td class="not_ver">
                <?
                $fname = 'on';
                $title = 'отменить верификацию';
                if($aval->verify_tag == 0)
                {
                    $fname = 'off';
                    $title = 'верифицировать';
                }
                ?>
                <img class="imgnot_ver" n_id="<?= $aval->n_id;?>" title="<?= $title;?>" src="/images/actions/<?= $fname;?>.gif">
            </td>

            <td class="not_del" >
                <?
                $fname = 'on';
                $title = 'отметить как удаленное';
                if($aval->deleted_tag == 1)
                {
                    $fname = 'off';
                    $title = 'отменить отметку об удалении';
                }
                ?>
                <img class="imgnot_del" n_id="<?= $aval->n_id;?>" title="<?= $title;?>" src="/images/actions/<?= $fname;?>.gif">
            </td>

            <td class="not_delplus">
                <img class="imgnot_delplus" title="удалить навсегда" id="imgdel_<?= $aval->n_id;?>" src="/images/actions/delete.gif" onclick="advert_del(<?= $aval->n_id;?>);">
            </td>
        </tr>
    <?
    }
    ?>
</table>


<div id="advert_del" style="background-color: #eee; border: #aaa solid 1px; width: 250px; height: 100px; position: absolute; top: 50px; text-align: center; display: none;">
    <div>Объявление</div>
    <div style="font-weight: bold;" id="del_advert_name"></div>
    <div>будет удалено навсегда</div>

    <br>
    <span id="span_advkill" style="border: #aaa solid 1px; padding: 3px; cursor: pointer;" >&nbsp;Удалить&nbsp;</span>
    <span style="border: #aaa solid 1px; padding: 3px; cursor: pointer; margin-left: 50px;" onclick="$('#advert_del').css('display', 'none');">&nbsp;Отмена&nbsp;</span>

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


</script>

























