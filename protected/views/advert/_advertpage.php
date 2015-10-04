<?
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/galleria/galleria-1.4.2.js');
?>

<div style="margin-top: 10px; margin-bottom: 5px; color: #000; border: #000020 solid 0px; ">
<?
    $date_add_str = date('d-m-Y H:i', $mainblock['date_add']);
    $day_string = Notice::TodayStrGenerate($mainblock['date_add'], 1);
?>
    <div style=" float: left; width: 682px; border: #000000 solid 0px;">
        <span style="padding-left: 15px; margin-right: 3px; background-image: url('/images/dateadd.png'); background-position: left center; background-repeat: no-repeat;" title="Время размещения объявления"></span> <?= $day_string;?>

        <span style="float: right; color: #999;">№ объявления: <?= $mainblock['daynumber_id'];?></span>

    </div>

    <div style="float: right; clear: right;">
        Просмотров: всего <?= $mainblock['counter_total']+1;?>, сегодня <?= $mainblock['counter_daily']+1;?>
        <img src="<?= Yii::app()->createUrl('supporter/advertcounter', array('n_id'=>$mainblock['n_id']));?>" width="0">
    </div>
    <br>
</div>


<table>
    <tr>
        <td style="vertical-align: top; padding: 0px;">
<?
//deb::dump(Yii::app()->controller->action->id);
?>
            <div id="notice" style="" >
                <div class="galleria" id="galleria" style="width: 600px;">
                    <?
                    //deb::dump($uploadfiles_array);
                    $part_path = '/photos/';
                    if($mainblock['n_id'] <= 0)
                    {
                        $part_path = '/tmp/';
                    }

                    $i=0;
                    foreach($uploadfiles_array as $ukey=>$uval)
                    {
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
                        ?>
                            <a href="<?= Notice::getPhotoName($part_path.$uval, "_big");?>"><img data-big="<?= $part_path.$uval;?>" src="<?= Notice::getPhotoName($part_path.$uval, "_thumb");?>" data-title="<?= htmlspecialchars($mainblock['title']). " за ".Notice::costCalcAndView(
                                    $mainblock['cost_valuta'],
                                    $mainblock['cost'],
                                    Yii::app()->request->cookies['user_valuta_view']->value)." ".$valuta_symbol;?>" data-description="<?= htmlspecialchars($mainblock['title']);?>"></a>
                        <?
                        }

                    }
                    ?>

                </div>
            </div>

            <div id="gallery_fullview" style="z-index: 10; position: absolute; top: 10px; left: 570px; cursor: pointer; ">
                <div style="background-image: url('/images/lupa_s.png'); background-position: 0px 0px; width: 20px; height: 20px;"></div>
            </div>

            <div style="margin-top: 10px; font-weight: normal; width: 682px; border: #000000 solid 0px;">

                <span style="padding-left: 20px; background-image: url('/images/client.png'); background-position: left center; background-repeat: no-repeat; font-weight: bold;" title="Имя"><?= $mainblock['client_name'];?></span>
                <span style="font-weight: normal;">на baraholka.ru с <?= Yii::app()->params['month_padezh'][intval(date("m", strtotime($mainblock['user_date_reg'])))];?> <?= date("Y", strtotime($mainblock['user_date_reg']));?> года
                </span>

                <div style="float: right; ">
                <a class="span_lnk" style="background: url('/images/phone-black.png'); background-position: left center; background-repeat: no-repeat; padding-left: 17px; width: 135px;"><span id="display_phone"  style="border-bottom: #008CC3 dotted; border-width: 1px; ">Показать телефон</span><img id="img_display_phone" src="/images/actions/loader.gif" style="display: none; margin-bottom: -8px; height: 20px;"></a>

                <a class="span_lnk" style="margin-left: 15px; background: url('/images/write-black.png'); background-position: left center; background-repeat: no-repeat; padding-left: 17px;">
                    <span id="writeauthor_btn" style="border-bottom: #008CC3 dotted; border-width: 1px;">Написать автору</span>
                </a>
                </div>

            </div>


            <div style="margin-top: 5px; width: 682px;">
                <span style="padding-left: 15px; margin-left: 3px; font-weight: bold; background-image: url('/images/location.png'); background-position: left center; background-repeat: no-repeat;" title="Город">
                <?
                $region_str = $mainblock_data['region']->name;
                if($mainblock_data['region']->name != $mainblock_data['town']->name)
                {
                    $mainblock_data['town']->name .= ", ".$region_str;
                }
                ?>
                <?= $region_str.", ".$mainblock_data['country']->name;?>
                </span>

                <div style="float: right;">
                <a class="span_lnk" style="background: url('/images/alladvert-black.png'); background-position: left center; background-repeat: no-repeat; padding-left: 20px;">
                    <span style="border-bottom: #008CC3 dotted; border-width: 1px;">Все объявления автора</span>
                </a>
                </div>

            </div>

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
                <div style="font-weight: bold;">Комментарий продавца</div>
                <?= $mainblock['notice_text'];?>
            </div>

            <div style="margin-top: 20px;">

                <div style="margin-top: 10px;">
                    <a class="span_btn" style="margin-left: 0px; text-decoration: none;">
                        <span style="border-bottom: #008CC3 dotted; border-width: 1px;">В избранное</span>
                    </a>

                    <a class="span_btn" style="margin-left: 5px; text-decoration: none;">
                        <span style="border-bottom: #008CC3 dotted; border-width: 1px;">Пожаловаться</span>
                    </a>

                    <a class="span_btn" style="margin-left: 5px; text-decoration: none;">
                        <span style="border-bottom: #008CC3 dotted; border-width: 1px;">Поделиться</span>
                    </a>

                    <script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>
                    <div style="display: inline;" class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="small" data-yashareQuickServices="vkontakte,facebook,twitter,odnoklassniki,moimir,lj,gplus" data-yashareTheme="counter"></div>
                </div>

            </div>



        </td>
        <td style="vertical-align: top;">

            <!-- Яндекс.Директ -->
            <div id="yandex_ad2" style="float: right;"></div>
            <script type="text/javascript">
                (function(w, d, n, s, t) {
                    w[n] = w[n] || [];
                    w[n].push(function() {
                        Ya.Direct.insertInto(150187, "yandex_ad2", {
                            ad_format: "direct",
                            type: "posterVertical",
                            limit: 3,
                            title_font_size: 3,
                            links_underline: false,
                            site_bg_color: "FFFFFF",
                            title_color: "008CC3",
                            url_color: "777777",
                            text_color: "000000",
                            hover_color: "008CC3",
                            favicon: true,
                            no_sitelinks: true
                        });
                    });
                    t = d.getElementsByTagName("script")[0];
                    s = d.createElement("script");
                    s.src = "//an.yandex.ru/system/context.js";
                    s.type = "text/javascript";
                    s.async = true;
                    t.parentNode.insertBefore(s, t);
                })(window, document, "yandex_context_callbacks");
            </script>


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