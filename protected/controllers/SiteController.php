<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

    public function actionIndex()
    {
        //$support = new Supporter();
        //$support->MakeTranslitAll();
        //deb::dump($row);


        /*
        // Генерация xml для всех фото
        $notices = Notice::model()->findAll();
        foreach($notices as $nkey=>$nval)
        {
            AdvertController::PropsXmlGenerate($nval->n_id);
        }
        */

//deb::dump(Yii::app()->session['usercheckphone_code']);
//deb::dump(Yii::app()->session['usercheckphone_message_id']);


        $countries = Countries::model()->findAll();
        $regions = Regions::model()->findAll(array(
            'condition'=>'c_id=1',
            'order'=>'name'
        ));

        /*
        $path = Yii::getPathOfAlias('webroot');
        $SxGeo = new SxGeo($path.'/sypexgeo/SxGeoCity.dat');
        $ip = $_SERVER['REMOTE_ADDR'];
        $geodata = $SxGeo->getCityFull($ip);
        //deb::dump($geodata);

        if(isset($geodata['city']))
        {
            if($city = Towns::model()->findByPk($geodata['city']['id']))
            {
                header('Location: /'.$city->transname);
            }

        }
        else
        {
            $this->render('index', array('countries'=>$countries, 'regions'=>$regions));
        }
        */


        $this->render('index', array('countries'=>$countries, 'regions'=>$regions));

    }



    // Установка куки подтверждения выбора региона
    public function actionSetregconfirmyes()
    {
        $cookie = new CHttpCookie('region_confirm_tag', 1);
        $cookie->expire = time() + 86400*30*12;
        Yii::app()->request->cookies['region_confirm_tag'] = $cookie;
    }

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

}