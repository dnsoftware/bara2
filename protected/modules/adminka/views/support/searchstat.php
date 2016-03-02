<?php

$this->renderPartial('/default/_admin_menu');
$this->renderPartial('searchstat_menu');

?>

<div style="width: 300px; float: left; border: #006600 solid 1px; padding: 5px;">
    <b>Высокочастотные запросы</b>
    <table>
<?
    foreach($statrows as $skey=>$sval)
    {
    ?>
    <tr>
        <td><?= $sval['query'];?></td>
        <td><?= $sval['count'];?></td>
    </tr>
    <?
    }
?>
    </table>
</div>


<div style="width: 500px; float: left; border: #006600 solid 1px; padding: 5px; margin-left: 15px;">
    <b>Последние запросы</b>
    <table>
        <?
        foreach($logrows as $key=>$val)
        {
            ?>
            <tr>
                <td><?= date("H:i:s", $val['date_add']);?></td>
                <td><a href="/all/?mainblock[r_id]=&params[q]=<?= urlencode($val['query']);?>" target="_blank"><?= $val['query'];?></a></td>
                <td><?= $val['ip'];?></td>
                <td><?= $val['count'];?></td>
            </tr>
        <?
        }
        ?>
    </table>
</div>