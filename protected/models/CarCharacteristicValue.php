<?php

/**
 * This is the model class for table "car_characteristic_value".
 *
 * The followings are the available columns in table 'car_characteristic_value':
 * @property integer $id_car_characteristic_value
 * @property string $value
 * @property string $unit
 * @property integer $id_car_characteristic
 * @property integer $id_car_modification
 * @property integer $id_car_type
 */
class CarCharacteristicValue extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'car_characteristic_value';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('value, id_car_characteristic, id_car_modification, id_car_type', 'required'),
			array('id_car_characteristic, id_car_modification, id_car_type', 'numerical', 'integerOnly'=>true),
			array('value, unit', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id_car_characteristic_value, value, unit, id_car_characteristic, id_car_modification, id_car_type', 'safe', 'on'=>'search'),
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
			'id_car_characteristic_value' => 'Id Car Characteristic Value',
			'value' => 'Value',
			'unit' => 'Unit',
			'id_car_characteristic' => 'Id Car Characteristic',
			'id_car_modification' => 'Id Car Modification',
			'id_car_type' => 'Id Car Type',
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

		$criteria->compare('id_car_characteristic_value',$this->id_car_characteristic_value);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('id_car_characteristic',$this->id_car_characteristic);
		$criteria->compare('id_car_modification',$this->id_car_modification);
		$criteria->compare('id_car_type',$this->id_car_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CarCharacteristicValue the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
