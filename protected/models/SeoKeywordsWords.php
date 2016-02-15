<?php

/**
 * This is the model class for table "{{seo_keywords_words}}".
 *
 * The followings are the available columns in table '{{seo_keywords_words}}':
 * @property integer $w_id
 * @property string $fullkeystring
 * @property integer $hash
 * @property integer $count
 */
class SeoKeywordsWords extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{seo_keywords_words}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fullkeystring, hash, count', 'required'),
			array('count', 'numerical', 'integerOnly'=>true),
			array('fullkeystring', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('w_id, fullkeystring, hash, count', 'safe', 'on'=>'search'),
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
			'w_id' => 'W',
			'fullkeystring' => 'Fullkeystring',
			'hash' => 'Hash',
			'count' => 'Count',
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

		$criteria->compare('w_id',$this->w_id);
		$criteria->compare('fullkeystring',$this->fullkeystring,true);
		$criteria->compare('hash',$this->hash);
		$criteria->compare('count',$this->count);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SeoKeywordsWords the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
