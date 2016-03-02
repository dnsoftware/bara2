<?
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/notice/writeauthor.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/notice/writeauthor.js', CClientScript::POS_END);

?>

<div class="form" id="modal_writeauthor">
    <span id="modal_writeauthor_close">X</span>

    <div id="modal_writeauthor_content">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'writeauthor-form',
        'enableAjaxValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'afterValidate'=>'js: afterValidate'
        ),
        'htmlOptions' => array('enctype'=>'multipart/form-data'),
        'action'=>Yii::app()->createUrl('/advert/writeauthor'),
    ));
    ?>


    <?php //echo $form->errorSummary(array($writeauthor)); ?>

    <?php echo $form->hiddenField($writeauthor,'n_id', array('')); ?>

    <div class="row">
        <?php echo $form->labelEx($writeauthor,'name'); ?>
        <?php echo $form->textField($writeauthor,'name', array(
            'placeholder'=>'Ваше имя',
            'style'=>'width: 350px;'
        )); ?>
        <?php echo $form->error($writeauthor,'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($writeauthor,'email'); ?>
        <?php echo $form->textField($writeauthor,'email', array(
            'placeholder'=>'Электронная почта',
            'style'=>'width: 350px;'
        )); ?>
        <?php echo $form->error($writeauthor,'email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($writeauthor,'message'); ?>
        <?php echo $form->textArea($writeauthor,'message', array(
            'style'=>'width: 350px; height: 100px;'
        )); ?>
        <?php echo $form->error($writeauthor,'message'); ?>
    </div>

    <div class="row" id="authcaptcha">
        <table>
            <tr>
                <td id="td_captext">
                    <?php echo $form->labelEx($writeauthor,'verifyCode'); ?>
                    <?php echo $form->textField($writeauthor,'verifyCode', array(
                        'placeholder'=>'Текст с картинки',
                        'style'=>'width: 150px; margin:0; margin-top: 6px;'
                    )); ?>
                </td>
                <td id="td_captcha">
                    <?php $this->widget('CCaptcha', array(
                        'id'=>'reg_captcha',
                        'clickableImage'=>true,
                        'showRefreshButton'=>true,
                        'buttonLabel' => CHtml::image(Yii::app()->baseUrl.'/images/icons/reload.gif'),
                    ));
                    ?>
                </td>
            </tr>
        </table>

        <?php echo $form->error($writeauthor,'verifyCode'); ?>

    </div>

    <div class="row submit">
        <?php echo CHtml::submitButton('Отправить', array('style'=>'font-size:14px;')); ?>
    </div>

    <?php $this->endWidget(); ?>

    </div>

</div><!-- form -->

<div id="modal_writeauthor_overlay"></div>

