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

<h1><?php echo UserModule::t("Восстановление пароля"); ?></h1>


<div class="form">
<?php echo CHtml::beginForm($action); ?>

	<div class="row">
		<?php echo CHtml::activeHiddenField($form,'login_or_email') ?>
	</div>
	
	<div class="row submit">
		<?php echo CHtml::submitButton(UserModule::t("Восстановить пароль")); ?>
	</div>

<?php echo CHtml::endForm(); ?>
</div>