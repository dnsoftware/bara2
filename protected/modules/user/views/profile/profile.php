<?php $this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Profile");
$this->breadcrumbs=array(
	UserModule::t("Profile"),
);
$this->menu=array(
	((UserModule::isAdmin())
		?array('label'=>UserModule::t('Manage Users'), 'url'=>array('/user/admin'))
		:array()),
    array('label'=>UserModule::t('List User'), 'url'=>array('/user')),
    array('label'=>UserModule::t('Edit'), 'url'=>array('edit')),
    array('label'=>UserModule::t('Change password'), 'url'=>array('changepassword')),
    array('label'=>UserModule::t('Logout'), 'url'=>array('/user/logout')),
);


?><h1 style="font-size: 18px; text-align: center; margin-top: 20px; margin-bottom: 20px;">Ваш профиль</h1>

<?php if(Yii::app()->user->hasFlash('profileMessage')): ?>
<div class="success">
	<?php echo Yii::app()->user->getFlash('profileMessage'); ?>
</div>
<?php endif; ?>

<table>
<tr>
    <td>
    <table class="dataGrid">

        <?/*?>
        <tr>
            <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('username')); ?></th>
            <td><?php echo CHtml::encode($model->username); ?></td>
        </tr>
        <?php
            $profileFields=ProfileField::model()->forOwner()->sort()->findAll();
            if ($profileFields) {
                foreach($profileFields as $field) {
                    //echo "<pre>"; print_r($profile); die();
                ?>
        <tr>
            <th class="label"><?php echo CHtml::encode(UserModule::t($field->title)); ?></th>
            <td><?php echo (($field->widgetView($profile))?$field->widgetView($profile):CHtml::encode((($field->range)?Profile::range($field->range,$profile->getAttribute($field->varname)):$profile->getAttribute($field->varname)))); ?></td>
        </tr>
                <?php
                }//$profile->getAttribute($field->varname)
            }
        ?>
        <?*/?>

        <tr>
            <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('id')); ?></th>
            <td><?php echo CHtml::encode($model->id); ?></td>
        </tr>
        <?
        if($model->user_type == 'c')
        {
        ?>
        <tr>
            <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('company_name')); ?></th>
            <td><?php echo CHtml::encode($model->company_name); ?></td>
        </tr>
        <?
        }
        ?>

        <tr>
            <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('privat_name')); ?></th>
            <td><?php echo CHtml::encode($model->privat_name); ?></td>
        </tr>
        <tr>
            <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('email')); ?></th>
            <td><?php echo CHtml::encode($model->email); ?></td>
        </tr>
        <tr>
            <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('create_at')); ?></th>
            <td><?php echo $model->create_at; ?></td>
        </tr>
        <tr>
            <?
            $lastvisit = $model->lastvisit_at;
            if($lastvisit == '0000-00-00 00:00:00')
            {
                $lastvisit = $model->create_at;
            }
            ?>
            <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('lastvisit_at')); ?></th>
            <td><?php echo $lastvisit; ?></td>
        </tr>
        <tr>
            <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></th>
            <td><?php echo CHtml::encode(User::itemAlias("UserStatus",$model->status)); ?></td>
        </tr>
    </table>
    </td>
    <td style="vertical-align: top;">

    <div style="background: #eee;">
        <table>
        <tr>
            <td><a href="<?= Yii::app()->createUrl('/user/profile/edit');?>" class="baralink">Редактировать</td>
        </tr>
        <tr>
            <td><a href="<?= Yii::app()->createUrl('/user/profile/changepassword');?>" class="baralink">Изменить пароль</td>
        </tr>
        </table>
    </div>
    </td>
</tr>
</table>


<?
if(0)
{
?>

<h2>Связанные с аккаунтом сервисы::</h2>
<?php
$this->widget('ext.eauth.EAuthWidget', array(
    'action' => 'deleteService',
    'view'=>'linkedServices',
    'popup'=>false,
    'predefinedServices'=>$services
));
?>


<h2>Выберите сервис для привязки к аккаунту:</h2>
<?php
$allServices = array_keys(Yii::app()->eauth->getServices());
$this->widget('ext.eauth.EAuthWidget', array(
    'action' => 'login/login',
    'predefinedServices' => array_diff($allServices,$services)));

}
?>




