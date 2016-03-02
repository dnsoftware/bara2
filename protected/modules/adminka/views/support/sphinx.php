<?php
$this->renderPartial('/default/_admin_menu');
$this->renderPartial('sphinx_menu');

?>

<div style="margin-left: 20px;">
<h2 style="font-size: 13px; margin-top: 10px; font-weight: bold;">Статистика</h2>


<?
if($runsphinx_tag == 1)
{
?>
    <div style="color: #006600">Sphinx запущен</div>
<?
}
else
{
?>
    <div style="color: #f00">Sphinx не запущен</div>
<?
}
?>

    <div>
        Объявлений: <b><?= $count_adverts;?></b><br>
        Записей в индексе: <b><?= $index_count;?></b>

    </div>


</div>