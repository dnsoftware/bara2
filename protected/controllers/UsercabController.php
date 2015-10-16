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
            'condition'=>'u_id = '.Yii::app()->user->id,
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
            'condition'=>'u_id = '. Yii::app()->user->id . $sql_rub,
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
                    $subrub_array[$rval->r_id] = $rval;
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
        if($advert = Notice::checkAdvertOwner(Yii::app()->user->id, $n_id))
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