<?php

/**
 * This is the model class for table "{{user_phones_sms_log}}".
 *
 * The followings are the available columns in table '{{user_phones_sms_log}}':
 * @property string $message_id
 * @property string $client_email
 * @property integer $с_id
 * @property string $phone
 * @property string $date_add
 * @property integer $verify_kod
 * @property integer $status
 * @property string $description
 * @property string $posted_at
 * @property string $updated_at
 * @property integer $parts
 * @property string $cost
 */
class UserPhonesSmsLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_phones_sms_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('message_id, с_id, phone, date_add, verify_kod, status, description, posted_at, updated_at, parts, cost', 'required'),
			array('с_id, verify_kod, status, parts, error_code', 'numerical', 'integerOnly'=>true),
			array('message_id', 'length', 'max'=>128),
			array('client_email', 'length', 'max'=>256),
			array('phone', 'length', 'max'=>16),
			array('date_add', 'length', 'max'=>14),
			array('description', 'length', 'max'=>64),
			array('posted_at, updated_at', 'length', 'max'=>32),
			array('cost', 'length', 'max'=>5),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('message_id, client_email, с_id, phone, date_add, verify_kod, status, description, posted_at, updated_at, parts, cost', 'safe', 'on'=>'search'),
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
			'message_id' => 'Message',
			'client_email' => 'Client Email',
			'с_id' => 'с',
			'phone' => 'Phone',
			'date_add' => 'Date Add',
			'verify_kod' => 'Verify Kod',
			'status' => 'Status',
			'description' => 'Description',
			'posted_at' => 'Posted At',
			'updated_at' => 'Updated At',
			'parts' => 'Parts',
			'cost' => 'Cost',
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

		$criteria->compare('message_id',$this->message_id,true);
		$criteria->compare('client_email',$this->client_email,true);
		$criteria->compare('с_id',$this->с_id);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('date_add',$this->date_add,true);
		$criteria->compare('verify_kod',$this->verify_kod);
		$criteria->compare('status',$this->status);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('posted_at',$this->posted_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('parts',$this->parts);
		$criteria->compare('cost',$this->cost,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserPhonesSmsLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
