<?
$urlasset = Yii::app()->assetManager->publish( Yii::getPathOfAlias('ext.geolocator.css').'/geolocator.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.$urlasset);

$urlasset = Yii::app()->assetManager->publish( Yii::getPathOfAlias('ext.geolocator.js').'/geolocator.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.$urlasset, CClientScript::POS_END);

?>

<div id="div_current_geoname">
<span id="current_geoname">
<?
    $region_name = '';
    if(Yii::app()->request->cookies['geo_mytown_name']->value != '')
    {
        $region_name = Yii::app()->request->cookies['geo_mycountry_name']->value .", ". Yii::app()->request->cookies['geo_mytown_name']->value;
        //echo $region_name;
    }
    else
    if(Yii::app()->request->cookies['geo_myregion_name']->value != '')
    {
        $region_name = Yii::app()->request->cookies['geo_mycountry_name']->value .", ". Yii::app()->request->cookies['geo_myregion_name']->value;
        //echo $region_name;
    }
    else
    if (Yii::app()->request->cookies['geo_mycountry_name']->value != '')
    {
        $region_name = Yii::app()->request->cookies['geo_mycountry_name']->value;
        //echo $region_name;
    }
    else
    {
        $region_name = "Регион не определен";
    }

    if(Yii::app()->request->cookies['geo_mytown_handchange_tag']->value == 1)
    {
        echo $region_name;
    }
    else
    {
        echo "Регион не определен";
    }

//deb::dump($_COOKIE);

?>
    <i class="regselect_arrow"></i>
</span>


</div>

<div id="region_change">
    <form id="form_region_change" action="<?= Yii::app()->createUrl('/filter/setregioncookie');?>" method="post" onsubmit="if($('#geo_region_id').val() == '') return false;">
    <input type="hidden" name="region_id" id="geo_region_id" value="" >
    <input type="text" name="region_name" id="geo_region_name" value="" placeholder="начните набирать название своего города или региона">

    <input type="hidden" name="reg_confirm_tag" id="reg_confirm_tag" value="1">

    </form>
</div>

<?
if(Yii::app()->request->cookies['region_confirm_tag']->value == 0)
{
?>
<div id="div_reg_confirm">

    <div class="reg_confirm_close">x</div>

    <?

    if(!isset(Yii::app()->request->cookies['region_confirm_tag']))
    {
        $cookie = new CHttpCookie('region_confirm_tag', 0);
        $cookie->expire = time() + 86400*30*12;
        Yii::app()->request->cookies['region_confirm_tag'] = $cookie;
    }

    //deb::dump(Yii::app()->request->cookies['region_confirm_tag']->value);
    //deb::dump($cookie['mytown']);
    //deb::dump($cookie['myregion']);
    //deb::dump($cookie['mycountry']);
    ?>
    <div id="opr_or_no">
    <?
    if($region_name != '' && $region_name != "Регион не определен")
    {
    ?>
        <div id="vibor_reg">
            <div id="vibor_reg_inner">Выберите город или регион для фильтрации<br>
            объявлений по территориальному признаку.</div>
            Мы определили Ваш регион как <span id="vibor_reg_regname"><?= $region_name;?></span>:
        </div>


        <span id="reg_confirm_yes" class="reg_confirm_yes">Да, правильно</span>
        <span id="reg_confirm_no">Нет, неправильно</span>
    <?
    }
    else
    {
    ?>
        <div id="your_reg_noopr">
        Ваш регион не определен!
        </div>
        <span id="reg_confirm_no">Указать регион</span>
    <?
    }
    ?>
    </div>

</div>
<?
}
?>

<script>

    $(document).ready(function()
    {
        getregion_js('<?= Yii::app()->createUrl('/filter/getregionlist');?>',
            '<?= Yii::app()->createUrl('/site/setregconfirmyes');?>');
    });


</script>