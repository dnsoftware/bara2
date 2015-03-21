<?php
/* @var $this PropTypesController */
/* @var $model PropTypes */

$this->breadcrumbs=array(
	'Prop Types'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List PropTypes', 'url'=>array('index')),
	array('label'=>'Manage PropTypes', 'url'=>array('admin')),
);
?>

<h1>Create PropTypes</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>