<?php

class AdvertController extends Controller
{
	public function actionAddadvert()
	{
        $rub_array = Rubriks::get_rublist();

        $this->render('addadvert', ['rub_array'=>$rub_array]);
    }

    public function actionGetRubriksProps()
    {
        $r_id=intval($_POST['r_id']);

        $model= new RubriksProps();

        $model_items = RubriksProps::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id = '.$r_id,
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
        ));

        $model_items_array = array();
        foreach ($model_items as $mkey=>$mval)
        {
            $model_items_array[$mval->rp_id] = $mval;
        }
    //deb::dump($model_items);
        $props_type_array = PropTypes::getPropsType();
        $potential_parents = RubriksProps::getPotentialParents($r_id, 0);
//deb::dump($props_type_array);

        $props_hierarhy = array();
        foreach ($model_items as $mkey=>$mval)
        {
            if ($mval->parent_id <= 0)
            {
                $props_hierarhy[$mval->selector]['parent_selector'] = '';
            }
            else
            {
                $props_hierarhy[$mval->selector]['parent_selector'] = $model_items_array[$mval->parent_id]->selector;
                $props_hierarhy[$model_items_array[$mval->parent_id]->selector]['childs_selector'][$mval->selector] = $mval->selector;
            }
        }

        //deb::dump($props_hierarhy);


        foreach ($model_items as $mkey=>$mval)
        {
    ?>
         <div id="div_<?= $mval->selector;?>">
            <input type="text" name="<?= $mval->selector;?>" id="<?= $mval->selector;?>">
            <span class="addnot-field-selected" id="<?= $mval->selector;?>-span" inputfield="<?= $mval->selector;?>"></span>
            <input style="width: 20px; background-color: #ddd;" readonly type="text" name="<?= $mval->selector;?>-id" id="<?= $mval->selector;?>-id">
         </div>
    <?
        }

        ?>
        <script>
        var props_hierarhy = [];

        props_hierarhy = <?= json_encode($props_hierarhy); ?>;
        //console.log(props_hierarhy['auto_model']);
        </script>

        <?
        $this->renderPartial('_get_rubriks_props');
        ?>

        <script>

        get_props_list('auto_marka', '');
        get_props_list('auto_model', 'auto_marka');


        $('.addnot-field-selected').click(
        function()
        {
        $(this).css('display', 'none');
        $('#'+$(this).attr('inputfield')).css('display', 'inline');
        }
        );
        </script>

        <?

    }


    public function actionGetpropslist()
    {

        $model_rubriks_props = RubriksProps::model()->find(
            array(
                'condition'=>'selector = :selector',
                'params'=>array(':selector'=>$_POST['field_id']),
            )
        );
        $props_list = PropsSprav::getPropsList($model_rubriks_props, intval($_POST['parent_ps_id']), $_POST['field_value']);
//deb::dump($props_list);
        $return_array = array();
        $return_array['status'] = 'ok';
        $return_array['props_list'] = array();

        if (count($props_list) > 0)
        {
            foreach ($props_list as $pkey=>$pval)
            {
                $return_array['props_list'][$pval->ps_id]['ps_id'] = $pval->ps_id;
                $return_array['props_list'][$pval->ps_id]['value'] = $pval->value;
            }
        }

        echo json_encode($return_array);

    }











	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}