<?
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.maskedinput.min.js');
?>

<?php $this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Registration");
$this->breadcrumbs=array(
    UserModule::t("Registration"),
);

?>


<div style="text-align: center; margin-top: 100px; margin-bottom: 140px;">


<div style="background-color: #efefef; margin: 0 auto; width: 580px; padding: 20px;">

<h1 style="font-size: 20px; margin-bottom: 30px;"><?php echo UserModule::t("Registration"); ?></h1>

<?php if(Yii::app()->user->hasFlash('registration')): ?>
    <div class="success">
        <?php echo Yii::app()->user->getFlash('registration'); ?>
    </div>
<?php else: ?>

    <div class="form">
        <?php $form=$this->beginWidget('UActiveForm', array(
            'id'=>'registration-form',
            'enableAjaxValidation'=>true,
            'disableAjaxValidationAttributes'=>array('RegistrationForm_verifyCode'),
            'clientOptions'=>array(
                'validateOnSubmit'=>true,
            ),
            'htmlOptions' => array('enctype'=>'multipart/form-data'),
        )); ?>


        <?php echo $form->errorSummary(array($model,$profile)); ?>

        <?
        /*
        ?>
        <div class="row">
            <?php echo $form->labelEx($model,'username'); ?>
            <?php echo $form->textField($model,'username'); ?>
            <?php echo $form->error($model,'username'); ?>
        </div>
        /*
        <?
        */
        ?>

        <div class="row" style="">
            <?php echo $form->radioButtonList($model,'user_type', RegistrationForm::$user_types,
                array(
                    'template'=>'{input} {label}',
                    'style'=>'display: inline; ',
                    'separator'=>'&nbsp;&nbsp;&nbsp;',
                    'labelOptions'=>array('style'=>'display: inline;font-size: 18px;'))
                ); ?>
            <?php echo $form->error($model,'user_type'); ?>
        </div>
<?
//deb::dump($model);
?>
        <div class="row" id="div_company_name" style="display: none;">
            <?php echo $form->textField($model,'company_name', array(
                'class'=>'regfield',
                'placeholder'=>'Название компании',
                'style'=>'font-size:18px; width: 350px;'
            )); ?>
            <div class="reg_hint">Название компании</div>
            <?php echo $form->error($model,'company_name'); ?>
        </div>

        <div class="row" id="div_privat_name">
            <?php echo $form->textField($model,'privat_name', array(
                'class'=>'regfield',
                'placeholder'=>'Ваше имя',
                'style'=>'font-size:18px; width: 350px;'
            )); ?>
            <div class="reg_hint">Ваше имя</div>
            <?php echo $form->error($model,'privat_name'); ?>
        </div>

        <div class="row">
            <?php echo $form->textField($model,'email', array(
                'class'=>'regfield',
                'placeholder'=>'Электронная почта',
                'style'=>'font-size:18px; width: 350px;'
            )); ?>
            <div class="reg_hint">Ваш адрес электронной почты. Указывайте реальный адрес, т.к. на него придет письмо, необходимое для завершения регистрации</div>
            <?php echo $form->error($model,'email'); ?>
        </div>

<?
        if(isset(Yii::app()->request->cookies['geo_mycountry']->value) &&
            Yii::app()->request->cookies['geo_mycountry']->value > 0 &&
            !isset($_POST['UserPhones']['c_id']))
        {
            $modelphone->c_id = intval(Yii::app()->request->cookies['geo_mycountry']->value);
        }
?>

        <div class="row">
            <?php echo $form->dropDownList($modelphone,'c_id', $countries_array, array(
                'style'=>'font-size:18px; width: 250px;'
            )); ?>
            <?php echo $form->textField($modelphone,'phone', array(
                'class'=>'regfield',
                'placeholder'=>'Номер телефона',
                'style'=>'font-size:18px; width: 150px;'
            )); ?>
            <div class="reg_hint">Для пользователей региона Россия возможно указывать только мобильные номера телефонов. Указывайте только реальные номера, так как прежде чем публиковать их в объявлениях необходимо будет подтвердить владение телефонным номером</div>
            <?php echo $form->error($modelphone,'phone'); ?>
        </div>

        <!--
        <div class="row">
            <select id="select_country_code" name="Phone[client_phone_c_id]">
                <?
                foreach($countries_array as $ckey=>$cval)
                {
                ?>
                    <option value="<?= $ckey;?>"><?= $cval;?></option>
                <?
                }
                ?>
            </select>

            <input type="text" name="Phone[client_phone]" id="client_phone" value="">
        </div>
        -->

        <div class="row">
            <?php echo $form->passwordField($model,'password', array(
                'class'=>'regfield',
                'placeholder'=>'Пароль',
                'style'=>'font-size:18px; width: 350px;'
            )); ?>
            <div class="reg_hint">Наберите пароль</div>
            <?php echo $form->error($model,'password'); ?>
            <!--
            <p class="hint">
                <?php echo UserModule::t("Minimal password length 4 symbols."); ?>
            </p>
            -->
        </div>

        <div class="row">
            <?php echo $form->passwordField($model,'verifyPassword', array(
                'class'=>'regfield',
                'placeholder'=>'Пароль еще раз',
                'style'=>'font-size:18px; width: 350px;'
            )); ?>
            <div class="reg_hint">Подтвердите пароль</div>
            <?php echo $form->error($model,'verifyPassword'); ?>
        </div>


        <?php
        /*
        $profileFields=$profile->getFields();
        if ($profileFields) {
            foreach($profileFields as $field) {
                ?>
                <div class="row">
                    <?php echo $form->labelEx($profile,$field->varname); ?>
                    <?php
                    if ($widgetEdit = $field->widgetEdit($profile)) {
                        echo $widgetEdit;
                    } elseif ($field->range) {
                        echo $form->dropDownList($profile,$field->varname,Profile::range($field->range));
                    } elseif ($field->field_type=="TEXT") {
                        echo$form->textArea($profile,$field->varname,array('rows'=>6, 'cols'=>50));
                    } else {
                        echo $form->textField($profile,$field->varname,array('size'=>60,'maxlength'=>(($field->field_size)?$field->field_size:255)));
                    }
                    ?>
                    <?php echo $form->error($profile,$field->varname); ?>
                </div>
            <?php
            }
        }
        */
        ?>
        <?php if (UserModule::doCaptcha('registration')): ?>
            <div class="row" style=" border: #000020 solid 0px; text-align: left; padding-left: 108px;">
                <table style="width: 350px; margin: 0; display: inline-block; margin-left: 0px;">
                <tr>
                <td style="padding-top: 0px; border: #000020 solid 0px;">
                <?php echo $form->textField($model,'verifyCode', array(
                    'class'=>'regfield',
                    'placeholder'=>'Текст с картинки',
                    'style'=>'font-size:18px; width: 150px; margin:0; margin-top: 6px;'
                )); ?>
                  <div class="reg_hint">Защита от автоматических регистраций</div>
                </td>
                <td style="border: #000020 solid 0px;">
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

                <?php echo $form->error($model,'verifyCode'); ?>

                <!--
                <p class="hint"><?php echo UserModule::t("Please enter the letters as they are shown in the image above."); ?>
                    <br/><?php echo UserModule::t("Letters are not case-sensitive."); ?></p>
                -->
            </div>
        <?php endif; ?>

        <div class="row submit">
            <?php echo CHtml::submitButton(UserModule::t("Register"), array('style'=>'font-size:18px;')); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div><!-- form -->
<?php endif; ?>


</div>

<div class="row" style="margin-top: 30px;">
    <span style="color: #000; font-size: 18px;">Уже зарегистрированы?</span>
    <?php echo CHtml::link('Входите!',Yii::app()->getModule('user')->loginUrl, array('style'=>'font-size:18px;', 'class'=>'hoverunderline')); ?>
</div>


</div>

<style>
    #reg_captcha
    {

    }

    #reg_captcha_button
    {
        float: right;
        margin-left: 0px;
        margin-top: 8px;
    }
</style>


<script type="text/javascript">
    jQuery(function($){
//        $("#client_phone").mask("999 999-99-99");

        $.mask.definitions['x'] = "[0-9]";
        $.mask.definitions['9'] = "";
        $("#UserPhones_phone").mask(mask_array[$('#UserPhones_c_id').val()]);
        if($('#UserPhones_c_id').val() == <?= Yii::app()->params['russia_id'];?>)
        {
            $("#UserPhones_phone").mask('(9xx) xxx-xx-xx');
        }
        strlen = $("#UserPhones_c_id option:selected").text().length;
        $("#UserPhones_c_id").css('width', strlen*11.5);


        user_type = $('input[name="RegistrationForm[user_type]"]:checked').val();
        if(user_type == 'c')
        {
            $('#div_company_name').css('display', 'block');
        }
        if(user_type == 'p')
        {
            $('#div_company_name').css('display', 'none');
        }

    });

    var mask_array = new Array();
    <?
    foreach($mask_array as $mkey=>$mask)
    {
    ?>
    mask_array[<?= $mkey;?>] = '<?= $mask;?>';
    <?
    }
    ?>

    $('#UserPhones_c_id').change(function()
    {
        $("#UserPhones_phone").mask(mask_array[$('#UserPhones_c_id').val()]);
        if($('#UserPhones_c_id').val() == <?= Yii::app()->params['russia_id'];?>)
        {
            $("#UserPhones_phone").mask('(9xx) xxx-xx-xx');
        }

        //$("#UserPhones_c_id").css('width', '100%');
        strlen = $("#UserPhones_c_id option:selected").text().length;
        $("#UserPhones_c_id").css('width', strlen*11.5);
    });

    $('#RegistrationForm_user_type_0').click(function()
    {
        $('#div_company_name').css('display', 'none');
    });

    $('#RegistrationForm_user_type_1').click(function()
    {
        $('#div_company_name').css('display', 'block');
    });

    $('.regfield').focus(function(){
        $('.reg_hint').css('display', 'none');
        field = $(this);
        field.next('.reg_hint').css('display', 'inline-block');
        field_coord = field.offset();

        if(field.attr('id') == 'RegistrationForm_verifyCode')
        {
            field.next('.reg_hint').offset({left: field.width() + field_coord.left + 200});
        }

    });

    $('.regfield').focusout(function(){
        $(this).next('.reg_hint').css('display', 'none');

    });
</script>

<style>
    .reg_hint
    {
        position: absolute; margin-left: 10px; margin-top: 3px;;
        display: none;
        padding: 5px;
        background-color: #DBF0F9; box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }
</style>