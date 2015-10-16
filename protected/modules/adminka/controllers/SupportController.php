<?php

class SupportController extends Controller
{
    public $sendmail_list = array(
        'medzhis@gmail.com',
        'ddaemon@mail.ru',
        'boris@kostyrko.ru',
        'dvnazarov@dn.farlep.net'
    );

	public function actionIndex()
	{
		$this->render('index');
	}



    public function actionTestmail()
    {
        $this->render('testmail');
    }


    public function actionSendmail()
    {
        $message = $_POST['message'];
/*
        $message = $this->renderFile(Yii::app()->basePath.'/data/mailtemplates/registration.php',
            array(
                'user_email'=>'medzhis@gmail.com',
                'link_expire_date'=>date("d.m.Y", time()+86400*30),
                'activation_link'=>'http://baraholka.ru/user/activation/activation?activkey=dkshfkgajsgdjhasgdas&email=test2015@mail'
            ),
            true);
*/

        $result = BaraholkaMailer::SendSmtpMail(Yii::app()->params['smtp1_connect_data'], array(
            'mailto'=>$_POST['mailto'],
            'nameto'=>$_POST['nameto'],
            'html_tag'=>true,
            'subject'=>$_POST['subject'],
            'message'=>$message
        ));





        if($result != 'ok') {
            echo $result;
        } else {
            echo 'ok';
        }

        //UserModule::sendMailFrom($_POST['mailto'], $_POST['subject'], $_POST['message'], "baraholka.ru <".Yii::app()->params['noreplyEmail'].">");

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