<?php

/**
 * This is the model class for table "{{prop_types}}".
 *
 * The followings are the available columns in table '{{prop_types}}':
 * @property string $type_id
 * @property string $name
 */
class PropTypes extends CActiveRecord
{
    private static $_props_type = [];


    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{prop_types}}';
	}

    public static function getPropsType()
    {
        if (count(self::$_props_type) == 0)
        {
            $temp = self::model()->findAll();
            self::$_props_type[''] = '-- тип свойства --';
            foreach($temp as $tkey=>$tval)
            {
                self::$_props_type[$tval->type_id] = $tval->name;
            }

        }

        return self::$_props_type;
    }

    public static function loadTable()
    {
        $_props_type = self::model()->findAll();
    }



	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type_id, name', 'required'),
			array('type_id', 'length', 'max'=>128),
			array('name', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('type_id, name', 'safe', 'on'=>'search'),
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
			'type_id' => 'Type',
			'name' => 'Name',
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

		$criteria->compare('type_id',$this->type_id,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PropTypes the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
