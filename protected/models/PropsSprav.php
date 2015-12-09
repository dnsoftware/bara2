<?php

/**
 * This is the model class for table "{{props_sprav}}".
 *
 * The followings are the available columns in table '{{props_sprav}}':
 * @property integer $ps_id
 * @property integer $prop_id
 * @property string $selector
 * @property string $value
 */
class PropsSprav extends CActiveRecord
{
    public $maxsort = 0;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{props_sprav}}';
	}

    public static function getSortSql($sort_key)
    {
        $sort_sql = '';
        switch($sort_key)
        {
            case "asc":
                $sort_sql = 'value ASC';
                break;

            case "desc":
                $sort_sql = 'value DESC';
                break;

            case "sort_number":
                $sort_sql = 'sort_number ASC';
                break;
        }

        return $sort_sql;
    }

    public static function getPropsSprav($model_rubriks_props, $prop_types_params_row)
    {
        $sort_sql = self::getSortSql($model_rubriks_props->sort_props_sprav);

        $props_spav_records = PropsSprav::model()->findAll(
            array(
                'select'=>'*',
                'condition'=>'rp_id = '.$model_rubriks_props->rp_id.' AND selector = "'.$prop_types_params_row->selector.'"',
                'order'=>$sort_sql,
                //'limit'=>'10'
            )
        );

        return $props_spav_records;

    }

    public static function getPropsListAutocomplete($model_rubriks_props, $parent_ps_id, $search_str)
    {
        $rp_id = $model_rubriks_props->rp_id;
        $sort_sql = self::getSortSql($model_rubriks_props->sort_props_sprav);

        if ($parent_ps_id <= 0)
        {
            $props_sprav_records = self::model()->findAll(
                array(
                    'select'=>'*',
                    'condition'=>'rp_id = :rp_id AND value LIKE :search_str',
                    'params'=>array(':rp_id'=>$rp_id, ':search_str'=>'%'.$search_str.'%'),
                    'order'=>$sort_sql,
                    //'limit'=>'10'
                )
            );
            //deb::dump($props_spav_records);
        }
        else
        {
            $prop = PropsSprav::model()->findByPk($parent_ps_id);
            $props_sprav_records = $prop->childs(array(
                    'condition'=>'rp_id = :rp_id AND value LIKE :search_str',
                    'order'=>self::getOrderStr($model_rubriks_props),
                    'params'=>array(':rp_id'=>$rp_id, ':search_str'=>'%'.$search_str.'%')
                     )
                     );
        }

        return $props_sprav_records;

    }

    public static function getPropsListSelector($model_rubriks_props, $prop_types_params_row, $parent_ps_id)
    {
        $rp_id = $model_rubriks_props->rp_id;
        $sort_sql = self::getSortSql($model_rubriks_props->sort_props_sprav);

        if ($parent_ps_id <= 0)
        {
            if($model_rubriks_props->parent_id == 0)
            {
                $props_sprav_records = self::model()->findAll(
                    array(
                        'select'=>'*',
                        'condition'=>'rp_id = :rp_id AND selector = :selector',
                        'params'=>array(':rp_id'=>$rp_id, ':selector'=>$prop_types_params_row->selector),
                        'order'=>$sort_sql,
                        //'limit'=>'10'
                    )
                );
            }
            else
            {
                $props_sprav_records = null;
            }

        }
        else
        {

            $prop = PropsSprav::model()->findByPk($parent_ps_id);
            //deb::dump($model_rubriks_props);

            $props_sprav_records = $prop->childs(array(
                'condition'=>'rp_id = :rp_id',
                'order'=>self::getOrderStr($model_rubriks_props),
                'params'=>array(':rp_id'=>$rp_id)
            ));
        }

        return $props_sprav_records;

    }

    public static function getPropsListListitem($model_rubriks_props, $prop_types_params_row, $parent_ps_id)
    {
        $rp_id = $model_rubriks_props->rp_id;
        $sort_sql = self::getSortSql($model_rubriks_props->sort_props_sprav);

        if ($parent_ps_id <= 0)
        {
            if($model_rubriks_props->parent_id == 0)
            {
                $props_sprav_records = self::model()->findAll(
                    array(
                        'select'=>'*',
                        'condition'=>'rp_id = :rp_id AND selector = :selector',
                        'params'=>array(':rp_id'=>$rp_id, ':selector'=>$prop_types_params_row->selector),
                        'order'=>$sort_sql,
                        //'limit'=>'10'
                    )
                );

            }
            else
            {
                $props_sprav_records = null;
            }
        }
        else
        {

            $prop = PropsSprav::model()->findByPk($parent_ps_id);

            $props_sprav_records = $prop->childs(array(
                'condition'=>'rp_id = :rp_id',
                'order'=>self::getOrderStr($model_rubriks_props),
                'params'=>array(':rp_id'=>$rp_id)));

            //deb::dump($prop);
            //deb::dump($props_sprav_records);

            /*
            $props_sprav_records = $prop->childs(array('condition'=>'value LIKE :search_str',
                    'params'=>array(':search_str'=>'%'.$search_str.'%')
                )
            );
            */
        }

        return $props_sprav_records;

    }


    // Получение данных по массиву кодов справочника
    public static function getDataByIds($props_ids)
    {
        $props_data = array();

        if(count($props_ids) > 0)
        {
            $props_str = implode(", ", $props_ids);
            $props = self::model()->findAll(array(
                'select'=>'*',
                'condition'=>'ps_id IN ('.$props_str.')'
            ));

            foreach ($props as $pkey=>$pval)
            {
                $props_data[$pval->ps_id] = $pval;
            }
        }

        return $props_data;
    }


    // Формирование подстроки ORDER для SQL запроса
    public static function getOrderStr($model_rubriks_props)
    {
        if($model_rubriks_props->sort_props_sprav == 'sort_number')
        {
            $order = 'sort_number ASC';
        }
        else
        {
            $order = 'value '.$model_rubriks_props->sort_props_sprav;

            switch($model_rubriks_props->ptype)
            {
                case "int":
                    $order = 'CAST(value as SIGNED) '.$model_rubriks_props->sort_props_sprav;
                break;

                case "float":
                    $order = 'CAST(value*1000 as SIGNED) '.$model_rubriks_props->sort_props_sprav;
                break;

                case "string":
                    $order = 'value '.$model_rubriks_props->sort_props_sprav;
                break;
            }
        }

        return $order;
    }



    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('rp_id, type_id, selector, value', 'required'),
			array('rp_id, sort_number', 'numerical', 'integerOnly'=>true),
			array('selector, value, transname', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ps_id, selector, value', 'safe', 'on'=>'search'),
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
            'props_relations'=>array(self::HAS_MANY,'PropsRelations','parent_ps_id','joinType'=>'INNER JOIN'),
            'childs'=>array(self::HAS_MANY,'PropsSprav',array('child_ps_id'=>'ps_id'),'through'=>'props_relations','joinType'=>'INNER JOIN'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ps_id' => 'Ps',
			'prop_id' => 'Prop',
			'selector' => 'Selector',
			'value' => 'Value',
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

		$criteria->compare('ps_id',$this->ps_id);
		$criteria->compare('prop_id',$this->prop_id);
		$criteria->compare('selector',$this->selector,true);
		$criteria->compare('value',$this->value,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PropsSprav the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
