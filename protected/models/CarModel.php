<?php

/**
 * This is the model class for table "car_model".
 *
 * The followings are the available columns in table 'car_model':
 * @property integer $id_car_model
 * @property integer $id_car_mark
 * @property string $name
 * @property integer $id_car_type
 * @property string $name_rus
 * @property integer $is_error_ignore
 */
class CarModel extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'car_model';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_car_mark, name, id_car_type', 'required'),
			array('id_car_mark, id_car_type, is_error_ignore', 'numerical', 'integerOnly'=>true),
			array('name, name_rus', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id_car_model, id_car_mark, name, id_car_type, name_rus, is_error_ignore', 'safe', 'on'=>'search'),
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
			'id_car_model' => 'Id Car Model',
			'id_car_mark' => 'Id Car Mark',
			'name' => 'Name',
			'id_car_type' => 'Id Car Type',
			'name_rus' => 'Name Rus',
			'is_error_ignore' => 'Is Error Ignore',
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

		$criteria->compare('id_car_model',$this->id_car_model);
		$criteria->compare('id_car_mark',$this->id_car_mark);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('id_car_type',$this->id_car_type);
		$criteria->compare('name_rus',$this->name_rus,true);
		$criteria->compare('is_error_ignore',$this->is_error_ignore);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CarModel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
