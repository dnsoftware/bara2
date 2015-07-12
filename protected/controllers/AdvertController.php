<?php

class AdvertController extends Controller
{
    // В режиме редактирования аналог Yii::app()->session['addfield']
    public $addfield_array = array();

	public function actionAddadvert()
	{
        $rub_array = Rubriks::get_rublist();
//    deb::dump(Yii::app()->session['addfield']);

        $n_id=0;
        if(isset($_GET['n_id']))
        {
            $n_id = intval($_GET['n_id']);
        }

        if(Yii::app()->controller->action->id != 'advert_edit')
        {
            $model = new Notice();
            $mainblock = array();
            if(isset(Yii::app()->session['mainblock']))
            {
                $mainblock = Yii::app()->session['mainblock'];
            }
        }
        else
        {
            $model = Notice::model()->findByPk($n_id);
            $mainblock = $model->attributes;
            //deb::dump($mainblock);
        }


        $country_array = Countries::getCountryList();

        $this->render('addadvert', array('rub_array'=>$rub_array, 'model'=>$model,
                    'mainblock'=>$mainblock, 'n_id'=>$n_id, 'country_array'=>$country_array));

    }

    public function getMainblockValue($model, $field_name)
    {

        // Режим добавления, данные берем из сессии (если там есть)
        if($model->n_id == 0 || $model->n_id == null)
        {
            $value = '';
            if(isset(Yii::app()->session['mainblock'][$field_name]))
            {
                $value = Yii::app()->session['mainblock'][$field_name];
            }

            return $value;
        }
        // Режим редактирования
        else
        {
            return $model->$field_name;
        }

    }

    public function getAddfieldValue($n_id, $field_name, $rubriks_props_model)
    {

        // Режим добавления, данные берем из сессии (если там есть)
        if($n_id == 0)
        {
            $value = '';
            if(isset(Yii::app()->session['addfield'][$field_name]))
            {
                $value = Yii::app()->session['addfield'][$field_name];
            }
//echo($value);
        }
        else
        {
            //deb::dump($rubriks_props_model);
            $prop = NoticeProps::model()->findAllByAttributes(array('n_id'=>$n_id, 'rp_id'=>$rubriks_props_model->rp_id));
            $value = '';
            //deb::dump($rubriks_props_model->vibor_type);
            switch($rubriks_props_model->vibor_type)
            {
                case "autoload_with_listitem":
                case "selector":
                case "listitem":
                case "radio":
                    $value = $prop[0]->ps_id;
                break;

                case "checkbox":
                    foreach($prop as $ckey=>$cval)
                    {
                        $value[$cval->ps_id] = 'on';
                    }
                break;

                case "string":
                    if(trim($prop[0]->hand_input_value) != '')
                    {
                        $value = array('ps_id'=>$prop[0]->ps_id, 'hand_input_value'=>$prop[0]->hand_input_value);
                    }
                break;

                case "photoblock":
                    if(trim($prop[0]->hand_input_value) != '')
                    {
                        $value = array('ps_id'=>$prop[0]->ps_id, 'hand_input_value'=>$prop[0]->hand_input_value);
                    }
                break;
            }

            //echo($value);
        }

        return $value;

    }

    public function getParentPsId($model_notice, $parent_field_id, $rubriks_props_model)
    {
        $addfield_array = array();
        $value = 0;

        // Режим добавления, данные берем из сессии (если там есть)
        if($model_notice->n_id == 0 || $model_notice->n_id == null)
        {
            if(isset(Yii::app()->session['addfield'][$parent_field_id]))
            {
                $addfield_array = Yii::app()->session['addfield'];
            }
        }
        // Режим редактирования, данные берем из массива $this addfield_array
        else
        {
            if(isset($this->addfield_array[$parent_field_id]))
            {
                $addfield_array = $this->addfield_array;
            }

        }
?>
    //console.log('<?= json_encode($parent_field_id);?>');
<?
        // Ищем родителя по полю selector в таблице rubriks_props
        $pub_props_parent_model = RubriksProps::model()->findByAttributes(array('selector'=>$parent_field_id));

        switch($pub_props_parent_model->vibor_type)
        {
            case "autoload_with_listitem":
            case "selector":
            case "listitem":
            case "radio":
                $value = $addfield_array[$parent_field_id];
                break;

            case "checkbox":
                // $parent_field_id получается что может быть несколько,
                // если будет необходимость - подумать над этим
                break;

            case "string":
                $value = $addfield_array[$parent_field_id]['ps_id'];
                break;

            case "photoblock":
                $value = $addfield_array[$parent_field_id]['ps_id'];
            break;

        }

        /*
        switch($rubriks_props_model->vibor_type)
        {
            case "autoload_with_listitem":
            case "selector":
            case "listitem":
            case "radio":
                $value = $addfield_array[$parent_field_id];
            break;

            case "checkbox":
                // $parent_field_id получается что может быть несколько,
                // если будет необходимость - подумать над этим
            break;

            case "string":
                $value = $addfield_array[$parent_field_id]['ps_id'];
            break;

            case "photoblock":
                // Заглушка
            break;

        }
        */

        return $value;

    }

    public function getSelectedAttr($value, $list_value)
    {
        if($value == $list_value)
        {
            return " selected ";
        }
        else
        {
            return " ";
        }
    }

    public function getCheckedAttr($value, $checked_array)
    {
        if(isset($checked_array[$value]))
        {
            return " checked ";
        }
        else
        {
            return " ";
        }
    }

    public function getRadioCheckedAttr($value, $list_value)
    {
        if($value == $list_value)
        {
            return " checked ";
        }
        else
        {
            return " ";
        }
    }

    public function actionGetRubriksProps()
    {
        $r_id=intval($_POST['r_id']);
        $model= new RubriksProps();

        $n_id=0;
        if(isset($_POST['n_id']))
        {
            $n_id = intval($_POST['n_id']);
        }

        if(!$model_notice = Notice::model()->findByPk($n_id))
        {
            $model_notice = new Notice();
        }

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

        /////////// Для режима редактирования получаем свойства и их значения
        if($n_id > 0)
        {
            $this->addfield_array = array();

            $props_relate = RubriksProps::model()->with('notice_props')->findAll(array(
                'select'=>'*',
                'condition'=>'r_id='.$r_id . " AND n_id=".$n_id,
                'order'=>'t.hierarhy_tag DESC, t.hierarhy_level ASC, t.display_sort, t.rp_id'
            ));

            foreach($props_relate as $pkey=>$pval)
            {
                //deb::dump($pval);
                //deb::dump('----------------------');
                switch($pval->vibor_type)
                {
                    case "autoload_with_listitem":
                    case "selector":
                    case "listitem":
                    case "radio":
                        $this->addfield_array[$pval->selector] = $pval->notice_props[0]->ps_id;
                    break;

                    case "checkbox":
                        foreach($pval->notice_props as $nkey=>$nval)
                        {
                            $this->addfield_array[$pval->selector][$nval->ps_id] = 'on';
                        }
                    break;

                    case "string":
                        $this->addfield_array[$pval->selector]['ps_id'] = $pval->notice_props[0]->ps_id;
                        $this->addfield_array[$pval->selector]['hand_input_value'] = $pval->notice_props[0]->hand_input_value;
                    break;

                    case "photoblock":
                        $this->addfield_array[$pval->selector]['ps_id'] = $pval->notice_props[0]->ps_id;
                        $this->addfield_array[$pval->selector]['hand_input_value'] = $pval->notice_props[0]->hand_input_value;
                    break;

                }
            }
            //deb::dump($this->addfield_array);
        }
        ////////////////// КОНЕЦ для режима редактирования

        //deb::dump($props_hierarhy);

        foreach ($model_items as $mkey=>$mval)
        {
            // Расскомментировать!!!
            $block_display = 'block';

            $block_display = 'none';
            if( ($mval->hierarhy_tag == 1 && ($mval->hierarhy_level == 1 || $mval->hierarhy_level == 2)) || $mval->hierarhy_tag == 0 )
            {
                $block_display = 'block';
            }

        ?>
         <!--<div id="div_<?= $mval->selector;?>" style="display: <?= $block_display;?>;">-->
         <div class="prop_block" id="div_<?= $mval->selector;?>" style="display: <?= $block_display;?>;">
             <div class="add_hideselector"><?= $mval->selector;?></div>
             <div class="add_hidevibortype"><?= $mval->vibor_type;?></div>

             <table border="1">
             <tr>
             <td class="tbl-prop-name">
                <div class="prop_name"><?= $mval->name;?>:</div>
             </td>
             <td>

             <div class="input-error-prop" id="input-error-prop-<?= $mval->selector;?>">
        <?
         switch($mval->vibor_type)
         {
             case "autoload_with_listitem":
                 ?>
                 <input type="text" name="<?= $mval->selector;?>-display" id="<?= $mval->selector;?>-display" inputfield="<?= $mval->selector;?>">
                 <span class="addnot-field-selected" id="<?= $mval->selector;?>-span" inputfield="<?= $mval->selector;?>"></span>
                 <input class="add_hideinput" style="width: 30px; background-color: #ddd;" readonly type="text" name="addfield[<?= $mval->selector;?>]" id="<?= $mval->selector;?>"  prop_id="<?= $mval->selector;?>" value="<?= $this->getAddfieldValue($n_id, $mval->selector, $mval);?>">

                 <?
                 // id поля формы <input> в которое заносится выбранное значение
                 // Возможно это лишнее, т.к. все id полей вроде как совпадают с selector rubriks_props
                 $props_hierarhy[$mval->selector]['field_value_id'] = $mval->selector;

                 ?>
                 <div id="div_<?= $mval->selector;?>_list"></div>
                 <?

             break;

             case "selector":
                 $props_hierarhy[$mval->selector]['field_value_id'] = $mval->selector;
             break;

             case "listitem":
                 ?>
                 <span class="addnot-field-selected" id="<?= $mval->selector;?>-span" inputfield="<?= $mval->selector;?>"></span>
                <input class="add_hideinput" style="width: 30px; background-color: #ddd;" readonly type="text" name="addfield[<?= $mval->selector;?>]" id="<?= $mval->selector;?>" prop_id="<?= $mval->selector;?>" value="<?= $this->getAddfieldValue($n_id, $mval->selector, $mval);?>">

                 <div id="div_<?= $mval->selector;?>_list"></div>
                 <?
                 $props_hierarhy[$mval->selector]['field_value_id'] = $mval->selector;
             break;

             case "checkbox":
             ?>
                 <div id="div_<?= $mval->selector;?>_list"></div>
             <?
             break;

             case "radio":
                 ?>
                 <div id="div_<?= $mval->selector;?>_list"></div>
                 <?
             break;

             case "string":
                 ?>
                 <div id="div_<?= $mval->selector;?>_field"></div>
                 <?
             break;

             case "photoblock":
                 //deb::dump($model);
             ?>
                <div id="div_<?= $mval->selector;?>_photoblock"></div>
             <?
             break;
         }
        ?>
             </div>
             <div class="input-error-prop-msg"></div>

             </td>
             </tr>
             </table>

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
            f<?= $vkey;?>: function(field_id, parent_field_id, n_id, parent_ps_id) {
                get_props_list_<?= $vkey;?>(field_id, parent_field_id, n_id, parent_ps_id);
            },
        <?
        }
        ?>
        };

        // Подготовка к загрузке блоков свойств, счетчик в ноль, отображаем индикатор загрузки и скрываем блок со свойствами
        props_load_stack_count = 0;
        $('#div_ajax_loader_icon').css('display', 'block');
        $('#div_props').css('display', 'none');
        // End Подготовка

        <?
        foreach ($model_items as $mkey=>$mval)
        {
            $field_id = $mval->selector;
            $parent_field_id = '';
            if($mval->parent_id > 0)
            {
                $parent_field_id = $model_items_array[$mval->parent_id]->selector;
            }

            $parent_ps_id = intval($this->getParentPsId($model_notice, $parent_field_id, $mval));

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
        //alert('<?= $parent_ps_id;?>');
            get_props_list_<?= $mval->vibor_type;?>('<?= $field_id;?>', '<?= $parent_field_id;?>', <?= $n_id;?>, <?= $parent_ps_id;?>);

        <?
        }
        ?>

        // Проверка все ли свойства загрузились на страницу
        var props_load_stack_count_timer = setInterval(function() {
            console.log(props_load_stack_count);
            if(props_load_stack_count == 0)
            {
                clearTimeout(props_load_stack_count_timer);
                $('#div_ajax_loader_icon').css('display', 'none');
                $('#div_props').css('display', 'block');
            }
        }, 1000);
        // End Проверка


        $('.addnot-field-selected').click(
        function()
        {
            $(this).css('display', 'none');

            $('#'+$(this).attr('inputfield')).val('');
            $('#'+$(this).attr('inputfield')+'-display').val('');

            $('#'+$(this).attr('inputfield')+'-display').css('display', 'inline');
            $('#div_'+$(this).attr('inputfield')+'_list').css('display', 'block');

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
        $n_id = 0;
        if(isset($_POST['n_id']))
        {
            $n_id = intval($_POST['n_id']);
        }
        if(!$model_notice = Notice::model()->findByPk($n_id))
        {
            $model_notice = new Notice();
        }

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

        $currvalue = $this->getAddfieldValue($n_id, $field_id, $model_rubriks_props);
        ?>
        <div class="add_hideselector"><?= $model_rubriks_props->selector;?></div>
        <div class="add_hidevibortype"><?= $model_rubriks_props->vibor_type;?></div>

        <table border="1">
        <tr>
        <td class="tbl-prop-name">
            <div class="prop_name"><?= $model_rubriks_props->name;?></div>
        </td>
        <td>

        <div class="input-error-prop" id="input-error-prop-<?= $field_id;?>">
        <select class="input-proplist-selector" name="addfield[<?= $field_id;?>]" id="<?= $field_id;?>" prop_id="<?= $field_id;?>">
            <option <?= $this->getSelectedAttr($currvalue, "");?> value=""></option>
        <?

        $props_sprav = PropsSprav::getPropsListSelector($model_rubriks_props, $prop_types_params_row, $parent_ps_id);

        if (count($props_sprav) > 0)
        {
            foreach ($props_sprav as $pkey=>$pval)
            {
                ?>
                <option <?= $this->getSelectedAttr($currvalue, $pval->ps_id);?> value="<?= $pval->ps_id;?>"><?= $pval->value;?></option>
                <?
            }
        }

        ?>
        </select>
        </div>
        <div class="input-error-prop-msg"></div>

        </td>
        </tr>
        </table>


        <script>
        if($('#<?= $field_id;?>').val() != '')
        {
            DisplayAfterLoad('<?= $field_id;?>');
        }

        // При смене значения - обновляем данные зависимых свойств
        $('#<?= $field_id;?>').change(
        function()
        {
            ChangeRelateProps($(this), <?= $n_id;?>);
        }
        );
        </script>
    <?

    }


    public function actionGetpropslist_listitem()
    {
        $field_id = $_POST['field_id'];
        //deb::dump($_POST);
        $parent_field_id = $_POST['parent_field_id'];
        $parent_ps_id = intval($_POST['parent_ps_id']);

        $n_id = 0;
        if(isset($_POST['n_id']))
        {
            $n_id = intval($_POST['n_id']);
        }
        if(!$model_notice = Notice::model()->findByPk($n_id))
        {
            $model_notice = new Notice();
        }
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

        // Сокрытие блока где нет подчиненных свойств (если стоит тег hide_if_no_elems_tag)
        $this->HideBlockIfNoElems($field_id, $parent_field_id, $parent_ps_id, $model_notice, $model_rubriks_props);

        $props_sprav = PropsSprav::getPropsListListitem($model_rubriks_props, $prop_types_params_row, $parent_ps_id);

        if (count($props_sprav) > 0)
        {
            foreach ($props_sprav as $pkey=>$pval)
            {
            ?>
                <span class="radio-listitem rl-<?= $field_id;?>" itemvalue="<?= $pval->ps_id;?>"><?= $pval->value;?></span>
            <?
            }
        }
        ?>
        <script>
            if($('#<?= $field_id;?>').val() != '')
            {
                DisplayAfterLoad('<?= $field_id;?>');
            }

            $('#<?= $field_id;?>-display').css('display', 'block');
            $('#div_<?= $field_id;?>_list').css('display', 'block');
            if($('#<?= $field_id;?>').val()>0)
            {
                $('#<?= $field_id;?>-span').html($('.rl-<?= $field_id;?>[itemvalue = '+$('#<?= $field_id;?>').val()+']').html());
            }

            $('.rl-<?= $field_id;?>').click(
                function()
                {
                    oldval = $('#<?= $field_id;?>').val();
                    $('#<?= $field_id;?>').val($(this).attr('itemvalue'));
                    $('#<?= $field_id;?>-span').css('display', 'inline');
                    $('#<?= $field_id;?>-span').html($(this).html());

                    $('#<?= $field_id;?>-display').css('display', 'none');
                    $('#<?= $field_id;?>-display').val($(this).html());

                    $('#div_<?= $field_id;?>_list').css('display', 'none');

                    if($('#<?= $field_id;?>').val() != oldval)
                    {
                        ChangeRelateProps($('#<?= $field_id;?>'), <?= $n_id;?>);
                    }
                }
            );
        </script>
        <?

    }

    public function actionGetpropslist_checkbox()
    {
        $field_id = $_POST['field_id'];
        //echo $field_id;
        $parent_field_id = $_POST['parent_field_id'];
        $parent_ps_id = intval($_POST['parent_ps_id']);

        $n_id = 0;
        if(isset($_POST['n_id']))
        {
            $n_id = intval($_POST['n_id']);
        }

        if(!$model_notice = Notice::model()->findByPk($n_id))
        {
            $model_notice = new Notice();
        }

        $model_rubriks_props = RubriksProps::model()->find(
            array(
                'condition'=>'selector = :selector',
                'params'=>array(':selector'=>$field_id),
            )
        );

        $checked_array = $this->getAddfieldValue($n_id, $field_id, $model_rubriks_props);

        $prop_types_params_row = PropTypesParams::model()->find(array(
            'select'=>'*',
            'condition'=>'type_id = "'.$model_rubriks_props->type_id.'" AND selector = "item"',
        ));

        // Сокрытие блока где нет подчиненных свойств (если стоит тег hide_if_no_elems_tag)
        $this->HideBlockIfNoElems($field_id, $parent_field_id, $parent_ps_id, $model_notice, $model_rubriks_props);

        $props_sprav = PropsSprav::getPropsListListitem($model_rubriks_props, $prop_types_params_row, $parent_ps_id);

        if (count($props_sprav) > 0)
        {
            foreach ($props_sprav as $pkey=>$pval)
            {
                //deb::dump($pval);
            ?>
                <input style="" class="<?= $model_rubriks_props->selector;?>" type="checkbox" name="addfield[<?= $model_rubriks_props->selector;?>][<?= $pval->ps_id;?>]" id="<?= $model_rubriks_props->selector;?>-<?= $pval->ps_id;?>" prop_id="<?= $model_rubriks_props->selector;?>" <?= $this->getCheckedAttr($pval->ps_id, $checked_array);?>> <?= $pval->value;?>
            <?
            }
        }

        ?>

        <script>
            checkboxes = $('.<?= $model_rubriks_props->selector;?>');
            checked_tag = 0;
            $.each(checkboxes, function(mkey, mval)
            {
                if($(mval).attr('checked') !== undefined)
                {
                    checked_tag = 1;
                }
            });

            if(checked_tag == 1)
            {
                DisplayAfterLoad('<?= $field_id;?>');
            }
        </script>
    <?

    }


    public function actionGetpropslist_radio()
    {
        $field_id = $_POST['field_id'];
        //echo $field_id;
        $parent_field_id = $_POST['parent_field_id'];
        $parent_ps_id = intval($_POST['parent_ps_id']);

        $n_id = 0;
        if(isset($_POST['n_id']))
        {
            $n_id = intval($_POST['n_id']);
        }

        if(!$model_notice = Notice::model()->findByPk($n_id))
        {
            $model_notice = new Notice();
        }

        $model_rubriks_props = RubriksProps::model()->find(
            array(
                'condition'=>'selector = :selector',
                'params'=>array(':selector'=>$field_id),
            )
        );

        $value = $this->getAddfieldValue($n_id, $field_id, $model_rubriks_props);

        $prop_types_params_row = PropTypesParams::model()->find(array(
            'select'=>'*',
            'condition'=>'type_id = "'.$model_rubriks_props->type_id.'" AND selector = "item"',
        ));

        // Сокрытие блока где нет подчиненных свойств (если стоит тег hide_if_no_elems_tag)
        $this->HideBlockIfNoElems($field_id, $parent_field_id, $parent_ps_id, $model_notice, $model_rubriks_props);

        $props_sprav = PropsSprav::getPropsListListitem($model_rubriks_props, $prop_types_params_row, $parent_ps_id);

        if (count($props_sprav) > 0)
        {
            foreach ($props_sprav as $pkey=>$pval)
            {
            ?>
                <?= $pval->value;?> <input style="" <?= $this->getRadioCheckedAttr($value, $pval->ps_id);?> type="radio" class="<?= $model_rubriks_props->selector;?>" name="addfield[<?= $model_rubriks_props->selector;?>]" id="<?= $model_rubriks_props->selector;?>-<?= $pval->ps_id;?>" prop_id="<?= $model_rubriks_props->selector;?>" value="<?= $pval->ps_id;?>">
            <?
            }
        }

        ?>

        <script>
            radios = $('.<?= $model_rubriks_props->selector;?>');
            checked_tag = 0;
            $.each(radios, function(mkey, mval)
            {
                if($(mval).attr('checked') !== undefined)
                {
                    checked_tag = 1;
                }
            });

            if(checked_tag == 1)
            {
                DisplayAfterLoad('<?= $field_id;?>');
            }

        </script>
    <?

    }

    public function actionGetpropslist_string()
    {
        $field_id = $_POST['field_id'];
        //echo $field_id;
        $parent_field_id = $_POST['parent_field_id'];
        $parent_ps_id = intval($_POST['parent_ps_id']);

        $n_id = 0;
        if(isset($_POST['n_id']))
        {
            $n_id = intval($_POST['n_id']);
        }

        if(!$model_notice = Notice::model()->findByPk($n_id))
        {
            $model_notice = new Notice();
        }

        $model_rubriks_props = RubriksProps::model()->find(
            array(
                'condition'=>'selector = :selector',
                'params'=>array(':selector'=>$field_id),
            )
        );

        $value = $this->getAddfieldValue($n_id, $field_id, $model_rubriks_props);
//deb::dump($value);
//deb::dump($model_rubriks_props);
        $prop_types_params_row = PropTypesParams::model()->find(array(
            'select'=>'*',
            'condition'=>'type_id = "'.$model_rubriks_props->type_id.'" AND selector = "string"',
        ));
//deb::dump($prop_types_params_row);

        // Сокрытие блока где нет подчиненных свойств (если стоит тег hide_if_no_elems_tag)
        $this->HideBlockIfNoElems($field_id, $parent_field_id, $parent_ps_id, $model_notice, $model_rubriks_props);

        $props_sprav = PropsSprav::getPropsListListitem($model_rubriks_props, $prop_types_params_row, $parent_ps_id);
        if (count($props_sprav) > 0)
        {
            // По идее в $props_sprav только один элемент. Цикл тем не менее оставил.
            // Если получится ситуация, что будет больше одного элемента - надо пересмотреть наименования
            // атрибутов "name" и "id"
            foreach ($props_sprav as $pkey=>$pval)
            {
                $value_hand = '';
                if(isset($value['hand_input_value']))
                {
                    $value_hand = $value['hand_input_value'];
                }
                ?>
                <?= $pval->value;?> <input class="add_hideinput" style="width: 30px; background-color: #ddd;" readonly type="text" name="addfield[<?= $model_rubriks_props->selector;?>][ps_id]" id="<?= $model_rubriks_props->selector;?>-<?= $pval->ps_id;?>" value="<?= $pval->ps_id;?>">

                <input style="" type="text" name="addfield[<?= $model_rubriks_props->selector;?>][hand_input_value]" id="<?= $model_rubriks_props->selector;?>" prop_id="<?= $model_rubriks_props->selector;?>" value="<?= htmlspecialchars($value_hand, ENT_COMPAT);?>">

            <?
            }
        }

        ?>

        <script>
            if($('#<?= $model_rubriks_props->selector;?>').val() != '')
            {
                DisplayAfterLoad('<?= $field_id;?>');
            }

        </script>
    <?

    }


    public function actionGetpropslist_photoblock()
    {
        $field_id = $_POST['field_id'];
        $parent_field_id = $_POST['parent_field_id'];
        $parent_ps_id = intval($_POST['parent_ps_id']);
//deb::dump($field_id);
//deb::dump($parent_field_id);
//deb::dump($parent_ps_id);

        $n_id = 0;
        if(isset($_POST['n_id']))
        {
            $n_id = intval($_POST['n_id']);
        }

        if(!$model_notice = Notice::model()->findByPk($n_id))
        {
            $model_notice = new Notice();
        }

        $model_rubriks_props = RubriksProps::model()->find(array(
            'select'=>'*',
            'condition'=>'selector=:rp_id',
            'params'=>array(':rp_id'=>$field_id)
        ));

        $fieldvalue = $this->getAddfieldValue($n_id, $field_id, $model_rubriks_props);
//deb::dump($n_id);
        $uploadfiles_array = Notice::getImageArray(isset($fieldvalue['hand_input_value']) ? $fieldvalue['hand_input_value'] : '');
//deb::dump($uploadfiles_array);
        $uploadmainfile = $uploadfiles_array[0];

        // Переносим фото в директорию /tmp для работы в режиме редактирования
        if($n_id > 0)
        {
            foreach ($uploadfiles_array as $fkey=>$fval)
            {
                if(@copy ( $_SERVER['DOCUMENT_ROOT']."/photos/".$fval, $_SERVER['DOCUMENT_ROOT']."/tmp/".$fval ))
                {
                    // Может нужно, а может и нет
                }
            }
        }


        $prop_types_params_row = PropTypesParams::model()->find(array(
            'select'=>'*',
            'condition'=>'type_id = "'.$model_rubriks_props->type_id.'" AND selector = "item"',
        ));

        // Сокрытие блока где нет подчиненных свойств (если стоит тег hide_if_no_elems_tag)
        $this->HideBlockIfNoElems($field_id, $parent_field_id, $parent_ps_id, $model_notice, $model_rubriks_props);

        $props_sprav = PropsSprav::getPropsListListitem($model_rubriks_props, $prop_types_params_row, $parent_ps_id);
        ?>

        <input class="add_hideinput" style="width: 30px; background-color: #ddd;" readonly type="text" name="addfield[<?= $field_id;?>][ps_id]" id="<?= $field_id;?>-<?= $props_sprav[0]->ps_id;?>" value="<?= $props_sprav[0]->ps_id;?>">

        <input type="text" class="upload_photo_field" name="addfield[<?= $field_id;?>][hand_input_value]" id="<?= $field_id;?>" prop_id="<?= $field_id;?>" value="<?= isset($fieldvalue['hand_input_value']) ? $fieldvalue['hand_input_value'] : '';?>" style="display: none; width: 1000px;">

        <div class="form-row">

            <div style="">
                <div id="fileuploader">Upload</div>
            </div>

            <div id="fileuploader_list" style="">
                <?

                if(count($uploadfiles_array) > 0)
                {
                    foreach($uploadfiles_array as $ukey=>$uval)
                    {
                        $imageclass = "otherfileborder";
                        if($uval == $uploadmainfile)
                        {
                            $imageclass = "mainfileborder";
                        }
                        ?>

                        <div class="ajax-file-upload-statusbar" id="oldload_<?= md5($uval);?>" style="width: 500px;">

                            <div class="ajax-file-upload-image">
                                <img src="/tmp/<?= $uval;?>" md5id="<?= md5($uval);?>" class="<?= $imageclass;?>" fileload_id="<?= $uval;?>" style="height: 80px; width: auto;" height="80" width="0">
                            </div>
                            <div class="ajax-file-upload-filename">
                                <? //тут название файла ?>
                            </div>
                            <div class="ajax-file-upload-progress" style="">
                                <div style="width: 100%;" class="ajax-file-upload-bar ajax-file-upload-<?= md5($uval);?>"></div>
                            </div>
                            <div class="ajax-file-upload-red ajax-file-upload-abort ajax-file-upload-<?= md5($uval);?>" style="display: none;">Abort</div>

                            <div class="ajax-file-upload-red ajax-file-upload-cancel ajax-file-upload-<?= md5($uval);?>" style="display: none;">Cancel</div>

                            <div class="ajax-file-upload-green" style="display: none;">Done</div>

                            <div class="ajax-file-upload-green" style="display: none;">Download</div>

                            <div class="ajax-file-upload-red old_load_delete" delfile="<?= $uval;?>" style="">Delete</div>

                        </div>
                    <?
                    }
                }
                ?>

            </div>


        </div>

        <script>

            field_id = '<?= $field_id;?>';

            if($('#<?= $field_id;?>').val() != '')
            {
                //alert($('#<?= $field_id;?>').val());
                DisplayAfterLoad('<?= $field_id;?>');
            }


            $('.otherfileborder, .mainfileborder').click(
                function()
                {
                    $('.mainfileborder').attr('class', 'otherfileborder');
                    $(this).attr('class', 'mainfileborder');
                    setMainfileInInput($(this).attr('fileload_id'));
                }
            );


            // Формирование массива из строки с именами файлов и главного файла
            function photosStrToArray(photos_str)
            {
                var photos = [];
                if(photos_str != '')
                {
                    photos = photos_str.split(';');
                    photos.splice(photos.length-1);
                }

                return photos;
            }

            // Формирование строки из массива с именами файлов и главного файла
            function arrayPhotosToStr(photos_array)
            {
                return photos_array.join(';')+';';
            }

            // Установка названия главного фото на первое место в поле ввода
            function setMainfileInInput(mainfile)
            {
                upload_photo_field_str = $('.upload_photo_field').val();
                upload_photo_field_str = mainfile + ';' + upload_photo_field_str.replace(mainfile+';', "");
                $('.upload_photo_field').val(upload_photo_field_str);
            }

            // Пометка красной рамкой главного фото по его имени файла
            function setMainfileBorder(mainfile)
            {

            }

            // Удаление имени удаленного файла из строки в поле ввода
            function deletePhotofileFromStr(deleted_file)
            {
                upload_photo_field_str = $('.upload_photo_field').val();
                upload_photo_field_str = upload_photo_field_str.replace(deleted_file+';', "");
                $('.upload_photo_field').val(upload_photo_field_str);
            }

            // Удаление подстроки с именем удаленного файла и вычисление нового заглавного изображения
            function changeFileListAfterDelete(deleted_file)
            {
                //alert(deleted_file);
                deletePhotofileFromStr(deleted_file);

                nextmain = $('.otherfileborder').first();
                if($('.otherfileborder').length == 0)
                {
                    nextmain = $('.mainfileborder').first();
                }
                nextmain.attr('class', 'mainfileborder');
                if(nextmain.attr('fileload_id') !== undefined)
                {
                    setMainfileInInput(nextmain.attr('fileload_id'));
                }
            }

            $("#fileuploader").uploadFile({
                url:"<?= Yii::app()->createUrl('advert/upload');?>",
                fileName:"myfile",
                multiple:true,
                showDelete:true,
                showDone: false,
                returnType:"json",
                allowedTypes:"jpg,png,gif,jpeg",
                maxFileCount:5,
                showFileCounter: false,
                onSuccess:function(files,data,xhr)
                {
                    //files: list of files
                    //data: response from server
                    //xhr : jquer xhr object
                    //alert(files[0]);
                    $('.upload_photo_field').val($('.upload_photo_field').val()+data[0]+';');
                    image = $('[md5id = '+ $.md5(files[0])+']');
                    image.attr('fileload_id', data[0]);
                    photos_array = photosStrToArray($('.upload_photo_field').val());
                    console.log(photos_array);
                    if(photos_array.length == 1)
                    {
                        image.attr('class', 'mainfileborder');
                    }

                    image.click(
                        function()
                        {
                            $('.mainfileborder').attr('class', 'otherfileborder');
                            $(this).attr('class', 'mainfileborder');
                            setMainfileInInput($(this).attr('fileload_id'));
                        }
                    );
                },

                deleteCallback: function (data, pd) {
                    //console.log(data);

                    for (var i = 0; i < data.length; i++) {
                        $.post("<?= Yii::app()->createUrl('advert/uploaddelete');?>", {op: "delete",name: data[i], field_id: field_id},
                            function (resp,textStatus, jqXHR) {
                                changeFileListAfterDelete(data[0]);
                            });
                    }
                    pd.statusbar.hide(); //You choice.

                }
            });


            $('.old_load_delete').click(function ()
            {
                delfile = $(this).attr('delfile');
                delfile_id = $.md5($(this).attr('delfile'));

                $.ajax({
                    type: 'POST',
                    url: '<?= Yii::app()->createUrl('advert/uploaddelete');?>',
                    data: 'op=delete&name='+delfile+'&field_id='+field_id,
                    success: function(msg){
                        $('#oldload_'+delfile_id).remove();
                        changeFileListAfterDelete(delfile);
                    }
                });
            });



        </script>
    <?
    }

    public function HideBlockIfNoElems($field_id, $parent_field_id, $parent_ps_id, $model_notice, $model_rubriks_props)
    {
        /*************** Блок сокрытия блока если нет ни одного зависимого элемента... ****************/
        if($parent_field_id != '')   // Если блок зависим от родителя
        {
            if($model_rubriks_props->hide_if_no_elems_tag == 1 && $model_rubriks_props->require_prop_tag == 0)
            {
                $prop = PropsSprav::model()->findByPk($parent_ps_id);
    //deb::dump($model_rubriks_props);
                if($prop != null)   // Если родитель выбран
                {
                    $props_sprav_records = $prop->childs(array('condition'=>'rp_id = :rp_id',
                        'params'=>array(':rp_id'=>$model_rubriks_props->rp_id)));

                    if(count($props_sprav_records) == 0 )    // если нет зависимых элементов
                    {
                        ?>
                        <script>
                            $('#div_<?= $field_id;?>').css('display', 'none');
                        </script>
                    <?
                    }
                }
            }
        }
        /****************** Конец Блок сокрытия**********************/
    }


    // Загрузка файлов
    public function actionUpload()
    {
        $output_dir = $_SERVER['DOCUMENT_ROOT']."/tmp/";
        if(isset($_FILES["myfile"]))
        {
            $ret = array();

            $error =$_FILES["myfile"]["error"];
            //You need to handle  both cases
            //If Any browser does not support serializing of multiple files using FormData()
            if(!is_array($_FILES["myfile"]["name"])) //single file
            {
                $fileName = md5(microtime()).mt_rand(0, mt_getrandmax()).".".strtolower(pathinfo($_FILES["myfile"]["name"], PATHINFO_EXTENSION));
                move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir.$fileName);
                $ret[]= $fileName;
            }
            else  //Multiple files, file[]
            {
                $fileCount = count($_FILES["myfile"]["name"]);
                for($i=0; $i < $fileCount; $i++)
                {
                    $fileName = md5(microtime().$i).mt_rand(0, mt_getrandmax()).".".strtolower(pathinfo($_FILES["myfile"]["name"][$i], PATHINFO_EXTENSION));
                    move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir.$fileName);
                    $ret[]= $fileName;
                }

            }
            echo json_encode($ret);
        }
    }

    // Удаление загруженных файлов
    public function actionUploadDelete()
    {
        $output_dir = $_SERVER['DOCUMENT_ROOT']."/tmp/";
        $field_id = $_POST['field_id'];
        $delfile = str_replace('../', '', $_POST['name']);
        unlink($output_dir.$delfile);

        $temp = Yii::app()->session['addfield'];
        $temp[$field_id] = str_replace($delfile.';', '', $temp[$field_id]);

        Yii::app()->session['addfield'] = $temp;

        echo json_encode($delfile);
    }

    // Проверка данных для  нового объявления
    public function actionAddnew()
    {
        $mainblock_array = array();
        if (isset($_POST['mainblock']))
        {
            Yii::app()->session['mainblock'] = $_POST['mainblock'];
            $mainblock_array = $_POST['mainblock'];
        }

        $addfield_array = array();
        if (isset($_POST['addfield']))
        {
            Yii::app()->session['addfield'] = $_POST['addfield'];
            $addfield_array = $_POST['addfield'];
        }

        if (!isset($_POST['mainblock']) || !isset($_POST['addfield']))
        {
            $return_array['status'] = 'error';
            $return_array['message'] = 'Данные в запросе отсутствуют';
            echo json_encode($return_array);

            return false;
        }

        $return_array = $this->CheckAndMakeNewData($mainblock_array, $addfield_array);

        if(count($return_array['errors_props']) == 0 && count($return_array['errors']) == 0)
        {
            $return_array['status'] = 'ok';
            $return_array['message'] = 'Все ок!';
        }
        else
        {
            $return_array['status'] = 'error';
            $return_array['message'] = 'Есть ошибки';
        }


        //$return_array['debugdata'] = $_POST['addfield'];

        echo json_encode($return_array);

    }


    // Сохранение
    public function actionSavenew()
    {
        $mainblock_array = array();
        if(isset(Yii::app()->session['mainblock']))
        {
            $mainblock_array = Yii::app()->session['mainblock'];
        }

        $addfield_array = array();
        if(isset(Yii::app()->session['addfield']))
        {
            $addfield_array = Yii::app()->session['addfield'];
        }

        $return_array = $this->CheckAndMakeNewData($mainblock_array, $addfield_array);
//deb::dump($return_array);

        $newnot_user_id = 0;
        if(Yii::app()->user->id > 0)
        {
            $newnot_user_id = Yii::app()->user->id;
        }
        else
        if (isset(Yii::app()->session['add_user_id']) && Yii::app()->session['add_user_id'] > 0)
        {
            $newnot_user_id = Yii::app()->session['add_user_id'];
        }
        else
        {
            echo "Ошибка! Пользователь не определен!";

            return false;
        }

        if(count($return_array['errors_props']) == 0 && count($return_array['errors']) == 0 )
        {
            //echo "Ура!";

            // Заносим данные в базу

            $newmodel = $this->MakeNoticeAttributes($mainblock_array);
            $newmodel->u_id = $newnot_user_id;


            //Формируем daynumber_id
            $optmodel = Options::model()->findByPk(1);
            $daycount_date_now = date("Ymd", time());
            if ($daycount_date_now != $optmodel->daycount_date)
            {
                $optmodel->daycount_date = $daycount_date_now;
                $optmodel->daycount_currcount = 1;
            }
            else
            {
                $optmodel->daycount_date = $daycount_date_now;
                $optmodel->daycount_currcount = $optmodel->daycount_currcount + 1;
            }
            $daycount_currcount_str = sprintf("%07d", $optmodel->daycount_currcount);
            $newmodel->daynumber_id = date("ymd", time()).$daycount_currcount_str;
            $optmodel->save();
            $newmodel->save();

            // Подготавливаем данные из свойств
            $this->AddPropsToDatabase($newmodel, $mainblock_array, $addfield_array);

            // Генерируем xml данные свойств
            self::PropsXmlGenerate($newmodel->n_id);

            $user_url = $this->createAbsoluteUrl('/usercab/adverts');
            $this->redirect($user_url);

        }
        else
        {
            echo "Ошибки!";
            deb::dump($return_array);
            foreach ($return_array['errors'] as $rkey=>$rval)
            {
                echo $rval."<br>";
            }
        }

    }


    // Сохранение отредактированного объявления
    public function actionSaveedit()
    {
        $mainblock_array = array();
        if (isset($_POST['mainblock']))
        {
            $mainblock_array = $_POST['mainblock'];
        }

        $addfield_array = array();
        if (isset($_POST['addfield']))
        {
            $addfield_array = $_POST['addfield'];
        }

        if (!isset($_POST['mainblock']) || !isset($_POST['addfield']))
        {
            $return_array['status'] = 'error';
            $return_array['message'] = 'Данные в запросе отсутствуют';
            echo json_encode($return_array);

            return false;
        }

        $return_array = $this->CheckAndMakeNewData($mainblock_array, $addfield_array);

        if(count($return_array['errors_props']) == 0 && count($return_array['errors']) == 0)
        {
            $newmodel = $this->MakeNoticeAttributes($mainblock_array);
            $newmodel->u_id = Yii::app()->user->id;
            $newmodel->save();

            // Получаем названия старых файлов фото, если есть
            $old_photoblock_prop = RubriksProps::model()->with('notice_props')->find(array(
                'select'=>'*',
                'condition'=>"t.r_id = ".$mainblock_array['r_id']." AND notice_props.n_id=".$mainblock_array['n_id']. " AND t.vibor_type = 'photoblock' "
            ));
            $params['old_photoblock_prop'] = $old_photoblock_prop;

            // Удаляем все старые свойства
            NoticeProps::model()->deleteAll(array(
                'condition'=>'n_id='.$newmodel->n_id
            ));

            // Подготавливаем данные из свойств
            $this->AddPropsToDatabase($newmodel, $mainblock_array, $addfield_array, $params);


            $return_array['status'] = 'ok';
            $return_array['message'] = 'Объявление отредактировано!';

            echo json_encode($return_array);

            return true;
        }
        else
        {
            $return_array['status'] = 'error';
            $return_array['message'] = 'Есть ошибки!';
            //$return_array['message'] = $_POST['mainblock']['n_id'];

            echo json_encode($return_array);

            return false;
        }


    }


    // Добавление набора свойств объявления в базу
    // $newmodel - модель записи объявления
    // $mainblock_array - данные объявления
    // $addfield_array - данные свойств
    public function AddPropsToDatabase($newmodel, $mainblock_array, $addfield_array, $params = array())
    {
        $rubrik_props = RubriksProps::getAllProps($mainblock_array['r_id']);

        foreach($rubrik_props as $rkey=>$rval)
        {
            if(isset($addfield_array[$rkey]))
            {
                switch($rval->vibor_type)
                {
                    case "autoload_with_listitem":
                    case "selector":
                    case "listitem":
                    case "radio":
                        if(intval($addfield_array[$rkey]) > 0)
                        {
                            $newprop = new NoticeProps();
                            $newprop->n_id = $newmodel->n_id;
                            $newprop->rp_id = $rval->rp_id;
                            $newprop->ps_id = $addfield_array[$rkey];
                            $newprop->save();
                            //deb::dump($newprop->errors);
                        }
                        break;

                    case "checkbox":
                        foreach($addfield_array[$rkey] as $ckey=>$cval)
                        {
                            $newprop = new NoticeProps();
                            $newprop->n_id = $newmodel->n_id;
                            $newprop->rp_id = $rval->rp_id;
                            $newprop->ps_id = $ckey;
                            $newprop->save();
                        }
                        break;

                    case "string":
                        if(trim($addfield_array[$rkey]['hand_input_value']) != '')
                        {
                            $newprop = new NoticeProps();
                            $newprop->n_id = $newmodel->n_id;
                            $newprop->rp_id = $rval->rp_id;
                            $newprop->ps_id = $addfield_array[$rkey]['ps_id'];
                            $newprop->hand_input_value = $addfield_array[$rkey]['hand_input_value'];
                            $newprop->save();
                        }
                        break;

                    case "photoblock":

                        $files_str = trim($addfield_array[$rkey]['hand_input_value']);
                        if($files_str != '')
                        {
                            if($files_str[strlen($files_str)-1] == ';')
                            {
                                $files_str = substr($files_str, 0, strlen($files_str)-1);
                            }

                            $files_array = explode(";", $files_str);
                            $files_assoc_array = array();
                            foreach ($files_array as $fkey=>$fval)
                            {
                                if(@copy ( $_SERVER['DOCUMENT_ROOT']."/tmp/".$fval, $_SERVER['DOCUMENT_ROOT']."/photos/".$fval ))
                                {
                                    unlink ( $_SERVER['DOCUMENT_ROOT']."/tmp/".$fval);
                                }

                                $files_assoc_array[$fval] = $fval;
                            }


                            $newprop = new NoticeProps();
                            $newprop->n_id = $newmodel->n_id;
                            $newprop->rp_id = $rval->rp_id;
                            $newprop->ps_id = $addfield_array[$rkey]['ps_id'];
                            $newprop->hand_input_value = $addfield_array[$rkey]['hand_input_value'];
                            $newprop->save();
                            //deb::dump($newprop->errors);
                        }


                        if(isset($params['old_photoblock_prop']->notice_props[0]))
                        {
                            $old_files_str = $params['old_photoblock_prop']->notice_props[0]->hand_input_value;
                            $image_array = Notice::getImageArray($old_files_str);

                            foreach ($image_array as $ikey=>$ival)
                            {
                                if(!isset($files_assoc_array[$ival]))
                                {
                                    //var_dump($ival);
                                    unlink ( $_SERVER['DOCUMENT_ROOT']."/photos/".$ival);
                                }
                            }
                        }

                        break;
                }
            }
        } // end foreach

    }


    // Генерация xml данных со значениями всех свойств объявления и сохранение их в запись с данными объявы
    public static function PropsXmlGenerate($n_id)
    {
        $notice = Notice::model()->findByPk($n_id);

        /*
        $rubriks_props = RubriksProps::model()->findAll(array(
            'select'=>'*',
            'condition'=>"r_id = ".$notice->r_id." ",
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
        ));

        $notice_props = NoticeProps::model()->findAllByAttributes(array('n_id'=>$n_id));
        */

        $notice_props = array();
        $connection=Yii::app()->db;
        $sql = "SELECT rp.rp_id, rp.name, rp.selector, rp.vibor_type, np.ps_id, np.hand_input_value,
                       ps.value
                        FROM
                        ". $connection->tablePrefix . "rubriks_props rp,
                        ". $connection->tablePrefix . "notice_props np,
                        ". $connection->tablePrefix . "props_sprav ps
                        WHERE
                        np.n_id = $n_id AND rp.rp_id = np.rp_id AND np.ps_id = ps.ps_id
                        ORDER BY
                        rp.hierarhy_tag DESC, rp.hierarhy_level ASC, rp.display_sort, rp.rp_id ";
        //deb::dump($sql);
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><body><block></block></body>');
        while(($row = $dataReader->read())!==false)
        {
            if(count($xml->block[0]->$row['selector']) == 0)
            {
                $props = $xml->block[0]->addChild($row['selector']);
            }
            else
            {
                $props = $xml->block[0]->$row['selector'];
            }

            $item = $props->addChild('item');
            foreach($row as $rkey=>$rval)
            {
                $item->addChild($rkey, $rval);
            }

            //$notice_props[] = $row;

        }

        //echo htmlspecialchars($xml->asXML());
        $notice->props_xml = $xml->asXML();
        $notice->save();

        //deb::dump($notice_props);

    }

    // Проверка и формирование данных для занесения в базу
    public function CheckAndMakeNewData($mainblock_array, $addfield_array)
    {
        $newmodel = $this->MakeNoticeAttributes($mainblock_array);

        $return_array = $this->CheckRequireNoticeProps($newmodel, $addfield_array);

        $return_array['errors'] = array();
        if(!$newmodel->validate())
        {
            $return_array['errors'] = $newmodel->getErrors();
        }

        return $return_array;

    }

    // Подготовка атрибутов модели объявления для проверки перед предварительным просмотром
    // и перед добавлением
    public function MakeNoticeAttributes($mainblock_array)
    {
        if($mainblock_array['n_id'] <= 0)
        {
            $newmodel = new Notice();
            $newmodel->date_add = time();
            $newmodel->active_tag = 1;
            $newmodel->verify_tag = 1;
            $newmodel->views_count = 0;
            $newmodel->moder_counted_tag = 0;
        }
        else
        {
            $newmodel = Notice::model()->findByPk($mainblock_array['n_id']);
        }

        $newmodel->attributes = $mainblock_array;

        $newmodel->date_lastedit = time();
        $expire_period = intval($mainblock_array['expire_period']);
        $newmodel->date_expire = $newmodel->date_add + $expire_period*86400;

        // Поиск дублей
        $control_string = $newmodel->client_name . $newmodel->client_email . $newmodel->client_phone . $newmodel->r_id . $newmodel->title . $newmodel->notice_type_id . $newmodel->notice_text;
        $newmodel->checksum = md5($control_string);
        // Доделать поиск дублей
        // ...


        if(Yii::app()->user->id <= 0)
        {
            $newmodel->u_id = 0;
        }
        else
        {
            $newmodel->u_id = Yii::app()->user->id;
        }

        return $newmodel;

    }


    // Проверка обязательных свойств перед подачей объявления
    // $newmodel - модель объявы
    //
    public function CheckRequireNoticeProps($newmodel, $addfield_array)
    {
        $return_array = array();

        // Блок проверки свойств
        $require_props = RubriksProps::getRequireProps(intval($newmodel->r_id));
        $return_array['errors_props'] = array();
        if(count($require_props) > 0)
        {
            foreach ($require_props as $rkey=>$rval)
            {
                if(!isset($addfield_array[$rkey]))
                {
                    $return_array['errors_props'][$rval['selector']] = 'Необходимо заполнить поле "' . $rval['name'] . '"';
                }
                else
                {
                    switch($rval->vibor_type)
                    {
                        case "autoload_with_listitem":
                        case "autoload":
                        case "listitem":
                        case "selector":
                        case "radio":
                        case "photoblock":
                            if(intval($addfield_array[$rkey]) <= 0)
                            {
                                $return_array['errors_props'][$rval['selector']] = 'Необходимо заполнить поле "' . $rval['name'] . '"';
                            }
                            break;

                        case "checkbox":
                            if(count($addfield_array[$rkey]) <= 0)
                            {
                                $return_array['errors_props'][$rval['selector']] = 'Необходимо заполнить поле "' . $rval['name'] . '"';
                            }
                            break;

                        case "string":
                            if(strlen(trim($addfield_array[$rkey]['hand_input_value'])) == 0)
                            {
                                $return_array['errors_props'][$rval['selector']] = 'Необходимо заполнить поле "' . $rval['name'] . '"';
                            }
                            break;

                    }
                }


            }

        }

        return $return_array;

    }

    // Предварительный просмотр и авторизация
    public function actionAddpreview()
    {
        Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/galleria/galleria-1.4.2.js');

        $mainblock = Yii::app()->session['mainblock'];
        $addfield = Yii::app()->session['addfield'];

        // Подготавливаем данные из основной части объявления
        $mainblock_data = array();
        $mainblock_data['country'] = Countries::model()->findByPk($mainblock['c_id']);
        $mainblock_data['region'] = Regions::model()->findByPk($mainblock['reg_id']);
        $mainblock_data['town'] = Towns::model()->findByPk($mainblock['t_id']);

        // Подготавливаем данные из свойств
        $rubrik_props = RubriksProps::getAllProps($mainblock['r_id']);
        $rubrik_props_rp_id = RubriksProps::getAllPropsRp_id($mainblock['r_id']);
//deb::dump($addfield);
//deb::dump($rubrik_props);
        $uploadfiles_array = array();

        $props_ids = array();
        $notice_props = array();
        $props_string_ids = array();    // Для ручного ввода данные берутся сразу из сессии (при предварительном просмотре)
                                        // или сразу из записи таблицы notice_props поля hand_input_value
        foreach($rubrik_props as $rkey=>$rval)
        {
            if(isset($addfield[$rkey]))
            {
                switch($rval->vibor_type)
                {
                    case "autoload_with_listitem":
                    case "selector":
                    case "listitem":
                    case "radio":
                        if(intval($addfield[$rkey]) > 0)
                        {
                            $props_ids[] = $addfield[$rkey];
                            $notice_props[$rval->rp_id] = $addfield[$rkey];
                        }
                    break;

                    case "checkbox":
                        foreach($addfield[$rkey] as $ckey=>$cval)
                        {
                            $props_ids[] = $ckey;
                            $props_string_ids[$ckey] = $ckey;
                            $notice_props[$rval->rp_id][$ckey] = $ckey;
                        }
                    break;

                    case "string":
                        if(trim($addfield[$rkey]['hand_input_value']) != '')
                        {
                            $props_ids[] = $addfield[$rkey]['ps_id'];
                            $notice_props[$rval->rp_id] = $addfield[$rkey]['hand_input_value'];
                        }
                    break;

                    case "photoblock":
                        if(trim($addfield[$rkey]['hand_input_value']) != '')
                        {
                            $uploadfiles_array = Notice::getImageArray($addfield[$rkey]['hand_input_value']);
                            //deb::dump($uploadfiles_array);
                        }
                    break;
                }
            }
        }

        $props_data = PropsSprav::getDataByIds($props_ids);
//deb::dump($notice_props);
        $addfield_data['notice_props'] = $notice_props;
        $addfield_data['rubrik_props'] = $rubrik_props;
        $addfield_data['rubrik_props_rp_id'] = $rubrik_props_rp_id;
        $addfield_data['props_data'] = $props_data;
        $addfield_data['props_string_ids']= $props_string_ids;

        $options = Options::getAllOptions();

    //deb::dump(Yii::app()->user->id);
        $email_in_database_tag = 0;
        if(count(User::model()->findByAttributes(array('email'=>$mainblock['client_email']))) > 0)
        {
            $email_in_database_tag = 1;
        }
    //deb::dump($email_in_database_tag);

        $this->render('addpreview', array(
                                    'mainblock'=>$mainblock,
                                    'addfield'=>$addfield,
                                    'uploadfiles_array'=>$uploadfiles_array,
                                    'mainblock_data'=>$mainblock_data,
                                    'addfield_data'=>$addfield_data,
                                    'options'=>$options,
                                    'email_in_database_tag'=>$email_in_database_tag
        ));

    }

    // Обнуляем зависимых потомков, сохраненных в сессионном массиве
    public function actionCascade_null_relate_props_session()
    {
        $r_id = intval($_POST['r_id']);
        $parent_field_id = $_POST['parent_field_id'];

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
        $this->NullPropInSession($props_hierarhy, $parent_field_id);

    }

    public function NullPropInSession($props_hierarhy, $parent_field_id)
    {
        if(isset($props_hierarhy[$parent_field_id]['childs_selector']) && count($props_hierarhy[$parent_field_id]['childs_selector']) > 0)
        {
            foreach ($props_hierarhy[$parent_field_id]['childs_selector'] as $pkey=>$pval)
            {
                if(isset(Yii::app()->session['addfield'][$pkey]))
                {
                    $addfield = Yii::app()->session['addfield'];
                    $addfield[$pkey] = '';
                    Yii::app()->session['addfield'] = $addfield;
                }

                $this->NullPropInSession($props_hierarhy, $pkey);
            }
        }
    }

    public function actionGet_html_regions()
    {
        $c_id = intval($_POST['c_id']);
        Regions::displayRegionList($c_id);
    }

    public function actionGet_html_towns()
    {
        $reg_id = intval($_POST['reg_id']);
        Towns::displayTownList($reg_id);
    }

    public function actionGet_notice_types()
    {
        $r_id = intval($_POST['r_id']);

        $model = new Notice();
        if(isset($_REQUEST['n_id']) && intval($_REQUEST['n_id'])>0)
        {
            $model = Notice::model()->findByPk(intval($_REQUEST['n_id']));
        }

        $notice_type_id = $this->getMainblockValue($model, 'notice_type_id');
        NoticeTypeRelations::displayNoticeTypeList($r_id, $notice_type_id);
    }


    public function actionAddreglogin()
    {
        $model = new RegistrationFromAddForm;
        $profile = new Profile;

        $usertype = $_POST['usertype'];

        $return_array = array();

        if (Yii::app()->user->id)
        {
            $return_array['status'] = 'ok';
            $return_array['message'] = 'Уже залогиненый пользователь';
            $return_array['errors'] = array();;

        }
        else {
            if(isset($_POST['RegistrationForm'])) {
                $model->attributes=$_POST['RegistrationForm'];

                if($usertype == 'newreg')
                {
                    if($model->validate())
                    {
                        $soucePassword = $model->password;
                        $model->activkey=UserModule::encrypting(microtime().$model->password);
                        $model->password=UserModule::encrypting($model->password);
                        $model->verifyPassword=UserModule::encrypting($model->verifyPassword);
                        $model->superuser=0;
                        $model->status=((Yii::app()->controller->module->activeAfterRegister)?User::STATUS_ACTIVE:User::STATUS_NOACTIVE);


                        if ($model->save()) {
                            $profile->user_id=$model->id;
                            $profile->save();

                            // отправка мыла с верификацией
                            $activation_url = $this->createAbsoluteUrl('/user/activation/activation',array("activkey" => $model->activkey, "email" => $model->email));
                            UserModule::sendMail($model->email,UserModule::t("You registered from {site_name}",array('{site_name}'=>Yii::app()->name)),UserModule::t("Please activate you account go to {activation_url}",array('{activation_url}'=>$activation_url)));

                            Yii::app()->session['add_user_id'] = $model->id;

                            $return_array['status'] = 'ok';
                            $return_array['message'] = 'Регистрация прошла успешно';
                        }

                    }
                    else
                    {
                        $return_array['status'] = 'error';
                        $errors = '';
                        foreach($model->errors as $ekey=>$eval)
                        {
                            $errors .= $eval[0]."<br>";
                        }
                        $return_array['message'] = $errors;
                        $return_array['errors'] = $model->errors;
                    }
                }
                else
                if($usertype == 'inbase')
                {
                    $usermodel = User::model()->findByAttributes(array(
                                                        'email'=>$model->email,
                                                        'password'=>UserModule::encrypting($model->password)));
                    if(isset($usermodel) && count($usermodel) == 1)
                    {
                        /******* Залогиниваемся **********/
                        $identity=new UserIdentity($usermodel->username,$model->password);
                        if($identity->authenticate())
                        {
                            Yii::app()->user->login($identity,0);
                        }
                        /****************/

                        Yii::app()->session['add_user_id'] = $usermodel->id;

                        $return_array['status'] = 'ok';
                        $return_array['message'] = 'Авторизация прошла успешно';
                    }
                    else
                    {
                        $return_array['status'] = 'error';
                        $return_array['message'] = 'Пароль введен неверно';
                    }
                }
                else
                {
                    $return_array['status'] = 'error';
                    $return_array['message'] = 'Ошибка авторизации!';
                    $return_array['errors'] = array();;
                }

            }
            else
            {
                $return_array['status'] = 'error';
                $return_array['message'] = 'Нет данных формы';
                $return_array['errors'] = array();;
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