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
            array('u_id, r_id, parent_r_id, t_id, reg_id, c_id, date_add, date_lastedit, expire_period, date_expire, client_name, client_email, client_phone_c_id, title, notice_text, active_tag, verify_tag, checksum,  views_count, moder_counted_tag, cost, cost_valuta', 'required'),

            array('u_id, r_id, t_id, reg_id, c_id, expire_period, active_tag, verify_tag, deactive_moder_id, moder_tag, moder_id, views_count, deleted_tag, otkaz_id, moder_counted_tag', 'numerical', 'integerOnly'=>true),
			array('date_add, date_lastedit, date_expire, date_deactive, date_moder, date_delete, date_sort', 'length', 'max'=>14),
			array('client_name, client_email, client_phone, phone_search, reject_reason', 'length', 'max'=>256),
            array('title', 'length', 'max'=>80),
			array('notice_type_id, from_ip', 'length', 'max'=>16),
			array('checksum', 'length', 'max'=>32),

            //array('client_phone', 'validatephone'),

			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('n_id, u_id, r_id, t_id, reg_id, c_id, date_add, date_lastedit, date_expire, client_name, client_email, client_phone, phone_search, notice_type_id, title, notice_text, active_tag, verify_tag, date_deactive, deactive_moder_id, checksum, moder_tag, date_moder, moder_id, views_count, deleted_tag, date_delete, reject_reason, otkaz_id, date_sort, from_ip, moder_counted_tag', 'safe', 'on'=>'search'),
		);
	}

    // Проверка корректности ввода телефона
    public function validatephone()
    {
        //$this->client_phone
        if(Yii::app()->controller->action->id == 'addnew')
        {
            if(Yii::app()->session['usercheckphone_tag'] == 1
                && $this->client_phone == Yii::app()->session['usercheckphone'])
            {

            }
            else
            {
                $this->addError('client_phone', 'Необходимо подтвердить номер телефона!');
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
                if($tval < $start_date)
                {
                    unset($temp[$tkey]);
                }
            }

            $cookie->value = serialize($temp);
            Yii::app()->request->cookies['last_visit_adverts'] = $cookie;

        }

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
