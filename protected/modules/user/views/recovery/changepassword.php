<?php $this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Change Password");
$this->breadcrumbs=array(
	UserModule::t("Login") => array('/user/login'),
	UserModule::t("Change Password"),
);
?>

<div style="text-align: center; margin-top: 100px; margin-bottom: 140px;">

    <div style="background-color: #efefef; margin: 0 auto; width: 580px; padding: 20px;">


    <h1 style="font-size: 20px; margin-bottom: 10px;">Сменить пароль</h1>


    <div class="form">
    <?php echo CHtml::beginForm(); ?>

        <?php echo CHtml::errorSummary($form); ?>

        <div class="row">
        <?php echo CHtml::activePasswordField($form,'password', array(
            'placeholder'=>'Пароль',
            'style'=>'font-size:18px; width: 350px;'
        )); ?>
        </div>

        <div class="row">
        <?php echo CHtml::activePasswordField($form,'verifyPassword', array(
            'placeholder'=>'Подтверждение пароля',
            'style'=>'font-size:18px; width: 350px;'
        )); ?>
        </div>


        <div class="row submit">
        <?php echo CHtml::submitButton(UserModule::t("Save")); ?>
        </div>

    <?php echo CHtml::endForm(); ?>
    </div><!-- form -->

    </div>
</div>