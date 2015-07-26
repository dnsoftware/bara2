<?php
/* @var $this FilterController */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.sumoselect.min.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/sumoselect.css');


$this->breadcrumbs=array(
	'Filter',
);
?>
<h1><?php echo $this->id . '/' . $this->action->id; ?></h1>


<div id="form_search" style="margin-bottom: 25px;">

<?
deb::dump($_GET);
?>

    <form id="form_filter" method="post" action="<?= Yii::app()->createUrl('filter/search');?>" _onsubmit="advertFilter('<?= Yii::app()->createUrl('filter/search');?>'); return false;">

        <table  style="display: inline;">
            <tr>
                <td>
                    <select class="_sumoselect" name="mainblock[r_id]" id="r_id" class="" onchange="">
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
                </td>
                <td>
                    <input style="width: 200px; border: 1px solid #A4A4A4; min-height: 14px; background-color: #fff;border-radius:2px;margin:0px; padding: 5px;" type="text" name="params[q]" placeholder="Поиск по объявлениям" value="<?= htmlspecialchars($_GET['params']['q']);?>">

                </td>

                <td  style="margin: 0px; padding: 0px;">
                    <select class="sumo_simple" style="width: 120px;" name="mainblock[c_id]" id="select_country">
                        <?
                        //$c_id = intval(AdvertController::getMainblockValue(null, 'c_id'));
                        Countries::displayCountryList(intval($_GET['mainblock']['c_id']));

                        ?>
                    </select>
                </td>
                <td>
                    <select class="_sumoselect" style="width: 150px;" name="mainblock[reg_id]" id="select_region">
                        <?
                        //$reg_id = intval(AdvertController::getMainblockValue(null, 'reg_id'));
                        Regions::displayRegionList(intval($_GET['mainblock']['c_id']), intval($_GET['mainblock']['reg_id']));
                        ?>
                    </select>
                </td>
                <td>
                    <select class="_sumoselect" style="width: 140px;" name="mainblock[t_id]" id="select_town" >
                        <?
                        //$t_id = intval(AdvertController::getMainblockValue(null, 't_id'));
                        Towns::displayTownList(intval($_GET['mainblock']['reg_id']), intval($_GET['mainblock']['t_id']));
                        ?>
                    </select>
                </td>
                <td>
                    <input type="submit" value="Найти">
                </td>
            </tr>
        </table>

        <?
        $this->renderPartial('_props_form_search', array(
            //'rubrik_groups'=>$rubrik_groups,
            //'search_adverts'=>$search_adverts,
            //'props_array'=>$props_array,
            //'rub_array'=>$rub_array,
            'props_sprav_sorted_array'=>$props_sprav_sorted_array,
            'rubriks_props_array'=>$rubriks_props_array,
        ));

        ?>




    </form>

    <div id="search_data">

    </div>

    <script>
        $('#select_country').change(function ()
        {
            $.ajax({
                type: 'POST',
                url: '<?= Yii::app()->createUrl('advert/get_html_regions');?>',
                data: 'c_id='+$(this).val(),
                success: function(msg){
                    $('#select_region').html(msg);

                    //$('#select_region')[0].sumo.unload();
                    //$('#select_region').SumoSelect();
                    $('#select_region').change();
                }
            });
        });

        $('#select_region').change(function ()
        {
            $.ajax({
                type: 'POST',
                url: '<?= Yii::app()->createUrl('advert/get_html_towns');?>',
                data: 'reg_id='+$(this).val(),
                success: function(msg){
                    $('#select_town').html(msg);

                    //$('#select_town')[0].sumo.unload();
                    //$('#select_town').SumoSelect();
                }
            });
        });

        function advertFilter(action)
        {
            $.ajax({
                type: 'POST',
                url: action,
                data: $('#form_filter').serialize(),
                success: function(msg){

                    $('#search_data').html(msg);

                }
            });
        }

    </script>

</div>


<?

foreach ($rubrik_groups as $rkey=>$rval)
{

?>
    <a href="<?= Yii::app()->createUrl($rval['path']);?>"><?= $rval['name'];?></a> (<?= $rval['cnt'];?>)
<?
}

?>
<table>
<?
//deb::dump(count($search_adverts));
foreach($search_adverts as $key=>$val)
{
?>
<tr style="">
    <td style="width: 125px;">
    <?
    if(count($props_array[$key]['photos']) > 0)
    {
    ?>
        <img width="120" src="/photos/<?= $props_array[$key]['photos'][0];?>">
    <?
    }
    ?>
    </td>
    <td>
    <?= $props_array[$key]['props_display'];?>
    </td>
</tr>
<?
}
?>
</table>
<?

//deb::dump($search_adverts);
//deb::dump($rubrik_groups);

?>
