<?php $this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Restore");
$this->breadcrumbs=array(
	UserModule::t("Login") => array('/user/login'),
	UserModule::t("Restore"),
);
?>

<div style="text-align: center; margin-top: 100px; margin-bottom: 140px;">

    <div style="background-color: #efefef; margin: 0 auto; width: 420px; padding: 20px;">

    <h1 style="font-size: 20px; margin-bottom: 30px;">Восстановление доступа</h1>

    <?php if(Yii::app()->user->hasFlash('recoveryMessage')): ?>
    <div class="success" style="font-size: 16px;">
    <?php echo Yii::app()->user->getFlash('recoveryMessage'); ?>
    </div>
    <?php else: ?>

    <div class="form">
    <?php echo CHtml::beginForm(); ?>

        <?php echo CHtml::errorSummary($form); ?>

        <div class="row" style="text-align: center;">
            <div style="margin: 0 auto; margin-bottom: 10px; border: #000020 solid 0px; width: 356px;">
            <?php echo CHtml::activeLabel($form,'Введите адрес электронной почты, который был указан при регистрации. ', array('style'=>'font-weight: normal; text-align: left; margin-bottom: 10px; font-size: 14px;')); ?>
            <?php echo CHtml::activeTextField($form,'login_or_email', array(
                'placeholder'=>'Электронная почта',
                'style'=>'font-size:18px; width: 350px;'
            )) ?>
            </div>
        </div>

        <div class="row submit">
            <?php echo CHtml::submitButton(UserModule::t("Restore"), array('style'=>'font-size:18px;')); ?>
        </div>

    <?php echo CHtml::endForm(); ?>
    </div><!-- form -->
    <?php endif; ?>

    </div>

    <div class="row" style="margin-top: 30px;">
        <span style="color: #000; font-size: 18px;">Вспомнили данные доступа?</span>
        <?php echo CHtml::link('Входите!',Yii::app()->getModule('user')->loginUrl, array('style'=>'font-size:18px;', 'class'=>'hoverunderline')); ?>
    </div>


</div>