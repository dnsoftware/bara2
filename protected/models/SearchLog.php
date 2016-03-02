<?php

/**
 * This is the model class for table "{{search_log}}".
 *
 * The followings are the available columns in table '{{search_log}}':
 * @property integer $sl_id
 * @property integer $ss_id
 * @property string $date_add
 * @property integer $u_id
 * @property integer $r_id
 * @property integer $t_id
 * @property integer $reg_id
 * @property integer $c_id
 * @property string $props_data
 */
class SearchLog extends CActiveRecord
{
    // Названия (подстроки) юзерагентов поисковиков для исключения их из логов
    public static $botuseragents = array(
        'Google', 'Yahoo', 'Slurp', 'MSNBot', 'Teoma', 'Scooter', 'ia_archiver',
        'Lycos', 'Yandex', 'Rambler', 'Mail.Ru', 'Aport', 'WebAlta'
    );


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{search_log}}';
	}


    // Проверка на вхождение названия поискового бота в подстроку
    public static function IsSearchBot($useragent)
    {
        foreach(self::$botuseragents as $bkey=>$bval)
        {
            if(strpos($useragent, $bval) !== false)
            {
                return true;
            }
        }

        return false;
    }


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ss_id, date_add, u_id, r_id, t_id, reg_id, c_id', 'required'),
			array('ss_id, u_id, r_id, t_id, reg_id, c_id', 'numerical', 'integerOnly'=>true),
			array('date_add', 'length', 'max'=>14),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('sl_id, ss_id, date_add, u_id, r_id, t_id, reg_id, c_id, props_data', 'safe', 'on'=>'search'),
		);
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
			'sl_id' => 'Sl',
			'ss_id' => 'Ss',
			'date_add' => 'Date Add',
			'u_id' => 'U',
			'r_id' => 'R',
			't_id' => 'T',
			'reg_id' => 'Reg',
			'c_id' => 'C',
			'props_data' => 'Props Data',
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

		$criteria->compare('sl_id',$this->sl_id);
		$criteria->compare('ss_id',$this->ss_id);
		$criteria->compare('date_add',$this->date_add,true);
		$criteria->compare('u_id',$this->u_id);
		$criteria->compare('r_id',$this->r_id);
		$criteria->compare('t_id',$this->t_id);
		$criteria->compare('reg_id',$this->reg_id);
		$criteria->compare('c_id',$this->c_id);
		$criteria->compare('props_data',$this->props_data,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SearchLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
