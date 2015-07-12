<div id="tparam_item_edit_<?= $model->pt_id;?>">
    <form class="prop_types_params_form" id="ptp_form_<?= $model->pt_id;?>" method="post"
          action="<?= Yii::app()->createUrl('adminka/proptypesparams/ajax_saveedit_rubprops_row');?>" onsubmit="saveedit_rubprops(<?= $model->pt_id;?>);return false;">

        <input type="hidden" name="params[pt_id]" value="<?= $model->pt_id;?>">
        <input type="hidden" name="params[type_id]" value="<?= $model->type_id;?>">

        <table style="margin: 1px; ">
            <tr>
                <td style="background-color: #adffc8; width: 120px;">
                    <input style="width: 115px;" type="text" name="params[selector]" value="<?= $model->selector;?>">
                </td>
                <td style="background-color: #adffc8; width: 120px;">
                    <input style="width: 115px;" type="text" name="params[name]" value="<?= $model->name;?>">
                </td>
                <td style="background-color: #adffc8; width: 140px;">
                    <?
                    echo CHtml::dropDownList('params[ptype]', $model->ptype, PropTypesParams::$ptype_spr, array('style'=>'width:140px;'));
                    ?>
                </td>
                <td style="background-color: #adffc8; width: 140px;">
                    <?
                    echo CHtml::dropDownList('params[maybe_count]', $model->maybe_count, PropTypesParams::$maybe_count_spr, array('style'=>'width:140px;'));
                    ?>
                </td>
                <td style="background-color: #adffc8; width: 120px;">
                    <input type="submit" value="Сохранить">

                    &nbsp;<span class="pointer" onclick="del_type_params(<?= $model->pt_id;?>);">Удалить</span>
                </td>
            </tr>
        </table>

    </form>
</div>
