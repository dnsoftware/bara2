<?php
/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 12.06.15
 * Time: 17:44
 */

?>


<h1 style="clear: both;"><?= $mainblock['title'];?></h1>

<table>
<tr>
    <td style="width: 500px; vertical-align: top;">

    <div id="notice" style="width: 500px;">
        <div class="galleria">
            <?
            deb::dump($uploadfiles_array);
            foreach($uploadfiles_array as $ukey=>$uval)
            {
                ?>
                <img src="/tmp/<?= $uval;?>" data-title="My title" data-description="My description">
            <?
            }
            ?>
        </div>
    </div>

        <div style="margin-top: 20px;">
            <div style="font-weight: bold;">Комментарий продавца</div>
            <?= $mainblock['notice_text'];?>
        </div>

        <div style="margin-top: 10px;">
            <div style="font-weight: bold;">Контакты</div>
            <div style="color: #999;">Телефон:</div>
            <?= $mainblock['client_phone'];?>

            <div style="color: #999;">Продавец:</div>
            <?= $mainblock['client_name'];?>

            <div style="color: #999;">Регион:</div>
            <?= $mainblock_data['country']->name." / ".$mainblock_data['region']->name." / ".$mainblock_data['town']->name;?>
        </div>



    </td>
    <td style="vertical-align: top;">


    <div style="border: #ddd solid 1px; padding: 5px; font-size: 18px; font-weight: bold; display: table-cell;">
        <?= intval($mainblock['cost']*$options['kurs_'.strtolower($mainblock['cost_valuta'])]);?>
        <?= Options::$valutes[$mainblock['cost_valuta']]['symbol'];?>
        <div style="font-weight: normal; font-size: 12px;">
        <?
        foreach(Options::$valutes as $vkey=>$vval)
        {
            if($mainblock['cost_valuta'] == 'RUB')
            {
                if($vkey != $mainblock['cost_valuta'])
                {
                    echo round($mainblock['cost']/$options['kurs_'.strtolower($vkey)], 2)." ".$vval['symbol']." ";
                }
            }
        }
        ?>
        </div>
    </div>

    <div id="properties" style="border: #000 solid 0px; margin-top: 5px;">
        <table>
            <?
            foreach($addfield_data['notice_props'] as $nkey=>$nval)
            {
                ?>
                <tr>
                    <td><?= $addfield_data['rubrik_props_rp_id'][$nkey]->name;?>:</td>
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
    </td>
</tr>
</table>




<script>
    Galleria.loadTheme('/js/galleria/themes/classic/galleria.classic.min.js');
    Galleria.run('.galleria', {
        width: 500,
        height: 400,
        //imageCrop: 'landscape'
        lightbox: true,
        //overlayBackground: '#ffffff'
        showImagenav: true,

    })
</script>

<?
//deb::dump($uploadfiles_array);

//deb::dump(Yii::app()->session['mainblock']);
//deb::dump(Yii::app()->session['addfield']);





















