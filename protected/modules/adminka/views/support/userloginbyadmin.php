
<?
$this->renderPartial('/default/_admin_menu');
?>

<div style="margin: 50px;">
    <b>Вход как пользователь:</b><br><br>
<form method="post" action="/adminka/support/userloginbyadmin">
    <input type="text" placeholder="E-mail пользователя" style="width: 300px; font-size: 14px;" name="UserLoginByAdmin">
    <input type="submit" value="Войти">
</form>
</div>