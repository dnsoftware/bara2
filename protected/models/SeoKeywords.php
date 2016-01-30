<?php

/**
 * This is the model class for table "{{seo_keywords}}".
 *
 * The followings are the available columns in table '{{seo_keywords}}':
 * @property integer $k_id
 * @property string $keyword
 * @property integer $r_id
 * @property integer $count
 */
class SeoKeywords extends CActiveRecord
{
    public static $position = array(
        '1'=>'Похожие',
        '2'=>'baraholka.ru'
    );


    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('keyword, r_id, position, prop_count', 'required'),
            array('r_id', 'numerical', 'integerOnly'=>true),
            array('keyword', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('k_id, keyword, r_id', 'safe', 'on'=>'search'),
        );
    }


    // Данные для формирование сигнатуры из кодов и имен
    public static function MakeSignature($k_id, $r_id)
    {
        //$keywordrow = SeoKeywords::model()->findByPk($k_id);

        $keyprops = SeoKeywordsProps::model()->findAllByAttributes(array('k_id'=>$k_id));
        $keyprops_array = array();
        foreach($keyprops as $key=>$val)
        {
            $keyprops_array[$val['rp_id']] = $val;
        }

        $props_relate = RubriksProps::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id='.$r_id,
            'order'=>'t.hierarhy_tag DESC, t.hierarhy_level ASC, t.display_sort, t.rp_id'
        ));

        $props_array = array();
        $props_names_array = array();
        foreach($props_relate as $pkey=>$pval)
        {
            if($pval->vibor_type == 'selector' ||
                $pval->vibor_type == 'listitem' ||
                $pval->vibor_type == 'autoload' ||
                $pval->vibor_type == 'autoload_with_listitem' )
            {
                //$props_array[$pval->rp_id] = $pval->rp_id;
                if(isset($keyprops_array[$pval->rp_id]))
                {
                    $props_array[$pval->rp_id] = $pval->rp_id;
                    $props_names_array[$pval->rp_id] = $pval->name;
                }
            }
        }


        $ret = array();
        $ret['rp_ids'] = $props_array;
        $ret['rp_names'] = $props_names_array;

        return $ret;
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{seo_keywords}}';
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
			'k_id' => 'K',
			'keyword' => 'Keyword',
			'r_id' => 'R',
			'count' => 'Count',
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

		$criteria->compare('k_id',$this->k_id);
		$criteria->compare('keyword',$this->keyword,true);
		$criteria->compare('r_id',$this->r_id);
		$criteria->compare('count',$this->count);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SeoKeywords the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
