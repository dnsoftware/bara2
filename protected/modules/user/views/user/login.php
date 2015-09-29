<?php
$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Login");
$this->breadcrumbs=array(
	UserModule::t("Вход"),
);
?>

<div style="text-align: center; margin-top: 120px; margin-bottom: 180px;">


<div class="form" style="margin-top: 30px;">

<div style="background-color: #efefef; margin: 0 auto; width: 500px; padding: 20px;">
    <h1 style="font-size: 20px; margin-bottom: 30px;"><?php echo UserModule::t("Вход"); ?></h1>


    <?php echo CHtml::beginForm(); ?>
	<?php echo CHtml::errorSummary($model); ?>
	
	<div class="row">
		<?php echo CHtml::activeLabel($model,'username', array('style'=>'font-size:18px;')); ?>
		<?php echo CHtml::activeTextField($model,'username', array('style'=>'font-size:18px; width: 350px;')) ?>
	</div>
	
	<div class="row">
		<?php echo CHtml::activeLabel($model,'password', array('style'=>'font-size:18px;')); ?>
		<?php echo CHtml::activePasswordField($model,'password', array('style'=>'font-size:18px; width: 350px;')) ?>
	</div>

	<div class="row rememberMe">
        <span style="margin-left: -1px; margin-right: 97px;"><?php echo CHtml::activeCheckBox($model,'rememberMe', array('style'=>'font-size:14px;')); ?>
            <?php echo CHtml::activeLabelEx($model,'rememberMe', array('style'=>'font-size:14px;')); ?></span>
        <span style="margin-left: 10px;"><?php echo CHtml::link(UserModule::t("Lost Password?"),Yii::app()->getModule('user')->recoveryUrl, array('style'=>'font-size:14px;', 'class'=>'hoverunderline')); ?></span>
	</div>
	

	<div class="row submit">
		<?php echo CHtml::submitButton('Войти', array('style'=>'font-size:18px;')); ?>
	</div>



    <?php echo CHtml::endForm(); ?>

</div>

    <div class="row" style="margin-top: 30px;">
        <span style="color: #000; font-size: 18px;">В первый раз у нас?</span>
        <?php echo CHtml::link('Зарегистрируйтесь!',Yii::app()->getModule('user')->registrationUrl, array('style'=>'font-size:18px;', 'class'=>'hoverunderline')); ?>
    </div>

</div>

<?

/*
?>
<h2>Вы уже имеете аккаунт на этих сайтах? Кликните на логотип сайта, чтобы войти с его помощью:</h2>
<?php
$this->widget('application.extensions.eauth.EAuthWidget', array('action' => '/user/login'));
*/
?>


</div>

