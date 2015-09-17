<?php
/* @var $this SypexgeoController */

$this->breadcrumbs=array(
	'Sypexgeo',
);

$this->renderPartial('/default/_admin_menu');
?>

<div style="margin: 20px;">
    <a style="color: #bd362f;" href="<?= Yii::app()->createUrl('/adminka/sypexgeo/load', array('selector'=>'load_and_unzip'));?>">ЗАГРУЗКА ДАННЫХ</a>
</div>


