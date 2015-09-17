<?php
/* @var $this SypexgeoController */

$this->breadcrumbs=array(
	'Sypexgeo',
);

$this->renderPartial('/default/_admin_menu');

?>
<div style="margin: 20px;">
<?
if(count($step_array) > 0)
{
    foreach ($step_array as $message)
    {
        ?>
        <div style="color: #bd362f;"><?= $message;?></div>
        <?
    }
}
?>
</div>


<div style="margin: 20px;">

</div>
