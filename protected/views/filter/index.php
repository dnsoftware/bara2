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

?>
<table>
<?
foreach($search_adverts as $key=>$val)
{
?>
<tr style="">
    <td style="width: 125px;">
    <?
    if(count($props_array[$key]['photos']) > 0)
    {
    ?>
        <img width="120" src="/photos/<?= $props_array[$key]['photos'][0];?>">
    <?
    }
    ?>
    </td>
    <td>
    <?= $props_array[$key]['props_display'];?>
    </td>
</tr>
<?
}
?>
</table>
<?

//deb::dump($search_adverts);
//deb::dump($rubrik_groups);

?>
