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

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
