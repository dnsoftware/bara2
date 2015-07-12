<?php
/* @var $this FilterController */

$this->breadcrumbs=array(
	'Filter',
);
?>
<h1><?php echo $this->id . '/' . $this->action->id; ?></h1>


<?

foreach ($rubrik_groups as $rkey=>$rval)
{

?>
    <a href="<?= Yii::app()->createUrl($rval['path']);?>"><?= $rval['name'];?></a> (<?= $rval['cnt'];?>)
<?
}

deb::dump($search_adverts);
//deb::dump($rubrik_groups);

?>
