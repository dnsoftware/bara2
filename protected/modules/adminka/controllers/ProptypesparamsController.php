<?php

class ProptypesparamsController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

    public function actionAjax_add_prop_types_params()
    {
        $model = new PropTypesParams();

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
            echo "<!--type_id--".$model->type_id."--/type_id-->";

            $this->renderPartial('_tparam_item', array('model'=>$model));
        }

    }

    public function actionAjax_edit_rubprops_row()
    {
        $model = PropTypesParams::model()->findByPk($_POST['pt_id']);

        $this->renderPartial('_tparam_item_edit', array('model'=>$model));
    }

    public function actionAjax_saveedit_rubprops_row()
    {
        $model = PropTypesParams::model()->findByPk($_POST['params']['pt_id']);

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
            echo "<!--type_id--".$model->type_id."--/type_id-->";

            $this->renderPartial('_tparam_item', array('model'=>$model));
        }

    }

    public function actionAjax_del_rubprops_row()
    {
        $model = PropTypesParams::model()->findByPk($_POST['pt_id']);

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