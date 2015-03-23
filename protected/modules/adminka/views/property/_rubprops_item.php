<div class="rubprops_item" id="rubprops_item_<?= $model->rp_id;?>">
    <table style="margin: 1px; width: 890px;" id="row_rubprops_<?= $model->rp_id;?>">
        <tr style="background-color: #fff;">
            <td style="background-color: #adffc8; width: 140px;"><?= $model->selector;?></td>
            <td style="background-color: #adffc8; width: 140px;"><?= $model->name;?></td>
            <td style="background-color: #adffc8;  width: 180px;"><?= $props_type_array[$model->type_id];?></td>
            <td style="background-color: #adffc8;  width: 180px;"><?= RubriksProps::$sort_sprav[$model->sort_props_sprav];?></td>
            <td style="background-color: #ffe39f;  width: 300px; text-align: center;" >
                <span class="pointer" onclick="edit_rubriks_props_row(<?= $model->rp_id;?>);">Редактировать</span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <span class="pointer" onclick="work_props_sprav(<?= $model->rp_id;?>);">Справочник свойств</span>
            </td>
        </tr>
    </table>
</div>
