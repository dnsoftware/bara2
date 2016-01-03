<?php

class PropertyController extends Controller
{
	public function actionIndex()
	{
        //deb::dump(Rubriks::get_rublist());

        $rub_array = Rubriks::get_rublist();


        //deb::dump($rub_array);

		$this->render('index', ['rub_array'=>$rub_array]);
	}


    public function actionAjax_rubprops()
    {
        $r_id=intval($_POST['r_id']);
        $rubrik = Rubriks::model()->findByPk($r_id);

        $model= new RubriksProps();

        $model_items = RubriksProps::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id = '.$r_id,
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
        ));

        $props_type_array = PropTypes::getPropsType();
        $potential_parents = RubriksProps::getPotentialParents($r_id, 0);

//        if($rubrik->parent_id != 0)
//        {
            $this->renderPartial('ajax_rubprops', array('r_id'=>$r_id, 'model'=>$model, 'model_items'=>$model_items,
                'props_type_array'=>$props_type_array, 'potential_parents'=>$potential_parents));
//        }
    }

    public function actionAjax_addrubprops()
    {
        $model = new RubriksProps();
        $model->hierarhy_tag = 0;
        $model->use_in_filter = 0;
        $model->all_values_in_filter = 0;
        $model->attributes = $_POST['rubrikprops'];

        if (!$model->save())
        {
            foreach ($model->errors as $ekey=>$eval)
            {
                echo $eval[0]."<br/>";
            };
        }
        else
        {
            echo "<!--ok-->";
            $props_type_array = PropTypes::getPropsType();
            $this->renderPartial('_rubprops_item', array('model'=>$model, 'props_type_array'=>$props_type_array));
        }

    }

    public function actionAjax_edit_rubriks_props_row()
    {
        $rp_id = intval($_POST['rp_id']);
        $model = RubriksProps::model()->findByPk($rp_id);

        $props_type_array = PropTypes::getPropsType();
//deb::dump($props_type_array[$model->type_id]);

        $potential_parents = RubriksProps::getPotentialParents($model->r_id, $rp_id);

        $this->renderPartial('_rubriks_props_item_edit',
                array('model'=>$model, 'props_type_array'=>$props_type_array, 'potential_parents'=>$potential_parents));

    }

    public function actionAjax_saveedit_rubriks_props_row()
    {
        $model = RubriksProps::model()->findByPk($_POST['params']['rp_id']);

        // Если разрываем зависимость, проверяем, есть ли связанные свойства в таблице props_relations
        $childs = array();
        $parent_ps_id_array = array();
        $child_ps_id_array = array();
        if($model->parent_id != $_POST['params']['parent_id'])
        {
            $propsprav_rows = PropsSprav::model()->findAllByAttributes(array('rp_id'=>$model->rp_id));
            $ps_array = array();
            foreach($propsprav_rows as $ppkey=>$ppval)
            {
                $ps_array[] = $ppval->ps_id;
            }

            $parent_rows = PropsSprav::model()->findAllByAttributes(array('rp_id'=>$model->parent_id));

            foreach($parent_rows as $pkey=>$pval)
            {
                $parent_ps_id_array[] = $pval->ps_id;
            }

            if(count($parent_ps_id_array) > 0)
            {
                $child_rows = PropsRelations::model()->findAll(array(
                    'select'=>'*',
                    'condition'=>'parent_ps_id IN ('.implode(", ", $parent_ps_id_array).')
                                    AND child_ps_id IN ('.implode(",", $ps_array).') '
                ));
            }


        }

        if(count($child_rows) > 0)
        {
            echo "Есть зависимости. Разрыв/смена зависимости невозможно!";
        }
        else
        {
            $model->hierarhy_tag = 0;
            $model->use_in_filter = 0;
            $model->require_prop_tag = 0;
            $model->hide_if_no_elems_tag = 0;
            $model->all_values_in_filter = 0;
            $model->attributes = $_POST['params'];

            if (!$model->save())
            {
                foreach ($model->errors as $ekey=>$eval)
                {
                    echo $model->sort_props_sprav;
                    echo $eval[0]."<br/>";
                };
            }
            else
            {
                echo "<!--ok-->";
                $props_type_array = PropTypes::getPropsType();
                $potential_parents = RubriksProps::getPotentialParents($model->r_id, 0);

                $this->renderPartial('_rubprops_item',
                    array('model'=>$model, 'props_type_array'=>$props_type_array, 'potential_parents'=>$potential_parents));
            }
        }

    }

    public function actionAjax_del_rubriks_props_row()
    {
        $model = RubriksProps::model()->findByPk($_POST['rp_id']);

        echo "Доработать! Не удалять, если есть связанные записи!";
    }


    public function actionAjax_save_advert_list_item_shablon()
    {
        $r_id = intval($_POST['r_id']);

        if($rubrik = Rubriks::model()->findByPk($r_id))
        {
            $rubrik->advert_list_item_shablon = $_POST['advert_list_item_shablon'];

            if($rubrik->save())
            {
                echo 'ok';
            }
            else
            {
                echo 'error';
            }
        }
        else
        {
            echo 'error';
        }
    }

    public function actionGet_rubrik_advert_list_shablon()
    {
        $r_id = intval($_POST['r_id']);

        if($rubrik = Rubriks::model()->findByPk($r_id))
        {
            echo $rubrik->advert_list_item_shablon;
        }
        else
        {
            echo "error";
        }
    }

    public function actionImportauto()
    {
        $this->render('importauto');

    }

    public function actionImportautomark()
    {
        $rubriks_props = RubriksProps::model()->findByAttributes(array('selector'=>'car_marka'));
//deb::dump($rubriks_props);
        $prop_type_params = PropTypesParams::model()->findByAttributes(array('type_id'=>$rubriks_props->type_id));
//deb::dump($prop_type_params);

        $car_mark = CarMark::model()->findAll();
        $add_count=0;
        $supporter = new Supporter();
        foreach($car_mark as $ckey=>$cval)
        {
            if(!$props_sprav = PropsSprav::model()->findByAttributes(array('rp_id'=>$rubriks_props->rp_id, 'support_key1'=>$cval->id_car_mark)))
            {
                $props_sprav = new PropsSprav();
                $props_sprav->rp_id = $rubriks_props->rp_id;
                $props_sprav->type_id = $rubriks_props->type_id;
                $props_sprav->selector = $prop_type_params->selector;
                $props_sprav->value = $cval->name;
                $props_sprav->transname = $supporter->TranslitForUrl($cval->name);
                $props_sprav->support_key1 = $cval->id_car_mark;
                $props_sprav->save();

                $add_count++;
            }
            else
            {
                //deb::dump($props_sprav);
                $props_sprav->value = $cval->name;
                $props_sprav->transname = $supporter->TranslitForUrl($cval->name);
                $props_sprav->support_key1 = $cval->id_car_mark;
                $props_sprav->save();
            }

        }


        /*
        $props_sprav = PropsSprav::model()->findAllByAttributes(array('rp_id'=>$rubriks_props->rp_id, 'support_key1'=>));*/


        $this->render('importautomark', array('add_count'=>$add_count));

    }

    public function actionImportcarmodel()
    {
        $rubriks_props = RubriksProps::model()->findByAttributes(array('selector'=>'car_model'));
//deb::dump($rubriks_props);
        $prop_type_params = PropTypesParams::model()->findByAttributes(array('type_id'=>$rubriks_props->type_id));
//deb::dump($prop_type_params);

        $car_model = CarModel::model()->findAll();
        $add_count=0;
        $supporter = new Supporter();
        foreach($car_model as $ckey=>$cval)
        {
            if(!$props_sprav = PropsSprav::model()->findAllByAttributes(array('rp_id'=>$rubriks_props->rp_id, 'support_key1'=>$cval->id_car_model)))
            {
                $props_sprav = new PropsSprav();
                $props_sprav->rp_id = $rubriks_props->rp_id;
                $props_sprav->type_id = $rubriks_props->type_id;
                $props_sprav->selector = $prop_type_params->selector;
                $props_sprav->value = $cval->name;
                $props_sprav->transname = $supporter->TranslitForUrl($cval->name);
                $props_sprav->support_key1 = $cval->id_car_model;
                $props_sprav->support_key2 = $cval->id_car_mark;
                $props_sprav->save();

                $add_count++;
            }

        }

        // Устанавливаем связи
        $rubriks_props_car_marka = RubriksProps::model()->findByAttributes(array('selector'=>'car_marka'));
        $car_marks = PropsSprav::model()->findAll(array(
            'select'=>'ps_id, support_key1',
            'condition'=>'rp_id = ' . $rubriks_props_car_marka->rp_id
        ));

        $add_link_count = 0;
        foreach($car_marks as $ckey=>$cval)
        {
//            deb::dump($cval);
            $car_models = PropsSprav::model()->findAll(array(
                'select'=>'ps_id, value,support_key2',
                'condition'=>'support_key2 = '.$cval->support_key1.' AND rp_id = '.$rubriks_props->rp_id
            ));

            foreach($car_models as $c2key=>$c2val)
            {
                if(!$prop_in_base = PropsRelations::model()->findByAttributes(array(
                    'parent_ps_id'=>$cval->ps_id, 'child_ps_id'=>$c2val->ps_id
                )))
                {
                    $newrel = new PropsRelations();
                    $newrel->parent_ps_id = $cval->ps_id;
                    $newrel->child_ps_id = $c2val->ps_id;
                    $newrel->save();

                    $add_link_count ++;
                }
            }
        }

            /*
            $props_sprav = PropsSprav::model()->findAllByAttributes(array('rp_id'=>$rubriks_props->rp_id, 'support_key1'=>));*/


        $this->render('importcarmodel', array('add_count'=>$add_count, 'add_link_count'=>$add_link_count));

    }

    // Годы выпуска
    public function actionImportcaryears()
    {
        $connection=Yii::app()->db;

        $add_count=0;
        $rubriks_props = RubriksProps::model()->findByAttributes(array('selector'=>'car_year_vipusk'));
        $rubriks_props_car_model = RubriksProps::model()->findByAttributes(array('selector'=>'car_model'));

        $prop_type_params = PropTypesParams::model()->findByAttributes(array('type_id'=>$rubriks_props->type_id));
//deb::dump($prop_type_params);

        for($i=1934; $i<=intval(date("Y")); $i++)
        {
            if(!$props_sprav = PropsSprav::model()->findAllByAttributes(array('rp_id'=>$rubriks_props->rp_id, 'value'=>$i)))
            {
                $props_sprav = new PropsSprav();
                $props_sprav->rp_id = $rubriks_props->rp_id;
                $props_sprav->type_id = $rubriks_props->type_id;
                $props_sprav->selector = $prop_type_params->selector;
                $props_sprav->value = $i;
                $props_sprav->transname = $i;
                $props_sprav->save();

                $add_count++;
            }
        }

        // Связи
        $years_ps_array = array();
        $props_sprav_year = PropsSprav::model()->findAllByAttributes(array(
            'rp_id'=>$rubriks_props->rp_id
        ));
        foreach($props_sprav_year as $ykey=>$yval)
        {
            $years_ps_array[$yval->value] = $yval->ps_id;
        }


        /*
        $sql = "SELECT ps.ps_id, MIN(cg.year_begin) year_start, MAX(cg.year_end) year_end
                FROM ". $connection->tablePrefix . "props_sprav ps,
                     car_generation cg
                WHERE ps.rp_id = ".$rubriks_props_car_model->rp_id."
                    AND ps.support_key1 = cg.id_car_model
                GROUP BY ps.ps_id, ps.value
                    ";
        */
        $sql = "SELECT ps.ps_id, MIN(cg.start_production_year) year_start, MAX(cg.end_production_year) year_end
                FROM ". $connection->tablePrefix . "props_sprav ps,
                     car_modification cg
                WHERE ps.rp_id = ".$rubriks_props_car_model->rp_id."
                    AND ps.support_key1 = cg.id_car_model
                GROUP BY ps.ps_id, ps.value
                    ";

        //deb::dump($sql);
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $add_link_count = 0;
        while(($row = $dataReader->read())!==false)
        {
            //deb::dump($row);
            if(is_null($row['year_end']))
            {
                $row['year_end'] = intval(date('Y'));
            }

            for($j=$row['year_start']; $j<=$row['year_end']; $j++)
            {
                $parent_ps_id = $row['ps_id'];
                $child_ps_id = $years_ps_array[$j];
                if(!$rel_row = PropsRelations::model()->findByAttributes(array(
                    'parent_ps_id'=>$parent_ps_id, 'child_ps_id'=>$child_ps_id
                    )))
                {
                    $relnew = new PropsRelations();
                    $relnew->parent_ps_id = $parent_ps_id;
                    $relnew->child_ps_id = $child_ps_id;
                    $relnew->save();

                    $add_link_count++;
                }
            }

        }




            $this->render('importcaryears', array('add_count'=>$add_count, 'add_link_count'=>$add_link_count));
    }


    // Кузов
    public function actionImportcarcharact($rp_id_selector, $id_characteristic)
    {
        //$id_characteristic
        // для кузовов = 2, car)kuzov
        // тип двигателя = 12, car_dvigatel
        // привод = 27, car_privod
        // коробка = 24, car_korobka
        // объем двигателя = 13, car_obyom_dvig
        // мощность лс = 14, car_moshnost

        $connection=Yii::app()->db;

        $add_count=0;
        $rp_car_year_vipusk = RubriksProps::model()->findByAttributes(array('selector'=>'car_year_vipusk'));
        $rp_car_model = RubriksProps::model()->findByAttributes(array('selector'=>'car_model'));
        $rp_row = RubriksProps::model()->findByAttributes(array('selector'=>$rp_id_selector));
        $prop_type_params = PropTypesParams::model()->findByAttributes(array('type_id'=>$rp_row->type_id));

        $supporter = new Supporter();

        // Получаем названия всех свойств
        $charact_types = CarCharacteristicValue::model()->findAll(array(
            'select'=>'value',
            'condition'=>'id_car_characteristic = '.$id_characteristic,
            'group'=>'value'
        ));

        foreach($charact_types as $kkey=>$kval)
        {
            // патч для объема двигателя
            if($rp_id_selector == 'car_obyom_dvig')
            {
                $kval->value = round($kval->value/1000, 1);
            }

            if(!$props_sprav = PropsSprav::model()->findByAttributes(array(
                'rp_id'=>$rp_row->rp_id, 'value'=>$kval->value
            )))
            {
                $props_sprav = new PropsSprav();
                $props_sprav->rp_id = $rp_row->rp_id;
                $props_sprav->type_id = $rp_row->type_id;
                $props_sprav->selector = $prop_type_params->selector;
                $props_sprav->value = $kval->value;
                $props_sprav->transname = $supporter->TranslitForUrl($kval->value);
                $props_sprav->save();

                $add_count++;

                //deb::dump($kval);
            }

            $props_sprav_charact_array[(string)$kval->value] = $props_sprav->ps_id;

        }

        $car_model = PropsSprav::model()->findAll(array(
            'select'=>'*',
            'condition'=>'rp_id = '.$rp_car_model->rp_id
        ));

        $sql = "SELECT ps.ps_id, cc.value
                FROM ". $connection->tablePrefix . "props_sprav ps,
                     car_modification cg,
                     car_characteristic_value cc
                WHERE ps.rp_id = ".$rp_car_model->rp_id."
                    AND ps.support_key1 = cg.id_car_model
                    AND cg.id_car_modification = cc.id_car_modification
                    AND cc.id_car_characteristic = ".$id_characteristic . "
                    ";

        //deb::dump($sql);
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $add_link_count = 0;
        //deb::dump($props_sprav_charact_array);
        while(($row = $dataReader->read())!==false)
        {

            $parent_ps_id = $row['ps_id'];

            $child_ps_id = $props_sprav_charact_array[$row['value']];
            // патч для объема двигателя
            if($rp_id_selector == 'car_obyom_dvig')
            {
                $child_ps_id = $props_sprav_charact_array[(string)round($row['value']/1000, 1)];
            }

            if(!$rel_row = PropsRelations::model()->findByAttributes(array(
                'parent_ps_id'=>$parent_ps_id, 'child_ps_id'=>$child_ps_id
            )))
            {
                $relnew = new PropsRelations();
                $relnew->parent_ps_id = $parent_ps_id;
                $relnew->child_ps_id = $child_ps_id;
                $relnew->save();

                $add_link_count++;
            }


        }

            $this->render('importcarcharact', array('add_count'=>$add_count, 'add_link_count'=>$add_link_count));
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