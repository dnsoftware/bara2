<?
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/galleria/galleria-1.4.2.js');

Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/notice/advertpage.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/notice/noticepage.js', CClientScript::POS_END);
?>

<?
if(Yii::app()->controller->action->id == 'addpreview')
{
?>
<div id="editpubbuttons">
    <?
    if(Yii::app()->user->id > 0)
    {
//echo "Вы залогинены, все ок";
    ?>
        <a id="preview-editbutton" href="<?= Yii::app()->createUrl('advert/addadvert');?>">Редактировать</a>

        <a id="preview-savebutton" href="<?= Yii::app()->createUrl('advert/savenew');?>">Опубликовать</a>
    <?
    }
    ?>
</div>
<?
}
?>

<h1 id="adverttitul"><?= $mainblock['title'];?></h1>

<span id="cost_block">

    Цена:
    <?
    if($mainblock['cost_nodisplay_tag'] == 0)
    {
    ?>
         <?= number_format(Notice::costCalcAndView(
        $mainblock['cost_valuta'],
        $mainblock['cost'],
        Yii::app()->request->cookies['user_valuta_view']->value
        ), 0, '.', ' ');?>

        <span id="valute_symbol" onclick="displayHide('div_valute_change');"><?= Options::$valutes[Yii::app()->request->cookies['user_valuta_view']->value]['symbol'];?></span>
    <?
    }
    else
    {
    ?> <span id="valute_symbol">не указана</span> <?
    }
    ?>

</span>

<div id="div_valute_change">

    <div id="valute_change_close" onclick="$('#div_valute_change').css('display', 'none');"></div>

    <div id="divvch">
        <table cellpadding="0" id="tbl_valute_change">
            <tr>
                <?
                foreach(Options::$valutes as $vkey=>$vval)
                {
                    ?>
                    <td class="valutecell">
                        <a class="baralink vc" href="<?= Yii::app()->createUrl('supporter/setvalutaview', array('valuta_view'=>$vval['abbr']));?>"><?= $vval['name_rodit'];?></a>
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
                    <td class="valuteval">
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

    <div id="cbrtext">
        *по курсу ЦБ на <?= date("d.m.Y", Yii::app()->params['options']['kurs_date']);?>
        <? //Yii::app()->params['month_padezh'][intval(date("m", Yii::app()->params['options']['kurs_date']))];?>
        <? //date("Y", Yii::app()->params['options']['kurs_date']);?>
        <div>
            Для отображения цен на сайте в другой валюте нажмите на ее название
        </div>
    </div>

</div>

<div id="topadvertdata">
<?
    if(Yii::app()->controller->action->id == 'addpreview')
    {
        $mainblock['date_add'] = time();
    }

    $date_add_str = date('d-m-Y H:i', $mainblock['date_add']);
    $day_string = Notice::TodayStrGenerate($mainblock['date_add'], 1);
?>
    <div id="timeadd">
        <span id="timedata" title="Время размещения объявления"></span> <?= $day_string;?>

        <?
        if(Yii::app()->controller->action->id != 'addpreview')
        {
        ?>
        <span id="daynumber">№ объявления: <?= $mainblock['daynumber_id'];?></span>
        <?
        }
        ?>
    </div>

    <?
    if(Yii::app()->controller->action->id != 'addpreview')
    {
    ?>
    <div id="viewscount">
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
        <td id="photoscell">
<?
//deb::dump(Yii::app()->controller->action->id);
//deb::dump(Yii::app()->user->create_at);
?>
            <div id="notice">
                <div class="galleria" id="galleria">
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
                <div id="gallery_fullview">
                    <div></div>
                </div>
            <?
            }
            ?>

            <?
            if( ($mainblock['date_expire'] > time() && isset($mainblock['deleted_tag']) && $mainblock['deleted_tag'] == 0 && $mainblock['active_tag'] == 1)
                || Yii::app()->controller->action->id == 'addpreview')
            {
                if(Yii::app()->controller->action->id == 'addpreview')
                {
                    $mainblock['user_date_reg'] = Yii::app()->user->create_at;
                }
            ?>
            <table id="advertattributes">
            <tr>

            <td id="client_pict"><img src="/images/client.png"></td>

            <td id="client_name">
            <div id="div_client_name">
                <span id="span_client_name" title="Имя"><a id="a_client_name" href="/user/uadverts/<?= $mainblock['u_id'];?>"><?= $mainblock['client_name'];?></a></span>
                <span>на baraholka.ru с <?= Yii::app()->params['month_padezh'][intval(date("m", strtotime($mainblock['user_date_reg'])))];?> <?= date("Y", strtotime($mainblock['user_date_reg']));?> года
                </span>
            </div>
            </td>

            <td id="client_do" rowspan="2">
            <?
            if(Yii::app()->controller->action->id != 'addpreview')
            {
                ?>
                <div id="phoneauth">
                    <a id="a_display_phone" class="span_lnk"><span id="display_phone">Показать телефон</span><img id="img_display_phone" src="/images/actions/loader.gif"></a>

                    <a id="a_write_author" class="span_lnk">
                        <span id="writeauthor_btn">Написать автору</span>
                    </a>
                </div>

                <div id="div_alladverts">
                    <a href="/user/uadverts/<?= $mainblock['u_id'];?>" class="span_lnk">
                        <span id="span_alladverts">Все объявления автора</span>
                    </a>
                </div>

                <?
                if(Yii::app()->controller->action->id != 'addpreview')
                {
                ?>
                <?
                }
                ?>

            <?
            }
            ?>
            </td>
            </tr>


            <tr>
            <td id="td_location_pict"><img src="/images/location.png"></td>
            <td id="td_location_data">
            <div>
            <span id="span_location" title="Город">
            <?
            $region_str = $mainblock_data['region']->name;
            if($mainblock_data['region']->name != $mainblock_data['town']->name)
            {
                $region_str = $mainblock_data['town']->name;
            }
            ?>
            <?= $region_str.", ".$mainblock_data['country']->name;?>
            </span>

            </div>

            </td>
            </tr>

            </table>


            <?
            }
            else
            {
            ?>
                <div id="advnoact">
                <span id="advnoacttown" title="Город">
                <?
                $region_str = $mainblock_data['region']->name;
                if($mainblock_data['region']->name != $mainblock_data['town']->name)
                {
                    $region_str = $mainblock_data['town']->name;
                }
                ?>
                <?= $region_str.", ".$mainblock_data['country']->name;?>
                </span>

                    <div id="noacttext">
                    К сожалению, данное объявление потеряло актуальность за сроком давности. Сейчас Вы будете автоматически перенаправлены на похожие объявления в Вашем городе. Нажмите <a id="a_noact" class="baralink" href="/<?= $advert_url_category;?>">здесь</a>, если Ваш браузер не поддерживает автоматическую переадресацию.
                    <?
                    Yii::app()->clientScript->registerMetaTag("5; URL=/".$advert_url_category, "archive", "refresh");
                    ?>
                    </div>

                </div>
            <?
            }

            if( ($mainblock['u_id'] == Yii::app()->user->id || Yii::app()->user->isAdmin())
                && Yii::app()->controller->action->id == 'viewadvert')
            {
                if($mainblock['active_tag'] == 0)
                {
                ?>
                <div id="nedostup">
                    Объявление неактивно и поэтому не доступно к просмотру другими пользователями!
                </div>
                <?
                }

                if($mainblock['verify_tag'] == 0)
                {
                ?>
                    <div id="advnoverify">
                        Объявление еще не верифицировано и поэтому не доступно к просмотру другими пользователями!
                    </div>
                <?
                }
            }
            ?>


            <div id="properties">
                <table id="tbl_properties">
                    <?
                    foreach($addfield_data['notice_props'] as $nkey=>$nval)
                    {
                        ?>
                        <tr>
                            <td class="right"><?= $addfield_data['rubrik_props_rp_id'][$nkey]->name;?>:</td>
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

            <div id="notice_text">
                <?= nl2br($mainblock['notice_text']);?>
            </div>

            <?
            if(Yii::app()->controller->action->id != 'addpreview')
            {
            ?>

            <div id="do_ipp">

                    <table id="do_table">
                    <tr>
                        <td style="text-align: center;">
                        <a class="span_lnk" id="a_favorit">
                            <?
                            $favorit_title = 'В избранное';
                            if(Notice::CheckAdvertInFavorit($mainblock['n_id']))
                            {
                                $favorit_title = 'В избранном';
                            }
                            ?>
                            <span id="favorit_button" advert_id="<?= $mainblock['n_id'];?>"><?= $favorit_title;?></span>
                        </a>
                        </td>
                        <td id="td_abuse">
                        <a class="span_lnk" id="a_abuse">
                            <span id="abuse_button">Пожаловаться</span>
                        </a>
                        </td>
                        <td style="text-align: center;">
                        <a class="span_lnk" id="a_share">
                            <span id="share_button" share_n_id="<?= $mainblock['n_id'];?>">Поделиться</span>
                        </a>
                        </td>
                        <td id="td_socials">
                        <script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>
                        <div style="display: inline;" class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="small" data-yashareQuickServices="vkontakte,facebook,twitter,odnoklassniki,moimir,lj,gplus" data-yashareTheme="counter"></div>
                        </td>
                    </tr>
                    </table>

            </div>

            <div style="width: 682px; margin-top: 10px;">
                <div style="font-weight: bold;" class="pohozh"><h3>Похожие<?= " ".$mainblock['keyword_1'];?>:</h3></div>

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
                                <td class="td_similar">
                                    <?
                                    //deb::dump($towns_array);
                                    $transliter = new Supporter();
                                    $advert_page_url = "/".$towns_array[$sval['t_id']]->transname."/".$subrub_array[$sval['r_id']]->transname."/".$transliter->TranslitForUrl($sval['title'])."_".$sval['daynumber_id'];
                                    $colphotos = count($similar_photos[$sval['n_id']]);

                                    $image_title = str_replace("'", '', $sval['title']);
                                    $image_title_alt = addslashes($image_title);
                                    $image_title = 'Объявление &laquo;'.addslashes($image_title).'&raquo;'.' ('.$colphotos.' фото)';

                                    if(isset($similar_photos[$sval['n_id']][0]))
                                    {
                                        $photoname = str_replace(".", "_medium.", $similar_photos[$sval['n_id']][0]);
                                        $curr_dir = Notice::getPhotoDir($photoname);
                                        ?>
                                    <a class="baralink img_similar" title="<?= $image_title;?>" href="<?= $advert_page_url;?>"><img   alt="<?= $image_title_alt;?>" src="/<?= Yii::app()->params['photodir'];?>/<?= $curr_dir;?>/<?= $photoname;?>"></a>
                                    <?
                                    }

                                    $ahref_title = str_replace("'", '', $sval['title']);
                                    $ahref_title = addslashes($ahref_title.' в г. '.$towns_array[$sval['t_id']]->name);
                                    ?>
                                    <div class="divsimname"><h3 class="h_similar"><a title="<?= $ahref_title;?>" class="baralink" href="<?= $advert_page_url;?>"><?= $sval['title'];?></a></h3></div>

                                    <?
                                    if($sval['cost'] > 0)
                                    {
                                    ?>
                                    <div  style="color: #777;">
                                    <?= Notice::costCalcAndView(
                                        $sval['cost_valuta'],
                                        $sval['cost'],
                                        Yii::app()->request->cookies['user_valuta_view']->value
                                    );?>

                                    <span><?= Options::$valutes[Yii::app()->request->cookies['user_valuta_view']->value]['symbol2'];?></span>
                                    </div>
                                    <?
                                    }
                                    ?>

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

<div id="abuse_window">

    <div id="awclose" onclick="$('#abuse_window').css('display', 'none');">x</div>
    <?
    foreach(Notice::$abuse_items as $akey=>$aval)
    {
    ?>
        <div><a class="<?= $aval['class'];?> span_lnk" abuseclass="<?= $aval['class'];?>" abusetype="<?= $akey;?>" abuse_n_id="<?= $mainblock['n_id'];?>"><?= $aval['name'];?></a></div>
    <?
    }
    ?>

</div>

<div class="form" id="modal_abusecaptcha">
    <div id="modal_abusecaptcha_close">X</div>

    <div id="modal_abusecaptcha_content">

    </div>

</div>


<div id="modal_abusecaptcha_overlay"></div>


<!---------------------------- Поделиться ------------------------------>

<div class="form" id="modal_share">
    <div id="modal_share_close">X</div>

    <div id="modal_share_content">

    </div>

</div>


<div id="modal_share_overlay"></div>



<script>


    $(document).ready(function()
    {
        share_and_abuse('<?= Yii::app()->createUrl('/advert/getabuseform');?>',
            '<?= Yii::app()->createUrl('/advert/getshareform');?>',
            '<?= Yii::app()->createUrl('/advert/addtofavorit');?>');
    });


    $('.fchange').change(function ()
    {
        changeFilterReload('<?= Yii::app()->createUrl('filter/getdatafilter');?>');
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


