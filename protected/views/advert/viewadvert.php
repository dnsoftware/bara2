<?
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.sumoselect.min.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/sumoselect.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/filtercontroller.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/baraholka.js');
?>

<?
$this->renderPartial('/filter/_search_form', array(
    'rub_array'=>$rub_array,
    'mselector'=>$mselector,
    'm_id'=>$m_id,
));
?>


<div style="text-align: left; padding-left: 14px; margin-top: 10px; height: 120px;">
<?
include(Yii::getPathOfAlias('webroot')."/banners/yandex/top_horizont.php");
?>
</div>


<div style="margin: 10px 0px 10px 0px">
    <?
    $url_parts = array();
    $url = '';
    $bread_count = count($breadcrumbs);
    $i=0;
    foreach ($breadcrumbs as $bkey=>$bval)
    {
        $i++;

        if($bval['type']=='subrubrik')
        {
            $url_parts[$bkey-1] = $bval['transname'];
        }
        else
        {
            $url_parts[$bkey] = $bval['transname'];
        }

        $url = implode("/", $url_parts);;
        ?>
        <a  class="baralink" href="/<?= $url;?>"><?= $bval['name'];?></a>
        <?
        if($i != $bread_count)
        {
            echo "<span class='baralink'> > </span>";
        }
    }
    ?>
</div>



<h1 style="display: inline; font-size: 28px; padding: 3px; "><?= $mainblock['title'];?></h1>

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

                <a class="baralink" href="<?= Yii::app()->createUrl('supporter/setvalutaview', array('valuta_view'=>$vval['abbr']));?>"><?= $vval['name_rodit'];?></a>
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




<?
$this->renderPartial('_advertpage', array(
    'mainblock'=>$mainblock,
    'addfield'=>$addfield,
    'uploadfiles_array'=>$this->uploadfiles_array,
    'mainblock_data'=>$this->mainblock_data,
    'addfield_data'=>$this->addfield_data,
    'options'=>$this->options,
));

?>


<script>
    Galleria.loadTheme('/js/galleria/themes/classic/galleria.classic.min.js');
    Galleria.run('#galleria', {
        width: 670,
        height: 520,
        //imageCrop: 'landscape',
        lightbox: true,
        //overlayBackground: '#ffffff'
        showImagenav: true,
        showinfo: false,
        extend: function() {
            var gallery = this; // "this" is the gallery instance
            $('#gallery_fullview').prependTo('.galleria-container');

            $('#gallery_fullview').click(function() {
                gallery.openLightbox();
            });
        }

    });

//    this.addElement('gallery_fullview');
//    this.appendChild('galleria','gallery_fullview');


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
                return;

            $("#div_valute_change").css('display', 'none');

            event.stopPropagation();
        });

        // Позиционирование таблицы валют
        $('#div_valute_change').offset({
            left: $('#valute_symbol').offset().left-125,
            top: $('#valute_symbol').offset().top + $('#valute_symbol').height() + 5
        });//$('#mesto_id').offset().left;

    });
</script>