<?php

class RegistrationController extends Controller
{
	public $defaultAction = 'registration';
	
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'RegCCaptchaAction',
				'backColor'=>0xFFFFFF,
                'height'=>35,
                'padding'=>0,
                'transparent'=>true,
                //'fontFile'=>'./fonts/agaaler.ttf'

            ),
		);
	}
	/**
	 * Registration user
	 */
    public function actionRegistration() {
        $model = new RegistrationForm;
        $profile=new Profile;
        $profile->regMode = true;
        $modelphone = new UserPhones;

        // Страны
        $countries = Countries::model()->findAll(array('order'=>'sort_number'));
        $countries_array = array();
        $mask_array = array();
        foreach($countries as $country)
        {
            $countries_array[$country->c_id] = $country->name . " (+".$country->phone_kod.")";
            $mask_array[$country->c_id] = UserPhones::PhoneMaskGenerate($country->phone_kod);
        }

        //$this->createAction('captcha')->getVerifyCode(true);

        // ajax validator
        if(isset($_POST['ajax']) && $_POST['ajax']==='registration-form')
        {
            echo UActiveForm::validate(array($model,$profile,$modelphone));
            Yii::app()->end();
        }

        if (Yii::app()->user->id) {
            $this->redirect(Yii::app()->controller->module->profileUrl);
        } else {
            if(isset($_POST['RegistrationForm'])) {
                $model->attributes=$_POST['RegistrationForm'];
                /* Патч исключения логина из данных подаваемых при регистрации*/
                $model->username = str_replace("@", "_", $model->email);
                /************* Конец патча исключения логина **************/

                $profile->attributes=((isset($_POST['Profile'])?$_POST['Profile']:array()));
                $modelphone->attributes=$_POST['UserPhones'];
                if($model->validate()&&$profile->validate())
                {
                    $soucePassword = $model->password;
                    $model->activkey=UserModule::encrypting(microtime().$model->password);
                    $model->password=UserModule::encrypting($model->password);
                    $model->verifyPassword=UserModule::encrypting($model->verifyPassword);
                    $model->superuser=0;
                    $model->status=((Yii::app()->controller->module->activeAfterRegister)?User::STATUS_ACTIVE:User::STATUS_NOACTIVE);

                    if ($model->save())
                    {
                        $profile->user_id = $model->id;
                        $profile->save();

                        if(strlen($modelphone->phone) >= 10)
                        {
                            $modelphone->u_id = $model->id;
                            $modelphone->date_add = time();
                            $modelphone->save();
                        }

                        if (Yii::app()->controller->module->sendActivationMail) {
                            $activation_url = $this->createAbsoluteUrl('/user/activation/activation',array("activkey" => $model->activkey, "email" => $model->email));
                            UserModule::sendMail($model->email,UserModule::t("You registered from {site_name}",array('{site_name}'=>Yii::app()->name)),UserModule::t("Please activate you account go to {activation_url}",array('{activation_url}'=>$activation_url)));
                        }

                        if ((Yii::app()->controller->module->loginNotActiv||(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false))&&Yii::app()->controller->module->autoLogin) {
                            $identity=new UserIdentity($model->username,$soucePassword);
                            $identity->authenticate();
                            Yii::app()->user->login($identity,0);
                            $this->redirect(Yii::app()->controller->module->returnUrl);
                        } else {
                            if (!Yii::app()->controller->module->activeAfterRegister&&!Yii::app()->controller->module->sendActivationMail) {
                                Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Contact Admin to activate your account."));
                            } elseif(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false) {
                                Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please {{login}}.",array('{{login}}'=>CHtml::link(UserModule::t('Login'),Yii::app()->controller->module->loginUrl))));
                            } elseif(Yii::app()->controller->module->loginNotActiv) {
                                Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email or login."));
                            } else {
                                Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email."));
                            }
                            $this->refresh();
                        }
                    }
                } else $profile->validate();
            }

            if($model->user_type == '')
            {
                $model->user_type = 'p';
            }

            $this->render('/user/registration',array('model'=>$model,'profile'=>$profile,
                'mask_array'=>$mask_array, 'countries_array'=>$countries_array, 'modelphone'=>$modelphone));
        }
    }


    // Если при регистрации введен телефон проверяем его наличие в базе
    public function checkPhoneNoempty($c_id, $phone)
    {
        if(strlen(trim($phone)) > 10)
        {
            // проверяем наличие в базе
            // если в базе - сообщаем что телефон уже в базе, выход
            if($phonerow = UserPhones::model()->findByAttributes(array(
                'c_id'=>$c_id,
                'phone'=>$phone,
                'verify_tag'=>1
            )))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        return false;

    }

    // Подтверждение телефона - отправка
    public function actionCheckphonesms()
    {
        if($_POST['phone'] == '')
        {
            echo 'empty';
            return false;
        }
        //echo $_POST['phone'];
        // Задержка на отправку от одного пользователя
        if( (isset(Yii::app()->session['usercheckphone_time'])
                && (time() - Yii::app()->session['usercheckphone_time']) > 30)
            || !isset(Yii::app()->session['usercheckphone_time']) )
        {


            // проверяем наличие в базе
            // если в базе - сообщаем что телефон уже в базе, выход
            if($phonerow = UserPhones::model()->findByAttributes(array(
                'c_id'=>$_POST['c_id'],
                'phone'=>$_POST['phone'],
                'verify_tag'=>1
            )))
            {
                echo 'inbase';
            }
            else
            {
                // если не в базе - заносим в сессию и отправляем смс с кодом, тег проверенности обнуляемс
                Yii::app()->session['usercheckphone'] = $_POST['phone'];
                Yii::app()->session['usercheckphone_code'] = rand(10000, 99999);
                Yii::app()->session['usercheckphone_tag'] = 0;
                Yii::app()->session['usercheckphone_time'] = time();


                // Отправляем смс
                $to_phone_number = trim(str_replace(' ', '', Yii::app()->session['usercheckphone']));
                $to_phone_number = str_replace('-', '', $to_phone_number);
                $countryrow = Countries::model()->findByPk(intval($_POST['c_id']));
                $to_phone_number = '+'.$countryrow->phone_kod.$to_phone_number;
                $bytehand = new ByteHandApi(array(
                    'id' => Yii::app()->params['bytehand_id'],
                    'key' => Yii::app()->params['bytehand_key'],
                    'from'=> 'SMS-INFO'
                ));

                $res = $bytehand->send($to_phone_number,'Код подтверждения '.Yii::app()->session['usercheckphone_code'], 'utf-8');

                if ($res->status != 0)
                {
                    echo 'bytehand_error';
                }
                else
                {
                    Yii::app()->session['usercheckphone_message_id'] = $res->description;

                    $details = $bytehand->get_details($res->description);

                    // Заносим в лог
                    $smssend_log = new UserPhonesSmsLog();
                    $smssend_log->message_id = $res->description;
                    $smssend_log->с_id = intval($_POST['c_id']);
                    $smssend_log->phone = $_POST['phone'];
                    $smssend_log->date_add = time();
                    $smssend_log->verify_kod = Yii::app()->session['usercheckphone_code'];
                    $smssend_log->status = $details->status;
                    $smssend_log->error_code = 0;
                    $smssend_log->description = $details->description;
                    $smssend_log->posted_at = $details->posted_at;
                    $smssend_log->updated_at = $details->updated_at;
                    $smssend_log->parts = $details->parts;
                    $smssend_log->cost = $details->cost;

                    $smssend_log->save();

                    echo 'send';
                }



            }


            // если юзер залогинен


            // если юзер не  залогинен
        }
        else
        {
            echo 'timeout'.(time() - Yii::app()->session['usercheckphone_time'] - 30);
        }



    }

    // Подтверждение телефона - проверка кода подтверждения
    public function actionCheckphonekod()
    {
        if( Yii::app()->session['usercheckphone_code'] == $_POST['code']
                && Yii::app()->session['usercheckphone'] == $_POST['phone'])
        {
                Yii::app()->session['usercheckphone_tag'] = 1;
                echo 'ok';
        }
        else
        {
            echo 'bad';
        }

    }

    // Подтверждение телефона - проверка кода из смс и занесение в базу
    public function actionSetUserPhone()
    {
        // берем из сессии проверочный код, если он совпадает с введенным,
        // выставляем тег подтвержденности в единицу

    }

}