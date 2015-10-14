<?php
/* @var $this SupportController */

$this->breadcrumbs=array(
	'Support',
);

$this->renderPartial('/default/_admin_menu');

?>
<h1 style="margin: 10px; font-size: 14px;">Тестирование отправки писем</h1>

<form id="mail_form" method="post">
    <input type="hidden" name="selector" value="sendmail">

    <input type="text" name="nameto" value="Дарт Вейдер">

    <select name="mailto" >
        <?
        foreach($this->sendmail_list as $val)
        {
        ?>
        <option value="<?= $val;?>"><?= $val;?></option>
        <?
        }
        ?>
    </select>
    Тема: <input type="text" name="subject" style="width: 400px;" value="Подтвердите свой e-mail для завершения регистрации">
    <span style="margin-left: 50px;"><input id="sendbutton" type="button" value="Отправить"></span>
    <span id="sendstatus"></span>
    <br><br>
    <textarea name="message" style="width: 600px; height: 300px;">
        <p>
            Здравствуйте!
        </p>
        <p>
            Вы получили это письмо, потому что Ваш e-mail (medzhis@gmail.com) был указан при регистрации аккаунта на сайте частных бесплатных объявлений <a href="http://baraholka.ru">baraholka.ru</a>.
        </p>
        <p>
            Чтобы подтвердить регистрацию, нажмите на эту ссылку до <?= date("d.m.Y", time()+86400*30);?>: <a href="http://baraholka.ru/user/activation/activation?activkey=dkshfkgajsgdjhasgdas&email=test2015@mail.ru">Нажмите на эту ссылку для активации</a>.
        </p>
        <p>
            Если ссылка не открывается, скопируйте ее в адресную строку своего браузера.
        </p>
        <p>
            Если Вы не регистрировались на сайте baraholka.ru, то просто оставьте это письмо без дополнительных действий. Аккаунт не будет верифицирован и никто не сможет подавать объявления, указывая Ваш e-mail.
        </p>
        <p>
            __________________________<br>
            С наилучшими пожеланиями,<br>
            коллектив сайта baraholka.ru
        </p>

    </textarea>

</form>




<script>

    $('#sendbutton').click(function(){

        $('#sendstatus').css('color', '#f00');
        $('#sendstatus').html('Отправляется');

        $.ajax({
            url: "<?= Yii::app()->createUrl('/adminka/support/sendmail');?>",
            method: "post",
            //dataType: "json",

            data: $('#mail_form').serialize(),

            success: function(data){
                if(data == 'ok')
                {
                    $('#sendstatus').css('color', '#198A0E');
                    $('#sendstatus').html('Отправлено');
                }
            }


        });
    });


</script>



<div style="height: 200px;"></div>