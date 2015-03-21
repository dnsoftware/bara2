<?php
/**
 * EAuthUserIdentity class file.
 *
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://github.com/Nodge/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * EAuthUserIdentity is a base User Identity class to authenticate with EAuth.
 *
 * @package application.extensions.eauth
 */
class EAuthUserIdentity extends CBaseUserIdentity {

	const ERROR_NOT_AUTHENTICATED = 3;
    const ERROR_NOT_USER_SAVE=5;
    const ERROR_NOT_USER_SERVICE_SAVE=6;

	/**
	 * @var EAuthServiceBase the authorization service instance.
	 */
	protected $service;

	/**
	 * @var string the unique identifier for the identity.
	 */
	protected $id;

	/**
	 * @var string the display name for the identity.
	 */
	protected $name;

	/**
	 * Constructor.
	 *
	 * @param EAuthServiceBase $service the authorization service instance.
	 */
	public function __construct($service) {
		$this->service = $service;
	}

	/**
	 * Authenticates a user based on {@link service}.
	 * This method is required by {@link IUserIdentity}.
	 *
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
//deb::dump($this->service);
//deb::dump($this->service->id);
//die();
        //$serviceModel = Service::model()->findByPk($this->service->id, $this->serviceName);
        $serviceModel = Service::model()->find('identity=:identity AND service_name=:service_name',
                                                array(':identity' => $this->service->id,
                                                      ':service_name' => $this->service->serviceName
                                                     )
                                              );
        /* Если в таблице tbl_service нет записи с таким id,
           значит надо создать аккаунт и привязать к нему сервис.
        - заводится запись в главную таблицу users c логином = user000000, где вместо нулей уникальный незанятый номер.
          т.е. для генерации номера ищем select login WHERE login REGEXP 'user[0-9]{6}' order DESC limit 0,1 и инкрементируем.
        */
        if($serviceModel === null)
        {
            if ($this->service->isAuthenticated)
            {
                if(Yii::app()->user->isGuest){
                    $usermodel = new User;
                    $usermodel->username = User::getNext_service_user_id();
                    $usermodel->email = $usermodel->username."@".$_SERVER['HTTP_HOST'];
                    $usermodel->status = 1;
                    $usermodel->setCreatetime(time());
                    $usermodel->setLastvisit(time());

                    if ($usermodel->save())
                    {
                        $profile = new Profile;

                        $profile->first_name = $this->service->getAttribute('name');
                        $profile->last_name = '';
                        $profile->user_id = $usermodel->id;
                        $profile->save();

                        $service = new Service();
                        $service->identity = $this->service->id;
                        $service->service_name = $this->service->serviceName;
                        $service->user_id = $usermodel->id;

                        if ($service->save())
                        {
                            $this->id = $usermodel->id;
                            $this->name = $usermodel->username;
                            $this->setState('service', $this->service->serviceName);

                            $this->errorCode = self::ERROR_NONE;
                        }
                        else{
                            $this->errorCode = self::ERROR_NOT_USER_SERVICE_SAVE;
                        }
                    }
                    else{
                        $this->errorCode = self::ERROR_NOT_USER_SAVE;
                    }
                }
                else{
                    $this->errorCode = self::ERROR_NONE;
                }


            }
            else {
                $this->errorCode = self::ERROR_NOT_AUTHENTICATED;
            }
        }
        /* Если запись есть, то используем данные из
        таблицы tbl_users, используя связь в модели Service */
        else {
            $usermodel = User::model()->findByPk($serviceModel->user_id);

            if ($usermodel !== null)
            {
                $this->id = $usermodel->id;
                $this->name = $usermodel->username;
                $this->setState('service', $serviceModel->service_name);

                $this->errorCode = self::ERROR_NONE;
            }
            else{
                $this->errorCode = self::ERROR_NOT_AUTHENTICATED;
            }

        }


		return !$this->errorCode;
	}

	/**
	 * Returns the unique identifier for the identity.
	 * This method is required by {@link IUserIdentity}.
	 *
	 * @return string the unique identifier for the identity.
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Returns the display name for the identity.
	 * This method is required by {@link IUserIdentity}.
	 *
	 * @return string the display name for the identity.
	 */
	public function getName() {
		return $this->name;
	}
}
