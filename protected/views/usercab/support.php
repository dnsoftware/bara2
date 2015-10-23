<h1 style="font-size: 18px; margin-top: 20px;text-align: center;">Связь с техподдержкой</h1>

<table>
<tr>
    <td style="vertical-align: top;">
        <?
        $this->renderPartial('usercabmenu');
        ?>
    </td>

    <td style="vertical-align: top;">

    <div id="div_support_form">
        <div style="">
            <form id="support_form" onsubmit="return false;">
                <div>Тема обращения:</div>
                <input type="text" name="subject" style="width: 400px;">

                <div>Текст обращения:</div>
                <textarea name="message" style="width: 400px; height: 120px;"></textarea>

                <table style="margin: 0; width: 400px;">
                    <tr>
                        <td>
                            <nobr>Код: <input type="text" name="verifycode" value="" style="width: 120px;"></nobr>
                        </td>
                        <td>
                            <img id="support_captcha_image" src="<?= Yii::app()->createUrl('/usercab/showsupportcaptcha', array('rnd'=>rand(11111, 99999)));?>" style="height: 50px; width: 150px;">
                        </td>
                        <td>
                            <img id="reload_support_captcha" src="/images/icons/reload.gif" style="cursor: pointer;">
                        </td>
                    </tr>
                </table>

            </form>
        </div>
    </div>

    <div id="support_error" style="color: #f00;">

    </div>

    <br>

    <input id="send_support_button" type="button" value="Отправить">
    </td>

</tr>

</table>


    <script>
        $('#reload_support_captcha').click(function(){
            rnd = Math.random() * (999999 - 11111) + 11111;
            $('#support_captcha_image').attr('src', '/usercab/showsupportcaptcha/rnd/'+rnd);
        });

        $('#send_support_button').click(function(){

            $.ajax({
                type: 'POST',
                url: '/usercab/sendsupport',
                dataType: 'json',
                data: $('#support_form').serialize(),
                success: function(msg){

                    if(msg['status'] == 'error')
                    {
                        //$('#reload_support_captcha').click();
                        $('#support_error').html(msg['message']);
                    }

                    if(msg['status'] == 'ok')
                    {
                        $('#support_error').html('');
                        $('#send_support_button').css('display', 'none');
                        $('#div_support_form').html(msg['message']);
                    }
                    //console.log(msg);
                    //$('#search_data').html(msg);

                }
            });

        });

    </script>

<?
//deb::dump(Yii::app()->session['supportcaptcha']);
?>