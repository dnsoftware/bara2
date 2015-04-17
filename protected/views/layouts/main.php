<?
header("Content-type: text/html; charset=utf-8");
?>
<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->


    <? Yii::app()->getClientScript()->registerCoreScript( 'jquery.ui' );?>

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

    <? Yii::app()->getClientScript()->registerCssFile(
        Yii::app()->assetManager->publish(
            Yii::getPathOfAlias('system').'/web/js/source/jui/css/base/jquery-ui.css'
        ));
    ?>

    <!--<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/yii/framework/web/js/source/jui/css/base/jquery-ui.css" />
    -->

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">
	<div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div><!-- header -->

	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Главная', 'url'=>array('/site/index')),
				array('label'=>'Подать', 'url'=>array('/advert/addadvert')),
				//array('label'=>'Contact', 'url'=>array('/site/contact')),

				array('label'=>'Вход', 'url'=>array('/user/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Выход ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'), 'visible'=>!Yii::app()->user->isGuest),
                array('label'=>'Профиль', 'url'=>array('/user/profile'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Админка', 'url'=>array('/adminka/tree/index'), 'visible'=>Yii::app()->user->checkAccess('Admin')),
				array('label'=>'RBAC', 'url'=>array('/rights'), 'visible'=>Yii::app()->user->checkAccess('Admin')),
                array('label'=>'Рубрикация', 'url'=>array('/adminka/property/index'), 'visible'=>Yii::app()->user->checkAccess('Admin')),
                array('label'=>'Типы свойств', 'url'=>array('/adminka/proptypes/index'), 'visible'=>Yii::app()->user->checkAccess('Admin')),

            ),
		)); ?>
	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
        Style
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>

<div id="ajax_debug">
</div>
