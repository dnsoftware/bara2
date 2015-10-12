<?php
$this->renderPartial('application.views.filter._search_form', array(
    'rub_array'=>$rub_array,
    'mselector'=>$mselector,
    'm_id'=>$m_id,
    'props_sprav_sorted_array'=>$props_sprav_sorted_array,
    'rubriks_props_array'=>$rubriks_props_array,

));
?>

<div style="text-align: center; padding-left: 0px; margin-top: 10px; height: 120px; width: 1050px;" >
<?
$banner_operator = Yii::app()->params['banners_raspred'][0];
include(Yii::getPathOfAlias('webroot')."/banners/".$banner_operator."/top_horizont.php");
?>
</div>

<h1 style=" text-align: center; font-size: 18px; margin-top: 15px; margin-left: 10px;">Объявления пользователя <?= $user->privat_name;?></h1>

<?
//deb::dump($_GET);
?>

<table>
<tr>
    <td style="width: 250px; vertical-align: top;">
    <table>
    <?
    foreach($parent_rubriks as $pkey=>$pval)
    {
    ?>
    <tr>
            <td><a  class="baralink" href="/user/uadverts/<?= $u_id;?>/<?= $pval->transname;?>/"><?= $pval->name;?></a></td> <td><?= $parent_ids_count[$pval->r_id];?></td>
    </tr>
    <?
    foreach($subrub_array as $skey=>$sval)
    {
    ?>
    <tr>
        <td style="padding-left: 20px; "><a  class="baralink" href="/user/uadverts/<?= $u_id;?>/<?= $pval->transname;?>/<?= $sval->transname;?>/"><?= $sval->name;?></a></td> <td><?= $rub_counter[$sval->r_id];?></td>
    </tr>
    <?
    }

    }
    ?>
    </table>

    <div style="text-align: left; padding-left: 14px; margin-top: 10px; ">
        <?
        $banner_operator = Yii::app()->params['banners_raspred'][1];
        include(Yii::getPathOfAlias('webroot')."/banners/".$banner_operator."/right_300.php");
        ?>
    </div>

    </td>

    <td style="800px; vertical-align: top">

    <table style=" border-right: #ddd solid 0px;  border-bottom: #ddd solid 0px; ">
    <?
    foreach($useradverts as $ukey=>$uval)
    {
        $advert_page_url = "/".$towns_array[$uval->t_id]->transname."/".$subrub_array[$uval->r_id]->transname."/".$transliter->TranslitForUrl($uval->title)."_".$uval->daynumber_id;
    ?>
    <tr>
        <td style="border-left: #ddd solid 0px; width: 140px; height: 105px; border-top: #ddd solid 0px;  text-align: center; vertical-align: top; ">
            <?
            if(isset($useradverts_photos[$uval->n_id][0]))
            {
                $photoname = str_replace(".", "_medium.", $useradverts_photos[$uval->n_id][0]);
                ?>
            <a class="baralink" href="<?= $advert_page_url;?>"><img src="/photos/<?= $photoname;?>"></a>
            <?
            }
            ?>
        </td>
        <td style="border-left: #ddd solid 0px;  border-top: #ddd solid 0px; vertical-align: top;">


            <div style="float: left;">
            <?
            $favprefix = "";
            //$favorit_title = 'В избранное';
            $favorit_title = '';
            if(Notice::CheckAdvertInFavorit($uval->n_id))
            {
                //$favorit_title = 'В избранном';
                $favorit_title = '';
                $favprefix = "_yellow";
            }
            ?>
            <a class="span_lnk favoritstar" advert_id="<?= $uval->n_id;?>" style="background-image: url('/images/favorit<?= $favprefix;?>.png'); background-position: left center; background-repeat: no-repeat; padding-left: 17px; margin-left: 0px; text-decoration: none;">
                <span class="favorit_button" advert_id="<?= $uval->n_id;?>" style="border-bottom: #008CC3 dotted; border-width: 1px;"><?= $favorit_title;?></span>
            </a>
            </div>


            <div style="float: left;">
            <div style="margin-bottom: 7px;">
            <a class="baralink" style="font-size: 18px; font-weight: bold;" href="<?= $advert_page_url;?>"><?= $uval->title;?></a>

            </div>

            <div style="margin-bottom: 7px;">
            <span style="font-size: 16px; font-weight: bold;">
            <?= Notice::costCalcAndView(
                $uval->cost_valuta,
                $uval->cost,
                Yii::app()->request->cookies['user_valuta_view']->value
            );?>
            </span>

            <span><?= Options::$valutes[Yii::app()->request->cookies['user_valuta_view']->value]['symbol2'];?></span>
            </div>

            <span style="color: #777;"><?= date("d.m.Y", $uval->date_add);?>
            <?= date("H:i", $uval->date_add);?></span>

            </div>
        </td>
    </tr>
    <?
    }
    ?>
    </table>

    </td>
</tr>
</table>

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
</script>