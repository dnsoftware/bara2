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

<h1 style=" text-align: center; font-size: 18px; margin-top: 15px; margin-left: 10px;">Мои объявления</h1>

<?
//deb::dump($_GET);
?>

<table>
    <tr>
        <td style="width: 250px; vertical-align: top;">
            <table>
                <?
                if(count($parent_rubriks) > 0)
                {
                    foreach($parent_rubriks as $pkey=>$pval)
                    {
                        ?>
                        <tr>
                            <td><a  class="baralink" href="/usercab/adverts/<?= $pval->transname;?>/"><?= $pval->name;?></a></td> <td><?= $parent_ids_count[$pval->r_id];?></td>
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

        <td style="800px; vertical-align: top">

<?

?>
            <table style="">
                <?
                //deb::dump(count($search_adverts));
                foreach($search_adverts as $key=>$val)
                {
                    ?>
                    <tr style="" id="tradv_<?= $val['n_id'];?>">
                        <td style="padding: 0 10px 0 0; margin: 0; width: 140px; height: 105px; vertical-align: middle; text-align: center; border: #000 solid 0px;">
                            <div style="position: relative;">
                                <?
                                if(count($props_array[$key]['photos']) > 0)
                                {
                                    $transliter = new Supporter();
                                    $advert_page_url = "/".$val['town_transname']."/".$rubriks_all_array[$val['r_id']]->transname."/".$transliter->TranslitForUrl($val['title'])."_".$val['daynumber_id'];

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

                                <?
                                }
                                ?>
                            </div>
                        </td>
                        <td style="vertical-align: top; padding: 0; margin: 0; padding-left: 10px;">
                            <?= $props_array[$key]['props_display'];?>
                        </td>
                        <td>
                            <div>
                                <a href="<?= Yii::app()->createUrl('usercab/advert_edit', array('n_id'=>$val['n_id']));?>">Редактировать</a>

                                <span class="imgnot_del" id="imgdel_<?= $val['n_id'];?>" style="cursor: pointer; border-bottom: #003399 solid 1px;color: #06C;" onclick="advert_del(<?= $val['n_id'];?>);">Удалить</span>
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


