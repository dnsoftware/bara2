<?php

/**
 * This is the model class for table "{{seo_keywords_notice}}".
 *
 * The followings are the available columns in table '{{seo_keywords_notice}}':
 * @property integer $kn_id
 * @property integer $k_id
 * @property integer $n_id
 * @property string $position
 */
class SeoKeywordsNotice extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{seo_keywords_notice}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('k_id, n_id, position', 'required'),
			array('k_id, n_id', 'numerical', 'integerOnly'=>true),
			array('position', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('kn_id, k_id, n_id, position', 'safe', 'on'=>'search'),
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
			'kn_id' => 'Kn',
			'k_id' => 'K',
			'n_id' => 'N',
			'position' => 'Position',
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

		$criteria->compare('kn_id',$this->kn_id);
		$criteria->compare('k_id',$this->k_id);
		$criteria->compare('n_id',$this->n_id);
		$criteria->compare('position',$this->position,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SeoKeywordsNotice the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
