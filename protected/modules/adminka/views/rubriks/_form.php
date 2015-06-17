<?php
/* @var $this RubriksController */
/* @var $model Rubriks */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'rubriks-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'parent_id'); ?>
		<?php echo $form->dropDownList($model,'parent_id', $parent_list); ?>
		<?php echo $form->error($model,'parent_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sort_num'); ?>
		<?php echo $form->textField($model,'sort_num'); ?>
		<?php echo $form->error($model,'sort_num'); ?>
	</div>

    Допустимые типы объявлений:<br>
    <div class="row">
    <? //$form->checkBoxList($empty_type, 'notice_type_id', Rubriks::$notice_type, array('style'=>'text-align: left;'));?>
    <?

    foreach (NoticeTypeRelations::$notice_type as $nkey=>$nval)
    {
        $checked = '';
        if(in_array($nkey, $empty_type->notice_type_id))
        {
            $checked = ' checked ';
        }
    ?>
        <div style="border: #003bb3 solid 1px; padding: 5px; margin: 3px;">
            <input type="checkbox" <?= $checked;?> name="NoticeTypeRelations[notice_type_id][<?= $nkey;?>]" value="<?= $nkey;?>"> <b><?= $nval;?></b><br>

            <div style="border: #aaaaaa solid 1px; margin: 3px; padding: 5px;">
            <?
            $checked = '';
            if(isset($types_records[$nkey]) && $types_records[$nkey]->image_field_tag == 1)
            {
                $checked = ' checked ';
            }
            ?>
            <input type="checkbox" <?= $checked;?> name="NoticeTypeRelations[image_field_tag][<?= $nkey;?>]" value="1"> Используются ли изображения?
            </div>

            <div style="border: #aaaaaa solid 1px; margin: 3px; padding: 5px;">
                Исключить поля:<br>
                <?
//                deb::dump($types_records);
                foreach (Rubriks::$notice_add_fields_exception as $ekey=>$eval)
                {
//                    deb::dump($ekey);
                    $checked = '';
                    if(isset($types_records[$nkey]) && in_array($ekey, $types_records[$nkey]->notice_fields_exception))
                    {
                        $checked = ' checked ';
                    }
                ?>
                <input type="checkbox"  <?= $checked;?>  name="NoticeTypeRelations[notice_fields_exception][<?= $nkey;?>][<?= $ekey;?>]" value="<?= $ekey;?>">
                    <?= $eval;?> (<?= $ekey;?>)<br>
                <?
                }
                ?>
            </div>

        </div>
    <?
    }

    ?>
    </div>



    <div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->