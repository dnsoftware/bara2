<?
//deb::dump($prop_types_params_row);
?>

<div style="border: #555555 solid 1px; margin: 5px; background-color: #eee;">

    <div style="color: #f79c11; font-weight: bold; font-size:14px; margin: 5px;"><?= $model_rubriks_props->name;?></div>


    <div id="div_props_sprav_error_<?= $prop_types_params_row->pt_id;?>">
    </div>

    <?
    //deb::dump($props_spav_records);
    if ( (count($props_spav_records)==0 && $prop_types_params_row->maybe_count == 'one') ||
        $prop_types_params_row->maybe_count != 'one' )
    {
    ?>
    <form id="props_sprav_form_<?= $prop_types_params_row->pt_id;?>" method="post" action="/index.php?r=adminka/propssprav/ajax_addrow"
        onsubmit="props_sprav_row_add(<?= $rp_id;?>, <?= $prop_types_params_row->pt_id;?>); return false;">

        <input type="hidden" name="field[rp_id]" value="<?= $rp_id;?>">
        <input type="hidden" readonly name="field[selector]" value="<?= $prop_types_params_row->selector;?>">
        <input type="hidden" name="field[type_id]" value="<?= $prop_types_params_row->type_id;?>">

        &nbsp;&nbsp;<span>Связано с</span>:
        <span style="color: #f00; font-size: 14px;" id="span_relation_parent_name">Нет связи, полный справочник</span><br>

        <table class="props_sprav_add_form" style="margin: 1px; width: 690px;">
            <tr>
                <td>
                    parent_id<br>
                    <input type="text" readonly style="width: 50px; background-color: #ddd;" id="relation_parent_ps_id" name="relation[parent_ps_id]" value="0">

                </td>
                <td>
                    value<br>
                    <input type="text" name="field[value]" value="">
                </td>
                <td>
                    <br>
                    <input type="submit" value="Добавить">
                </td>
            </tr>
        </table>
    </form>

    <?
    }


    $this->renderPartial('_props_sprav_item_type_rows', array('pt_id'=>$prop_types_params_row->pt_id, 'props_spav_records'=>$props_spav_records));
    ?>

</div>