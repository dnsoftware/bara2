<?php

class AdminadvertController extends Controller
{
	public function actionIndex()
	{
        $rubriks = Rubriks::get_simple_rublist();

        $adverts = Notice::model()->findAll(array(
            'select'=>'*',
            'order'=>'date_add DESC'
        ));

        $props_array = array();
        foreach($adverts as $key=>$val)
        {
            $props_display = array();
            $photos = array();
            $xml = new SimpleXMLElement($val->props_xml);
            foreach($xml->block as $bkey=>$bval)
            {
                foreach($bval as $b2key=>$b2val)
                {
                    $temp = array();
                    foreach($b2val->item as $ikey=>$ival)
                    {
                        if($ival->hand_input_value != '')
                        {
                            $temp[] = (string)$ival->hand_input_value;
                            if($ival->vibor_type == 'photoblock')
                            {
                                if(strlen($ival->hand_input_value) > 0)
                                {
                                    $files_str = (string)$ival->hand_input_value;
                                    if($files_str[strlen($files_str)-1] == ';')
                                    {
                                        $files_str = substr($files_str, 0, strlen($files_str)-1);
                                    }
                                    $photos = explode(";", $files_str);
                                    //deb::dump($photos);
                                }
                            }
                        }
                        else
                        {
                            $temp[] = (string)$ival->value;
                        }
                    }

                    $props_display[$b2key] = implode(", ", $temp);
                    //deb::dump($props_display);
//            deb::dump($b2val);
                }
//        deb::dump($bval);
            }

            $props_array[$val->n_id]['photos'] = $photos;
            //deb::dump($props_array);
        }


        $this->render('index', array('adverts'=>$adverts, 'rubriks'=>$rubriks, 'props_array'=>$props_array));
	}


    // Активация/деактивация объявы
    public function actionSetadvert_act()
    {
        $n_id = intval($_POST['n_id']);

        $advert = Notice::model()->findByPk($n_id);
        $ret = '';

        if($advert->active_tag == 1)
        {
            $advert->active_tag = 0;
            $ret = 'deact';
        }
        else
        if($advert->active_tag == 0)
        {
            $advert->active_tag = 1;
            $ret = 'act';
        }

        if($advert->save())
        {
            echo $ret;
        }

    }

    // Верификация/деверификация объявы
    public function actionSetadvert_ver()
    {
        $n_id = intval($_POST['n_id']);

        $advert = Notice::model()->findByPk($n_id);
        $ret = '';

        if($advert->verify_tag == 1)
        {
            $advert->verify_tag = 0;
            $ret = 'deact';
        }
        else
            if($advert->verify_tag == 0)
            {
                $advert->verify_tag = 1;
                $ret = 'act';
            }

        if($advert->save())
        {
            echo $ret;
        }

    }

    // Пометка удаленности объявы
    public function actionSetadvert_del()
    {
        $n_id = intval($_POST['n_id']);

        $advert = Notice::model()->findByPk($n_id);
        $ret = '';

        if($advert->deleted_tag == 1)
        {
            $advert->deleted_tag = 0;
            $ret = 'act';
        }
        else
            if($advert->deleted_tag == 0)
            {
                $advert->deleted_tag = 1;
                $ret = 'deact';
            }

        if($advert->save())
        {
            echo $ret;
        }

    }


    // Удаление навсегда
    public function actionAdvert_kill()
    {
        $part_path = '/photos/';
        $n_id = intval($_POST['n_id']);
        //$n_id = intval($_GET['n_id']);

        if($advert = Notice::model()->findByPk($n_id))
        {
            $props_relate = RubriksProps::model()->with('notice_props')->find(array(
                'select'=>'*',
                'condition'=>'r_id='.$advert->r_id . " AND n_id=".$advert->n_id . " AND type_id = 'photo_block' ",
                'order'=>'t.hierarhy_tag DESC, t.hierarhy_level ASC, t.display_sort, t.rp_id'
            ));

            $photo_list = $props_relate->notice_props[0]['hand_input_value'];
            $photo_array = Notice::getImageArray($photo_list);
            //deb::dump($photo_array);
            if(count($photo_array) > 0)
            {
                foreach($photo_array as $pkey=>$pval)
                {
                    @unlink ( $_SERVER['DOCUMENT_ROOT']."/photos/".$pval);
                }
            }

            $res = NoticeProps::model()->deleteAll(array(
                'condition'=>'n_id = '.$n_id
            ));

            $advert->delete();

            echo "del";
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