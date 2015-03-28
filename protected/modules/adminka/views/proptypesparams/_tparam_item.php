<div class="tparam_item" id="tparam_item_<?= $model->pt_id;?>">
    <table style="margin: 1px; " id="row_tparam_<?= $model->pt_id;?>">
        <tr style="background-color: #fff;">
            <td style="background-color: #adffc8; width: 120px;"><?= $model->selector;?></td>
            <td style="background-color: #adffc8; width: 120px;"><?= $model->name;?></td>
            <td style="background-color: #adffc8;  width: 140px;"><?= PropTypesParams::$ptype_spr[$model->ptype];?></td>
            <td style="background-color: #adffc8;  width: 140px;"><?= PropTypesParams::$maybe_count_spr[$model->maybe_count];?></td>
            <td style="background-color: #adffc8;  width: 120px;" onclick="edit_rubprops_row(<?= $model->pt_id;?>);">Редактировать</td>
        </tr>
    </table>
</div>
