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


<?
if(count($step_array) <= 0 && 0)
{
?>

<div style="margin: 20px;">
    <a style="color: #bd362f;" href="<?= Yii::app()->createUrl('/adminka/sypexgeo/update');?>">ЗАПУСТИТЬ ОБНОВЛЕНИЕ</a>
</div>

<?
}
?>