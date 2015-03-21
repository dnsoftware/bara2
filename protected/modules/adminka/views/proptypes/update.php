<?php
/* @var $this PropTypesController */
/* @var $model PropTypes */

$this->breadcrumbs=array(
	'Prop Types'=>array('index'),
	$model->name=>array('view','id'=>$model->type_id),
	'Update',
);

$this->menu=array(
	array('label'=>'List PropTypes', 'url'=>array('index')),
	array('label'=>'Create PropTypes', 'url'=>array('create')),
	array('label'=>'View PropTypes', 'url'=>array('view', 'id'=>$model->type_id)),
	array('label'=>'Manage PropTypes', 'url'=>array('admin')),
);
?>

<h1>Update PropTypes <?php echo $model->type_id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>