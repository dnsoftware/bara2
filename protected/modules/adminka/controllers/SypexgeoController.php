<?php

class SypexgeoController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}


    public function actionLoad($selector)
    {
        $path = Yii::getPathOfAlias('webroot');

        $zipfilename = $path.'/tmp/sxgeo_info.zip';
        $urlload = 'https://sypexgeo.net/files/SxGeo_Info.zip';
        $extractpath = $path.'/tmp/sxgeo_info/';

        $step_array = array();

        // ******************** Загрузка справочников регионов с sypexgeo
        if($selector == 'load_and_unzip')
        {
            $error_count = 0;

            if(copy($urlload, $zipfilename))
            {
                $zip = new ZipArchive();
                if ($zip->open($zipfilename) !== true)
                {
                    $step_array[] = 'НЕ получилось открыть файл';
                    $error_count++;
                }

                if(!$zip->extractTo($extractpath))
                {
                    $step_array[] = 'Распаковка архива не удалась';
                    $error_count++;
                }
            }
            else
            {
                $step_array[] = 'Не получилось загрузить '.$urlload;
                $error_count++;
            }

            if($error_count > 0)
            {
                $this->render('update', array('step_array'=>$step_array));
            }
            else
            {
                header('Location: '.Yii::app()->createUrl('/adminka/sypexgeo/load', array('selector'=>'sxgeo_country') ));
                return true;
            }

        }
        // ******************** КОНЕЦ Загрузка справочников регионов с sypexgeo



        $connection=Yii::app()->db;

        /*
        $sql = "TRUNCATE sxgeo_country";
        $command = $connection->createCommand($sql);
        $res = $command->query();

        $sql = "LOAD DATA INFILE '".$extractpath."country.tsv' INTO TABLE sxgeo_country";
        $command = $connection->createCommand($sql);
        if($res = $command->query())
        {
            $step_array[] = 'База данных стран импортирована!';
        }
        */


        //******************************** Импорт стран в таблицу sxgeo_country

        if($selector == 'sxgeo_country')
        {
            $error_count = 0;

            $step_array[] = 'База данных стран/городов/регионов загружена';

            // Импорт стран
            $sql = "TRUNCATE sxgeo_country";
            $command = $connection->createCommand($sql);
            $res = $command->query();
            $csvIterator = new CsvIterator($extractpath."country.tsv", "\t");
            $i = 0;
            while ($csvIterator->next())
            {
                $i++;
                $cur = $csvIterator->current();

                $row = new SxgeoCountry();
                $row->id = $cur[0];
                $row->iso = $cur[1];
                $row->continent = $cur[2];
                $row->name_ru = $cur[3];
                $row->name_en = $cur[4];
                $row->lat = $cur[5];
                $row->lon = $cur[6];
                $row->timezone = $cur[7];
                $row->save();

                if($row->hasErrors())
                {
                    //deb::dump($cur);
                    //deb::dump($row->getErrors());
                }

            }
            $step_array[] = 'В таблицу sxgeo_country внесено '.$i.' записей!';

            if($error_count > 0)
            {
                $this->render('update', array('step_array'=>$step_array));
            }
            else
            {
                header('Location: '.Yii::app()->createUrl('/adminka/sypexgeo/load', array('selector'=>'sxgeo_region') ));
                return true;
            }

        }
        //******************************** КОНЕЦ Импорт стран в таблицу sxgeo_country



        //******************************** Импорт регионов в таблицу sxgeo_region

        if($selector == 'sxgeo_region')
        {
            $error_count = 0;

            // Импорт регионов
            $sql = "TRUNCATE sxgeo_regions";
            $command = $connection->createCommand($sql);
            $res = $command->query();
            $csvIterator = new CsvIterator($extractpath."region.tsv", "\t");
            $i = 0;
            while ($csvIterator->next())
            {
                $i++;
                $cur = $csvIterator->current();

                $row = new SxgeoRegions();
                $row->id = $cur[0];
                $row->iso = $cur[1];
                $row->country = $cur[2];
                $row->name_ru = $cur[3];
                $row->name_en = $cur[4];
                $row->timezone = $cur[5];
                $row->okato = $cur[6];
                $row->save();

                //deb::dump($row->attributes);
                //die();
                if($row->hasErrors())
                {
                    //deb::dump($cur);
                    //deb::dump($row->getErrors());
                }

            }
            $step_array[] = 'В таблицу sxgeo_regions внесено '.$i.' записей!';

            if($error_count > 0)
            {
                $this->render('update', array('step_array'=>$step_array));
            }
            else
            {
                header('Location: '.Yii::app()->createUrl('/adminka/sypexgeo/load', array('selector'=>'sxgeo_town_prepare') ));
                return true;
            }

        }


        //********************* Очистка таблицы sxgeo_cities и старт импорта с первой строки
        if($selector == 'sxgeo_town_prepare')
        {
            // Импорт городов
            $sql = "TRUNCATE sxgeo_cities";
            $command = $connection->createCommand($sql);
            $res = $command->query();

            header('Location: '.Yii::app()->createUrl('/adminka/sypexgeo/load',
                                        array('selector'=>'sxgeo_town', 'fromstr'=>0) ));
            return true;

        }


        // ********************** Импорт из городов текстового файла в таблицу sxgeo_cities
        if($selector == 'sxgeo_town')
        {
            $block_count = 10000;   // Погружаем блоками в $block_count строк

            $fromstr = intval($_GET['fromstr']);

            $towns_array = array();

            $f = fopen($extractpath."city.tsv", "r");
            // Читать построчно до конца файла
            while(!feof($f)) {
                $towns_array[] = trim(fgets($f));
            }
            fclose($f);

            $tostr = $fromstr + $block_count;
            for($i=$fromstr; $i<$tostr; $i++)
            {
                $cur = explode(Chr(9), $towns_array[$i]);

                $dup_row = SxgeoCities::model()->findByPk($cur[0]);
                if(!$dup_row)
                {
                    $row = new SxgeoCities();
                    $row->id = $cur[0];
                    $row->region_id = $cur[1];
                    $row->name_ru = $cur[2];
                    $row->name_en = $cur[3];
                    $row->lat = $cur[4];
                    $row->lon = $cur[5];
                    $row->okato = '';
                    if(isset($cur[6]))
                    {
                        $row->okato = $cur[6];
                    }

                    $row->save();
                }

            }

            if($tostr < count($towns_array))
            {
                header('Location: '.Yii::app()->createUrl('/adminka/sypexgeo/load',
                        array('selector'=>'sxgeo_town', 'fromstr'=>$tostr) ));
                return true;
            }
            else
            {
                header('Location: '.Yii::app()->createUrl('/adminka/sypexgeo/load',
                        array('selector'=>'sxload_stat') ));
                return true;
            }

        }

        if($selector == 'sxload_stat')
        {
            $countries_kol = SxgeoCountry::model()->count(array('select'=>'1'));
            $regions_kol = SxgeoRegions::model()->count(array('select'=>'1'));
            $towns_kol = SxgeoCities::model()->count(array('select'=>'1'));

            $this->render('sxload_stat', array(
                'countries_kol'=>$countries_kol,
                'regions_kol'=>$regions_kol,
                'towns_kol'=>$towns_kol
            ));

        }

        // ********************** КОНЕЦ Импорт из городов текстового файла в таблицу sxgeo_cities




    }

    public function actionUpdate($selector)
    {
        $step_array = array();
        $supporter = new Supporter();


        // *************** Подготовка сводного массива всех транслитераций из стран, регионов и городов
        // *************** Уникализация
        if($selector == 'unicalizate')
        {
            $translit_array = array();

            // Страны
            $countries = Countries::model()->findAll(array(
                'select'=>'c_id, transname'
            ));
            foreach($countries as $key=>$val)
            {
                $translit_array[$val->transname][] = array('c_id'=>$val->c_id);
            }
            unset($countries);

            //Регионы
            $regions = Regions::model()->findAll(array(
                'select'=>'reg_id, transname'
            ));
            foreach($regions as $key=>$val)
            {
                $translit_array[$val->transname][] = array('reg_id'=>$val->reg_id);
            }
            unset($regions);

            //Города
            $towns = Towns::model()->findAll(array(
                'select'=>'t_id, transname'
            ));
            foreach($towns as $key=>$val)
            {
                $translit_array[$val->transname][] = array('t_id'=>$val->t_id);
            }
            unset($towns);

            foreach($translit_array as $key=>$val)
            {
                if(count($val) > 1)
                {
                    deb::dump($key);
                    deb::dump($val);
                    echo '<br>';

                    foreach($val as $key2=>$val2)
                    {
                        if($key2 != 0)
                        {
                            foreach($val2 as $key3=>$val3)
                            {
                                switch($key3)
                                {
                                    case "t_id":
                                        $transname = $key."_town".$val3;
                                        $town = Towns::model()->findByPk($val3);
                                        $town->transname = $transname;
                                        $town->save();
                                        break;

                                    case "reg_id":
                                        $transname = $key."_region".$val3;
                                        $region = Regions::model()->findByPk($val3);
                                        $region->transname = $transname;
                                        $region->save();
                                        break;

                                    case "c_id":
                                        $transname = $key."_country".$val3;
                                        $country = Countries::model()->findByPk($val3);
                                        $country->transname = $transname;
                                        $country->save();
                                        break;

                                }
                            }
                        }
                    }
                }
            }

            header('Location: '.Yii::app()->createUrl('/adminka/sypexgeo/update',
                    array('selector'=>'countries_update') ));
            return true;


        }
        // ******* Конец Подготовка сводного массива всех транслитераций из стран, регионов и городов

        $countries_data = Countries::model()->findAll();
        $countries_bykod = array();
        foreach ($countries_data as $ckey=>$cval)
        {
            $countries_bykod[$cval->iso] = $cval;
        }


        // Обновление стран
        if($selector == 'countries_update')
        {
            $from = SxgeoCountry::model()->findAll();
            foreach ($from as $fkey=>$fval)
            {
                if($to = Countries::model()->findByPk($fval->id))
                {
                    $to->name_en = $fval->name_en;
                    //$to->transname = $supporter->TranslitForUrl($fval->name_en);;
                    $to->iso = $fval->iso;
                    $to->continent = $fval->continent;
                    $to->lat = $fval->lat;
                    $to->lon = $fval->lon;
                    $to->timezone = $fval->timezone;
                    $to->save();

                }
                else
                {
                    $to = new Countries();
                    $to->c_id = $fval->id;
                    $to->name = $fval->name_ru;
                    $to->name_en = $fval->name_en;
                    $to->sort_number = 10000;
                    $to->transname = $supporter->TranslitForUrl($fval->name_en);
                    $to->iso = $fval->iso;
                    $to->continent = $fval->continent;
                    $to->lat = $fval->lat;
                    $to->lon = $fval->lon;
                    $to->timezone = $fval->timezone;
                    $to->save();

                    $step_array[] = 'Добавлена страна "'.$to->name.'"';
                }

            }

            header('Location: '.Yii::app()->createUrl('/adminka/sypexgeo/update',
                    array('selector'=>'regions_update') ));
            return true;

        }


        // Обновление регионов
        if($selector == 'regions_update')
        {
            $from = SxgeoRegions::model()->findAll();
            $i=0;
            foreach ($from as $fkey=>$fval)
            {
                if($to = Regions::model()->findByPk($fval->id))
                {
                    $to->c_id = $countries_bykod[$fval->country]->c_id;
                    $to->name = $fval->name_ru;
                    $to->name_en = $fval->name_en;
                    //$to->transname = $supporter->TranslitForUrl($fval->name_en);;
                    $to->iso = $fval->iso;
                    $to->country = $fval->country;
                    $to->timezone = $fval->timezone;
                    $to->okato = $fval->okato;
                    $to->save();

                }
                else
                {
                    $i++;
                    $to = new Regions();
                    $to->reg_id = $fval->id;
                    $to->c_id = $countries_bykod[$fval->country]->c_id;
                    $to->name = $fval->name_ru;
                    $to->name_en = $fval->name_en;
                    $to->transname = $supporter->TranslitForUrl($fval->name_en);;
                    if($transcheck = Regions::model()->findByAttributes(array('transname'=>$to->transname)))
                    {
                        $to->transname .= '_region'.$fval->id;
                    }
                    $to->iso = $fval->iso;
                    $to->country = $fval->country;
                    $to->timezone = $fval->timezone;
                    $to->okato = $fval->okato;
                    $to->save();

                    $step_array[] = 'Добавлен регион "'.$to->name.'"';
                }

            }

            header('Location: '.Yii::app()->createUrl('/adminka/sypexgeo/update',
                    array('selector'=>'towns_update', 'from_id'=>0) ));
            return true;


        }



        // Обновление городов
        if($selector == 'towns_update')
        {
            $block_count = 1000;   // Погружаем блоками в $block_count строк
            $from_id = intval($_GET['from_id']);


            $regions_data = Regions::model()->findAll();
            $regions_bykod = array();
            foreach ($regions_data as $ckey=>$cval)
            {
                $regions_bykod[$cval->reg_id] = $cval->c_id;
            }

            $from = SxgeoCities::model()->findAll(array(
                'select'=>'*',
                'condition'=>'id > '.$from_id,
                'order'=>'id ASC',
                'limit'=>$block_count
            ));

            if($from)
            {
                $i=0;
                foreach ($from as $fkey=>$fval)
                {
                    if($to = Towns::model()->findByPk($fval->id))
                    {
                        $to->reg_id = $fval->region_id;
                        $to->c_id = $regions_bykod[$fval->region_id];
                        $to->name = $fval->name_ru;
                        $to->name_en = $fval->name_en;
                        //$to->transname = $supporter->TranslitForUrl($fval->name_en);;
                        $to->inname = "in ".$fval->name_en;
                        $to->lat = $fval->lat;
                        $to->lon = $fval->lon;
                        $to->okato = $fval->okato;
                        $to->save();
                    }
                    else
                    {
                        if($fval->region_id > 0)
                        {
                            $i++;
                            $to = new Towns();
                            $to->t_id = $fval->id;
                            $to->reg_id = $fval->region_id;
                            $to->c_id = $regions_bykod[$fval->region_id];
                            $to->name = $fval->name_ru;
                            $to->name_en = $fval->name_en;
                            $to->transname = $supporter->TranslitForUrl($fval->name_en);;
                            if($transcheck = Towns::model()->findByAttributes(array('transname'=>$to->transname)))
                            {
                                $to->transname .= '_town'.$fval->id;
                            }
                            $to->inname = "in ".$fval->name_en;
                            $to->lat = $fval->lat;
                            $to->lon = $fval->lon;
                            $to->okato = $fval->okato;
                            $to->date_add = time();

                            $to->save();

                            /*
                            if($to->hasErrors())
                            {
                                deb::dump($to->getErrors());
                            }
                            */

                            $step_array[] = 'Добавлен город "'.$to->name.'"';
                        }
                    }

                }

                header('Location: '.Yii::app()->createUrl('/adminka/sypexgeo/update',
                        array('selector'=>'towns_update', 'from_id'=>$fval->id, 'rnd'=>rand(11111, 99999)) ));
                return true;

            }
            else
            {
                $new_update_from = time() - 600;
                $new_towns = Towns::model()->findAll(array(
                    'select'=>'*',
                    'condition'=>'date_add > "$new_update_from"',
                ));
                $step_array[] = 'Таблицы обновлены!';
                $this->render('update2', array('step_array'=>$step_array, 'new_towns'=>$new_towns));
            }


        }



    }


    public function actionLoadbase()
    {
        $path = Yii::getPathOfAlias('webroot');

        $zipfilename = $path.'/tmp/sxgeocity.zip';
        $urlload = 'https://sypexgeo.net/files/SxGeoCity_utf8.zip';
        $extractpath = $path.'/sypexgeo/';

        $step_array = array();
        $connection=Yii::app()->db;

        if(copy($urlload, $zipfilename))
        {
            $step_array[] = 'Файл с базой загружен!';

            $zip = new ZipArchive();
            if ($zip->open($zipfilename) !== true)
            {
                $step_array[] = 'НЕ получилось открыть файл';
            }

            if($zip->extractTo($extractpath))
            {
                $step_array[] = 'База данных IP адресов загружена! Обновление завершено!!!';

            }
        }
        else
        {
            $step_array[] = 'Ошибка при копировании файла с базой!';

        }

        $this->render('loadbase', array('step_array'=>$step_array));
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