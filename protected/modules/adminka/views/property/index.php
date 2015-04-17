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

<select id="select_subrub" class="selrub" onchange="">
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

<span style="cursor: pointer; text-decoration: underline;" onclick="$('#select_subrub').change();">Обновить</span>

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
        $.ajax({
            type: 'POST',
            url: '/index.php?r=adminka/property/ajax_rubprops',
            data: 'r_id='+this.value,
            success: function(msg){
                $('#div_errors').html('');
                $('#div_errors').css('display', 'none');

                $('#div_props').html(msg);
            }
        });
    });


    function edit_rubriks_props_row(rp_id)
    {
        $.ajax({
            type: 'POST',
            url: '/index.php?r=adminka/property/ajax_edit_rubriks_props_row',
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
            url: '/index.php?r=adminka/property/ajax_saveedit_rubriks_props_row',
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
            url: '/index.php?r=adminka/property/ajax_del_rubriks_props_row',
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
            url: '/index.php?r=adminka/propssprav/ajax_get_props_sprav',
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