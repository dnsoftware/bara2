<?php

class SupporterController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

    // Получение курса рубля к доллару и евро с Центробанка
    public function actionGetCbrKurs()
    {
        $content = file_get_contents('http://www.cbr.ru/scripts/XML_daily.asp?date_req='.date('d/m/Y'));
        $content = iconv('Windows-1251', 'UTF-8', $content);
        //deb::dump($content);

        preg_match('|<ValCurs Date="([^\"]+)"|siU', $content, $match);
        deb::dump($match);
        $temp = explode("/", $match[1]);
        if(count($temp) != 3)
        {
            $temp = explode(".", $match[1]);
        }
        $kurs_date = mktime(12,0,0, intval($temp[1]), intval($temp[0]), intval($temp[2]));

        preg_match_all('|<NumCode>([0-9]+)</NumCode>[\n\t\r\s ]*<CharCode>([A-Z]+)</CharCode>[\n\t\r\s ]*<Nominal>([0-9]+)</Nominal>[\n\t\r\s ]*<Name>(.+)</Name>[\n\t\r\s ]*<Value>(.+)</Value>[\n\t\r\s ]*|siU', $content, $matches);

        $kurs_array = array();
        foreach($matches[1] as $mkey=>$mval)
        {
            $kurs_array[$matches[2][$mkey]] = $matches[5][$mkey];
        }
        deb::dump($kurs_array);


        $usd_kurs = str_replace(",", ".", $kurs_array['USD']);
        $eur_kurs = str_replace(",", ".", $kurs_array['EUR']);

        Options::setOption('kurs_date', $kurs_date);
        Options::setOption('kurs_usd', $usd_kurs);
        Options::setOption('kurs_eur', $eur_kurs);

        $valuta = Valutes::model()->findByPk('USD');
        $valuta->kurs = $usd_kurs;
        $valuta->save();

        $valuta = Valutes::model()->findByPk('EUR');
        $valuta->kurs = $eur_kurs;
        $valuta->save();


    }


    // Выставление куки валюты отображения цены
    public function actionSetValutaView()
    {
        unset(Yii::app()->request->cookies['user_valuta_view']);
        $cookie = new CHttpCookie('user_valuta_view', $_GET['valuta_view']);
        $cookie->expire = time() + 86400*30;
        Yii::app()->request->cookies['user_valuta_view'] = $cookie;

        header('Location: '. $_SERVER['HTTP_REFERER']);
    }



    // Отслеживание статуса СМС на bytehand
    public function actionSmsstatus()
    {
        if( ($smslog = UserPhonesSmsLog::model()->findByPk($_GET['message']))
            && Yii::app()->params['bytehand_id'] == $_GET['id']
            && Yii::app()->params['bytehand_key'] == $_GET['key']
            && $_GET['action'] == 'outgoingStatus')
        {
            $smslog->description = $_GET['description'];
            $smslog->error_code = $_GET['error_code'];
            $smslog->parts = $_GET['parts'];
            $smslog->cost = $_GET['cost'];
            $smslog->updated_at = $_GET['updated_at'];

            $smslog->save();
        }

    }



    // Вспомогательная ф-я. Импорт стран из sypexgeo
    public function actionImportCountrySxgeo()
    {
        ///////////////////////// Импорт стран sypexgeo ///////////////////////////
        /*
        // Сохрание старых кодов
        $countries = Countries::model()->findAll();
        foreach ($countries as $ckey=>$cval)
        {
            $cval->old_c_id = $cval->c_id;
            $cval->save();
        }
        */


        /*
        // Перенос данных
        $supporter = new Supporter();
        $countries = SxgeoCountry::model()->findAll();
        foreach ($countries as $ckey=>$cval)
        {
            if($tblcountry = Countries::model()->findByPk($cval->id))
            {
                $tblcountry->name = $cval->name_ru;
                $tblcountry->name_en = $cval->name_en;
                if($tblcountry->sort_number == 0)
                {
                    $tblcountry->sort_number = 10000;
                }
                $tblcountry->transname = $supporter->TranslitForUrl($cval->name_en);
                $tblcountry->iso = $cval->iso;
                $tblcountry->continent = $cval->continent;
                $tblcountry->lat = $cval->lat;
                $tblcountry->lon = $cval->lon;
                $tblcountry->timezone = $cval->timezone;
                $tblcountry->save();
            }
            else
            {
                $tblcountry = new Countries();

                $tblcountry->c_id = $cval->id;
                $tblcountry->name = $cval->name_ru;
                $tblcountry->name_en = $cval->name_en;
                $tblcountry->sort_number = 10000;
                $tblcountry->transname = $supporter->TranslitForUrl($cval->name_en);
                $tblcountry->iso = $cval->iso;
                $tblcountry->continent = $cval->continent;
                $tblcountry->lat = $cval->lat;
                $tblcountry->lon = $cval->lon;
                $tblcountry->timezone = $cval->timezone;
                $tblcountry->save();
            }

        }
        */

        /*
        // Занесение телефонных кодов
        $content = file_get_contents('http://numberphone.ru/world?sss');
        preg_match('|<table class="table table-condensed table-hover" (.+)</table>|siU', $content, $match);
        preg_match_all('|<tr>[\n\t\r\s ]*<td[^>]*>([^>]*)</td[^>]*>[\n\t\r\s ]*<td[^>]*>([^>]*)</td>[\n\t\r\s ]*<td[^>]*>([^>]*)</td>[\n\t\r\s ]*</tr>|siU', $match[1], $matches);
//echo $content;
        foreach($matches[1] as $mkey=>$mval)
        {
            $engname = trim(str_replace("&nbsp;", "", $mval));
            //$rusname = trim(str_replace("&nbsp;", "", $matches[2][$mkey]));
            $telcode = trim(str_replace("-", "", $matches[3][$mkey]));
            //deb::dump($engname);


            if($updrow = Countries::model()->findByAttributes(array('name_en'=>$engname)))
            {
                //deb::dump($updrow);
                $updrow->phone_kod = $telcode;
                $updrow->save();
            }

        }
        */
        ///////////////////////// Конец импорт стран sypexgeo ///////////////////////////



        ///////////////////////////////// Импорт регионов sypexgeo /////////////////////////////////
        /*
        // Сохрание старых кодов
        $regions = Regions::model()->findAll();
        foreach ($regions as $ckey=>$cval)
        {
            $cval->old_reg_id = $cval->reg_id;
            $cval->old_c_id = $cval->c_id;
            $cval->save();
        }
        */

        /*
        // Простановка актуальных кодов стран
        $regions = Regions::model()->findAll();
        foreach ($regions as $ckey=>$cval)
        {
            $country = Countries::model()->findByAttributes(array('old_c_id'=>$cval->old_c_id));
            $cval->c_id = $country->c_id;
            $cval->country = $country->iso;
            $cval->save();
        }
        */


        /*
        // Перенос данных
        $supporter = new Supporter();
        $regions = SxgeoRegions::model()->findAll();
        foreach ($regions as $ckey=>$cval)
        {
            if($tbl = Regions::model()->findByAttributes(array(
                'country'=>$cval->country,
                'name'=>$cval->name_ru
            )))
            {
                $tbl->reg_id = $cval->id;
                $tbl->name_en = $cval->name_en;
                $tbl->transname = $supporter->TranslitForUrl($cval->name_en);
                $tbl->iso = $cval->iso;
                $tbl->country = $cval->country;
                $tbl->timezone = $cval->timezone;
                $tbl->okato = $cval->okato;
                $tbl->save();
            }
            else
            {
                $tbl = new Regions();

                $tbl->reg_id = $cval->id;

                $cntr = Countries::model()->findByAttributes(array('iso'=>$cval->country));
                $tbl->c_id = $cntr->c_id;

                $tbl->name = $cval->name_ru;
                $tbl->name_en = $cval->name_en;
                $tbl->transname = $supporter->TranslitForUrl($cval->name_en);
                if($transcheck = Regions::model()->findByAttributes(array('transname'=>$tbl->transname)))
                {
                    $tbl->transname .= '_'.$cval->id;
                }

                $tbl->iso = $cval->iso;
                $tbl->country = $cval->country;
                $tbl->timezone = $cval->timezone;
                $tbl->okato = $cval->okato;
                $tbl->save();

                if($tbl->hasErrors())
                {
                    deb::dump($tbl->getErrors());
                }


            }

        }
        */

        ///////////////////////////////// КОНЕЦ Импорт регионов sypexgeo /////////////////////////////////


        ///////////////////////////////// Импорт городов sypexgeo /////////////////////////////////

        /*
        // Сохрание старых кодов
        $towns = Towns::model()->findAll();
        foreach ($towns as $ckey=>$cval)
        {
            $cval->old_t_id = $cval->t_id;
            $cval->old_reg_id = $cval->reg_id;
            $cval->old_c_id = $cval->c_id;
            $cval->save();
        }
        */


        /*
        // Простановка актуальных кодов стран и регионов
        $towns = Towns::model()->findAll();
        foreach ($towns as $ckey=>$cval)
        {
            $country = Countries::model()->findByAttributes(array('old_c_id'=>$cval->old_c_id));
            $region = Regions::model()->findByAttributes(array('old_reg_id'=>$cval->old_reg_id));
            $cval->c_id = $country->c_id;
            $cval->reg_id = $region->reg_id;
            $cval->save();
        }
        */



        /*
        // Перенос данных
        $supporter = new Supporter();
        $towns = SxgeoCities::model()->findAll();
        foreach ($towns as $ckey=>$cval)
        {
            if($tbl = Towns::model()->findByAttributes(array(
                'reg_id'=>$cval->region_id,
                'name'=>$cval->name_ru
            )))
            {
                $tbl->t_id = $cval->id;
                $tbl->name_en = $cval->name_en;
                $tbl->transname = $supporter->TranslitForUrl($cval->name_en);
                $tbl->lat = $cval->lat;
                $tbl->lon = $cval->lon;
                $tbl->okato = $cval->okato;
                $tbl->save();
            }
            else
            {
                $tbl = new Towns();

                $tbl->t_id = $cval->id;
                $tbl->reg_id = $cval->region_id;

                $regrow = Regions::model()->findByPk($cval->region_id);
                $tbl->c_id = $regrow->c_id;

                $tbl->name = $cval->name_ru;
                $tbl->name_en = $cval->name_en;

                $tbl->transname = $supporter->TranslitForUrl($cval->name_en);
                if($transcheck = Towns::model()->findByAttributes(array('transname'=>$tbl->transname)))
                {
                    $tbl->transname .= '_'.$cval->id;
                }

                $tbl->inname = "in ".$cval->name_en;
                $tbl->lat = $cval->lat;
                $tbl->lon = $cval->lon;
                $tbl->okato = $cval->okato;
                $tbl->save();

                if($tbl->hasErrors())
                {
                    deb::dump($tbl->getErrors());
                }

            }

        }
        */



        ///////////////////////////////// КОНЕЦ Импорт городов sypexgeo /////////////////////////////////

    }


    public function actionCheckDoubles()
    {
        return false;   // Удалить когда понадобится

        $countries = Countries::getCountryListLight();
        $regions = Regions::getRegionListLight();

        $towns = Towns::model()->findAll(array(
            'select'=>'t_id, reg_id, c_id, name',
            'order'=>'t_id'
        ));

        foreach($towns as $tkey=>$tval)
        {
            $dub = Towns::model()->findAll(array(
                'select'=>'*',
                'condition'=>'name = :name AND t_id > :t_id AND c_id = :c_id
                                    AND reg_id = :reg_id AND double_tag = 0',
                'params'=>array(':name'=>$tval->name, ':t_id'=>$tval->t_id,
                                ':c_id'=>$tval->c_id, ':reg_id'=>$tval->reg_id
                )
            ));

            if(count($dub) > 0)
            {
            ?>
            <div>
                <?
                echo $tval->t_id.", ".$tval->name.", ".$countries[$tval->c_id].", ".$regions[$tval->reg_id].", ".$tval->reg_id."<br>";
                $j=0;
                foreach($dub as $dkey=>$dval)
                {
                    echo " --==-- ";
                    echo $dval->t_id.", ".$dval->name.", ".$countries[$dval->c_id].", ".$regions[$dval->reg_id].", ".$dval->reg_id."<br>";

                    $dval->double_tag = 1;
                    $dval->save();
                }
                ?>
                <br>
            </div>
            <?
            }
        }
    }


/*
    // Патч таблицы объявлений. Старые коды стран и регионов заменяем на актуальные для кода города
    public function actionPatchCountryRegions()
    {
        $nots = Notice::model()->findAll();

        foreach($nots as $nkey=>$nval)
        {
            $town = Towns::model()->findByPk($nval->t_id);
            $nval->reg_id = $town->reg_id;
            $nval->c_id = $town->c_id;
            $nval->save();
        }
    }
*/



    // Поиск дублей, где регион равен городу
    public function actionCheckRegTownDouble()
    {
        $countries = Countries::getCountryListLight();

        $regions = Regions::model()->findAll();
        foreach($regions as $rkey=>$rval)
        {
            if($town = Towns::model()->findByAttributes(array('reg_id'=>$rval->reg_id, 'name'=>$rval->name)))
            {
                $subtowns = Towns::model()->findAllByAttributes(array('reg_id'=>$rval->reg_id));
                //deb::dump($subtowns);
            ?>
                <div style="margin-bottom: 20px;">
                    <b><?= $town->name;?>, <?= $countries[$town->c_id];?></b>
                    <br>
                    <?
                    foreach($subtowns as $skey=>$sval)
                    {
                        echo $sval->name.", ".$countries[$sval->c_id]."<br>";
                    }
                    ?>
                </div>
            <?
            }

        }

    }



    // Поиск и корректировка городов со скобками, типа Красноармейск (Московская область)
    public function actionSkobkaTowns()
    {
        $towns = Towns::model()->findAll(array(
            'condition'=>'name LIKE "%(%" AND (c_id=185 OR c_id=222)'
        ));

        foreach($towns as $tkey=>$tval)
        {
            $clear = trim(preg_replace('|\(.+\)|siU', '', $tval->name));
            deb::dump($clear);
            $tval->name = $clear;
            $tval->save();
        ?>

            <?= $tval->t_id;?> <b><?= $tval->name;?></b> <?= $tval->reg_id;?> <b><?= ($tval->t_id == $tval->old_t_id);?></b><br>
        <?
            if($dubtowns = Towns::model()->findAllByAttributes(array(
                'name'=>$clear,
                'reg_id'=>$tval->reg_id
            )))
            {
                foreach($dubtowns as $dkey=>$dval)
                {
                    echo $dval->name."<br>";
                }
            }

            echo "<br><br>";
        }

    }



    // Счетчик просмотров
    public function actionAdvertCounter($n_id)
    {
        $advert = Notice::model()->find(array(
            'select'=>'*',
            'condition'=>'n_id = :n_id',
            'params'=>array(':n_id'=>$n_id)
        ));

        $start_date = mktime(0,0,0,
                intval(date("m", intval($advert->counter_date))),
                intval(date("d", intval($advert->counter_date))),
                intval(date("Y", intval($advert->counter_date))));
        if(time() - $start_date > 86400)
        {
            $advert->counter_daily = 1;
        }
        else
        {
            $advert->counter_daily++;
        }
        $advert->counter_total++;

        $advert->counter_date = time();
        $advert->save();

        header ("Content-type: image/png");
        $im = ImageCreate (1, 1)
            or die ("Ошибка при создании изображения");
        $couleur_fond = ImageColorAllocate ($im, 255, 255, 255);
        ImagePng ($im);


    }


    // Генерация номера телефона
    public function actionDisplayphone($n_id, $bkey)
    {
        $curr_time = time();
        $start_day = mktime(0,0,0,intval(date("m", $curr_time)), intval(date("d", $curr_time)), intval(date("Y", $curr_time)));
        $int_key = floor(($curr_time - $start_day) / 1800);

        if($bkey == md5($n_id.Yii::app()->params['security_key'].$int_key)
            || $bkey == md5($n_id.Yii::app()->params['security_key'].($int_key-1)))
        {
            if($advert = Notice::model()->find(array(
                'select'=>'client_phone, client_phone_c_id',
                'condition'=>'n_id = :n_id',
                'params'=>array(':n_id'=>$n_id)
            )))
            {
                $country = Countries::model()->findByPk($advert->client_phone_c_id);

                $width = 130;
                $return = '+'.$country->phone_kod.' '.$advert->client_phone;
            }
            else
            {
                $width = 100;
                $return = 'Ошибка!';
            }
        }
        else
        {
            $width = 210;
            $return = 'Ошибка! Перезагрузите страницу';
        }


        header ("Content-type: image/png");
        $height = 20;
        $im = ImageCreate ($width, $height);
        $colorBack = ImageColorAllocate ($im, 255, 255, 255);
        imageFilledRectangle($im, 0, 0, $width, $height, $colorBack);

        $colorText = ImageColorAllocate ($im, 0, 0, 0);
        imagettftext ($im, 10, 0, 3, 13, $colorText, "./fonts/arial.ttf", $return);

        imageColorTransparent($im, $colorBack);

        ImagePng ($im);


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