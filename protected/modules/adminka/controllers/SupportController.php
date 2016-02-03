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

        /*
        $users = User::model()->findAll(array(
            'select'=>'id',
            'condition'=>'id > 2'
        ));

        foreach($users as $ukey=>$uval)
        {
            if(!$search_profile = Profile::model()->findByPk($uval->id))
            {
                $profile = new Profile();
                $profile->user_id = $uval->id;
                $profile->first_name = null;
                $profile->last_name = null;
                $profile->save();
            }
        }
        */



        die('Уже импортировано, если нужно снова - уберите die() в SupportController');
        // Плюс выставить регулярку в User.php чтобы пропускаля все username

        $connection = Yii::app()->db_old;

        $sql = "SELECT * FROM
                ". $connection->tablePrefix . "users u
                WHERE id > 222829 AND login <> 'admin'
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
            else
            {
                $profile = new Profile();
                $profile->user_id = $new->id;
                $profile->first_name = null;
                $profile->last_name = null;
                $profile->save();

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

        $rub_array = Rubriks::get_rublist(true);

        $this->render("importoldadvertsmenu", array(
            'rubold_array'=>$rubold_array,
            'rub_array'=>$rub_array
        ));

    }



    // Восстановление утерянных свойств в процессе неудачного импорта
    public function actionImportRemake()
    {
        $connection = Yii::app()->db_auto;

        $notice = Notice::model()->findAll(array(
            'select'=>'*',
            'condition'=>'import_wave_number = 2',
            'limit'=>10000
        ));

        $i=0;
        $j=0;
        $rubs = array();
        foreach($notice as $nkey=>$nval)
        {
            $sql = "SELECT n_id, r_id, title
                FROM tbl_notice n
                WHERE n_id = ".$nval->n_id;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            if(($row = $dataReader->read())!==false)
            {
                if($row['r_id'] != $nval->r_id)
                {
                    $i++;
                    //$rubs[$row['r_id']] = $nval->r_id;

                    $nval->import_wave_number = -1;
                    $nval->save();
                    if(count($nval->getErrors()) > 0)
                    {
                        deb::dump($nval->getErrors());
                    }
                }
                else
                {
                    $j++;
                    NoticeProps::model()->deleteAll('n_id = :n_id', array(':n_id'=>$nval->n_id));

                    $nval->import_wave_number = 3;
                    $nval->save();
                    if(count($nval->getErrors()) > 0)
                    {
                        deb::dump($nval->getErrors());
                    }

                    $sqlprop = "SELECT *
                            FROM tbl_notice_props
                            WHERE n_id = ".$nval->n_id;
                    $command_prop = $connection->createCommand($sqlprop);
                    $dataReader_prop = $command_prop->query();
                    while(($rowprop = $dataReader_prop->read())!==false)
                    {
                        $propmodel = new NoticeProps();
                        $propmodel->n_id = $rowprop['n_id'];
                        $propmodel->rp_id = $rowprop['rp_id'];
                        $propmodel->ps_id = $rowprop['ps_id'];
                        $propmodel->old_base_tag = 1;

                        $propmodel->save();
                    }

                    AdvertController::PropsXmlGenerate($nval->n_id);


                }
            }
        }
        deb::dump($i);

    }

    // Процесс импорта в базу
    public function actionImportOldAdverts()
    {
        /*
        В старую таблицу notice добавлено поле import_wave_number
        сюда выставляется номер "волны" импорта, например вначале было импортировано около 80000 объяв
        ставим для всех этих объяв номер = 1
        во время следующего импорта отличные от нуля игнорируем, работаем только с нулевыми
        */

        /*
        // выставляем import_wave_number
        $base_rows = Notice::model()->findAll(array(
            'select'=>'n_id, import_wave_number',
            'condition'=>'import_wave_number = 1'
        ));

        $connection_old = Yii::app()->db_old;

        foreach($base_rows as $okey=>$oval)
        {
            $sql = "UPDATE
                ". $connection_old->tablePrefix . "notice
                SET import_wave_number = ".$oval->import_wave_number."
                WHERE n_id = ".$oval->n_id;
            $res = $connection_old->createCommand($sql)->query();
        }
        die('Пометка волны завершена');
        /**/


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
        // вставить потом: //AND import_wave_number = 0
        $sql = "SELECT n.*, t.name townname, r.name regionname, c.name countryname
                FROM
                ". $connection->tablePrefix . "notice n,
                ". $connection->tablePrefix . "towns t,
                ". $connection->tablePrefix . "regions r,
                ". $connection->tablePrefix . "countries c
                WHERE n.r_id = ".$oldbase['rubold']." AND import_wave_number = 0
                    AND n.t_id = t.t_id AND n.region_id = r.r_id AND n.c_id = c.c_id
                ";
//deb::dump($sql);
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
                //$inbaserow->r_id = $mainblock['r_id'];
                //$rubrow = Rubriks::model()->findByPk($mainblock['r_id']);
                //$inbaserow->parent_r_id = $rubrow->parent_id;
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
                    // Формируем свойства
                    NoticeProps::model()->deleteAll('n_id = :n_id', array(':n_id'=>$inbaserow->n_id));

                    // Делаем пометку
                    $sql = "UPDATE
                            ". $connection->tablePrefix . "notice
                            SET import_wave_number = 2
                            WHERE n_id = ".$aval['n_id'];
                    $res_upd = $connection->createCommand($sql)->query();

                    $inbaserow->import_wave_number = 2;
                    $inbaserow->save();

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

                    AdvertController::PropsXmlGenerate($inbaserow->n_id);


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

deb::dump("NEW - ". $newadv->n_id);

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
                    // Делаем пометку
                    $sql = "UPDATE
                            ". $connection->tablePrefix . "notice
                            SET import_wave_number = 2
                            WHERE n_id = ".$newadv->n_id;
                    $res_upd = $connection->createCommand($sql)->query();

                    $newadv->import_wave_number = 2;
                    $newadv->save();

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

                    AdvertController::PropsXmlGenerate($newadv->n_id);

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
            'condition'=>'old_base_tag = 1 AND client_phone = ""  ',
            'limit'=>10000
        ));

        $plus = 0;
        foreach($adverts as $akey=>$aval)
        {
            $sph = preg_replace('|[ \-\(\)]*|siU', '', $aval->old_client_phone);

            // Россия
            if($aval['c_id'] == 185)
            {
                $aval->client_phone_c_id = 185;

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
                    && $sph[1] == '7'
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

            }



            // Украина
            if($aval['c_id'] == 222)
            {
                $aval->client_phone_c_id = 222;

                $solidnum = '';
                $phone_search = '';

                if(strlen($sph) == 11
                    && $sph[0] == '8')
                {
                    $solidnum = "(".$sph[1].$sph[2].$sph[3].") ".$sph[4].$sph[5].$sph[6]."-".$sph[7].$sph[8]."-".$sph[9].$sph[10];
                    echo "----------";
                }
                else
                if(strlen($sph) == 13
                    && $sph[0] == '+'
                    && $sph[1] == '3'
                    && $sph[2] == '8')
                {
                    $solidnum = "(".$sph[3].$sph[4].$sph[5].") ".$sph[6].$sph[7]."-".$sph[8].$sph[9]."-".$sph[10].$sph[11].$sph[12];
                    echo "----------";
                }
                else
                if(strlen($sph) == 10)
                {
                    $solidnum = "(".$sph[0].$sph[1].$sph[2].") ".$sph[3].$sph[4].$sph[5]."-".$sph[6].$sph[7]."-".$sph[8].$sph[9];
                    echo "----------";
                }
                else
                if(strlen($sph) == 12
                    && $sph[0] == '3'
                    && $sph[1] == '8')
                {
                    $solidnum = "(".$sph[2].$sph[3].$sph[4].") ".$sph[5].$sph[6]."-".$sph[7].$sph[8]."-".$sph[9].$sph[10].$sph[11];
                    echo "----------";
                }
            }

            // Казахстан
            if($aval['c_id'] == 122)
            {
                $aval->client_phone_c_id = 122;

                $solidnum = '';
                $phone_search = '';
                if(strlen($sph) == 11
                    && $sph[0] == '8')
                {
                    $solidnum = "(".$sph[1].$sph[2].$sph[3].") ".$sph[4].$sph[5].$sph[6]."-".$sph[7].$sph[8]."-".$sph[9].$sph[10];
                    echo "----------";
                }
                else
                if(strlen($sph) == 12
                    && $sph[0] == '+'
                    && $sph[1] == '7')
                {
                    $solidnum = "(".$sph[2].$sph[3].$sph[4].") ".$sph[5].$sph[6].$sph[7]."-".$sph[8].$sph[9]."-".$sph[10].$sph[11];
                    echo "----------";
                }
                else
                if(strlen($sph) == 10)
                {
                    $solidnum = "(".$sph[0].$sph[1].$sph[2].") ".$sph[3].$sph[4].$sph[5]."-".$sph[6].$sph[7]."-".$sph[8].$sph[9];
                    echo "----------";
                }
                else
                if(strlen($sph) == 11
                    && $sph[0] == '7')
                {
                    $solidnum = "(".$sph[1].$sph[2].$sph[3].") ".$sph[4].$sph[5].$sph[6]."-".$sph[7].$sph[8]."-".$sph[9].$sph[10];
                    echo "----------";
                }

            }

            $phone_search = preg_replace('|[\(\) \-]|siU', '', $solidnum);

            echo $sph." - ".$solidnum." - ".$phone_search."<br>";

            $aval->client_phone = $solidnum;
            if($solidnum == '')
            {
                $aval->client_phone = '-';
            }
            $aval->phone_search = $phone_search;

            $aval->save();

        }

    }

    /* генератор активационных ключей для старых юзеров */
    public function actionActiveKeysGenerate()
    {
        die('Скрипт заблокирован! Убрать die()');

        $users = User::model()->findAll(array(
            'select'=>'*',
            'condition'=>" activkey = '' ",
            'limit'=>10000
        ));

        foreach($users as $ukey=>$uval)
        {
            $uval->activkey = md5(microtime().rand(0,999999).$uval->password);
            $uval->save();
            $error = $uval->getErrors();
            if(count($error) > 0)
            {
                deb::dump($error);
            }
        }

    }

    /* проверка картинок старых объяв на существование */
    public function actionOldImageChecker()
    {
        //die('Заблокировано');

        if($noticeimages = NoticeImagesOld::model()->findAll(array(
            'select'=>'*',
            'condition'=>'scan_import_tag = 0',
            'order'=>'ni_id',
            'limit'=>20000
        )))
        {
            //$folder = 'tempphotos';
            $folder = Yii::app()->params['photodir'];
            $files_array = array();
            foreach($noticeimages as $ikey=>$ival)
            {
                $tofile = md5($ival->filename).".".$ival->file_ext;
                $tofile_small = md5($ival->filename)."_thumb.".$ival->file_ext;
                $curr_dir = Notice::getPhotoDirMake($folder, $tofile);
                $output_dir = realpath(Yii::app()->basePath."/../".$folder."/".$curr_dir)."/";

                $filename = $output_dir.$tofile;

                if(is_file($filename))
                {
                    deb::dump($filename);
                    $ival->scan_import_tag = 1;
                    //$ival->save();
                    if(count($ival->getErrors()) > 0)
                    {
                        deb::dump($ival->getErrors());
                    }
                }
                else
                {
                    if($notice = Notice::model()->findByPk($ival->n_id))
                    {
                        $notice->img_import_tag = 0;
                        //$notice->save();
                        if(count($notice->getErrors()) > 0)
                        {
                            deb::dump($notice->getErrors());
                        }

                        $ival->scan_import_tag = 1;
                        //$ival->save();
                        if(count($ival->getErrors()) > 0)
                        {
                            deb::dump($ival->getErrors());
                        }
                    }
                    else
                    {
                        $ival->scan_import_tag = -1;
                        //$ival->save();
                        if(count($ival->getErrors()) > 0)
                        {
                            deb::dump($ival->getErrors());
                        }
                    }
                    //deb::dump($notice);
                }


            }
        }
    }



    public function actionImageimportmenu()
    {

        $this->render('imageimportmenu');
    }


    public function actionImageimport()
    {
        // Получение всех кодов rp_id со свойством "photoblock" для рубрик
        // Ремарка: для временных рубрик должно быть определено свойство "photoblock"
        // иначе данные о сымпортированных фотографиях не заносятся в базу свойств.
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

        /*
         * ВНИМАНИЕ, алгоритм перетасован (много закомментировано), добавлены куски для восстановления
         * утерянных данных в свойствах о фотографиях
         * для восстановления функционала загрузки надо будет прошерстить код
         * */

        $notices = Notice::model()->findAll(array(
            'select'=>'*',
            //'condition'=>'old_base_tag = 1 AND img_import_tag = 0 ',    //AND n_id=1198638
            //'condition'=>'old_base_tag = 1 AND img_import_tag = 0',       // AND n_id = 1317532',
            'condition'=>'old_base_tag = 1 AND img_import_tag = 3',       // 3 пометка фоток художника, тест
            'order'=>'n_id ',
            'limit'=>5000
        ));

        foreach($notices as $nkey=>$nval)
        {
            //deb::dump($nval->n_id);
            //die();
            if($noticeimages = NoticeImagesOld::model()->findAll(array(
                'select'=>'*',
                'condition'=>'n_id = '.$nval->n_id,
                'order'=>'n_id ASC, titul_tag DESC, fotonumber ASC',
            )))
            {
                //$folder = 'tempphotos';
                $folder = Yii::app()->params['photodir'];
                $files_array = array();
                foreach($noticeimages as $ikey=>$ival)
                {
                    $tofile = md5($ival->filename).".".$ival->file_ext;
                    $tofile_small = md5($ival->filename)."_thumb.".$ival->file_ext;
                    $curr_dir = Notice::getPhotoDirMake($folder, $tofile);
                    $output_dir = Yii::app()->basePath."/../".$folder."/".$curr_dir."/";
                //deb::dump('http://auto.baraholka.ru/imgnot/'.$ival->filename.".".$ival->file_ext);
                //deb::dump($output_dir.$tofile);
                //die();

                    ///if(copy('http://auto.baraholka.ru/imgnot/'.$ival->filename.".".$ival->file_ext, $output_dir.$tofile))
                    if(1)
                    {
                        $files_array[] = $tofile;

                        /***
                        // Копируем маленькое превью
                        copy('http://auto.baraholka.ru/imgnot/'.$ival->filename."_s.".$ival->file_ext, $output_dir.$tofile_small);

                        // дублируем в качестве исходника
                        $original_photo = $tofile;

                        // Наложение водяного знака и генерация картинок разных размеров
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

                        ***/

                    }
                }

deb::dump($ival->n_id);

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
                    if($notice_prop->hand_input_value == '')    // проверка для восстановления потерянных данных (закоментить)
                    {
echo 'Обновление<br>';
deb::dump($files_array);

                        $notice_prop->ps_id = $notice_photo_ps_id;
                        $notice_prop->hand_input_value = $hand_input_value;
                        $notice_prop->old_base_tag = 1;
                        $notice_prop->save();
                        if(count($notice_prop->getErrors()) > 0)
                        {
                            deb::dump($notice_prop->getErrors());
                            die('Ошибка обновления');
                        }
                    }
                }
                else
                {
echo 'Вставка<br>';
deb::dump($files_array);

                    $notice_prop = new NoticeProps();
                    $notice_prop->n_id = $nval->n_id;
                    $notice_prop->rp_id = $notice_photo_rp_id;
                    $notice_prop->ps_id = $notice_photo_ps_id;
                    $notice_prop->hand_input_value = $hand_input_value;
                    $notice_prop->old_base_tag = 1;
                    $notice_prop->save();

                    if(count($notice_prop->getErrors()) > 0)
                    {
                        deb::dump($notice_prop->getErrors());
                        die('Ошибка вставки');
                    }

                }

                //deb::dump($nval->n_id);
            }
//die();
            $nval->img_import_tag = 4;
            $nval->save();

            // Перегенерируем xml свойства
            AdvertController::PropsXmlGenerate($nval->n_id);

            //deb::dump($noticeimages);
        }


        //$this->redirect('/adminka/support/imageimport/?rnd='.rand(0,9999));

        ?>
        <script>
            document.location = '/adminka/support/imageimport/?rnd=<?= rand(0,9999);?>';
        </script>
        <?
        //$this->render('imageimport');
    }


    // Восстановление данных об импортированных картинках в в базе
    // данные берутся из таблицы ohtbsfvre_notice_images старой базы
    public function actionImportedImageRecovery()
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

        $notices = Notice::model()->findAll(array(
            'select'=>'*',
            'condition'=>'old_base_tag = 1 AND img_import_tag = 0 ',    //AND n_id=1198638
            'order'=>'n_id ',
            'limit'=>1000
        ));

        foreach($notices as $nkey=>$nval)
        {
            //deb::dump($nval->n_id);
            //die();
            if($noticeimages = NoticeImagesOld::model()->findAll(array(
                'select'=>'*',
                'condition'=>'n_id = '.$nval->n_id,
                'order'=>'n_id ASC, titul_tag DESC, fotonumber ASC',
            )))
            {
                //$folder = 'tempphotos';
                $folder = Yii::app()->params['photodir'];
                $files_array = array();
                foreach($noticeimages as $ikey=>$ival)
                {
                    $tofile = md5($ival->filename).".".$ival->file_ext;
                    $tofile_small = md5($ival->filename)."_thumb.".$ival->file_ext;
                    $curr_dir = Notice::getPhotoDirMake($folder, $tofile);
                    $output_dir = Yii::app()->basePath."/../".$folder."/".$curr_dir."/";
                    //deb::dump($ival);
                    //deb::dump($output_dir.$tofile);
                    //die();
                    if(1)
                    {
                        $files_array[] = $tofile;
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

//deb::dump($nval->n_id);
//deb::dump($notice_prop->getErrors());
//die();
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

                //deb::dump($nval->n_id);
            }

            $nval->img_import_tag = 1;
            $nval->save();

            // Перегенерируем xml свойства
            AdvertController::PropsXmlGenerate($nval->n_id);

            //deb::dump($noticeimages);
        }


        //$this->redirect('/adminka/support/imageimport/?rnd='.rand(0,9999));

        ?>
        <script>
            document.location = '/adminka/support/importedimagerecovery/?rnd=<?= rand(0,9999);?>';
        </script>
        <?
        //$this->render('imageimport');
    }


    // Работа с ключевыми словами
    public function actionSeo()
    {
        $query_delta = 0;

        $query_type = 'all';
        if(isset($_POST['query_type']))
        {
            $query_type = $_POST['query_type'];
        }

        $position = $_POST['position'];
        $position_sql = ' ';
        if($position > 0)
        {
            $position_sql = ' AND position = '.$position.' ';
        }

        switch($query_type)
        {
            case "all":
                if(isset($_SESSION['keyword']))
                {
                    $keyword = $_SESSION['keyword'];
                }
                if(isset($_SESSION['seoparams']))
                {
                    $seoparams = $_SESSION['seoparams'];
                }
            break;

            case "search":
                $keyword = $_POST['keyword'];
                $seoparams = $_POST['seoparams'];
            break;

            case "edit":
/*
                $k_id = intval($_POST['k_id']);
                $row_keyword = SeoKeywords::model()->findByPk($k_id);
                $row_props = SeoKeywordsProps::model()->findAllByAttributes(array('k_id'=>$k_id));
                $keyword = array();
                $keyword['r_id'] = $row_keyword->r_id;
                $keyword['seokeyword'] = $row_keyword->keyword;

                $seoparams = array();

                $connection=Yii::app()->db;
                $sql_full = "SELECT DISTINCT r.selector, p.value
                        FROM ". $connection->tablePrefix . "seo_keywords_props s,
                             ". $connection->tablePrefix . "rubriks_props r,
                             ". $connection->tablePrefix . "props_sprav p
                        WHERE s.k_id = $k_id AND s.rp_id = r.rp_id AND s.ps_id = p.ps_id ";

//deb::dump($sql_full);

                $command = $connection->createCommand($sql_full);
                $dataReader = $command->query();
                while(($row = $dataReader->read())!==false)
                {
                    $keyword[$row['selector']] = $row['value'];
                }
                //$_SESSION['keyword'] = $keyword;
*/
            break;
        }

        $r_id = 0;
        if(isset($keyword['r_id']))
        {
            $r_id = $keyword['r_id'];
            unset($keyword['r_id']);
        }
        $seokeyword = '';
        if(isset($keyword['seokeyword']))
        {
            $seokeyword = $keyword['seokeyword'];
            unset($keyword['seokeyword']);
        }



        if(count($keyword) > 0)
        {
            foreach($keyword as $kkey=>$kval)
            {
                if($kval == 0)
                {
                    unset($keyword[$kkey]);
                }
            }
        }

//deb::dump($keyword);
        $page = intval($seoparams['page']);
        if($page == 0)
        {
            $page = 1;
        }
        $col_on_page = 500;
        if(isset($seoparams['col_on_page']) && intval($seoparams['col_on_page'] > 0))
        {
            $col_on_page = intval($seoparams['col_on_page']);
        }

        $connection=Yii::app()->db;

        // Показ архивных
        $expire_sql = " ";

        // Местоположение
        $mesto_sql = " 1 ";

        //Рубрика
        $rubrik_sql = " 1 ";
        if(isset($r_id) && intval($r_id) > 0)
        {
            $rubrik = Rubriks::model()->findByPk($r_id);
            if($rubrik->parent_id > 0)
            {
                $rubrik_sql = " r_id = ".intval($r_id);
            }
            else
            {
                deb::dump("Можно выбрать только подрубрику");
                /*
                $subrubs = Rubriks::model()->findAll(array('condition'=>'parent_id = '.intval($r_id)));
                $subrubs_ids = array();
                if($subrubs)
                {
                    foreach($subrubs as $key=>$val)
                    {
                        $subrubs_ids[] = $val->r_id;
                    }
                    $rubrik_sql = " r_id IN (". implode(", ", $subrubs_ids).") ";
                }
                */
            }

        }

        $rubriks_props = RubriksProps::model()->findAll(array(
                'select'=>'*',
                'condition'=>$rubrik_sql." AND use_in_filter = 1 ",
                'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
                //'limit'=>'10'
            )
        );

        $rp_ids = array();
        $rubriks_props_poryadok_array = array();
        $rubriks_props_poryadok_by_selector_array = array();
        $rubriks_poryadok_props_array = array();
        $i=2;
        $pubriks_props_array = array();
        $pubriks_props_by_selector_array = array();
        foreach ($rubriks_props as $rkey=>$rval)
        {
            $rubriks_props_poryadok_array[$rval->rp_id] = $i++;
            $rubriks_props_poryadok_by_selector_array[$rval->selector] = $rubriks_props_poryadok_array[$rval->rp_id];
            $rubriks_poryadok_props_array[$i-1] = $rval->rp_id;
            $rp_ids[$rval->rp_id] = $rval->rp_id;
            $pubriks_props_array[$rval->rp_id] = $rval;
            $pubriks_props_by_selector_array[$rval->selector] = $rval;
        }


        $search_keywords = array();  // Найденные ключевики

        if( (isset($keyword) && count($keyword) > 0 ) )
        {
            $props_sprav = PropsSprav::model()->findAll(array('condition'=>'rp_id IN ('.implode(", ", $rp_ids).')'));
            $props_route_items = array();
            $props_route_items_by_id = array();
            foreach($props_sprav as $pkey=>$pval)
            {
                $props_route_items[$rubriks_props_poryadok_array[$pval->rp_id]][$pval->transname] = $pval;
                $props_route_items_by_id[$pval->ps_id] = $pval;
            }

            foreach($keyword as $pkey=>$pval)
            {
                if(isset($props_route_items[$pkey][$pval]))
                {
                    $ps_id = $props_route_items[$pkey][$pval]->ps_id;
                }
            }
            $current_ps_id = $ps_id;

            // Ищем объявы с совпадением значений всех указанных свойств
            //if(count($_GET['prop']) == count($props_sql_array))
            if(1)
            {
                $from_tables_array = array();
                $from_tables_sql = "";
                $where_n_array = array();
                $where_n = "";
                $where_filter_array = array();
                $where_filter_sql = "";

                $i=0;
                foreach($keyword as $gkey=>$gval)
                {
                    if(intval($gval) > 0)
                    {
                        $switch_rp_id = $pubriks_props_by_selector_array[$gkey]->rp_id;
                        $i++;

//deb::dump($gkey);
                        $from_tables_array[] = $connection->tablePrefix . "seo_keywords_props n".$i;
                        $where_n_array[] = " AND n".$i.".rp_id = ".$switch_rp_id;
                        $where_n_array[] = " AND n".$i.".k_id = n".($i+1).".k_id ";
                        $where_filter_array[] = "n".$i.".ps_id = ".intval($gval);
                    }

                }
//deb::dump($pubriks_props_by_selector_array);
                $from_tables_sql = implode(", ", $from_tables_array);
                unset($where_n_array[count($where_n_array)-1]);
                $where_n = implode(" ", $where_n_array);
                $where_filter_sql = implode(" AND ", $where_filter_array);
                //deb::dump($from_tables_sql);
                //deb::dump($where_n);

                // Полный запрос
                $rubrik_prop_sql = str_replace("r_id", "n.r_id", $rubrik_sql);
                $sql_full = "SELECT DISTINCT n.*
                        FROM ". $connection->tablePrefix . "seo_keywords n,
                        ".$from_tables_sql."
                        WHERE 1 ".$position_sql." AND $expire_sql
                        $mesto_sql AND $rubrik_prop_sql AND
                        $where_filter_sql
                        ".$where_n."
                        AND n1.k_id = n.k_id
                        ORDER BY n.k_id DESC ";    // патч по количеству, иначе вылетает изза нехватки памяти

//deb::dump($sql_full);

                $command = $connection->createCommand($sql_full);
                $dataReader = $command->query();
                $rowcount = $dataReader->getRowCount();
                $col_pages = ceil($rowcount / $col_on_page);

                // Постраничный запрос
                if($page == 0)
                {
                    $page = 1;
                }
                $start = ($page - 1)*$col_on_page;
                $stop = $col_on_page;
                $sql = $sql_full . " LIMIT $start, $stop";
//deb::dump($col_pages);
                $start_time = microtime();

                $command = $connection->createCommand($sql);
                $dataReader = $command->query();

                $stop_time = microtime();
                $query_delta = $stop_time - $start_time;

                while(($row = $dataReader->read())!==false)
                {
                    $search_keywords[$row['k_id']] = $row;
                }
//deb::dump($sql);
                //deb::dump($search_keywords);
                //die();

            }
            else    // Нет записей удовлетворяющих критерию
            {

            }

        }
        // Если поиск только по местоположению/рубрике - простой запрос
        else
        {
            $adverts_full = SeoKeywords::model()->findAll(
                array(
                    'select'=>'*',
                    'condition'=>' 1 '.$position_sql.' AND '.$rubrik_sql,
                    'order'=>'k_id DESC',
                )
            );

            $rowcount = count($adverts_full);
            $col_pages = ceil($rowcount / $col_on_page);

            // Постраничный запрос
            if($page == 0)
            {
                $page = 1;
            }
            $start = ($page - 1)*$col_on_page;
            $adverts = SeoKeywords::model()->findAll(
                array(
                    'select'=>'*',
                    'condition'=>' 1 '.$position_sql.' AND '.$rubrik_sql,
                    'order'=>'k_id DESC',
                    'limit'=>$col_on_page,
                    'offset'=>$start
                )
            );


            foreach ($adverts as $akey=>$aval)
            {
                $search_keywords[$aval->k_id] = $aval->attributes;
            }

        }


        $rub_array = Rubriks::get_rublist();
        $randomwords = SeoRandomword::model()->findall();

        switch($query_type)
        {
            case "all":
                $this->render('seo', array(
                    'r_id'=>$r_id,
                    'seokeyword'=>$seokeyword,
                    'rub_array'=>$rub_array,
                    'keyword'=>$keyword,
                    'search_keywords'=>$search_keywords,
                    'query_type'=>$query_type,
                    'randomwords'=>$randomwords,

                ));
            break;

            case "search":
                $this->renderPartial('seo_keywords', array(
                    'r_id'=>$r_id,
                    'seokeyword'=>$seokeyword,
                    'rub_array'=>$rub_array,
                    'keyword'=>$keyword,
                    'search_keywords'=>$search_keywords,
                    'query_type'=>$query_type

                ));
            break;

            case "edit":
                /*
                $this->renderPartial('seo_form', array(
                    'r_id'=>$r_id,
                    'seokeyword'=>$seokeyword,
                    'rub_array'=>$rub_array,
                    'keyword'=>$keyword,
                    'search_keywords'=>$search_keywords,
                    'query_type'=>$query_type

                ));
                */
            break;
        }


    }


    // Формирование списка свойств рубрики для формы работы с ключевиками
    public function actionGetKeywordProps()
    {
        $connection = Yii::app()->db;

        if(isset($_POST['keyword']))
        {
            Yii::app()->session['keyword'] = $_POST['keyword'];
            $keyword = Yii::app()->session['keyword'];
        }
        else if (isset($_SESSION['keyword']))
        {
            $keyword = $_SESSION['keyword'];
        }

        $r_id = intval($keyword['r_id']);
        $rubrik = Rubriks::model()->findByPk($r_id);

        $rubriks_props = RubriksProps::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id = '.$r_id . ' AND
                    (vibor_type = "selector"
                     OR vibor_type = "listitem"
                     OR vibor_type = "autoload"
                     OR vibor_type = "autoload_with_listitem" )',
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
        ));

        $rprops_array = array();
        $rprops_byselector_array = array();
        $rp_ids = array();

        foreach($rubriks_props as $pkey=>$pval)
        {
            if($pval->parent_id <= 0)
            {
                $prop_items = PropsSprav::model()->findAll(array(
                    'select'=>'*',
                    'condition'=>'rp_id = '. $pval->rp_id,
                    'order'=>'value'
                ));

                $temp = $pval->attributes;
                foreach($prop_items as $pikey=>$pival)
                {
                    //deb::dump($pival->ps_id);
                    $temp['sprav_items'][$pival->ps_id] = $pival->attributes;
                }

                $rprops_array[$pval->rp_id] = $temp;
                $rprops_byselector_array[$pval->rp_id] = $temp;
            }
            else
            {
                $parent_rubriks_props = RubriksProps::model()->findByPk($pval->parent_id);

                $temp = $pval->attributes;

                if(isset($keyword[$parent_rubriks_props->selector])
                    && intval($keyword[$parent_rubriks_props->selector]) > 0)
                {
//            deb::dump($parent_rubriks_props);
//            die();
                    $parent_ps_id = intval($keyword[$parent_rubriks_props->selector]);

                    $sql = "SELECT *
                        FROM
                        ". $connection->tablePrefix . "props_relations pr,
                        ". $connection->tablePrefix . "props_sprav ps
                        WHERE pr.parent_ps_id = $parent_ps_id AND pr.child_ps_id = ps.ps_id
                                AND ps.rp_id = ".$pval->rp_id."
                        ORDER BY ps.sort_number " ;
                    //deb::dump($sql);
                    $command = $connection->createCommand($sql);
                    $dataReader = $command->query();
                    while(($row = $dataReader->read())!==false)
                    {
                        $temp['sprav_items'][$row['ps_id']] = $row;
                    }

                    $rprops_array[$pval->rp_id] = $temp;
                    $rprops_byselector_array[$pval->rp_id] = $temp;
                }

            }

            $rp_ids[$pval->rp_id] = $pval->rp_id;

        }

        // Для формирования ссылки в футере
        if(count($rp_ids) > 0)
        {
            $props_sprav = PropsSprav::model()->findAll(array('condition'=>'rp_id IN ('.implode(", ", $rp_ids).')'));
            $props_route_items_by_id = array();
            foreach($props_sprav as $pkey=>$pval)
            {
                $props_route_items_by_id[$pval->ps_id] = $pval;
            }
        }
        $postkeyword = $_POST['keyword'];
        $r_id = $postkeyword['r_id'];
        unset($postkeyword['r_id']);
        unset($postkeyword['seokeyword']);

        $props_path = array();
        $props_path_ids = array();
        $props_path_names = array();
        if(count($postkeyword) > 0)
        {
            foreach($postkeyword as $pkey=>$pval)
            {
                if($pval > 0)
                {
                    if($props_route_items_by_id[$pval]->value != null)
                    {
                        $props_path[$pval]['name'] = $props_route_items_by_id[$pval]->value;
                        $props_path[$pval]['transname'] = $props_route_items_by_id[$pval]['transname'];
                        $props_path_ids[$pval] = $pval;
                        $props_path_names[$pval] = $props_route_items_by_id[$pval]->value;
                    }
                }
            }
        }

//        deb::dump($postkeyword);
        //deb::dump($props_path_names);
        if($r_id > 0)
        {
            $url_path_ids = $r_id;
            $url_path_names = $rubrik->name;
            if(count($props_path_ids) > 0)
            {
                $url_path_ids .= ".".implode(".", $props_path_ids);
                $url_path_names .= " / ".implode(" / ", $props_path_names);
            }
        }
        ////////////////////////////////

//deb::dump($keyword);
//deb::dump(Yii::app()->session['keyword']);
        $signature_array = array();
        $signature_ps_id_array = array();

        ?>
        <div style="margin: 15px;">
            Код рубрики.коды свойств (для ссылки в футере)<br>
            <input type="text" name="url_path_ids" id="url_path_ids" value="<?= $url_path_ids;?>">
            <span id="url_path_names"><?= $url_path_names;?></span>
        </div>

        <div style="margin-top: 5px;">Свойства:</div>
        <table>
        <tr>
        <td>
        <div style="border: #999 solid 1px; padding: 5px;">
            <?
            // Выводим сформированные списки
            foreach($rprops_array as $rkey=>$rval)
            {
                ?>

                <div style="float: left;">
                    <?= $rval['name'];?>:<br>
                    <select class="prop_item" name="keyword[<?= $rval['selector'];?>]" style="width: 200px;">
                        <option value="0">-- выберите свойство --</option>
                        <?
                        if(count($rval['sprav_items']) > 0)
                        {
                            foreach($rval['sprav_items'] as $ikey=>$ival)
                            {
                                $selected = " ";
                                if($ival['ps_id'] == $keyword[$rval['selector']])
                                {
                                    $selected = " selected ";

                                    $signature_array[] = $rkey;
                                    $signature_ps_id_array[] = $ival['ps_id'];
                                }
                                ?>
                                <option <?= $selected;?> value="<?= $ival['ps_id'];?>"><?= $ival['value'];?></option>
                            <?
                            }
                        }
                        ?>
                    </select>
                </div>


            <?
            }
            ?>
            <br clear="all">
        </div>
        </td>
        </tr>
        </table>

        <?
        $signature = implode('.', $signature_array);
        $signature_ps_id = implode('.', $signature_ps_id_array);
        $words = '';
        $textclass = 'bwred';
        if($wordsrow = SeoBoardWords::model()->findByAttributes(array(
            'r_id'=>$r_id,
            'signature'=>$signature,
            'signature_ps_id'=>$signature_ps_id
        )))
        {
            $words = $wordsrow->words;
            $textclass = 'bwgreen';
        }
        //deb::dump($signature_array);
        //deb::dump($signature_ps_id_array);
        //deb::dump(str_replace("\n", "\r\n", $words));
        ?>

        <script>

            $('#signature').val('<?= $signature;?>');
            $('#signature_ps_id').val('<?= $signature_ps_id;?>');
            $('#words').val("<?= str_replace("\n", "\\n", $words);?>");
            $('#words').attr('class', '<?= $textclass;?>');

            $('.prop_item').change(function(){
                GetPanelProps();
            });

            $('#url_path_ids').keyup(function(){
                $.ajax({
                    url: "<?= Yii::app()->createUrl('adminka/support/getkeywordurlpath');?>",
                    method: "post",
                    dataType: 'json',
                    data:{
                        r_id: $('#panel_r_id').val(),
                        url_path_ids: $(this).val()
                    },
                    // обработка успешного выполнения запроса
                    success: function(data){
                        if(data['status'] == 'ok')
                        {
                            $('#url_path_names').html(data['data']);
                        }
                    }
                });

            });


        </script>
    <?

    }


    // Генерация наименования ссылки в футере из кодов рубрики и кодов свойств
    public function actionGetKeywordUrlPath()
    {
        $ret['status'] = 'ok';
        $ret['data'] = '';

        if(isset($_POST['url_path_ids']) && trim($_POST['url_path_ids']) != '')
        {
            $url_path_ids = $_POST['url_path_ids'];
            $url_path_ids_array = explode(".", $url_path_ids);
            $url_path_names = "";
            if(isset($url_path_ids_array[0]))
            {
                if($rubrik = Rubriks::model()->findByPk($url_path_ids_array[0]))
                {
                    $url_path_names .= $rubrik->name;

                    unset($url_path_ids_array[0]);
                    if(count($url_path_ids_array) > 0)
                    {
                        $props_rows = PropsSprav::model()->findall(array(
                            'select'=>'*',
                            'condition'=>'ps_id IN ('.implode(",", $url_path_ids_array).')'
                        ));

                        foreach($props_rows as $prkey=>$prval)
                        {
                            $url_path_names .= " / ".$prval->value;
                        }
                    }
                }
            }

            $ret['status'] = 'ok';
            $ret['data'] = $url_path_names;
        }
        else
        if(trim($_POST['url_path_ids']) == '' && intval($_POST['r_id']) > 0)
        {
            $subrubrik = Rubriks::model()->findByPk(intval($_POST['r_id']));
            $rubrik = Rubriks::model()->findByPk($subrubrik->parent_id);

            $ret['status'] = 'ok';
            $ret['data'] = $rubrik->name;
        }

        echo json_encode($ret);
    }


    // Сохранение словосочетания для набора свойств
    public function actionSaveboardwords()
    {
        $r_id = intval($_POST['r_id']);
        $signature = trim($_POST['signature']);
        $signature_ps_id = trim($_POST['signature_ps_id']);
        $words = trim($_POST['words']);

        if($wordsrow = SeoBoardWords::model()->findByAttributes(array(
            'r_id'=>$r_id,
            'signature'=>$signature,
            'signature_ps_id'=>$signature_ps_id
        )))
        {
            $wordsrow->words = $words;
            $wordsrow->save();
        }
        else
        {
            $wordsrow = new SeoBoardWords();
            $wordsrow->r_id = $r_id;
            $wordsrow->signature = $signature;
            $wordsrow->signature_ps_id = $signature_ps_id;
            $wordsrow->words = $words;
            $wordsrow->save();
        }

        $res = array();
        $res['status'] = 'ok';
        $res['textclass'] = 'bwgreen';
        $res['message'] = 'Сохранено';

        echo json_encode($res);
    }


    // Удаление ключевика
    public function actionSeokeyworddel()
    {
        SeoKeywords::model()->deleteByPk($_POST['k_id']);
        SeoKeywordsNotice::model()->deleteAllByAttributes(array('k_id'=>$_POST['k_id']));
        SeoKeywordsProps::model()->deleteAllByAttributes(array('k_id'=>$_POST['k_id']));

    }


    public function actionAddNewKeyword()
    {
        $seokeyword = trim($_POST['keyword']['seokeyword']);
        $r_id = trim($_POST['keyword']['r_id']);
        $position = trim($_POST['position']);
        $props = array();
        if(count($_POST['keyword']) > 2)
        {
            foreach($_POST['keyword'] as $pkey=>$pval)
            {
                if($pkey != 'seokeyword' && $pkey != 'r_id')
                {
                    $props[$pkey] = $pval;
                }
            }
        }

        // Если ключевая фраза не указана
        if($seokeyword == '')
        {
            $ret['status'] = 'error';
            $ret['errors'] = 'Ключевая фраза не заполнена!';
            echo json_encode($ret);
            die();
        }


        // Если подрубрика не указана
        $r_id_array = array();
        if(trim($r_id) == '')
        {
            $rublist = Rubriks::get_rublist();
            foreach($rublist as $rkey=>$rval)
            {
                foreach($rval['childs'] as $r2key=>$r2val)
                {
                    $r_id_array[] = $r2val->r_id;
                }

            }

        }
        else
        {
            $r_id_array[] = $r_id;
        }

        // Если позиция не указана
        $position_array = array();
        if(trim($position) == '')
        {
            foreach(SeoKeywords::$position as $pkey=>$pval)
            {
                $position_array[] = $pkey;
            }
        }
        else
        {
            $position_array[] = $position;
        }

        // Если ключевая фраза многострочная
        $seokeyword_array = array();
        $seokeyword_array = explode("\r\n", $seokeyword);


        // Ссылка для футера
        $url_path_ids_fromform = trim($_POST['url_path_ids']);


        // Добавление
        $errors_array = array();
        foreach($r_id_array as $r_id_key=>$r_id_val)
        {
            $url_path_ids = $url_path_ids_fromform;
            if($url_path_ids == '')
            {
                $subrubrik = Rubriks::model()->findByPk($r_id_val);
                $url_path_ids = $subrubrik->parent_id;
            }

            foreach($position_array as $position_key=>$position_val)
            {
                foreach($seokeyword_array as $seokeyword_key=>$seokeyword_val)
                {
                    $ret = $this->AddNewSingleKeyword($seokeyword_val, $r_id_val, $position_val, $props, $url_path_ids);
                    if($ret['status'] == 'error')
                    {
                        $errors_array[] = $ret['errors'];
                    }
                }
            }

        }

        if(count($errors_array) > 0)
        {
            $ret['status'] = 'error';
            $ret['errors'] = implode("<br>", $errors_array);
        }
        else
        {
            $ret['status'] = 'ok';
        }

        echo json_encode($ret);
    }


    // Добавление единичной ключевой фразы
    public function AddNewSingleKeyword($seokeyword, $r_id, $position, $props, $url_path_ids)
    {
        $keyword = new SeoKeywords();

        $keyword->keyword = $seokeyword;
        $keyword->r_id = $r_id;
        $keyword->position = $position;
        $keyword->prop_count = 0;
        $keyword->url_path_ids = $url_path_ids;
        $keyword->save();

        $errors = $keyword->getErrors();

        $ret = array();
        $rp_names_array = array();
        $prop_sprav_array = array();
        if(count($errors) == 0)
        {
            $ret['status'] = 'ok';

            if(count($props) > 0)
            {
                foreach($props as $pkey=>$pval)
                {
                    if($pval > 0)
                    {
                        $prop = new SeoKeywordsProps();
                        $prop->k_id = $keyword->k_id;
                        $rubrik_props = RubriksProps::model()->findByAttributes(array('selector'=>$pkey));
                        $prop->rp_id = $rubrik_props->rp_id;
                        $prop->ps_id = $pval;
                        $prop->save();

                        $prop_sprav = PropsSprav::model()->findByPk($prop->ps_id);
                        $prop_sprav_array[$prop->rp_id] = $prop_sprav;
                    }
                }

                $data = SeoKeywords::MakeSignature($keyword->k_id, $keyword->r_id);

                $ps_ids_array = array();
                foreach($prop_sprav_array as $p2key=>$p2val)
                {
                    $rp_names_array[$p2key] = $data['rp_names'][$p2key].":".$p2val->value;
                    $ps_ids_array[$p2val->ps_id] = $p2val->ps_id;
                }


                $rubrik = Rubriks::model()->findByPk($keyword->r_id);
                $keyword->signature = implode(".", $data['rp_ids']);
                $keyword->signature_ps_id = implode(".", $ps_ids_array);
                $rp_names_array = array_merge(array($rubrik->name), $rp_names_array);
                $keyword->propnames = implode("; ", $rp_names_array);
                $keyword->prop_count = count($data['rp_ids']);

                $keyword->save();
            }
        }
        else
        {
            $ret['status'] = 'error';

            $errall = array();
            foreach($errors as $key=>$val)
            {
                foreach($val as $key2=>$val2)
                {
                    $errall[] = $val2;
                }
            }
            $ret['errors'] = implode("<br>", $errall);
        }

        $ret['data'] = $data;

        return $ret;
    }



    // Отладочная, удалить
    public function actionSignkeyword()
    {

        die();
        $keywords = SeoKeywords::model()->findAll();

        foreach($keywords as $key=>$keyword)
        {



            $props_relate = RubriksProps::model()->with('seo_keywords_props')->findAll(array(
                'select'=>'*',
                'condition'=>'r_id='.$keyword->r_id . " AND k_id=".$keyword->k_id,
                'order'=>'t.hierarhy_tag DESC, t.hierarhy_level ASC, t.display_sort, t.rp_id'
            ));
    //deb::dump($props_relate);

            $rp_id_array = array();
            $ps_id_array = array();
            foreach($props_relate as $pkey=>$pval)
            {
                switch($pval->vibor_type)
                {
                    case "autoload":
                    case "autoload_with_listitem":
                    case "selector":
                    case "listitem":

                        $rp_id_array[$pval->rp_id] = $pval->rp_id;
                        $ps_id_array[$pval->seo_keywords_props[0]->ps_id] = $pval->seo_keywords_props[0]->ps_id;

                        break;

                }
            }

            $ret['rp_ids'] = implode(".", $rp_id_array);
            $ret['ps_ids'] = implode(".", $ps_id_array);

            $keyword_signature = $ret['ps_ids'];
            $signature_array = explode('.', $keyword_signature);

            deb::dump($keyword->k_id." - ".$keyword_signature);
            echo "<br>";
            $keyword->signature_ps_id = $keyword_signature;
            $keyword->save();

        }


        deb::dump($signature_array);

    }


    public function actionRandomword()
    {

        $wordrows = array();
        $wordrows = SeoRandomword::model()->findAll(array(
            'select'=>'*',
            'order'=>'sr_id'
        ));

        $this->render('randomword', array(
            'wordrows'=>$wordrows,
            'errors'=>array()
        ));
    }


    public function actionAddrandomword()
    {

        $newrow = new SeoRandomword();
        $newrow->key = $_POST['key'];
        $newrow->words = $_POST['words'];
        $newrow->save();

        $wordrows = array();
        $wordrows = SeoRandomword::model()->findAll(array(
            'select'=>'*',
            'order'=>'sr_id'
        ));


        $this->renderPartial('addrandomword', array(
            'wordrows'=>$wordrows,
            'errors'=>$newrow->getErrors()
        ));
    }

    public function actionRandomword_edit()
    {
        $sr_id = intval($_POST['sr_id']);

        $row = SeoRandomword::model()->findByPk($sr_id);
        ?>
        <form id="form_save_<?= $sr_id;?>">
        <input type="text" name="sr_id" value="<?= $row->sr_id;?>">
        <input type="text" name="key" value="<?= $row->key;?>">
        <input type="text" name="words" value="<?= $row->words;?>">
        <input type="button" class="save_edit_button" sr_id="<?= $sr_id;?>" value="Сохранить">
        </form>

        <script>
            $('.save_edit_button').click(function(){
                sr_id = $(this).attr('sr_id');

                $.ajax({
                    async: false,
                    //dataType: 'json',
                    type: 'POST',
                    url: '<?= Yii::app()->createUrl('adminka/support/saverandomword');?>',
                    data: $('#form_save_'+sr_id).serialize(),
                    success: function(msg){
                        $('#tdrand_'+sr_id).html(msg);
                    }
                });

            });
        </script>
        <?
    }


    public function actionSaverandomword()
    {
        $sr_id = intval($_POST['sr_id']);

        $row = SeoRandomword::model()->findByPk($sr_id);
        $row->key = $_POST['key'];
        $row->words = $_POST['words'];
        if($row->save())
        {
        ?>
        <span class="col_key"><?= $row->key;?></span>
        <span class="col_words"><?= $row->words;?></span>
        <?
        }
        else
        {
            echo "Ошибка";
        }
    }

    public function actionRandomword_del()
    {
        $sr_id = intval($_POST['sr_id']);

        $row = SeoRandomword::model()->findByPk($sr_id);

        if($row->delete())
        {
            echo "ok";
        }
        else
        {
            echo "Ошибка";
        }
    }


    // Вспомогательная. Приведение в соответствие parent_r_id согласно r_id
    public function actionRecalcParentRid()
    {
        die('Убрать заглушку, если понадобится!');

        $notices = Notice::model()->findAll(array(
            'select'=>'*',
            'limit'=>1000000,
            'offset'=>0
        ));

        $rubriks = Rubriks::get_simple_rublist();
//        deb::dump($rubriks[91]->parent_id);
        $i=0;
        foreach($notices as $nkey=>$nval)
        {
            if($nval->parent_r_id != $rubriks[$nval->r_id]->parent_id)
            {
                $i++;
                $nval->parent_r_id = $rubriks[$nval->r_id]->parent_id;
                $nval->save();
                deb::dump($nval->getErrors());
            }
        }
        deb::dump($i);
    }





    // Служебная функция, визуальна проверка правильности соответствий старых рубрик новым + свойства
    public function actionCheckSootv()
    {
        header('Content-Type: text/html; charset=utf-8');

        $old_rubs = RubriksOld::model()->get_rublist();
        foreach($old_rubs as $okey=>$oval)
        {
        ?>
        <div>
            <b><?= $oval['parent']->name;?></b>
            <div style="margin-left: 20px;">
            <?
            foreach($oval['childs'] as $ckey=>$cval)
            {
                $parts = array();
            ?>
                <div>
                    <?= $cval->name;?> =>
                    <?
                    if($newrub = Rubriks::model()->findByPk($cval->new_r_id))
                    {
                        $parentrub = Rubriks::model()->findByPk($newrub->parent_id);
                        $parts[] = $parentrub->name;
                        $parts[] = $newrub->name;
                    }


                    ?>
                    <?

                    if($cval->props_list_ids != '')
                    {
                        $props_array = explode(",", $cval->props_list_ids);
                        foreach($props_array as $p2key=>$p2val)
                        {
                            $prop = PropsSprav::model()->findByPk($p2val);
                            $parts[] = $prop->value;
                        }
                    }

                    ?>

                    <span style="color: #259c1d;"><?= implode(" / ", $parts);?></span>

                </div>
            <?
            }
            ?>
            </div>
        </div>
        <?
        }
        //deb::dump($old_rubs);
    }



    // Логин под любым пользователем
    public function actionUserLoginByAdmin()
    {

        /************* Вход под любым юзером из под админского аккаунта **************/
        if(Yii::app()->user->id == 1)
        {
            if(isset($_POST['UserLoginByAdmin']))
            {
                $user = User::model()->findByAttributes(array('email'=>$_POST['UserLoginByAdmin']));
                Yii::app()->getSession()->regenerateID(true);
                Yii::app()->user->setId($user->id);
                Yii::app()->user->setName($user->username);
                Yii::app()->user->setState('email', $user->email);
                Yii::app()->user->setState(WebUser::STATES_VAR,array());
                header('Location: /usercab/adverts');
                die();
            }
            else
            {
                $this->render('userloginbyadmin');

            }

        }
        /************ КОНЕЦ ***************/

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