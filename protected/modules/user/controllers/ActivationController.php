<?php

class ActivationController extends Controller
{
	public $defaultAction = 'activation';

	
	/**
	 * Activation user account
	 */
	public function actionActivation () {
		$email = $_GET['email'];
		$activkey = $_GET['activkey'];
		if ($email&&$activkey) {
			$find = User::model()->notsafe()->findByAttributes(array('email'=>$email));
			if (isset($find)&&$find->status) {
			    $this->render('/user/message',array('title'=>UserModule::t("User activation"),'content'=>UserModule::t("You account is active.")));
			} elseif(isset($find->activkey) && ($find->activkey==$activkey)) {
				$find->activkey = UserModule::encrypting(microtime());
				$find->status = 1;
                $find->email_status = 1;
				$find->save();
			    $this->render('/user/message',array('title'=>UserModule::t("User activation"),'content'=>UserModule::t("You account is activated.")));
			} else {
			    $this->render('/user/message',array('title'=>UserModule::t("User activation"),'content'=>UserModule::t("Incorrect activation URL.")));
			}
		} else {
			$this->render('/user/message',array('title'=>UserModule::t("User activation"),'content'=>UserModule::t("Incorrect activation URL.")));
		}
	}


    public function actionEmailactivate () {
        $email = $_GET['email'];
        $activkey = $_GET['activkey'];
        if ($email&&$activkey) {
            $find = User::model()->notsafe()->findByAttributes(array('email'=>$email));
            if (isset($find)&&$find->email_status) {
                $this->render('/user/message',array('title'=>UserModule::t("E-mail activation"),'content'=>UserModule::t("Ваш e-mail уже активирован.")));
            } elseif(isset($find->activkey) && ($find->activkey==$activkey)) {
                $find->activkey = UserModule::encrypting(microtime());
                $find->email_status = 1;
                $find->save();
                $this->render('/user/message',array('title'=>UserModule::t("E-mail activation"),'content'=>UserModule::t("Ваш e-mail активирован.")));
            } else {
                $this->render('/user/message',array('title'=>UserModule::t("E-mail activation"),'content'=>UserModule::t("Incorrect activation URL.")));
            }
        } else {
            $this->render('/user/message',array('title'=>UserModule::t("E-mail activation"),'content'=>UserModule::t("Incorrect activation URL.")));
        }
    }



}