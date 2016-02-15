<?php

/**
 * This is the model class for table "{{sitemap_scan_metadata}}".
 *
 * The followings are the available columns in table '{{sitemap_scan_metadata}}':
 * @property integer $m_id
 * @property string $date_expire_start
 * @property string $date_expire_end
 * @property integer $archive_tag
 * @property string $filename
 * @property string $block_scan_date
 * @property integer $urls_count
 * @property integer $scan_hours_count
 * @property integer $block_index
 * @property string $scan_date_after
 */
class SitemapScanMetadata extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{sitemap_scan_metadata}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date_expire_start, date_expire_end, archive_tag, filename, block_scan_date, urls_count, scan_hours_count, block_index, scan_date_after', 'required'),
			array('archive_tag, urls_count, scan_hours_count, block_index', 'numerical', 'integerOnly'=>true),
			array('date_expire_start, date_expire_end, block_scan_date, scan_date_after', 'length', 'max'=>14),
			array('filename', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('m_id, date_expire_start, date_expire_end, archive_tag, filename, block_scan_date, urls_count, scan_hours_count, block_index, scan_date_after', 'safe', 'on'=>'search'),
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
			'm_id' => 'M',
			'date_expire_start' => 'Date Expire Start',
			'date_expire_end' => 'Date Expire End',
			'archive_tag' => 'Archive Tag',
			'filename' => 'Filename',
			'block_scan_date' => 'Block Scan Date',
			'urls_count' => 'Urls Count',
			'scan_hours_count' => 'Scan Hours Count',
			'block_index' => 'Block Index',
			'scan_date_after' => 'Scan Date After',
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

		$criteria->compare('m_id',$this->m_id);
		$criteria->compare('date_expire_start',$this->date_expire_start,true);
		$criteria->compare('date_expire_end',$this->date_expire_end,true);
		$criteria->compare('archive_tag',$this->archive_tag);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('block_scan_date',$this->block_scan_date,true);
		$criteria->compare('urls_count',$this->urls_count);
		$criteria->compare('scan_hours_count',$this->scan_hours_count);
		$criteria->compare('block_index',$this->block_index);
		$criteria->compare('scan_date_after',$this->scan_date_after,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SitemapScanMetadata the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
