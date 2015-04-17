<?
//deb::dump($props_spav_records);
//deb::dump($props_selected_spav_records);

?>
<style>
.table_relation td
{
    background-color: #ececec;
    cursor: pointer;
}

tr.tr_relate_header td, td.td_relate_header
{
    background-color: #b6c9ec;
}

.table_relation td.yeslink { background-color: #0f0;}

</style>

<div style="float: right;">
    <span style="cursor: pointer;" onclick="work_props_sprav(<?= $current_rp_id;?>);">Справочник</span>&nbsp;
</div>
<br>
<br>

<div style="width: 100%; overflow-x: auto;">
<table class="table_relation" style="border-spacing: 1px;">
<tr class="tr_relate_header">
    <td></td>
    <?
    foreach ($props_spav_records as $colkey=>$colval)
    {
    ?><td><?= $colval->value;?></td><?
    }
    ?>
</tr>
<?
$currrow=0;
foreach ($props_selected_spav_records as $rowkey=>$rowval)
{
    ?>
    <tr>
    <?
    $currcol = 0;
    foreach ($props_spav_records as $colkey=>$colval)
    {
        if ($currcol == 0)
        {
            ?><td class="td_relate_header"><?= $rowval->value;?></td><?
        }
        $active_class = '';
        if (isset($rel_array[$colval->ps_id][$rowval->ps_id]))
        {
            $active_class = ' yeslink';
        }
        ?>
        <td class="relcell <?= $active_class;?>" parent_ps_id="<?= $colval->ps_id?>" child_ps_id="<?= $rowval->ps_id?>">
            <?= $colval->value?> / <?= $rowval->value?>
        </td>
        <?

        $currcol++;
    }
    ?>
    </tr>
    <?
    $currrow++;
}
?>
</table>
</div>

<script>

    $('.relcell').click(function()
    {
//        alert($(this).attr('parent_ps_id'));

        cell = $(this);
        $.ajax({
            type: 'POST',
            url: '/index.php?r=adminka/propssprav/ajax_gettable_relation_setrelate',
            data: 'parent_ps_id='+$(this).attr('parent_ps_id')+'&child_ps_id='+$(this).attr('child_ps_id'),
            success: function(ret) {
                if(ret.indexOf('<!--yes-->') + 1)
                {
                    cell.addClass('yeslink');
                }
                else
                if(ret.indexOf('<!--no-->') + 1)
                {
                    cell.removeClass('yeslink');
                }
                else
                {
                    alert(ret);
                }
            }
        });

    }
    )

</script>