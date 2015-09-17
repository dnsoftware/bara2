<?php

/**
 * This is the model class for table "{{regions}}".
 *
 * The followings are the available columns in table '{{regions}}':
 * @property integer $reg_id
 * @property integer $c_id
 * @property string $name
 * @property string $transname
 */
class Regions extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{regions}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('c_id, name', 'required'),
            array('transname', 'unique'),
			array('c_id', 'numerical', 'integerOnly'=>true),
			array('name, transname', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('reg_id, c_id, name', 'safe', 'on'=>'search'),
		);
	}

    // Получение списка регионов по стране
    public static function getRegionList($c_id)
    {
        $model = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'c_id='.$c_id,
            'order'=>'name ASC'
        ));

        $result_array = array();
        foreach($model as $mval)
        {
            $result_array[$mval->reg_id] = $mval;
        }

        return $result_array;

    }

    // Получение списка регионов (облегченный вариан), код - название из поля name
    public static function getRegionListLight()
    {
        $model = self::model()->findAll(array(
            'select'=>'reg_id, name',
            'condition'=>'1',
            'order'=>'name ASC'
        ));

        $region_array = array();
        foreach($model as $mval)
        {
            $region_array[$mval->reg_id] = $mval->name;
        }

        return $region_array;

    }

    // Получение списка регионов для select
    public static function displayRegionList($c_id, $selected_id = 0, $null_value = 'Выберите регион')
    {
        $result_array = self::getRegionList($c_id);

        ?>
        <option value=""><?= $null_value;?></option>
        <?
        if(count($result_array) > 0)
        {
            foreach($result_array as $mval)
            {
                $selected = " ";
                if($mval->reg_id == $selected_id)
                {
                    $selected = " selected ";
                }
            ?>
                <option <?= $selected;?> value="<?= $mval->reg_id;?>"><?= $mval->name;?></option>
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
			'reg_id' => 'Reg',
			'c_id' => 'C',
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

		$criteria->compare('reg_id',$this->reg_id);
		$criteria->compare('c_id',$this->c_id);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Regions the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
