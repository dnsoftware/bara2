<div class="rubprops_item" id="rubprops_item_<?= $model->rp_id;?>">
    <table style="margin-bottom: 10px; width: 890px;" id="row_rubprops_<?= $model->rp_id;?>">
        <tr style="background-color: #fff;">
            <td  title="Селектор" style="background-color: #adffc8; width: 140px; font-weight: bold;"><?= $model->selector;?></td>
            <td title="Название" style="background-color: #adffc8; width: 140px; font-weight: bold;"><?= $model->name;?></td>
            <td title="Тип свойства (type_id)" style="background-color: #adffc8;  width: 180px;"><?= $props_type_array[$model->type_id];?></td>
            <td title="Тип выбора (vibor_type)" style="background-color: #adffc8;  width: 180px;"><?= RubriksProps::$vibor_type[$model->vibor_type];?></td>
            <td title="Тип сортировки свойств (sort_props_sprav)" style="background-color: #adffc8;  width: 180px;"><?= RubriksProps::$sort_sprav[$model->sort_props_sprav];?></td>
            <td title="" style="background-color: #ffe39f;  width: 300px; text-align: center;" rowspan="4">
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
            <td title="Участвует в иерархии? (hierarhy_tag)" style="background-color: #adffc8; <?= $style;?>">
                <?= Yii::app()->params['yesno'][$model->hierarhy_tag];?>
            </td>

            <td title="Уровень иерархии (hierarhy_level)" style="background-color: #adffc8; <?= $style;?>">
                <?= $model->hierarhy_level;?>
            </td>

            <td title="Сортировка при отображении (display_sort)" style="background-color: #adffc8;">
                <?= $model->display_sort;?>
            </td>

            <td title="Используется в фильтре? (use_in_filter)" style="background-color: #adffc8;">
                <?= Yii::app()->params['yesno'][$model->use_in_filter];?>
            </td>

            <td title="" style="background-color: #adffc8;">
                <div style="font-size: 10px;">Зависит от:</div>
                <?= $potential_parents[$model->parent_id];?>
            </td>
        </tr>
        <tr>
            <td title="Тип данных (ptype)" style="background-color: #adffc8;">
                <?= PropTypesParams::$ptype_spr[$model->ptype];?>
            </td>

            <td title="Обязательное поле?" style="background-color: #adffc8;">
                <?= Yii::app()->params['yesno'][$model->require_prop_tag];?>
            </td>

            <td title="Скрывать, если нет ни одного зависимого?" style="background-color: #adffc8;">
                <?= Yii::app()->params['yesno'][$model->hide_if_no_elems_tag];?>
            </td>

            <td title="все значения в фильтре?" style="background-color: #adffc8;">
                <?= Yii::app()->params['yesno'][$model->all_values_in_filter];?>
            </td>

            <td title="Тип фильтра (filter_type)" style="background-color: #adffc8;  width: 180px;"><?= RubriksProps::$filter_type[$model->filter_type];?></td>
        </tr>

        <tr>
            <td title="Код блока просмотра (view_block_type)" style="background-color: #adffc8;">
                <?= RubriksProps::$view_block_id[$model->view_block_id];?>
            </td>

            <td title="Правила валидации" style="background-color: #adffc8;">
                <?= $model->validate_rules;?>
            </td>

            <td title="" style="background-color: #adffc8;">

            </td>

            <td title="" style="background-color: #adffc8;">

            </td>

            <td title="" style="background-color: #adffc8;  width: 180px;"></td>
        </tr>
    </table>
</div>
