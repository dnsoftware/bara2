<?php

/**
 * This is the model class for table "sxgeo_cities".
 *
 * The followings are the available columns in table 'sxgeo_cities':
 * @property integer $id
 * @property integer $region_id
 * @property string $name_ru
 * @property string $name_en
 * @property string $lat
 * @property string $lon
 * @property string $okato
 */
class SxgeoCities extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sxgeo_cities';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'required'),
			array('id, region_id', 'numerical', 'integerOnly'=>true),
			array('name_ru, name_en', 'length', 'max'=>128),
			array('lat, lon', 'length', 'max'=>10),
			array('okato', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, region_id, name_ru, name_en, lat, lon, okato', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'region_id' => 'Region',
			'name_ru' => 'Name Ru',
			'name_en' => 'Name En',
			'lat' => 'Lat',
			'lon' => 'Lon',
			'okato' => 'Okato',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('region_id',$this->region_id);
		$criteria->compare('name_ru',$this->name_ru,true);
		$criteria->compare('name_en',$this->name_en,true);
		$criteria->compare('lat',$this->lat,true);
		$criteria->compare('lon',$this->lon,true);
		$criteria->compare('okato',$this->okato,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SxgeoCities the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
