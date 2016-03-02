<?
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/main.js', CClientScript::POS_END);


header("Content-type: text/html; charset=utf-8");
?>
<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">


<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="google-site-verification" content="lrX3FvPtZIU5WGZnwy8m4GOWJM_t4Q5liOkR6pYfw1U" />
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

    //Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/advertisement.js');

    ?>


    <?
    if(0)
    {
    ?>
    <!-- BEGIN JIVOSITE CODE {literal} -->
    <script type='text/javascript'>
        (function(){ var widget_id = 'qNIlckKhzn';
            var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);})();</script>
    <!-- {/literal} END JIVOSITE CODE -->
    <?
    }
    ?>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>


<body>

<script>
    (function($){

        if ($.adblock === undefined){
            $.adblock = true;
            //alert('Выключите Adblock');
        }

    })(jQuery);
</script>


<?
/*
?>
<div style="width: 100%; background-color: #f00; color: #fff; padding: 5px; text-align: center;">
    Уважаемые посетители! Наш сайт переезжает на новое программное обеспечение. <br>
    В связи с этим в работе сайта могут возникнуть ошибки или неточности. Просьба сообщать нам о них
    <span id="user_message" style="border-bottom: #fff solid 1px; cursor: pointer;">здесь</span></div>
<?
/**/
?>

<div class="container" id="page">
	<div id="header">
        <table id="tbl-geolocator" cellpadding="0" cellspacing="0">
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
                <a href="<?= $main_url;?>"><img alt="Доска бесплатных объявлений от частных лиц - www.baraholka.ru" src="/images/logo.png"></a>
            </div>
            </td>
            <td id="td-topmenu">

            <div id="div-topmenu">
                <span id="span-lastvisit">
                <a id="a-lastvisit" href="<?= Yii::app()->createUrl('/user/lastvisit');?>" class="baralink" >Недавнее: <span id="lastvisit_count"><?= Notice::GetLastvisitCount();?></span></a></span>

                <span id="span-favorit">
                <a id="a-favorit" href="<?= Yii::app()->createUrl('/user/favorit');?>" class="baralink" >Избранное: <span id="favorit_count"><?= Notice::GetFavoritCount();?></span></a></span>

                <?
                $usernotcount = Notice::GetUserCountAllAdverts(Yii::app()->user->id);
                Yii::app()->session->add('usernotcount', $usernotcount);

                if(isset(Yii::app()->user->id) && Yii::app()->user->id > 0)
                {
                    ?>
                    <span id="span-uadvertscount">
                <a id="a-uadvertscount" href="<?= Yii::app()->createUrl('/usercab/adverts');?>" class="baralink">Мои объявления: <span id="useradverts_count"><?= $usernotcount;?></span></a></span>
                <?
                }
                ?>

                <?
            if(Yii::app()->user->isGuest)
            {
            ?>
                <span id="span-login"><a class="baralink" href="<?= Yii::app()->createUrl('/user/login');?>" class="baralink">Вход или регистрация</a></span>
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


            <div id="div-addadvbutton">
            <a id="a-addadvbutton" href="/advert/addadvert">
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
        && Yii::app()->controller->action->id != 'addadvert'
        && Yii::app()->controller->action->id != 'addpreview')
    {
    ?>
    <div id="banner-block-footer">
        <?
        $banner_operator = Yii::app()->params['banners_raspred'][2];
        include(Yii::getPathOfAlias('webroot')."/banners/".$banner_operator."/bottom_horizont.php");
        ?>
    </div>
    <?
    }
    ?>


    <div id="footer" style="">

        <div id="div-footer-menu">
        <a href="/map" class="baralink">Карта сайта</a>

        <span id="user_message"><a class="baralink" style="">Обратная связь</a></span>
        </div>

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

        <div>
            baraholka.ru
            <?
            if(Yii::app()->params['footer_keyword'] != '')
            {
                echo " - ".Yii::app()->params['footer_keyword'];
            }
            ?>

        </div>

    </div><!-- footer -->

</div><!-- page -->



<div class="form" id="modal_usermessage">
    <div id="modal_usermessage_close">X</div>

    <div id="modal_usermessage_content">

    </div>

</div>


<div id="modal_usermessage_overlay"></div>

<script>

    $(document).ready(function()
    {
        user_message_init('<?= Yii::app()->createUrl('/advert/getusermessageform');?>');
    });

</script>


<?
if($_SERVER['HTTP_HOST'] != 'baraholka2.dn')
{
?>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter10041385 = new Ya.Metrika({
                    id:10041385,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>

<noscript><div><img src="https://mc.yandex.ru/watch/10041385" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<?
}
?>


</body>
</html>

<div id="ajax_debug">
</div>


