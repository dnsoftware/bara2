<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;

?>


<div style="height: 400px; width: 100%; border: #000 solid 0px;">

<div style="width:500px; height: 100px; margin:60px auto 10px; border: #000 solid 0px;"">

    <div style="margin-bottom: 20px; width: 600px;">
    <?
    foreach ($countries as $ckey=>$cval)
    {
    ?>
        <a href="<?= Yii::app()->createUrl($cval->transname);?>"><?= $cval->name;?></a>&nbsp;
    <?
    }
    ?>
    </div>

    <div style="margin-bottom: 20px;">
    <b>
    <a href="<?= Yii::app()->createUrl('moskva');?>">Москва</a>&nbsp;
    <a href="<?= Yii::app()->createUrl('sankt_peterburg');?>">Санкт-Петербург</a>&nbsp;
    <a href="<?= Yii::app()->createUrl('rostov_na_donu');?>">Ростов-на-Дону</a>&nbsp;
    <a href="<?= Yii::app()->createUrl('krasnodar');?>">Краснодар</a>
    </b>
    </div>

    <?
    foreach($regions as $rkey=>$rval)
    {
    ?>
        <a href="<?= Yii::app()->createUrl($rval->transname);?>"><?= $rval->name;?></a>&nbsp;
    <?
    }
    ?>


</div>