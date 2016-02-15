<?php

/**
 * This is the model class for table "{{seo_board_words}}".
 *
 * The followings are the available columns in table '{{seo_board_words}}':
 * @property integer $sw_id
 * @property integer $r_id
 * @property string $signature
 * @property string $signature_ps_id
 * @property string $words
 */
class SeoBoardWords extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{seo_board_words}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('r_id', 'required'),
            array('signature, signature_ps_id, words', 'safe'),
			array('r_id', 'numerical', 'integerOnly'=>true),
			array('signature, signature_ps_id', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('sw_id, r_id, signature, signature_ps_id, words', 'safe', 'on'=>'search'),
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
			'sw_id' => 'Sw',
			'r_id' => 'R',
			'signature' => 'Signature',
			'signature_ps_id' => 'Signature Ps',
			'words' => 'Words',
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

		$criteria->compare('sw_id',$this->sw_id);
		$criteria->compare('r_id',$this->r_id);
		$criteria->compare('signature',$this->signature,true);
		$criteria->compare('signature_ps_id',$this->signature_ps_id,true);
		$criteria->compare('words',$this->words,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SeoBoardWords the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
