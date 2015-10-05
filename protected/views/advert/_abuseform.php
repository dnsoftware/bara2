
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'abusecaptcha-form',
        'enableAjaxValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'afterValidate'=>'js: afterValidateAbuse'
        ),
        'htmlOptions' => array('enctype'=>'multipart/form-data'),
        'action'=>Yii::app()->createUrl('/advert/sendabuse'),
    ));
    ?>


    <?php echo $form->hiddenField($formabuse,'n_id'); ?>
    <?php echo $form->hiddenField($formabuse,'class'); ?>
    <?php echo $form->hiddenField($formabuse,'type'); ?>

    <div class="row">
        <?php echo $form->labelEx($formabuse,'message'); ?>
        <?php echo $form->textArea($formabuse,'message', array(
            'style'=>'width: 350px; height: 100px;'
        )); ?>
        <?php echo $form->error($formabuse,'message'); ?>
    </div>

    <div class="row" style=" border: #000020 solid 0px; text-align: left; margin-left: 0px;">
        <table style="width: 350px; margin: 0; display: inline-block; padding: 0px;">
            <tr>
                <td style="padding-top: 0px; border: #000020 solid 0px; padding: 0px;">
                    <?php echo $form->labelEx($formabuse,'verifyCode'); ?>
                    <?php echo $form->textField($formabuse,'verifyCode', array(
                        'placeholder'=>'Текст с картинки',
                        'style'=>'width: 150px; margin:0; margin-top: 6px;'
                    )); ?>
                </td>
                <td style="border: #000020 solid 0px;">
                    <?php $this->widget('CCaptcha', array(
                        'captchaAction'=>'abuse_captcha',
                        'id'=>'reg_captcha',
                        'clickableImage'=>true,
                        'showRefreshButton'=>true,
                        'buttonLabel' => CHtml::image(Yii::app()->baseUrl.'/images/icons/reload.gif'),
                    ));
                    ?>
                </td>
            </tr>
        </table>

        <?php echo $form->error($formabuse,'verifyCode'); ?>

    </div>

    <div class="row submit">
        <?php echo CHtml::submitButton('Отправить', array('style'=>'font-size:14px;')); ?>
    </div>

    <?php $this->endWidget(); ?>

    <script>
        /*
        $('#abusecaptcha-form').submit(function(){
            return false;
        });
        */
    </script>


