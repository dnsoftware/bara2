<?php

class ProfileController extends Controller
{
	public $defaultAction = 'profile';
	public $layout='//layouts/column1';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;
	/**
	 * Shows a particular model.
	 */
	public function actionProfile()
	{
		$model = $this->loadUser();
        $serviceModel = Service::model()->findAllByAttributes(array(
            'user_id'=>$model->id
        ));
        $services = array();
        foreach ($serviceModel as $row) {
            $services[] = $row->service_name;
        }

	    $this->render('profile',array(
	    	'model'=>$model,
			'profile'=>$model->profile,
            'services'=>$services,
	    ));
	}


	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionEdit()
	{
		$model = $this->loadUser();
		$profile=$model->profile;
        if(!$modelphone = UserPhones::model()->find(array(
            'select'=>'*',
            'condition'=>'u_id = '.$model->id,
            'order'=>'ph_id ASC'
        )))
        {
            $modelphone = new UserPhones();
            $modelphone->u_id = Yii::app()->user->id;
            $modelphone->date_add = time();
        }

//deb::dump($modelphone->attributes);
        $old_email = $model->email;

        // Страны
        $countries = Countries::model()->findAll(array('order'=>'sort_number'));
        $countries_array = array();
        $mask_array = array();
        foreach($countries as $country)
        {
            $countries_array[$country->c_id] = $country->name . " (+".$country->phone_kod.")";
            $mask_array[$country->c_id] = UserPhones::PhoneMaskGenerate($country->phone_kod);
        }

		// ajax validator
		if(isset($_POST['ajax']) && $_POST['ajax']==='profile-form')
		{
			echo UActiveForm::validate(array($model, $profile, $modelphone));
			Yii::app()->end();
		}
		
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$profile->attributes=$_POST['Profile'];
            $modelphone->attributes=$_POST['UserPhones'];

            $modelphone->validate();

			if($model->validate()&&$profile->validate()&&$modelphone->validate()) {
				$model->save();
				$profile->save();

                if($modelphone->ph_id > 0)
                {
                    $old_modelphone = UserPhones::model()->findByPk($modelphone->ph_id);
                    if($old_modelphone->phone != $modelphone->phone)
                    {
                        $modelphone->verify_tag = 0;
                    }
                    $modelphone->save();
                }

                if($modelphone->ph_id == 0 && strlen($modelphone->phone) > 9)
                {
                    unset($modelphone->ph_id);
                    $modelphone->save();
                }


                if ($old_email != $model->email){
                    $model->activkey=UserModule::encrypting(microtime().rand(12345, 987654));
                    $model->email_status=0;
                    $activation_url = $this->createAbsoluteUrl('/user/activation/emailactivate',array("activkey" => $model->activkey, "email" => $model->email));
/*
                    UserModule::sendMail($model->email,UserModule::t("Вы поменяли e-mail на {site_name}",array('{site_name}'=>Yii::app()->name)),UserModule::t("Пожалуйста, подтвердите его перейдя по ссылке {activation_url}",array('{activation_url}'=>$activation_url)));
*/

                    $emessage = $this->renderFile(Yii::app()->basePath.'/data/mailtemplates/emailchange.php',
                        array(
                            'user_email'=>$model->email,
                            'activation_url'=>$activation_url
                        ),
                        true);

                    $result = BaraholkaMailer::SendSmtpMail(Yii::app()->params['smtp1_connect_data'], array(
                        'mailto'=>$model->email,
                        'nameto'=>$profile->first_name,
                        'html_tag'=>true,
                        'subject'=>"Смена e-mail на сайте baraholka.ru",
                        'message'=>$emessage
                    ));


                    $model->save();

                    $this->render('/user/message',array('title'=>UserModule::t("Смена e-mail"),'content'=>UserModule::t("Ваш профиль сохранен. На указанный e-mail выслано письмо со ссылкой для подтверждения.")));
                    Yii::app()->end();
                }

                Yii::app()->user->updateSession();
				Yii::app()->user->setFlash('profileMessage',UserModule::t("Changes is saved."));
				$this->redirect(array('/user/profile'));


			} else $profile->validate();
		}

        $params['is_social_email'] = User::isSocialDefaultEmail();
        $this->render('edit',array(
			'model'=>$model,
			'profile'=>$profile,
            'params'=>$params,
            'modelphone'=>$modelphone,
            'countries_array'=>$countries_array,
            'mask_array'=>$mask_array,
		));
	}
	
	/**
	 * Change password
	 */
	public function actionChangepassword() {
		$model = new UserChangePassword;
		if (Yii::app()->user->id) {

            $usermodel = Yii::app()->controller->module->user();
            //deb::dump(User::checkSetPassword(Yii::app()->user->id));
            if (User::checkSetPassword(Yii::app()->user->id)){
                // ajax validator
                if(isset($_POST['ajax']) && $_POST['ajax']==='changepassword-form')
                {
                    echo UActiveForm::validate($model);
                    Yii::app()->end();
                }

                if(isset($_POST['UserChangePassword'])) {
                    $model->attributes=$_POST['UserChangePassword'];
                    if($model->validate()) {
                        $new_password = User::model()->notsafe()->findbyPk(Yii::app()->user->id);
                        $new_password->password = UserModule::encrypting($model->password);
                        $new_password->activkey=UserModule::encrypting(microtime().$model->password);
                        $new_password->save();
                        Yii::app()->user->setFlash('profileMessage',UserModule::t("New password is saved."));
                        $this->redirect(array("profile"));
                    }
                }
                $this->render('changepassword',array('model'=>$model));
            }
            else{
                if ($usermodel->email_status){

                    $form = new UserRecoveryForm;
                    $form->login_or_email = $usermodel->email;
                    $action = $this->createUrl('/user/recovery/recovery');
                    $this->render('changebutton', array('form'=>$form, 'action'=>$action));
                }
                else
                {
                    $this->render('/user/message',array('title'=>UserModule::t("Востановление пароля"),'content'=>UserModule::t("Восстановление пароля невозможно, потому что e-mail в вашем аккаунте не был подтвержден.")));
                    Yii::app()->end();
                }

            }
	    }
	}


    /* Метод для удаления записи из таблицы tbl_service */
    public function actionDeleteService(){
        $service = Service::model()->findByAttributes(array(
            'service_name'=>Yii::app()->request->getQuery('service'),
            'user_id'=>Yii::app()->user->id,
        ));
        $service->delete();
        $this->redirect(array('/user/profile'));
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
	 */
	public function loadUser()
	{
		if($this->_model===null)
		{
			if(Yii::app()->user->id)
				$this->_model=Yii::app()->controller->module->user();
			if($this->_model===null)
				$this->redirect(Yii::app()->controller->module->loginUrl);
		}
		return $this->_model;
	}
}