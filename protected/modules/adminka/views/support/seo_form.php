

    <div style="background-color: #eee; padding: 5px; ">
        <table style="">
        <tr>
            <td style="width: 800px;">

        <div style="margin-bottom: 10px;">
            <div>Рубрика</div>
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

            <select name="position">
                <option value="">Расположение</option>
            <?
            foreach(SeoKeywords::$position as $pkey=>$pval)
            {
            ?>
                <option value="<?= $pkey;?>"><?= $pval;?></option>
            <?
            }
            ?>
            </select>

            <div style="display: inline-block; margin-left: 20px; background-color: #f8c4cb; padding: 5px;" id="span_seokeyword">
            <textarea placeholder="Ключевая фраза" id="textseokeyword" name="keyword[seokeyword]" style="width: 300px; height: 20px;" ></textarea>
                <span id="viewrand" style="cursor: pointer;"> RND </span>
            </div>

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



                <span style="background-color: #f8c4cb; padding: 10px;"><input style="width: 150px;" id="add_new_keyword" type="button" value="Сохранить новое"></span><br>
                <br>
                <input style="width: 150px;" id="filter_keyword" type="button" value="Фильтровать">

            </td>
        </tr>
        </table>

        </form>

        <div id="seo_errors" style="color: #f00; margin: 5px;">

        </div>


</div>


<div id="randword_list" style="position: absolute; background-color: #F8C4CB; width: 450px; padding: 3px; display: none;">
<table style="border-collapse: collapse; width: 100%;">
<?
foreach($randomwords as $rkey=>$rval)
{
?>
<tr>
    <td class="td_keyword" style="width: 80px; cursor: pointer;"><b><span class="keycheck"><?= $rval->key;?></span></b></td>
    <td class="td_keyword"><?= $rval->words;?></td>
</tr>
<?
}
?>
</table>
</div>


<script>
    $('#randword_list').offset({left: $('#span_seokeyword').position().left});
    $('#randword_list').offset({top: $('#span_seokeyword').position().top+40});
//    console.log($('#span_seokeyword').position().left);

    $('#viewrand').click(function (){
        if($('#randword_list').css('display') == 'none')
        {
            $('#randword_list').css('display', 'block');
        }
        else
        {
            $('#randword_list').css('display', 'none');
        }

    });

    $('.keycheck').click(function(){
        $('#textseokeyword').focus();
        $('#textseokeyword').insertAtCaret('('+$(this).html()+')');
    });

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

    jQuery.fn.extend({
        insertAtCaret: function(myValue){
            return this.each(function(i) {
                if (document.selection) {
                    // Для браузеров типа Internet Explorer
                    this.focus();
                    var sel = document.selection.createRange();
                    sel.text = myValue;
                    this.focus();
                }
                else if (this.selectionStart || this.selectionStart == '0') {
                    // Для браузеров типа Firefox и других Webkit-ов
                    var startPos = this.selectionStart;
                    var endPos = this.selectionEnd;
                    var scrollTop = this.scrollTop;
                    this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
                    this.focus();
                    this.selectionStart = startPos + myValue.length;
                    this.selectionEnd = startPos + myValue.length;
                    this.scrollTop = scrollTop;
                } else {
                    this.value += myValue;
                    this.focus();
                }
            })
        }
    });

</script>