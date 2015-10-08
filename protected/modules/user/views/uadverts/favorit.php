<?php
$this->renderPartial('application.views.filter._search_form', array(
    'rub_array'=>$rub_array,
    'mselector'=>$mselector,
    'm_id'=>$m_id,
    'props_sprav_sorted_array'=>$props_sprav_sorted_array,
    'rubriks_props_array'=>$rubriks_props_array,

));

?>



<div style="text-align: left; padding-left: 14px; margin-top: 10px; height: 120px;">
<?
include(Yii::getPathOfAlias('webroot')."/banners/yandex/top_horizont.php");
?>
</div>

<h1 style="font-size: 18px; margin-top: 15px; margin-left: 10px;">Избранные объявления</h1>

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
                <td><a href="/user/favorit/<?= $pval->transname;?>/"><?= $pval->name;?></a></td> <td><?= $parent_ids_count[$pval->r_id];?></td>
        </tr>
        <?
            if(count($subrub_array) > 0)
            {
                foreach($subrub_array as $skey=>$sval)
                {
                ?>
                <tr>
                    <td style="padding-left: 20px;"><a href="/user/favorit/<?= $pval->transname;?>/<?= $sval->transname;?>/"><?= $sval->name;?></a></td> <td><?= $rub_counter[$sval->r_id];?></td>
                </tr>
                <?
                }
            }

        }
    }
    ?>
    </table>
    </td>

    <td style="800px; vertical-align: top">

    <table style=" border-right: #ddd solid 1px;  border-bottom: #ddd solid 1px; ">
    <?
    if(count($useradverts) > 0)
    {

        foreach($useradverts as $ukey=>$uval)
        {
        ?>
        <tr id="row_favorit_<?= $uval->n_id;?>">
            <td style="border-left: #ddd solid 1px; border-top: #ddd solid 1px; ">
                <?= Notice::costCalcAndView(
                    $uval->cost_valuta,
                    $uval->cost,
                    Yii::app()->request->cookies['user_valuta_view']->value
                );?>

                <span><?= Options::$valutes[Yii::app()->request->cookies['user_valuta_view']->value]['symbol2'];?></span>
            </td>
            <td style="border-left: #ddd solid 1px;  border-top: #ddd solid 1px; ">
                <?
                if(isset($useradverts_photos[$uval->n_id][0]))
                {
                    $photoname = str_replace(".", "_thumb.", $useradverts_photos[$uval->n_id][0]);
                ?>
                <img src="/photos/<?= $photoname;?>">
                <?
                }
                ?>
            </td>
            <td style="border-left: #ddd solid 1px;  border-top: #ddd solid 1px; ">
                <?
                $advert_page_url = "/".$towns_array[$uval->t_id]->transname."/".$subrub_array[$uval->r_id]->transname."/".$transliter->TranslitForUrl($uval->title)."_".$uval->daynumber_id;

                ?>
                <a href="<?= $advert_page_url;?>"><?= $uval->title;?></a>
            </td>
            <td style="border-left: #ddd solid 1px;  border-top: #ddd solid 1px; ">
                <?= date("d-m-Y", $uval->date_add);?>
            </td>
            <td style="border-left: #ddd solid 1px;  border-top: #ddd solid 1px;">
                <img class="img_delete_favorit" src="/images/actions/delete.gif" del_id="<?= $uval->n_id;?>">
            </td>
        </tr>
        <?
        }
    }
    ?>
    </table>

    </td>
</tr>
</table>

<style>
    .img_delete_favorit
    {
        cursor: pointer;
    }
</style>

<script>
    $('.img_delete_favorit').click(function(){
        fbut = $(this);

        $.ajax({
            url: "<?= Yii::app()->createUrl('/advert/addtofavorit');?>",
            method: "post",
            dataType: 'json',
            data:{
                n_id: fbut.attr('del_id')
            },
            // обработка успешного выполнения запроса
            success: function(data){
                $('#favorit_count').html(data['count']);
                if(data['status'] == 'add')
                {

                }
                else
                {
                    $('#row_favorit_'+fbut.attr('del_id')).css('display', 'none');
                }

            }
        });

    });
    //
</script>