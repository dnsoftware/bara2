<?php
/* @var $this PropTypesController */
/* @var $data PropTypes */

//deb::dump($this->types_params_array);
?>

<div class="view" id="div_prop_item_<?= $data->type_id;?>">

	<?php echo CHtml::link(CHtml::encode($data->type_id), array('view', 'id'=>$data->type_id)); ?>

	<?php echo CHtml::encode($data->name); ?>

    <div class="divparam" type_id="<?= $data->type_id;?>" style="float: right; cursor: pointer; ">Открыть</div>

    <br/>

    <div style="display: none;" id="div_params_<?= $data->type_id;?>">

        <div id="div_prop_types_params_form_error_<?= $data->type_id;?>">
        </div>

        <form class="prop_types_params_form" method="post"
              action="/index.php?r=adminka/proptypesparams/ajax_add_prop_types_params">

            <input type="hidden" name="params[type_id]" value="<?= $data->type_id;?>">

            <table style="width: 10px;">
            <tr>
                <td>
                    selector<br>
                    <input style="width: 120px;" type="text" name="params[selector]" value="">
                </td>
                <td>
                    name<br>
                    <input style="width: 120px;" type="text" name="params[name]" value="">
                </td>
                <td>
                    ptype<br>
                    <?
                    echo CHtml::dropDownList('params[ptype]', '', PropTypesParams::$ptype_spr, array('style'=>'width:120px;'));
                    ?>
                </td>
                <td>
                    maybe_count<br>
                    <?
                    echo CHtml::dropDownList('params[maybe_count]', '', PropTypesParams::$maybe_count_spr);
                    ?>
                </td>
                <td>
                    <br>
                    <input type="submit" value="Добавить">
                </td>
            </tr>
            </table>

        </form>

        <?
        //deb::dump($this->types_params_array);
        if (isset($this->types_params_array[$data->type_id]))
        {
            foreach ($this->types_params_array[$data->type_id] as $tkey=>$tval)
            {
                //deb::dump($tval);
                //$this->renderPartial('//adminka/proptypesparams/_tparam_item', array('model'=>$tval));
                $this->renderPartial('application.modules.adminka.views.proptypesparams._tparam_item', array('model'=>$tval));
            }
        }
        ?>

    </div>


</div>

