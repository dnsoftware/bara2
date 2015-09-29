<?php

/**
 * This is the model class for table "{{options}}".
 *
 * The followings are the available columns in table '{{options}}':
 * @property string $daycount_date
 * @property integer $daycount_currcount
 */
class Options extends CActiveRecord
{
    public static $valutes = [
        'RUB'=>array(
            'name'=>'рубль',
            'abbr'=>'RUB',
            'symbol'=>'<img class="rubsymbol" src="/images/rub_sign.png" width="13px;">',
            'symbol2'=>'<img class="rubsymbol" src="/images/rub_sign2.png">',
            'name_rodit'=>'рублей'
        ),
        'USD'=>array(
            'name'=>'доллар',
            'abbr'=>'USD',
            'symbol'=>'$',
            'symbol2'=>'$',
            'name_rodit'=>'долларов'
        ),
        'EUR'=>array(
            'name'=>'евро',
            'abbr'=>'EUR',
            'symbol'=>'€',
            'symbol2'=>'€',
            'name_rodit'=>'евро'
        ),
    ];


    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{options}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('daycount_date, daycount_currcount', 'required'),
			array('daycount_currcount', 'numerical', 'integerOnly'=>true),
			array('daycount_date', 'length', 'max'=>14),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('daycount_date, daycount_currcount', 'safe', 'on'=>'search'),
		);
	}

    // Занесение значения опции в базу
    public static function setOption($optname, $optvalue)
    {
        $model = self::model()->findByPk(1);
        $model->$optname = $optvalue;
        $model->save();
    }

    // Получение опции из базы
    public static function getOption($optname)
    {
        $model = self::model()->findByPk(1);
        return $model->$optname;
    }

    // Получение всех опций из базы
    public static function getAllOptions()
    {
        $model = self::model()->findByPk(1);

        $opt_array = array();
        foreach($model as $mkey=>$mval)
        {
            $opt_array[$mkey] = $mval;
        }

        return $opt_array;
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
			'daycount_date' => 'Daycount Date',
			'daycount_currcount' => 'Daycount Currcount',
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

		$criteria->compare('daycount_date',$this->daycount_date,true);
		$criteria->compare('daycount_currcount',$this->daycount_currcount);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Options the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
