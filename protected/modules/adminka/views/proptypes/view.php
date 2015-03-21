<?php
/* @var $this PropTypesController */
/* @var $model PropTypes */

$this->breadcrumbs=array(
	'Prop Types'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List PropTypes', 'url'=>array('index')),
	array('label'=>'Create PropTypes', 'url'=>array('create')),
	array('label'=>'Update PropTypes', 'url'=>array('update', 'id'=>$model->type_id)),
	array('label'=>'Delete PropTypes', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->type_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage PropTypes', 'url'=>array('admin')),
);
?>

<h1>View PropTypes #<?php echo $model->type_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'type_id',
		'name',
	),
)); ?>
