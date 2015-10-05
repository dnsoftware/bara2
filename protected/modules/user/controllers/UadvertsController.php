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
            'condition'=>'u_id = :u_id AND verify_tag = 1 AND active_tag = 1 ',
            'order'=>'date_add DESC',
            'params'=>array(':u_id'=>$u_id),
        )))
        {
            $useradverts_photos = array();
            foreach($useradverts as $ukey=>$uval)
            {
                $rub_counter[$uval->r_id]++;
                $rub_ids[$uval->r_id] = $uval->r_id;
                $towns_ids[$uval->t_id] = $uval->t_id;
            }
        }

        if($useradverts = Notice::model()->findAll(array(
            'select'=>'*',
            'condition'=>'u_id = :u_id AND verify_tag = 1 AND active_tag = 1 ' . $sql_rub,
            'order'=>'date_add DESC',
            'params'=>array(':u_id'=>$u_id),
        )))
        {
            $useradverts_photos = array();
            foreach($useradverts as $ukey=>$uval)
            {
                // Фотографии
                preg_match('|<vibor_type>photoblock</vibor_type>.+<hand_input_value>([^<]+)</hand_input_value>.+</item>|siU', $uval->props_xml, $match);
                $photos = explode(";", $match[1]);
                unset($photos[count($photos)-1]);

                $useradverts_photos[$uval->n_id] = $photos;

            }
        }

        //deb::dump($useradverts_photos);
        //deb::dump($useradverts);

        $subrub_array = array();
        $parent_ids = array();
        $parent_ids_count = array();
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

        // Города
        $towns = Towns::model()->findAll(array(
            'condition'=>'t_id IN ('.implode(",", $towns_ids).')'
        ));
        $towns_array = array();
        foreach($towns as $tkey=>$tval)
        {
            $towns_array[$tval->t_id] = $tval;
        }

        $parent_rubriks = Rubriks::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id IN ('.implode(", ", $parent_ids).')'
        ));




		$this->render('index', array(
            'u_id'=>$u_id, 'useradverts'=>$useradverts,
            'parent_rubriks'=>$parent_rubriks, 'subrub_array'=>$subrub_array,
            'parent_ids_count'=>$parent_ids_count, 'rub_counter'=>$rub_counter,
            'useradverts_photos'=>$useradverts_photos, 'towns_array'=>$towns_array,
            'transliter'=>$transliter
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