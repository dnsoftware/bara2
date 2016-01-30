<?php
/**
 * RegistrationForm class.
 * RegistrationForm is the data structure for keeping
 * user registration form data. It is used by the 'registration' action of 'UserController'.
 */
class RegistrationForm extends User {
	public $verifyPassword;
	public $verifyCode;
    public static $user_types = array(
        'p'=>'Частное лицо',
        'c'=>'Компания'
    );
    public static $blocked_email_domens = array(
        'outlook.com',
        'hotmail.com',
        'live.com'
    );


    public function rules() {
		$rules = array(
			array('user_type, password, verifyPassword, email', 'required'),
			array('password', 'length', 'max'=>128, 'min' => 4,'message' => UserModule::t("Incorrect password (minimal length 4 symbols).")),
			array('email', 'email_validate'),
			array('username', 'safe'),
			array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
			//array('verifyPassword', 'compare', 'compareAttribute'=>'password', 'message' => UserModule::t("Retype Password is incorrect.")),
			//array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),

            array('user_type', 'length', 'min'=>1),
            array('privat_name', 'privat_name_validate'),
            array('company_name', 'company_name_validate'),


        );
		if (!(isset($_POST['ajax']) && $_POST['ajax']==='registration-form')) {
			array_push($rules,array('verifyCode', 'captcha', 'allowEmpty'=>!UserModule::doCaptcha('registration')));
		}
		
		array_push($rules,array('verifyPassword', 'compare', 'compareAttribute'=>'password', 'message' => UserModule::t("Retype Password is incorrect.")));
		return $rules;
	}

    // Проверка мыла на запрещенные домены
    public function email_validate($attr, $params)
    {
        $validator = new CEmailValidator();
        if(!$validator->validateValue($this->email))
        {
            $this->addError('email', 'Электронная почта не является правильным sE-Mail адресом.');
        }
        else
        {
            $domain = trim(substr($this->email, (strpos($this->email, '@') + 1)));
            if(in_array($domain, self::$blocked_email_domens))
            {
                $this->addError('email', 'E-mail на сервере '.$domain.' не гарантирует
получения наших электронных писем. Выберите другой e-mail');
            }
        }

    }

    // Проверка имени пользователя
    public function privat_name_validate($attr, $params)
    {
        if($this->user_type == 'p' && trim($this->privat_name) == '')
        {
            $this->addError('privat_name', 'Укажите ваше имя!');
        }

    }

    // Проверка имени компании
    public function company_name_validate($attr, $params)
    {
        if($this->user_type == 'c')
        {
            if(trim($this->privat_name) == '')
            {
                $this->addError('privat_name', 'Укажите ваше имя!');
            }

            if(trim($this->company_name) == '')
            {
                $this->addError('company_name', 'Укажите название компании!');
            }
        }
    }

}