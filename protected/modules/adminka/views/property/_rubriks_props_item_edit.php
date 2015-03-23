<div id="rubriks_props_item_edit_<?= $model->rp_id;?>">
    <form class="rubriks_props_form" id="rpf_form_<?= $model->rp_id;?>" method="post"
          action="/index.php?r=adminka/property/ajax_saveedit_rubriks_props_row" onsubmit="saveedit_rubriks_props(<?= $model->rp_id;?>);return false;">

        <input type="hidden" name="params[rp_id]" value="<?= $model->rp_id;?>">
        <input type="hidden" name="params[r_id]" value="<?= $model->r_id;?>">

        <table style="margin: 1px; width: 890px;">
            <tr>
                <td style="background-color: #adffc8; width: 120px;">
                    <input style="width: 115px;" type="text" name="params[selector]" value="<?= $model->selector;?>">
                </td>
                <td style="background-color: #adffc8; width: 120px;">
                    <input style="width: 115px;" type="text" name="params[name]" value="<?= $model->name;?>">
                </td>
                <td style="background-color: #adffc8; width: 180px;">
                    <?
                    echo CHtml::dropDownList('params[type_id]', $model->type_id, $props_type_array, array('style'=>'width:140px;'));
                    ?>
                </td>
                <td style="background-color: #adffc8; width: 180px;">
                    <?
                    echo CHtml::dropDownList('params[sort_props_sprav]', $model->sort_props_sprav, RubriksProps::$sort_sprav, array('style'=>'width:140px;'));
                    ?>
                </td>
                <td style="background-color: #adffc8; width: 300px; text-align: center;">
                    <input type="submit" value="Сохранить">

                    &nbsp;<span class="pointer" onclick="del_rubriks_props(<?= $model->rp_id;?>);">Удалить</span>
                </td>
            </tr>
        </table>

    </form>
</div>
