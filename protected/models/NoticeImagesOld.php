<?php

/**
 * This is the model class for table "ohtbsfvre_notice_images".
 *
 * The followings are the available columns in table 'ohtbsfvre_notice_images':
 * @property integer $ni_id
 * @property integer $n_id
 * @property string $filename
 * @property string $file_ext
 * @property integer $titul_tag
 * @property integer $fotonumber
 */
class NoticeImagesOld extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ohtbsfvre_notice_images';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('n_id, filename, file_ext, titul_tag, fotonumber', 'required'),
			array('n_id, titul_tag, fotonumber', 'numerical', 'integerOnly'=>true),
			array('filename', 'length', 'max'=>255),
			array('file_ext', 'length', 'max'=>4),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ni_id, n_id, filename, file_ext, titul_tag, fotonumber', 'safe', 'on'=>'search'),
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
			'ni_id' => 'Ni',
			'n_id' => 'N',
			'filename' => 'Filename',
			'file_ext' => 'File Ext',
			'titul_tag' => 'Titul Tag',
			'fotonumber' => 'Fotonumber',
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

		$criteria->compare('ni_id',$this->ni_id);
		$criteria->compare('n_id',$this->n_id);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('file_ext',$this->file_ext,true);
		$criteria->compare('titul_tag',$this->titul_tag);
		$criteria->compare('fotonumber',$this->fotonumber);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NoticeImagesOld the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
