<?php
/* @var $this UsercabController */

$this->breadcrumbs=array(
	'Usercab',
);
?>
<h1><?php echo $this->id . '/' . $this->action->id; ?></h1>

<table>
<?
foreach ($adverts as $akey=>$aval)
{
?>
<tr>
    <td><?= date('d-m-Y H:i:s', $aval->date_add);?></td>
    <td>
        <a href=""><?= $aval->title;?></a>
    </td>
    <td>
        <a href="/index.php?r=usercab/advert_edit&n_id=<?= $aval->n_id;?>">Редактировать</a>
    </td>
</tr>
<?
}
?>
</table>