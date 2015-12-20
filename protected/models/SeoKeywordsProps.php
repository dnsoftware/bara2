<?php

/**
 * This is the model class for table "{{seo_keywords_props}}".
 *
 * The followings are the available columns in table '{{seo_keywords_props}}':
 * @property integer $kp_id
 * @property integer $k_id
 * @property integer $rp_id
 * @property integer $ps_id
 */
class SeoKeywordsProps extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{seo_keywords_props}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('k_id, rp_id, ps_id', 'required'),
			array('k_id, rp_id, ps_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('kp_id, k_id, rp_id, ps_id', 'safe', 'on'=>'search'),
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
			'kp_id' => 'Kp',
			'k_id' => 'K',
			'rp_id' => 'Rp',
			'ps_id' => 'Ps',
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

		$criteria->compare('kp_id',$this->kp_id);
		$criteria->compare('k_id',$this->k_id);
		$criteria->compare('rp_id',$this->rp_id);
		$criteria->compare('ps_id',$this->ps_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SeoKeywordsProps the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
