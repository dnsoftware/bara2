<?php

/**
 * This is the model class for table "{{notice_props}}".
 *
 * The followings are the available columns in table '{{notice_props}}':
 * @property integer $np_id
 * @property integer $n_id
 * @property integer $rp_id
 * @property integer $ps_id
 * @property string $hand_input_value
 */
class NoticeProps extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{notice_props}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('n_id, rp_id, ps_id', 'required'),
			array('n_id, rp_id, ps_id', 'numerical', 'integerOnly'=>true),
            array('hand_input_value_digit', 'numerical'),
			array('hand_input_value', 'length', 'max'=>1024),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('np_id, n_id, rp_id, ps_id, hand_input_value', 'safe', 'on'=>'search'),
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
			'np_id' => 'Np',
			'n_id' => 'N',
			'rp_id' => 'Rp',
			'ps_id' => 'Ps',
			'hand_input_value' => 'Hand Input Value',
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

		$criteria->compare('np_id',$this->np_id);
		$criteria->compare('n_id',$this->n_id);
		$criteria->compare('rp_id',$this->rp_id);
		$criteria->compare('ps_id',$this->ps_id);
		$criteria->compare('hand_input_value',$this->hand_input_value,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NoticeProps the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
