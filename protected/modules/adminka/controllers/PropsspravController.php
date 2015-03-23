<?php

class PropsspravController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

    public function actionAjax_get_props_sprav()
    {
        $rp_id = intval($_POST['rp_id']);
        $model_rubriks_props = RubriksProps::model()->findByPk($rp_id);

        $props_type_array = PropTypes::getPropsType();

        $this->renderPartial('_get_props_sprav', array('model_rubriks_props'=>$model_rubriks_props,
            'props_type_array'=>$props_type_array));

        $prop_types_params = PropTypesParams::model()->findAll(
            array(
                'select'=>'*',
                'condition'=>'type_id = "'.$model_rubriks_props->type_id.'"',
                'order'=>'pt_id',
                //'limit'=>'10'
            )
        );

//deb::dump($prop_types_params);
        foreach ($prop_types_params as $pkey=>$pval)
        {
            $props_spav_records = PropsSprav::getPropsSprav($model_rubriks_props, $pval);

            $this->renderPartial('_props_sprav_item',
                array('rp_id'=>$rp_id, 'prop_types_params_row'=>$pval, 'props_spav_records'=>$props_spav_records));
        }


        echo "<!--ok-->";

    }


    public function actionAjax_addrow()
    {
        $model = new PropsSprav();
        $model->attributes = $_POST['field'];

        $max_sort = PropsSprav::model()->find(
            array(
                'select'=>'MAX(sort_number) maxsort',
                'condition'=>'rp_id = '.$model->rp_id,
            )

        );
        $model->sort_number = $max_sort->maxsort + 1;

        $rubriks_props_model = RubriksProps::model()->findByPk($model->rp_id);
        $prop_types_params_model = PropTypesParams::model()->find(
            array(
                'select'=>'*',
                'condition'=>'type_id = "'.$rubriks_props_model->type_id . '" AND selector = "'.$model->selector . '"',
            )

        );

        $props_sprav_model = PropsSprav::model()->find(
            array(
                'select'=>'*',
                'condition'=>'rp_id = '. $model->rp_id . ' AND selector = "'.$model->selector . '"',
            )

        );

        //deb::dump($prop_types_params_model);
        if (count($props_sprav_model) == 1 && $prop_types_params_model->maybe_count == 'one')
        {
            echo "Возможно добавление только одного элемента!";
        }
        else
        {
            if (!$model->save())
            {
                deb::model_errors($model->errors);
            }
            else
            {
                echo "<!--ok-->";
                $this->renderPartial('_props_sprav_item_row', array('model'=>$model));
            }
        }

    }

    public function actionAjax_editrow()
    {
        $model = PropsSprav::model()->findByPk($_POST['ps_id']);

        $this->renderPartial('_props_sprav_item_row_edit', array('model'=>$model));

    }

    public function actionAjax_saveedit_row()
    {
        $model = PropsSprav::model()->findByPk($_POST['params']['ps_id']);

        $model->attributes = $_POST['params'];
        if (!$model->save())
        {
            deb::model_errors($model->errors);
        }
        else
        {
            echo "<!--ok-->";
            $this->renderPartial('_props_sprav_item_row', array('model'=>$model));
        }

    }


    public function actionAjax_del_row()
    {
        $model = PropsSprav::model()->findByPk($_POST['ps_id']);

        if ($model->delete())
        {
            echo "<!--ok-->";
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