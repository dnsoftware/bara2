<?php $this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Change Password");
$this->breadcrumbs=array(
	UserModule::t("Profile") => array('/user/profile'),
	UserModule::t("Change Password"),
);
$this->menu=array(
	((UserModule::isAdmin())
		?array('label'=>UserModule::t('Manage Users'), 'url'=>array('/user/admin'))
		:array()),
    array('label'=>UserModule::t('List User'), 'url'=>array('/user')),
    array('label'=>UserModule::t('Profile'), 'url'=>array('/user/profile')),
    array('label'=>UserModule::t('Edit'), 'url'=>array('edit')),
    array('label'=>UserModule::t('Logout'), 'url'=>array('/user/logout')),
);
?>

<h1 style="font-size: 18px; text-align: center; margin-bottom: 20px;"><?php echo UserModule::t("Change password"); ?></h1>



<table>
    <tr>
        <td style="vertical-align: top; width: 200px;">

            <div style="background: #fff;">
                <table>
                    <tr>
                        <td><a href="<?= Yii::app()->createUrl('/user/profile/edit');?>" class="baralink">Редактировать профиль</td>
                    </tr>
                    <tr>
                        <td><a href="<?= Yii::app()->createUrl('/user/profile/changepassword');?>" class="baralink">Изменить пароль</td>
                    </tr>
                </table>
            </div>

            <?
            $this->renderPartial('application.views.usercab.usercabmenu');
            ?>

        </td>

        <td style="vertical-align: top;">

        <div class="form">
        <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'changepassword-form',
            'enableAjaxValidation'=>true,
            'clientOptions'=>array(
                'validateOnSubmit'=>true,
            ),
        )); ?>

            <?php echo $form->errorSummary($model); ?>

            <div class="row">
            <?php echo $form->labelEx($model,'Введите текущий пароль'); ?>
            <?php echo $form->passwordField($model,'oldPassword'); ?>
            <?php echo $form->error($model,'oldPassword'); ?>
            </div>

            <div class="row">
            <?php echo $form->labelEx($model,'Введите новый пароль'); ?>
            <?php echo $form->passwordField($model,'password'); ?>
            <?php echo $form->error($model,'password'); ?>
            <p class="hint">
            <?php echo UserModule::t("Minimal password length 4 symbols."); ?>
            </p>
            </div>

            <div class="row">
            <?php echo $form->labelEx($model,'Повторите новый пароль'); ?>
            <?php echo $form->passwordField($model,'verifyPassword'); ?>
            <?php echo $form->error($model,'verifyPassword'); ?>
            </div>


            <div class="row submit">
            <?php echo CHtml::submitButton(UserModule::t("Save")); ?>
            </div>

        <?php $this->endWidget(); ?>
        </div><!-- form -->

        </td>
    </tr>
</table>
