<?

/*
public $n_id;
public $class;
public $type;
public $message;
public $verifyCode;
*/


?>

<div id="div_abuse_form">
<div style="">
<form id="abuse_form" onsubmit="return false;">
    <input type="hidden" name="n_id" value="<?= $formabuse['n_id'];?>">
    <input type="hidden" name="class" value="<?= $formabuse['class'];?>">
    <input type="hidden" name="type" value="<?= $formabuse['type'];?>">

    <?
    if($formabuse['class'] == 'abuse_other')
    {
    ?>
        <div>Текст жалобы:</div>
        <textarea name="message" style="width: 350px; height: 100px;"></textarea>
    <?
    }
    else
    {
    ?>
        <div style="font-size: 16px;">Причина жалобы: <?= Notice::$abuse_items[$formabuse['type']]['name'];?></div>
    <?
    }
    ?>

    <table style="margin: 0;">
    <tr>
        <td>
        <nobr>Код: <input type="text" name="verifycode" value="" style="width: 120px;"></nobr>
        </td>
        <td>
        <img id="abuse_captcha_image" src="<?= Yii::app()->createUrl('/advert/showabusecaptcha', array('rnd'=>rand(11111, 99999)));?>" style="height: 50px; width: 150px;">
        </td>
        <td>
            <img id="reload_abuse_captcha" src="/images/icons/reload.gif" style="cursor: pointer;">
        </td>
    </tr>
    </table>

</form>
</div>
</div>

<div id="abuse_error" style="color: #f00;">

</div>

    <input id="send_abuse_button" type="button" value="Отправить">


<script>
    $('#reload_abuse_captcha').click(function(){
        rnd = Math.random() * (999999 - 11111) + 11111;
        $('#abuse_captcha_image').attr('src', '/advert/showabusecaptcha/rnd/'+rnd);
    });

    $('#send_abuse_button').click(function(){

        $.ajax({
            type: 'POST',
            url: '/advert/sendabuse',
            dataType: 'json',
            data: $('#abuse_form').serialize(),
            success: function(msg){

                if(msg['status'] == 'error')
                {
                    //$('#reload_abuse_captcha').click();
                    $('#abuse_error').html(msg['message']);
                }

                if(msg['status'] == 'ok')
                {
                    $('#abuse_error').html('');
                    $('#send_abuse_button').css('display', 'none');
                    $('#div_abuse_form').html(msg['message']);
                }
                //console.log(msg);
                //$('#search_data').html(msg);

            }
        });

    });

</script>

<?
//deb::dump(Yii::app()->session['abusecaptcha']);
?>