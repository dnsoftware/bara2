<?php
/* @var $this PropertyController */

$this->breadcrumbs=array(
	'Property',
);
?>
<h1>Импорт автомобилей из базы basebuy.ru</h1>

<a href="<?= Yii::app()->createUrl('/adminka/property/importautomark');?>">Марки</a>&nbsp;
<a href="<?= Yii::app()->createUrl('/adminka/property/importcarmodel');?>">Модели</a>&nbsp;
<a href="<?= Yii::app()->createUrl('/adminka/property/importcaryears');?>">Годы выпуска</a>&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/property/importcarcharact?rp_id_selector=car_kuzov&id_characteristic=2');?>">Кузов</a>&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/property/importcarcharact?rp_id_selector=car_dvigatel&id_characteristic=12');?>">Двигатель</a>&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/property/importcarcharact?rp_id_selector=car_privod&id_characteristic=27');?>">Привод</a>&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/property/importcarcharact?rp_id_selector=car_korobka&id_characteristic=24');?>">Коробка передач</a>&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/property/importcarcharact?rp_id_selector=car_obyom_dvig&id_characteristic=13');?>">Объем двигателя</a>&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/property/importcarcharact?rp_id_selector=car_moshnost&id_characteristic=14');?>">Мощность, лс</a>&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/property/importcarcharact?rp_id_selector=car_kol_dveri&id_characteristic=3');?>">Количество дверей</a>&nbsp;
