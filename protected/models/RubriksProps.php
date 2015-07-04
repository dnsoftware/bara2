<?php

/**
 * This is the model class for table "{{rubriks_props}}".
 *
 * The followings are the available columns in table '{{rubriks_props}}':
 * @property integer $rp_id
 * @property integer $r_id
 * @property integer $pr_id
 * @property integer $hierarhy_tag
 * @property integer $hierarhy_level
 * @property integer $display_sort
 * @property integer $use_in_filter
 */
class RubriksProps extends CActiveRecord
{
    public static $vibor_type = [''=>'-- тип выбора --',
                                 'string'=>'строка (ручной ввод)',
                                 'radio'=>'radio',
                                 'checkbox'=>'checkbox',
                                 'selector'=>'selector',
                                 'listitem'=>'ссылка из списка',
                                 'autoload'=>'поле с автоподгрузкой',
                                 'autoload_with_listitem'=>'автоподгрузка + список',
                                 'photoblock'=>'блок фотографий',
                                ];

    public static $sort_sprav = [''=>'-- тип сортировки свойств --',
        'asc'=>'по возрастанию',
        'desc'=>'по убыванию',
        'sort_number'=>'по полю сортировки'
    ];

    // Шаблон возможных опций настройки формы подачи объявления
    public static $addform_options = array(

    );

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{rubriks_props}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('r_id, selector, name, type_id, vibor_type, sort_props_sprav, ptype', 'required'),
            array('selector', 'unique'),
            array('r_id', 'numerical', 'min'=>1),
			array('hierarhy_tag, hierarhy_level, display_sort, use_in_filter, parent_id, require_prop_tag, hide_if_no_elems_tag, all_values_in_filter', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('rp_id, r_id, hierarhy_tag, hierarhy_level, display_sort, use_in_filter', 'safe', 'on'=>'search'),
		);
	}

    // Получение массива "потенциальных" родителей. Такими могут по идее любые свойства.
    // сортируем эти свойства сначала по уровню иерархии, а потом по display_sort
    public static function getPotentialParents($r_id, $rp_id)
    {
        $model_props = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id = '.$r_id,
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
        ));

//deb::dump($model_props);

       $hierarhy_array = array('0'=>'-- нет зависимости --');
       foreach($model_props as $mkey => $mval)
       {
           $hierarhy_array[$mval->rp_id] = $mval->name;
           /*
           if ($mval->rp_id == $rp_id)
           {
               break;
           }
           else
           {
               $hierarhy_array[$mval->rp_id] = $mval->name;
           }
           */
       }

       return $hierarhy_array;

    }

    // $r_id - код рубрики в таблице rubriks
    // $rp_id - код текущего выбранного свойства
    public static function getParentHierarchyChain($r_id, $rp_id)
    {
        $rubriks_props_row = RubriksProps::model()->findByPk($rp_id);
        //deb::dump($rubriks_props_row);
        $hierarhy_array = array();
        while ($rubriks_props_row->parent_id != 0)
        {
            array_unshift($hierarhy_array, $rubriks_props_row->rp_id);

            $rubriks_props_row = RubriksProps::model()->find(array(
                'select'=>'*',
                'condition'=>'rp_id = '.$rubriks_props_row->parent_id,
            ));
        }
        array_unshift($hierarhy_array, $rubriks_props_row->rp_id);

        $i=0;
        $prevkey = 0;
        foreach($hierarhy_array as $hkey=>$hval)
        {
            $hierarhy_chain[$prevkey] = $hval;
            $prevkey = $hval;
        }
        //deb::dump($hierarhy_chain);

        return $hierarhy_chain;
    }

    public static function getSimpleRangeSpr($rp_id)
    {
        $model = RubriksProps::model()->findByPk($rp_id);
//deb::dump($model);
        $range_spr = array();
        if ($model->type_id == 'simple_range')
        {
            $sort_str = PropsSprav::getSortSql($model->sort_props_sprav);
            //deb::dump($sort_str);
            $range_spr = PropsSprav::model()->findAll(array(
                'select'=>'*',
                'condition'=>'rp_id = '.$rp_id,
                'order'=>$sort_str,
                ));
            //deb::dump($range_spr);

        }

        return $range_spr;
    }


    // Получение массива обязательных к заполнению свойств
    public static function getRequireProps($r_id)
    {
        $require_props = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id = '.$r_id.' AND require_prop_tag = 1 ',
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
        ));

        $require_props_array = array();
        foreach($require_props as $val)
        {
            $require_props_array[$val->selector] = $val;
        }

        return $require_props_array;
    }

    // Получение массива всех свойств данной рубрики - индекс selector
    public static function getAllProps($r_id)
    {
        $all_props = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id = '.$r_id,
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
        ));

        $all_props_array = array();
        foreach($all_props as $val)
        {
            $all_props_array[$val->selector] = $val;
        }

        return $all_props_array;
    }

    // Получение массива всех свойств данной рубрики индекс - rp_id
    public static function getAllPropsRp_id($r_id)
    {
        $all_props = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id = '.$r_id,
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
        ));

        $all_props_array = array();
        foreach($all_props as $val)
        {
            $all_props_array[$val->rp_id] = $val;
        }

        return $all_props_array;
    }

    // Получение массива всех свойств данной рубрики, сгруппированных по vibor_type
    public static function getPropsByViborType($r_id)
    {
        $all_props = self::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id = '.$r_id,
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
        ));

        $props_array = array();
        foreach($all_props as $val)
        {
            $props_array[$val->vibor_type][$val->selector] = $val;
        }

        return $props_array;
    }


    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'notice_props'=>array(self::HAS_MANY, 'NoticeProps', 'rp_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'rp_id' => 'Rp',
			'r_id' => 'R',
			'pr_id' => 'Pr',
			'hierarhy_tag' => 'Hierarhy Tag',
			'hierarhy_level' => 'Hierarhy Level',
			'display_sort' => 'Display Sort',
			'use_in_filter' => 'Use In Filter',
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

		$criteria->compare('rp_id',$this->rp_id);
		$criteria->compare('r_id',$this->r_id);
		$criteria->compare('pr_id',$this->pr_id);
		$criteria->compare('hierarhy_tag',$this->hierarhy_tag);
		$criteria->compare('hierarhy_level',$this->hierarhy_level);
		$criteria->compare('display_sort',$this->display_sort);
		$criteria->compare('use_in_filter',$this->use_in_filter);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RubriksProps the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
