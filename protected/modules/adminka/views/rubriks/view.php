<?php
/* @var $this RubriksController */
/* @var $model Rubriks */

$this->breadcrumbs=array(
	'Rubriks'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Rubriks', 'url'=>array('index')),
	array('label'=>'Create Rubriks', 'url'=>array('create')),
	array('label'=>'Update Rubriks', 'url'=>array('update', 'id'=>$model->r_id)),
	array('label'=>'Delete Rubriks', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->r_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Rubriks', 'url'=>array('admin')),
);
?>

<h1>View Rubriks #<?php echo $model->r_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'r_id',
		'parent_id',
		'name',
	),
)); ?>
