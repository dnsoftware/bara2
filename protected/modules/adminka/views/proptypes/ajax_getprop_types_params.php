<div class="form">

    <div id="div_types_params_form_error_<?= $type_id;?>">
    </div>

    <form id="types_params_form_<?= $type_id;?>" method="post" action="/index.php?r=adminka/property/ajax_addtypeparam">

        <input type="text" name="typeparam[type_id]" value="<?= $type_id;?>">

        <input type="text" name="typeparam[selector]" value="">
        <input type="text" name="typeparam[ptype]" value="">

        <?
        echo CHtml::dropDownList('rubrikprops[type_id]', '', RubriksProps::$vibor_type);
        ?>

        <input type="submit" value="Добавить">

    </form>

</div>


<script>

    $('#types_params_form_<?= $type_id;?>').submit(
        function()
        {
            $.ajax({
                type: 'POST',
                url: this.action,
                data: $(this).serialize(),
                success: function(ret) {
                    $('#div_types_params_form_error_<?= $type_id;?>').html(ret);
                }
            });

            return false;
        }

    )

</script>