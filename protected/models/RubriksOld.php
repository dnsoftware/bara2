<?php

/**
 * This is the model class for table "{{rubriks_old}}".
 *
 * The followings are the available columns in table '{{rubriks_old}}':
 * @property integer $r_id
 * @property integer $parent_id
 * @property string $name
 * @property integer $sort_number
 * @property integer $count_demand
 * @property integer $count_supply
 * @property integer $notice_type_index
 * @property string $contema_theme
 */
class RubriksOld extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{rubriks_old}}';
	}


    public static function get_rublist()
    {
        $rubs = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'1',
            'order' => 'parent_id, name'
        ));

        $rub_array = [];
        foreach ($rubs as $rkey => $rval)
        {
            if ($rval->parent_id==0)
            {
                $rub_array[$rval->r_id]['parent'] = $rval;
            }
            else
            {
                $rub_array[$rval->parent_id]['childs'][$rval->r_id] = $rval;
            }

        }

        return $rub_array;
    }


    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('notice_type_index, contema_theme', 'required'),
			array('parent_id, sort_number, count_demand, count_supply, notice_type_index', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('contema_theme', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('r_id, parent_id, name, sort_number, count_demand, count_supply, notice_type_index, contema_theme', 'safe', 'on'=>'search'),
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
			'r_id' => 'R',
			'parent_id' => 'Parent',
			'name' => 'Name',
			'sort_number' => 'Sort Number',
			'count_demand' => 'Count Demand',
			'count_supply' => 'Count Supply',
			'notice_type_index' => 'Notice Type Index',
			'contema_theme' => 'Contema Theme',
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

		$criteria->compare('r_id',$this->r_id);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('sort_number',$this->sort_number);
		$criteria->compare('count_demand',$this->count_demand);
		$criteria->compare('count_supply',$this->count_supply);
		$criteria->compare('notice_type_index',$this->notice_type_index);
		$criteria->compare('contema_theme',$this->contema_theme,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RubriksOld the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
