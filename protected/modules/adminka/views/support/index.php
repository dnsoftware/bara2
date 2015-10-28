<?php
/* @var $this SupportController */

$this->breadcrumbs=array(
	'Support',
);

$this->renderPartial('/default/_admin_menu');

?>
<h1 style="margin: 10px; font-size: 14px;">Служебные скрипты</h1>

<a href="<?= Yii::app()->createUrl('/adminka/support/testmail');?>">Тестирование отправки мыла</a>

<a style="margin-left: 20px;" href="<?= Yii::app()->createUrl('/adminka/support/importolduserbase');?>">Импорт старой базы пользователей</a>

<a style="margin-left: 20px;" href="<?= Yii::app()->createUrl('/adminka/support/importoldadvertsmenu');?>">Импорт старых объявлений</a>

<a style="margin-left: 20px;" href="<?= Yii::app()->createUrl('/adminka/support/oldphonescorrect');?>">Корректировка телефонов в старом формате</a>

<br>
<a style="margin-left: 20px;" href="<?= Yii::app()->createUrl('/adminka/support/imageimportmenu');?>">Импорт изображений</a>





<div style="height: 200px;"></div>