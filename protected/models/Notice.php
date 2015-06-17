<?php

/**
 * This is the model class for table "{{notice}}".
 *
 * The followings are the available columns in table '{{notice}}':
 * @property integer $n_id
 * @property integer $u_id
 * @property integer $r_id
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
    public static $expire_period = [
        '30'=>'дней',
        '21'=>'день',
        '14'=>'дней',
        '7'=>'дней',
        '3'=>'дня',
        '1'=>'день',
    ];



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
			array('u_id, r_id, t_id, reg_id, c_id, date_add, date_lastedit, expire_period, date_expire, client_name, client_email, client_phone, notice_type_id, title, notice_text, active_tag, verify_tag, checksum,  views_count, moder_counted_tag, cost, cost_valuta',  'required'),
			array('u_id, r_id, t_id, reg_id, c_id, expire_period, active_tag, verify_tag, deactive_moder_id, moder_tag, moder_id, views_count, deleted_tag, otkaz_id, moder_counted_tag', 'numerical', 'integerOnly'=>true),
			array('date_add, date_lastedit, date_expire, date_deactive, date_moder, date_delete, date_sort', 'length', 'max'=>14),
			array('client_name, client_email, client_phone, phone_search, title, reject_reason', 'length', 'max'=>256),
			array('notice_type_id, from_ip', 'length', 'max'=>16),
			array('checksum', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('n_id, u_id, r_id, t_id, reg_id, c_id, date_add, date_lastedit, date_expire, client_name, client_email, client_phone, phone_search, notice_type_id, title, notice_text, active_tag, verify_tag, date_deactive, deactive_moder_id, checksum, moder_tag, date_moder, moder_id, views_count, deleted_tag, date_delete, reject_reason, otkaz_id, date_sort, from_ip, moder_counted_tag', 'safe', 'on'=>'search'),
		);
	}


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


	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'user'=>array(self::BELONGS_TO, 'Users', array('id'=>'u_id')),
            'country'=>array(self::BELONGS_TO, 'Countries', 'c_id')
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
