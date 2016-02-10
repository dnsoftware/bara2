<ul>
<?php
foreach($rubriks as $rkey=>$rval)
{
?>
    <li style="display: inline-block; width: 20%; vertical-align: top; margin: 10px;">
    <a style="font-weight: bold;" href="/all/<?= $rval['parent']->transname;?>"><?= $rval['parent']->name;?></a><br>

        <div>
        <?
        foreach($rval['childs'] as $r2key=>$r2val)
        {
        ?>
            <a href="/all/<?= $r2val->transname;?>"><?= $r2val->name;?></a><br>
            <div>
            <?
            if(isset($first_level_rub[$r2val->r_id]))
            {
                $total = count($first_level_props[$first_level_rub[$r2val->r_id]->rp_id]);
                $counter = 0;
                foreach($first_level_props[$first_level_rub[$r2val->r_id]->rp_id] as $fkey=>$fval)
                {
                    $counter++;
                    if(!in_array($fval->ps_id, PropsSprav::$sitemap_ps_id_first_for_second))
                    {
                    ?>
                        <a class="smap" href="/all/<?= $r2val->transname;?>/<?= $fval->transname;?>"><?= $fval->value;?></a><?
                        if($counter != $total)
                        {
                            echo ", ";
                        }
                    }
                    else
                    {
                    ?>
                    <br><a class="smap" style="font-weight: bold;" href="/all/<?= $r2val->transname;?>/<?= $fval->transname;?>"><?= $fval->value;?>:</a>

                    <?
                    $total2 = count($second_level[$fval->ps_id]);
                    $counter2 = 0;
                    foreach($second_level[$fval->ps_id] as $skey=>$sval)
                    {
                        $counter2++;
                    ?><a class="smap" href="/all/<?= $r2val->transname;?>/<?= $fval->transname;?>/<?= $sval['transname'];?>"><?= $sval['value'];?></a><?
                        if($counter2 != $total2)
                        {
                            echo ", ";
                        }
                    }
                    ?>

                    <br>
                    <?
                    }

                }
            }
            ?>
            </div>
        <?
        }
        ?>
        </div>

    </li>


<?
}
?>
</ul>