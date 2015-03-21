<?php
/* @var $this DefaultController */

$this->breadcrumbs=array(
	$this->module->id,
);
?>
<h1><?php echo $this->uniqueId . '/' . $this->action->id; ?></h1>

<table style="width: 100%;">
<tr>
    <td style="width: 200px; border: #000 solid 1px;">

    </td>

    <td style="border: #000 solid 1px; height: 500px;">

        <div id="map_canvas" style="width:100%; height:100%"></div>

        <div id="popupmenu" style="position: absolute; width: 200px; 300px; background-color: #eeeeee;">
            sdfsdfsdf

        </div>
    <?
        //Yii::app()->controller->module->map_api_key;
    ?>
    </td>
</tr>
</table>

