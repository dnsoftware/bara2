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
                    echo CHtml::dropDownList('params[vibor_type]', $model->vibor_type,
                        RubriksProps::$vibor_type, array('style'=>'width:140px;'));
                    ?>
                </td>
                <td style="background-color: #adffc8; width: 180px;">
                    <?
                    echo CHtml::dropDownList('params[sort_props_sprav]', $model->sort_props_sprav, RubriksProps::$sort_sprav, array('style'=>'width:140px;'));
                    ?>
                </td>
                <td style="background-color: #adffc8; width: 300px; text-align: center;" rowspan="2">
                    <input type="submit" value="Сохранить">

                    &nbsp;<span class="pointer" onclick="del_rubriks_props(<?= $model->rp_id;?>);">Удалить</span>
                </td>
            </tr>
            <tr>
                <td style="background-color: #adffc8; width: 120px;">
                    <?
                    $checked = "";
                    if ($model->hierarhy_tag)
                    {
                        $checked = " checked ";
                    }
                    ?>
                    <input style="width: 115px;" <?= $checked;?> type="checkbox" name="params[hierarhy_tag]" value="1">
                </td>
                <td style="background-color: #adffc8; width: 120px;">
                    <input style="width: 50px;" type="text" name="params[hierarhy_level]" value="<?= $model->hierarhy_level;?>">
                </td>
                <td style="background-color: #adffc8;">
                    <input style="width: 50px;" type="text" name="params[display_sort]" value="<?= $model->display_sort;?>">
                </td>
                <td style="background-color: #adffc8;">
                    <?
                    $checked = "";
                    if ($model->use_in_filter)
                    {
                        $checked = " checked ";
                    }
                    ?>
                    <input style="width: 115px;" <?= $checked;?> type="checkbox" name="params[use_in_filter]" value="1">
                </td>
                <td style="background-color: #adffc8; width: 180px;">
                    <?
                    echo CHtml::dropDownList('params[parent_id]', $model->parent_id, $potential_parents, array('style'=>'width:140px;'));
                    ?>

                </td>
            </tr>
            <tr>
                <td style="background-color: #adffc8; width: 120px;">
                    <?
                    echo CHtml::dropDownList('params[ptype]', $model->ptype, PropTypesParams::$ptype_spr, array('style'=>'width:140px;'));
                    ?>
                </td>
                <td style="background-color: #adffc8; width: 120px;">
                    <?
                    $checked = "";
                    if ($model->require_prop_tag)
                    {
                        $checked = " checked ";
                    }
                    ?>
                    <input style="width: 115px;" <?= $checked;?> type="checkbox" name="params[require_prop_tag]" value="1">
                </td>
                <td style="background-color: #adffc8; width: 180px;">
                </td>
                <td style="background-color: #adffc8; width: 180px;">
                </td>
                <td style="background-color: #adffc8; width: 180px;">
                </td>
                <td style="background-color: #adffc8; width: 300px; text-align: center;" rowspan="2">
                </td>
            </tr>

        </table>

    </form>
</div>
