<?php

/**
 * This is the model class for table "car_modification".
 *
 * The followings are the available columns in table 'car_modification':
 * @property integer $id_car_modification
 * @property integer $id_car_serie
 * @property integer $id_car_model
 * @property string $name
 * @property integer $start_production_year
 * @property integer $end_production_year
 * @property integer $id_car_type
 */
class CarModification extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'car_modification';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_car_serie, id_car_model, name, id_car_type', 'required'),
			array('id_car_serie, id_car_model, start_production_year, end_production_year, id_car_type', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id_car_modification, id_car_serie, id_car_model, name, start_production_year, end_production_year, id_car_type', 'safe', 'on'=>'search'),
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
			'id_car_modification' => 'Id Car Modification',
			'id_car_serie' => 'Id Car Serie',
			'id_car_model' => 'Id Car Model',
			'name' => 'Name',
			'start_production_year' => 'Start Production Year',
			'end_production_year' => 'End Production Year',
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

		$criteria->compare('id_car_modification',$this->id_car_modification);
		$criteria->compare('id_car_serie',$this->id_car_serie);
		$criteria->compare('id_car_model',$this->id_car_model);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('start_production_year',$this->start_production_year);
		$criteria->compare('end_production_year',$this->end_production_year);
		$criteria->compare('id_car_type',$this->id_car_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CarModification the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
