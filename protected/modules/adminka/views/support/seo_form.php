

    <div style="background-color: #eee; padding: 5px; ">
        <table style="">
        <tr>
            <td style="width: 800px;">
        <div>Рубрика</div>

        <div style="margin-bottom: 10px;">
            <form name="panel_form" id="panel_form" onsubmit="return false;">

            <select class="panel_rubriks" id="panel_r_id" name="keyword[r_id]" style="margin: 0px; width: 250px;">
                <option value="">--- выберите категорию  ---</option>
                <?
                foreach ($rub_array as $rkey=>$rval)
                {
                    $selected = " ";
                    if($rkey == intval($r_id))
                    {
                        $selected = " selected ";
                    }
                    ?>
                    <option <?= $selected;?> disabled style="color:#000; font-weight: bold;" value="<?= $rval['parent']->r_id;?>"><?= $rval['parent']->name;?></option>
                    <?
                    foreach ($rval['childs'] as $ckey=>$cval)
                    {
                        $selected = " ";
                        if($ckey == intval($r_id))
                        {
                            $selected = " selected ";
                        }
                        ?>
                        <option <?= $selected;?> value="<?= $cval->r_id;?>">&nbsp;<?= $cval->name;?></option>
                    <?
                    }
                }
                ?>

            </select>

            <span style="margin-left: 20px;">
            Ключевая фраза:
            <input type="text" name="keyword[seokeyword]" style="width: 300px;">
            </span>

        <div id="props_data" style="overflow: auto">
            <?
            Yii::app()->controller->actionGetKeywordProps();
            ?>
        </div>
            </td>

            <td style="vertical-align: top; padding-top: 20px;">
                <input type="hidden" name="seoparams[page]" value="1">
                <input type="hidden" name="seoparams[kol_on_page]" value="1000">
                <input type="hidden" name="query_type" id="query_type" value="<?= $query_type;?>">



                <input style="width: 150px;" id="add_new_keyword" type="button" value="Сохранить новое"><br>
                <br>
                <input style="width: 150px;" id="filter_keyword" type="button" value="Фильтровать">

            </td>
        </tr>
        </table>

        </form>

        <div id="seo_errors" style="color: #f00; margin: 5px;">

        </div>


</div>



<script>

//    GetPanelProps();

    $('#add_new_keyword').click(function(){
        $.ajax({
            async: false,
            dataType: 'json',
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/support/addnewkeyword');?>',
            data: $('#panel_form').serialize(),
            success: function(msg){
                if(msg['status'] == 'ok')
                {
                    $('#seo_errors').html('');
                    $('#filter_keyword').click();
                }
                else
                {
                    $('#seo_errors').html(msg['errors']);
                }

            }
        });


    });

    $('#filter_keyword').click(function(){

        $('#query_type').val('search');

        $.ajax({
            async: false,
            //dataType: 'json',
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/support/seo');?>',
            data: $('#panel_form').serialize(),
            success: function(msg){
                $('#seo_keywords').html(msg);
            }
        });
    });

    $('#panel_r_id').change(function(){

        GetPanelProps();

    });

    function GetPanelProps()
    {
        $.ajax({
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/support/getkeywordprops');?>',
            data: $('#panel_form').serialize(),
            success: function(msg){
                $('#props_data').html(msg);
            }
        });

    }

</script>