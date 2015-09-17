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


if(count($new_towns) > 0)
{
    ?><br><b>Добавлены следующие города:</b><br><?
    foreach ($new_towns as $town)
    {
        ?>
        <div style="color: #259c1d;"><?= $town->name;?></div>
    <?
    }
}

?>
</div>


<div style="margin: 20px;">
    <a style="color: #bd362f;" href="<?= Yii::app()->createUrl('/adminka/sypexgeo/loadbase');?>">ОБНОВИТЬ БАЗУ IP АДРЕСОВ</a>
</div>
