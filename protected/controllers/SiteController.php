<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

    public function actionIndex()
    {
        //$support = new Supporter();
        //$support->MakeTranslitAll();
        //deb::dump($row);


        /*
        // Генерация xml для всех фото
        $notices = Notice::model()->findAll();
        foreach($notices as $nkey=>$nval)
        {
            AdvertController::PropsXmlGenerate($nval->n_id);
        }
        */

//deb::dump(Yii::app()->session['usercheckphone_code']);
//deb::dump(Yii::app()->session['usercheckphone_message_id']);


        $countries = Countries::model()->findAll();
        $regions = Regions::model()->findAll(array(
            'condition'=>'c_id=1',
            'order'=>'name'
        ));

        /*
        $path = Yii::getPathOfAlias('webroot');
        $SxGeo = new SxGeo($path.'/sypexgeo/SxGeoCity.dat');
        $ip = $_SERVER['REMOTE_ADDR'];
        $geodata = $SxGeo->getCityFull($ip);
        //deb::dump($geodata);

        if(isset($geodata['city']))
        {
            if($city = Towns::model()->findByPk($geodata['city']['id']))
            {
                header('Location: /'.$city->transname);
            }

        }
        else
        {
            $this->render('index', array('countries'=>$countries, 'regions'=>$regions));
        }
        */


        $this->render('index', array('countries'=>$countries, 'regions'=>$regions));

    }



    // Установка куки подтверждения выбора региона
    public function actionSetregconfirmyes()
    {
        $cookie = new CHttpCookie('region_confirm_tag', 1);
        $cookie->expire = time() + 86400*30*12;
        Yii::app()->request->cookies['region_confirm_tag'] = $cookie;

        FilterController::SetGeolocatorCookie('geo_mytown_handchange_tag', 1, 86400*30);
    }

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}


    // Карта сайт
    public function actionMap()
    {

        $data = $this->GetCategoryMapData();

        $this->render('map',array(
            'rubriks'=>$data['rubriks'],
            'first_level_rub'=>$data['first_level_rub'],
            'first_level_props'=>$data['first_level_props'],
            'second_level'=>$data['second_level']
        ));

    }


    // Генерация файлов карты сайта
    public function actionMakeSitemap()
    {
        $this->MakeSitemapCategories();
        $this->MakeSitemapActualAdverts();
        $ret = $this->MakeSitemapArchiveAdverts();

        // Генерация индексного файла
        $filename = $_SERVER['DOCUMENT_ROOT']."/sitemapindex.xml";
        $url_prefix = "http://".Yii::app()->params['basehost'];

        @unlink($filename);

        if (!$handle = fopen($filename, 'a')) {
            echo "Не могу открыть файл ($filename)";
            exit;
        }

        $header = '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        if (is_writable($filename))
        {
            fwrite($handle, $header);

            // Ссылка на сайтмап с актуальными
            $loc = $url_prefix."/sitemapactual.xml";
            $date = date(DateTime::W3C, time());
            $content = '<sitemap>'."\n";
            $content .= '<loc>'.$loc.'</loc>'."\n";
            $content .= '<lastmod>'.$date.'</lastmod>'."\n";
            $content .= '</sitemap>'."\n";
            fwrite($handle, $content);

            // Ссылка на сайтмап с категориями
            $loc = $url_prefix."/sitemapcat.xml";
            $content = '<sitemap>'."\n";
            $content .= '<loc>'.$loc.'</loc>'."\n";
            $content .= '</sitemap>'."\n";
            fwrite($handle, $content);

            ///// Генерация ссылок на сайтмапы архива
            $archives = SitemapScanMetadata::model()->findAll(array(
                'select'=>'*',
                'condition'=>'archive_tag = 1',
                'order'=>'block_index ASC'
            ));
            $total_count = count($archives);
            $count = 0;
            foreach($archives as $ukey=>$uval)
            {
                $count++;
                $loc = $url_prefix."/".$uval['filename'];

                $content = '<sitemap>'."\n";
                $content .= '<loc>'.$loc.'</loc>'."\n";
                if(isset($ret['scan_date']))
                {
                    $date = date(DateTime::W3C, $ret['scan_date']);
                }
                if($count == $total_count)
                {
                    $date = date(DateTime::W3C, time());
                }
                if(isset($date))
                {
                    $content .= '<lastmod>'.$date.'</lastmod>'."\n";
                }
                $content .= '</sitemap>'."\n";

                fwrite($handle, $content);
            }
            ///// КОНЕЦ Генерация ссылок на сайтмапы архива

            fwrite($handle, '</sitemapindex>');
            fclose($handle);
        }
        else
        {
            echo "Файл $filename недоступен для записи";
        }

    }


    // Генерация sitemap для карты категорий
    public function MakeSitemapCategories()
    {
        $url_prefix = "http://".Yii::app()->params['basehost'];

        $data = $this->GetCategoryMapData();
        $rubriks = $data['rubriks'];
        $first_level_rub = $data['first_level_rub'];
        $first_level_props = $data['first_level_props'];
        $second_level = $data['second_level'];

        $urls_array = array();
        foreach($rubriks as $rkey=>$rval)
        {
            // Титул
            $temp = array();
            $temp['loc'] = $url_prefix;
            $temp['lastmod'] = date("Y-m-d", time());
            $temp['changefreq'] = 'always';
            $temp['priority'] = '1.0';
            $urls_array[] = $temp;


            // Рубрика
            $temp = array();
            $temp['loc'] = $url_prefix."/all/". $rval['parent']->transname;
            $temp['lastmod'] = date("Y-m-d", time());
            $temp['changefreq'] = 'hourly';
            $temp['priority'] = '0.9';
            $urls_array[] = $temp;

            foreach($rval['childs'] as $r2key=>$r2val)
            {

                // Подрубрика
                $temp = array();
                $temp['loc'] = $url_prefix."/all/". $r2val->transname;
                $temp['lastmod'] = date("Y-m-d", time());
                $temp['changefreq'] = 'hourly';
                $temp['priority'] = '0.8';
                $urls_array[] = $temp;

                if(isset($first_level_rub[$r2val->r_id]))
                {
                    foreach($first_level_props[$first_level_rub[$r2val->r_id]->rp_id] as $fkey=>$fval)
                    {
                        if(!in_array($fval->ps_id, PropsSprav::$sitemap_ps_id_first_for_second))
                        {
                            // Первое свойство без подсвойств
                            $temp = array();
                            $temp['loc'] = $url_prefix."/all/". $r2val->transname."/".$fval->transname;
                            $temp['changefreq'] = 'daily';
                            $temp['priority'] = '0.7';
                            $urls_array[] = $temp;

                        }
                        else
                        {
                            // Первое свойство с подсвойствами
                            $temp = array();
                            $temp['loc'] = $url_prefix."/all/". $r2val->transname."/".$fval->transname;
                            $temp['changefreq'] = 'daily';
                            $temp['priority'] = '0.7';
                            $urls_array[] = $temp;

                            foreach($second_level[$fval->ps_id] as $skey=>$sval)
                            {
                                // Подсвойство
                                $temp = array();
                                $temp['loc'] = $url_prefix."/all/". $r2val->transname."/".$fval->transname."/".$sval['transname'];
                                $urls_array[] = $temp;
                            }

                        }

                    }
                }

            }

        }

        $filename = $_SERVER['DOCUMENT_ROOT']."/sitemapcat.xml";
        //deb::dump($filename);
        $this->SiteMapFileGenerate($filename, $urls_array);
//deb::dump($urls_array);
    }

    // Генерация sitemap для архивных объявлений
    public function MakeSitemapArchiveAdverts()
    {
        $connection = Yii::app()->db;

        $global_start_date = 1317600000;        // Дата самого старого объявления
        $basefilename = 'sitemaparch';          // Базовое название файлов сайтмапа архивных объяв,
                                                // к нему прибавляется суффикс-порядковый номер
        $basehoursperiod = 24*365;              // базовый размер сканируемого блока в часах
                                                // в дальнейшем автокорректируется в зависимости от колва объяв
        $base_scandateafter_period = 86400*7;   // Сканировать через указанный период (базовое значение)
        $min_count_in_block = 30000;            // Минимальное кол-во ссылок в блоке
        $max_count_in_block = 40000;            // Максимальное кол-во ссылок в блоке
                                                // Если получается больше - корректируется период сканирования
        $period_scan_hours_correct_count = 10;  // Кол-во часов на которое корректируется период сканирования
                                                // при превышении/пременьшении граничных значений

        $ret = array();

        // Начальная инициализация
        if(!$scan = SitemapScanMetadata::model()->find(array(
            'select'=>'*',
            'condition'=>'archive_tag = 1 AND block_index = 1'
        )))
        {
            $scan = new SitemapScanMetadata();
            $scan->date_expire_start = $global_start_date;
            $scan->date_expire_end = $scan->date_expire_start + $basehoursperiod*3600;
            $scan->archive_tag = 1;
            $scan->block_index = 1;
            $scan->filename = $basefilename.$scan->block_index.".xml";
            $scan->block_scan_date = 0;
            $scan->scan_date_after = $scan->block_scan_date + $base_scandateafter_period;
            $scan->scan_hours_count = $basehoursperiod;
            $scan->urls_count = 0;
            $scan->save();
        }

        // Пересчет диапазонов сканирования
        $this->RecalcScanBorders(1);

        // Поэтапное сканирование
        $block_index = 1;
        do
        {
            if($scan = SitemapScanMetadata::model()->find(array(
                'select'=>'*',
                'condition'=>'archive_tag = 1 AND block_index = '.$block_index
            )))
            {


            }
            else
            {
                $prevscan = SitemapScanMetadata::model()->find(array(
                    'select'=>'*',
                    'condition'=>'archive_tag = 1 AND block_index = '.($block_index-1)
                ));

                $date_expire_start = $prevscan->date_expire_end;
                $date_expire_end = $date_expire_start + $basehoursperiod*3600;

                $scan = new SitemapScanMetadata();
                $scan->date_expire_start = $date_expire_start;
                $scan->date_expire_end = $date_expire_end;
                $scan->archive_tag = 1;
                $scan->block_index = $block_index;
                $scan->filename = $basefilename.$scan->block_index.".xml";
                $scan->block_scan_date = 0;
                $scan->scan_date_after = $scan->block_scan_date + $base_scandateafter_period;
                $scan->scan_hours_count = $basehoursperiod;
                $scan->urls_count = 0;

                if($scan->date_expire_start < time())
                {
                    $scan->save();
                }
                else
                {
                    $scan = $prevscan;
                    break;
                }
            }

            if($scan->scan_date_after <= time() && $scan->date_expire_start < time())
            {
                $date_expire_start = $scan->date_expire_start;
                $date_expire_end = $scan->date_expire_end;

                $sql = "SELECT n.title, n.daynumber_id, t.transname as town_transname, r.transname rub_transname
                        FROM
                        ". $connection->tablePrefix . "notice n use index (date_expire),
                        ". $connection->tablePrefix . "towns t,
                        ". $connection->tablePrefix . "rubriks r
                        WHERE  n.verify_tag = 1
                            AND (date_expire < '".$date_expire_end."' AND date_expire >= '".$date_expire_start."' )
                            AND n.t_id = t.t_id AND n.r_id = r.r_id
                        ORDER BY n.date_expire ASC";
                $command = $connection->createCommand($sql);
                $adverts = $command->queryAll();

                $this->SitemapArchiveBlockGenerate($adverts, $scan->filename);


                if(count($adverts) > 0 && count($adverts) > $max_count_in_block)
                {
                    $scan->scan_hours_count = $scan->scan_hours_count - $period_scan_hours_correct_count;
                }
                else
                if(count($adverts) > 0 && count($adverts) < $min_count_in_block)
                {
                    $scan->scan_hours_count = $scan->scan_hours_count + $period_scan_hours_correct_count;
                }

                $scan->urls_count = count($adverts);
                $scan->block_scan_date = time();
                $scan->scan_date_after = $scan->block_scan_date + $base_scandateafter_period;

                $scan->save();

            }

            if($block_index == 1)
            {
                $ret['scan_date'] = $scan->block_scan_date;
            }

            $block_index++;
        }
        //while(0);
        while($date_expire_end < time());

        // Для последнего блока обновление чаще
        $scan->scan_date_after = time() + 86400;
        $scan->save();

        return $ret;

    }


    // Генерация блока ссылок архивных объяв
    // $adverts - строки с данными
    public function SitemapArchiveBlockGenerate($adverts, $filename)
    {
        $transliter = new Supporter();
        $url_prefix = "http://".Yii::app()->params['basehost'];
        $filename = $_SERVER['DOCUMENT_ROOT']."/".$filename;
        $urls_array = array();

        foreach($adverts as $akey=>$aval)
        {
            $temp = array();
            $temp['loc'] = $url_prefix."/".$aval['town_transname']."/".$aval['rub_transname']."/".$transliter->TranslitForUrl($aval['title'])."_".$aval['daynumber_id'];
            $temp['changefreq'] = 'monthly';
            $urls_array[] = $temp;
        }

        $this->SiteMapFileGenerate($filename, $urls_array);

    }


    // Генерация sitemap для актуальных объявлений
    public function MakeSitemapActualAdverts()
    {
        $connection = Yii::app()->db;

        $transliter = new Supporter();
        $url_prefix = "http://".Yii::app()->params['basehost'];
        $filename = $_SERVER['DOCUMENT_ROOT']."/sitemapactual.xml";
        $urls_array = array();

        $sql = "SELECT n.title, n.daynumber_id, t.transname as town_transname, r.transname rub_transname
                        FROM
                        ". $connection->tablePrefix . "notice n use index (date_expire),
                        ". $connection->tablePrefix . "towns t,
                        ". $connection->tablePrefix . "rubriks r
                        WHERE  n.active_tag = 1 AND n.verify_tag = 1 AND n.deleted_tag = 0
                            AND date_expire >= '".time()."'
                            AND n.t_id = t.t_id AND n.r_id = r.r_id
                        ORDER BY n.date_expire ASC";
        $command = $connection->createCommand($sql);
        $adverts = $command->queryAll();

        foreach($adverts as $akey=>$aval)
        {
            $temp = array();
            $temp['loc'] = $url_prefix."/".$aval['town_transname']."/".$aval['rub_transname']."/".$transliter->TranslitForUrl($aval['title'])."_".$aval['daynumber_id'];
            $temp['changefreq'] = 'daily';
            $temp['priority'] = '0.9';
            $urls_array[] = $temp;
        }

        $this->SiteMapFileGenerate($filename, $urls_array);

    }


    //


    // Пересчет границ блоков сканирования
    public function RecalcScanBorders($archive_tag)
    {
        if($scans = SitemapScanMetadata::model()->findAll(array(
            'select'=>'*',
            'condition'=>'archive_tag = '.$archive_tag.' ',
            'order'=>'block_index ASC'
        )))
        {
            $i=0;
            $prev_date_expire_end = 0;
            foreach($scans as $key=>$val)
            {
                $i++;
                if($i == 1)
                {
                    $date_expire_start = $val->date_expire_start;
                }
                else
                {
                    $date_expire_start = $prev_date_expire_end;
                }
                $date_expire_end = $date_expire_start + $val->scan_hours_count*3600;

                $val->date_expire_start = $date_expire_start;
                $val->date_expire_end = $date_expire_end;
                $val->save();

                $prev_date_expire_end = $date_expire_end;
            }
        }

    }



    // Генерация файла в формате sitemap
    public function SiteMapFileGenerate($filename, $urls_array)
    {
        @unlink($filename);

        if (!$handle = fopen($filename, 'a')) {
            echo "Не могу открыть файл ($filename)";
            exit;
        }

        $header = '<?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        if (is_writable($filename))
        {
            fwrite($handle, $header);

            foreach($urls_array as $ukey=>$uval)
            {
                $content = '<url>'."\n";

                $uval['loc'] = htmlspecialchars($uval['loc'], ENT_QUOTES);
                $uval['loc'] = str_replace("&#039;", "&apos;", $uval['loc']);

                foreach($uval as $u2key=>$u2val)
                {
                    $content .= '<'.$u2key.'>'.$u2val.'</'.$u2key.'>'."\n";
                }
                $content .= '</url>'."\n";

                fwrite($handle, $content);
            }

            fwrite($handle, '</urlset>');
            fclose($handle);
        }
        else
        {
            echo "Файл $filename недоступен для записи";
        }

    }


    // Подготовка данных для генерации карты категорий
    public function GetCategoryMapData()
    {

        $connection = Yii::app()->db;

        $rubriks = Rubriks::get_rublist();

        $rubriks_props = RubriksProps::model()->findAll(array(
                'select'=>'*',
                'condition'=>"hierarhy_tag = 1 AND hierarhy_level = 1 AND use_in_filter = 1 ",
                'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
                //'limit'=>'10'
            )
        );

        $first_level_rp = array();
        $first_level_rub = array();
        foreach($rubriks_props as $rkey=>$rval)
        {
            $first_level_rp[$rval->rp_id] = $rval;
            $first_level_rub[$rval->r_id] = $rval;
        }

        $props_sprav = PropsSprav::model()->findAll(array(
            'select'=>'*',
            'condition'=>'rp_id IN ('.implode(", ", array_keys($first_level_rp)).')'
        ));

        $first_level_props = array();
        foreach($props_sprav as $pkey=>$pval)
        {
            $first_level_props[$pval->rp_id][$pval->ps_id] = $pval;
        }
        //deb::dump($first_level_props);

        // Свойства второго уровня для избранных
        $second_level = array();
        foreach(PropsSprav::$sitemap_ps_id_first_for_second as $pkey=>$pval)
        {
            $sql = "SELECT *
                            FROM ". $connection->tablePrefix . "rubriks_props rp ,
                            ". $connection->tablePrefix . "props_relations pr ,
                            ". $connection->tablePrefix . "props_sprav ps
                            WHERE ps.rp_id = rp.rp_id AND pr.child_ps_id = ps.ps_id
                                    AND rp.hierarhy_level = 2 AND pr.parent_ps_id = $pval

                            ";
            //deb::dump($sql);
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            while(($row = $dataReader->read())!==false)
            {
                $second_level[$pval][$row['ps_id']] = $row;
            }
        }

        $data = array();
        $data['rubriks'] = $rubriks;
        $data['first_level_rub'] = $first_level_rub;
        $data['first_level_props'] = $first_level_props;
        $data['second_level'] = $second_level;


        return $data;

    }















}


























