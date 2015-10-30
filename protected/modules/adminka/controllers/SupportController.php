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

//deb::dump($props_data);
//die();
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
            if($inbaserow = Notice::model()->findByPk($aval['n_id']))
            {
                $inbaserow->n_id = $aval['n_id'];
                $inbaserow->u_id = $aval['u_id'];
                $inbaserow->r_id = $mainblock['r_id'];
                $rubrow = Rubriks::model()->findByPk($mainblock['r_id']);
                $inbaserow->parent_r_id = $rubrow->parent_id;
                $inbaserow->old_r_id = $oldbase['rubold'];
                $inbaserow->save();

                $errors = $inbaserow->getErrors();
                if(count($errors) > 0)
                {
                    deb::dump($errors);
                    deb::dump($inbaserow->attributes);
                    die();
                }
                else
                {
                    NoticeProps::model()->deleteAll('n_id = :n_id', array(':n_id'=>$inbaserow->n_id));
                    AdvertController::PropsXmlGenerate($inbaserow->n_id);

                    foreach($props_array as $pkey=>$pval)
                    {
                        $propmodel = new NoticeProps();
                        $propmodel->n_id = $inbaserow->n_id;
                        $propmodel->rp_id = $pkey;
                        $propmodel->ps_id = $pval;
                        $propmodel->old_base_tag = 1;
                        $propmodel->save();

                        $prop_errors = $propmodel->getErrors();
                        if(count($errors) > 0)
                        {
                            deb::dump($errors);
                            die();
                        }
                    }

                    deb::dump("Запись ".$inbaserow->n_id." обновлена");
                }


            }
            else
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
                $newadv->old_client_phone = $aval['client_phone'];
                //$newadv->client_phone = $aval['client_phone'];
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
                $newadv->old_r_id = $oldbase['rubold'];

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
                        $propmodel->old_base_tag = 1;
                        $propmodel->save();

                        $prop_errors = $propmodel->getErrors();
                        if(count($errors) > 0)
                        {
                            deb::dump($errors);
                            die();
                        }
                    }
                }
            }

            //deb::dump($newadv->attributes);
        }



        //deb::dump($props_array);

        //deb::dump($rubriks_props);

    }


    // Корректировка телефонов в старом формате
    public function actionOldphonescorrect()
    {
        $adverts = Notice::model()->findAll(array(
            'select'=>'*',
            'condition'=>'old_base_tag = 1 AND client_phone = "" '
        ));

        $plus = 0;
        foreach($adverts as $akey=>$aval)
        {
            $sph = preg_replace('|[ \-\(\)]*|siU', '', $aval->old_client_phone);

            // Россия
            $solidnum = '';
            $phone_search = '';
            if(strlen($sph) == 11
                && $sph[0] == '8'
                && $aval->c_id == Yii::app()->params['russia_id'])
            {
                $solidnum = "(".$sph[1].$sph[2].$sph[3].") ".$sph[4].$sph[5].$sph[6]."-".$sph[7].$sph[8]."-".$sph[9].$sph[10];
                echo "----------";
            }
            else
            if(strlen($sph) == 12
                && $sph[0] == '+'
                && $sph[1] == '+7'
                && $aval->c_id == Yii::app()->params['russia_id'])
            {
                $solidnum = "(".$sph[2].$sph[3].$sph[4].") ".$sph[5].$sph[6].$sph[7]."-".$sph[8].$sph[9]."-".$sph[10].$sph[11];
                echo "----------";
            }
            else
            if(strlen($sph) == 10
                && $aval->c_id == Yii::app()->params['russia_id'])
            {
                $solidnum = "(".$sph[0].$sph[1].$sph[2].") ".$sph[3].$sph[4].$sph[5]."-".$sph[6].$sph[7]."-".$sph[8].$sph[9];
                echo "----------";
            }
            else
            if(strlen($sph) == 11
                && $sph[0] == '7'
                && $aval->c_id == Yii::app()->params['russia_id'])
            {
                $solidnum = "(".$sph[1].$sph[2].$sph[3].") ".$sph[4].$sph[5].$sph[6]."-".$sph[7].$sph[8]."-".$sph[9].$sph[10];
                echo "----------";
            }
            // Украина
            else
            if(1)
            {
                // Доработать Украину и прочие страны
                deb::dump($aval->c_id);
            }

            $phone_search = preg_replace('|[\(\) \-]|siU', '', $solidnum);

            echo $sph." - ".$solidnum." - ".$phone_search."<br>";

            $aval->client_phone = $solidnum;
            $aval->phone_search = $phone_search;
            $aval->save();

        }

    }


    public function actionImageimportmenu()
    {

        $this->render('imageimportmenu');
    }


    public function actionImageimport()
    {
        // Получение всех кодов rp_id со свойством "photoblock" для рубрик
        $temp = RubriksProps::model()->with('props_sprav')->findAll(array(
            'select'=>'*',
            'condition'=>'vibor_type = "photoblock"'
        ));
//deb::dump($temp);
        $rubriks_props_photoblock = array();
        foreach($temp as $tkey=>$tval)
        {
            $rubriks_props_photoblock[$tval->r_id] = $tval;
        }
//deb::dump($rubriks_props_photoblock);

        $notices = Notice::model()->findAll(array(
            'select'=>'*',
            'condition'=>'old_base_tag = 1 AND img_import_tag = 0 ',    //AND n_id=1198638
            'order'=>'n_id ',
            'limit'=>2000
        ));

        foreach($notices as $nkey=>$nval)
        {
            //deb::dump($nval);
            if($noticeimages = NoticeImagesOld::model()->findAll(array(
                'select'=>'*',
                'condition'=>'n_id = '.$nval->n_id,
                'order'=>'n_id ASC, titul_tag DESC, fotonumber ASC',
            )))
            {
                $folder = 'tempphotos';
                $files_array = array();
                foreach($noticeimages as $ikey=>$ival)
                {
                    //deb::dump($ival);
                    $tofile = md5($ival->filename).".".$ival->file_ext;
                    $tofile_small = md5($ival->filename)."_thumb.".$ival->file_ext;
                    $output_dir = Yii::app()->basePath."/../".$folder."/";
                    if(copy('http://baraholka.ru/imgnot/'.$ival->filename.".".$ival->file_ext, $output_dir.$tofile))
                    {
                        $files_array[] = $tofile;

                        // Копируем маленькое превью
                        copy('http://baraholka.ru/imgnot/'.$ival->filename."_s.".$ival->file_ext, $output_dir.$tofile_small);

                        // дублируем в качестве исходника
                        $original_photo = $tofile;

                        /* Наложение водяного знака и генерация картинок разных размеров */
                        $fileName = $original_photo;
                        $temp = explode(".", $tofile);
                        $filename_root = $temp[0];
                        $filename_ext = $temp[1];

                        $img = new CImageHandler();
                        $full_filename = $output_dir.$fileName;
                        $img->load($full_filename);

                        $orient = 'h';
                        if($img->getWidth() < $img->getHeight())
                        {
                            $orient = 'v';
                        }

                        // Резайз до самой большой картинки
                        $img_width = $img->getWidth();
                        $img_height = $img->getHeight();
                        if($orient == 'h')
                        {
                            $img->resize(Notice::HUGE_WIDTH, false);
                        }
                        else
                        {
                            $img->resize(false, Notice::HUGE_HEIGHT);
                        }

                        $img->save($output_dir.$filename_root."_huge.".$filename_ext);

                        // Резайз до средней картинки
                        $img->reload();
                        $img_width = $img->getWidth();
                        $img_height = $img->getHeight();
                        if($orient == 'h')
                        {
                            $img->resize(Notice::BIG_PREVIEW_WIDTH, false);
                        }
                        else
                        {
                            $img->resize(false, Notice::BIG_PREVIEW_HEIGHT);
                        }
                        $img->save($output_dir.$filename_root."_big.".$filename_ext);

                        // Средняя превьюшка
                        $img->reload();
                        if($orient == 'h')
                        {
                            $img->resize(Notice::MEDIUM_PREVIEW_WIDTH, false);
                        }
                        else
                        {
                            $img->resize(false, Notice::MEDIUM_PREVIEW_HEIGHT);
                        }

                        $img->save($output_dir.$filename_root."_medium.".$filename_ext);

                        // Маленькая превьюшка
                        $img->load($output_dir.$tofile_small);
                        if($orient == 'h')
                        {
                            $img->resize(Notice::PREVIEW_WIDTH, false);
                        }
                        else
                        {
                            $img->resize(false, Notice::PREVIEW_HEIGHT);
                        }

                        $img->save($output_dir.$tofile_small);



                    }
                }


                $notice_photo_rp_id = $rubriks_props_photoblock[$nval->r_id]->rp_id;
                $notice_photo_ps_id = $rubriks_props_photoblock[$nval->r_id]->props_sprav[0]->ps_id;
                $hand_input_value = '';
                if(count($files_array) > 0)
                {
                    $hand_input_value = implode(";", $files_array).";";
                }

                if($notice_prop = NoticeProps::model()->findByAttributes(array(
                    'n_id'=>$nval->n_id,
                    'rp_id'=>$notice_photo_rp_id
                )))
                {
                    $notice_prop->ps_id = $notice_photo_ps_id;
                    $notice_prop->hand_input_value = $hand_input_value;
                    $notice_prop->old_base_tag = 1;
                    $notice_prop->save();
                }
                else
                {
                    $notice_prop = new NoticeProps();
                    $notice_prop->n_id = $nval->n_id;
                    $notice_prop->rp_id = $notice_photo_rp_id;
                    $notice_prop->ps_id = $notice_photo_ps_id;
                    $notice_prop->hand_input_value = $hand_input_value;
                    $notice_prop->old_base_tag = 1;
                    $notice_prop->save();

                }

                deb::dump($nval->n_id);
            }

            $nval->img_import_tag = 1;
            $nval->save();

            // Перегенерируем xml свойства
            AdvertController::PropsXmlGenerate($nval->n_id);


            //deb::dump($noticeimages);
        }



        //$this->render('imageimport');
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