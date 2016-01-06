<?php
/* @var $this FilterController */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.sumoselect.min.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/sumoselect.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/filtercontroller.js');

Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/nouislider/nouislider.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/nouislider/nouislider.tooltips.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/nouislider/nouislider.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/wNumb.js');

?>

<div id="form_search" style="margin-bottom: 15px; ">




<div style="margin: 5px;">

    <!--
    <div id="pips-values"></div>
    <br><br><br><br>
    <div id="pips-values-stepped"></div>
    <br><br><br><br>
    <div id="baraslide"></div>
    <div id="slider-limit-value-min">1</div>
    <div id="slider-limit-value-max">2</div>
    -->

<script>
    /*
    var range_all_sliders = {
        'min': [     0 ],
        '10%': [   500,  500 ],
        '50%': [  4000, 1000 ],
        'max': [ 10000 ]
    };

    var range_baraslide = {
        'min': [ 0 ],
        'max': [ 5 ]
    };

    var pipsValues = document.getElementById('pips-values');

    noUiSlider.create(pipsValues, {
        range: range_all_sliders,
        start: 0,
        pips: {
            mode: 'values',
            values: [50, 552, 2251, 3200, 5000, 7080, 9000],
            density: 4
        }
    });

    var pipsValuesStepped = document.getElementById('pips-values-stepped');
    noUiSlider.create(pipsValuesStepped, {
        range: range_all_sliders,
        start: 0,
        pips: {
            mode: 'values',
            values: [50, 552, 4651, 4952, 5000, 7080, 9000],
            density: 4,
            stepped: true
        }
    });

    var tooltipSlider = document.getElementById('baraslide');

    noUiSlider.create(tooltipSlider, {
        start: [20, 80],
        behaviour: 'drag',
        connect: true,
        tooltips: [ wNumb({ decimals: 1 }), wNumb({ decimals: 1 }) ],
        range: {
            'min': 0,
            'max': 100
        }
    });

    var limitFieldMin = document.getElementById('slider-limit-value-min');
    var limitFieldMax = document.getElementById('slider-limit-value-max');

    tooltipSlider.noUiSlider.on('update', function( values, handle ){
//        console.log(wNumb({ decimals: 1 }));
        //val = Number(values[handle]);
        (handle ? limitFieldMax : limitFieldMin).innerHTML = Number(values[handle]).toFixed(1);
    });
    */

</script>

</div>



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


<div style="text-align: center; padding-left: 0px; border: #000099 solid 0px;">
    <?
    $banner_operator = Yii::app()->params['banners_raspred'][0];
    include(Yii::getPathOfAlias('webroot')."/banners/".$banner_operator."/top_horizont.php");
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

<div style="margin-bottom: 15px; text-align: left;">
<?
if(count($rubrik_groups) > 0)
{
    foreach ($rubrik_groups as $rkey=>$rval)
    {

        ?>
        <a style="margin-right: 0px;" class="baralink_plus" href="<?= Yii::app()->createUrl($rval['path']);?>"><?= $rval['name'];?></a> <span class="notcount" ><?= $rval['cnt'];?></span>
    <?
    }
}
//deb::dump($props_array);
?>
</div>

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
            <td style="padding: 0 10px 0 0; margin: 0; width: 140px; height: 105px; vertical-align: middle; text-align: center; border: #000 solid 0px;">
            <?
            if(count($props_array[$key]['photos']) > 0)
            {
                $transliter = new Supporter();
                $advert_page_url = "/".$val['town_transname']."/".$rubriks_all_array[$val['r_id']]->transname."/".$transliter->TranslitForUrl($val['title'])."_".$val['daynumber_id'];
            ?>
                <div style="position: relative;">
                    <?
                    $photoname = Notice::getPhotoName($props_array[$key]['photos'][0], "_medium");
                    $curr_dir = Notice::getPhotoDir($photoname);
                    ?>
                    <a href="<?= $advert_page_url;?>"><img src="/<?= Yii::app()->params['photodir'];?>/<?= $curr_dir;?>/<?= $photoname;?>"></a>
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
            <td style="vertical-align: top; padding: 0; margin: 0; padding-left: 10px;">
            <?= $props_array[$key]['props_display'];?>
            </td>


        </tr>

        <?
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
        ?>

        <tr>
            <td style="height: 10px;"></td><td></td>
        </tr>
        <?
        }
        ?>
        </table>

        <?
        $page_url = preg_replace('|&page=\d+?|siU', '', Yii::app()->getRequest()->getUrl());
        if($kolpages > 1)
        {
            for($i=1; $i<=$kolpages; $i++)
            {
            ?>
                <a href="<?= $page_url;?>&page=<?= $i;?>"><?= $i;?></a>
            <?
            }
        }
        ?>
    </td>
    <td style="vertical-align: top; height: 1000px;  border: #000020 solid 0px; width: 300px; padding: 0">
        <aside>
            <div style="width: 300px; height: 600px; border: #000020 solid 0px;">
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

<script>
    $('.favorit_button, .favoritstar').click(function(){
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
                    //fbut.html('В избранном');
                    fbut.css('background-image', 'url("/images/favorit_yellow.png")');
                }
                else
                {
                    //fbut.html('В избранное');
                    fbut.css('background-image', 'url("/images/favorit.png")');
                }

            }
        });

    });


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

</script>
