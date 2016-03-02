<?php
$this->renderPartial('/default/_admin_menu');
$this->renderPartial('sphinx_menu');

?>

<div style="margin-left: 20px;">
    <h2 style="font-size: 13px; margin-top: 10px; font-weight: bold;">Переиндексация объявлений</h2>

    <div>
        <form>
            Стартовая дата: <input type="text" name="min_id" id="min_id" value="<?= date(DateTime::W3C);?>" style="width: 200px;">
            <br>
            <div style="margin: 10px;">
            Проиндексировано: <input type="text" readonly id="index_count" style="border: none; font-size: 16px; color: #006600; font-weight: bold; " value="0"><br>
            </div>

            <input type="button" value="Индексировать" id="index_button" >
            <img id="index_loader" style="display: none;" src="/images/ajaxload.gif" width="40">
        </form>
    </div>


</div>


<script>
    $('#index_button').click(function(){

        $('#index_button').css('display', 'none');
        $('#index_loader').css('display', 'inline');

        $.ajax({
            url: "<?= Yii::app()->createUrl('/adminka/support/sphinx_reindex_run');?>",
            method: "post",
            dataType: 'json',
            data:{
                start_id: $('#min_id').val()
            },
            // обработка успешного выполнения запроса
            success: function(data){
                if(data['status'] == 'process')
                {
                    $('#min_id').val(data['from_id']);
                    $('#index_button').click();
                    $('#index_count').val(parseInt($('#index_count').val()) + parseInt(data['add_count']));
                }

                if(data['status'] == 'end')
                {
                    $('#index_button').css('display', 'inline');
                    $('#index_loader').css('display', 'none');
                    alert('Индексация завершена');
                }

            }
        });

    });
</script>
