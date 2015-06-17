<?php

class AdvertController extends Controller
{
	public function actionAddadvert()
	{
        $rub_array = Rubriks::get_rublist();

        $n_id=0;
        if(isset($_GET['n_id']))
        {
            $n_id = intval($_GET['n_id']);
        }

        $model = new Notice();
//deb::dump($model->n_id);
        $mainblock = array();
        if(isset(Yii::app()->session['mainblock']))
        {
            $mainblock = Yii::app()->session['mainblock'];
        }

        $country_array = Countries::getCountryList();

        $this->render('addadvert', array('rub_array'=>$rub_array, 'model'=>$model,
                    'mainblock'=>$mainblock, 'n_id'=>$n_id, 'country_array'=>$country_array));

    }

    public function getMainblockValue($model, $field_name)
    {

        // Режим добавления, данные берем из сессии (если там есть)
        if($model->n_id == null)
        {
            $value = '';
            if(isset(Yii::app()->session['mainblock'][$field_name]))
            {
                $value = Yii::app()->session['mainblock'][$field_name];
            }

            return $value;
        }
    }

    public function getAddfieldValue($n_id, $field_name)
    {

        // Режим добавления, данные берем из сессии (если там есть)
        if($n_id == 0)
        {
            $value = '';
            if(isset(Yii::app()->session['addfield'][$field_name]))
            {
                $value = Yii::app()->session['addfield'][$field_name];
            }

            return $value;
        }
    }

    public function getParentPsId($n_id, $parent_field_id)
    {

        // Режим добавления, данные берем из сессии (если там есть)
        if($n_id == 0)
        {
            $value = '';
            if(isset(Yii::app()->session['addfield'][$parent_field_id]))
            {
                $value = Yii::app()->session['addfield'][$parent_field_id];
            }

            return $value;
        }
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
            // Расскомментировать!!!
            $block_display = 'block';
            /*
            $block_display = 'none';
            if( ($mval->hierarhy_tag == 1 && $mval->hierarhy_level == 1) || $mval->hierarhy_tag == 0 )
            {
                $block_display = 'block';
            }
            */
            //deb::dump($mval);
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
                 <input class="add_hideinput" style="width: 30px; background-color: #ddd;" readonly type="text" name="addfield[<?= $mval->selector;?>]" id="<?= $mval->selector;?>" value="<?= $this->getAddfieldValue($n_id, $mval->selector);?>">

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
                <input class="add_hideinput" style="width: 30px; background-color: #ddd;" readonly type="text" name="addfield[<?= $mval->selector;?>]" id="<?= $mval->selector;?>" value="<?= $this->getAddfieldValue($n_id, $mval->selector);?>">

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
             ?>
                dsfdfsfsd
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

        <?
        foreach ($model_items as $mkey=>$mval)
        {
            $field_id = $mval->selector;
            $parent_field_id = '';
            if($mval->parent_id > 0)
            {
                $parent_field_id = $model_items_array[$mval->parent_id]->selector;
            }

            $parent_ps_id = intval($this->getParentPsId($n_id, $parent_field_id));

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
            //alert('<?= $mval->vibor_type;?>-<?= $field_id;?>-<?= $parent_field_id;?>');
            get_props_list_<?= $mval->vibor_type;?>('<?= $field_id;?>', '<?= $parent_field_id;?>', <?= $n_id;?>, <?= $parent_ps_id;?>);
            <?
        }
        ?>

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
        $n_id = intval($_POST['n_id']);

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

        $currvalue = $this->getAddfieldValue($n_id, $field_id);
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
        <select class="input-proplist-selector" name="addfield[<?= $field_id;?>]" id="<?= $field_id;?>">
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
        //echo $_POST;
        $parent_field_id = $_POST['parent_field_id'];
        $parent_ps_id = intval($_POST['parent_ps_id']);
        $n_id = intval($_POST['n_id']);

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
//deb::dump($model_rubriks_props);
//deb::dump($prop_types_params_row);
//deb::dump($parent_ps_id);
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
        $n_id = intval($_POST['n_id']);

        $checked_array = $this->getAddfieldValue($n_id, $field_id);
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
                //deb::dump($pval);
            ?>
                <input style="" type="checkbox" name="addfield[<?= $model_rubriks_props->selector;?>][<?= $pval->ps_id;?>]" id="<?= $model_rubriks_props->selector;?>-<?= $pval->ps_id;?>" <?= $this->getCheckedAttr($pval->ps_id, $checked_array);?>> <?= $pval->value;?>
            <?
            }
        }

        ?>

        <script>

        </script>
    <?

    }


    public function actionGetpropslist_radio()
    {
        $field_id = $_POST['field_id'];
        //echo $field_id;
        $parent_field_id = $_POST['parent_field_id'];
        $parent_ps_id = intval($_POST['parent_ps_id']);
        $n_id = intval($_POST['n_id']);

        $value = $this->getAddfieldValue($n_id, $field_id);

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
                <?= $pval->value;?> <input style="" <?= $this->getRadioCheckedAttr($value, $pval->ps_id);?> type="radio" name="addfield[<?= $model_rubriks_props->selector;?>]" id="<?= $model_rubriks_props->selector;?>-<?= $pval->ps_id;?>" value="<?= $pval->ps_id;?>">
            <?
            }
        }

        ?>

        <script>

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

        $value = $this->getAddfieldValue($n_id, $field_id);

        $model_rubriks_props = RubriksProps::model()->find(
            array(
                'condition'=>'selector = :selector',
                'params'=>array(':selector'=>$field_id),
            )
        );
//deb::dump($model_rubriks_props);
        $prop_types_params_row = PropTypesParams::model()->find(array(
            'select'=>'*',
            'condition'=>'type_id = "'.$model_rubriks_props->type_id.'" AND selector = "string"',
        ));
//deb::dump($prop_types_params_row);

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
                <?= $pval->value;?> <input class="add_hideinput" style="width: 30px; background-color: #ddd;" readonly type="text" name="addfield[<?= $model_rubriks_props->selector;?>][ps_id]" id="<?= $model_rubriks_props->selector;?>" value="<?= $pval->ps_id;?>">

                <input style="" type="text" name="addfield[<?= $model_rubriks_props->selector;?>][hand_input_value]" id="<?= $model_rubriks_props->selector;?>" value="<?= htmlspecialchars($value_hand, ENT_COMPAT);?>">

            <?
            }
        }

        ?>

        <script>

        </script>
    <?

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
        $delfile = str_replace('../', '', $_POST['name']);
        unlink($output_dir.$delfile);

        $temp = Yii::app()->session['mainblock'];
        $temp['uploadfiles'] = str_replace($delfile.';', '', $temp['uploadfiles']);
        if($delfile == $temp['uploadmainfile'])
        {
            $temp['uploadmainfile'] = '';
        }

        Yii::app()->session['mainblock'] = $temp;


        echo json_encode($delfile);
    }

    // Сохранение нового объявления
    public function actionAddnew()
    {
        $mainblock_array = array();
        if (isset($_POST['mainblock']))
        {
            Yii::app()->session['mainblock'] = $_POST['mainblock'];
            $mainblock_array = $_POST['mainblock'];
        }
        else
        if(isset(Yii::app()->session['mainblock']))
        {
            $mainblock_array = Yii::app()->session['mainblock'];
        }

        $addfield_array = array();
        if (isset($_POST['addfield']))
        {
            Yii::app()->session['addfield'] = $_POST['addfield'];
            $addfield_array = $_POST['addfield'];
        }
        else
        if(isset(Yii::app()->session['addfield']))
        {
            $addfield_array = Yii::app()->session['addfield'];
        }

        $newmodel = new Notice();
        $newmodel->attributes = $mainblock_array;

        $newmodel->date_add = time();
        $newmodel->date_lastedit = $newmodel->date_add;
        $expire_period = intval($mainblock_array['expire_period']);
        $newmodel->date_expire = $newmodel->date_add + $expire_period*86400;

        // Поиск дублей
        $control_string = $newmodel->client_name . $newmodel->client_email . $newmodel->client_phone . $newmodel->r_id . $newmodel->title . $newmodel->notice_type_id . $newmodel->notice_text;
        $newmodel->checksum = md5($control_string);
        // Доделать поиск дублей
        // ...

        $newmodel->active_tag = 1;
        $newmodel->verify_tag = 1;
        $newmodel->views_count = 0;
        $newmodel->moder_counted_tag = 0;

        if(Yii::app()->user->id <= 0)
        {
            $newmodel->u_id = 0;
        }
        else
        {
            $newmodel->u_id = Yii::app()->user->id;
        }

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


        $return_array['errors'] = array();
        if(!$newmodel->validate())
        {
            $return_array['errors'] = $newmodel->getErrors();
        }
        else
        {
            //

            // На следующем шаге
            /*
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
            */
        }

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


    // Предварительный просмотр и авторизация
    public function actionAddpreview()
    {
        Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/galleria/galleria-1.4.2.js');

        $mainblock = Yii::app()->session['mainblock'];
        $addfield = Yii::app()->session['addfield'];

        $uploadfiles_array = Notice::getImageArray($mainblock['uploadfiles'], $mainblock['uploadmainfile']);

        // Подготавливаем данные из основной части объявления
        $mainblock_data = array();
        $mainblock_data['country'] = Countries::model()->findByPk($mainblock['c_id']);
        $mainblock_data['region'] = Regions::model()->findByPk($mainblock['reg_id']);
        $mainblock_data['town'] = Towns::model()->findByPk($mainblock['t_id']);

        // Подготавливаем данные из свойств
        $rubrik_props = RubriksProps::getAllProps($mainblock['r_id']);
        $rubrik_props_rp_id = RubriksProps::getAllPropsRp_id($mainblock['r_id']);
//deb::dump($rubrik_props);

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
//                            deb::dump($rkey);
//                            deb::dump($addfield[$rkey]);
                            $props_ids[] = $addfield[$rkey];
                            $notice_props[$rval->rp_id] = $addfield[$rkey];
                        }
                    break;

                    case "checkbox":
                        foreach($addfield[$rkey] as $ckey=>$cval)
                        {
//                            deb::dump($rkey);
//                            deb::dump($ckey);
                            $props_ids[] = $ckey;
                            $props_string_ids[$ckey] = $ckey;
                            $notice_props[$rval->rp_id][$ckey] = $ckey;
                        }
                    break;

                    case "string":
                        if(trim($addfield[$rkey]['hand_input_value']) != '')
                        {
//                            deb::dump($rkey);
//                            deb::dump($addfield[$rkey]['ps_id']);
                            $props_ids[] = $addfield[$rkey]['ps_id'];
                            $notice_props[$rval->rp_id] = $addfield[$rkey]['hand_input_value'];
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

        //deb::dump($props_data);

        $this->render('addpreview', array('mainblock'=>$mainblock, 'addfield'=>$addfield, 'uploadfiles_array'=>$uploadfiles_array,
                                    'mainblock_data'=>$mainblock_data, 'addfield_data'=>$addfield_data
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