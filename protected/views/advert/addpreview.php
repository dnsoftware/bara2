<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/baraholka.js');

Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/notice/addpreview.css');

?>


<h1 class="h1-preview">Предварительный просмотр объявления</h1>


<?
$this->renderPartial('_advertpage', array(
    'mainblock'=>$mainblock,
    'addfield'=>$addfield,
    'uploadfiles_array'=>$this->uploadfiles_array,
    'mainblock_data'=>$this->mainblock_data,
    'addfield_data'=>$this->addfield_data,
    'options'=>$this->options
));

//deb::dump($mainblock);

if(Yii::app()->user->id > 0)
{
    /*
    ?>
    <a href="<?= Yii::app()->createUrl('advert/savenew');?>">Опубликовать</a>
    <?
    */
}
else
{
    ?>
    <form id="addregloginform" onsubmit="SendUserForm(); return false;">
    <?
    if($email_in_database_tag == 1)
    {
        echo "Ваше мыло в базе, введите пароль";
    ?>
        <input type="hidden" name="usertype" value="inbase">
        <table style="width: 400px;">
            <tr>
                <td>
                    Электронная почта
                </td>
                <td>
                    <input type="text" name="RegistrationForm[email]" value="<?= $mainblock['client_email'];?>" readonly style="border: none;">
                </td>
            </tr>
            <tr>
                <td>
                    Пароль
                </td>
                <td>
                    <input type="password" name="RegistrationForm[password]" value="" >
                </td>
            </tr>
        </table>
    <?
    }
    else
    {
        echo "Регистрация нового юзера<br>";
    ?>
        <input type="hidden" name="usertype" value="newreg">
        <table style="width: 400px;">
        <tr>
            <td>
                Электронная почта
            </td>
            <td>
                <input type="text" name="RegistrationForm[email]" value="<?= $mainblock['client_email'];?>" readonly style="border: none;">
            </td>
        </tr>
        <tr>
            <td>
                Логин
            </td>
            <td>
                <input type="text" name="RegistrationForm[username]" value="" >
            </td>
        </tr>
        <tr>
            <td>
                Пароль
            </td>
            <td>
                <input type="password" name="RegistrationForm[password]" value="" >
            </td>
        </tr>
        <tr>
            <td>
                Повторите пароль
            </td>
            <td>
                <input type="password" name="RegistrationForm[verifyPassword]" value="" >
            </td>
        </tr>
        </table>
    <?
    }
    ?>
        <div id="addreg_errors" style="display: none; color: #f00">

        </div>

        <input type="submit" value="Опубликовать">
    </form>
    <?
}

?>

<div id="boxes">
    <div id="dialog" class="window">
        <div class="content">Ваше объявление почти опубликовано!<br>
            Проверьте, как оно будет выглядеть на сайте и если все в порядке - нажмите кнопку "Опубликовать", если требуется редактирование, нажмите кнопку "Редактировать"</div>
        <div ><span class="close"/>OK</span></div>
    </div>
</div>

<!-- Маска, затемняющая фон -->
<div id="mask"></div>


<script>
    function SendUserForm()
    {
        var form_data = $('#addregloginform').serialize();

        $.ajax({
            type: "POST",
            url: '<?= Yii::app()->createUrl('advert/addreglogin');?>',
            data: form_data,
            dataType: 'json',
            error: function(msg) {

            },
            success: function(msg) {
                $('#addreg_errors').css('border', '');
                $('#addreg_errors').html('');
                $('#addreg_errors').css('display', 'none');

                //console.log(msg);

                if(msg['status'] == 'error')
                {
                    $('#addreg_errors').css('border', '#f00 solid 2px');
                    $('#addreg_errors').html(msg['message']);
                    $('#addreg_errors').css('display', 'block');
                }

                if(msg['status'] == 'ok')
                {
                    location.href = '<?= Yii::app()->createUrl('advert/savenew');?>';
                }

            }
        });
    }

    $(document).ready(function() {

        //e.preventDefault();
        var id = '#dialog';
        var maskHeight = $(document).height();
        var maskWidth = $(window).width();
        $('#mask').css({'width':maskWidth,'height':maskHeight});
        $('#mask').fadeIn(100);
        $('#mask').fadeTo("slow",0.2);
        var winH = $(window).height();
        var winW = $(window).width();
        $(id).css('top',  winH/2-$(id).height()/2);
        $(id).css('left', winW/2-$(id).width()/2);
        $(id).fadeIn(100);

        $('.window .close').click(function (e) {
            e.preventDefault();
            $('#mask, .window').hide();
        });

        $('#mask').click(function () {
            $(this).hide();
            $('.window').hide();
        });

    });

</script>

<?

















