<?

/*
public $n_id;
public $class;
public $type;
public $message;
public $verifyCode;
*/


?>

<div id="div_usermessage_form">
<div style="">
<form id="usermessage_form" onsubmit="return false;">

    <br>
    <input type="text" name="username" placeholder="Ваше имя" style="width: 350px;"><br>
    <input type="text" name="useremail" placeholder="Ваш E-mail" style="width: 350px;"><br>
    <div>Сообщение:</div>
    <textarea name="message" style="width: 350px; height: 190px;"></textarea>

    <table style="margin: 0;">
    <tr>
        <td>
        <nobr>Код: <input type="text" name="verifycode" value="" style="width: 120px;"></nobr>
        </td>
        <td>
        <img id="usermessage_captcha_image" src="<?= Yii::app()->createUrl('/advert/showusermessagecaptcha', array('rnd'=>rand(11111, 99999)));?>" style="height: 50px; width: 150px;">
        </td>
        <td>
            <img id="reload_usermessage_captcha" src="/images/icons/reload.gif" style="cursor: pointer;">
        </td>
    </tr>
    </table>

</form>
</div>
</div>

<div id="usermessage_error" style="color: #f00;">

</div>

    <input id="send_usermessage_button" type="button" value="Отправить">


<script>
    $('#reload_usermessage_captcha').click(function(){
        rnd = Math.random() * (999999 - 11111) + 11111;
        $('#usermessage_captcha_image').attr('src', '/advert/showusermessagecaptcha/rnd/'+rnd);
    });

    $('#send_usermessage_button').click(function(){

        $.ajax({
            type: 'POST',
            url: '/advert/sendusermessage',
            dataType: 'json',
            data: $('#usermessage_form').serialize(),
            success: function(msg){

                if(msg['status'] == 'error')
                {
                    //$('#reload_usermessage_captcha').click();
                    $('#usermessage_error').html(msg['message']);
                }

                if(msg['status'] == 'ok')
                {
                    $('#usermessage_error').html('');
                    $('#send_usermessage_button').css('display', 'none');
                    $('#div_usermessage_form').html(msg['message']);
                }
                //console.log(msg);
                //$('#search_data').html(msg);

            }
        });

    });

</script>

<?
//deb::dump(Yii::app()->session['usermessagecaptcha']);
?>