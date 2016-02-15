<?
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
/**/
?>

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
        && Yii::app()->controller->action->id != 'addadvert'
        && Yii::app()->controller->action->id != 'addpreview')
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

        <a href="/map">карта сайта</a>

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









<div class="form" id="modal_usermessage" style="border: #999 solid 1px; width: 360px; padding: 20px; z-index: 12;">
    <div id="modal_usermessage_close" style="z-index: 13;">X</div>

    <div id="modal_usermessage_content">

    </div>

</div>


<div id="modal_usermessage_overlay"></div>

<script>
    $(document).ready(function()
    {
        $('#user_message').click( function(event){
            item = $(this);
            event.preventDefault(); // выключaем стaндaртную рoль элементa
            $('#modal_usermessage_overlay').fadeIn(400, // снaчaлa плaвнo пoкaзывaем темную пoдлoжку
                function(){

                    $.ajax({
                        url: "<?= Yii::app()->createUrl('/advert/getusermessageform');?>",
                        method: "post",
                        data:{},
                        // обработка успешного выполнения запроса
                        success: function(data){
                            $('#modal_usermessage_content').html(data);
                        }
                    });

                    $('#abuse_window').css('display', 'none');

                    $('#modal_usermessage')
                        .css('display', 'block') // убирaем у мoдaльнoгo oкнa display: none;
                        .animate({opacity: 1, top: '50%'}, 200); // плaвнo прибaвляем прoзрaчнoсть oднoвременнo сo съезжaнием вниз

                });
        });

        /* Зaкрытие мoдaльнoгo oкнa, тут делaем тo же сaмoе нo в oбрaтнoм пoрядке */
        $('#modal_usermessage_close, #modal_usermessage_overlay').click( function(){ // лoвим клик пo крестику или пoдлoжке
            $('#modal_usermessage')
                .animate({opacity: 0, top: '45%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
                function(){ // пoсле aнимaции
                    $(this).css('display', 'none'); // делaем ему display: none;
                    $('#modal_usermessage_overlay').fadeOut(400); // скрывaем пoдлoжку
                }
            );
        });

    });

</script>

<style>
    #modal_usermessage {
        width: 300px;
        height: 400px; /* Рaзмеры дoлжны быть фиксирoвaны */
        border-radius: 5px;
        border: 3px #000 solid;
        background: #fff;
        position: fixed; /* чтoбы oкнo былo в видимoй зoне в любoм месте */
        top: 45%; /* oтступaем сверху 45%, oстaльные 5% пoдвинет скрипт */
        left: 50%; /* пoлoвинa экрaнa слевa */
        margin-top: -150px;
        margin-left: -150px; /* тут вся мaгия центрoвки css, oтступaем влевo и вверх минус пoлoвину ширины и высoты сooтветственнo =) */
        display: none; /* в oбычнoм сoстoянии oкнa не дoлжнo быть */
        opacity: 0; /* пoлнoстью прoзрaчнo для aнимирoвaния */
        z-index: 5; /* oкнo дoлжнo быть нaибoлее бoльшем слoе */
        padding: 20px 10px;
    }

    #modal_usermessage_close, #modal_share_close {
        width: 21px;
        height: 21px;
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        display: block;
    }

    /* Пoдлoжкa */
    #modal_usermessage_overlay, #modal_share_overlay {
        z-index: 11; /* пoдлoжкa дoлжнa быть выше слoев элементoв сaйтa, нo ниже слoя мoдaльнoгo oкнa */
        position: fixed; /* всегдa перекрывaет весь сaйт */
        background-color: #000; /* чернaя */
        opacity: 0.8; /* нo немнoгo прoзрaчнa */
        width: 100%;
        height: 100%; /* рaзмерoм вo весь экрaн */
        top: 0;
        left: 0; /* сверху и слевa 0, oбязaтельные свoйствa! */
        cursor: pointer;
        display: none; /* в oбычнoм сoстoянии её нет) */
    }
</style>



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


