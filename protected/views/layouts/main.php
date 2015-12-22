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
            <td style="border: #111 solid 0px; text-align: right; vertical-align: top; margin-top: 0px">

            <div style="margin-bottom: 5px; margin-top: 2px; ">
                <span style="background: url('/images/lastvisit.png'); background-position: left center; background-repeat: no-repeat; padding-left: 23px;">
                <a href="<?= Yii::app()->createUrl('/user/lastvisit');?>" class="baralink" style="margin-right: 20px;">Недавнее: <span id="lastvisit_count"><?= Notice::GetLastvisitCount();?></span></a></span>

                <span style="background: url('/images/favorit.png'); background-position: left center; background-repeat: no-repeat; padding-left: 18px;">
                <a href="<?= Yii::app()->createUrl('/user/favorit');?>" class="baralink" style="margin-right: 20px;">Избранное: <span id="favorit_count"><?= Notice::GetFavoritCount();?></span></a></span>

                <?
                $usernotcount = Notice::GetUserCountAllAdverts(Yii::app()->user->id);
                Yii::app()->session->add('usernotcount', $usernotcount);

                if(isset(Yii::app()->user->id) && Yii::app()->user->id > 0)
                {
                    ?>
                    <span style="background: url('/images/alladvert-black.png'); background-position: left center; background-repeat: no-repeat; padding-left: 20px; border: #000 solid 0px; ">
                <a href="<?= Yii::app()->createUrl('/usercab/adverts');?>" class="baralink" style="margin-right: 20px; ">Мои объявления: <span id="useradverts_count"><?= $usernotcount;?></span></a></span>
                <?
                }
                ?>

                <?
            if(Yii::app()->user->isGuest)
            {
            ?>
                <span style="background: url('/images/loginicon.png'); background-position: left center; background-repeat: no-repeat; padding-left: 20px;"><a class="baralink" href="<?= Yii::app()->createUrl('/user/login');?>" class="baralink">Вход или регистрация</a></span>
            <?
            }
            else
            {
            ?>
                <!--<span style="background: url('/images/loginicon.png'); background-position: left center; background-repeat: no-repeat; padding-left: 20px;"><a class="baralink" href="<?= Yii::app()->createUrl('/user/logout');?>" class="baralink"><?= Yii::app()->user->email;?></a></span>-->


                    <?php $this->widget('zii.widgets.CMenu',array(
                        'id'=>'menu_goriz',
                        'items'=>array(
                            array(
                                'encodeLabel'=>false,
                                'label'=>"<span id='usermenuemail' style=''>".Yii::app()->user->email."</span>",
                                'itemOptions'=>array('class'=>'baramenu', 'style'=>"background: url('/images/loginicon.png');  background-position: 1px 2px; background-repeat: no-repeat; width: 117px; height: 13px; display: inline-block; padding-bottom: 5px; padding-left: 21px; padding-top:2px; margin-top:0px; text-align: left; border: #000 solid 0px;"),
                                'items'=>array(
                                    array('label'=>'Кабинет', 'url'=>array('/usercab/adverts'), 'visible'=>!Yii::app()->user->isGuest),
                                    array('label'=>'Админка', 'url'=>array('/adminka/'), 'visible'=>Yii::app()->user->checkAccess('Admin')),
                                    array('label'=>'RBAC', 'url'=>array('/rights'), 'visible'=>Yii::app()->user->checkAccess('Admin')),
                                    array('label'=>'Рубрикация', 'url'=>array('/adminka/property/index'), 'visible'=>Yii::app()->user->checkAccess('Admin')),
                                    array('label'=>'Типы свойств', 'url'=>array('/adminka/proptypes/index'), 'visible'=>Yii::app()->user->checkAccess('Admin')),
                                    array('label'=>'Рубрикатор', 'url'=>array('/adminka/rubriks/index'), 'visible'=>Yii::app()->user->checkAccess('Admin')),
                                    array('label'=>'Выход', 'url'=>array('/user/logout')),

                                ),

                            )
                        ),



                    )); ?>
                <!-- mainmenu -->



            <?
            }
            ?>
            </div>
            <br style="height: 1px;">


            <div style="float: right; border: #000099 solid 0px;">
            <a href="/advert/addadvert" style="display: inline-block; padding: 9px 18px; margin-top: -3px; color: #fff; text-decoration: none; background-color: #0D9D0D; font-size: 15px;
-moz-border-radius: 3px;
-webkit-border-radius: 3px;
-khtml-border-radius: 3px;
border-radius: 3px;">
                Добавить объявление
            </a>
            </div>

            </td>
        </tr>
        </table>
	</div><!-- header -->


	<?php echo $content; ?>

	<div class="clear"></div>

    <?
    //deb::dump(Yii::app()->controller->action->id);
    if(Yii::app()->controller->id != 'profile' && Yii::app()->controller->id != 'usercab'
        && Yii::app()->controller->module->id != 'adminka'
        && Yii::app()->controller->action->id != 'addadvert')
    {
    ?>
    <div style="text-align: center; padding-left: 0; margin-top: 10px; height: 120px; width: 1050px; border: #000099 solid 0px;">
        <?
        $banner_operator = Yii::app()->params['banners_raspred'][2];
        include(Yii::getPathOfAlias('webroot')."/banners/".$banner_operator."/bottom_horizont.php");
        ?>
    </div>
    <?
    }
    ?>


    <div id="footer" style="">
        baraholka.ru
        <?
            if(Yii::app()->params['footer_keyword'] != '')
            {
                echo " - ".Yii::app()->params['footer_keyword'];
            }
        ?>

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
