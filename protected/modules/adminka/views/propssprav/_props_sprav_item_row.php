<div class="props_sprav_item_row" id="props_sprav_item_row_<?= $model->ps_id;?>">
    <table style="margin: 1px; width: 690px;" id="tbl_props_sprav_item_row_<?= $model->ps_id;?>">
        <tr style="background-color: #fff;">
            <td style="background-color: #ccc;"><?= $model->ps_id;?></td>
            <td style="background-color: #ffe0f0; width: 140px;"><?= $model->value;?></td>
            <td style="background-color: #ffcef2; width: 140px;">
                <?= $model->sort_number;?>

            </td>
            <td style="background-color: #ffbd97;  width: 300px; text-align: center;" >
                <span class="pointer" onclick="edit_props_sprav_item_row(<?= $model->ps_id;?>);">Редактировать</span>
            </td>
        </tr>
    </table>
</div>
