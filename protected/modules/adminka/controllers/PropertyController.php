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

        $model= new RubriksProps();

        $model_items = RubriksProps::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id = '.$r_id,
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
        ));

        $props_type_array = PropTypes::getPropsType();
        $potential_parents = RubriksProps::getPotentialParents($r_id, 0);

        $this->renderPartial('ajax_rubprops', array('r_id'=>$r_id, 'model'=>$model, 'model_items'=>$model_items,
                'props_type_array'=>$props_type_array, 'potential_parents'=>$potential_parents));
    }

    public function actionAjax_addrubprops()
    {
        $model = new RubriksProps();
        $model->hierarhy_tag = 0;
        $model->use_in_filter = 0;
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

        $model->hierarhy_tag = 0;
        $model->use_in_filter = 0;
        $model->attributes = $_POST['params'];
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
            $potential_parents = RubriksProps::getPotentialParents($model->r_id, 0);

            $this->renderPartial('_rubprops_item',
                array('model'=>$model, 'props_type_array'=>$props_type_array, 'potential_parents'=>$potential_parents));
        }

    }

    public function actionAjax_del_rubriks_props_row()
    {
        $model = RubriksProps::model()->findByPk($_POST['rp_id']);

        echo "Доработать! Не удалять, если есть связанные записи!";
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