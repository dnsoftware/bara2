<?php
/* @var $this FilterController */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.sumoselect.min.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/sumoselect.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/filtercontroller.js');

?>

<div id="form_search" style="margin-bottom: 25px; ">


<?
//deb::dump($query_delta);
?>


<?

$this->renderPartial('/filter/_search_form', array(
    'rub_array'=>$rub_array,
    'mselector'=>$mselector,
    'm_id'=>$m_id,
    'props_sprav_sorted_array'=>$props_sprav_sorted_array,
    'rubriks_props_array'=>$rubriks_props_array,

));

?>


<div style="margin: 5px; 0px 5px 0px">
    <?
    $url_parts = array();
    $url = '';
    $bread_count = count($this->breadcrumbs);
    $i=0;
    foreach ($this->breadcrumbs as $bkey=>$bval)
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


<div style="text-align: left; padding-left: 14px;">
    <?
    include(Yii::getPathOfAlias('webroot')."/banners/yandex/top_horizont.php");
    ?>
</div>





    <div id="search_data">

    </div>

    <script>
/*
        $('#select_country').change(function ()
        {
            $.ajax({
                type: 'POST',
                url: '<?= Yii::app()->createUrl('advert/get_html_regions');?>',
                data: 'c_id='+$(this).val(),
                success: function(msg){
                    $('#select_region').html(msg);

                    //$('#select_region')[0].sumo.unload();
                    //$('#select_region').SumoSelect();
                    $('#select_region').change();
                }
            });
        });

        $('#select_region').change(function ()
        {
            $.ajax({
                type: 'POST',
                url: '<?= Yii::app()->createUrl('advert/get_html_towns');?>',
                data: 'reg_id='+$(this).val(),
                success: function(msg){
                    $('#select_town').html(msg);

                    //$('#select_town')[0].sumo.unload();
                    //$('#select_town').SumoSelect();
                }
            });
        });

        function advertFilter(action)
        {
            $.ajax({
                type: 'POST',
                url: action,
                data: $('#form_filter').serialize(),
                success: function(msg){

                    $('#search_data').html(msg);

                }
            });
        }
*/

    </script>

</div>


<?

foreach ($rubrik_groups as $rkey=>$rval)
{

?>
    <a style="margin-left: 10px;" class="baralink_plus" href="<?= Yii::app()->createUrl($rval['path']);?>"><?= $rval['name'];?></a> <span class="notcount" ><?= $rval['cnt'];?></span>
<?
}
//deb::dump($props_array);
?>
<table style="" cellpadding="0" cellspacing="0" >
<tr>
    <td style="vertical-align: top;  border: #000020 solid 0px; width: 720px; padding: 0;">
        <table style="">
        <?
        //deb::dump(count($search_adverts));
        foreach($search_adverts as $key=>$val)
        {
        ?>
        <tr style="">
            <td style="width: 125px;">
            <?
            if(count($props_array[$key]['photos']) > 0)
            {
            ?>
                <img src="/photos/<?= Notice::getPhotoName($props_array[$key]['photos'][0], "_thumb");?>">
            <?
            }
            ?>
            </td>
            <td>
            <?= $props_array[$key]['props_display'];?>
            </td>
        </tr>
        <?
        }
        ?>
        </table>
    </td>
    <td style="vertical-align: top; height: 1000px;  border: #000020 solid 0px; width: 300px; padding: 0">
        <aside>
            <div style="width: 300px; height: 600px; border: #000020 solid 0px;">
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
                </script>            </div>
        </aside>

    </td>
</tr>
</table>


<?

//deb::dump($search_adverts);
//deb::dump($rubrik_groups);

?>
<style>
    .prilip {
        position: fixed;
        z-index: 101;
    }
    .stop {
        position: relative;
    }
</style>

<script>
    // document.documentElement.scrollHeight - высота веб-документа;
    // aside.offsetHeight - высота элемента
    var aside = document.querySelector('aside'),
        t0 = aside.getBoundingClientRect().top - document.documentElement.getBoundingClientRect().top,
        t1 = document.documentElement.scrollHeight - 0 - aside.offsetHeight;

    function asideScroll() {
        if (window.pageYOffset > t1) {
            aside.className = 'stop';
            aside.style.top = t1 - t0 + 'px';
        } else {
            aside.className = (t0 < window.pageYOffset ? 'prilip' : '');
            aside.style.top = '0';
        }
    }
    window.addEventListener('scroll', asideScroll, false);
</script>
