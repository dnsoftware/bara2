<?php

/**
 * This is the model class for table "{{rubriks_props}}".
 *
 * The followings are the available columns in table '{{rubriks_props}}':
 * @property integer $rp_id
 * @property integer $r_id
 * @property integer $pr_id
 * @property integer $hierarhy_tag
 * @property integer $hierarhy_level
 * @property integer $display_sort
 * @property integer $use_in_filter
 */
class RubriksProps extends CActiveRecord
{
    public static $vibor_type = [''=>'-- тип выбора --',
                                 'radio'=>'только один',
                                 'checkbox'=>'ни одного или несколько',
                                 'selector'=>'один из выпадающего списка'
                                ];

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{rubriks_props}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('r_id, selector, name, type_id', 'required'),
            array('selector', 'unique'),
			array('r_id, hierarhy_tag, hierarhy_level, display_sort, use_in_filter', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('rp_id, r_id, hierarhy_tag, hierarhy_level, display_sort, use_in_filter', 'safe', 'on'=>'search'),
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
			'rp_id' => 'Rp',
			'r_id' => 'R',
			'pr_id' => 'Pr',
			'hierarhy_tag' => 'Hierarhy Tag',
			'hierarhy_level' => 'Hierarhy Level',
			'display_sort' => 'Display Sort',
			'use_in_filter' => 'Use In Filter',
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

		$criteria->compare('rp_id',$this->rp_id);
		$criteria->compare('r_id',$this->r_id);
		$criteria->compare('pr_id',$this->pr_id);
		$criteria->compare('hierarhy_tag',$this->hierarhy_tag);
		$criteria->compare('hierarhy_level',$this->hierarhy_level);
		$criteria->compare('display_sort',$this->display_sort);
		$criteria->compare('use_in_filter',$this->use_in_filter);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RubriksProps the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
