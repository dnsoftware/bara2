<?
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.sumoselect.min.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/sumoselect.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/filtercontroller.js');
?>

<h1 style="font-size: 16px; text-align: center; margin: 10px;">Импорт старых объявлений</h1>

<div style="color: #f00;">Внимание! Только значения простых свойств могут быть занесены в базу! Мультивыбор, диапазоны, ручной ввод и т.п. не поддерживается.</div>

<div style="margin: 50px; font-size: 16px;">
    <!--<a href="<?= Yii::app()->createUrl('/adminka/support/importoldadverts');?>">Начать импорт</a>-->

<?
//deb::dump($rubold_array);
?>
    <form id="form_filter" method="post" action="<?= Yii::app()->createUrl('/adminka/support/importoldadverts');?>">

        <div style="border: #ddd solid 1px; padding: 10px;">
            Фильтр по старым объявлениям:<br>
        <select name="oldbase[rubold]">
        <?
        foreach($rubold_array as $rkey=>$rval)
        {
        ?>
            <option value="<?= $rval['parent']['r_id'];?>" disabled style="color: #000; font-weight: bold;"><?= $rval['parent']['name'];?></option>
            <?
            foreach($rval['childs'] as $r2key=>$r2val)
            {
            ?>
                <option value="<?= $r2val['r_id'];?>" >&nbsp;&nbsp;<?= $r2val['name'];?></option>
            <?
            }
            ?>
        <?
        }
        ?>
        </select>
        </div>

        <div style="padding: 10px; margin-top: 20px; border: #005580 solid 1px;">
            Параметры занесения в новую базу:<br>


            <select class="filterselect fchange" name="mainblock[r_id]" id="r_id" style="margin: 0px;">
                <option value="">--- выберите категорию  ---</option>
                <?
                foreach ($rub_array as $rkey=>$rval)
                {
                    $selected = " ";
                    if($rkey == intval($_GET['mainblock']['r_id']))
                    {
                        $selected = " selected ";
                    }
                    ?>
                    <option <?= $selected;?> style="color:#000; font-weight: bold;" value="<?= $rval['parent']->r_id;?>"><?= $rval['parent']->name;?></option>
                    <?
                    foreach ($rval['childs'] as $ckey=>$cval)
                    {
                        $selected = " ";
                        if($ckey == intval($_GET['mainblock']['r_id']))
                        {
                            $selected = " selected ";
                        }
                        ?>
                        <option <?= $selected;?> value="<?= $cval->r_id;?>">&nbsp;<?= $cval->name;?></option>
                    <?
                    }
                }
                ?>
            </select>


        </div>

        <div id="form_search_filter">

        </div>

        <input type="submit" value="Импортировать">

        <!--
        <input type="button" value="Импортировать" onclick="SendImport();">
        -->

    </form>



</div>

<div id="search_data">

</div>



<script>
$('.fchange').change(function ()
{
changeFilterReload('<?= Yii::app()->createUrl('filter/getdatafilter');?>');
});


function SendImport()
{

}

</script>