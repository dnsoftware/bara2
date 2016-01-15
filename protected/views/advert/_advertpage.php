<?
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/galleria/galleria-1.4.2.js');
?>

<?
if(Yii::app()->controller->action->id == 'addpreview')
{
?>
<div style="width: 100%; text-align: center; margin-bottom: 20px;">
    <?
    if(Yii::app()->user->id > 0)
    {
//echo "Вы залогинены, все ок";
        ?>
        <a style="margin-left: 20px; padding: 8px; background-color: #f00; text-decoration: none; color: #fff; border-radius: 3px; font-size: 16px;" href="<?= Yii::app()->createUrl('advert/addadvert');?>">Редактировать</a>

        <a style="margin-left: 20px; padding: 8px; background-color: #0D9D0D; text-decoration: none; color: #fff; border-radius: 3px; font-size: 16px;" href="<?= Yii::app()->createUrl('advert/savenew');?>">Опубликовать</a>
    <?
    }
    ?>
</div>
<?
}
?>

<h1 style="display: inline; font-size: 28px; padding: 3px 3px 3px 0; "><?= $mainblock['title'];?></h1>

<span style=" margin-left: 10px; padding: 5px; padding-left: 6px; padding-right: 6px; display: inline; font-size: 22px; font-weight: normal;  background-color: #0D9D0D; color: #fff; text-align: center; ">

        Цена: <?= Notice::costCalcAndView(
        $mainblock['cost_valuta'],
        $mainblock['cost'],
        Yii::app()->request->cookies['user_valuta_view']->value
    );?>

    <span id="valute_symbol" style="border-bottom-style: dotted; cursor: pointer; border-width: 1px;" onclick="displayHide('div_valute_change');"><?= Options::$valutes[Yii::app()->request->cookies['user_valuta_view']->value]['symbol'];?></span>

    </span>

<div id="div_valute_change" style="display: none; position: absolute; z-index: 90; width: 250px; height: 110px; border: #000000 solid 1px; background-color: #eee; padding: 5px;">

    <div style=" background-image: url('/images/x.png'); background-position: 0px 0px; width: 17px; height: 17px; margin-right: 0px; float: right; cursor: pointer;" onclick="$('#div_valute_change').css('display', 'none');"></div>

    <div style="float: left; width: 230px;">
        <table cellpadding="0" style="margin: 0; padding: 3px; float: left; ">
            <tr>
                <?
                foreach(Options::$valutes as $vkey=>$vval)
                {
                    ?>
                    <td style="text-align:center; border-bottom: #ccc solid  1px; border-width: 1px;">

                        <a class="baralink" style="border-bottom: #008CC3 dotted 1px;" href="<?= Yii::app()->createUrl('supporter/setvalutaview', array('valuta_view'=>$vval['abbr']));?>"><?= $vval['name_rodit'];?></a>
                        <?
                        if($vval['abbr'] != 'RUB')
                        {
                            echo "*";
                        }
                        ?>
                    </td>
                <?
                }
                ?>
            </tr>
            <tr>
                <?
                foreach(Options::$valutes as $vkey=>$vval)
                {
                    ?>
                    <td style="text-align:center; color: #000;">
                        <?= Notice::costCalcAndView(
                            $mainblock['cost_valuta'],
                            $mainblock['cost'],
                            $vval['abbr']
                        );?>
                    </td>
                <?
                }
                ?>
            </tr>
        </table>
    </div>

    <div style="margin: 5px; float: left; font-size: 11px;">
        *по курсу ЦБ на <?= date("d.m.Y", Yii::app()->params['options']['kurs_date']);?>
        <? //Yii::app()->params['month_padezh'][intval(date("m", Yii::app()->params['options']['kurs_date']))];?>
        <? //date("Y", Yii::app()->params['options']['kurs_date']);?>
        <div style="margin-top: 5px;">
            Для отображения цен на сайте в другой валюте нажмите на ее название
        </div>
    </div>

</div>

<div style="margin-top: 10px; margin-bottom: 5px; color: #000; border: #000020 solid 0px; ">
<?
    if(Yii::app()->controller->action->id == 'addpreview')
    {
        $mainblock['date_add'] = time();
    }

    $date_add_str = date('d-m-Y H:i', $mainblock['date_add']);
    $day_string = Notice::TodayStrGenerate($mainblock['date_add'], 1);
?>
    <div style=" float: left; width: 682px; border: #000000 solid 0px;">
        <span style="padding-left: 15px; margin-right: 3px; background-image: url('/images/dateadd.png'); background-position: left center; background-repeat: no-repeat;" title="Время размещения объявления"></span> <?= $day_string;?>

        <?
        if(Yii::app()->controller->action->id != 'addpreview')
        {
        ?>
        <span style="float: right; color: #999;">№ объявления: <?= $mainblock['daynumber_id'];?></span>
        <?
        }
        ?>

    </div>

    <?
    if(Yii::app()->controller->action->id != 'addpreview')
    {
    ?>
    <div style="float: right; clear: right;">
        Просмотров: всего <?= $mainblock['counter_total']+1;?>, сегодня <?= $mainblock['counter_daily']+1;?>
        <img src="<?= Yii::app()->createUrl('supporter/advertcounter', array('n_id'=>$mainblock['n_id']));?>" width="0">
    </div>
    <?
    }
    ?>

    <br>
</div>


<table>
    <tr>
        <td style="vertical-align: top; padding: 0px;">
<?
//deb::dump(Yii::app()->controller->action->id);
//deb::dump(Yii::app()->user->create_at);
?>
            <div id="notice" style="" >
                <div class="galleria" id="galleria" style="width: 600px;">
                    <?
                    //deb::dump($uploadfiles_array);
                    $i=0;
                    foreach($uploadfiles_array as $ukey=>$uval)
                    {
                        $curr_dir = Notice::getPhotoDir($uval);

                        $part_path = "/".Yii::app()->params['photodir']."/".$curr_dir."/";
                        if($mainblock['n_id'] <= 0)
                        {
                            $part_path = '/tmp/';
                        }

                        $i++;
                        if(Yii::app()->controller->action->id == 'addpreview')
                        {
                        ?>
                            <img src="<?= $part_path.$uval;?>" data-title="<?= htmlspecialchars($mainblock['title']). " - ".$i;?>" data-description="<?= htmlspecialchars($mainblock['title']);?>">
                        <?
                        }
                        else
                        {
                            $valuta_symbol = Options::$valutes[Yii::app()->request->cookies['user_valuta_view']->value]['symbol'];
                            if(Yii::app()->request->cookies['user_valuta_view']->value == 'RUB')
                            {
                                $valuta_symbol = 'Р';
                            }

                            $alt_img = "";
                            if($i == 1)
                            {
                                $img_title = htmlspecialchars($mainblock['title'])." в г. ".$mainblock_data['town']->name;
                                $alt_img = $img_title;
                            }
                        ?>
                            <a href="<?= Notice::getPhotoName($part_path.$uval, "_big");?>"><img data-big="<?= Notice::getPhotoName($part_path.$uval, "_huge");?>" src="<?= Notice::getPhotoName($part_path.$uval, "_thumb");?>" data-title="<?= htmlspecialchars($mainblock['title']). " за ".Notice::costCalcAndView(
                                    $mainblock['cost_valuta'],
                                    $mainblock['cost'],
                                    Yii::app()->request->cookies['user_valuta_view']->value)." ".$valuta_symbol;?>" data-description="<?= htmlspecialchars($mainblock['title']);?>" alt="<?= $alt_img;?>"></a>
                        <?
                        }

                    }
                    ?>

                </div>

            </div>

            <?
            if(count($uploadfiles_array) > 0)
            {
            ?>
                <div id="gallery_fullview" style="z-index: 10; position: absolute; top: 10px; left: 570px; cursor: pointer; ">
                    <div style="background-image: url('/images/lupa_s.png'); background-position: 0px 0px; width: 20px; height: 20px;"></div>
                </div>
            <?
            }
            ?>

            <?
            if($mainblock['date_expire'] > time() || Yii::app()->controller->action->id == 'addpreview')
            {
                if(Yii::app()->controller->action->id == 'addpreview')
                {
                    $mainblock['user_date_reg'] = Yii::app()->user->create_at;
                }
            ?>
            <div style="margin-top: 10px; font-weight: normal; width: 682px; border: #000000 solid 0px;">

                <span style="padding-left: 20px; background-image: url('/images/client.png'); background-position: left center; background-repeat: no-repeat; font-weight: bold;" title="Имя"><a style="color: inherit; text-decoration: none;" href="/user/uadverts/<?= $mainblock['u_id'];?>"><?= $mainblock['client_name'];?></a></span>
                <span style="font-weight: normal;">на baraholka.ru с <?= Yii::app()->params['month_padezh'][intval(date("m", strtotime($mainblock['user_date_reg'])))];?> <?= date("Y", strtotime($mainblock['user_date_reg']));?> года
                </span>

                <?
                if(Yii::app()->controller->action->id != 'addpreview')
                {
                ?>
                <div style="float: right; ">
                <a class="span_lnk" style="background: url('/images/phone-black.png'); background-position: left center; background-repeat: no-repeat; padding-left: 17px; width: 135px;"><span id="display_phone"  style="border-bottom: #008CC3 dotted; border-width: 1px; ">Показать телефон</span><img id="img_display_phone" src="/images/actions/loader.gif" style="display: none; margin-bottom: -8px; height: 20px;"></a>

                <a class="span_lnk" style="margin-left: 15px; background: url('/images/write-black.png'); background-position: left center; background-repeat: no-repeat; padding-left: 17px;">
                    <span id="writeauthor_btn" style="border-bottom: #008CC3 dotted; border-width: 1px;">Написать автору</span>
                </a>
                </div>
                <?
                }
                ?>

            </div>


            <div style="margin-top: 5px; width: 682px;">
                <span style="padding-left: 15px; margin-left: 3px; font-weight: bold; background-image: url('/images/location.png'); background-position: left center; background-repeat: no-repeat;" title="Город">
                <?
                $region_str = $mainblock_data['region']->name;
                if($mainblock_data['region']->name != $mainblock_data['town']->name)
                {
                    $region_str = $mainblock_data['town']->name;
                }
                ?>
                <?= $region_str.", ".$mainblock_data['country']->name;?>
                </span>

                <?
                if(Yii::app()->controller->action->id != 'addpreview')
                {
                ?>
                <div style="float: right;">
                <a href="/user/uadverts/<?= $mainblock['u_id'];?>" class="span_lnk" style="background: url('/images/alladvert-black.png'); background-position: left center; background-repeat: no-repeat; padding-left: 20px;">
                    <span style="border-bottom: #008CC3 dotted; border-width: 1px;">Все объявления автора</span>
                </a>
                </div>
                <?
                }
                ?>

            </div>
            <?
            }
            else
            {
            ?>
                <div style="margin-top: 5px; width: 682px;">
                <span style="padding-left: 15px; margin-left: 3px; font-weight: bold; background-image: url('/images/location.png'); background-position: left center; background-repeat: no-repeat;" title="Город">
                <?
                $region_str = $mainblock_data['region']->name;
                if($mainblock_data['region']->name != $mainblock_data['town']->name)
                {
                    $region_str = $mainblock_data['town']->name;
                }
                ?>
                <?= $region_str.", ".$mainblock_data['country']->name;?>
                </span>

                    <div style="background-color: #f00; color: #fff; font-size: 14px; padding: 5px;">
                    К сожалению, данное объявление потеряло актуальность за сроком давности. Сейчас Вы будете автоматически перенаправлены на похожие объявления в Вашем городе. Нажмите <a class="baralink" style="font-size: 14px;" href="/<?= $advert_url_category;?>">здесь</a>, если Ваш браузер не поддерживает автоматическую переадресацию.
                    <?
                    //Yii::app()->clientScript->registerMetaTag("5; URL=/".$advert_url_category, "archive", "refresh");
                    ?>
                    </div>

                </div>
            <?
            }

            ?>

            <?
            if( ($mainblock['u_id'] == Yii::app()->user->id || Yii::app()->user->isAdmin())
                && Yii::app()->controller->action->id == 'viewadvert')
            {
                if($mainblock['active_tag'] == 0)
                {
                ?>
                <div style="background-color: #f00; padding: 5px; width: 682px; margin-top: 10px; color: #fff;">
                    Объявление неактивно и поэтому не доступно к просмотру другими пользователями!
                </div>
                <?
                }

                if($mainblock['verify_tag'] == 0)
                {
                ?>
                    <div style="background-color: #f00; padding: 5px;  width: 682px; margin-top: 10px; color: #fff;">
                        Объявление еще не верифицировано и поэтому не доступно к просмотру другими пользователями!
                    </div>
                <?
                }
            }
            ?>


            <div id="properties" style="border: #000 solid 0px; margin-top: 5px;">
                <table style="width: auto; margin: 0px; padding: 0px;">
                    <?
                    foreach($addfield_data['notice_props'] as $nkey=>$nval)
                    {
                        ?>
                        <tr>
                            <td style="text-align: right;"><?= $addfield_data['rubrik_props_rp_id'][$nkey]->name;?>:</td>
                            <td>
                                <?
                                switch($addfield_data['rubrik_props_rp_id'][$nkey]->vibor_type)
                                {
                                    case "autoload_with_listitem":
                                    case "selector":
                                    case "listitem":
                                    case "radio":
                                        echo $addfield_data['props_data'][$nval]->value;
                                        break;

                                    case "checkbox":
                                        $temp = array();
                                        foreach($nval as $n2key=>$n2val)
                                        {
                                            $temp[] = $addfield_data['props_data'][$n2val]->value;
                                        }
                                        echo implode(", ", $temp);
                                        break;

                                    case "string":
                                        echo $nval;
                                    break;
                                }
                                ?>
                            </td>
                        </tr>
                    <?
                    }
                    ?>
                </table>

            </div>

            <div style="margin-top: 20px;">
                <?= nl2br($mainblock['notice_text']);?>
            </div>

            <?
            if(Yii::app()->controller->action->id != 'addpreview')
            {
            ?>

            <div style="margin-top: 20px;">

                <div style="margin-top: 10px;">
                    <table style="width: 682px; border: #000099 solid 0px;">
                    <tr>
                        <td style="text-align: center;">
                        <a class="span_lnk" style="background: url('/images/favorit.png'); background-position: left center; background-repeat: no-repeat; padding-left: 17px; margin-left: 0px; text-decoration: none;">
                            <?
                            $favorit_title = 'В избранное';
                            if(Notice::CheckAdvertInFavorit($mainblock['n_id']))
                            {
                                $favorit_title = 'В избранном';
                            }
                            ?>
                            <span id="favorit_button" advert_id="<?= $mainblock['n_id'];?>" style="border-bottom: #008CC3 dotted; border-width: 1px;"><?= $favorit_title;?></span>
                        </a>
                        </td>
                        <td style="text-align: center;">
                        <a class="span_lnk" style="background: url('/images/abuse.png'); background-position: left center; background-repeat: no-repeat; padding-left: 17px; margin-left: 5px; text-decoration: none;">
                            <span id="abuse_button" style="border-bottom: #008CC3 dotted; border-width: 1px;" >Пожаловаться</span>
                        </a>
                        </td>
                        <td style="text-align: center;">
                        <a class="span_lnk" style="background: url('/images/podelit.png'); background-position: left center; background-repeat: no-repeat; padding-left: 15px; margin-left: 5px; text-decoration: none;">
                            <span id="share_button" share_n_id="<?= $mainblock['n_id'];?>" style="border-bottom: #008CC3 dotted; border-width: 1px;">Поделиться</span>
                        </a>
                        </td>
                        <td style="text-align: center;">
                        <script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>
                        <div style="display: inline;" class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="small" data-yashareQuickServices="vkontakte,facebook,twitter,odnoklassniki,moimir,lj,gplus" data-yashareTheme="counter"></div>
                        </td>
                    </tr>
                    </table>
                </div>

            </div>

            <div style="width: 682px; margin-top: 10px;">
                <div style="font-weight: bold;">Похожие<?= " ".$mainblock['keyword_1'];?>:</div>

                <table style="width: auto;">
                    <tr>
                        <?
                        if(count($similar_adverts) > 0)
                        {
                            $i=0;
                            foreach($similar_adverts as $skey=>$sval)
                            {

                                if(!preg_match('|<item><rp_id>[\d]+</rp_id><name>[^<]+</name><selector>[^<]+</selector><vibor_type>photoblock</vibor_type><ps_id>[\d]+</ps_id><hand_input_value>([^<]+)</hand_input_value>|siU', $sval['props_xml'], $match))
                                {
                                    continue;
                                }

                                $i++;
                                ?>
                                <td style="vertical-align: top; text-align: center; width: 120px;">
                                    <?
                                    if(isset($similar_photos[$sval['n_id']][0]))
                                    {
                                        $photoname = str_replace(".", "_medium.", $similar_photos[$sval['n_id']][0]);
                                        $curr_dir = Notice::getPhotoDir($photoname);
                                        ?>
                                        <img style="height: 80px;" src="/<?= Yii::app()->params['photodir'];?>/<?= $curr_dir;?>/<?= $photoname;?>">
                                    <?
                                    }

                                    $transliter = new Supporter();
                                    $advert_page_url = "/".$towns_array[$sval['t_id']]->transname."/".$subrub_array[$sval['r_id']]->transname."/".$transliter->TranslitForUrl($sval['title'])."_".$sval['daynumber_id'];

                                    ?>
                                    <div style="margin-top: 5px;"><a class="baralink" href="<?= $advert_page_url;?>"><?= $sval['title'];?></a></div>

                                    <div  style="color: #777;">
                                    <?= Notice::costCalcAndView(
                                        $sval['cost_valuta'],
                                        $sval['cost'],
                                        Yii::app()->request->cookies['user_valuta_view']->value
                                    );?>

                                    <span><?= Options::$valutes[Yii::app()->request->cookies['user_valuta_view']->value]['symbol2'];?></span>
                                    </div>

                                </td>
                            <?
                                if($i == 5)
                                {
                                    break;
                                }

                            }
                        }
                        ?>
                    </tr>
                </table>
            </div>
            <?
            }
            ?>


        </td>
        <td style="vertical-align: top;">

        <?
        if(Yii::app()->controller->action->id != 'addpreview')
        {
            $banner_operator = Yii::app()->params['banners_raspred'][1];
            include(Yii::getPathOfAlias('webroot')."/banners/".$banner_operator."/right_300.php");
        }
        ?>


        </td>
    </tr>
</table>








<div>
<?
$writeauthor = new FormWriteAuthor();
$writeauthor->n_id = $mainblock['n_id'];
$this->renderPartial('writeauthor', array('writeauthor'=>$writeauthor));


?>
</div>

<div id="abuse_window" style="border: #ddd solid 2px; display: none; padding: 5px; width: 200px; background-color: #fff; z-index: 90; position: absolute;">

    <div id="" style="float: right; margin-top: -5px; cursor: pointer;" onclick="$('#abuse_window').css('display', 'none');">x</div>

    <?
    foreach(Notice::$abuse_items as $akey=>$aval)
    {
    ?>
        <div><a class="<?= $aval['class'];?> span_lnk" abuseclass="<?= $aval['class'];?>" abusetype="<?= $akey;?>" abuse_n_id="<?= $mainblock['n_id'];?>"><?= $aval['name'];?></a></div>
    <?
    }
    ?>

</div>

<div class="form" id="modal_abusecaptcha" style="border: #999 solid 1px; width: 360px; padding: 20px; z-index: 12;">
    <div id="modal_abusecaptcha_close" style="z-index: 13;">X</div>

    <div id="modal_abusecaptcha_content">

    </div>

</div>


<div id="modal_abusecaptcha_overlay"></div>


<!---------------------------- Поделиться ------------------------------>

<div class="form" id="modal_share" style="border: #999 solid 1px; width: 360px; padding: 20px; z-index: 12;">
    <div id="modal_share_close" style="z-index: 13;">X</div>

    <div id="modal_share_content" style="display: table-cell; vertical-align: middle; border: #000000 solid 0px; height: 310px; padding-left: 20px;">

    </div>

</div>


<div id="modal_share_overlay"></div>



<script>

    $('#abuse_button').click(function(){

        $('#abuse_window').css('display', 'block');
        $('#abuse_window').offset({
            left: $('#abuse_button').offset().left - 10,
            top: $('#abuse_button').offset().top - 123
        });

    });

    $(document).ready(function()
    {
        $('.abuse_quick, .abuse_other').click( function(event){
            item = $(this);
            event.preventDefault(); // выключaем стaндaртную рoль элементa
            $('#modal_abusecaptcha_overlay').fadeIn(400, // снaчaлa плaвнo пoкaзывaем темную пoдлoжку
                function(){

                    if(item.attr('abusetype') != 'other_abuse')
                    {
                        $('#modal_abusecaptcha').css('height', '140px');
                    }
                    else
                    {
                        $('#modal_abusecaptcha').css('height', '230px');
                    }

                    $.ajax({
                        url: "<?= Yii::app()->createUrl('/advert/getabuseform');?>",
                        method: "post",
                        data:{
                            n_id: item.attr('abuse_n_id'),
                            class: item.attr('abuseclass'),
                            type: item.attr('abusetype')
                        },
                        // обработка успешного выполнения запроса
                        success: function(data){
                            $('#modal_abusecaptcha_content').html(data);

                        }
                    });

                    $('#abuse_window').css('display', 'none');

                    $('#modal_abusecaptcha')
                        .css('display', 'block') // убирaем у мoдaльнoгo oкнa display: none;
                        .animate({opacity: 1, top: '50%'}, 200); // плaвнo прибaвляем прoзрaчнoсть oднoвременнo сo съезжaнием вниз

                });
        });

        /* Зaкрытие мoдaльнoгo oкнa, тут делaем тo же сaмoе нo в oбрaтнoм пoрядке */
        $('#modal_abusecaptcha_close, #modal_abusecaptcha_overlay').click( function(){ // лoвим клик пo крестику или пoдлoжке
            $('#modal_abusecaptcha')
                .animate({opacity: 0, top: '45%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
                function(){ // пoсле aнимaции
                    $(this).css('display', 'none'); // делaем ему display: none;
                    $('#modal_abusecaptcha_overlay').fadeOut(400); // скрывaем пoдлoжку
                }
            );
        });


        /****************************** Поделиться*********************************/

        $('#share_button').click( function(event){
            item = $(this);
            event.preventDefault(); // выключaем стaндaртную рoль элементa
            $('#modal_share_overlay').fadeIn(400, // снaчaлa плaвнo пoкaзывaем темную пoдлoжку
                function(){

                    $.ajax({
                        url: "<?= Yii::app()->createUrl('/advert/getshareform');?>",
                        method: "post",
                        data:{
                            n_id: item.attr('share_n_id')
                        },
                        // обработка успешного выполнения запроса
                        success: function(data){
                            $('#modal_share_content').html(data);

                        }
                    });

                    $('#modal_share')
                        .css('display', 'block') // убирaем у мoдaльнoгo oкнa display: none;
                        .animate({opacity: 1, top: '50%'}, 200); // плaвнo прибaвляем прoзрaчнoсть oднoвременнo сo съезжaнием вниз

                });
        });

        /* Зaкрытие мoдaльнoгo oкнa, тут делaем тo же сaмoе нo в oбрaтнoм пoрядке */
        $('#modal_share_close, #modal_share_overlay').click( function(){ // лoвим клик пo крестику или пoдлoжке
            $('#modal_share')
                .animate({opacity: 0, top: '45%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
                function(){ // пoсле aнимaции
                    $(this).css('display', 'none'); // делaем ему display: none;
                    $('#modal_share_overlay').fadeOut(400); // скрывaем пoдлoжку
                }
            );
        });

    });

    /******************************************************************/
    $('#favorit_button').click(function(){
        fbut = $(this);

        $.ajax({
            url: "<?= Yii::app()->createUrl('/advert/addtofavorit');?>",
            method: "post",
            dataType: 'json',
            data:{
                n_id: fbut.attr('advert_id')
            },
            // обработка успешного выполнения запроса
            success: function(data){
                $('#favorit_count').html(data['count']);
                if(data['status'] == 'add')
                {
                    $('#favorit_button').html('В избранном');
                }
                else
                {
                    $('#favorit_button').html('В избранное');
                }

            }
        });

    });

</script>

<script>
    Galleria.loadTheme('/js/galleria/themes/classic/galleria.classic.js');
    Galleria.run('#galleria', {
        width: 684,
        height: 484,
        //imageCrop: 'landscape',
        lightbox: true,
        //overlayBackground: '#ffffff'
        showImagenav: true,
        showinfo: false,
        carousel: false,
        thumbPosition: 'center',
        extend: function() {
            var gallery = this; // "this" is the gallery instance
            $('#gallery_fullview').prependTo('.galleria-container');

            $('#gallery_fullview').click(function() {
                gallery.openLightbox();
            });

            // Задание стиля для активного превью
            $('.galleria-image').click(function(){
                $('.galleria-image').css('border', '#fff solid 2px');
                $(this).css('border', '#999 solid 2px');

                $('.galleria-images > .galleria-image').css('border', '0px');

            });

        }

    });



    $('.fchange').change(function ()
    {
        changeFilterReload('<?= Yii::app()->createUrl('filter/getdatafilter');?>');
    });

    $(function(){
        // Закрытие таблицы валют по клику за пределами таблицы
        $(document).click(function(event) {
            //valute_symbol
            //console.log($(event.target).closest("#valute_symbol").length);
            if($(event.target).closest("#valute_symbol").length)
            {
                return;
            }

            if ($(event.target).closest("#div_valute_change").length)
            {
                return;
            }

            $("#div_valute_change").css('display', 'none');

            if ($(event.target).closest("#abuse_button").length)
            {
                return;
            }
            if ($(event.target).closest("#abuse_window").length)
            {
                return;
            }

            $("#abuse_window").css('display', 'none');


            event.stopPropagation();
        });

        // Позиционирование таблицы валют
        $('#div_valute_change').offset({
            left: $('#valute_symbol').offset().left-125,
            top: $('#valute_symbol').offset().top + $('#valute_symbol').height() + 5
        });


    });

    // Отобразить телефон
    <?
        // Для формирования ключа
        $curr_time = time();
        $start_day = mktime(0,0,0,intval(date("m", $curr_time)), intval(date("d", $curr_time)), intval(date("Y", $curr_time)));
        $int_key = floor(($curr_time - $start_day) / 1800);

    ?>

    $('#display_phone').click(function(event){

        $('#display_phone').css('display', 'none');
        $('#img_display_phone').attr('src', '<?= Yii::app()->createUrl('supporter/displayphone', array('n_id'=>$mainblock['n_id'], 'bkey'=>md5($mainblock['n_id'].Yii::app()->params['security_key'].$int_key)));?>')
        $('#img_display_phone').css('display', 'inline');

    })

</script>


<style>
    #modal_abusecaptcha, #modal_share {
        width: 300px;
        height: 310px; /* Рaзмеры дoлжны быть фиксирoвaны */
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

    #modal_abusecaptcha_close, #modal_share_close {
        width: 21px;
        height: 21px;
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        display: block;
    }

    /* Пoдлoжкa */
    #modal_abusecaptcha_overlay, #modal_share_overlay {
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
