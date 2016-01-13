<?php

/**
 * This is the model class for table "{{notice}}".
 *
 * The followings are the available columns in table '{{notice}}':
 * @property integer $n_id
 * @property integer $u_id
 * @property integer $r_id
 * @property integer $parent_r_id
 * @property integer $t_id
 * @property integer $reg_id
 * @property integer $c_id
 * @property string $date_add
 * @property string $date_lastedit
 * @property string $date_expire
 * @property string $client_name
 * @property string $client_email
 * @property string $client_phone
 * @property string $phone_search
 * @property string $notice_type_id
 * @property string $title
 * @property string $notice_text
 * @property integer $active_tag
 * @property integer $verify_tag
 * @property string $date_deactive
 * @property integer $deactive_moder_id
 * @property string $checksum
 * @property integer $moder_tag
 * @property string $date_moder
 * @property integer $moder_id
 * @property string $daynumber_id
 * @property integer $views_count
 * @property integer $deleted_tag
 * @property string $date_delete
 * @property string $reject_reason
 * @property integer $otkaz_id
 * @property string $date_sort
 * @property string $from_ip
 * @property integer $moder_counted_tag
 */
class Notice extends CActiveRecord
{
    const BASE_KOEFF_WATER_SCALE = 0.43;    // Базовый коэффициент масштабирования водяного знака для 600px по ширине

    const HUGE_WIDTH = 1000;                // Максимальный размер горизонтальной большой фотки по ширине
    const HUGE_HEIGHT = 800;                // Максимальный размер вертикальной большой фотки по высоте

    const BIG_PREVIEW_WIDTH = 600;          // Максимальный размер горизонтальной большой превьшки по ширине
    const BIG_PREVIEW_HEIGHT = 480;         // Максимальный размер вертикальной большой превьшки по высоте

    const MEDIUM_PREVIEW_WIDTH = 140;       // Максимальный размер горизонтальной средней превьшки по ширине
    const MEDIUM_PREVIEW_HEIGHT = 105;      // Максимальный размер вертикальной средней превьшки по высоте

    const PREVIEW_WIDTH = 60;              // Максимальный размер горизонтальной превьюшки по ширине
    const PREVIEW_HEIGHT = 40;             // Максимальный размер вертикальной превьюшки по высоте


    public static $expire_period = [
        '30'=>'дней',
        '21'=>'день',
        '14'=>'дней',
        '7'=>'дней',
        '3'=>'дня',
        '1'=>'день',
    ];

    public static $abuse_items = array(
        'tovar_prodan'=>array('class'=>'abuse_quick', 'name'=>'Товар продан'),
        'nevernaya_cena'=>array('class'=>'abuse_quick', 'name'=>'Неверная цена'),
        'ne_dozvonitsya'=>array('class'=>'abuse_quick', 'name'=>'Не дозвониться'),
        'contacts_and_links'=>array('class'=>'abuse_quick', 'name'=>'Контакты и ссылки в описании'),
        'money_moshennik'=>array('class'=>'abuse_quick', 'name'=>'Мошенничество с деньгами'),
        'other_abuse'=>array('class'=>'abuse_other', 'name'=>'Другая причина'),
    );

    // Коды рубрик, в которых объява не имеет заголовка
    public static $maybe_empty_title = array(
        12,     // Транспорт/Автомобили
    );

    // Коды рубрик, в которых объява может не иметь текста
    public static $maybe_empty_notice_text = array(
        12,     // Транспорт/Автомобили
    );


    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{notice}}';
	}


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('u_id, r_id, parent_r_id, t_id, reg_id, c_id, date_add, date_lastedit, expire_period, date_expire, client_name, client_email, client_phone_c_id, active_tag, verify_tag, checksum,  views_count, moder_counted_tag', 'required'), // , cost, cost_valuta

            array('cost', 'numerical'),
            array('cost_valuta', 'length', 'max'=>3),
            array('u_id, r_id, t_id, reg_id, c_id, expire_period, active_tag, verify_tag, deactive_moder_id, moder_tag, moder_id, views_count, deleted_tag, otkaz_id, moder_counted_tag', 'numerical', 'integerOnly'=>true),
			array('date_add, date_lastedit, date_expire, date_deactive, date_moder, date_delete, date_sort', 'length', 'max'=>14),
			array('client_name, client_email, client_phone, phone_search, reject_reason', 'length', 'max'=>256),
            //array('title', 'length', 'max'=>256), //validatetitle()
			array('notice_type_id, from_ip', 'length', 'max'=>16),
			array('checksum', 'length', 'max'=>32),

            array('client_phone', 'validatephone'),
            array('title', 'validatetitle'),
            array('notice_text', 'validatenotice_text'),

			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('n_id, u_id, r_id, t_id, reg_id, c_id, date_add, date_lastedit, date_expire, client_name, client_email, client_phone, phone_search, notice_type_id, title, notice_text, active_tag, verify_tag, date_deactive, deactive_moder_id, checksum, moder_tag, date_moder, moder_id, views_count, deleted_tag, date_delete, reject_reason, otkaz_id, date_sort, from_ip, moder_counted_tag', 'safe', 'on'=>'search'),
		);
	}

    // Проверка корректности ввода заголовка
    public function validatetitle()
    {
        if(in_array($this->r_id, self::$maybe_empty_title))
        {

        }
        else
        {
            if(strlen(trim($this->title)) <= 0)
            {
                $this->addError('title', 'Необходимо заполнить название объявления!');
            }
        }

        if(strlen(trim($this->title)) > 256)
        {
            $this->addError('title', 'Максимальная длина название объявления 256 символов!');
        }

    }

    // Проверка корректности ввода описания объявления
    public function validatenotice_text()
    {
        if(in_array($this->r_id, self::$maybe_empty_notice_text))
        {

        }
        else
        {
            if(strlen(trim($this->notice_text)) <= 0)
            {
                $this->addError('notice_text', 'Необходимо заполнить текст объявления!');
            }
        }

    }

    // Проверка корректности ввода телефона
    public function validatephone()
    {
        //$this->client_phone
        if(Yii::app()->controller->action->id == 'addnew')
        {
            if(intval($this->client_phone_c_id) == Yii::app()->params['russia_id'])
            {
                if(Yii::app()->session['usercheckphone_tag'] == 1
                    && $this->client_phone == Yii::app()->session['usercheckphone'])
                {

                }
                else
                {
                    if(Yii::app()->session['usercheckphone_tag'] == 0)
                    {
                        if($user_phone = UserPhones::model()->findByAttributes(array(
                            'u_id'=>Yii::app()->user->id,
                            'c_id'=>$this->client_phone_c_id,
                            'phone'=>$this->client_phone,
                            'verify_tag'=>1
                        )))
                        {

                        }
                        else
                        {
                            $this->addError('client_phone', 'Необходимо подтвердить номер телефона!');
                        }
                    }
                    else
                    {
                        $this->addError('client_phone', 'Необходимо подтвердить номер телефона!');
                    }

                }
            }
            else
            {
                if(strlen($this->client_phone) < 7)
                {
                    $this->addError('client_phone', 'Необходимо указать номер телефона');
                }
            }
        }
    }


    public static function getImageArray($upload_data)
    {
        $temp = array();
        $uploadfiles = trim($upload_data);
        if(strlen($uploadfiles)>10)
        {
            $temp = explode(";", $uploadfiles);
            unset($temp[count($temp)-1]);
        }

        return $temp;
    }

    // Проверка является ли пользователь владельцем объявы
    // u_id - код юзера, n_id - код объявы
    // true - является, false - Не является
    public static function checkAdvertOwner($u_id, $n_id)
    {
        if($advert = Notice::model()->findByAttributes(array('u_id'=>$u_id, 'n_id'=>$n_id)))
        {
            return $advert;
        }
        else
        {
            return false;
        }

    }


    // Вычисление цены в указанной валюте и генерация отображения <цена> <символ валюты>
    // $advert_valuta - код валюты объявления
    // $advert_cost - цена в валюте объявления
    // $to_view_valuta - код валюты в которой надо отображать
    public static function costCalcAndView($advert_valuta, $advert_cost, $to_view_valuta)
    {
        if($to_view_valuta == 'RUB')
        {
            $to_view_cost = round($advert_cost * Yii::app()->params['options']['kurs_'.strtolower($advert_valuta)]);
        }
        else
        {
            if($advert_valuta == 'RUB')
            {
                $to_view_cost = round($advert_cost / Yii::app()->params['options']['kurs_'.strtolower($to_view_valuta)], 2);
            }
            else
            {
                $cross_kurs = Yii::app()->params['options']['kurs_'.strtolower($advert_valuta)] / Yii::app()->params['options']['kurs_'.strtolower($to_view_valuta)];
                $to_view_cost = round($advert_cost * $cross_kurs , 2);
            }
        }

        return $to_view_cost;
    }



    // Генерация имени файла с суффиксом
    public static function getPhotoName($photoname, $suffix)
    {
        return str_replace(".", $suffix.".", $photoname);
    }


    // Генерация слова "Сегодня" или "вчера" в зависимости от даты
    public static function TodayStrGenerate($date, $upper_tag)
    {
        $start_time = mktime(0,0,0,intval(date("m", $date)), intval(date("d", $date)), intval(date("Y", $date)));
        if((time() - $start_time) < 86400)
        {
            $day_string = "сегодня в ".date("H:i", $date);
        }
        else
        if(((time() - $start_time) > 86400) && (time() - $start_time < 86400*2))
        {
            $day_string = "вчера в ".date("H:i", $date);
        }
        else
        {
            $day_string = intval(date("d", $date))." ".Yii::app()->params['month_padezh'][intval(date("m", $date))]." в ".date("H:i", $date);
        }

        if($upper_tag)
        {
            $day_string[0] = strtoupper($day_string[0]);
        }

        return $day_string;
    }


    // Получение количества объяв в избранном
    public static function GetFavoritCount()
    {
        $count = 0;
        if(isset(Yii::app()->request->cookies['favorit']))
        {
            $cookie = Yii::app()->request->cookies['favorit'];
            $temp = explode(";", $cookie->value);
            if($temp[0] != '')
            {
                $count = count($temp);
            }
        }

        return $count;
    }

    // Проверка наличия объявы в избранном
    public static function CheckAdvertInFavorit($n_id)
    {
        $tag = 0;
        if(isset(Yii::app()->request->cookies['favorit']))
        {
            $cookie = Yii::app()->request->cookies['favorit'];
            $temp = explode(";", $cookie->value);
            if(in_array($n_id, $temp))
            {
                $tag = 1;
            }
        }

        return $tag;
    }


    // Добавление объявы в избранное
    public static function AddToFavorit($n_id)
    {
        if(isset(Yii::app()->request->cookies['favorit']))
        {
            $cookie = Yii::app()->request->cookies['favorit'];
            $cookie->expire = time() + 86400*365;

            $temp = $cookie->value.";".$n_id;
            $favorit_array = explode(";", $temp);
            $favorit_array = array_flip(array_flip($favorit_array));
            $cookie->value = implode(";", $favorit_array);
            Yii::app()->request->cookies['favorit'] = $cookie;
            //echo Yii::app()->request->cookies['favorit'];
        }
        else
        {
            $cookie = new CHttpCookie('favorit', $n_id);
            $cookie->expire = time() + 86400*365;
            Yii::app()->request->cookies['favorit'] = $cookie;
            $favorit_array[] = $n_id;
        }

        return count($favorit_array);

    }

    // Удаление из избранного
    public static function DeleteFromFavorit($n_id)
    {
        $count = 0;
        if(isset(Yii::app()->request->cookies['favorit']))
        {
            $cookie = Yii::app()->request->cookies['favorit'];
            $temp = explode(";", $cookie->value);
            if($temp[0] != '')
            {
                foreach($temp as $tkey=>$tval)
                {
                    if($tval == $n_id)
                    {
                        unset($temp[$tkey]);
                    }
                }
                $cookie->value = implode(";", $temp);
                Yii::app()->request->cookies['favorit'] = $cookie;
            }
        }

        return count($temp);
    }

    // Удаление всех из избранного
    public static function DeleteAllFromFavorit()
    {
        $count = 0;
        if(isset(Yii::app()->request->cookies['favorit']))
        {
            unset(Yii::app()->request->cookies['favorit']);
        }

        return 0;
    }

    // Удаление всех из недавнего
    public static function DeleteAllLastVisit()
    {
        $count = 0;
        if(isset(Yii::app()->request->cookies['last_visit_adverts']))
        {
            unset(Yii::app()->request->cookies['last_visit_adverts']);
        }

        return 0;
    }

    // Добавление объявы в последние просмотренные
    public static function AddToLastVisit($n_id)
    {
        $temp = array();
        if(isset(Yii::app()->request->cookies['last_visit_adverts']))
        {
            $cookie = Yii::app()->request->cookies['last_visit_adverts'];
        }
        else
        {
            $cookie = new CHttpCookie('last_visit_adverts', $n_id);
        }
        $cookie->expire = time() + 86400*365;

        $temp = unserialize($cookie->value);
        $temp[$n_id]['date_view'] = time();
        $cookie->value = serialize($temp);

        Yii::app()->request->cookies['last_visit_adverts'] = $cookie;

        return count($temp);

    }


    // Количество последних просмотренных
    public static function GetLastvisitCount()
    {
        self::DeleteOldLastvisit();

        $count = 0;
        $temp = array();
        if(isset(Yii::app()->request->cookies['last_visit_adverts']))
        {
            $cookie = Yii::app()->request->cookies['last_visit_adverts'];
            $temp = unserialize($cookie->value);
        }


        return count($temp);

    }

    // Удаление старых просмотренных
    public static function DeleteOldLastvisit()
    {
        $temp = array();
        if(isset(Yii::app()->request->cookies['last_visit_adverts']))
        {
            $cookie = Yii::app()->request->cookies['last_visit_adverts'];
            $temp = unserialize($cookie->value);

            $start_date = time() - 86400;
            foreach ($temp as $tkey=>$tval)
            {

                if($tval['date_view'] < $start_date)
                {
                    unset($temp[$tkey]);
                }
            }

            $cookie->value = serialize($temp);
            Yii::app()->request->cookies['last_visit_adverts'] = $cookie;

        }

    }


    // Формирование отображения списка объявлений
    // $search_adverts - массив объявлений
    // $shablons_display - шаблоны  отображения объявы в списке в зависимости от рубрики
    // $rubriks_all_array - массив всех рубрик
    public static function DisplayAdvertsList($search_adverts, $shablons_display, $rubriks_all_array)
    {
        $props_array = array();
        foreach($search_adverts as $key=>$val)
        {

            $res = self::MakePropsDisplayData($val['props_xml']);
            $props_display = $res['props_display'];
            $photos = $res['photos'];

            $val['cost'] = Notice::costCalcAndView($val['cost_valuta'], $val['cost'],
                    Yii::app()->request->cookies['user_valuta_view']->value).
                " ".Options::$valutes[Yii::app()->request->cookies['user_valuta_view']->value]['symbol2'];

            $short_advert_display = $shablons_display[$val['r_id']];
            foreach($props_display as $pkey=>$pval)
            {
                $short_advert_display = str_replace('['.$pkey.']', $pval, $short_advert_display);
            }

            preg_match_all('|\{([a-zA-Z0-9_-]+)\}|siU', $short_advert_display, $matches);
            //deb::dump($matches[1]);
            foreach($matches[1] as $match)
            {
                $short_advert_display = str_replace('{'.$match.'}', $val[$match], $short_advert_display);
            }

            // Титул
            ob_start();
            $favprefix = "";
            $favorit_title = '';
            if(Notice::CheckAdvertInFavorit($val['n_id']))
            {
                //$favorit_title = 'В избранном';
                $favorit_title = '';
                $favprefix = "_yellow";
            }
            ?>
            <a class="span_lnk favoritstar" advert_id="<?= $val['n_id'];?>" style="background-image: url('/images/favorit<?= $favprefix;?>.png'); background-position: left center; background-repeat: no-repeat; padding-left: 17px; margin-left: 0px; text-decoration: none;">
                <span class="favorit_button" advert_id="<?= $val['n_id'];?>" style="border-bottom: #008CC3 dotted; border-width: 1px;"></span>
            </a>
            <?
            $favoritstar_block = ob_get_contents();
            ob_end_clean();
            $short_advert_display = str_replace('[[favoritstar_block]]', $favoritstar_block, $short_advert_display);

            //deb::dump($val);
            //$short_advert_display = str_replace('[[advert_page_url]]');
            $short_advert_display = str_replace('[[mestopolozhenie]]', $val['town_name'], $short_advert_display);
            $date_add_str = date('d.m.Y H:i', $val['date_add']);
            $start_time = mktime(0,0,0,intval(date("m", $val['date_add'])), intval(date("d", $val['date_add'])), intval(date("Y", $val['date_add'])));
            if((time() - $start_time) < 86400)
            {
                $date_add_str = date('Сегодня H:i', $val['date_add']);
            }
            if(((time() - $start_time) > 86400) && (time() - $start_time < 86400*2))
            {
                $date_add_str = date('Вчера H:i', $val['date_add']);
            }
            $short_advert_display = str_replace('[[date_add]]', $date_add_str, $short_advert_display);

            // Генерация ссылки на объяву
            $transliter = new Supporter();

            preg_match('|<a class="baralink"[^>]+>(.+)</a>|siU', $short_advert_display, $match);
            $title_ankor = $match[1];

            $advert_page_url = $val['town_transname']."/".$rubriks_all_array[$val['r_id']]->transname."/".$transliter->TranslitForUrl($title_ankor/*$val['title']*/)."_".$val['daynumber_id'];
            //deb::dump($advert_page_url);
            $short_advert_display = str_replace('[[advert_page_url]]', Yii::app()->createUrl($advert_page_url), $short_advert_display);

            $props_array[$key]['props_display'] = $short_advert_display;
            $props_array[$key]['photos'] = $photos;


        }

        return $props_array;
    }

    // Формирование массива 'ключ свойства'=>'значение свойства' для объявления по XML данным
    public static function MakePropsDisplayData($props_xml)
    {
        $props_display = array();
        $photos = array();
        $xml = new SimpleXMLElement($props_xml);
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
                            }
                        }
                    }
                    else
                    {
                        $temp[] = (string)$ival->value;
                    }
                }

                $props_display[$b2key] = implode(", ", $temp);

            }
        }

        $data['props_display'] = $props_display;
        $data['photos'] = $photos;

        return $data;

    }




    // Подсчет кодичества объявлений пользователя
    public static function GetUserCountAllAdverts($u_id)
    {
        $count = Notice::model()->count(array(
            'select'=>'n_id',
            'condition'=>'u_id = '.$u_id
        ));

        return $count;
    }


    /*
        public static function getImageArray($uploadfiles, $uploadmainfile)
        {
            $uploadfiles_array = array();
            $uploadfiles = trim($uploadfiles);
            if(strlen($uploadfiles)>4)
            {
                $uploadfiles = substr($uploadfiles, 0, (strlen($uploadfiles)-1));
                $temp = explode(";", $uploadfiles);
                $uploadfiles_array[0] = $uploadmainfile;

                foreach ($temp as $tkey=>$tval)
                {
                    if($tval != $uploadmainfile)
                    {
                        $uploadfiles_array[] = $tval;
                    }
                }
            }

            return $uploadfiles_array;
        }
    */


    // Подсчет контрольной суммы объявы для поиска дублей и т.п.
    public static function GetChecksum($newmodel)
    {
        $control_string = $newmodel->client_name . $newmodel->client_email . $newmodel->client_phone . $newmodel->r_id . $newmodel->title . $newmodel->notice_text;
        $checksum = md5($control_string);

        return $checksum;
    }


    // Получение названия директории для файла-картинки.
    // Первые три символа имени файла
    public static function getPhotoDir($filename)
    {
        return substr($filename, 0, 3);
    }

    // Получение названия директории для файла-картинки. Если директории нет, она создается
    // $photodir - базовая директория для фотографий
    public static function getPhotoDirMake($photodir, $filename)
    {
        $dirname = self::getPhotoDir($filename);

        $full_dirname = $_SERVER['DOCUMENT_ROOT']."/".$photodir."/".$dirname;
        if(!is_dir($full_dirname))
        {
            mkdir($full_dirname);
        }

        return $dirname;
    }


    // Формирование строки из кодов свойств для использования в генерации ключевиков
    public static function MakeKeywordSignature($props_relate)
    {
        $rp_id_array = array();
        $ps_id_array = array();
        $rp_selector_array = array();
        foreach($props_relate as $pkey=>$pval)
        {
            switch($pval->vibor_type)
            {
                case "autoload":
                case "autoload_with_listitem":
                case "selector":
                case "listitem":

                    $rp_id_array[$pval->rp_id] = $pval->rp_id;
                    $ps_id_array[$pval->notice_props[0]->ps_id] = $pval->notice_props[0]->ps_id;
                    $rp_selector_array[$pval->selector] = $pval->notice_props[0]->ps_id;

                break;

            }
        }

        $ret['rp_ids'] = implode(".", $rp_id_array);
        $ret['ps_ids'] = implode(".", $ps_id_array);
        $ret['rp_selector'] = $rp_selector_array;

        return $ret;

    }



    // Генерация ключевиков для объявления
    public static function KeywordsGenerate($n_id)
    {
        $advert = Notice::model()->findByPk($n_id);

        $props_relate = RubriksProps::model()->with('notice_props')->findAll(array(
            'select'=>'*',
            'condition'=>'r_id='.$advert->r_id . " AND n_id=".$advert->n_id,
            'order'=>'t.hierarhy_tag DESC, t.hierarhy_level ASC, t.display_sort, t.rp_id'
        ));
//deb::dump($props_relate);
        $retsign= Notice::MakeKeywordSignature($props_relate);
//deb::dump($retsign);
//die();
        $keyword_signature = $retsign['ps_ids'];
        $signature_array = explode('.', $keyword_signature);
        $prop_count = count($signature_array);


        $keywords_keys = array();
        $keywords_pos = array();
        //$positions_array = array_flip(SeoKeywords::$position);

        //$pkey = array_shift($positions_array);

        foreach(SeoKeywords::$position as $pkey=>$pval)
        {
            $i_start = count($signature_array)-1;
            $signtemp = $signature_array;
            $break_tag = 0;


            for($i=$i_start; $i>=-1; $i--)
            {
                $signstr = implode(".", $signtemp);

                if(count($signtemp) > 0)
                {
                    $seo_keywords = SeoKeywords::model()->findAll(array(
                        'select'=>'k_id, keyword, r_id, position, signature, signature_ps_id, prop_count, count',
                        'condition'=>'r_id = '.$advert->r_id . " AND position = '".$pkey."'
                           AND prop_count <= $prop_count AND signature_ps_id LIKE '".$signstr."%' ",
                        'order'=>'prop_count DESC, count ASC, k_id DESC'
                    ));
                    //deb::dump($seo_keywords);

                }
                else
                {
                    $seo_keywords = SeoKeywords::model()->findAll(array(
                        'select'=>'k_id, keyword, r_id, position, signature, signature_ps_id, prop_count, count',
                        'condition'=>'r_id = '.$advert->r_id . " AND position = '".$pkey."' ",
                        'order'=>'prop_count ASC, count ASC, k_id DESC'
                    ));
                    //deb::dump($seo_keywords);
                }

                if($seo_keywords)
                {
                    foreach($seo_keywords as $skey=>$seo_keyword)
                    {
                        if(!isset($keywords_keys[$seo_keyword->keyword]))
                        {
                            $keywords_keys[$seo_keyword->keyword] = $seo_keyword->keyword;
                            $keywords_pos[$pkey] = $seo_keyword;

                            /*
                            if(count($positions_array) > 0)
                            {
                                $pkey = array_shift($positions_array);
                            }
                            else
                            {
                                $break_tag = 1;
                                break;
                            }
                            */
                            $break_tag = 1;
                            break;

                        }
                    }
                }


                if($break_tag)
                {
                    break;
                }


                unset($signtemp[$i]);
            }

        }

        $keywords_maked = self::KeywordByShablonGenerate($advert, $retsign['rp_selector'], $keywords_pos);

        foreach($keywords_maked as $k2key=>$k2val)
        {
            $advert->{'keyword_'.$k2key} = $k2val;
        }

        if($advert->save())
        {
            foreach($keywords_pos as $kpkey=>$kpval)
            {
                if(!$skn = SeoKeywordsNotice::model()->findByAttributes(array(
                    'k_id'=>$kpval->k_id,
                    'n_id'=>$advert->n_id
                )))
                {
                    $seokeynot = new SeoKeywordsNotice();
                    $seokeynot->k_id = $kpval->k_id;
                    $seokeynot->n_id = $advert->n_id;
                    $seokeynot->save();

                    // Подсчет кол-ва
                    $kpval->count = SeoKeywordsNotice::model()->count('k_id = '.$kpval->k_id);
                    $kpval->save();

                    //deb::dump($kpval);
                }
            }
        }



        //deb::dump($advert);

        //return $keywords_pos;


    }


    // Формирование ключевой фразы по шаблону
    public static function KeywordByShablonGenerate($advert, $pr_selector_array, $keywords_pos)
    {
        $keywords_maked = array();
        $replace_fields = array();
        $replace_props = array();

        $randwords = SeoRandomword::model()->findAll();
        $randwords_array = array();
        foreach($randwords as $rkey=>$rval)
        {
            $randwords_array[$rval->key] = explode("/",$rval->words);
        }
//deb::dump($randwords_array);
//die();
        foreach($keywords_pos as $kkey=>$keyword)
        {
            //deb::dump($keyword->keyword);
            if(preg_match_all('|(<[^>]+>)|siU', $keyword->keyword, $matches))
            {
                foreach($matches[1] as $mkey=>$mval)
                {
                    $replace_fields[$mval] = $mval;
                }
            }

            if(preg_match_all('|\[([^\]]+)\]|siU', $keyword->keyword, $matches))
            {
                foreach($matches[1] as $mkey=>$mval)
                {
                    $replace_props[$mval] = $mval;
                }
            }

            if(preg_match_all('|\(([^\]]+)\)|siU', $keyword->keyword, $matches))
            {
                foreach($matches[1] as $mkey=>$mval)
                {
                    if(isset($randwords_array[$mval]))
                    {
                        $word = $randwords_array[$mval][rand(0, (count($randwords_array[$mval])-1))];
                        $keyword->keyword = str_replace("(".$mval.")", $word, $keyword->keyword);
                    }

                }
            }

            $keywords_maked[$kkey] = $keyword->keyword;

            if(count($replace_props) > 0)
            {
                $props_array = array();
                if(count($pr_selector_array) > 0)
                {
                    if($props = PropsSprav::model()->findAll(array(
                        'select'=>'*',
                        'condition'=>'ps_id IN ('.implode(", ", $pr_selector_array).')'
                    )))
                    {
                        foreach($props as $pkey=>$pval)
                        {
                            $props_array[$pval->ps_id] = $pval;
                        }
                    }
                }

                foreach($replace_props as $rkey=>$rval)
                {
                    $keywords_maked[$kkey] = str_replace("[".$rval."]", $props_array[$pr_selector_array[$rval]]->value, $keywords_maked[$kkey]);
                }

            }

            if(count($replace_fields) > 0)
            {
                foreach($replace_fields as $rkey=>$rval)
                {
                    switch($rval)
                    {
                        case "<город>":
                            $town = Towns::model()->findByPk($advert->t_id);
                            $keywords_maked[$kkey] = str_replace($rval, $town->name, $keywords_maked[$kkey]);
                        break;

                        case "<регион>":
                            $region = Regions::model()->findByPk($advert->reg_id);
                            $keywords_maked[$kkey] = str_replace($rval, $region->name, $keywords_maked[$kkey]);
                        break;

                    }
                }
            }

//deb::dump($keywords_maked);


        }

//        deb::dump($replace_fields);
//        deb::dump($replace_props);

        return $keywords_maked;

        //die();
    }


    // Выбираем какой индекс будем использовать в filter запросах
    // используем $expire_sql, $mesto_sql, $rubrik_sql
    public static function GetUseIndexSql($expire_sql, $mesto_sql, $rubrik_sql, $mesto_use_index_prefix)
    {
        /*
        deb::dump($expire_sql);
        echo "<br>";
        deb::dump($mesto_sql);
        echo "<br>";
        deb::dump($rubrik_sql);
        /**/

        $expire_use_tag = 0;
        $mesto_use_tag = 0;
        $rubrik_use_tag = 0;
        if(trim($expire_sql) != '')
        {
            $expire_use_tag = 1;
        }
        if(trim($mesto_sql) != '1')
        {
            $mesto_use_tag = 1;
        }
        if(trim($rubrik_sql) != '1')
        {
            $rubrik_use_tag = 1;
        }

        /*
        deb::dump($expire_use_tag);
        echo "<br>";
        deb::dump($mesto_use_tag);
        echo "<br>";
        deb::dump($rubrik_use_tag);
        /**/

        $use_index_sql = " ";
        if(!$rubrik_use_tag)
        {
            if($mesto_use_tag && $expire_use_tag)
            {
                $use_index_sql = " use index (date_expire_and_".$mesto_use_index_prefix.") ";
            }

            if($mesto_use_tag && !$expire_use_tag)
            {
                $use_index_sql = " use index (avd_index, ".$mesto_use_index_prefix.") ";
            }

            if(!$mesto_use_tag && $expire_use_tag)
            {
                $use_index_sql = " use index (avd_index, date_expire) ";
            }
        }
        else
        {
            if($mesto_use_tag && $expire_use_tag)
            {
                $use_index_sql = " use index (r_id_and_date_expire_and_".$mesto_use_index_prefix.") ";
            }

            if($mesto_use_tag && !$expire_use_tag)
            {
                $use_index_sql = " use index (r_id_and_".$mesto_use_index_prefix.") ";
            }

        }

        return $use_index_sql;

    }


	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'user'=>array(self::BELONGS_TO, 'Users', array('id'=>'u_id')),
            'country'=>array(self::BELONGS_TO, 'Countries', 'c_id'),
            'town'=>array(self::BELONGS_TO, 'Towns', array('t_id'=>'t_id')),
            'rubriks'=>array(self::BELONGS_TO, 'Rubriks', 'r_id'),
            'rubriks_props'=>array(self::HAS_MANY, 'RubriksProps', array('r_id'=>'r_id')),
            'notice_props'=>array(self::HAS_MANY, 'NoticeProps', array('n_id'=>'n_id'))
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'n_id' => 'N',
			'u_id' => 'Пользователь',
			'r_id' => 'Рубрика',
			't_id' => 'Город',
			'reg_id' => 'Регион',
			'c_id' => 'Страна',
			'date_add' => 'Date Add',
			'date_lastedit' => 'Date Lastedit',
            'expire_period' => 'Период размещения',
			'date_expire' => 'Date Expire',
			'client_name' => 'Ваше имя',
			'client_email' => 'Электронная почта',
			'client_phone' => 'Телефон',
			'phone_search' => 'Phone Search',
			'notice_type_id' => 'Тип объявления',
			'title' => 'Название объявления',
			'notice_text' => 'Текст объявления',
			'active_tag' => 'Active Tag',
			'verify_tag' => 'Verify Tag',
			'date_deactive' => 'Date Deactive',
			'deactive_moder_id' => 'Deactive Moder',
			'checksum' => 'Checksum',
			'moder_tag' => 'Moder Tag',
			'date_moder' => 'Date Moder',
			'moder_id' => 'Moder',
			'daynumber_id' => 'Daynumber',
			'views_count' => 'Views Count',
			'deleted_tag' => 'Deleted Tag',
			'date_delete' => 'Date Delete',
			'reject_reason' => 'Reject Reason',
			'otkaz_id' => 'Otkaz',
			'date_sort' => 'Date Sort',
			'from_ip' => 'From Ip',
			'moder_counted_tag' => 'Moder Counted Tag',
            'cost'=>'Цена',
            'cost_valuta'=>'валюта'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('n_id',$this->n_id);
		$criteria->compare('u_id',$this->u_id);
		$criteria->compare('r_id',$this->r_id);
		$criteria->compare('t_id',$this->t_id);
		$criteria->compare('reg_id',$this->reg_id);
		$criteria->compare('c_id',$this->c_id);
		$criteria->compare('date_add',$this->date_add,true);
		$criteria->compare('date_lastedit',$this->date_lastedit,true);
		$criteria->compare('date_expire',$this->date_expire,true);
		$criteria->compare('client_name',$this->client_name,true);
		$criteria->compare('client_email',$this->client_email,true);
		$criteria->compare('client_phone',$this->client_phone,true);
		$criteria->compare('phone_search',$this->phone_search,true);
		$criteria->compare('notice_type_id',$this->notice_type_id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('notice_text',$this->notice_text,true);
		$criteria->compare('active_tag',$this->active_tag);
		$criteria->compare('verify_tag',$this->verify_tag);
		$criteria->compare('date_deactive',$this->date_deactive,true);
		$criteria->compare('deactive_moder_id',$this->deactive_moder_id);
		$criteria->compare('checksum',$this->checksum,true);
		$criteria->compare('moder_tag',$this->moder_tag);
		$criteria->compare('date_moder',$this->date_moder,true);
		$criteria->compare('moder_id',$this->moder_id);
		$criteria->compare('daynumber_id',$this->daynumber_id,true);
		$criteria->compare('views_count',$this->views_count);
		$criteria->compare('deleted_tag',$this->deleted_tag);
		$criteria->compare('date_delete',$this->date_delete,true);
		$criteria->compare('reject_reason',$this->reject_reason,true);
		$criteria->compare('otkaz_id',$this->otkaz_id);
		$criteria->compare('date_sort',$this->date_sort,true);
		$criteria->compare('from_ip',$this->from_ip,true);
		$criteria->compare('moder_counted_tag',$this->moder_counted_tag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Notice the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}



}
