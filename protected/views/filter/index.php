<?php
/* @var $this FilterController */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.sumoselect.min.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/sumoselect.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/filtercontroller.js');

Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/nouislider/nouislider.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/nouislider/nouislider.tooltips.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/nouislider/nouislider.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/wNumb.js');

// Стили данного отображения
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/filter/index.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/filter/index.js', CClientScript::POS_END);

Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/filter/listitemshablons.css');

?>

<div id="form_search">
<?

$this->renderPartial('/filter/_search_form', array(
    'rub_array'=>$rub_array,
    'mselector'=>$mselector,
    'm_id'=>$m_id,
    'props_sprav_sorted_array'=>$props_sprav_sorted_array,
    'rubriks_props_array'=>$rubriks_props_array,
    'cookie'=>$cookie

));

?>
    <div id="breadcrumbs">
        <?
        $url_parts = array();
        $url = '';
        $url_part_groups = array();
        $bread_count = count($this->breadcrumbs);
        $i=0;
        foreach ($this->breadcrumbs as $bkey=>$bval)
        {
            $i++;
            if($bval['type']=='subrubrik')
            {
                $url_parts[$bkey-1] = $bval['transname'];
                $url_part_groups[$bkey-1] = $bval;
            }
            else
            {
                $url_parts[$bkey] = $bval['transname'];
                $url_part_groups[$bkey] = $bval;
            }
            $url = implode("/", $url_parts);;
            if($url == 'all')
            {
                $url = '';
            }
            ?>
            <a  class="baralink" href="/<?= $url;?>"><?= $bval['name'];?></a>
            <?
            if($i != $bread_count)
            {
                echo "<span class='baralink'> > </span>";
            }
        }

        if($bread_count == 0)
        {
            $url_parts[1] = 'all';
        }

        $url_group = $url;

        if($url_part_groups[count($url_part_groups)]['type'] == 'rubrik')
        {
            unset($url_part_groups[count($url_part_groups)]);
        }

        $url_part_groups_array = array();
        if(isset($url_part_groups) && count($url_part_groups) > 0)
        {
            foreach($url_part_groups as $ukey=>$uval)
            {
                $url_part_groups_array[] = $uval['transname'];
            }
            $url_group = implode("/", $url_part_groups_array);
        }
        ?>
    </div>

    <div id="topban">
        <?
        $banner_operator = Yii::app()->params['banners_raspred'][0];
        include(Yii::getPathOfAlias('webroot')."/banners/".$banner_operator."/top_horizont.php");
        ?>
    </div>


    <div id="search_data">

    </div>


</div>

<?
if($h1_text != '')
{
?>
    <h1><?= $h1_text;?></h1>
<?
}
?>



<?
if($display_titul_tag == 0)
{
?>
<div id="subcats">
<?
if(count($rubrik_groups) > 0)
{
    $page_url = Yii::app()->getRequest()->getUrl();

    $allreg_prefix = "";
    if($m_id == 0 && ($page_url == '/' || preg_match('|index.php|siU', $page_url, $match) ) )
    {
        $allreg_prefix = '/all';
    }

    foreach ($rubrik_groups as $rkey=>$rval)
    {
        if($rval->hide_tag == 1)
        {
            continue;
        }
        $href = $allreg_prefix.Yii::app()->createUrl($url_group."/".$rval['transname']/*$rval['path']*/);
        ?>
        <nobr><a class="cat_count" href="<?= $href;?>"><?= $rval['name'];?></a> <span class="notcount" ><?= $rval['cnt'];?></span></nobr>
    <?
    }
}
?>
</div>
<?
}

if($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php' )
{
    $this->renderPartial('/filter/_titulrubriks', array(
        'rub_array'=>$rub_array,
        'url_parts'=>$url_parts
    ));


}
?>


<table style="" cellpadding="0" cellspacing="0" >
<tr>
    <td id="noticelist">

        <?
        if(count($search_adverts) > 1)
        {
        ?>
        <div id="bfiltersort">
            <select id="filtersort">
                <?
                foreach(Notice::$sort_codes as $key=>$val)
                {
                    $selected = " ";
                    if($_GET['params']['s'] == $key )
                    {
                        $selected = " selected ";
                    }


                    if($key == 'rl' && trim($_GET['params']['q'] == ''))
                    {
                        continue;
                    }

                    ?>
                    <option value="<?= $key;?>" <?= $selected;?>><?= $val;?></option>
                <?
                }
                ?>
            </select>
        </div>
        <?
        }
        ?>

        <table >
        <?
        $k=0;
        foreach($search_adverts as $key=>$val)
        {
            $k++;
            // Для главного титула кол-во показываемых объяв на странице другое,
            // остальные скрыты до нажатия
            $hidetype = '';
            //if(intval($_GET['mainblock']['r_id']) == 0 && !isset($_GET['prop']) && !isset($_GET['addfield'])
            //    && !isset($_GET['page']) && $k>10)
            if(($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php') && $k>10)
            {
                $hidetype = 'titulhide';
            }

        ?>
        <tr class="<?= $hidetype;?>">
            <td class="itemphoto">
            <?
            if(count($props_array[$key]['photos']) > 0)
            {
                $transliter = new Supporter();
                $advert_page_url = "/".$val['town_transname']."/".$rubriks_all_array[$val['r_id']]->transname."/".$transliter->TranslitForUrl($val['title'])."_".$val['daynumber_id'];
            ?>
                <div class="bphoto">
                    <?
                    $photoname = Notice::getPhotoName($props_array[$key]['photos'][0], "_medium");
                    $curr_dir = Notice::getPhotoDir($photoname);
                    $alt = str_replace("'", '"', $val['title']);
                    ?>
                    <a href="<?= $advert_page_url;?>"><img alt='<?= $alt;?>' src="/<?= Yii::app()->params['photodir'];?>/<?= $curr_dir;?>/<?= $photoname;?>"></a>
                    <?
                    if(count($props_array[$key]['photos']) > 1)
                    {
                    ?>
                    <div class="colphoto"><div><?= count($props_array[$key]['photos']);?></div></div>
                    <?
                    }
                    ?>
                </div>
            <?
            }
            ?>
            </td>
            <td class="itemdata">
            <?= $props_array[$key]['props_display'];?>
            </td>
        </tr>

        <?
        /* // Для смены цены прямо на сайте, раскомментировать при необходимости
        if(isset($_GET['changeprice']))
        {
        ?>
        <tr>
            <td colspan="2">
                <div style="padding: 5px; width:160px;; border: #ff4444 solid 2px;" >
                    <input style="font-size: 26px; width: 150px; border: none;" type="text" class="changeprice" id="pricechange_<?= $val['n_id'];?>" value="<?= $val['cost'];?>">

                    <select name="cost_valuta" id="cost_valuta_<?= $val['n_id'];?>">
                        <?
                        foreach (Options::$valutes as $vkey=>$vval)
                        {
                            $selected = ' ';
                            if($val['cost_valuta'] == $vkey)
                            {
                                $selected = ' selected ';
                            }
                            ?>
                            <option <?= $selected;?> value="<?= $vkey;?>"><?= $vkey;?></option>
                        <?
                        }
                        ?>
                    </select>


                    <input type="button" class="changeprice_button" value="Сохранить" cvalue="<?= $val['n_id'];?>">
                </div>
                <br><br>
                <?= $val['notice_text'];?>
            </td>
        </tr>
        <?
        }
        */
        ?>

        <tr class="<?= $hidetype;?>">
            <td style="height: 10px;"></td><td></td>
        </tr>
        <?
        }
        ?>
        </table>

        <div style="text-align: center;" class="<?= $hidetype;?>" id="paginator">
        <?
        $this->widget('application.extensions.bpaginator.BPaginatorWidget', $paginator_params);
        ?>
        </div>


    </td>
    <td id="cell_titultext">


        <?
        if($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php')
        {
            $this->renderPartial('/filter/_titultext', array(

            ));
        }
        ?>


        <aside>
            <div id="aside_div">
            <?
            $banner_operator = Yii::app()->params['banners_raspred'][1];
            include(Yii::getPathOfAlias('webroot')."/banners/".$banner_operator."/right_300.php");
            ?>
            </div>
        </aside>

    </td>
</tr>
</table>

<?
if($hidetype == 'titulhide')
{
?>
<span id="razvorot">
    <span>
       <span id="display_otheradverts">Показать еще</span>
    </span>
</span>
<?
}
?>

<script>


    $(document).ready(function()
    {
        filter_index_init('<?= Yii::app()->createUrl('/advert/addtofavorit');?>',
                          '<?= Yii::app()->createUrl('/filter/setsortmode');?>');
    });


    <?
    /* // для смены цен прямо на сайте. Раскомментировать при необходимости
    ?>

    $('.changeprice').click(function(){
        input = $(this);

        input.css('border', '');
    });


    $('.changeprice_button').click(function(){
        button = $(this);

        $.ajax({
            url: "<?= Yii::app()->createUrl('/filter/changeprice');?>",
            method: "post",
            dataType: 'json',
            data:{
                n_id: button.attr('cvalue'),
                price: $('#pricechange_'+button.attr('cvalue')).val(),
                cost_valuta: $('#cost_valuta_'+button.attr('cvalue')).val()
            },

            // обработка успешного выполнения запроса
            success: function(data){
                $('#pricechange_'+button.attr('cvalue')).css('border', 'none');
                $('#pricechange_'+button.attr('cvalue')).css('background-color', '#0f0');

            }
        });

    });
    <?
    */
    ?>

</script>
