<div class="form">

    <form id="rubriks_props_form" method="post" action="/index.php?r=adminka/property/ajax_addrubprops">

        <input type="hidden" name="rubrikprops[r_id]" value="<?= $r_id;?>">

        <table style="margin: 1px; width: 690px;">
        <tr>
            <td>
                selector<br>
                <input type="text" name="rubrikprops[selector]" value="">
            </td>
            <td>
                name<br>
                <input type="text" name="rubrikprops[name]" value="">
            </td>
            <td>
                type_id<br>
                <?
                echo CHtml::dropDownList('rubrikprops[type_id]', '', PropTypes::getPropsType(),
                                            array('style'=>'width: 130px;'));
                ?>
            </td>
            <td>
                vibor_type<br>
                <?
                echo CHtml::dropDownList('rubrikprops[vibor_type]', '', RubriksProps::$vibor_type,
                    array('style'=>'width: 130px;'));
                ?>
            </td>
            <td>
                sort_props_sprav<br>
                <?
                echo CHtml::dropDownList('rubrikprops[sort_props_sprav]', '', RubriksProps::$sort_sprav,
                    array('style'=>'width: 130px;'));
                ?>
            </td>
            <td rowspan="2">
                <input type="submit" value="Добавить">
            </td>
        </tr>
        <tr>
            <td>
                hierarhy_tag<br>
                <input type="checkbox" name="rubrikprops[hierarhy_tag]" value="1">
            </td>

            <td>
                hierarhy_level<br>
                <input type="text" name="rubrikprops[hierarhy_level]" value="0">
            </td>

            <td>
                display_sort<br>
                <input type="text" name="rubrikprops[display_sort]" value="0">
            </td>

            <td>
                use_in_filter<br>
                <input type="checkbox" name="rubrikprops[use_in_filter]" value="1">
            </td>

            <td>

            </td>
        </tr>
        </table>


    </form>

</div>

<div id="div_rubriks_props">
<?
foreach ($model_items as $mkey=>$mval)
{
    $this->renderPartial('_rubprops_item',
            array('model'=>$mval, 'props_type_array'=>$props_type_array, 'potential_parents'=>$potential_parents));

}
?>
</div>

<script>

    $('#rubriks_props_form').submit(
        function()
        {
            $.ajax({
                type: 'POST',
                url: this.action,
                data: $('#rubriks_props_form').serialize(),
                success: function(ret) {
                    if(ret.indexOf('<!--ok-->') + 1)
                    {
                        $('#div_rubriks_props').append(ret);
                    }
                    else
                    {
                        $('#div_errors').css('display', 'block');
                        $('#div_errors').html(ret);
                    }

                }
            });

            return false;
        }

    )



</script>