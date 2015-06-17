<?php

/**
 * This is the model class for table "{{countries}}".
 *
 * The followings are the available columns in table '{{countries}}':
 * @property integer $c_id
 * @property string $name
 * @property integer $sort_number
 */
class Countries extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{countries}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, sort_number', 'required'),
			array('sort_number', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('c_id, name, sort_number', 'safe', 'on'=>'search'),
		);
	}

    // Получение списка стран
    public static function getCountryList()
    {
        $model = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'1',
            'order'=>'sort_number ASC'
        ));

        $country_array = array();
        foreach($model as $mval)
        {
            $country_array[$mval->c_id] = $mval;
        }

        return $country_array;

    }

    // Получение списка стран для select
    public static function displayCountryList($selected_id = 0)
    {
        $result_array = self::getCountryList();

        ?>
        <option value="">Выберите страну</option>
        <?
        if(count($result_array) > 0)
        {
            foreach($result_array as $mval)
            {
                $selected = " ";
                if($mval->c_id == $selected_id)
                {
                    $selected = " selected ";
                }
                ?>
                <option <?= $selected;?> value="<?= $mval->c_id;?>"><?= $mval->name;?></option>
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
			'c_id' => 'C',
			'name' => 'Name',
			'sort_number' => 'Sort Number',
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

		$criteria->compare('c_id',$this->c_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('sort_number',$this->sort_number);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Countries the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
