<?php

?>
<style>
    .td_word
    {
        border: #aaa solid 1px;
    }
    .col_key
    {
        width: 100px; display: table-cell; font-weight: bold;
    }
    .col_words
    {
        width: 500px; display: table-cell;
    }
    .rand_edit, .rand_del
    {
        cursor: pointer;
    }
</style>

<div style="margin-left: 50px; margin-top: 10px;">
    <a href="/adminka/support/seo">SEO</a>&nbsp;&nbsp;&nbsp;
    <a href="/adminka/support/randomword">Рандомайзер</a>
</div>

<div style="margin-bottom: 10px; font-weight: bold; margin-top: 20px;">Рандомизатор</div>

<div id="random_form" style="margin-top: 10px; ">

    <form id="frm_random" onsubmit="return false;">
        <input placeholder="Ключ группы" type="text" name="key">
        <input placeholder="группа слов, разделитель '/' " type="text" name="words" style="width: 500px;">
        <input type="button" value="Добавить" id="add_randomword">
    </form>


</div>

<div id="randoword_list">
<?
$this->renderPartial('addrandomword', array(
    'wordrows'=>$wordrows,
    'errors'=>array()
));

?>
</div>


<script>

    $('#add_randomword').click(function(){

        $.ajax({
            async: false,
            //dataType: 'json',
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/support/addrandomword');?>',
            data: $('#frm_random').serialize(),
            success: function(msg){
                $('#randoword_list').html(msg);
            }
        });

    });

</script>