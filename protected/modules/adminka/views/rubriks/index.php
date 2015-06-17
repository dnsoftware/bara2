<?php
/* @var $this RubriksController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Rubriks',
);

$this->menu=array(
	array('label'=>'Create Rubriks', 'url'=>array('create')),
	array('label'=>'Manage Rubriks', 'url'=>array('admin')),
);
?>

<h1>Rubriks</h1>

<?php
//deb::dump($rub_array);
foreach ($rub_array as $rval)
{
?>
<div style="margin-left:10px;">
    <?= $rval['parent']->name;?>
    <?
    foreach ($rval['childs'] as $cval)
    {
    ?>
    <div style="margin-left:30px;">
        <?= $cval->name;?>
    </div>
    <?
    }
    ?>
</div>
<?
}

?>
