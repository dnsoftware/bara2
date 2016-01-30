<?php

class UadvertsController extends Controller
{
	public function actionIndex($u_id)
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

        if($useradverts = Notice::model()->findAll(array(
            'select'=>'*',
            'condition'=>'u_id = :u_id AND verify_tag = 1 AND active_tag = 1 AND deleted_tag = 0 AND date_expire > "'.time().'" ',
            'order'=>'date_add DESC',
            'params'=>array(':u_id'=>$u_id),
        )))
        {
            foreach($useradverts as $ukey=>$uval)
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
        if($useradverts = Notice::model()->findAll(array(
            'select'=>'*',
            'condition'=>'u_id = :u_id AND verify_tag = 1 AND active_tag = 1 AND deleted_tag = 0 AND date_expire > "'.time().'" ' . $sql_rub,
            'order'=>'date_add DESC',
            'params'=>array(':u_id'=>$u_id),
        )))
        {
            foreach($useradverts as $ukey=>$uval)
            {
                $search_adverts[$uval->n_id] = $uval->attributes;
                $search_adverts[$uval->n_id]['town_name'] = $towns_array[$uval->t_id]->name;
                $search_adverts[$uval->n_id]['town_transname'] = $towns_array[$uval->t_id]->transname;
            }

            // Подготовка данных для отображения
            //// Шаблоны отображения из рубрик
            $shablons_display = Rubriks::GetShablonsDisplay();
            $rubriks_all_array = Rubriks::get_all_subrubs();
            $props_array = Notice::DisplayAdvertsList($search_adverts, $shablons_display, $rubriks_all_array);

            /*
            $useradverts_photos = array();
            foreach($useradverts as $ukey=>$uval)
            {
                // Фотографии
                preg_match('|<vibor_type>photoblock</vibor_type>.+<hand_input_value>([^<]+)</hand_input_value>.+</item>|siU', $uval->props_xml, $match);
                $photos = explode(";", $match[1]);
                unset($photos[count($photos)-1]);

                $useradverts_photos[$uval->n_id] = $photos;

            }
            */

        }

        //deb::dump($search_adverts);
        //deb::dump($useradverts);
        //die();

        $subrub_array = array();
        $parent_ids = array();
        $parent_ids_count = array();

        $rubriks = array();
        $parent_rubriks = array();
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

        $user = Users::model()->findByPk($u_id);


		$this->render('index', array(
            'u_id'=>$u_id,
            //'useradverts'=>$useradverts,
            'parent_rubriks'=>$parent_rubriks, 'subrub_array'=>$subrub_array,
            'parent_ids_count'=>$parent_ids_count, 'rub_counter'=>$rub_counter,
            //'useradverts_photos'=>$useradverts_photos, 'towns_array'=>$towns_array,
            'transliter'=>$transliter,
            'user'=>$user,
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
	}


    // Избранное
    public function actionFavorit()
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

        if(Notice::GetFavoritCount() > 0)
        {
            $n_ids = str_replace(";", ",", Yii::app()->request->cookies['favorit']);

            if($useradverts = Notice::model()->findAll(array(
                'select'=>'*',
                'condition'=>'n_id IN ('.$n_ids.') AND verify_tag = 1 AND active_tag = 1 ',
                'order'=>'date_add DESC'
            )))
            {
                foreach($useradverts as $ukey=>$uval)
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
            if($useradverts = Notice::model()->findAll(array(
                'select'=>'*',
                'condition'=>'n_id IN ('.$n_ids.') AND verify_tag = 1 AND active_tag = 1 ' . $sql_rub,
                'order'=>'date_add DESC',
            )))
            {

                foreach($useradverts as $ukey=>$uval)
                {
                    $search_adverts[$uval->n_id] = $uval->attributes;
                    $search_adverts[$uval->n_id]['town_name'] = $towns_array[$uval->t_id]->name;
                    $search_adverts[$uval->n_id]['town_transname'] = $towns_array[$uval->t_id]->transname;
                }

                // Подготовка данных для отображения
                //// Шаблоны отображения из рубрик
                $shablons_display = Rubriks::GetShablonsDisplay();
                $rubriks_all_array = Rubriks::get_all_subrubs();
                $props_array = Notice::DisplayAdvertsList($search_adverts, $shablons_display, $rubriks_all_array);

                /*
                $useradverts_photos = array();
                foreach($useradverts as $ukey=>$uval)
                {
                    // Фотографии
                    preg_match('|<vibor_type>photoblock</vibor_type>.+<hand_input_value>([^<]+)</hand_input_value>.+</item>|siU', $uval->props_xml, $match);
                    $photos = explode(";", $match[1]);
                    unset($photos[count($photos)-1]);

                    $useradverts_photos[$uval->n_id] = $photos;

                }
                */


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

                $parent_rubriks = Rubriks::model()->findAll(array(
                    'select'=>'*',
                    'condition'=>'r_id IN ('.implode(", ", $parent_ids).')'
                ));

            }



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


        $this->render('favorit', array(
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

    }


    // Избранное
    public function actionLastvisit()
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

        if(Notice::GetLastvisitCount() > 0)
        {

            $cookie = Yii::app()->request->cookies['last_visit_adverts'];
            $useradverts = unserialize($cookie->value);
            arsort($useradverts);
            $lastvisitdate = $useradverts;

            $n_ids_array = array();
            foreach($useradverts as $tkey=>$tval)
            {
                $n_ids_array[$tkey] = $tkey;
            }
            $n_ids = implode(",", $n_ids_array);

            if($useradverts_temp = Notice::model()->findAll(array(
                'select'=>'*',
                'condition'=>'n_id IN ('.$n_ids.') AND verify_tag = 1 AND active_tag = 1 ',
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
                'condition'=>'n_id IN ('.$n_ids.') AND verify_tag = 1 AND active_tag = 1 ' . $sql_rub,
                'order'=>'date_add DESC',
            )))
            {
                foreach($useradverts_temp as $ukey=>$uval)
                {
                    $search_adverts[$uval->n_id] = $uval->attributes;
                    $search_adverts[$uval->n_id]['town_name'] = $towns_array[$uval->t_id]->name;
                    $search_adverts[$uval->n_id]['town_transname'] = $towns_array[$uval->t_id]->transname;
                }

                // Подготовка данных для отображения
                //// Шаблоны отображения из рубрик
                $shablons_display = Rubriks::GetShablonsDisplay();
                $rubriks_all_array = Rubriks::get_all_subrubs();
                $props_array = Notice::DisplayAdvertsList($search_adverts, $shablons_display, $rubriks_all_array);

                /*
                $useradverts_photos = array();
                foreach($useradverts_temp as $ukey=>$uval)
                {
                    // Заносим в нужном порядке сортировки
                    $useradverts[$uval->n_id] = $uval;

                    // Фотографии
                    preg_match('|<vibor_type>photoblock</vibor_type>.+<hand_input_value>([^<]+)</hand_input_value>.+</item>|siU', $uval->props_xml, $match);
                    $photos = explode(";", $match[1]);
                    unset($photos[count($photos)-1]);

                    $useradverts_photos[$uval->n_id] = $photos;

                }
                */

            }

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



        $this->render('lastvisit', array(
            //'useradverts'=>$useradverts,
            'parent_rubriks'=>$parent_rubriks, 'subrub_array'=>$subrub_array,
            'parent_ids_count'=>$parent_ids_count, 'rub_counter'=>$rub_counter,
            //'useradverts_photos'=>$useradverts_photos, 'towns_array'=>$towns_array,
            'transliter'=>$transliter, 'lastvisitdate'=>$lastvisitdate,
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