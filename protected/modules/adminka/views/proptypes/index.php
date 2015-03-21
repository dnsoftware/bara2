<?php
/* @var $this PropTypesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Prop Types',
);

$this->menu=array(
	array('label'=>'Create PropTypes', 'url'=>array('create')),
	array('label'=>'Manage PropTypes', 'url'=>array('admin')),
);
?>
<style>
    .tparam_item table
    {
        border-spacing: 1px;
    }
    .pointer
    {
        cursor: pointer;
    }
</style>

<h1>Prop Types</h1>

<div style="color: #f00; display: none;" id="div_errors">

</div>

<?php
    $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
    )); ?>


<script>
    $('.divparam').click(
        function()
        {
            obj = $('#div_params_'+$(this).attr('type_id'));

            $('#div_errors').css('display', 'none');
            obj.prepend($('#div_errors'));
            if (obj.css('display') == 'block')
            {
                obj.css('display', 'none');
            }
            else
            {
                obj.css('display', 'block');
            }
        }

    );


    $('.prop_types_params_form').submit(
        function()
        {
            obj.prepend($('#div_errors'));

            $.ajax({
                type: 'POST',
                url: this.action,
                data: $(this).serialize(),
                success: function(ret) {
                    $('#div_errors').html('');
                    $('#div_errors').css('display', 'block');

                    if(ret.indexOf('<!--ok-->') + 1)
                    {
                        matches = ret.match(/<!--type_id--([^<]+)--\/type_id-->/i);
                        type_id = matches[1];

                        //alert('div_prop_item_'+type_id);
                        $('#div_params_'+type_id).append(ret);

                        //console.log(matches[1]);
                        //alert('dd');
                        //location.href = '/index.php?r=adminka/proptypes/index&r_id=';
                    }
                    else
                    {
                        $('#div_errors').html(ret);
                    }
                }
            });

            return false;
        }

    )

    function edit_rubprops_row(pt_id)
    {
        $.ajax({
            type: 'POST',
            url: '/index.php?r=adminka/proptypesparams/ajax_edit_rubprops_row',
            data: 'pt_id='+pt_id,
            success: function(ret) {
                //alert(ret);
                $('#tparam_item_'+pt_id).html(ret);
            }
        });

    }

    function saveedit_rubprops(pt_id)
    {
        $.ajax({
            type: 'POST',
            url: '/index.php?r=adminka/proptypesparams/ajax_saveedit_rubprops_row',
            data: $('#ptp_form_'+pt_id).serialize(),
            success: function(ret) {
                if(ret.indexOf('<!--ok-->') + 1)
                {
                    $('#tparam_item_edit_'+pt_id).parent().html(ret);
                }
                else
                {
                    alert(ret);
                }
            }
        });

    }

    function del_type_params(pt_id)
    {
        $.ajax({
            type: 'POST',
            url: '/index.php?r=adminka/proptypesparams/ajax_del_rubprops_row',
            data: 'pt_id='+pt_id,
            success: function(ret) {
                if(ret.indexOf('<!--ok-->') + 1)
                {
                    $('#tparam_item_'+pt_id).remove();
                }
            }
        });
    }


</script>