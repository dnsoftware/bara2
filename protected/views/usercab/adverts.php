<?php
/* @var $this UsercabController */


$this->renderPartial('application.views.filter._search_form', array(
    'rub_array'=>$rub_array,
    'mselector'=>$mselector,
    'm_id'=>$m_id,
    'props_sprav_sorted_array'=>$props_sprav_sorted_array,
    'rubriks_props_array'=>$rubriks_props_array,

));
?>

<?/*?>
<div style="text-align: center; padding-left: 0px; margin-top: 10px; height: 120px;">
    <?
    $banner_operator = Yii::app()->params['banners_raspred'][0];
    include(Yii::getPathOfAlias('webroot')."/banners/".$banner_operator."/top_horizont.php");
    ?>
</div>
<?*/?>

<?
//deb::dump($_GET);
?>

<table>
    <tr>
        <td style="width: 250px; vertical-align: top;">
            <h1 style=" text-align: center; font-size: 18px; margin-top: 15px; margin-left: 10px;">Мои объявления</h1>

            <table>
                <?
                if(count($parent_rubriks) > 0)
                {
                    foreach($parent_rubriks as $pkey=>$pval)
                    {
                        ?>
                        <tr>
                            <td><a class="baralink" href="/usercab/adverts/<?= $pval->transname;?>/"><?= $pval->name;?></a></td> <td><?= $parent_ids_count[$pval->r_id];?></td>
                        </tr>
                        <?
                        if(count($subrub_array[$pval->r_id]) > 0)
                        {
                            foreach($subrub_array[$pval->r_id] as $skey=>$sval)
                            {
                                ?>
                                <tr>
                                    <td style="padding-left: 20px;"><a  class="baralink" href="/usercab/adverts/<?= $pval->transname;?>/<?= $sval->transname;?>/"><?= $sval->name;?></a></td> <td><?= $rub_counter[$sval->r_id];?></td>
                                </tr>
                            <?
                            }
                        }

                    }
                }
                ?>
            </table>

            <?
            $this->renderPartial('usercabmenu');
            ?>

            <?/*?>
            <div style="text-align: left; padding-left: 14px; margin-top: 10px; ">
                <?
                $banner_operator = Yii::app()->params['banners_raspred'][1];
                include(Yii::getPathOfAlias('webroot')."/banners/".$banner_operator."/right_300.php");
                ?>
            </div>
            <?*/?>

        </td>

        <td style="800px; vertical-align: top; text-align: left; padding-top: 10px; ">

            <div id="tabs">
                <ul>
                    <li><a href="#tabs-actual">Активные (<?= count($search_adverts['actual']);?>)</a></li>
                    <li><a href="#tabs-expire">Архивные (<?= count($search_adverts['expire']);?>)</a></li>
                </ul>

            <?
            foreach($search_adverts as $index=>$search_adverts_part)
            {
            ?>
            <div id="tabs-<?= $index;?>" style="">
            <p>
            <table style="">
                <?
                foreach($search_adverts_part as $key=>$val)
                {
                    ?>
                    <tr style="" id="tradv_<?= $val['n_id'];?>">
                        <td style="padding: 0 10px 0 0; margin: 0; width: 140px; height: 105px; vertical-align: middle; text-align: center; border: #000 solid 0px;">
                            <div style="position: relative;">
                                <?
                                if(count($props_array[$index][$key]['photos']) > 0)
                                {
                                    $transliter = new Supporter();
                                    $advert_page_url = "/".$val['town_transname']."/".$rubriks_all_array[$val['r_id']]->transname."/".$transliter->TranslitForUrl($val['title'])."_".$val['daynumber_id'];

                                    $photoname = Notice::getPhotoName($props_array[$index][$key]['photos'][0], "_medium");
                                    $curr_dir = Notice::getPhotoDir($photoname);
                                    ?>
                                    <a href="<?= $advert_page_url;?>"><img src="/<?= Yii::app()->params['photodir'];?>/<?= $curr_dir;?>/<?= $photoname;?>"></a>
                                    <?
                                    if(count($props_array[$index][$key]['photos']) > 1)
                                    {
                                        ?>
                                        <div class="colphoto"><div><?= count($props_array[$index][$key]['photos']);?></div></div>
                                    <?
                                    }
                                    ?>

                                <?
                                }
                                ?>
                            </div>
                        </td>
                        <td style="vertical-align: top; padding: 0; margin: 0; padding-left: 10px;">
                            <?= $props_array[$index][$key]['props_display'];?>
                        </td>
                        <td>
                            <div>
                                <a href="<?= Yii::app()->createUrl('usercab/advert_edit', array('n_id'=>$val['n_id']));?>">Редактировать</a><br>

                                <?
                                if($val['date_expire'] > time() && $val['active_tag'] == 1 )
                                {
                                ?>
                                    <a style="color: #4d950e;" href="<?= Yii::app()->createUrl('usercab/advert_deactivate', array('n_id'=>$val['n_id']));?>">В архив</a><br>
                                <?
                                }
                                ?>


                                <span class="imgnot_del" id="imgdel_<?= $val['n_id'];?>" style="cursor: pointer; border-bottom: #003399 solid 1px;color: #008CC3;" onclick="advert_del(<?= $val['n_id'];?>);">Удалить</span><br>

                                <?
                                if($val['date_expire'] < time() || $val['active_tag'] == 0 )
                                {
                                ?>
                                    <a style="color: #f00;" href="<?= Yii::app()->createUrl('usercab/advert_activate', array('n_id'=>$val['n_id']));?>">Активировать</a>
                                <?
                                }
                                else
                                if($val['date_expire'] < (time()+86400*2) && $val['date_expire'] > time())
                                {
                                ?>
                                    <a style="color: #f00;" href="<?= Yii::app()->createUrl('usercab/advert_activate', array('n_id'=>$val['n_id']));?>">Обновить</a>
                                <?
                                }
                                ?>


                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 10px;" colspan="3"></td>
                    </tr>
                <?
                }
                ?>
            </table>
            </p>
            </div>
            <?
            }
            ?>

        </td>
    </tr>
</table>

<div id="advert_del" style="background-color: #eee; border: #aaa solid 1px; width: 250px; height: 100px; position: absolute; top: 50px; text-align: center; display: none;">
    <div>Объявление</div>
    <div style="font-weight: bold;" id="del_advert_name"></div>
    <div>будет удалено</div>

    <br>
    <span id="span_advkill" style="border: #aaa solid 1px; padding: 3px; cursor: pointer;" >&nbsp;Удалить&nbsp;</span>
    <span style="border: #aaa solid 1px; padding: 3px; cursor: pointer; margin-left: 50px;" onclick="$('#advert_del').css('display', 'none');">&nbsp;Отмена&nbsp;</span>

</div>


<script>
    $( "#tabs" ).tabs();

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

    function advert_del(n_id)
    {
        $('#span_advkill').unbind('click');
        $('#span_advkill').click(function(){

            $.ajax({
                type: 'POST',
                url: '<?= Yii::app()->createUrl('usercab/useradvertdel');?>',
                data: 'n_id='+n_id,
                success: function(msg){
                    if(msg == 'del')
                    {
                        $('#advert_del').css('display', 'none');
                        $('#tradv_'+n_id).fadeOut(800);
                    }

                    if(msg == 'baduser')
                    {
                        alert("Удаление невозможно! Неверный пользователь!");
                    }

                }
            });
        });

        $('#advert_del').css('display', 'block');
        $('#del_advert_name').html($('#advtitul_'+n_id).html());
        $('#advert_del').offset({
            left: $('#imgdel_'+n_id).offset().left-230,
            top: $('#imgdel_'+n_id).offset().top+16
        });

    }

</script>


<style>
    .ui-widget-content a
    {
        color: #008CC3;
    }

    .ui-widget-content a:focus, .ui-widget-content a:hover {
        color: #09F;
    }

</style>