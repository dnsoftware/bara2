<?php
/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 09.09.15
 * Time: 17:35
 */


if(Yii::app()->user->id == 1)
{
?>

<div style="margin: 5px;">
<a href="<?= Yii::app()->createUrl('/adminka/');?>">Админка</a>
&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/adminadvert/index');?>">Объявления</a>
&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/sypexgeo/index');?>">Обновление Sypexgeo</a>
&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/support/seo');?>">SEO</a>

&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/support/index');?>">Служебные</a>

&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/support/sphinx');?>">Sphinx индексы</a>

&nbsp;

<a href="<?= Yii::app()->createUrl('/adminka/support/searchstat');?>">Поисковая статистика</a>

</div>

<?
}
else
{
?>
    <a href="<?= Yii::app()->createUrl('/adminka/adminadvert/index');?>">Объявления</a>
<?
}
?>