<?php
/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 05.07.15
 * Time: 19:57
 */

// Для разных вспомогательных действий

class Supporter {

    public function MakeTranslitAll()
    {
    /*
        $countries = Countries::model()->findAll();
        foreach ($countries as $ckey=>$cval)
        {
            $this->MakeTranslitForGeoplace('countries', $cval->name, $cval->c_id);
        }
    */
    /*
        $regions = Regions::model()->findAll();
        foreach ($regions as $ckey=>$cval)
        {
            $this->MakeTranslitForGeoplace('regions', $cval->name, $cval->reg_id);
        }
    */
    /*
        $towns = Towns::model()->findAll();
        foreach ($towns as $ckey=>$cval)
        {
            $this->MakeTranslitForGeoplace('towns', $cval->name, $cval->t_id);
        }
    */
    /*
        $supporter = new Supporter();
        $props = PropsSprav::model()->findAll();
        foreach ($props as $ckey=>$cval)
        {
            $cval->transname = $supporter->TranslitForUrl($cval->value);
            $cval->save();
        }
    */

    }

    // Генерация и вставка транслитерированноего названия страны, города или региона.
    // поле transname должно быть уникальным среди записей всех трех таблиц
    // (это надо для правильной маршрутизации урлов)
    // $pk - primary key
    public function MakeTranslitForGeoplace($table, $rusname, $pk)
    {
        $transname = $this->TranslitForUrl($rusname);

        $row_country = Countries::model()->findByAttributes(array('transname'=>$transname),
                        array('condition'=>'c_id <> '.$pk));
        $row_region = Regions::model()->findByAttributes(array('transname'=>$transname),
                        array('condition'=>'reg_id <> '.$pk));
        $row_town = Towns::model()->findByAttributes(array('transname'=>$transname),
                        array('condition'=>'t_id <> '.$pk));

        // Если в какой-то из таблиц есть такое транслит-название
        if($row_country || $row_region || $row_town)
        {
            if($pk > 0)
            {
                switch($table)
                {
                    case "countries":
                        $model_country = Countries::model()->findByPk($pk);
                        $model_country->transname = $transname."_country".$model_country->c_id;
                        $model_country->save();
                    break;

                    case "regions":
                        $model_region = Regions::model()->findByPk($pk);
//                    deb::dump($model_region->transname." - ".$transname);
                        $model_region->transname = $transname."_region".$model_region->c_id;
                        $model_region->save();
                    break;

                    case "towns":
                        $model_town = Towns::model()->findByPk($pk);
                    //deb::dump($model_town->transname." - ".$transname."<br>");
                        $model_town->transname = $transname."_town".$model_town->t_id;
                        $model_town->save();
                    break;
                }
            }
        }
        else
        {
            // если нет такого транслит-названия ни в одной из таблиц
            if($pk > 0)
            {

                switch($table)
                {
                    case "countries":
                        $model_country = Countries::model()->findByPk($pk);
                        $model_country->transname = $transname;
                        $model_country->save();
                    break;

                    case "regions":
                        $model_region = Regions::model()->findByPk($pk);
                        //deb::dump($model_region);
                        $model_region->transname = $transname;
                        $model_region->save();
                    break;

                    case "towns":
                        $model_town = Towns::model()->findByPk($pk);
//                    deb::dump($model_town->transname." - ".$transname."<br>");
                        $model_town->transname = $transname;
                        $model_town->save();
                    break;
                }
            }
        }

    }


    public function TranslitForUrl($st)
    {
       $table = array(
                "а"=>"a",
                "б"=>"b",
                "в"=>"v",
                "г"=>"g",
                "д"=>"d",
                "е"=>"e",
                "з"=>"z",
                "и"=>"i",
                "й"=>"j",
                "к"=>"k",
                "л"=>"l",
                "м"=>"m",
                "н"=>"n",
                "о"=>"o",
                "п"=>"p",
                "р"=>"r",
                "с"=>"s",
                "т"=>"t",
                "у"=>"u",
                "ф"=>"f",
                "х"=>"h",
                "ц"=>"c",
                "ы"=>"y",
                " "=>"_",

                "А"=>"A",
                "Б"=>"B",
                "В"=>"V",
                "Г"=>"G",
                "Д"=>"D",
                "Е"=>"E",
                "З"=>"Z",
                "И"=>"I",
                "Й"=>"J",
                "К"=>"K",
                "Л"=>"L",
                "М"=>"M",
                "Н"=>"N",
                "О"=>"O",
                "П"=>"P",
                "Р"=>"R",
                "С"=>"S",
                "Т"=>"T",
                "У"=>"U",
                "Ф"=>"F",
                "Х"=>"H",
                "Ц"=>"C",
                "Ы"=>"Y",

                "ё"=>"yo", "ж"=>"zh", "ч"=>"ch", "ш"=>"sh",
                "щ"=>"shh", "ъ"=>"", "ь"=>"", "э"=>"je", "ю"=>"yu", "я"=>"ya",
                "Ё"=>"yo", "Ж"=>"zh", "Ч"=>"ch", "Ш"=>"sh",
                "Щ"=>"shh","Ъ"=>"", "Ь"=>"", "Э"=>"je", "Ю"=>"yu", "Я"=>"ya",
                "ї"=>"i", "Ї"=>"yi", "є"=>"ie", "Є"=>"ye",
                ","=>"_", "/"=>"_", "("=>"_", ")"=>"_", "["=>"_", "]"=>"_",
                "/"=>"_", "-"=>"_", "—"=>"_"

            );


        $st = str_replace(
            array_keys($table),
            array_values($table), $st);
        $st = str_replace('___', '_', $st);
        $st = str_replace('__', '_', $st);
        $st = mb_strtolower($st, Yii::app()->charset);


        return $st;

    }


} 