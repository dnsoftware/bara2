<?
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.maskedinput.min.js');
?>

<?php $this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Profile");
$this->breadcrumbs=array(
	UserModule::t("Profile")=>array('profile'),
	UserModule::t("Edit"),
);
$this->menu=array(
	((UserModule::isAdmin())
		?array('label'=>UserModule::t('Manage Users'), 'url'=>array('/user/admin'))
		:array()),
    array('label'=>UserModule::t('List User'), 'url'=>array('/user')),
    array('label'=>UserModule::t('Profile'), 'url'=>array('/user/profile')),
    array('label'=>UserModule::t('Change password'), 'url'=>array('changepassword')),
    array('label'=>UserModule::t('Logout'), 'url'=>array('/user/logout')),
);

?><h1 style="font-size: 20px;">Редактирование профиля</h1>

<?php if(Yii::app()->user->hasFlash('profileMessage')): ?>
<div class="success">
<?php echo Yii::app()->user->getFlash('profileMessage'); ?>
</div>
<?php endif; ?>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'profile-form',
	'enableAjaxValidation'=>true,
	'htmlOptions' => array('enctype'=>'multipart/form-data'),
)); ?>

    <br>
	<?php echo $form->errorSummary(array($model,$profile)); ?>

<?php
/*
		$profileFields=$profile->getFields();
		if ($profileFields) {
			foreach($profileFields as $field) {
			?>
	<div class="row">
		<?php echo $form->labelEx($profile,$field->varname);
		
		if ($widgetEdit = $field->widgetEdit($profile)) {
			echo $widgetEdit;
		} elseif ($field->range) {
			echo $form->dropDownList($profile,$field->varname,Profile::range($field->range));
		} elseif ($field->field_type=="TEXT") {
			echo $form->textArea($profile,$field->varname,array('rows'=>6, 'cols'=>50));
		} else {
			echo $form->textField($profile,$field->varname,array('size'=>60,'maxlength'=>(($field->field_size)?$field->field_size:255)));
		}
		echo $form->error($profile,$field->varname); ?>
	</div>	
			<?php
			}
		}
*/

    //deb::dump($model);
?>
    <div class="row" style="">
        <?php echo $form->radioButtonList($model,'user_type', RegistrationForm::$user_types,
            array(
                'template'=>'{input} {label}',
                'style'=>'display: inline; ',
                'separator'=>'&nbsp;&nbsp;&nbsp;',
                'labelOptions'=>array('style'=>'display: inline;_font-size: 18px;')
            )
        ); ?>
        <?php echo $form->error($model,'user_type'); ?>
    </div>

    <?
    $company_display = 'none';
    if($model->user_type == 'c')
    {
        $company_display = 'display';
    }
    ?>
    <div class="row" id="div_company_name" style="display: <?= $company_display;?>;">
        <?php echo $form->labelEx($model,'company_name'); ?>
        <?php echo $form->textField($model,'company_name', array(
            //'placeholder'=>'Название компании',
            //'style'=>'font-size:18px; width: 350px;'
        )); ?>
        <?php echo $form->error($model,'company_name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'privat_name'); ?>
        <?php echo $form->textField($model,'privat_name', array(
            //'placeholder'=>'Ваше имя',
            //'style'=>'font-size:18px; width: 350px;'
        )); ?>
        <?php echo $form->error($model,'privat_name'); ?>
    </div>



    <div class="row" style="display: none;">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>


	<div class="row">
        <?
        $email_visible = 'block';
        if ($params['is_social_email']){
            $model->email = '';
            //$email_visible = 'none';
            ?>
            <div style="color: #ff0000;">Требуется заполнить</div>
            <?
        }
        ?>
        <div style="display: <?= $email_visible;?>;">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'email'); ?>
        </div>
	</div>

    <div class="row">
        <span style="display: none;"><?php echo $form->textField($modelphone,'ph_id'); ?></span>

        <?php echo $form->dropDownList($modelphone,'c_id', $countries_array, array(
            //'style'=>'font-size:18px; width: 250px;'
        )); ?>
        <?php echo $form->textField($modelphone,'phone', array(
            //'placeholder'=>'Номер телефона',
            //'style'=>'font-size:18px; width: 150px;'
        )); ?>

        <?
        if($modelphone->ph_id > 0)
        {
            if($modelphone->verify_tag == 1)
            {
                echo "верифицирован";
            }
            else
            {
                echo "не верифицирован";
            }
        }
        ?>

        <?php echo $form->error($modelphone,'phone'); ?>
    </div>

    <div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? UserModule::t('Create') : UserModule::t('Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->


<script>

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

    $('#User_user_type_0').click(function()
    {
        $('#div_company_name').css('display', 'none');
    });

    $('#User_user_type_1').click(function()
    {
        $('#div_company_name').css('display', 'block');
    });

</script>