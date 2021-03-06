<?php

class RecoveryController extends Controller
{
	public $defaultAction = 'recovery';
	
	/**
	 * Recovery password
	 */
	public function actionRecovery () {
		$form = new UserRecoveryForm;
		if (Yii::app()->user->id && User::checkSetPassword(Yii::app()->user->id)) {
		    	$this->redirect(Yii::app()->controller->module->returnUrl);
		    } else {
				$email = ((isset($_GET['email']))?$_GET['email']:'');
				$activkey = ((isset($_GET['activkey']))?$_GET['activkey']:'');
				if ($email&&$activkey) {
					$form2 = new UserChangePassword;
		    		$find = User::model()->notsafe()->findByAttributes(array('email'=>$email));
		    		if(isset($find)&&$find->activkey==$activkey) {
			    		if(isset($_POST['UserChangePassword'])) {
							$form2->attributes=$_POST['UserChangePassword'];
							if($form2->validate()) {
								$find->password = Yii::app()->controller->module->encrypting($form2->password);
								$find->activkey=Yii::app()->controller->module->encrypting(microtime().$form2->password);
								if ($find->status==0) {
									$find->status = 1;
								}
								$find->save();
								Yii::app()->user->setFlash('recoveryMessage',UserModule::t("New password is saved."));
								$this->redirect(Yii::app()->controller->module->recoveryUrl);
							}
						} 
						$this->render('changepassword',array('form'=>$form2));
		    		} else {
		    			Yii::app()->user->setFlash('recoveryMessage',UserModule::t("Incorrect recovery link."));
						$this->redirect(Yii::app()->controller->module->recoveryUrl);
		    		}
		    	} else {
			    	if(isset($_POST['UserRecoveryForm'])) {
			    		$form->attributes=$_POST['UserRecoveryForm'];
			    		if($form->validate()) {
			    			$user = User::model()->notsafe()->findbyPk($form->user_id);

                            if ($user->email_status){
                                $activation_url = 'http://' . $_SERVER['HTTP_HOST'].$this->createUrl(implode(Yii::app()->controller->module->recoveryUrl),array("activkey" => $user->activkey, "email" => $user->email));

/*
                                $subject = UserModule::t("You have requested the password recovery site {site_name}",
                                    array(
                                        '{site_name}'=>Yii::app()->name,
                                    ));
                                $message = UserModule::t("You have requested the password recovery site {site_name}. To receive a new password, go to {activation_url}.",
                                    array(
                                        '{site_name}'=>Yii::app()->name,
                                        '{activation_url}'=>$activation_url,
                                    ));

                                UserModule::sendMail($user->email,$subject,$message);
*/

                                $emessage = $this->renderFile(Yii::app()->basePath.'/data/mailtemplates/recoverypassword.php',
                                    array(
                                        'user_privat_name'=>$user->privat_name,
                                        'user_email'=>$user->email,
                                        'recovery_link'=>$activation_url
                                    ),
                                    true);

                                $result = BaraholkaMailer::SendSmtpMail(Yii::app()->params['smtp1_connect_data'], array(
                                    'mailto'=>$user->email,
                                    'nameto'=>$user->privat_name,
                                    'html_tag'=>true,
                                    'subject'=>"Запрос на восстановление пароля",
                                    'message'=>$emessage
                                ));


                                Yii::app()->user->setFlash('recoveryMessage',UserModule::t("Please check your email. An instructions was sent to your email address."));
                                $this->refresh();
                            }
                            else
                            {
                                $this->render('/user/message',array('title'=>UserModule::t("Востановление пароля"),'content'=>UserModule::t("Восстановление пароля невозможно, потому что указанный e-mail не был подтвержден.")));
                                Yii::app()->end();
                            }

			    		}
			    	}
		    		$this->render('recovery',array('form'=>$form));
		    	}
		    }
	}

}