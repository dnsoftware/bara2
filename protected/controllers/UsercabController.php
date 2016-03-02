<?php

class UsercabController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}


    public function actionAdverts()
    {


        $transliter = new Supporter();
//deb::dump($_GET);
        $rub_counter = array();
        $rub_ids = array();
        $towns_ids = array();

        $sql_rub = ' ';
        if(isset($_GET['subrubrik']))
        {
            if($url_rub = Rubriks::model()->findByAttributes(array('transname'=>$_GET['subrubrik'])))
            {
                $sql_rub = ' AND r_id = '.$url_rub->r_id;
            }

        }
        else if(isset($_GET['rubrik']))
        {
            if($url_rub = Rubriks::model()->findByAttributes(array('transname'=>$_GET['rubrik'])))
            {

                if($url_subs = Rubriks::model()->findAllByAttributes(array('parent_id'=>$url_rub->r_id)))
                {
                    //deb::dump($url_subs);
                    $url_subs_array = array();
                    foreach($url_subs as $ukey=>$uval)
                    {
                        $url_subs_array[] = $uval->r_id;
                    }

                    if(count($url_subs_array) > 0)
                    {
                        $sql_rub = ' AND r_id IN ('.implode(",",$url_subs_array).')';
                    }
                }
            }
        }

//        deb::dump($sql_rub);

        $useradverts = array();
        $useradverts_photos = array();
        $subrub_array = array();
        $parent_ids = array();
        $parent_ids_count = array();


        if($useradverts_temp = Notice::model()->findAll(array(
            'select'=>'*',
            'condition'=>'u_id = '.Yii::app()->user->id." AND deleted_tag = 0 ",
            'order'=>'date_add DESC'
        )))
        {
            foreach($useradverts_temp as $ukey=>$uval)
            {
                $rub_counter[$uval->r_id]++;
                $rub_ids[$uval->r_id] = $uval->r_id;
                $towns_ids[$uval->t_id] = $uval->t_id;
            }

            // Города
            $towns = Towns::model()->findAll(array(
                'condition'=>'t_id IN ('.implode(",", $towns_ids).')'
            ));
            $towns_array = array();
            foreach($towns as $tkey=>$tval)
            {
                $towns_array[$tval->t_id] = $tval;
            }
        }


        //////////////////////////
        $search_adverts = array();
        $props_array = array();
        if($useradverts_temp = Notice::model()->findAll(array(
            'select'=>'*',
            'condition'=>'u_id = '. Yii::app()->user->id ." AND deleted_tag = 0 ". $sql_rub,
            'order'=>'date_add DESC',
        )))
        {
            foreach($useradverts_temp as $ukey=>$uval)
            {
                $index = 'actual';
                if($uval->date_expire < time() || $uval->active_tag == 0)
                {
                    $index = 'expire';
                }

                $search_adverts[$index][$uval->n_id] = $uval->attributes;
                $search_adverts[$index][$uval->n_id]['town_name'] = $towns_array[$uval->t_id]->name;
                $search_adverts[$index][$uval->n_id]['town_transname'] = $towns_array[$uval->t_id]->transname;
            }

            // Подготовка данных для отображения
            //// Шаблоны отображения из рубрик
            $shablons_display = Rubriks::GetShablonsDisplay();
            $rubriks_all_array = Rubriks::get_all_subrubs();
            //deb::dump($rubriks_all_array);

            if(isset($search_adverts['actual']))
            {
                $props_array['actual'] = Notice::DisplayAdvertsList($search_adverts['actual'], $shablons_display, $rubriks_all_array);
            }

            if(isset($search_adverts['expire']))
            {
                $props_array['expire'] = Notice::DisplayAdvertsList($search_adverts['expire'], $shablons_display, $rubriks_all_array);
            }

        }
//deb::dump($props_array);

        if(count($rub_ids) > 0)
        {
            if($rubriks = Rubriks::model()->findAll(array(
                'select'=>'*',
                'condition'=>'r_id IN ('.implode(", ", $rub_ids).')'
            )))
            {
                foreach($rubriks as $rkey=>$rval)
                {
                    $subrub_array[$rval->parent_id][$rval->r_id] = $rval;
                    $parent_ids[$rval->parent_id] = $rval->parent_id;
                    $parent_ids_count[$rval->parent_id] += $rub_counter[$rval->r_id];
                }
            }
        }

        if(count($parent_ids) > 0)
        {
            $parent_rubriks = Rubriks::model()->findAll(array(
                'select'=>'*',
                'condition'=>'r_id IN ('.implode(", ", $parent_ids).')'
            ));
        }


        // Данные для формы поиска
        $rub_array = Rubriks::get_rublist();

        $props_sprav_sorted_array = array();    // Заглушка, при необходимости нужно нормально инициализировать
        $rubriks_props_array = array();    // Заглушка, при необходимости нужно нормально инициализировать

        if(Yii::app()->request->cookies->contains('geo_mytown'))
        {
            $mselector = 't';
            $m_id = Yii::app()->request->cookies['geo_mytown']->value;
        }
        else
            if(Yii::app()->request->cookies->contains('geo_myregion'))
            {
                $mselector = 'reg';
                $m_id = Yii::app()->request->cookies['geo_myregion']->value;
            }
            else
                if(Yii::app()->request->cookies->contains('geo_mycountry'))
                {
                    $mselector = 'c';
                    $m_id = Yii::app()->request->cookies['geo_mycountry']->value;
                }
                else
                {
                    $mselector = 'c';
                    $m_id = Yii::app()->params['russia_id'];
                }

        $this->render('adverts', array(
            //'useradverts'=>$useradverts,
            'parent_rubriks'=>$parent_rubriks, 'subrub_array'=>$subrub_array,
            'parent_ids_count'=>$parent_ids_count, 'rub_counter'=>$rub_counter,
            //'useradverts_photos'=>$useradverts_photos, 'towns_array'=>$towns_array,
            'transliter'=>$transliter,
            'search_adverts'=>$search_adverts,
            'props_array'=>$props_array,

            /********для формы поиска*********/
            'rub_array'=>$rub_array,
            'mselector'=>$mselector,
            'm_id'=>$m_id,
            'props_sprav_sorted_array'=>$props_sprav_sorted_array,
            'rubriks_props_array'=>$rubriks_props_array,
            'rubriks_all_array'=>$rubriks_all_array,


        ));














        if(0)
        {
            $adverts = Notice::model()->findAll(array(
                'select'=>'*',
                'condition'=>'u_id = '.Yii::app()->user->id
            ));

    //        deb::dump($adverts);
            $this->render('adverts', array('adverts'=>$adverts));
        }
    }


    public function actionAdvert_edit()
    {
        $n_id = intval($_GET['n_id']);
//AdvertController::PropsXmlGenerate($n_id);
        if(Yii::app()->user->isAdmin())
        {
            $advert = Notice::model()->findByPk($n_id);
        }
        else
        {
            $advert = Notice::checkAdvertOwner(Yii::app()->user->id, $n_id);
        }

        if($advert)
        {
            //deb::dump($advert);

            $props = NoticeProps::model()->findAllByAttributes(array('n_id'=>$n_id));
            //deb::dump($props);

            list($controller) = Yii::app()->createController('advert');
            //deb::dump($controller);
            $controller->actionAddadvert();

            //$this->render('/advert/addadvert');
        }

    }



    // Продление объявый
    public function actionAdvert_activate()
    {
        $n_id = intval($_GET['n_id']);

        if($advert = Notice::checkAdvertOwner(Yii::app()->user->id, $n_id))
        {
            if($advert->date_expire < (time()+86400*2) || $advert->active_tag == 0)
            {
                $props_array = AdvertController::GetPropsFromDatabase($n_id);

                $return_array['errors'] = array();
                if(!$advert->validate())
                {
                    $return_array['errors'] = $advert->getErrors();
                }

                $advertcontroller = new AdvertController('advertcontroller');
                $return_array = array_merge($return_array, $advertcontroller->CheckRequireNoticeProps($advert, $props_array));


                if(count($return_array['errors_props']) == 0 && count($return_array['errors']) == 0)
                {
                    if($advert->date_expire < (time()+86400*2))
                    {
                        if($advert->date_add_first == '')
                        {
                            $advert->date_add_first = $advert->date_add;
                        }
                        $new_date_add = time();
                        $advert->date_add = $new_date_add;
                        $advert->date_lastedit = $new_date_add;
                        $advert->date_expire = $new_date_add + $advert->expire_period*86400;
                        $advert->active_tag = 1;
                        $advert->save();
                    }

                    if($advert->active_tag == 0)
                    {
                        $advert->active_tag = 1;
                        $advert->save();
                    }

                    header('Location: '.$_SERVER['HTTP_REFERER']);
                    die();

                }
                else
                {
                    $redirect_url = Yii::app()->createUrl('usercab/advert_edit', array('n_id'=>$n_id, 'republic'=>'1'));
                    //deb::dump($redirect_url);
                    header('Location: '.$redirect_url);
                    die();
                }



            }

        }

        header('Location: '.$_SERVER['HTTP_REFERER']);
//die('stop');
    }


    public function actionAdvert_deactivate()
    {
        $n_id = intval($_GET['n_id']);

        if($advert = Notice::checkAdvertOwner(Yii::app()->user->id, $n_id))
        {
            $advert->active_tag = 0;
            $advert->save();
        }

        header('Location: '.$_SERVER['HTTP_REFERER']);
    }


    // Техподдержка
    public function actionSupport()
    {

        $this->render('support');
    }


    public function actionShowSupportCaptcha()
    {
        $captcha = new BaraholkaCaptcha();
        $captcha->renderImage();
        Yii::app()->session['supportcaptcha'] = $captcha->code;
    }

    public function actionSendSupport()
    {
        $ret = array();

        if(strlen($_POST['subject']) < 3)
        {
            $ret['status'] = 'error';
            $ret['message'] = 'Укажите тему запроса';
        }
        if(strlen($_POST['message']) < 10)
        {
            $ret['status'] = 'error';
            $ret['message'] = 'Текст сообщения слишком короткий';
        }

        if(isset($ret['status']) && $ret['status'] == 'error')
        {
            echo json_encode($ret);
            Yii::app()->end();
        }

        if(strtolower(Yii::app()->session['supportcaptcha']) == strtolower($_POST['verifycode']) )
        {
            $user = Users::model()->findByPk(Yii::app()->user->id);
            $user_page_url = "http://".$_SERVER['HTTP_HOST']."/user/uadverts/".Yii::app()->user->id;

            $emessage = $this->renderFile(Yii::app()->basePath.'/data/mailtemplates/mailtosupport.php',
                array(
                    'user_page_url'=>$user_page_url,
                    'privat_name'=>$user->privat_name,
                    'subject'=>$_POST['subject'],
                    'message'=>$_POST['message'],
                    'userid'=>Yii::app()->user->id,
                    'email'=>Yii::app()->user->email
                ),
                true);

            $result = BaraholkaMailer::SendSmtpMail(Yii::app()->params['smtp1_connect_data'], array(
                'mailto'=>Yii::app()->params['adminEmail'],
                'nameto'=>'Webmaster',
                'html_tag'=>true,
                'subject'=>"Запрос в техподдержку",
                'message'=>$emessage
            ));


            if($result == 'ok')
            {
                $ret['status'] = 'ok';
                $ret['message'] = 'Ваша запрос успешно отправлен!';
            }
            else
            {
                $ret['status'] = 'error';
                $ret['message'] = $result;
            }

        }
        else
        {
            $ret['status'] = 'error';
            $ret['message'] = 'Неверный код проверки!';
        }


        echo json_encode($ret);
        Yii::app()->end();

    }


    // Пометка удаленности объявы
    public function actionUserAdvertDel()
    {
        $n_id = intval($_POST['n_id']);

        $advert = Notice::model()->findByPk($n_id);
        if($advert->u_id == Yii::app()->user->id)
        {
            $advert->deleted_tag = 1;
            if($advert->save())
            {
                echo "del";
            }
        }
        else
        {
            echo "baduser";
        }

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