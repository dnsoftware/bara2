<?php

/**
 * This is the model class for table "{{user_phones}}".
 *
 * The followings are the available columns in table '{{user_phones}}':
 * @property integer $ph_id
 * @property integer $u_id
 * @property integer $c_id
 * @property string $phone
 * @property string $date_add
 * @property integer $verify_kod
 * @property integer $verify_tag
 * @property string $message_id
 */
class UserPhones extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_phones}}';
	}



    // Генерация массива масок телефонных номеров в зависимости от страны
    // $country - запись ActiveRecord таблицы стран
    public static function PhoneMaskGenerate($country_phone_kod)
    {
        $mask = '';
        if(strlen($country_phone_kod) == 3)
        {
            $mask = "xxxxx-xx-xx";
        }
        if(strlen($country_phone_kod) == 2)
        {
            $mask = "xxxxxx-xx-xx";
        }
        if(strlen($country_phone_kod) == 1)
        {
            $mask = "xxxxxx-xx-xx";
        }
        if(strlen($country_phone_kod) == 4)
        {
            $mask = "xxx-xx-xx";
        }


        return $mask;
    }










	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_id, c_id, date_add', 'required'),
			array('u_id, c_id, verify_kod, verify_tag', 'numerical', 'integerOnly'=>true),
			array('phone', 'length', 'max'=>16),
			array('date_add', 'length', 'max'=>14),

            array('phone', 'validatephone'),

            // The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ph_id, u_id, c_id, phone, date_add, verify_kod, verify_tag', 'safe', 'on'=>'search'),
		);
	}

    // Проверка корректности ввода телефона
    public function validatephone($attr, $params)
    {

        if(Yii::app()->controller->id == 'registration')
        {
            // если регистрация, то или пустое поле или заполненное но отсутствующее в базе
            if(Yii::app()->controller->action->id == 'registration')
            {
                if($phonerow = self::model()->findByAttributes(array('phone'=>$this->phone)))
                {
                    $this->addError($attr, 'Указанный телефон уже присутствует в базе!');
                }

            }
            else
            {
                // Проверка на обязательное поле, если экшн - не регистрация
                if(strlen($this->phone) < 10)
                {
                    $this->addError($attr, 'Необходимо заполнить поле Телефон');
                }
            }
        }

        if(Yii::app()->controller->id == 'profile')
        {
            $ph_id = intval($_POST['UserPhones']['ph_id']);
            $c_id = intval($_POST['UserPhones']['c_id']);
            $phone = htmlspecialchars($_POST['UserPhones']['phone']);

            if($ph_id <= 0) // Новый телефон
            {
                if($phoneinbase = UserPhones::model()->find(array(
                    'select'=>'*',
                    'condition'=>'c_id='. $c_id . " AND phone = '".$phone."'"
                )))
                {
                    $this->addError($attr, 'Такой телефон уже есть в базе');
                }
            }
            else
            {
                if(strlen($phone) < 10)
                {
                    $this->addError($attr, 'Укажите номер телефона!');
                }

                if($phoneinbase = UserPhones::model()->find(array(
                    'select'=>'*',
                    'condition'=>'ph_id <> '.$ph_id.' AND c_id='. $c_id . " AND phone = '".$phone."'"
                )))
                {
                    $this->addError($attr, 'Такой телефон уже есть в базе');
                }
            }

            //$this->addError($attr, serialize($_POST['UserPhones']));
        }

    }


    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ph_id' => 'Ph',
			'u_id' => 'U',
			'c_id' => 'Страна',
			'phone' => 'Phone',
			'date_add' => 'Date Add',
			'verify_kod' => 'Verify Kod',
			'verify_tag' => 'Verify Tag',
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

		$criteria->compare('ph_id',$this->ph_id);
		$criteria->compare('u_id',$this->u_id);
		$criteria->compare('c_id',$this->c_id);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('date_add',$this->date_add,true);
		$criteria->compare('verify_kod',$this->verify_kod);
		$criteria->compare('verify_tag',$this->verify_tag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserPhones the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
