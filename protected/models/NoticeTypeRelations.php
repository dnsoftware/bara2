<?php

/**
 * This is the model class for table "{{notice_type_relations}}".
 *
 * The followings are the available columns in table '{{notice_type_relations}}':
 * @property integer $ntr_id
 * @property integer $r_id
 * @property string $notice_type_id
 * @property string $notice_fields_exception
 */
class NoticeTypeRelations extends CActiveRecord
{
    public static $notice_type = array(
        'kup'=>'куплю',
        'prd'=>'продам',
        'otd'=>'отдам даром',
        'obm'=>'обменяю',
        'snm'=>'сниму',
        'sdm'=>'сдам',

    );

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{notice_type_relations}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('r_id, notice_type_id, image_field_tag', 'required'),
			array('r_id', 'numerical', 'integerOnly'=>true),
			array('notice_type_id', 'length', 'max'=>16),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ntr_id, r_id, notice_type_id, notice_fields_exception', 'safe', 'on'=>'search'),
		);
	}


    // Получение списка доступных типов объявлений в зависимости от рубрики
    public static function getNoticeTypeList($r_id)
    {
        $model = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id='.$r_id,
            'order'=>'notice_type_id ASC'
        ));

        $result_array = array();
        foreach($model as $mval)
        {
            $result_array[$mval->notice_type_id] = $mval;
        }

        return $result_array;

    }

    // Получение списка регионов для select
    public static function displayNoticeTypeList($r_id, $selected_id = '')
    {
        $result_array = self::getNoticeTypeList($r_id);

        ?>
        <option value="">Тип объявления</option>
        <?
        if(count($result_array) > 0)
        {
            foreach($result_array as $mval)
            {
                $selected = " ";
                if($mval->notice_type_id == $selected_id)
                {
                    $selected = " selected ";
                }
                ?>
                <option <?= $selected;?> value="<?= $mval->notice_type_id;?>"><?= self::$notice_type[$mval->notice_type_id];?></option>
            <?
            }
        }

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
			'ntr_id' => 'Ntr',
			'r_id' => 'R',
			'notice_type_id' => 'Notice Type',
			'notice_fields_exception' => 'Notice Fields Exception',
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

		$criteria->compare('ntr_id',$this->ntr_id);
		$criteria->compare('r_id',$this->r_id);
		$criteria->compare('notice_type_id',$this->notice_type_id,true);
		$criteria->compare('notice_fields_exception',$this->notice_fields_exception,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NoticeTypeRelations the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
