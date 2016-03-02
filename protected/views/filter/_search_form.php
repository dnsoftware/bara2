<?
// Стили данного отображения
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/filter/search_form.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/filter/search_form.js', CClientScript::POS_END);

?>

<form id="form_filter" method="post" action="<?= Yii::app()->createUrl('filter/search');?>">

        <span myhint="Тестовая кнопка" style="display: none; background-color: #aaaaaa;" onclick="changeFilterReload('<?= Yii::app()->createUrl('filter/getdatafilter');?>')">
            Фильтр
        </span>
<?
?>
    <table id="sform_tbl">
        <tr>
            <td>
                <select class="filterselect fchange" name="mainblock[r_id]" id="r_id">
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
                        <option class="fsrub" <?= $selected;?> value="<?= $rval['parent']->r_id;?>"><?= $rval['parent']->name;?></option>
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
            <td id="sform_searchfield">
                <input type="text" name="params[q]" id="searchquery" placeholder="Поиск по объявлениям" autocomplete="off" value="<?= htmlspecialchars($_GET['params']['q']);?>">

            </td>

            <td id="td_selectregion">
                <select class="filterselect" name="mesto_id" id="mesto_id" onchange="displaySearchReg();">
                    <?
                    $data = '';
                    //if(isset($_GET['mainblock']['t_id']) && intval($_GET['mainblock']['t_id']) > 0)
                    if($mselector == 't' && $m_id > 0)
                    {
                        $data = FilterController::ListMestoForSearch('t', $m_id);
                    }
                    else
                    //if(isset($_GET['mainblock']['reg_id']) && intval($_GET['mainblock']['reg_id']) > 0)
                    if($mselector == 'reg' && $m_id > 0)
                    {
                        $data = FilterController::ListMestoForSearch('reg', $m_id);
                    }
                    else
                    //if(isset($_GET['mainblock']['c_id']) && intval($_GET['mainblock']['c_id']) > 0)
                    if($mselector == 'c' && $m_id > 0)
                    {
                        $data = FilterController::ListMestoForSearch('c', $m_id);
                    }
                    /*
                    else
                    if($cookie['mytown'] > 0)
                    {
                        $data = FilterController::ListMestoForSearch('t', $cookie['mytown']);
                    }
                    else
                    if($cookie['myregion'] > 0)
                    {
                        $data = FilterController::ListMestoForSearch('reg', $cookie['myregion']);
                    }
                    else
                    if($cookie['mycountry'] > 0)
                    {
                        $data = FilterController::ListMestoForSearch('c', $cookie['mycountry']);
                    }
                    */
                    else
                    {
                        $data = FilterController::ListMestoForSearch('none', 0);
                    }
                    echo $data;
                    ?>
                </select>

            </td>
            <?

            /*
            ?>
            <td  style="margin: 0px; padding: 0px; ">

                <select class="sumo_simple" style="width: 50px;" name="mainblock[c_id]" id="select_country">
                    <?
                    //$c_id = intval(AdvertController::getMainblockValue(null, 'c_id'));
                    Countries::displayCountryList(intval($_GET['mainblock']['c_id']));

                    ?>
                </select>
            </td>
            <td>
                <select class="_sumoselect" style="width: 50px;" name="mainblock[reg_id]" id="select_region">
                    <?
                    //$reg_id = intval(AdvertController::getMainblockValue(null, 'reg_id'));
                    Regions::displayRegionList(intval($_GET['mainblock']['c_id']), intval($_GET['mainblock']['reg_id']));
                    ?>
                </select>
            </td>
            <td>
                <select class="_sumoselect" style="width: 50px; " name="mainblock[t_id]" id="select_town" >
                    <?
                    //$t_id = intval(AdvertController::getMainblockValue(null, 't_id'));
                    Towns::displayTownList(intval($_GET['mainblock']['reg_id']), intval($_GET['mainblock']['t_id']));
                    ?>
                </select>
            </td>
            <?
            */
            ?>
            <td id="searchbut">
                <input type="hidden" name="params[s]" id="sort" value="<?= htmlspecialchars($_GET['params']['s']);?>">
                <input id="btsubmit" type="submit" name="filter_submit_button" value="Найти">
            </td>
        </tr>
    </table>


    <div id="form_search_filter">
        <?

        // Если страница поиска
        if((Yii::app()->controller->action->id == 'index' && Yii::app()->controller->id == 'filter'))
        {
            $this->renderPartial('_props_form_search', array(
                //'rubrik_groups'=>$rubrik_groups,
                //'search_adverts'=>$search_adverts,
                //'props_array'=>$props_array,
                //'rub_array'=>$rub_array,
                'props_sprav_sorted_array'=>$props_sprav_sorted_array,
                'rubriks_props_array'=>$rubriks_props_array,
            ));
        }

        ?>
    </div>



</form>



<div id="div_searchreg_name" style="display: none; position: absolute; background-color: #ddd; padding: 5px; border:#000020 solid 0px; padding-top: 30px; opacity: 0.9; z-index: 100;">

    <div >
        <input type="text" name="searchreg_name" id="searchreg_name" value="" style="width: 335px;" placeholder="начните набирать название своего города или региона" >
    </div>


</div>


<script>

    $(document).ready(function()
    {
        search_form_init('<?= Yii::app()->createUrl('/filter/getregionlist');?>',
            '<?= Yii::app()->createUrl('/filter/mestolistgenerate');?>',
            '<?= Yii::app()->createUrl('/filter/getquerylist');?>');
    });

</script>
