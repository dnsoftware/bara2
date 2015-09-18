<?php

/**
 * This is the model class for table "{{towns}}".
 *
 * The followings are the available columns in table '{{towns}}':
 * @property integer $t_id
 * @property integer $reg_id
 * @property integer $c_id
 * @property string $name
 * @property string $transname
 * @property string $inname
 */
class Towns extends CActiveRecord
{
    // Для подмены регионов в форме поиска объяв, 'город'=>'регион'
    public static $alter_regions = array(
        // Москва и Московская область
        '524901'=>'524925',
        // Санкт-Петербург и Ленинградская область
        '498817'=>'536199',
        //Севастополь и Республика Крым
        '694423'=>'703883',
        // Страшенский раойон
        '617302'=>'617301'

    );



    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{towns}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('reg_id, c_id, name, transname, inname', 'required'),
            array('transname', 'unique'),
			array('reg_id, c_id', 'numerical', 'integerOnly'=>true),
			array('name, transname, inname', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('t_id, reg_id, c_id, name, transname, inname', 'safe', 'on'=>'search'),
		);
	}


    // Получение списка городов по региону
    public static function getTownList($reg_id)
    {
        $model = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'reg_id='.$reg_id." AND double_tag = 0",
            'order'=>'name ASC'
        ));

        $result_array = array();
        foreach($model as $mval)
        {
            $result_array[$mval->t_id] = $mval;
        }

        return $result_array;

    }

    // Получение списка городов для select
    public static function displayTownList($reg_id, $selected_id = 0, $null_value = 'Выберите город')
    {
        $result_array = self::getTownList($reg_id);

        ?>
        <option value=""><?= $null_value;?></option>
        <?
        if(count($result_array) > 0)
        {
            foreach($result_array as $mval)
            {
                $selected = " ";
                if($mval->t_id == $selected_id)
                {
                    $selected = " selected ";
                }
            ?>
                <option <?= $selected;?> value="<?= $mval->t_id;?>"><?= $mval->name;?></option>
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
			't_id' => 'T',
			'reg_id' => 'Reg',
			'c_id' => 'C',
			'name' => 'Name',
			'transname' => 'Transname',
			'inname' => 'Inname',
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

		$criteria->compare('t_id',$this->t_id);
		$criteria->compare('reg_id',$this->reg_id);
		$criteria->compare('c_id',$this->c_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('transname',$this->transname,true);
		$criteria->compare('inname',$this->inname,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Towns the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
