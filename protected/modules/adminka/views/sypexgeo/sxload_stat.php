<?php
/* @var $this SypexgeoController */

$this->breadcrumbs=array(
	'Sypexgeo',
);

$this->renderPartial('/default/_admin_menu');

?>

<div style="margin: 20px;">

    <div><?= $countries_kol;?> стран</div>
    <div><?= $regions_kol;?> регионов</div>
    <div><?= $towns_kol;?> городов</div>

</div>


<div style="margin: 20px;">
    <a style="color: #bd362f;" href="<?= Yii::app()->createUrl('/adminka/sypexgeo/update', array('selector'=>'unicalizate'));?>">ПРОДОЛЖИТЬ</a>
</div>

