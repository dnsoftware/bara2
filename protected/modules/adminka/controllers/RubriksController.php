<?php

class RubriksController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Rubriks;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Rubriks']))
		{
			$model->attributes=$_POST['Rubriks'];
            $supporter = new Supporter();
            $model->transname = $supporter->TranslitForUrl($model->name);
			if($model->save())
            {
                /*
                if(is_array($_POST['NoticeTypeRelations']['notice_type_id']) > 0)
                {
                    foreach ($_POST['NoticeTypeRelations']['notice_type_id'] as $nval)
                    {
                        $ntr_model = new NoticeTypeRelations();
                        $ntr_model->r_id = $model->r_id;
                        $ntr_model->notice_type_id = $nval;
                        $ntr_model->save();
                    }
                }
                */

                $this->redirect(array('view','id'=>$model->r_id));
            }
		}

        $parent_list = Rubriks::get_parentlist();
        //$empty_type = new NoticeTypeRelations;

        $this->render('create',array(
			'model'=>$model,
            'parent_list'=>$parent_list,
            /*'empty_type'=>$empty_type*/
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Rubriks']))
		{
			$model->attributes=$_POST['Rubriks'];
            $supporter = new Supporter();
            $model->transname = $supporter->TranslitForUrl($model->name);
            if($model->save())
            {
                NoticeTypeRelations::model()->deleteAll('r_id='.$id);
                if(is_array($_POST['NoticeTypeRelations']['notice_type_id']) > 0)
                {
                    //deb::dump($_POST);
                    //die();
                    foreach ($_POST['NoticeTypeRelations']['notice_type_id'] as $nval)
                    {
                        $ntr_model = new NoticeTypeRelations();
                        $ntr_model->r_id = $id;
                        $ntr_model->notice_type_id = $nval;

                        $ntr_model->image_field_tag = 0;
                        if(isset($_POST['NoticeTypeRelations']['image_field_tag'][$nval]))
                        {
                            $ntr_model->image_field_tag = 1;
                        }

                        $ntr_model->notice_fields_exception = '';
                        if(is_array($_POST['NoticeTypeRelations']['notice_fields_exception'][$nval])
                            && count($_POST['NoticeTypeRelations']['notice_fields_exception'][$nval]) > 0 )
                        {
                            $ntr_model->notice_fields_exception = implode(";",$_POST['NoticeTypeRelations']['notice_fields_exception'][$nval]);
                        }

                        if($ntr_model->save())
                        {

                        }
                        else{
                            deb::dump($ntr_model->getErrors());
                            die();
                        }
                    }
                }
                $this->redirect(array('view','id'=>$model->r_id));
            }
		}

        $empty_type = new NoticeTypeRelations;
        $types = NoticeTypeRelations::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id = '.$id
        ));
        $types_array = array();
        $types_records = array();
        if(count($types)>0)
        {
            foreach($types as $tval)
            {
                $tval->notice_fields_exception = explode(";", $tval->notice_fields_exception);
                $types_array[] = $tval->notice_type_id;
                $types_records[$tval->notice_type_id] = $tval;
            }
        }
        $empty_type->notice_type_id = $types_array;

        $parent_list = Rubriks::get_parentlist();

		$this->render('update',array(
			'model'=>$model, 'types'=>$types, 'empty_type'=>$empty_type, 'parent_list'=>$parent_list,
            'types_records'=>$types_records
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Rubriks');

        $rubriks_parent = Rubriks::model()->findAll(array(
            'select'=>'*',
            'condition'=>'parent_id = 0',
            'order'=>'sort_num ASC',
        ));
        $rubriks_child = Rubriks::model()->findAll(array(
            'select'=>'*',
            'condition'=>'parent_id > 0',
            'order'=>'sort_num ASC',
        ));
        $rub_array = array();
        foreach ($rubriks_parent as $rkey=>$rval)
        {
            $rub_array[$rval->r_id]['parent'] = $rval;
        }
        foreach ($rubriks_child as $rkey=>$rval)
        {
            $rub_array[$rval->parent_id]['childs'][$rval->r_id] = $rval;
        }
//        deb::dump($rub_array);

		$this->render('index',array(
			'rub_array'=>$rub_array,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Rubriks('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Rubriks']))
			$model->attributes=$_GET['Rubriks'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Rubriks the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Rubriks::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Rubriks $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='rubriks-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
