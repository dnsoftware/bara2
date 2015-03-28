<div class="rubprops_item" id="rubprops_item_<?= $model->rp_id;?>">
    <table style="margin-bottom: 10px; width: 890px;" id="row_rubprops_<?= $model->rp_id;?>">
        <tr style="background-color: #fff;">
            <td style="background-color: #adffc8; width: 140px;"><?= $model->selector;?></td>
            <td style="background-color: #adffc8; width: 140px;"><?= $model->name;?></td>
            <td style="background-color: #adffc8;  width: 180px;"><?= $props_type_array[$model->type_id];?></td>
            <td style="background-color: #adffc8;  width: 180px;"><?= RubriksProps::$vibor_type[$model->vibor_type];?></td>
            <td style="background-color: #adffc8;  width: 180px;"><?= RubriksProps::$sort_sprav[$model->sort_props_sprav];?></td>
            <td style="background-color: #ffe39f;  width: 300px; text-align: center;" rowspan="2">
                <span class="pointer" onclick="edit_rubriks_props_row(<?= $model->rp_id;?>);">Редактировать</span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <span class="pointer" onclick="work_props_sprav(<?= $model->rp_id;?>);">Справочник свойств</span>
            </td>
        </tr>
        <tr>
            <?
            $style = '';
            if ($model->hierarhy_tag)
            {
                $style = 'color: #f00; font-weight: bold;';
            }
            ?>
            <td style="background-color: #adffc8; <?= $style;?>">
                <?= Yii::app()->params['yesno'][$model->hierarhy_tag];?>
            </td>

            <td style="background-color: #adffc8; <?= $style;?>">
                <?= $model->hierarhy_level;?>
            </td>

            <td style="background-color: #adffc8;">
                <?= $model->display_sort;?>
            </td>

            <td style="background-color: #adffc8;">
                <?= Yii::app()->params['yesno'][$model->use_in_filter];?>
            </td>

            <td style="background-color: #adffc8;">
                <div style="font-size: 10px;">Зависит от:</div>
                <?= $potential_parents[$model->parent_id];?>
            </td>
        </tr>
    </table>
</div>
