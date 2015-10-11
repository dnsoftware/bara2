<?php
/* @var $this PropertyController */

$this->breadcrumbs=array(
	'Property',
);
?>
<h1></h1>

<?
//deb::dump($rub_array);

?>
<style>
    .rubprops_item table
    {
        border-spacing: 1px;
    }
    .pointer
    {
        cursor: pointer;
    }

</style>

<div style="float: right;">
    <a href="<?= Yii::app()->createUrl('/adminka/property/importauto');?>">Импорт автомобилей</a>&nbsp;

</div>

<select id="r_id" class="selrub" onchange="">
    <option>--- выберите подрубрику ---</option>
<?
foreach ($rub_array as $rkey=>$rval)
{
?>
    <option disabled style="color:#000; font-weight: bold;" value="<?= $rval['parent']->r_id;?>"><?= $rval['parent']->name;?></option>
<?
    foreach ($rval['childs'] as $ckey=>$cval)
    {
    ?>
    <option value="<?= $cval->r_id;?>">&nbsp;<?= $cval->name;?></option>
    <?
    }
}
?>
</select>

<span style="cursor: pointer; text-decoration: underline;" onclick="$('#r_id').change();">Обновить</span>
&nbsp;&nbsp;&nbsp;
<span id="span_advert_list_item" style="cursor: pointer; text-decoration: underline;" onclick="GetRubrikAdvertListShablon($('#r_id'));">Шаблон вывода объявления в списке</span>

<div id="div_advert_list_item" style="display: none;">
    <table>
    <tr>
        <td>
        <form id="form_advert_list_item" onsubmit="SaveAdvertListItemShablon(); return false;">
            <input type="hidden" id="ali_r_id" name="r_id" value="">
            <textarea id="advert_list_item_shablon" name="advert_list_item_shablon" style="width: 600px; height: 200px;"></textarea>
            <br>
            <input type="submit" value="Сохранить шаблон">
        </form>
        </td>
        <td style="vertical-align: top;">
            [[advert_page_url]] - url объявления<br>
            [[mestopolozhenie]] - местоположение (город)<br>
            [[date_add]] - дата добавления<br>
            [[favoritstar_block]] - Звезда "Избранное"
        </td>
    </tr>
    </table>
</div>

<div style="color: #f00; display: none;" id="div_errors">

</div>

<div id="div_props" style="margin: 10px;">

</div>

<div style="position: absolute; left: 200px; top:140px; width: 800px; height: auto;
                background-color: #ddd; border: #555 solid 1px; display: none;" id="div_props_sprav" >

    <div style="float: right; margin: 2px; cursor: pointer;" onclick="$('#div_props_sprav').css('display', 'none');">X</div>
    <br clear="all">

    <div id="div_props_sprav_content" style="margin: 5px;">

    </div>

</div>

<script>
    $('.selrub').change(function ()
    {
        GetRubrikAdvertListShablon();

        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/property/ajax_rubprops');?>',
            data: 'r_id='+this.value,
            success: function(msg){
                $('#div_errors').html('');
                $('#div_errors').css('display', 'none');

                $('#div_props').html(msg);
            }
        });
    });

    function SaveAdvertListItemShablon()
    {
        $('#ali_r_id').val($('#r_id').val());

        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/property/ajax_save_advert_list_item_shablon');?>',
            data: $('#form_advert_list_item').serialize(),
            success: function(ret) {
                if(ret == 'ok')
                {
                    $('#span_advert_list_item').css('color', '#000;');
                    $('#div_advert_list_item').css('display', 'none');
                }
                if(ret == 'error')
                {
                    alert('Ошибка!');
                }

            }
        });
    }

    function GetRubrikAdvertListShablon()
    {
        $('#span_advert_list_item').css('color', '#f00;');
        $('#div_advert_list_item').css('display', 'block');

        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/property/get_rubrik_advert_list_shablon');?>',
            data: 'r_id='+$('#r_id').val(),
            success: function(ret) {
                if(ret != 'error')
                {
                    $('#advert_list_item_shablon').val(ret);
                }
                else
                {

                }
            }
        });

    }

    function edit_rubriks_props_row(rp_id)
    {
        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/property/ajax_edit_rubriks_props_row');?>',
            data: 'rp_id='+rp_id,
            success: function(ret) {
                //alert(ret);
                $('#rubprops_item_'+rp_id).html(ret);
            }
        });

    }

    function saveedit_rubriks_props(rp_id)
    {
        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/property/ajax_saveedit_rubriks_props_row');?>',
            data: $('#rpf_form_'+rp_id).serialize(),
            success: function(ret) {
                if(ret.indexOf('<!--ok-->') + 1)
                {
                    $('#rubriks_props_item_edit_'+rp_id).parent().html(ret);
                }
                else
                {
                    alert(ret);
                }
            }
        });

    }

    function del_rubriks_props(rp_id)
    {
        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/property/ajax_del_rubriks_props_row');?>',
            data: 'rp_id='+rp_id,
            success: function(ret) {
                if(ret.indexOf('<!--ok-->') + 1)
                {
                    $('#rubprops_item_'+rp_id).remove();
                }
                else
                {
                    alert('Доработать! Не удалять, если есть связанные записи!');
                }
            }
        });
    }

    function work_props_sprav(rp_id)
    {
        $('#div_props_sprav').css('display', 'block');
        $('#div_props_sprav_content').html('<img src="/images/ajaxload.gif">');

        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/propssprav/ajax_get_props_sprav');?>',
            data: 'rp_id='+rp_id,
            success: function(ret) {
                if(ret.indexOf('<!--ok-->') + 1)
                {
                    $('#div_props_sprav_content').html(ret);
                }
                else
                {
                    alert(ret);
                }
            }
        });


    }

</script>