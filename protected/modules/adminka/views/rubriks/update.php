<?php
/* @var $this RubriksController */
/* @var $model Rubriks */

$this->breadcrumbs=array(
	'Rubriks'=>array('index'),
	$model->name=>array('view','id'=>$model->r_id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Rubriks', 'url'=>array('index')),
	array('label'=>'Create Rubriks', 'url'=>array('create')),
	array('label'=>'View Rubriks', 'url'=>array('view', 'id'=>$model->r_id)),
	array('label'=>'Manage Rubriks', 'url'=>array('admin')),
);
?>

<h1>Update Rubriks <?php echo $model->r_id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>