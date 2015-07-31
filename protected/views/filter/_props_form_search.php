<div style="margin: 5px;">
    <?
    $select_multi_placeholders = '';

    if(count($rubriks_props_array) == 0)
    {
        $rubriks_props_array = array();
    }

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
                <select class="sumoselect fchange" id="<?= $rval['selector'];?>" name="addfield[<?= $rval['selector'];?>]">
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
                <select class="sumoselect_multi fchange" id="<?= $rval['selector'];?>" multiple="multiple" <?= $multiple;?> name="addfield[<?= $rval['selector'];?>][]">
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

                //deb::dump($rval);
                if($rval->vibor_type == 'string')
                {
                ?>
                    <br>
                    <input title="<?= $rval['name'];?>, от" placeholder="<?= $rval['name'];?>, от" type="text" name="addfield[<?= $rval['selector'];?>][from]" style="width: 200px; border: 1px solid #A4A4A4; min-height: 14px; background-color: #fff;border-radius:2px;margin:0px; padding: 5px;" value="<?= $_GET['addfield'][$rval['selector']]['from'];?>">

                    <input title="<?= $rval['name'];?>, до" placeholder="до" type="text" name="addfield[<?= $rval['selector'];?>][to]" style="width: 200px; border: 1px solid #A4A4A4; min-height: 14px; background-color: #fff;border-radius:2px;margin:0px; padding: 5px;" value="<?= $_GET['addfield'][$rval['selector']]['to'];?>">
                <?
                }
                else
                {
            ?>
                <br>
                <select class="sumoselect  fchange" style_id="<?= $rval['selector'];?>_start" name="addfield[<?= $rval['selector'];?>][from]">
                    <option value="0" disabled selected><?= $rval['name'];?>, от</option>
                    <?
                    foreach ($props as $pkey=>$pval)
                    {
                        $selected = " ";
                        if(isset($_GET['addfield'][$rval['selector']]['from'])
                            && $_GET['addfield'][$rval['selector']]['from'] == $pval['ps_id'] )
                        {
                            $selected = " selected ";
                        }
                    ?>
                        <option <?= $selected;?> value="<?= $pval['ps_id'];?>"><?= $pval['value'];?></option>
                    <?
                    }
                    ?>
                </select>

                <select class="sumoselect  fchange" style_id="<?= $rval['selector'];?>_end" name="addfield[<?= $rval['selector'];?>][to]">
                    <option value="0" disabled selected>до </option>
                    <?
                    foreach ($props as $pkey=>$pval)
                    {
                        $selected = " ";
                        if(isset($_GET['addfield'][$rval['selector']]['to'])
                            && $_GET['addfield'][$rval['selector']]['to'] == $pval['ps_id'] )
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
                }

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
                    $checked = " ";
                    if(isset($_GET['addfield'][$rval['selector']]) && count($_GET['addfield'][$rval['selector']]) > 0 && in_array($hval['ps_id'], $_GET['addfield'][$rval['selector']]))
                    {
                        $checked = " checked ";
                    }

            ?>
                <nobr><input <?= $checked;?> class="fchange" type="checkbox" name="addfield[<?= $rval['selector'];?>][]" value="<?= $hval['ps_id'];?>">
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
                <?= $rval['name'];?>, только, если есть<br>
                <?
                $checked = " ";
                if(isset($_GET['addfield'][$rval['selector']]) && $_GET['addfield'][$rval['selector']] == 1)
                {
                    $checked = " checked ";
                }
                ?>
                <input <?= $checked;?> class="fchange" type="checkbox" name="addfield[<?= $rval['selector'];?>]" value="1">
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

    $('.fchange').change(function ()
    {
        changeFilterReload('<?= Yii::app()->createUrl('filter/getdatafilter');?>');
    });


</script>