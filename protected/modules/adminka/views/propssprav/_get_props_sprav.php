<script>

    function props_sprav_row_add(rp_id, pt_id)
    {
        //props_sprav_form_
        $('#div_props_sprav_error_'+pt_id).html('<img src="/images/ajaxload.gif" width="30px;">');

        $.ajax({
            type: 'POST',
            url: '/index.php?r=adminka/propssprav/ajax_addrow',
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
            url: '/index.php?r=adminka/propssprav/ajax_editrow',
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
            url: '/index.php?r=adminka/propssprav/ajax_saveedit_row',
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
            url: '/index.php?r=adminka/propssprav/ajax_del_row',
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

    //div_props_sprav_item_

</script>

<b><?= $model_rubriks_props->selector;?></b>&nbsp;&nbsp;|&nbsp;&nbsp;
<b><?= $model_rubriks_props->name;?></b>&nbsp;&nbsp;|&nbsp;&nbsp;
<b><?= $props_type_array[$model_rubriks_props->type_id];?></b>
<span style="float: right; cursor: pointer;" onclick="work_props_sprav(<?= $model_rubriks_props->rp_id;?>);">Обновить</span>

