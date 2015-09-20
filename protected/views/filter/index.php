<?php
/* @var $this FilterController */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.sumoselect.min.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/sumoselect.css');

?>

<div id="form_search" style="margin-bottom: 25px; ">


<?
//deb::dump($query_delta);
?>

    <form id="form_filter" method="post" action="<?= Yii::app()->createUrl('filter/search');?>" _onsubmit="advertFilter('<?= Yii::app()->createUrl('filter/search');?>'); return false;">

        <span myhint="Тестовая кнопка" style="display: none; background-color: #aaaaaa;" onclick="changeFilterReload('<?= Yii::app()->createUrl('filter/getdatafilter');?>')">
            Фильтр
        </span>

        <table  style="display: inline; width: 100%;">
            <tr style="background-color: #eee;">
                <td style="border: #000020 solid 0px; padding: 0px 0px 0px 5px;">
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
                </td>
                <td style="width: 100%; border: #000020 solid 0px;">
                    <input style="width: 100%; border: 1px solid #A4A4A4; min-height: 14px; background-color: #fff;border-radius:2px;margin:0px; padding: 5px;" type="text" name="params[q]" placeholder="Поиск по объявлениям" value="<?= htmlspecialchars($_GET['params']['q']);?>">

                </td>

                <td style="border: #000020 solid 0px; padding-right: 0px;">
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
                <td style="padding-right: 5px;">
                    <input style=" padding: 3px;" type="submit" name="filter_submit_button" value="Найти">
                </td>
            </tr>
        </table>


        <div id="form_search_filter">
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
        </div>



    </form>

<div style="margin: 5px; 0px 5px 0px">
    <?
    $url_parts = array();
    $url = '';
    $bread_count = count($this->breadcrumbs);
    $i=0;
    foreach ($this->breadcrumbs as $bkey=>$bval)
    {
        $i++;
        if($bval['type']=='subrubrik')
        {
            $url_parts[$bkey-1] = $bval['transname'];
        }
        else
        {
            $url_parts[$bkey] = $bval['transname'];
        }
        $url = implode("/", $url_parts);;
        ?>
        <a  class="baralink" href="/<?= $url;?>"><?= $bval['name'];?></a>
        <?
        if($i != $bread_count)
        {
            echo "<span class='baralink'> > </span>";
        }
    }
    ?>
</div>


<div id="div_searchreg_name" style="display: none; position: absolute; background-color: #ddd; padding: 5px; border:#000020 solid 0px; padding-top: 30px; opacity: 0.9">

    <div >
    <input type="text" name="searchreg_name" id="searchreg_name" value="" style="width: 335px;" placeholder="начните набирать название своего города или региона" >
    </div>


</div>


<script>

    $('#div_searchreg_name').offset({
        left: $('#mesto_id').offset().left-10,
        top: $('#mesto_id').offset().top-5
    });//$('#mesto_id').offset().left;

    $('#searchreg_name').autocomplete({
        position:{my:"left top", at:"left bottom"},
        minLength: 3,
        source: function(request, response){

            $.ajax({
                url: "<?= Yii::app()->createUrl('/filter/getregionlist');?>",
                method: "post",
                dataType: "json",
                // параметры запроса, передаваемые на сервер:
                data:{
                    searchstr: request.term
                },
                // обработка успешного выполнения запроса
                success: function(data){
                    $('#ajax_debug').html(data);
                    // приведем полученные данные к необходимому формату и передадим в предоставленную функцию response
                    response($.map(data.reglist, function(item){
                        console.log(item);

                        return{
                            label: item.name_ru,
                            value: item.id
                        }

                    }));

                }


            });
        },
        focus: function( event, ui ) {
            //$('#searchreg_name').val( ui.item.label );
            return false;

        },
        select: function(event, ui) {
            /*ui.item будет содержать выбранный элемент*/
            //console.log(ui.item);
            //$('#searchreg_id').val(ui.item.value);

            $.ajax({
                url: "<?= Yii::app()->createUrl('/filter/mestolistgenerate');?>",
                method: "post",
                dataType: "json",
                // параметры запроса, передаваемые на сервер:
                data:{
                    mesto_id: ui.item.value
                },
                // обработка успешного выполнения запроса
                success: function(data){
                    $('#mesto_id').html(data['data']);
                    $('#div_searchreg_name').css('display', 'none');
                }
            });


            return false;
        }

    });

    function displaySearchReg()
    {
        if($('#mesto_id').val() == 'other')
        {
            $('#div_searchreg_name').css('display', 'block');
            $('#searchreg_name').focus();
        }

    }

</script>




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

        function changeFilterReload(action)
        {
            $.ajax({
                type: 'get',
                url: action,
                data: $('#form_filter').serialize(),
                success: function(msg){

                    $('#form_search_filter').html(msg);

                }
            });
        }

    </script>

</div>


<?

foreach ($rubrik_groups as $rkey=>$rval)
{

?>
    <a class="baralink_plus" href="<?= Yii::app()->createUrl($rval['path']);?>"><?= $rval['name'];?></a> <span class="notcount"><?= $rval['cnt'];?></span>
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
