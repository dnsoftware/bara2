<?php

?>

<div style="background-color: #eee; margin: 0px; padding: 3px;">
<table style="margin: 0px;">
<tr>
    <td><a class="baralink" href="<?= Yii::app()->createUrl('/user/profile');?>">Профиль</a></td>
</tr>
<tr>
    <?
    ?>
    <td><a class="baralink" href="<?= Yii::app()->createUrl('/usercab/adverts');?>">Мои объявления (<?= Yii::app()->session->get('usernotcount');?>)</td>
</tr>
<tr>
    <td><a class="baralink" href="<?= Yii::app()->createUrl('/usercab/support');?>">Тех.поддержка</a></td>
</tr>
</table>
</div>
