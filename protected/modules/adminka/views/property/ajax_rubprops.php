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
                echo CHtml::dropDownList('rubrikprops[type_id]', '', PropTypes::getPropsType());
                ?>
            </td>
            <td>
                <input type="submit" value="Добавить">
            </td>
        </tr>
        </table>


    </form>

</div>

<div id="div_rubriks_props">
<?
foreach ($model_items as $mkey=>$mval)
{
    $this->renderPartial('_rubprops_item', array('model'=>$mval, 'props_type_array'=>$props_type_array));

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