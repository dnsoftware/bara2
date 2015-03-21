<?php
/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 21.01.15
 * Time: 16:12
 */

Yii::import('zii.widgets.grid.CDataColumn');

class PrimechanieDataColumn extends CDataColumn
{

    protected function renderDataCellContent($row, $data)
    {
    ?>
        <div style="border: #aaa solid 1px;">
        <!--<input id="inp_<?= $data->id;?>" type="submit" value="Примечание" class="input_prim">-->
        <form>

            <textarea id="area_<?= $data->id;?>" class="primechanie_cell" style="width: 100px; height: 30px; border: #ddd solid 1px;"><?= $data->primechanie;?></textarea>
        </form>
        </div>
    <?
    }

}