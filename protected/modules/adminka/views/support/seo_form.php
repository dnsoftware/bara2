

    <div style="background-color: #eee; padding: 5px; ">
        <table style="">
        <tr>
            <td style="width: 800px; vertical-align: top;">

        <div style="margin-bottom: 10px;">
            <form name="panel_form" id="panel_form" onsubmit="return false;">


            <div style="display: inline-block; margin-left: 0px; background-color: #f8c4cb; padding: 5px;" id="span_seokeyword">
                <textarea placeholder="Ключевая фраза" id="textseokeyword" name="keyword[seokeyword]" style="width: 720px; height: 70px;" ></textarea>
                <span id="viewrand" style="cursor: pointer;"> RND </span>
            </div>


            <div style="margin-top: 10px;">Рубрика</div>
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

            <select name="position" id="position">
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

            <span style="margin-left: 150px;">
                <input type="hidden" name="seoparams[page]" value="1">
                <input type="hidden" name="seoparams[kol_on_page]" value="1000">
                <input type="hidden" name="query_type" id="query_type" value="<?= $query_type;?>">

                <input style="width: 150px;" id="filter_keyword" type="button" value="Фильтровать">
            </span>

        <div id="props_data" style="overflow: auto">
            <?
            Yii::app()->controller->actionGetKeywordProps();
            ?>
        </div>
            </td>

            <td style="vertical-align: top; padding-top: 20px;">

                <span style="background-color: #f8c4cb; padding: 10px; "><input style="width: 150px;" id="add_new_keyword" type="button" value="Сохранить новое"></span>

                <div style="margin-top: 70px;">
                    <div style="margin-bottom: 10px;">Словосочетания:</div>

                    <input type="hidden" id="signature" name="signature" value="">
                    <input type="hidden" id="signature_ps_id" name="signature_ps_id" value="">
                    <textarea id="words" name="words" style="width: 200px; height: 120px; font-size: 11px;"></textarea>

                    <br>
                    <input type="button" id="save_board_words" value="Сохранить">
                </div>


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
    GetPanelProps();

    $('#save_board_words').click(function(){
        $.ajax({
            url: "<?= Yii::app()->createUrl('adminka/support/saveboardwords');?>",
            method: "post",
            dataType: 'json',
            data:{
                r_id: $('#panel_r_id').val(),
                signature: $('#signature').val(),
                signature_ps_id: $('#signature_ps_id').val(),
                words: $('#words').val()
            },
            // обработка успешного выполнения запроса
            success: function(data){
                if(data['status'] == 'error')
                {

                }

                if(data['status'] == 'ok')
                {
                    $('#words').attr('class', data['textclass']);
                }
            }
        });

    });

    $('#words').keypress(function(){
        $('#words').attr('class', 'bwred');
    });


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

        data = $.trim($('#textseokeyword').val()).split('\n');
        var error_array = [];

        if(data.length > 1)
        {
            error_array.push('Будет добавлено сразу '+data.length+' ключевых фраз!');
        }

        if($('#panel_r_id').val() == '')
        {
            error_array.push('Не выбрана подрубрика! Ключевики будут добавлены сразу во все подрубрики!');
        }

        if($('#position').val() == '')
        {
            error_array.push('Не указано Расположение! Ключевики будут добавлены сразу во все позиции!');
        }

        if(error_array.length > 0)
        {
            message = error_array.join('\n') + '\n\nПродолжить?';

            if(confirm(message))
            {
                AddNewKeyword();
            }
        }
        else
        {
            AddNewKeyword();
        }



    });

    function AddNewKeyword()
    {
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
    }

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