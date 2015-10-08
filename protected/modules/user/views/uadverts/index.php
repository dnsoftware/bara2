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

<h1 style="font-size: 18px; margin-top: 15px; margin-left: 10px;">Объявления пользователя ID <?= $u_id;?></h1>

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
            <td><a href="/user/uadverts/<?= $u_id;?>/<?= $pval->transname;?>/"><?= $pval->name;?></a></td> <td><?= $parent_ids_count[$pval->r_id];?></td>
    </tr>
    <?
    foreach($subrub_array as $skey=>$sval)
    {
    ?>
    <tr>
        <td style="padding-left: 20px;"><a href="/user/uadverts/<?= $u_id;?>/<?= $pval->transname;?>/<?= $sval->transname;?>/"><?= $sval->name;?></a></td> <td><?= $rub_counter[$sval->r_id];?></td>
    </tr>
    <?
    }

    }
    ?>
    </table>
    </td>

    <td style="800px; vertical-align: top">

    <table style=" border-right: #ddd solid 1px;  border-bottom: #ddd solid 1px; ">
    <?
    foreach($useradverts as $ukey=>$uval)
    {
    ?>
    <tr>
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
    </tr>
    <?
    }
    ?>
    </table>

    </td>
</tr>
</table>