
<div id="div_share_form">
<div style="margin-top: 10px;">
<form id="share_form" onsubmit="return false;">
    <input type="hidden" name="n_id" value="<?= $n_id;?>">

    <table style="margin: 0;">
        <tr>
            <td style="text-align: left;"><input style=" width: 300px;" type="text" placeholder="Ваше имя" name="your_name"></td>
        </tr>
        <tr>
            <td style="text-align: left;"><input style=" width: 300px;" type="text" placeholder="Ваша электронная почта" name="your_email"></td>
        </tr>
        <tr>
            <td style="text-align: left;"><input style=" width: 300px;" type="text" placeholder="Имя друга" name="friend_name"></td>
        </tr>
        <tr>
            <td style="text-align: left;"><input style=" width: 300px;" type="text" placeholder="Почта друга" name="friend_email"></td>
        </tr>
    </table>

    <table style="margin: 0; width: 260px;">
    <tr>
        <td>
        <nobr><input type="text" placeholder="Проверочный код" name="verifycode" value="" style="width: 120px;"></nobr>
        </td>
        <td>
        <img id="share_captcha_image" src="<?= Yii::app()->createUrl('/advert/showsharecaptcha', array('rnd'=>rand(11111, 99999)));?>" style="height: 50px; width: 150px;">
        </td>
        <td>
            <img id="reload_share_captcha" src="/images/icons/reload.gif" style="cursor: pointer;">
        </td>
    </tr>
    </table>

</form>
</div>
</div>

<div id="share_error" style="color: #f00;">

</div>

    <input id="send_share_button" type="button" value="Отправить">


<script>
    $('#reload_share_captcha').click(function(){
        rnd = Math.random() * (999999 - 11111) + 11111;
        $('#share_captcha_image').attr('src', '/advert/showsharecaptcha/rnd/'+rnd);
    });

    $('#send_share_button').click(function(){

        $.ajax({
            type: 'POST',
            url: '/advert/sendshare',
            dataType: 'json',
            data: $('#share_form').serialize(),
            success: function(msg){

                if(msg['status'] == 'error')
                {
                    //$('#reload_share_captcha').click();
                    $('#share_error').html(msg['message']);
                }

                if(msg['status'] == 'ok')
                {
                    $('#share_error').html('');
                    $('#send_share_button').css('display', 'none');
                    $('#div_share_form').html(msg['message']);
                }
                //console.log(msg);
                //$('#search_data').html(msg);

            }
        });

    });

</script>

<?
//deb::dump(Yii::app()->session['sharecaptcha']);
?>