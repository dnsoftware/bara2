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



    // Импорт старой базы пользователей
    public function actionImportOldUserBase()
    {
        $connection = Yii::app()->db_old;
        //$connection=new CDbConnection('mysql:host=localhost;dbname=baraholka', 'baraholka', 'baraholka');
        $connection->tablePrefix = 'ohtbsfvre_';

        $date_end = time() - 86400*30;

        /**************** Чистка мертвых юзеров *******************
        $sql = "DELETE FROM
                        ". $connection->tablePrefix . "users
                        WHERE activate = 0 AND registered < '$date_end'
                        ";
        deb::dump($sql);
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();

        ************************************/


        $this->render("importolduserbase");

    }


    // Старт импорта старой базы юзеров
    public function actionStartImportUserbase()
    {
        die('Уже импортировано, если нужно снова - уберите die() в SupportController');
        // Плюс выставить регулярку d User.php чтобы пропускаля все Email

        $connection = Yii::app()->db_old;

        $sql = "SELECT * FROM
                ". $connection->tablePrefix . "users u
                WHERE id > 200000 AND id <=250000 AND login <> 'admin'
                ORDER BY id

                ";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        while(($row = $dataReader->read())!==false)
        {
            /*
            if(preg_match('|[^a-zA-Z0-9_.\-@ $,+\[/&*>\']+|siU', $row['login'], $match))
            {
                //deb::dump($row);
            }
            */

            $new = new User();

            $new->id = $row['id'];
            $new->user_type = 'p';
            if($row['name'] == '')
            {
                $row['name'] = $row['login'];
            }
            $new->privat_name = $row['name'];
            $new->username = $row['login'];
            $new->password = $row['pass'];
            $new->email = $row['user_email'];
            $new->status = 1;
            $new->create_at = date('Y-m-d H:i:s', $row['registered']);
            $new->lastvisit_at = date('Y-m-d H:i:s', time());
            $new->email_status = $row['activate'];
            $new->old_base_tag = 1;

            $new->save();
            $errors = $new->getErrors();
            if(count($errors) > 0)
            {
                foreach($errors as $ekey=>$eval)
                {
                    if($ekey == 'email')
                    {
                        $new->email = str_replace('@', '_dubl'.$row['id'].'@', $new->email);
                        $new->save();
                    }
                    else
                    {
                        deb::dump($errors);
                        deb::dump($new->attributes);
                    }
                }
                //die();
            }

        }
    }


    public function actionImportOldAdvertsmenu()
    {
        // Рубрика старой барахолки
        $connection = Yii::app()->db_old;

        $sql = "SELECT * FROM
                ". $connection->tablePrefix . "rubriks
                WHERE 1
                ORDER BY parent_id, r_id

                ";

        $rubold_array = array();
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        while(($row = $dataReader->read())!==false)
        {
            if ($row['parent_id']==0)
            {
                $rubold_array[$row['r_id']]['parent'] = $row;
            }
            else
            {
                $rubold_array[$row['parent_id']]['childs'][$row['r_id']] = $row;
            }

        }

        $rub_array = Rubriks::get_rublist();

        $this->render("importoldadvertsmenu", array(
            'rubold_array'=>$rubold_array,
            'rub_array'=>$rub_array
        ));

    }



    // Процесс импорта в базу
    public function actionImportOldAdverts()
    {
        $oldbase = $_POST['oldbase'];
        $mainblock = $_POST['mainblock'];
        $addfield = $_POST['addfield'];

        $props_data = array();
        if(isset($addfield) && count($addfield) > 0)
        {
            foreach($addfield as $akey=>$aval)
            {
                if(!is_array($aval))
                {
                    $props_data[$akey] = $aval;
                }
            }
        }


        // Получаем по выбранным критериям данные из старой базы
        $connection = Yii::app()->db_old;
        $sql = "SELECT n.*, t.name townname, r.name regionname, c.name countryname
                FROM
                ". $connection->tablePrefix . "notice n,
                ". $connection->tablePrefix . "towns t,
                ". $connection->tablePrefix . "regions r,
                ". $connection->tablePrefix . "countries c
                WHERE n.r_id = ".$oldbase['rubold']."
                    AND n.t_id = t.t_id AND n.region_id = r.r_id AND n.c_id = c.c_id
                ";

        $adv_old_array = array();
        $temp_towns = array();
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        while(($row = $dataReader->read())!==false)
        {
            $adv_old_array[$row['n_id']] = $row;

            // Находим соответствия между старыми городами и новыми
            if($town = Towns::model()->findByAttributes(array('old_t_id'=>$row['t_id'])))
            {
                $temp_towns[$row['t_id']] = $town;
            }
            else
            {
                echo "Не найдено соответствие города! Внесите изменения в базу!<br>";
                deb::dump("Старый код города: ".$row['t_id']. " - " . $row['townname']);
                die();
            }

        }
//deb::dump($temp_towns);
//die();

        // Получаем свойства объяв в выбранной рубрике новой базы
        $rubriks_props = RubriksProps::getAllProps($mainblock['r_id']);

        $props_array = array();
        foreach($props_data as $pkey=>$pval)
        {
            $props_array[$rubriks_props[$pkey]->rp_id] = $pval;
        }
//deb::dump($props_array);
//die();

        // Формирование записи объявления
        foreach($adv_old_array as $akey=>$aval)
        {
            $newadv = new Notice();

            $newadv->n_id = $aval['n_id'];
            $newadv->u_id = $aval['u_id'];
            $newadv->r_id = $mainblock['r_id'];
            $rubrow = Rubriks::model()->findByPk($mainblock['r_id']);
            $newadv->parent_r_id = $rubrow->parent_id;

            /*
            $newadv->t_id = 0;
            $newadv->reg_id = 0;
            $newadv->c_id = 0;
            */

            $newadv->t_id = $temp_towns[$aval['t_id']]->t_id;
            $newadv->reg_id = $temp_towns[$aval['t_id']]->reg_id;
            $newadv->c_id = $temp_towns[$aval['t_id']]->c_id;

            $newadv->date_add = $aval['date_add'];
            $newadv->date_lastedit = $aval['date_lastedit'];
            $newadv->date_expire = $aval['date_expire'];
            $newadv->expire_period = ceil(($aval['date_expire'] - $aval['date_add'])/86400);
            $newadv->client_name = $aval['client_name'];
            $newadv->client_email = $aval['client_email'];
            $newadv->client_phone_c_id = Yii::app()->params['russia_id'];
            $newadv->client_phone = $aval['client_phone'];
            $newadv->phone_search = '';
            $newadv->title = $aval['title'];
            $newadv->notice_text = $aval['notice_text'];
            $newadv->active_tag = $aval['active_tag'];
            $newadv->verify_tag = $aval['verify_tag'];
            $newadv->date_deactive = $aval['date_deactive'];
            $newadv->deactive_moder_id = $aval['deactive_moder_id'];
            $newadv->moder_tag = $aval['moder_tag'];
            $newadv->date_moder = $aval['date_moder'];
            $newadv->moder_id = $aval['moder_id'];
            $newadv->daynumber_id = $aval['daynumber_id'];
            $newadv->views_count = $aval['views_count'];
            $newadv->deleted_tag = $aval['deleted_tag'];
            $newadv->date_delete = $aval['date_delete'];
            $newadv->reject_reason = $aval['reject_reason'];
            $newadv->otkaz_id = $aval['otkaz_id'];
            $newadv->date_sort = $aval['date_sort'];
            $newadv->from_ip = $aval['from_ip'];
            $newadv->moder_counted_tag = $aval['moder_counted_tag'];
            $newadv->video_youtube = '';
            $newadv->cost = 0;
            $newadv->cost_valuta = 'RUB';
            $newadv->props_xml = '';
            $newadv->counter_total = $aval['views_count'];
            $newadv->counter_daily = 0;
            $newadv->counter_date = time();
            $newadv->old_base_tag = 1;

            $newadv->checksum = Notice::GetChecksum($newadv);

            $newadv->save();

            $errors = $newadv->getErrors();
            if(count($errors) > 0)
            {
                foreach($errors as $ekey=>$eval)
                {

                }

                deb::dump($errors);
                deb::dump($newadv->attributes);
                die();
            }
            else
            {
                AdvertController::PropsXmlGenerate($newadv->n_id);

                foreach($props_array as $pkey=>$pval)
                {
                    $propmodel = new NoticeProps();
                    $propmodel->n_id = $newadv->n_id;
                    $propmodel->rp_id = $pkey;
                    $propmodel->ps_id = $pval;
                    $propmodel->old_base_tag = $pval;
                    $propmodel->save();

                    $prop_errors = $propmodel->getErrors();
                    if(count($errors) > 0)
                    {
                        deb::dump($errors);
                        die();
                    }
                }
            }

            //deb::dump($newadv->attributes);
        }



        //deb::dump($props_array);

        //deb::dump($rubriks_props);

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