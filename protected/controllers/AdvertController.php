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
            $props_hierarhy[$mval->selector]['vibor_type'] = $mval->vibor_type;

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
         <div id="div_<?= $mval->selector;?>" style="margin: 5px;padding: 3px; border: #dddddd solid 1px;">
             <div style="color: #00aa00"><?= $mval->selector;?></div>
         <?
         echo $mval->vibor_type."<br>";
         switch($mval->vibor_type)
         {
             case "autoload_with_listitem":
         ?>
             <input type="text" name="<?= $mval->selector;?>" id="<?= $mval->selector;?>">
             <span class="addnot-field-selected" id="<?= $mval->selector;?>-span" inputfield="<?= $mval->selector;?>"></span>
             <input style="width: 30px; background-color: #ddd;" readonly type="text" name="<?= $mval->selector;?>-id" id="<?= $mval->selector;?>-id">
         <?
             // id поля формы <input> в которое заносится выбранное значение
             $props_hierarhy[$mval->selector]['field_value_id'] = $mval->selector."-id";

             break;

             case "selector":
                 $props_hierarhy[$mval->selector]['field_value_id'] = $mval->selector;
             break;

             case "listitem":
             ?>
                 <span class="addnot-field-selected" id="<?= $mval->selector;?>-span" inputfield="<?= $mval->selector;?>"></span>
                 <input style="width: 30px; background-color: #ddd;" readonly type="text" name="<?= $mval->selector;?>" id="<?= $mval->selector;?>">

                 <div id="div_<?= $mval->selector;?>_list">
                 </div>
             <?
                 $props_hierarhy[$mval->selector]['field_value_id'] = $mval->selector;
             break;
         }
         ?>
         </div>
        <?
        }

        ?>

        <script>
            var props_hierarhy = [];
            props_hierarhy = <?= json_encode($props_hierarhy); ?>;
            //console.log(props_hierarhy);
        </script>

        <?
//deb::dump($props_hierarhy);
        $this->renderPartial('_get_rubriks_props');
        ?>

        <script>

            var get_props_list_functions = {
            <?
            foreach (RubriksProps::$vibor_type as $vkey=>$vval)
            {
            ?>
                f<?= $vkey;?>: function(field_id, parent_field_id) {
                    get_props_list_<?= $vkey;?>(field_id, parent_field_id);
                },
            <?
            }
            ?>
            };

        <?
        foreach ($model_items as $mkey=>$mval)
        {
            $field_id = $mval->selector;
            $parent_field_id = '';
            if($mval->parent_id > 0)
            {
                $parent_field_id = $model_items_array[$mval->parent_id]->selector;
            }


            /*
            // убрать switch, т.к. вызов всех функций формируется автоматом
            switch($mval->vibor_type)
            {
                case "autoload_with_listitem":
                ?>
                    get_props_list_<?= $mval->vibor_type;?>('<?= $field_id;?>', '<?= $parent_field_id;?>');
                <?
                break;

                case "selector":
                ?>
                    get_props_list_<?= $mval->vibor_type;?>('<?= $field_id;?>', '<?= $parent_field_id;?>');
                <?
                break;
            }
            */
            ?>
            get_props_list_<?= $mval->vibor_type;?>('<?= $field_id;?>', '<?= $parent_field_id;?>');
            <?
        }
        ?>

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


    public function actionGetpropslist_autocomplete()
    {

        $model_rubriks_props = RubriksProps::model()->find(
            array(
                'condition'=>'selector = :selector',
                'params'=>array(':selector'=>$_POST['field_id']),
            )
        );
        $props_list = PropsSprav::getPropsListAutocomplete($model_rubriks_props, intval($_POST['parent_ps_id']), $_POST['field_value']);
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


    public function actionGetpropslist_selector()
    {
        $field_id = $_POST['field_id'];
        $parent_field_id = $_POST['parent_field_id'];
        $parent_ps_id = intval($_POST['parent_ps_id']);
//тут остановился, переделать get_props_list_selector

        $model_rubriks_props = RubriksProps::model()->find(
            array(
                'condition'=>'selector = :selector',
                'params'=>array(':selector'=>$field_id),
            )
        );

        $prop_types_params_row = PropTypesParams::model()->find(array(
            'select'=>'*',
            'condition'=>'type_id = "'.$model_rubriks_props->type_id.'" AND selector = "item"',
        ));

        ?>
        <select name="<?= $field_id;?>" id="<?= $field_id;?>">
            <option value=""></option>
        <?

        $props_sprav = PropsSprav::getPropsListSelector($model_rubriks_props, $prop_types_params_row, $parent_ps_id);

        if (count($props_sprav) > 0)
        {
            foreach ($props_sprav as $pkey=>$pval)
            {
                ?>
                <option value="<?= $pval->ps_id;?>"><?= $pval->value;?></option>
            <?
            }
        }

        ?>
        </select>
        <?

    }


    public function actionGetpropslist_listitem()
    {
        $field_id = $_POST['field_id'];
        $parent_field_id = $_POST['parent_field_id'];
        $parent_ps_id = intval($_POST['parent_ps_id']);

        $model_rubriks_props = RubriksProps::model()->find(
            array(
                'condition'=>'selector = :selector',
                'params'=>array(':selector'=>$field_id),
            )
        );

        $prop_types_params_row = PropTypesParams::model()->find(array(
            'select'=>'*',
            'condition'=>'type_id = "'.$model_rubriks_props->type_id.'" AND selector = "item"',
        ));

        $props_sprav = PropsSprav::getPropsListListitem($model_rubriks_props, $prop_types_params_row, $parent_ps_id);

        if (count($props_sprav) > 0)
        {
            foreach ($props_sprav as $pkey=>$pval)
            {
                ?>
                <span><?= $pval->value;?></span>
            <?
            }
        }

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