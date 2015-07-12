<script>

    function props_sprav_row_add(rp_id, pt_id)
    {
        //props_sprav_form_
        $('#div_props_sprav_error_'+pt_id).html('<img src="/images/ajaxload.gif" width="30px;">');

        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/propssprav/ajax_addrow');?>',
            data: $('#props_sprav_form_'+pt_id).serialize(),
            success: function(ret) {
                if(ret.indexOf('<!--ok-->') + 1)
                {
                    $('#div_props_sprav_item_'+pt_id).append(ret);
                    $('#div_props_sprav_error_'+pt_id).html('');
                    //work_props_sprav(rp_id);
                }
                else
                {
                    $('#div_props_sprav_error_'+pt_id).html(ret);
                }
            }
        });
    }

    function edit_props_sprav_item_row(ps_id)
    {
        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/propssprav/ajax_editrow');?>',
            data: 'ps_id='+ps_id,
            success: function(ret) {
                //alert(ret);
                $('#props_sprav_item_row_'+ps_id).html(ret);
            }
        });

    }

    function saveedit_props_sprav_item_row(ps_id)
    {
        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/propssprav/ajax_saveedit_row');?>',
            data: $('#props_sprav_item_edit_form_'+ps_id).serialize(),
            success: function(ret) {
                if(ret.indexOf('<!--ok-->') + 1)
                {
                    $('#props_sprav_item_row_'+ps_id).html(ret);
                }
                else
                {
                    alert(ret);
                }
            }
        });

    }

    function del_props_sprav_item_row(ps_id)
    {
        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/propssprav/ajax_del_row');?>',
            data: 'ps_id='+ps_id,
            success: function(ret) {
                if(ret.indexOf('<!--ok-->') + 1)
                {
                    $('#props_sprav_item_row_'+ps_id).remove();
                }
                else
                {
                    alert(ret);
                }
            }
        });

    }


    function get_range_spr_select(rp_id, parent_rp_id, child_rp_id, ps_id)
    {
        $('#current_ps_id').val(ps_id);

        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/propssprav/ajax_get_range_spr_select');?>',
            data: 'rp_id='+rp_id+'&child_rp_id='+child_rp_id+'&ps_id='+ps_id,
            success: function(ret) {
                if(ret.indexOf('<!--ok-->') + 1)
                {
                    //alert(parent_rp_id);
                    if ($('div').is('#div_range_spr_select_'+child_rp_id))
                    {
                        //alert('есть');
                        $('#div_range_spr_select_'+child_rp_id).replaceWith(ret);
                    }
                    else
                    {
                    //console.log($('range_spr_select_'+rp_id).next());
                        $('#div_range_spr_select_'+parent_rp_id).append(ret);
                    }
                    //alert(rp_id+' '+parent_rp_id+' '+child_rp_id+' '+ps_id);
                    //$('#div_props_sprav_range').html('');
                    //$('.props_sprav_add_form').css('display', 'none');
                }
                else
                {
                    alert(ret);
                }
            }
        });

    }

    function get_range_spr_select_end(rp_id, parent_rp_id, ps_id)
    {
        $('#current_ps_id').val(ps_id);

        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/propssprav/ajax_get_range_spr_select_end');?>',
            data: 'rp_id='+rp_id+'&parent_rp_id='+parent_rp_id+'&ps_id='+ps_id,
            success: function(ret) {
                if(ret.indexOf('<!--ok-->') + 1)
                {
                    //alert(parent_rp_id);
                    //alert($('#span_relation_parent_name').length);

                    if (ps_id > 0)
                    {
                        $('#span_relation_parent_name').html($('#range_spr_select_'+parent_rp_id + '  option:selected').text());
                    }
                    else
                    {
                        $('#span_relation_parent_name').html('Нет связи, полный справочник');
                    }

                    $('#relation_parent_ps_id').attr('value', ps_id);
                    //alert($('.props_level').length+' '+ps_id );

                    $('#div_props_sprav_range').html(ret);
                    $('.props_sprav_add_form').css('display', 'block');
                }
                else
                {
                    alert(ret);
                }
            }
        });

    }

    function gettable_relation()
    {
        current_ps_id = $('#current_ps_id').val();
        parent2_rp_id = $('#parent2_rp_id').val();
        current_rp_id = $('#current_rp_id').val();

        if (parent2_rp_id == -1)
        {
            alert('Связь сама с собой невозможна!');
        }
        else
        {
            if (parent2_rp_id > 0)
            {
                current_ps_id = $('#range_spr_select_'+parent2_rp_id).val();
                //alert(current_rp_id);
                if (current_ps_id > 0)
                {
                    $('#current_ps_id').val(current_ps_id);

                    // Отправляем сформированную форму
                    send_frm_gettable_relation();
                }
                else
                {
                    alert('Заполните цепочку зависимостей!');
                }
            }
            else if (parent2_rp_id == 0)    // Выводится весь справочник на один уровень выше
            {
                // Отправляем форму как есть
                send_frm_gettable_relation();
            }
            else
            {
                alert('parent2_rp_id < 0, неизвестная ошибка');
            }

        }
    }

    function send_frm_gettable_relation()
    {
        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/propssprav/ajax_gettable_relation');?>',
            data: $('#frm_gettable_relation').serialize(),
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

<b><?= $model_rubriks_props->selector;?></b>&nbsp;&nbsp;|&nbsp;&nbsp;
<b><?= $model_rubriks_props->name;?></b>&nbsp;&nbsp;|&nbsp;&nbsp;
<b><?= $props_type_array[$model_rubriks_props->type_id];?></b>

<div style="float: right;">
<span style="cursor: pointer;" onclick="work_props_sprav(<?= $model_rubriks_props->rp_id;?>);">Обновить</span>&nbsp;
</div>
<br>
<br>

<div style="float: right;">
    <form id="frm_gettable_relation"  method="post" action="<?= Yii::app()->createUrl('adminka/propssprav/ajax_gettable_relation');?>"
           onsubmit="gettable_relation(); return false;">
        <input readonly style="width: 20px; background-color: #dedede;" type="text" id="current_ps_id" name="current_ps_id" value="<?= $current_ps_id;?>">
        <input readonly style="width: 20px; background-color: #dedede;" type="text" id="parent2_rp_id" name="parent2_rp_id" value="<?= $parent2_rp_id;?>">
        <input readonly style="width: 20px; background-color: #dedede;" type="text" id="current_rp_id" name="current_rp_id" value="<?= $rp_id;?>">

        <input type="submit" value="Связи">
    </form>
</div>
<?
    $this->renderPartial('_get_range_spr_select', array('range_spr'=>$range_spr, 'rp_id'=>$rp_id,
                            'child_rp_id'=>$child_rp_id, 'parent_rp_id'=>$parent_rp_id, 'range_spr_rubriks_props_row'=>$range_spr_rubriks_props_row));
?>

