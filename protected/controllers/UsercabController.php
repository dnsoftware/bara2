<?php

class UsercabController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}


    public function actionAdverts()
    {
        $adverts = Notice::model()->findAll(array(
            'select'=>'*',
            'condition'=>'u_id = '.Yii::app()->user->id
        ));

//        deb::dump($adverts);
        $this->render('adverts', array('adverts'=>$adverts));
    }


    public function actionAdvert_edit()
    {
        $n_id = intval($_GET['n_id']);
        if($advert = Notice::checkAdvertOwner(Yii::app()->user->id, $n_id))
        {
            //deb::dump($advert);

            $props = NoticeProps::model()->findAllByAttributes(array('n_id'=>$n_id));
            //deb::dump($props);

            list($controller) = Yii::app()->createController('advert');
            //deb::dump($controller);
            $controller->actionAddadvert();

            //$this->render('/advert/addadvert');
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