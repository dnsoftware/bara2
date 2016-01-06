<?php

/**
 * This is the model class for table "{{rubriks}}".
 *
 * The followings are the available columns in table '{{rubriks}}':
 * @property integer $r_id
 * @property integer $parent_id
 * @property string $name
 */
class Rubriks extends CActiveRecord
{
    // Список полей, которые могут быть исключены из формы добавления/редактирования объявления
    public static $notice_add_fields_exception = array(
        'video_youtube'=>'Ссылка на ролик с Youtube',
        'client_phone'=>'Телефон клиента'
    );

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{rubriks}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_id, name, sort_num, transname', 'required'),
            array('transname', 'unique'),
			array('parent_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>256),
            array('advert_list_item_shablon', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('r_id, parent_id, name', 'safe', 'on'=>'search'),
		);
	}

    public static function get_rublist($display_hide = false)
    {
        $display_hide_sql = " AND hide_tag = 0 ";
        if($display_hide)
        {
            $display_hide_sql = " ";
        }

        $rubs = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'1 '.$display_hide_sql,
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

    public static function get_simple_rublist()
    {
        $rubs = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'1',
            'order' => 'parent_id, name'
        ));

        $rub_array = [];
        foreach ($rubs as $rkey => $rval)
        {
            $rub_array[$rval->r_id] = $rval;
        }

        return $rub_array;
    }

    public static function get_all_subrubs()
    {
        $rubs = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'parent_id > 0',
            'order' => 'parent_id, name'
        ));

        $rub_array = [];
        foreach ($rubs as $rkey => $rval)
        {
            $rub_array[$rval->r_id] = $rval;
        }

        return $rub_array;
    }

    public static function get_parentlist()
    {
        $rubs = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'parent_id=0',
            'order' => 'parent_id, name'
        ));

        $rub_array = [];
        foreach ($rubs as $rkey => $rval)
        {
            if ($rval->parent_id==0)
            {
                $rub_array[$rval->r_id] = $rval;
            }

        }

        $parent_list = array(''=>'-выберите родительскую рубрику-');
        foreach ($rub_array as $pkey=>$pval)
        {
            $parent_list[$pkey] = $pval['name'];
        }

        return $parent_list;
    }

    // Получение массива шаблонов отображения из рубрик
    public static function GetShablonsDisplay()
    {
        $shablons = Rubriks::model()->findAll(array('select'=>'r_id, advert_list_item_shablon'));
        $shablons_display = array();
        foreach($shablons as $skey=>$sval)
        {
            $shablons_display[$sval->r_id] = $sval->advert_list_item_shablon;
        }

        return $shablons_display;
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Rubriks the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
