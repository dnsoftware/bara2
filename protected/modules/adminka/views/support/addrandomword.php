<?php


if(count($errors) > 0)
{
    ?>
    <div style="color: #f00;">
    <?
    foreach($errors as $ekey=>$eval)
    {
        foreach($eval as $e2key=>$e2val)
        {
            echo $e2val."<br>";
        }
    }
    ?>
    </div>
    <?
}
?>

<div style="margin-top: 20px; margin-left: 20px; margin-right: 30px;">
<table style="border-collapse: collapse;">
<?
foreach($wordrows as $wkey=>$wval)
{
?>
<tr id="trrand_<?= $wval->sr_id;?>">
    <td class="td_word" id="tdrand_<?= $wval->sr_id;?>">
        <span class="col_key"><?= $wval->key;?></span>
        <span class="col_words"><?= $wval->words;?></span>
    </td>
    <td class="td_word">
        <span class="rand_edit" sr_id="<?= $wval->sr_id;?>">Редактировать</span>
        <br>
        <span class="rand_del" sr_id="<?= $wval->sr_id;?>">Удалить</span>
    </td>
</tr>
<?
}
?>
</table>
</div>

<script>
    $('.rand_edit').click(function(){

        sr_id = $(this).attr('sr_id');
        console.log(sr_id);

        $.ajax({
            async: false,
            //dataType: 'json',
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/support/randomword_edit');?>',
            data: 'sr_id='+sr_id,
            success: function(msg){
                $('#tdrand_'+sr_id).html(msg);
            }
        });

    });

    $('.rand_del').click(function(){

        sr_id = $(this).attr('sr_id');

        $.ajax({
            async: false,
            //dataType: 'json',
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/support/randomword_del');?>',
            data: 'sr_id='+sr_id,
            success: function(msg){
                if(msg == 'ok')
                {
                    $('#trrand_'+sr_id).remove();
                }
            }
        });

    });

</script>