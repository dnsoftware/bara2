<?php
/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 12.06.15
 * Time: 17:44
 */

?>


<h1 style="clear: both;"><?= $mainblock['title'];?></h1>


<?
$this->renderPartial('_advertpage', array(
    'mainblock'=>$mainblock,
    'addfield'=>$addfield,
    'uploadfiles_array'=>$this->uploadfiles_array,
    'mainblock_data'=>$this->mainblock_data,
    'addfield_data'=>$this->addfield_data,
    'options'=>$this->options
));



if(Yii::app()->user->id > 0)
{
    echo "Вы залогинены, все ок";
    ?>
    <form id="savenew" method="post" action="<?= Yii::app()->createUrl('advert/savenew');?>">
    <table style="width: 400px;">
        <tr>
            <td>
                <input type="submit" value="Опубликовать">
            </td>
        </tr>
    </table>
    </form>
    <?
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




    Galleria.loadTheme('/js/galleria/themes/classic/galleria.classic.min.js');
    Galleria.run('.galleria', {
        width: 500,
        height: 400,
        //imageCrop: 'landscape'
        lightbox: true,
        //overlayBackground: '#ffffff'
        showImagenav: true,

    })
</script>

<?
//deb::dump($uploadfiles_array);

//deb::dump(Yii::app()->session['mainblock']);
//deb::dump(Yii::app()->session['addfield']);





















