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

$user_status = array('1'=>'активирован', '0'=>'неактивен');
?><h1 style="font-size: 18px; text-align: center; margin-top: 20px; margin-bottom: 20px;">Профиль ID <?= $model->id;?>, <?= $user_status[$model->status];?></h1>

<?php if(Yii::app()->user->hasFlash('profileMessage')): ?>
<div class="success">
	<?php echo Yii::app()->user->getFlash('profileMessage'); ?>
</div>
<?php endif; ?>

<table>
<tr>
    <td style="vertical-align: top;">

        <div style="background: #fff;">
            <table>
                <tr>
                    <td><a href="<?= Yii::app()->createUrl('/user/profile/edit');?>" class="baralink">Редактировать</td>
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
            <th class="label">Имя</th>
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
        <!--
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
        -->
    </table>
    </td>
</tr>
</table>


<?
//deb::dump(Yii::app()->basePath);
require_once Yii::app()->basePath . '/extensions/Facebook/autoload.php';

$fb = new Facebook\Facebook([
    'app_id' => '1078653292174466',
    'app_secret' => '07b5035b3d475c88bdb1b90418378e69',
    'default_graph_version' => 'v2.5',
]);


/*
$fb = new Facebook\Facebook([
    'app_id' => '1080339912005804',
    'app_secret' => 'cd7ec5c4d19de3abb42c4b6f2755586b',
    'default_graph_version' => 'v2.5',
]);
*/

$callback = "http://".$_SERVER['HTTP_HOST']."/site/fbcallback";

$helper = $fb->getRedirectLoginHelper();

// для публикации в группах достаточно разрешения publish_actions
// для публикации на страницах нужны все 3 элемента
/*
$permissions = ['user_birthday', 'user_religion_politics', 'user_relationships', 'user_relationship_details', 'user_hometown', 'user_location', 'user_likes', 'user_education_history', 'user_work_history', 'user_website', 'user_managed_groups', 'user_events', 'user_photos', 'user_videos', 'user_friends', 'user_about_me', 'user_status', 'user_games_activity', 'user_tagged_places', 'user_posts', 'read_page_mailboxes', 'rsvp_event', 'email', 'ads_management', 'ads_read', 'read_insights', 'manage_pages', 'publish_pages', 'pages_show_list', 'pages_manage_cta', 'pages_manage_leads', 'publish_actions', 'read_audience_network_insights', 'user_actions.books', 'user_actions.music', 'user_actions.video', 'user_actions.news', 'user_actions.fitness', 'public_profile', 'basic_info'];
*/

$permissions = ['user_posts'];

$loginUrl = $helper->getLoginUrl($callback, $permissions);

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';


if(1)
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




