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
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/baraholka.css" />
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

<div class="container" id="page" style="border: #000020 solid 0px;">
	<div id="header">
        <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
		<tr>
            <td>
            <div id="logo">
                <?php $this->widget('application.extensions.geolocator.GeolocatorWidget'); ?>
                <?
                $main_url = 'http://baraholka.ru';
                if($_SERVER['REMOTE_ADDR'] == '127.0.0.1')
                {
                    $main_url = '/';
                }
                ?>
                <a href="<?= $main_url;?>"><img src="/images/logo.png" style="border: #000 solid 0px; margin-top: 7px;"></a>
            </div>
            </td>
            <td style="border: #111 solid 0px; text-align: right; vertical-align: top;">

            <div style="margin-bottom: 5px; margin-top: 2px;">
            <?
            if(Yii::app()->user->isGuest)
            {
            ?>
                <a class="baralink" href="/user/login">Вход или регистрация</a>
            <?
            }
            else
            {
            ?>
                <a class="baralink" href="/user/logout">Выход</a>
            <?
            }
            ?>
            </div>

            <a href="/advert/addadvert" style="display: inline-block; padding: 9px 18px; margin-top: 15px; color: #fff; text-decoration: none; background-color: #0D9D0D; font-size: 15px;
-moz-border-radius: 3px;
-webkit-border-radius: 3px;
-khtml-border-radius: 3px;
border-radius: 3px;">
                Добавить объявление
            </a>

            </td>
        </tr>
        </table>
	</div><!-- header -->

	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				//array('label'=>'Подать', 'url'=>array('/advert/addadvert')),
				//array('label'=>'Contact', 'url'=>array('/site/contact')),
				//array('label'=>'Вход', 'url'=>array('/user/login'), 'visible'=>Yii::app()->user->isGuest),
				//array('label'=>'Выход ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'), 'visible'=>!Yii::app()->user->isGuest),
                array('label'=>'Профиль', 'url'=>array('/user/profile'), 'visible'=>!Yii::app()->user->isGuest),
                array('label'=>'Кабинет', 'url'=>array('/usercab/adverts'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Админка', 'url'=>array('/adminka/'), 'visible'=>Yii::app()->user->checkAccess('Admin')),
				array('label'=>'RBAC', 'url'=>array('/rights'), 'visible'=>Yii::app()->user->checkAccess('Admin')),
                array('label'=>'Рубрикация', 'url'=>array('/adminka/property/index'), 'visible'=>Yii::app()->user->checkAccess('Admin')),
                array('label'=>'Типы свойств', 'url'=>array('/adminka/proptypes/index'), 'visible'=>Yii::app()->user->checkAccess('Admin')),
                array('label'=>'Рубрикатор', 'url'=>array('/adminka/rubriks/index'), 'visible'=>Yii::app()->user->checkAccess('Admin')),

                //array('label'=>'', 'url'=>array('/')),

            ),
		)); ?>
	</div><!-- mainmenu -->

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
        baraholka.ru

        <div style="float: right;">
        <!--LiveInternet counter--><script type="text/javascript"><!--
            document.write("<a href='//www.liveinternet.ru/stat/baraholka.ru' "+
                "target=_blank><img src='//counter.yadro.ru/hit?t26.1;r"+
                escape(document.referrer)+((typeof(screen)=="undefined")?"":
                ";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
                    screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
                ";h"+escape(document.title.substring(0,80))+";"+Math.random()+
                "' alt='' title='LiveInternet: показано число посетителей за"+
                " сегодня' "+
                "border='0' width='88' height='15'><\/a>")

            //--></script><!--/LiveInternet-->
        </div>

    </div><!-- footer -->

</div><!-- page -->

</body>
</html>

<div id="ajax_debug">
</div>
