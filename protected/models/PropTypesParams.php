<?php

/**
 * This is the model class for table "{{prop_types_params}}".
 *
 * The followings are the available columns in table '{{prop_types_params}}':
 * @property integer $pt_id
 * @property string $type_id
 * @property string $selector
 * @property string $name
 * @property string $ptype
 * @property string $maybe_count
 */
class PropTypesParams extends CActiveRecord
{

    public static $maybe_count_spr = [''=>'-- кол-во элементов --',
        'one'=>'один элемент',
        'many'=>'несколько элементов',
    ];

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{prop_types_params}}';
	}

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type_id, selector, name, ptype, maybe_count', 'required'),
			array('type_id, selector, ptype', 'length', 'max'=>128),
			array('name', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('pt_id, type_id, selector, name, ptype', 'safe', 'on'=>'search'),
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
			'pt_id' => 'Pt',
			'type_id' => 'Type',
			'selector' => 'Selector',
			'name' => 'Name',
			'ptype' => 'Ptype',
            'maybe_count' => 'Maybe_count',
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

		$criteria->compare('pt_id',$this->pt_id);
		$criteria->compare('type_id',$this->type_id,true);
		$criteria->compare('selector',$this->selector,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('ptype',$this->ptype,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PropTypesParams the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
