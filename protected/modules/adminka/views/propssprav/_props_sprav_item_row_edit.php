<form class="" id="props_sprav_item_edit_form_<?= $model->ps_id;?>" method="post"
      action="<?= Yii::app()->createUrl('adminka/propssprav/ajax_saveedit_row');?>" onsubmit="saveedit_props_sprav_item_row(<?= $model->ps_id;?>);return false;">

    <input type="hidden" name="params[ps_id]" value="<?= $model->ps_id;?>">

    <table style="margin: 1px; width: 590px;">
        <tr>
            <td style="background-color: #adffc8; width: 120px;">
                <input style="width: 115px;" type="text" name="params[value]" value="<?= $model->value;?>">
            </td>
            <td style="background-color: #adffc8; width: 120px;">
                <input style="width: 115px;" type="text" name="params[sort_number]" value="<?= $model->sort_number;?>">
            </td>
            <td style="background-color: #adffc8; width: 300px; text-align: center;">
                <input type="submit" value="Сохранить">

                &nbsp;<span class="pointer" onclick="del_props_sprav_item_row(<?= $model->ps_id;?>);">Удалить</span>
            </td>
        </tr>
    </table>

</form>
