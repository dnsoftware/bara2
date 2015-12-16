<div style="padding: 0px; background-color: #ededed; padding: 5px;">
    <?
    $select_multi_placeholders = '';

    if(count($rubriks_props_array) == 0)
    {
        $rubriks_props_array = array();
    }

    ?>
    <div id="search_mainblock" style="background-color: #ededed; ">
    <?
    $view_block_id = 'main';
    foreach($rubriks_props_array as $rkey=>$rval)
    {
        if($rval->view_block_id != $view_block_id && $view_block_id == 'main')
        {
            $view_block_id = $rval->view_block_id;
            ?>
            </div>
            <?
            $extended_search_tag = 0;
            $display_extended = 'none';
            if(isset($_GET['extended_search_tag']) && intval($_GET['extended_search_tag']) == 1)
            {
                $extended_search_tag = 1;
                $display_extended = 'block';
            }
            ?>
            <div style="text-align: right; background-color: #ddd; padding: 5px; margin-top: 5px; ">
                <input type="hidden" name="extended_search_tag" id="extended_search_tag" value="<?= $extended_search_tag;?>" >

                <input type="button" id="extended_button" value="Расширенный поиск">
            </div>

            <div id="search_extend" style="display: <?= $display_extended;?>; margin-top: 10px;">

            <?
        }

        $props = $props_sprav_sorted_array[$rkey];

        if(!isset($props))
        {
            $props = array();
        }
        if(count($props) == 0 && $rval->hide_if_no_elems_tag)
        {
            continue;
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

                    <div style="display: inline-block; margin-top: -10px;">
                    <nobr>
                    <input title="<?= $rval['name'];?>, от" placeholder="<?= $rval['name'];?>, от" type="text" name="addfield[<?= $rval['selector'];?>][from]" style="width: 207px; border: 1px solid #A4A4A4; min-height: 14px; background-color: #fff;border-radius:2px;margin:0px; padding: 2px; padding-left: 7px;" value="<?= $_GET['addfield'][$rval['selector']]['from'];?>">

                    <input title="<?= $rval['name'];?>, до" placeholder="до" type="text" name="addfield[<?= $rval['selector'];?>][to]" style="width: 207px; border: 1px solid #A4A4A4; min-height: 14px; background-color: #fff;border-radius:2px;margin:0px; padding: 2px; padding-left: 7px;" value="<?= $_GET['addfield'][$rval['selector']]['to'];?>">
                    </nobr>
                    </div>
                <?
                }
                else
                {
                    $sumoclass = 'sumoselect';
                    if($rval->options->filter_view_type == 'polzun')
                    {
                        $sumoclass = 'forpolzun';



                        $data_range = array();
                        $data_range_val = array();
                        if(count($props) > 1)
                        {
                            $i=0;
                            foreach($props as $pkey=>$pval)
                            {
                                $data_range[$i] = $pval['ps_id'];
                                $data_range_val[$i] = $pval['value'];
                                $i++;
                            }


                            $from = 0;
                            if(isset($_GET['addfield'][$rval['selector']]['from']))
                            {
                                //$from = $props[$_GET['addfield'][$rval['selector']]['from']]['value'];
                                $from = intval($_GET['addfield'][$rval['selector']]['from']);
                            }

                            $to = 0;
                            if(isset($_GET['addfield'][$rval['selector']]['to']))
                            {
                                //$to = $props[$_GET['addfield'][$rval['selector']]['to']]['value'];
                                $to = intval($_GET['addfield'][$rval['selector']]['to']);
                            }

                            $start_min = array_search($from, $data_range);
                            if($from == 0 || $start_min === false)
                            {
                                $start_min = 0;
                            }

                            $start_max = array_search($to, $data_range);
                            if($to == 0 || $start_max === false)
                            {
                                $start_max = count($data_range)-1;
                            }

                            //deb::dump($start_min);
                            //deb::dump($start_max);
                            ?>

                            <div style="display: inline-block;">

                                <div class="range_selector" id="select_range_<?= $rval['selector'];?>" rubprops_selector="<?= $rval['selector'];?>">
                                    <span><?= $rval['name'];?></span><i></i>
                                </div>

                                <div class="div_polzun" id="polzun_<?= $rval['selector'];?>" style="display: none;">
                                    <div id="slider_<?= $rval['selector'];?>" style="width: 200px;"></div>
                                </div>



                                <script>
                                    $('#select_range_<?= $rval['selector'];?>').click(function(){});

                                    var slider_<?= $rval['selector'];?> = document.getElementById('slider_<?= $rval['selector'];?>');
                                    var data_range_<?= $rval['selector'];?> = [<?= implode(",", $data_range);?>];
                                    var data_range_val_<?= $rval['selector'];?> = [<?= implode(",", $data_range_val);?>];
                                    items_count_<?= $rval['selector'];?> = data_range_<?= $rval['selector'];?>.length;
                                    //console.log(items_count);

                                    noUiSlider.create(slider_<?= $rval['selector'];?>, {
                                        start: [<?= $start_min;?>, <?= $start_max;?>],
                                        behaviour: 'drag',
                                        connect: true,

                                        tooltips: [ wNumb({ decimals: 0 }), wNumb({ decimals: 0 }) ],
                                        step: 1,
                                        margin:1,
                                        range: {
                                            'min': 0,
                                            'max': <?= count($data_range)-1;?>
                                        }
                                    });

                                    var slider_<?= $rval['selector'];?>_min = document.getElementById('<?= $rval['selector'];?>_start');
                                    var slider_<?= $rval['selector'];?>_max = document.getElementById('<?= $rval['selector'];?>_end');

                                    index_min_<?= $rval['selector'];?> = <?= $start_min;?>;
                                    index_max_<?= $rval['selector'];?> = <?= $start_max;?>;

                                    slider_<?= $rval['selector'];?>.noUiSlider.on('update', function( values, handle ){
                                        if(handle == 0)
                                        {
                                            index_min_<?= $rval['selector'];?> = Number(values[handle]).toFixed(0);
                                            $('#<?= $rval['selector'];?>_start').val(data_range_<?= $rval['selector'];?>[index_min_<?= $rval['selector'];?>]);
                                            $('#slider_<?= $rval['selector'];?> .noUi-handle-lower .noUi-tooltip').html(data_range_val_<?= $rval['selector'];?>[index_min_<?= $rval['selector'];?>]);

                                        }
                                        else
                                        {
                                            index_max_<?= $rval['selector'];?> = Number(values[handle]).toFixed(0);
                                            $('#<?= $rval['selector'];?>_end').val(data_range_<?= $rval['selector'];?>[index_max_<?= $rval['selector'];?>]);
                                            $('#slider_<?= $rval['selector'];?> .noUi-handle-upper .noUi-tooltip').html(data_range_val_<?= $rval['selector'];?>[index_max_<?= $rval['selector'];?>]);

                                        }

                                        shablon_<?= $rval['selector'];?> = '<?= $rval['name'];?>';
                                        if(index_min_<?= $rval['selector'];?> > 0)
                                        {
                                            shablon_<?= $rval['selector'];?> = '<?= $rval->options->shablon_left;?>';
                                        }

                                        if(index_max_<?= $rval['selector'];?> < (items_count_<?= $rval['selector'];?>-1))
                                        {
                                            shablon_<?= $rval['selector'];?> = '<?= $rval->options->shablon_right;?>';
                                        }

                                        if(index_min_<?= $rval['selector'];?> > 0 && index_max_<?= $rval['selector'];?> < (items_count_<?= $rval['selector'];?>-1))
                                        {
                                            shablon_<?= $rval['selector'];?> = '<?= $rval->options->shablon_both;?>';
                                        }

                                        shablon_<?= $rval['selector'];?> = shablon_<?= $rval['selector'];?>.replace('[value_min]', data_range_val_<?= $rval['selector'];?>[index_min_<?= $rval['selector'];?>]);
                                        shablon_<?= $rval['selector'];?> = shablon_<?= $rval['selector'];?>.replace('[value_max]', data_range_val_<?= $rval['selector'];?>[index_max_<?= $rval['selector'];?>]);

                                        $('#select_range_<?= $rval['selector'];?> span').html(shablon_<?= $rval['selector'];?>);

                                    });

                                </script>
                                <?

                                ?>

                            </div>
                        <?
                        }
                    }

            ?>
                <nobr>
                <select class="<?= $sumoclass;?>  fchange" id="<?= $rval['selector'];?>_start" style_id="<?= $rval['selector'];?>_start" name="addfield[<?= $rval['selector'];?>][from]">
                    <option value="" disabled selected><?= $rval['name'];?>, от</option>
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

                <select class="<?= $sumoclass;?>  fchange" id="<?= $rval['selector'];?>_end" style_id="<?= $rval['selector'];?>_end" name="addfield[<?= $rval['selector'];?>][to]">
                    <option value="" disabled selected>до </option>
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
                </nobr>
            <?
                }

            break;

/*
            case "range_polzun":

                deb::dump($rval);
                if($rval->vibor_type == 'string')
                {
                    ?>
                    <nobr>
                        <input title="<?= $rval['name'];?>, от" placeholder="<?= $rval['name'];?>, от" type="text" name="addfield[<?= $rval['selector'];?>][from]" style="width: 207px; border: 1px solid #A4A4A4; min-height: 14px; background-color: #fff;border-radius:2px;margin:0px; padding: 2px; padding-left: 7px;" value="<?= $_GET['addfield'][$rval['selector']]['from'];?>">

                        <input title="<?= $rval['name'];?>, до" placeholder="до" type="text" name="addfield[<?= $rval['selector'];?>][to]" style="width: 207px; border: 1px solid #A4A4A4; min-height: 14px; background-color: #fff;border-radius:2px;margin:0px; padding: 2px; padding-left: 7px;" value="<?= $_GET['addfield'][$rval['selector']]['to'];?>">
                    </nobr>
                <?
                }
                else
                {
                    deb::dump($props);
                    $data_range = array();
                    $data_range_val = array();
                    if(count($props) > 1)
                    {
                        $i=0;
                        foreach($props as $pkey=>$pval)
                        {
                            $data_range[$i] = $pval['ps_id'];
                            $data_range_val[$i] = $pval['value'];
                            $i++;
                        }


                        $from = 0;
                        if(isset($_GET['addfield'][$rval['selector']]['from']))
                        {
                            //$from = $props[$_GET['addfield'][$rval['selector']]['from']]['value'];
                            $from = intval($_GET['addfield'][$rval['selector']]['from']);
                        }

                        $to = 0;
                        if(isset($_GET['addfield'][$rval['selector']]['to']))
                        {
                            //$to = $props[$_GET['addfield'][$rval['selector']]['to']]['value'];
                            $to = intval($_GET['addfield'][$rval['selector']]['to']);
                        }

                        $start_min = array_search($from, $data_range);
                        if($from == 0)
                        {
                            $start_min = 0;
                        }

                        $start_max = array_search($to, $data_range);
                        if($to == 0)
                        {
                            $start_max = count($data_range)-1;
                        }

deb::dump($from);
deb::dump($to);
                        ?>

                        <div style="margin-bottom: 10px;">

                            <div class="range_selector" id="select_range_<?= $rval['selector'];?>" >
                                <span><?= $rval['name'];?></span><i></i>
                            </div>

                            <input type="text" id="slider_<?= $rval['selector'];?>_min" value="<?= $from;?>" name="addfield[<?= $rval['selector'];?>][from]">
                            <input type="text" id="slider_<?= $rval['selector'];?>_max" value="<?= $to;?>" name="addfield[<?= $rval['selector'];?>][to]">

                            <div id="slider_<?= $rval['selector'];?>"></div>
                            <script>
                                $('#select_range_<?= $rval['selector'];?>').click(function(){});

                                var slider_<?= $rval['selector'];?> = document.getElementById('slider_<?= $rval['selector'];?>');
                                var data_range_<?= $rval['selector'];?> = [<?= implode(",", $data_range);?>];
                                var data_range_val_<?= $rval['selector'];?> = [<?= implode(",", $data_range_val);?>];
                                items_count_<?= $rval['selector'];?> = data_range_<?= $rval['selector'];?>.length;
                                //console.log(items_count);

                                noUiSlider.create(slider_<?= $rval['selector'];?>, {
                                    start: [<?= $start_min;?>, <?= $start_max;?>],
                                    behaviour: 'drag',
                                    connect: true,

                                    tooltips: [ wNumb({ decimals: 0 }), wNumb({ decimals: 0 }) ],
                                    step: 1,
                                    range: {
                                        'min': 0,
                                        'max': <?= count($data_range)-1;?>
                                    }
                                });

                                var slider_<?= $rval['selector'];?>_min = document.getElementById('slider_<?= $rval['selector'];?>_min');
                                var slider_<?= $rval['selector'];?>_max = document.getElementById('slider_<?= $rval['selector'];?>_max');

                                slider_<?= $rval['selector'];?>.noUiSlider.on('update', function( values, handle ){
    //                                    console.log(handle);
                                    if(handle == 0)
                                    {
                                        index = Number(values[handle]).toFixed(0);
                                        slider_<?= $rval['selector'];?>_min.value = data_range_<?= $rval['selector'];?>[index];
                                        $('#slider_<?= $rval['selector'];?> .noUi-handle-lower .noUi-tooltip').html(data_range_val_<?= $rval['selector'];?>[index]);
                                    }
                                    else
                                    {
                                        index = Number(values[handle]).toFixed(0);
                                        slider_<?= $rval['selector'];?>_max.value = data_range_<?= $rval['selector'];?>[index];
                                        $('#slider_<?= $rval['selector'];?> .noUi-handle-upper .noUi-tooltip').html(data_range_val_<?= $rval['selector'];?>[index]);
                                    }
                                });

                            </script>
                            <?

                            ?>

                        </div>

                    <?
                    }

                    if(0)
                    {
                    ?>
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
                    ?>
                <?
                }

            break;
*/

            case "checkbox_list":
                //deb::dump($props);
                ?>
                <br>
                <div style=" display:inline-block ; border: #ddd solid 0px; padding: 5px; ">
                <b><?= $rval['name'];?></b><br>
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
                <div  style=" display:inline-block ; border: #ddd solid 0px; padding: 2px; padding-left: 5px;">
                <?
                $checked = " ";
                if(isset($_GET['addfield'][$rval['selector']]) && $_GET['addfield'][$rval['selector']] == 1)
                {
                    $checked = " checked ";
                }
                ?>
                <input <?= $checked;?> class="fchange" type="checkbox" name="addfield[<?= $rval['selector'];?>]" value="1">
                <?= $rval['name'];?>, только, если есть<br>
                </div>
            <?
            break;


        }

    }
    //deb::dump($select_multi_placeholders);
    //deb::dump($rubriks_props_array);
    ?>
    </div>
</div>

<script>
    $('.sumoselect').SumoSelect();
    <?= $select_multi_placeholders;?>

    $('.fchange').unbind('change');
    $('.fchange').change(function ()
    {
        changeFilterReload('<?= Yii::app()->createUrl('filter/getdatafilter');?>');
    });


    $('#extended_button').click(function(){
        if($('#extended_search_tag').val() == 1)
        {
            $('#extended_search_tag').val(0);
            $('#search_extend').css('display', 'none');
        }
        else
        {
            $('#extended_search_tag').val(1);
            $('#search_extend').css('display', 'block');
        }

    });

    $('.range_selector').click(
        function(){
            selector = $(this).attr('rubprops_selector');
            if($('#polzun_'+selector).css('display') == 'none')
            {
                $('#polzun_'+selector).css('display', 'block');
            }
            else
            {
                $('#polzun_'+selector).css('display', 'none');
            }

        }
    );


    $(function(){
        // Закрытие ползуна по клику за его пределами
        $(document).click(function(event) {

            if($(event.target).closest(".range_selector").length)
            {
                return;
            }
            if($(event.target).closest(".div_polzun").length)
            {
                console.log($(event.target).closest(".div_polzun").attr('id'));
                return;
            }

            $(".div_polzun").css('display', 'none');

            event.stopPropagation();
        });

    });


</script>