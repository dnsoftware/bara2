<?php


?>
<table style="border-collapse: collapse;">
<?
foreach($search_keywords as $key=>$val)
{
?>
<tr id="tr_keyword_<?= $key;?>">
    <td class="td_keyword" id="td_keyword_<?= $key;?>">
        <?= $val['keyword'];?>
    </td>
    <td class="td_keyword" style="width: 100px;">
        <!--<span class="keyword_edit" k_id="<?= $key;?>">Редактировать</span>-->
        <span class="keyword_del" k_id="<?= $key;?>">Удалить</span>
    </td>
</tr>
<?
}
?>
</table>

<script>
    /*
    $('.keyword_edit').click(function(){

        $('#query_type').val('edit');

        $.ajax({
            async: false,
            //dataType: 'json',
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/support/seo');?>',
            data: $('#panel_form').serialize()+'&k_id='+$(this).attr('k_id'),
            success: function(msg){
                $('#seo_form').html(msg);
            }
        });

    });
    */

    $('.keyword_del').click(function(){

        k_id = $(this).attr('k_id');
        $.ajax({
            async: false,
            //dataType: 'json',
            type: 'POST',
            url: '<?= Yii::app()->createUrl('adminka/support/seokeyworddel');?>',
            data: 'k_id='+k_id,
            success: function(msg){
                $('#tr_keyword_'+k_id).remove();
            }
        });

    });

</script>

<?