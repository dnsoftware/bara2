<div style="margin: 5px;">
    <?
    $select_multi_placeholders = '';
    foreach($rubriks_props_array as $rkey=>$rval)
    {
        $props = $props_sprav_sorted_array[$rkey];

        if(!isset($props))
        {
            $props = array();
        }

        switch($rval->filter_type)
        {
            case "select_one":
                ?>
                <select class="sumoselect" id="<?= $rval['selector'];?>" name="addfield[<?= $rval['selector'];?>]">
                    <option value="0" disabled selected><?= $rval['name'];?></option>
                    <?
                    foreach ($props as $pkey=>$pval)
                    {
                        $selected = " ";
                        if(isset($_GET['addfield'][$rval['selector']]) && $_GET['addfield'][$rval['selector']] == $pval['ps_id'])
                        {
                            $selected = " selected ";
                        }
                        ?>
                        <option <?= $selected;?> value="<?= $pval['ps_id'];?>"><?= $pval['value'];?></option>
                    <?
                    }
                    ?>
                </select>
                <?
            break;

            case "select_multi":
                ?>
                <select class="sumoselect_multi" id="<?= $rval['selector'];?>" multiple="multiple" <?= $multiple;?> name="addfield[<?= $rval['selector'];?>][]">
                    <?
                    foreach ($props as $pkey=>$pval)
                    {
                        $selected = " ";
                        if(isset($_GET['addfield'][$rval['selector']]) && count($_GET['addfield'][$rval['selector']]) > 0 && in_array($pval['ps_id'], $_GET['addfield'][$rval['selector']]))
                        {
                            $selected = " selected ";
                        }
                    ?>
                        <option <?= $selected;?> value="<?= $pval['ps_id'];?>"><?= $pval['value'];?></option>
                    <?
                    }
                    ?>
                </select>
                <?
                $select_multi_placeholders .= "$('#".$rval['selector']."').SumoSelect({placeholder: '".$rval['name']."'}); ";
            break;


            case "range":
            ?>
                <br>
                <select class="sumoselect" style_id="<?= $rval['selector'];?>_start" name="addfield[<?= $rval['selector'];?>][from]">
                    <option value="0" disabled selected><?= $rval['name'];?>, от</option>
                    <?
                    foreach ($props as $pkey=>$pval)
                    {
                        ?>
                        <option value="<?= $pval['ps_id'];?>"><?= $pval['value'];?></option>
                    <?
                    }
                    ?>
                </select>

                <select class="sumoselect" style_id="<?= $rval['selector'];?>_end" name="addfield[<?= $rval['selector'];?>][to]">
                    <option value="0" disabled selected>до </option>
                    <?
                    foreach ($props as $pkey=>$pval)
                    {
                        ?>
                        <option value="<?= $pval['ps_id'];?>"><?= $pval['value'];?></option>
                    <?
                    }
                    ?>
                </select>
            <?

            break;


            case "checkbox_list":
                //deb::dump($props);
                ?>
                <br>
                <div style=" display:inline-block ; border: #ddd solid 1px; padding: 5px; ">
                <?= $rval['name'];?><br>
                <?
                foreach($props as $hkey=>$hval)
                {
            ?>
                <nobr><input type="checkbox" name="addfield[<?= $rval['selector'];?>][]" value="<?= $hval['ps_id'];?>">
                <?= $hval['value'];?>&nbsp;&nbsp;</nobr>
            <?
                }
                ?>
                </div>
                <?
            break;

            case "is_prop":
            ?>
                <br>
                <div  style=" display:inline-block ; border: #ddd solid 1px; padding: 5px; ">
                <?= $rval['name'];?>, есть/нет<br>
                <input type="checkbox" name="addfield[<?= $rval['selector'];?>]" value="1">
                </div>
            <?
            break;


        }
    }
    //deb::dump($select_multi_placeholders);
    //deb::dump($rubriks_props_array);
    ?>
</div>

<script>

    $('.sumoselect').SumoSelect();
    <?= $select_multi_placeholders;?>

</script>