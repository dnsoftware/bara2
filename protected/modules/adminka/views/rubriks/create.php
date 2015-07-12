<?php
/* @var $this RubriksController */
/* @var $model Rubriks */

$this->breadcrumbs=array(
	'Rubriks'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Rubriks', 'url'=>array('index')),
	array('label'=>'Manage Rubriks', 'url'=>array('admin')),
);
?>

<h1>Create Rubriks</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'types'=>$types, 'parent_list'=>$parent_list, /*'empty_type'=>$empty_type,*/ 'types_records'=>$types_records)); ?>