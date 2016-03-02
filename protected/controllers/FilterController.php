<?php

class FilterController extends Controller
{
    public $breadcrumbs = array();  // Хлебные крошки
                                    // array('Номер по порядку'=>array('transname'=>'часть пути', 'type'=>'страна, регион, город, рубрика, подрубр., св-во', 'url'=>'', 'name'=>'Название ссылки, анкор'))


    public function actionSphinx()
    {
        $connection = Yii::app()->db_sphinx;

        $str = Notice::SphinxEscapeString ('ЖК "Высокий Берег" - уютный жилой комплекс нового формата, с благоустроенной территорией и сетями инженерной инфраструктуры. Предлагаются 1-комнатные квартиры современной планировки. Площадь 42/20/10 кв. м. Квартиры в ЖК «Высокий Берег» отличаются оптимальными планировочными решениями. В каждой квартире предусматривается остекление окон двухкамерными стеклопакетами. Высота потолков согласно проекту - 2,8 м. Все квартиры оборудованы застекленными лоджиями. Выполняется устройство внутриквартирных перегородок. Производится установка счетчиков расхода горячей и холодной воды. Комплекс расположен в городе Ногинске (35 км от МКАД) в районе с развитой инфраструктурой, на берегу реки Клязьма, вблизи домов 226-230 на одной из главных улиц города - Третьего Интернационала. Первая очередь строительства - возведение 17-этажного 2-подъездного дома №1 из двух секций на 128 квартир с встроенным детским садом на 1 этаже. ФЗ-214. Страхование ответственности Застройщика. Ипотека Сбербанк РФ. Сдача 1 квартал 2016 года.<br>Цена (руб.): 2300 000 руб. <br>На период с 01 июня по 01 сентября 2015 года действуют выгодные условия на приобретение квартиры: В случае 100% оплаты, включая ипотеку и материнский капитал, каждому покупателю квартиры гарантирована скидка 3%, при покупке 2-х и более квартир – 4,5%. В рамках акции покупатель может воспользоваться рассрочкой на выгодных условиях: рассрочка от 20% на 1-й взнос, остаток на 6 месяцев без удорожания. Спешите приобрести квартиру на выгодных для Вас условиях! Официальный брокер по продаже недвижимости: МИЭЛЬ Офис «В Ногинске» Застройщик: ООО «Контакт Строй». Срок сдачи 1 очереди: I квартал 2016 года<br>Цена: 1712002 руб.');

        //$str = "сер";
        /*
        $sql = "SELECT WEIGHT() weight, * FROM rt_adverts
                WHERE MATCH('@notice_text \"".$str."\"/0.95')
                LIMIT 100";
        */

        /*
        $sql = "SELECT WEIGHT() weight, * FROM rt_search_stat
                WHERE MATCH('кварт')
                ORDER BY count DESC, weight DESC";
        */

        //deb::dump(Notice::GetSphinxSearchStat('кварт', 0.9, $ids_array));

        //$sql = "CALL KEYWORDS('грози южному', 'rt_search_stat') ";
        $sql = "CALL SNIPPETS(('this is my document text', 'text this botva'), 'rt_search_stat', 'this', 1 AS query_mode)";

        $command = $connection->createCommand($sql);
        $row_notices = $command->queryAll();
deb::dump($row_notices);

    }


	public function actionIndex()
	{
        //deb::dump($_GET);
        $query_delta = 0;

        $display_titul_tag = 0; // Признак того что мы на "титуле" региона или на главной

        $connection=Yii::app()->db;

        $cookie['mytown'] = Yii::app()->request->cookies->contains('geo_mytown') ?
            Yii::app()->request->cookies['geo_mytown']->value : 0;
        $cookie['myregion'] = Yii::app()->request->cookies->contains('geo_myregion') ?
            Yii::app()->request->cookies['geo_myregion']->value : 0;
        $cookie['mycountry'] = Yii::app()->request->cookies->contains('geo_mycountry') ?
            Yii::app()->request->cookies['geo_mycountry']->value : 0;

        $parts = array();
        if(isset($_GET['mesto_id'])) // $_POST['region_id'] может содержать город, страну или регион
        {
            if($_GET['mesto_id'] == '0')
            {
                $parts = 0;
            }
            else
            {
                $parts = explode("_", $_GET['mesto_id']);
            }
        }

        $mesto_isset_tag = 0;
        $mselector = '';
        $m_id = 0;
        if(count($parts) == 2 && intval($parts[1]) > 0)
        {
            $mesto_isset_tag = 1;
            $mselector = $parts[0];
            $m_id = intval($parts[1]);
        }

        // страница
        $page = 1;
        if(isset($_GET['page']))
        {
            $page = intval($_GET['page']);
            if($page <= 0)
            {
                $page = 1;
            }
        }

        // Тег сортировки
        $sort_select_mode = 0;
//deb::dump($_SESSION['sort_select_mode']);
        if($_SESSION['sort_select_mode'] == 1 && trim($_GET['params']['q']) != '')
        {
            $sort_select_mode = 1;
        }

        if(trim($_GET['params']['q']) == '')
        {
            $sort_select_mode = 0;
            $_SESSION['sort_select_mode'] = 0;
        }
//deb::dump($sort_select_mode);


        if(isset($_GET['params']['s']) && trim($_GET['params']['s']) == 'rl'
                    && trim($_GET['params']['q']) == '')
        {
            $_GET['params']['s'] = '';
        }


        if(trim($_GET['params']['q']) != '' && $sort_select_mode == 0)
        {
            $_GET['params']['s'] = 'rl';
        }

        // Сортировка
        $order_sql = " n.date_add DESC ";
        if(isset($_GET['params']['s']))
        {
            switch($_GET['params']['s'])
            {
                case "":
                    $order_sql = " n.date_add DESC ";
                break;

                case "dr":
                    $order_sql = " rubcost DESC ";
                break;

                case "ds":
                    $order_sql = " rubcost ASC ";
                break;

                case "rl":
                    $order_sql = " n.date_add DESC ";
                break;
            }
        }




        // Показ архивных
        $expire_sql = " date_expire > '".time()."' AND ";
        if(isset($_GET['viewarchive']) && $_GET['viewarchive'] == 1)
        {
            $expire_sql = " ";
        }
        // Конец Показ архивных

        // Куки местоположения
        if(isset($_GET['filter_submit_button']) && $_GET['mesto_id'] != 'none_0')
        {
            //deb::dump($_COOKIE);
            self::unsetRegionCookies();
            self::SetGeolocatorCookie('geo_mytown_handchange_tag', 1, 86400*30);

            if($mesto_isset_tag && $mselector == 't')
            {
                $town = Towns::model()->findByPk($m_id);
                $region = Regions::model()->findByPk($town->reg_id);
                $country = Countries::model()->findByPk($town->c_id);

                self::SetGeolocatorCookie('geo_mytown', $town->t_id, 86400*30);
                self::SetGeolocatorCookie('geo_mytown_name', $town->name, 86400*30);

                self::SetGeolocatorCookie('geo_myregion', $region->reg_id, 86400*30);
                self::SetGeolocatorCookie('geo_myregion_name', $region->name, 86400*30);

                self::SetGeolocatorCookie('geo_mycountry', $country->c_id, 86400*30);
                self::SetGeolocatorCookie('geo_mycountry_name', $country->name, 86400*30);
            }

            if($mesto_isset_tag && $mselector == 'reg')
            {
                $region = Regions::model()->findByPk($m_id);
                $country = Countries::model()->findByPk($region->c_id);

                self::SetGeolocatorCookie('geo_myregion', $region->reg_id, 86400*30);
                self::SetGeolocatorCookie('geo_myregion_name', $region->name, 86400*30);

                self::SetGeolocatorCookie('geo_mycountry', $country->c_id, 86400*30);
                self::SetGeolocatorCookie('geo_mycountry_name', $country->name, 86400*30);
            }

            if($mesto_isset_tag && $mselector == 'c')
            {
                $country = Countries::model()->findByPk($m_id);

                self::SetGeolocatorCookie('geo_mycountry', $country->c_id, 86400*30);
                self::SetGeolocatorCookie('geo_mycountry_name', $country->name, 86400*30);

            }

        }


        // Местоположение
        $mesto_sql = "1 ";
        $mesto_use_index_prefix = '';
        $urlparts = explode("/", parse_url($_SERVER['REQUEST_URI'])['path']);
        if($mesto_isset_tag && $mselector == 't')
        {
            $mesto_sql = " n.t_id = ".intval($m_id);
            $mesto_use_index_prefix = 't_id';
        }
        else
        if($mesto_isset_tag && $mselector == 'reg')
        {
            $mesto_sql = " n.reg_id = ".intval($m_id);
            $mesto_use_index_prefix = 'reg_id';
        }
        else
        if($mesto_isset_tag && $mselector == 'c')
        {
            $mesto_sql = " n.c_id = ".intval($m_id);
            $mesto_use_index_prefix = 'c_id';
        }
        else
        if(Yii::app()->request->cookies['region_confirm_tag']->value != 0
                    && (isset($urlparts[1]) && $urlparts[1] != 'all'))
        {
            if($cookie['mytown'] > 0)
            {
                $mesto_sql = " n.t_id = ".intval($cookie['mytown']);
                $mesto_use_index_prefix = 't_id';
                $mesto_isset_tag = 1;
                $mselector = 't';
                $m_id = $cookie['mytown'];
            }
            else
            if($cookie['myregion'] > 0)
            {
                $mesto_sql = " n.reg_id = ".intval($cookie['myregion']);
                $mesto_use_index_prefix = 'reg_id';
                $mesto_isset_tag = 1;
                $mselector = 'reg';
                $m_id = $cookie['myregion'];
            }
            else
            if($cookie['mycountry'] > 0)
            {
                $mesto_sql = " n.c_id = ".intval($cookie['mycountry']);
                $mesto_use_index_prefix = 'c_id';
                $mesto_isset_tag = 1;
                $mselector = 'c';
                $m_id = $cookie['mycountry'];
            }
        }



        //Рубрика
        $rubrik_sql = " 1 ";
        if(!isset($_GET['parent_r_id']) && isset($_GET['mainblock']['r_id']) && $_GET['mainblock']['r_id'] != '')
        {
              $rubrik_sql = " r_id = ".intval($_GET['mainblock']['r_id']);
        }
        else if(isset($_GET['parent_r_id']))
        {
            $subrubs = Rubriks::model()->findAll(array('condition'=>'parent_id = '.intval($_GET['parent_r_id'])));
            $subrubs_ids = array();
            if($subrubs)
            {
                foreach($subrubs as $key=>$val)
                {
                    $subrubs_ids[] = $val->r_id;
                }
                $rubrik_sql = " r_id IN (". implode(", ", $subrubs_ids).") ";
            }
            //deb::dump($rubrik_sql);
        }

        $rubriks_props = RubriksProps::model()->findAll(array(
            'select'=>'*',
            'condition'=>$rubrik_sql." AND use_in_filter = 1 ",
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
            )
        );

        $rp_ids = array();
        $rubriks_props_poryadok_array = array();
        $rubriks_props_poryadok_by_selector_array = array();
        $rubriks_poryadok_props_array = array();
        $i=2;
        $pubriks_props_array = array();
        $pubriks_props_by_selector_array = array();
        foreach ($rubriks_props as $rkey=>$rval)
        {
            $rubriks_props_poryadok_array[$rval->rp_id] = $i++;
            $rubriks_props_poryadok_by_selector_array[$rval->selector] = $rubriks_props_poryadok_array[$rval->rp_id];
            $rubriks_poryadok_props_array[$i-1] = $rval->rp_id;
            $rp_ids[$rval->rp_id] = $rval->rp_id;
            $pubriks_props_array[$rval->rp_id] = $rval;
            $pubriks_props_by_selector_array[$rval->selector] = $rval;
        }

        //deb::dump($rubriks_poryadok_props_array);
//deb::dump($pubriks_props_array);
//deb::dump($_GET['prop']);
//deb::dump($_GET['addfield']);
        //deb::dump($pubriks_props_array);

        // Переводим переменные из $_GET['prop'] в $_GET['addfield']
        $gpkey_offset = 0;  // $gpkey_offset - изза того что могут быть пропуски $gpkey может неправильно
                            // позиционироваться, поэтому фиксируется смежение дальше по массиву
        $first_index_tag = 1;
        $parent_ps_ids = array();
        if(isset($_GET['prop']) && count($_GET['prop']) > 0)
        {
            foreach($_GET['prop'] as $gpkey=>$gpval)
            {
                while(isset($rubriks_poryadok_props_array[$gpkey + $gpkey_offset]))
                {
                    $new_gpkey = $gpkey + $gpkey_offset;

                    $prop_row = null;
                    if($ps_rows = PropsSprav::model()->findAllByAttributes(array(
                        'rp_id'=>$rubriks_poryadok_props_array[$new_gpkey], 'transname'=>$gpval)
                    ))
                    {
                        foreach($ps_rows as $prkey=>$prval)
                        {
                            if($psparents = PropsRelations::model()->findAllByAttributes(array(
                                'child_ps_id'=>$prval->ps_id)
                            ))
                            {
                                $break_tag = 0;
                                foreach($psparents as $pr2key=>$psparent)
                                {
                                    if(isset($parent_ps_ids[$psparent->parent_ps_id])
                                        && in_array($prval->ps_id, $parent_ps_ids[$psparent->parent_ps_id]))
                                    {
                                        $prop_row = $prval;
                                        $break_tag = 1;
                                        break;
                                    }
                                }

                                if($break_tag == 1)
                                {
                                    break;
                                }
                            }
                            else
                            {
                                $prop_row = $prval;
                                break;
                            }

                        }

                        if($prop_row)
                        {
                            $childs_array = array();
                            if($childs = PropsRelations::model()->findAllByAttributes(
                                array('parent_ps_id'=>$prop_row->ps_id)
                            ))
                            {
                                foreach($childs as $ckey=>$cval)
                                {
                                    $childs_array[] = $cval->child_ps_id;
                                }
                            }
                            $parent_ps_ids[$prop_row->ps_id] = $childs_array;

                            //deb::dump($prop_row);
                            $rp_selector = $pubriks_props_array[$rubriks_poryadok_props_array[$new_gpkey]]->selector;

                            if($pubriks_props_array[$rubriks_poryadok_props_array[$new_gpkey]]->filter_type == 'range')
                            {
                                $_GET['addfield'][$rp_selector]['from'] = $prop_row->ps_id;
                                $_GET['addfield'][$rp_selector]['to'] = $prop_row->ps_id;
                            }
                            else
                                if($pubriks_props_array[$rubriks_poryadok_props_array[$new_gpkey]]->filter_type == 'select_multi')
                                {
                                    $_GET['addfield'][$rp_selector][] = $prop_row->ps_id;
                                }
                                else
                                {
                                    $_GET['addfield'][$rp_selector] = $prop_row->ps_id;
//                            deb::dump($rp_selector);
                                }
                            break;
                        }

                        //deb::dump($prop_row);

                        break;
                    }
                    else
                    {
                        $gpkey_offset++;
                    }

                }
            }
        }
        // КОНЕЦ Переводим переменные из $_GET['prop'] в $_GET['addfield']



        // Переводим переменные из $_GET['addfield'] в $_GET['prop']
        // Только для тех кто участвует в иерархии
        if(isset($_GET['addfield']) && count($_GET['addfield']) > 0 )
        {
            foreach($_GET['addfield'] as $akey=>$aval)
            {
                if(is_array($aval) && count($aval) == 1)
                {
                    $aval = $aval[0];
                }

                if(!is_array($aval) && !isset($_GET['prop'][$rubriks_props_poryadok_by_selector_array[$akey]]))
                {
                    if($pubriks_props_by_selector_array[$akey]->hierarhy_tag)
                    {
                        $prop_row = PropsSprav::model()->findByPk($aval);
                        $_GET['prop'][$rubriks_props_poryadok_by_selector_array[$akey]] = $prop_row['transname'];
                    }
                }
            }
        }

        // КОНЕЦ Переводим переменные из $_GET['addfield'] в $_GET['prop']


        // Удаление из $_GET['addfield'] пустых диапазонов
        if(isset($_GET['addfield']) && count($_GET['addfield']) > 0 )
        {
            foreach($_GET['addfield'] as $gkey=>$gval)
            {
                if(is_array($gval) && isset($gval['from']) && $gval['from'] == '' && $gval['to'] == '')
                {
                    unset($_GET['addfield'][$gkey]);
                }
            }
        }


        // Для титулов-регионов кол-во объяв на странице другое, плюс добавлена вставка рубрик и текстовый блок
        if(intval($_GET['mainblock']['r_id']) == 0 && !isset($_GET['prop']) && !isset($_GET['addfield'])
            && !isset($_GET['page']))
        {
            $display_titul_tag = 1;
        }

//deb::dump(intval($_GET['mainblock']['r_id']));
//deb::dump($_GET['prop']);
//deb::dump($_GET['addfield']);


        $search_adverts = array();  // Найденные объявы
        $ps_ids_array = array(); // Список кодов св-в используемых в фильтре (для построения титула страницы)

        // Сложный поиск по свойствам
        if(isset($_GET['addfield']) && count($_GET['addfield']) > 0 )
        {
            $props_sprav = PropsSprav::model()->findAll(array('condition'=>'rp_id IN ('.implode(", ", $rp_ids).')'));
            $props_route_items = array();
            $props_route_items_by_id = array();
            foreach($props_sprav as $pkey=>$pval)
            {
                $props_route_items[$rubriks_props_poryadok_array[$pval->rp_id]][$pval->transname] = $pval;
                $props_route_items_by_id[$pval->ps_id] = $pval;
            }
//deb::dump($props_route_items);
            foreach($_GET['prop'] as $pkey=>$pval)
            {
                if(isset($props_route_items[$pkey][$pval]))
                {
                    $ps_id = $props_route_items[$pkey][$pval]->ps_id;
                    $ps_ids_array[$ps_id] = $ps_id;
                }
            }
            $current_ps_id = $ps_id;

//deb::dump($current_ps_id);
//deb::dump($_GET['prop']);
//deb::dump($props_sql_array);
            // Ищем объявы с совпадением значений всех указанных свойств
            //if(count($_GET['prop']) == count($props_sql_array))
            if(1)
            {
                $from_tables_array = array();
                $from_tables_sql = "";
                $where_n_array = array();
                $where_n = "";
                $where_filter_array = array();
                $where_filter_sql = "";

                /*
                for($i=1; $i<=$kol_props; $i++)
                {
                    $from_tables_array[] = $connection->tablePrefix . "notice_props n".$i;
                    $where_n_array[] = " AND n".$i.".n_id = n".($i+1).".n_id ";
                    $where_filter_array[] = "n".$i.".ps_id = ".$props_sql_array[$i-1]->ps_id;
                }
                */

                $i=0;
                foreach($_GET['addfield'] as $gkey=>$gval)
                {
                    $switch_rp_id = $pubriks_props_by_selector_array[$gkey]->rp_id;
                    $i++;

                    switch($pubriks_props_by_selector_array[$gkey]->filter_type)
                    {
                        case "select_one":
                            //deb::dump($pubriks_props_by_selector_array[$gkey]);
                            $from_tables_array[] = $connection->tablePrefix . "notice_props n".$i;
                            $where_n_array[] = " AND n".$i.".rp_id = ".$switch_rp_id;
                            $where_n_array[] = " AND n".$i.".n_id = n".($i+1).".n_id ";
                            $where_filter_array[] = "n".$i.".ps_id = ".intval($gval);
                        break;

                        case "select_multi":
                            // В ссылке вида http://baraholka2.dn/krasnodar/kvartiry/prodam/vtorichka
                            // $gval ("vtorichka") будет не массивом мультивыбора, а обычной переменной, поэтому
                            // приводим к массиву
                            $multi_array = array();
                            if(!is_array($gval))
                            {
                                $multi_array[] = $gval;
                            }
                            else
                            {
                                $multi_array = $gval;
                            }
                            //deb::dump($gkey);
                            //deb::dump($multi_array);

                            if(count($multi_array > 0))
                            {
                                foreach($multi_array as $g2key=>$g2val)
                                {
                                    $multi_array[$g2key] = intval($g2val);
                                }

                                $from_tables_array[] = $connection->tablePrefix . "notice_props n".$i;
                                $where_n_array[] = " AND n".$i.".rp_id = ".$switch_rp_id;
                                $where_n_array[] = " AND n".$i.".n_id = n".($i+1).".n_id ";
                                $where_filter_array[] = "n".$i.".ps_id IN (".implode(", ", $multi_array).")";
                            }
                        break;

                        case "range":
                            // В диапазоне не учтены зависимости, возможно это понадобится
                            if($pubriks_props_by_selector_array[$gkey]->vibor_type == 'string')
                            {
                                $from = $gval['from'];
                                $to = $gval['to'];

                                $from_tables_array[] = $connection->tablePrefix . "notice_props n".$i;
                                $where_n_array[] = " AND n".$i.".rp_id = ".$switch_rp_id;
                                $where_n_array[] = " AND  n".$i.".n_id = n".($i+1).".n_id ";

                                if(isset($from) && $from != ''
                                    && isset($to) && $to != '' )
                                {
                                    $where_filter_array[] = "n".$i.".hand_input_value_digit >= ".$from . "
                                                            AND n".$i.".hand_input_value_digit <= ".$to."";
                                }
                                else
                                if( (!isset($from) || $from != '')
                                    && isset($to) && $to != '' )
                                {
                                    $where_filter_array[] = "n".$i.".hand_input_value_digit <= ".$to;
                                }
                                else
                                if( isset($from) && $from != ''
                                    && (!isset($to) || $to != '') )
                                {
                                    $where_filter_array[] = "n".$i.".hand_input_value_digit >= ".$from;
                                }
                            }
                            else
                            {
                                $from = PropsSprav::model()->findByPk(intval($gval['from']));
                                $to = PropsSprav::model()->findByPk(intval($gval['to']));

                                $from_tables_array[] = $connection->tablePrefix . "notice_props n".$i;
                                $from_tables_array[] = $connection->tablePrefix . "props_sprav ps".$i;
                                $where_n_array[] = " AND n".$i.".ps_id = ps".$i.".ps_id
                                                     AND n".$i.".rp_id = ".$switch_rp_id;
                                $where_n_array[] = " AND  n".$i.".n_id = n".($i+1).".n_id ";

                                if(isset($from) && $from->value != ''
                                    && isset($to) && $to->value != '' )
                                {
                                    $where_filter_array[] = "ps".$i.".value >= ".$from->value . "
                                                            AND ps".$i.".value <= ".$to->value;
                                }

                                if( (!isset($from) || $from->value != '')
                                        && isset($to) && $to->value != '' )
                                {
                                    $where_filter_array[] = " ps".$i.".value <= ".$to->value;
                                }

                                if( isset($from) && $from->value != ''
                                    && (!isset($to) || $to->value != '') )
                                {
                                    $where_filter_array[] = " ps".$i.".value >= ".$from->value;
                                }

                            }
/*
                            $from = PropsSprav::model()->findByPk(intval($_GET['addfield'][$parent_selector]['from']));
                            $to = PropsSprav::model()->findByPk(intval($_GET['addfield'][$parent_selector]['to']));
                            $sql = "SELECT *
                                    FROM ". $connection->tablePrefix . "props_sprav ps
                                    WHERE ps.rp_id = ".$rubriks_props_array[$parent_ps_id]->rp_id . "
                                        AND ps.value >= " . intval($from->value) . " AND ps.value <= " . intval($to->value);
*/

                        break;

                        case "range_polzun":
                            // В диапазоне не учтены зависимости, возможно это понадобится
                            if($pubriks_props_by_selector_array[$gkey]->vibor_type == 'string')
                            {
                                $from = $gval['from'];
                                $to = $gval['to'];

                                $from_tables_array[] = $connection->tablePrefix . "notice_props n".$i;
                                $where_n_array[] = " AND n".$i.".rp_id = ".$switch_rp_id;
                                $where_n_array[] = " AND  n".$i.".n_id = n".($i+1).".n_id ";

                                if(isset($from) && $from != ''
                                    && isset($to) && $to != '' )
                                {
                                    $where_filter_array[] = "n".$i.".hand_input_value_digit >= ".$from . "
                                                            AND n".$i.".hand_input_value_digit <= ".$to."";
                                }
                                else
                                    if( (!isset($from) || $from != '')
                                        && isset($to) && $to != '' )
                                    {
                                        $where_filter_array[] = "n".$i.".hand_input_value_digit <= ".$to;
                                    }
                                    else
                                        if( isset($from) && $from != ''
                                            && (!isset($to) || $to != '') )
                                        {
                                            $where_filter_array[] = "n".$i.".hand_input_value_digit >= ".$from;
                                        }
                            }
                            else
                            {
                                $from = PropsSprav::model()->findByPk(intval($gval['from']));
                                $to = PropsSprav::model()->findByPk(intval($gval['to']));

                                $from_tables_array[] = $connection->tablePrefix . "notice_props n".$i;
                                $from_tables_array[] = $connection->tablePrefix . "props_sprav ps".$i;
                                $where_n_array[] = " AND n".$i.".ps_id = ps".$i.".ps_id
                                                     AND n".$i.".rp_id = ".$switch_rp_id;
                                $where_n_array[] = " AND  n".$i.".n_id = n".($i+1).".n_id ";

                                if(isset($from) && $from->value != ''
                                    && isset($to) && $to->value != '' )
                                {
                                    $where_filter_array[] = "ps".$i.".value >= ".$from->value . "
                                                            AND ps".$i.".value <= ".$to->value;
                                }

                                if( (!isset($from) || $from->value != '')
                                    && isset($to) && $to->value != '' )
                                {
                                    $where_filter_array[] = " ps".$i.".value <= ".$to->value;
                                }

                                if( isset($from) && $from->value != ''
                                    && (!isset($to) || $to->value != '') )
                                {
                                    $where_filter_array[] = " ps".$i.".value >= ".$from->value;
                                }

                            }
                        break;

                        case "checkbox_list":
                            //deb::dump($gval);
                            $temp = array();
                            foreach($gval as $g3key=>$g3val)
                            {
                                $from_tables_array[] = $connection->tablePrefix . "notice_props n".$i;
                                $where_n_array[] = " AND n".$i.".rp_id = ".$switch_rp_id;
                                $where_n_array[] = " AND n".$i.".n_id = n".($i+1).".n_id ";
                                $where_filter_array[] = "n".$i.".ps_id = ".$g3val;
                                $i++;
                            }
                            $i--;

                        break;

                        case "is_prop":
                            $from_tables_array[] = $connection->tablePrefix . "notice_props n".$i;
                            $where_n_array[] = " AND n".$i.".rp_id = ".$switch_rp_id;
                            $where_n_array[] = " AND n".$i.".n_id = n".($i+1).".n_id ";
                            if($pubriks_props_by_selector_array[$gkey]->vibor_type == 'string')
                            {
                                $where_filter_array[] = "n".$i.".hand_input_value != '' ";
                            }
                            else
                            {
                                $where_filter_array[] = "n".$i.".ps_id > 0 ";
                            }
                        break;

                    }

                }

//deb::dump($where_filter_array);
                $from_tables_sql = implode(", ", $from_tables_array);
                unset($where_n_array[count($where_n_array)-1]);
                $where_n = implode(" ", $where_n_array);
                $where_filter_sql = implode(" AND ", $where_filter_array);

                $rubrik_prop_sql = str_replace("r_id", "n.r_id", $rubrik_sql);

                // Выбираем какой индекс будем использовать
                $use_index_sql = Notice::GetUseIndexSql($expire_sql, $mesto_sql, $rubrik_sql, $mesto_use_index_prefix);

                $q_sql = " ";
                if(isset($_GET['params']['q']) && strlen(trim($_GET['params']['q'])) > 0)
                {
                    $q_sql = Notice::GetSphinxSearchAdvert($_GET['params']['q'], 0.9, $ids_array);
                    $use_index_sql = ' ';
                    if(count($ids_array) > 0 && isset($_GET['params']['s']) && $_GET['params']['s'] == 'rl')
                    {
                        $order_sql = " FIND_IN_SET(n.n_id, '".implode(",", $ids_array)."'), date_add DESC";
                    }
                }

                // Для подсчета количества
                $sql = "SELECT COUNT(DISTINCT n.n_id) cnt
                        FROM ". $connection->tablePrefix . "notice n ".$use_index_sql.",
                        ".$from_tables_sql.",
                        ". $connection->tablePrefix . "towns t
                        WHERE n.active_tag = 1 AND n.verify_tag = 1 AND n.deleted_tag = 0 AND $expire_sql
                        $mesto_sql AND $rubrik_prop_sql AND
                        $where_filter_sql
                        ".$where_n.$q_sql."
                        AND n1.n_id = n.n_id
                        AND n.t_id = t.t_id ";
                //deb::dump($sql);

                $command = $connection->cache(600)->createCommand($sql);

                $row_count = $command->queryAll();

                $offset = ($page-1)*Yii::app()->params['countonpage'];

                // Для отображаемой страницы
                $sql = "SELECT DISTINCT n.*, t.name town_name, t.transname town_transname,
                                        v.kurs*n.cost rubcost
                        FROM ". $connection->tablePrefix . "notice n ".$use_index_sql.",
                        ".$from_tables_sql.",
                        ". $connection->tablePrefix . "towns t,
                        ". $connection->tablePrefix . "valutes v
                        WHERE n.active_tag = 1 AND n.verify_tag = 1 AND n.deleted_tag = 0 AND $expire_sql
                        $mesto_sql AND $rubrik_prop_sql AND
                        $where_filter_sql
                        ".$where_n.$q_sql."
                        AND n1.n_id = n.n_id
                        AND n.t_id = t.t_id  AND n.cost_valuta = v.code
                        ORDER BY $order_sql
                        LIMIT $offset, ".Yii::app()->params['countonpage'];
//deb::dump($sql);
                $command = $connection->cache(600)->createCommand($sql);

                $row_notices = $command->queryAll();

//$row_count = $connection->createCommand('SELECT FOUND_ROWS() cnt')->queryAll();

                $kolpages = ceil($row_count[0]['cnt']/Yii::app()->params['countonpage']);

//deb::dump($rows_cnt);
//deb::dump($kolpages);

                if(count($row_notices) > 0)
                {
                    foreach($row_notices as $rnkey=>$rnval)
                    {
                        $search_adverts[$rnval['n_id']] = $rnval;
                    }
                }


                // Формирование данных для ссылок на подгруппы
                $rubrik_groups = array();

                $subprop_rp_id = $rubriks_poryadok_props_array[count($_GET['prop'])+2];
                $subprop_rp_row = RubriksProps::model()->findByPk($subprop_rp_id);
//deb::dump($subprop_rp_row);
                if(isset($subprop_rp_id) && $subprop_rp_row->hierarhy_tag)
                {
                    $subprops = PropsRelations::model()->findAll(array('condition'=>'parent_ps_id = '.$current_ps_id));
                    $sql = "SELECT nsub.ps_id, ps.value, ps.transname, count(nsub.ps_id) cnt
                            FROM ". $connection->tablePrefix . "notice n ".$use_index_sql.",
                            ".$from_tables_sql.",
                            ". $connection->tablePrefix . "notice_props nsub,
                            ". $connection->tablePrefix . "props_sprav ps
                            WHERE
                            $mesto_sql AND $rubrik_prop_sql AND
                            $where_filter_sql
                            ".$where_n.$q_sql."
                            AND n1.n_id = n.n_id
                            AND n.active_tag = 1 AND n.verify_tag = 1 AND n.deleted_tag = 0
                            AND $expire_sql n.n_id = nsub.n_id AND nsub.rp_id = ".$subprop_rp_id . "
                            AND nsub.ps_id = ps.ps_id
                            GROUP BY nsub.ps_id, ps.value, ps.transname ";
                    //deb::dump($sql);
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    while(($row = $dataReader->read())!==false)
                    {
                        //$row['path'] = Yii::app()->getRequest()->getPathInfo()."/".$row['transname'];
                        $row['name'] = $row['value'];
                        $rubrik_groups[] = $row;

                    }

                }

//deb::dump($rubrik_groups);

            }
            else    // Нет записей удовлетворяющих критерию
            {

            }

        }
        /****************************** ПРОСТОЙ ЗАПРОС БЕЗ СВОЙСТВ *******************************/
        // Если поиск только по местоположению/рубрике - простой запрос
        else
        {
            $use_index_sql = Notice::GetUseIndexSql($expire_sql, $mesto_sql, $rubrik_sql, $mesto_use_index_prefix);


            $q_sql = " ";
            if(isset($_GET['params']['q']) && strlen(trim($_GET['params']['q'])) > 0)
            {
                $q_sql = Notice::GetSphinxSearchAdvert($_GET['params']['q'], 0.9, $ids_array);
                $use_index_sql = ' ';
                if(count($ids_array) > 0 && isset($_GET['params']['s']) && $_GET['params']['s'] == 'rl')
                {
                    $order_sql = " FIND_IN_SET(n.n_id, '".implode(",", $ids_array)."'), date_add DESC";
                }
            }
//deb::dump($ids_list);
            $sql = "SELECT COUNT(*) cnt
                    FROM ". $connection->tablePrefix . "notice n ".$use_index_sql.",
                    ". $connection->tablePrefix . "towns t
                    WHERE n.active_tag = 1 AND n.verify_tag = 1 AND n.deleted_tag = 0 AND $expire_sql
                    $mesto_sql AND ".$rubrik_sql.$q_sql." AND n.t_id = t.t_id ";
//deb::dump($sql);

            $command = $connection->cache(600)->createCommand($sql);

            $row_count = $command->queryAll();

            $offset = ($page-1)*Yii::app()->params['countonpage'];
            $kolpages = ceil($row_count[0]['cnt']/Yii::app()->params['countonpage']);

            $sql = "SELECT n.*, t.name as town_name, t.transname as town_transname,
                            v.kurs*n.cost as rubcost
                    FROM ". $connection->tablePrefix . "notice n ".$use_index_sql.",
                    ". $connection->tablePrefix . "towns t,
                    ". $connection->tablePrefix . "valutes v
                    WHERE n.active_tag = 1 AND n.verify_tag = 1 AND n.deleted_tag = 0 AND $expire_sql
                    $mesto_sql AND ".$rubrik_sql.$q_sql." AND n.t_id = t.t_id AND n.cost_valuta = v.code
                    ORDER BY $order_sql
                    LIMIT ".$offset.", ".Yii::app()->params['countonpage'];
//deb::dump($sql);
            $command = $connection->cache(600)->createCommand($sql);

            $adverts = $command->queryAll();


//deb::dump($adverts);
            //$ddd = array();
            foreach ($adverts as $akey=>$aval)
            {
                //$ddd[] = $aval['n_id'];
                //deb::dump($aval->town['name']);
                //$search_adverts[$aval->n_id] = $aval->attributes;
                $search_adverts[$aval['n_id']] = $aval;
                $search_adverts[$aval['n_id']]['town_name'] = $aval['town_name'];
                $search_adverts[$aval['n_id']]['town_transname'] = $aval['town_transname'];
            }
//deb::dump($ddd);

            // Разбивка выбранного раздела на подгруппы
            // Если выбранный раздел является родительской рубрикой
            if(isset($_GET['parent_r_id']))
            {
                $rubrik_simple_sql = " r.r_id IN (". implode(", ", $subrubs_ids).") ";

                $sql = "SELECT r.name, r.transname, COUNT(n.n_id) cnt
                        FROM
                        ". $connection->tablePrefix . "notice n ".$use_index_sql.",
                        ". $connection->tablePrefix . "rubriks r
                        WHERE n.active_tag = 1 AND n.verify_tag = 1 AND n.deleted_tag = 0 AND $expire_sql
                        ".$mesto_sql." AND ".$rubrik_simple_sql.$q_sql."
                        AND r.parent_id <> 0 AND n.r_id = r.r_id
                        GROUP BY r.name, r.transname ";
//                deb::dump($sql);
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $rubrik_groups = array();
                while(($rowgroup = $dataReader->read())!==false)
                {
                    $curr_path_parts = explode("/", Yii::app()->getRequest()->getPathInfo());
                    //$rowgroup['path'] = $curr_path_parts[0]."/".$rowgroup['transname'];
                    $rubrik_groups[] = $rowgroup;
                }

                //deb::dump($rubrik_groups);
            }
            // Если выбранный раздел является подрубрикой
            else if (isset($_GET['mainblock']['r_id']) && $_GET['mainblock']['r_id'] != '' && !isset($_GET['mainblock']['parent_r_id']) )
            {

                if(count($rubriks_props) > 0 && $rubriks_props[0]['vibor_type'] != 'photoblock')
                {

                    $props_sprav = PropsSprav::model()->findAll(array('condition'=>'rp_id = '.$rubriks_props[0]->rp_id));

                    $props_groups = array();
                    $props_rows = array();
                    foreach($props_sprav as $pkey=>$pval)
                    {
                        $props_groups[] = $pval->ps_id;
                        $props_rows[$pval->ps_id] = $pval;
                    }
                    //deb::dump($q_sql);
                    $mesto_simple_sql = str_replace(" n.", " ", $mesto_sql);
                    $sql = "SELECT p.ps_id, count(p.ps_id) cnt
                        FROM
                        ". $connection->tablePrefix . "notice n ".$use_index_sql.",
                        ". $connection->tablePrefix . "rubriks r,
                        ". $connection->tablePrefix . "notice_props p
                        WHERE n.active_tag = 1 AND n.verify_tag = 1 AND n.deleted_tag = 0 AND $expire_sql
                        n.r_id = ".intval($_GET['mainblock']['r_id'])."
                        AND $mesto_simple_sql $q_sql
                        AND n.r_id = r.r_id AND n.n_id = p.n_id
                        AND p.ps_id IN (".implode(", ", $props_groups).")
                        GROUP BY p.ps_id ";
//deb::dump($sql);
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    $rubrik_groups = array();
                    while(($row = $dataReader->read())!==false)
                    {
                        //deb::dump($row);
                        $rubrik_groups[$row['ps_id']]['cnt'] = $row['cnt'];
                        $rubrik_groups[$row['ps_id']]['name'] = $props_rows[$row['ps_id']]->value;
                        $rubrik_groups[$row['ps_id']]['transname'] = $props_rows[$row['ps_id']]->transname;
                        //$rubrik_groups[$row['ps_id']]['path'] = Yii::app()->getRequest()->getPathInfo()."/".$props_rows[$row['ps_id']]->transname;

                    }
                }


                //deb::dump($rubrik_groups);


            }
            // Если выбранные раздел является местоположением (страна или регион или город)
            else
            {
                $sql = "SELECT r.name, r.transname, COUNT(n.n_id) cnt
                        FROM
                        ". $connection->tablePrefix . "notice n ".$use_index_sql.",
                        ". $connection->tablePrefix . "rubriks r,
                        ". $connection->tablePrefix . "rubriks r2
                        WHERE n.active_tag = 1 AND n.verify_tag = 1 AND n.deleted_tag = 0 AND $expire_sql
                        ".$mesto_sql.$q_sql." AND r.parent_id = 0 AND r2.parent_id = r.r_id
                        AND n.r_id = r2.r_id
                        GROUP BY r.name, r.transname ";
                //deb::dump($sql);
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $rubrik_groups = array();
                while(($rowgroup = $dataReader->read())!==false)
                {
                    //$rowgroup['path'] = Yii::app()->getRequest()->getPathInfo()."/".$rowgroup['transname'];
                    $rubrik_groups[] = $rowgroup;
                }
                //deb::dump($rubrik_groups);
            }



        }



        // Подготовка данных для отображения
        //// Шаблоны отображения из рубрик
        $shablons_display = Rubriks::GetShablonsDisplay();

        $rubriks_all_array = Rubriks::get_all_subrubs();

        $props_array = Notice::DisplayAdvertsList($search_adverts, $shablons_display, $rubriks_all_array);

//deb::dump(count($search_adverts));

        /**************************************************************************/
        // Формирование данных для фильтра по свойствам
        $ret = $this->MakeDataFilter();
        $props_sprav_sorted_array = $ret['props_sprav_sorted_array'];
        $rubriks_props_array = $ret['rubriks_props_array'];
        /*************** END формирование данных ********************************/

        $rub_array = Rubriks::get_rublist();

        // *************************** Формирование хлебных крошек

        //Местоположение
        $bread_number = 0;  // индекс в массиве $this->breadcrumbs
        $region_for_titul = '';
        $url_parts = explode("/", parse_url(Yii::app()->getRequest()->getUrl())['path']);
        if($mesto_isset_tag && $mselector == 't')
        {
            if(!isset($town))
            {
                $town = Towns::model()->findByPk($m_id);
            }

            $bread_number++;
            $this->breadcrumbs[$bread_number]['transname'] = $town->transname;
            $this->breadcrumbs[$bread_number]['type'] = 'town';
            $this->breadcrumbs[$bread_number]['name'] = $town->name . ": все объявления";
            $region_for_titul = $town->name;
        }
        else
        if($mesto_isset_tag && $mselector == 'reg')
        {
            if(!isset($region))
            {
                $region = Regions::model()->findByPk($m_id);
            }

            $bread_number++;
            $this->breadcrumbs[$bread_number]['transname'] = $region->transname;
            $this->breadcrumbs[$bread_number]['type'] = 'region';
            $this->breadcrumbs[$bread_number]['name'] = $region->name . ": все объявления";
            $region_for_titul = $region->vregione;
        }
        else
        if($mesto_isset_tag && $mselector == 'c')
        {
            if(!isset($country))
            {
                $country = Countries::model()->findByPk($m_id);
            }

            $bread_number++;
            $this->breadcrumbs[$bread_number]['transname'] = $country->transname;
            $this->breadcrumbs[$bread_number]['type'] = 'country';
            $this->breadcrumbs[$bread_number]['name'] = $country->name . ": все объявления";
            $region_for_titul = $country->vregione;
        }
        else
        if($mesto_isset_tag == 0 && $mselector == '' && isset($url_parts[1]) && $url_parts[1] == 'all')
        {
            $bread_number++;
            $this->breadcrumbs[$bread_number]['transname'] = 'all';
            $this->breadcrumbs[$bread_number]['type'] = 'country';
            $this->breadcrumbs[$bread_number]['name'] = "Все объявления";
            $region_for_titul = 'Все объявления';
        }

        //Рубрикация
        if(isset($_GET['mainblock']['r_id']) && ($bread_r_id = intval($_GET['mainblock']['r_id'])) > 0)
        {
            $bread_rubrik = Rubriks::model()->findByPk($bread_r_id);
            if($bread_rubrik->parent_id > 0)
            {
                $bread_parent_rubrik = Rubriks::model()->findByPk($bread_rubrik->parent_id);

                $bread_number++;
                $this->breadcrumbs[$bread_number]['transname'] = $bread_parent_rubrik->transname;
                $this->breadcrumbs[$bread_number]['type'] = 'rubrik';
                $this->breadcrumbs[$bread_number]['name'] = $bread_parent_rubrik->name;

                $bread_number++;
                $this->breadcrumbs[$bread_number]['transname'] = $bread_rubrik->transname;
                $this->breadcrumbs[$bread_number]['type'] = 'subrubrik';
                $this->breadcrumbs[$bread_number]['name'] = $bread_rubrik->name;
            }
            else
            {
                $bread_number++;
                $this->breadcrumbs[$bread_number]['transname'] = $bread_rubrik->transname;
                $this->breadcrumbs[$bread_number]['type'] = 'rubrik';
                $this->breadcrumbs[$bread_number]['name'] = $bread_rubrik->name;
            }

        }

//deb::dump($this->breadcrumbs);
//deb::dump($_GET['addfield']);
//deb::dump($rubriks_props_array);
//deb::dump($props_route_items);
//deb::dump($rubriks_props_poryadok_by_selector_array);
//deb::dump($pubriks_props_by_selector_array);
//deb::dump($props_route_items_by_id);

        if(isset($_GET['addfield']) && count($_GET['addfield']) > 0)
        {
            foreach($_GET['addfield'] as $pkey=>$pval)
            {
                if(is_array($pval))
                {
                    if(count($pval) == 1)
                    {
                        $pval = $pval[0];
                    }
                    else
                    {
                        break;
                    }
                }

                //$ankor = $pubriks_props_by_selector_array[$pkey]['name'];
                $ankor = $props_route_items_by_id[$pval]['value'];
                $transname = $props_route_items_by_id[$pval]['transname'];

                $bread_number++;
                $this->breadcrumbs[$bread_number]['transname'] = $transname;
                $this->breadcrumbs[$bread_number]['type'] = 'prop';
                $this->breadcrumbs[$bread_number]['name'] = $ankor;
            }
        }

//deb::dump($this->breadcrumbs);

        // *************************** КОНЕЦ Формирование хлебных крошек


        // Формирование титула
        $titul_array = $this->breadcrumbs;
        array_shift($titul_array);
        if($bread_rubrik->parent_id > 0)
        {
            array_shift($titul_array);
        }
        $titulpart = array();
        foreach($titul_array as $tkey=>$tval)
        {
            $titulpart[] = $tval['name'];
        }
        $deystvie = 'купить';

        if(count($ps_ids_array) > 0)
        {
            foreach($ps_ids_array as $pikey=>$pival)
            {
                foreach(PropsSprav::$anti_category_theme as $akey=>$aval)
                {
                    if(in_array($pikey, $aval))
                    {
                        $deystvie = $akey;
                        break;
                    }
                }
            }
        }

        $curr_url = Yii::app()->getRequest()->getUrl();
        $regstr = 'регионе';
        if(isset($region) || isset($country))
        {
            $regstr = '';
        }
        if(isset($town))
        {
            $regstr = 'г. ';
        }

        $h1_text = '';
        if($curr_url == '/' || (stripos($curr_url, '/index.php') !== false)
            || (stripos($curr_url, 'all') !== false) )
        {
            $this->pageTitle = 'Доска бесплатных объявлений от частных лиц - '.$_SERVER['HTTP_HOST'];
        }
        else
        if(count($titul_array) == 0 && $mesto_isset_tag > 0)
        {
            //deb::dump($region);
            $this->pageTitle = 'Доска бесплатных частных объявлений в '.$regstr.' '.$region_for_titul.' - '.$_SERVER['HTTP_HOST'];
            $h1_text = 'Доска бесплатных частных объявлений в '.$regstr.' '.$region_for_titul;
        }
        else
        {
            $this->pageTitle = implode(", ", $titulpart)  ." в ".$regstr.' '.$region_for_titul." - ". $deystvie. " на ".$_SERVER['HTTP_HOST'];
            $h1_text = implode(", ", $titulpart)  ." в ".$regstr.' '.$region_for_titul;
        }

        $this->pageTitle = preg_replace("|в[ ]+Украине|siU", "на Украине", $this->pageTitle);
        $h1_text = preg_replace("|в[ ]+Украине|siU", "на Украине", $h1_text);

        ////////// Конец формирования титула


        // Формирование данных для пагинатора
        $page_url = Yii::app()->getRequest()->getUrl();
//deb::dump($m_id);
        $allreg_prefix = "";
        if($m_id == 0 && ($page_url == '/' || preg_match('|index.php|siU', $page_url, $match)))
        {
            $page_url = '/all';
        }
        else
        if($m_id > 0 && ($page_url == '/' || preg_match('|index.php|siU', $page_url, $match)))
        {
            $mesto = '';
            if($mesto_isset_tag && $mselector == 't')
            {
                $town = Towns::model()->findByPk($m_id);
                $mesto = $town->transname;
            }

            if($mesto_isset_tag && $mselector == 'reg')
            {
                $region = Regions::model()->findByPk($m_id);
                $mesto = $region->transname;
            }

            if($mesto_isset_tag && $mselector == 'c')
            {
                $country = Countries::model()->findByPk($m_id);
                $mesto = $country->transname;
            }

            if($mesto != '')
            {
                if($page_url == '/')
                {
                    $page_url = '/'.$mesto;
                }
            }

        }


        if($page_url == '/all/')
        {
            $page_url = '/all';
        }


        // Переменная page с префиксом ? или & в зависимости от урла
        $page_substr = '&page';
        if(strpos($page_url, '?') === false || strpos($page_url, '/?page') !== false)
        {
            $page_substr = '/?page';
        }
//deb::dump($page_substr);

        // Урл без переменной page
        $page_url = preg_replace('|&page=\d+?|siU', '', $page_url);
        $page_url = preg_replace('|/\?page=\d+?|siU', '', $page_url);

        // Массив параметров пагинатора
        $paginator_params = array();
        $paginator_params['page_url'] = $page_url;
        $paginator_params['page_substr'] = $page_substr;
        $paginator_params['kolpages'] = $kolpages;
        $paginator_params['page'] = $page;
        $paginator_params['css_class'] = 'bpaginator';


        /********************** Статистика поиска ***************************/
if(0)
{
deb::dump($town);
deb::dump($region);
deb::dump($country);
}

        if(isset($_GET['params']['q']) && trim($_GET['params']['q']) != '')
        {
            $query = trim(mb_strtolower($_GET['params']['q']));
            $hash = md5($query);

            if(!$search_stat = SearchStat::model()->findByAttributes(array('hash'=>$hash)))
            {
                $search_stat = new SearchStat();
                $search_stat->query = $query;
                $search_stat->hash = $hash;
                $search_stat->count = 1;
            }
            else
            {
                $search_stat->count++;
                $search_log_old = SearchLog::model()->find(array(
                    'select'=>'*',
                    'condition'=>'ss_id = '.$search_stat->ss_id,
                    'order'=>'date_add DESC'
                ));
            }


            if(!isset($search_log_old)
                || (isset($search_log_old) && $search_log_old->ip != $_SERVER['REMOTE_ADDR'])
                || (isset($search_log_old) && $search_log_old->ip == $_SERVER['REMOTE_ADDR'] && (time() - $search_log_old->date_add) > 86400 ))
            {
                if(!SearchLog::IsSearchBot($_SERVER['HTTP_USER_AGENT'])
                        && Yii::app()->user->id != 1)
                {
                    // Заносим в базу и в индекс
                    $search_stat->save();
                    Notice::InsertRtIndexSearch($search_stat->ss_id);

                    $search_log = new SearchLog();
                    $search_log->ss_id = $search_stat->ss_id;
                    $search_log->date_add = time();
                    $search_log->ip = $_SERVER['REMOTE_ADDR'];
                    $search_log->useragent = $_SERVER['HTTP_USER_AGENT'];
                    $search_log->u_id = Yii::app()->user->id;
                    $search_log->r_id = intval($_GET['mainblock']['r_id']);

                    $search_log->t_id = 0;
                    $search_log->reg_id = 0;
                    $search_log->c_id = 0;
                    if(isset($town))
                    {
                        $search_log->t_id = $town->t_id;
                        $search_log->reg_id = $town->reg_id;
                        $search_log->c_id = $town->c_id;
                    }
                    else
                    if(isset($region))
                    {
                        $search_log->t_id = 0;
                        $search_log->reg_id = $region->reg_id;
                        $search_log->c_id = $region->c_id;
                    }
                    else
                    if(isset($country))
                    {
                        $search_log->t_id = 0;
                        $search_log->reg_id = 0;
                        $search_log->c_id = $country->c_id;
                    }

                    $search_log->props_data = '';
                    if(isset($_GET['addfield']))
                    {
                        $search_log->props_data = json_encode($_GET['addfield']);
                    }
                    $search_log->save();

                }
            }


        }


        //echo($query);

        /******************** Конец Статистика поиска ***********************/


//deb::dump($paginator_params);
//die();
        $this->render('index', array(
            'rubrik_groups'=>$rubrik_groups,
            'search_adverts'=>$search_adverts,
            'props_array'=>$props_array,
            'rub_array'=>$rub_array,
            'props_sprav_sorted_array'=>$props_sprav_sorted_array,
            'rubriks_props_array'=>$rubriks_props_array,
            'query_delta'=>$query_delta,
            'mselector'=>$mselector,
            'm_id'=>$m_id,
            'rubriks_all_array'=>$rubriks_all_array,
            'kolpages'=>$kolpages,
            'paginator_params'=>$paginator_params,
            'display_titul_tag'=>$display_titul_tag,
            'cookie'=>$cookie,
            'h1_text'=>$h1_text
        ));

	}


    public function actionGetdatafilter()
    {
        $ret = $this->MakeDataFilter();
        //deb::dump($ret);
        $props_sprav_sorted_array = $ret['props_sprav_sorted_array'];
        $rubriks_props_array = $ret['rubriks_props_array'];
//deb::dump($rubriks_props_array);
        $this->renderPartial('_props_form_search', array(
            //'rubrik_groups'=>$rubrik_groups,
            //'search_adverts'=>$search_adverts,
            //'props_array'=>$props_array,
            //'rub_array'=>$rub_array,
            'props_sprav_sorted_array'=>$props_sprav_sorted_array,
            'rubriks_props_array'=>$rubriks_props_array,
        ));

    }

    // Формирование данных для построения формы фильтра
    public function MakeDataFilter()
    {
        $connection=Yii::app()->db;
        $ret['props_sprav_sorted_array'] = array();
        $ret['rubriks_props_array'] = array();


        if(isset($_GET['mainblock']['r_id']) && intval($_GET['mainblock']['r_id']) > 0)
        {
            $r_id = intval($_GET['mainblock']['r_id']);
            $rubriks_props = RubriksProps::model()->findAll(array(
                'select'=>'*',
                'condition'=>'r_id = '.$r_id . " AND use_in_filter = 1 ",
//                'condition'=>'r_id = '.$r_id . " AND use_in_filter = 1 AND (parent_id = 0 OR all_values_in_filter = 1) ",
                'order'=>'view_block_id ASC, hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
                //'limit'=>'10'
            ));

            $rubriks_props_array = array();
            $rp_id_ids_array = array();
            foreach ($rubriks_props as $mkey=>$mval)
            {
                $mval->options = json_decode($mval->options);
                $rubriks_props_array[$mval->rp_id] = $mval;
                $rp_id_ids_array[] = $mval->rp_id;
            }

            $props_hierarhy = array();
            $props_id_hierarhy = array();
            foreach ($rubriks_props_array as $mkey=>$mval)
            {
                $props_hierarhy[$mval->selector]['vibor_type'] = $mval->vibor_type;
                $props_id_hierarhy[$mval->rp_id]['vibor_type'] = $mval->vibor_type;
                $props_id_hierarhy[$mval->rp_id]['selector'] = $mval->selector;

                if ($mval->parent_id <= 0)
                {
                    $props_hierarhy[$mval->selector]['parent_selector'] = '';
                    $props_id_hierarhy[$mval->rp_id]['parent_ps_id'] = '';
                }
                else
                {
                    $props_hierarhy[$mval->selector]['parent_selector'] = $rubriks_props_array[$mval->parent_id]->selector;
                    $props_id_hierarhy[$mval->rp_id]['parent_ps_id'] = $rubriks_props_array[$mval->parent_id]->rp_id;

                    $props_hierarhy[$rubriks_props_array[$mval->parent_id]->selector]['childs_selector'][$mval->selector] = $mval->selector;
                    $props_id_hierarhy[$rubriks_props_array[$mval->parent_id]->rp_id]['childs_ps_id'][$mval->rp_id] = $mval->rp_id;
                }
            }



            // Получение данных из справочника props_sprav для всех свойств рубрики
            // Внимание: выбираем всё, где rubriks_props.rp_id = props_sprav.rp_id
            // без учета props_sprav.selector. В будущем, если в таблице prop_types_params
            // для каждого rubriks_props.type_id будет несколько записей (сейчас одна),
            // то надо внести соответствующие корректировки

            $props_sprav_array = array();
            $props_sprav_index_array = array();
            $props_sprav = array();
            foreach ($rubriks_props_array as $mkey=>$mval)
            {
                $props_sprav_temp = array();
                if( ($props_id_hierarhy[$mval->rp_id]['parent_ps_id'] == '')
                    || ($props_id_hierarhy[$mval->rp_id]['parent_ps_id'] > 0
                        && $mval->all_values_in_filter == 1) )
                {
                    $temp = PropsSprav::model()->findAll(array(
                        'select'=>'*',
                        'condition'=>'rp_id = '. $mval->rp_id,
                        //'order'=>'rp_id'
                    ));

                    foreach($temp as $tkey => $tval)
                    {
                        $props_sprav_temp[$tval->ps_id] = $tval->attributes;
                    }
                }


                // Зависимые свойства
                $parent_selector = $props_id_hierarhy[$props_id_hierarhy[$mval->rp_id]['parent_ps_id']]['selector'];
                $parent_ps_id = $props_id_hierarhy[$mval->rp_id]['parent_ps_id'];
                //$parent_ps_id = 0;
                $parent_ps_id_array = array();
                //deb::dump($props_id_hierarhy);
                //deb::dump($_GET['addfield'][$parent_selector]);
                if(isset($parent_selector) && isset($_GET['addfield'][$parent_selector]))
                {
                    //$parent_ps_id = intval($_GET['addfield'][$parent_selector]);

                    if($rubriks_props_array[$parent_ps_id]->filter_type == 'select_one'
                        || $rubriks_props_array[$parent_ps_id]->filter_type == 'select_multi'
                        || $rubriks_props_array[$parent_ps_id]->filter_type == 'checkbox_list')
                    {
                        if(is_array($_GET['addfield'][$parent_selector])
                            && count($_GET['addfield'][$parent_selector]) > 0 )
                        {
                            foreach($_GET['addfield'][$parent_selector] as $manykey=>$manyval)
                            {
                                $parent_ps_id_array[] = intval($manyval);
                            }

                        }
                        else
                        {
                            $parent_ps_id_array[] = intval($_GET['addfield'][$parent_selector]);
                        }
                    }

                    //deb::dump($mval);
                    if($rubriks_props_array[$parent_ps_id]->filter_type == 'range')
                    {
                        // Сделал без учета зависимости диапазона от родителя.
                        // Т.е. берем все значения в диапазоне, без учета зависиомостей.
                        // Возможно надо будет доработать

                        //deb::dump($rubriks_props_array[$parent_ps_id]);

                        if(isset($_GET['addfield'][$parent_selector]['from'])
                            && isset($_GET['addfield'][$parent_selector]['to'])
                            && $_GET['addfield'][$parent_selector]['from'] != ''
                            && $_GET['addfield'][$parent_selector]['to'] != '')
                        {
                            $from = PropsSprav::model()->findByPk(intval($_GET['addfield'][$parent_selector]['from']));
                            $to = PropsSprav::model()->findByPk(intval($_GET['addfield'][$parent_selector]['to']));
                            $sql = "SELECT *
                                    FROM ". $connection->tablePrefix . "props_sprav ps
                                    WHERE ps.rp_id = ".$rubriks_props_array[$parent_ps_id]->rp_id . "
                                        AND ps.value >= " . $from->value . " AND ps.value <= " . $to->value;
                        }
                        else
                            if(isset($_GET['addfield'][$parent_selector]['from'])
                                && $_GET['addfield'][$parent_selector]['from'] != '')
                            {
                                $from = PropsSprav::model()->findByPk(intval($_GET['addfield'][$parent_selector]['from']));
                                $sql = "SELECT *
                                FROM ". $connection->tablePrefix . "props_sprav ps
                                WHERE ps.rp_id = ".$rubriks_props_array[$parent_ps_id]->rp_id . "
                                    AND ps.value >= " . $from->value ;
                            }
                            else
                                if(isset($_GET['addfield'][$parent_selector]['to'])
                                    && $_GET['addfield'][$parent_selector]['from'] != '')
                                {
                                    $to = PropsSprav::model()->findByPk(intval($_GET['addfield'][$parent_selector]['to']));
                                    $sql = "SELECT *
                            FROM ". $connection->tablePrefix . "props_sprav ps
                            WHERE ps.rp_id = ".$rubriks_props_array[$parent_ps_id]->rp_id . "
                                AND ps.value <= " . $to->value;
                                }



                        $command2 = $connection->createCommand($sql);
                        $dataReader2 = $command2->query();

                        while(($row2 = $dataReader2->read())!==false)
                        {
                            $parent_ps_id_array[] = $row2['ps_id'];
                        }

                    }

                }

                if($props_id_hierarhy[$mval->rp_id]['parent_ps_id'] > 0
                    && $mval->all_values_in_filter == 0
                    && count($parent_ps_id_array) > 0 )
                {
                    $parent_ps_id_sql = implode(", ", $parent_ps_id_array);

                    $sql = "SELECT *
                            FROM
                            ". $connection->tablePrefix . "props_sprav ps,
                            ". $connection->tablePrefix . "props_relations pr
                            WHERE
                            ps.rp_id = $mval->rp_id AND ps.ps_id = pr.child_ps_id
                            AND pr.parent_ps_id IN (".$parent_ps_id_sql.") ";
                    //deb::dump($sql);
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    $props_sprav_temp = array();
                    while(($rowrelate = $dataReader->read())!==false)
                    {
                        $props_sprav_temp[$rowrelate['ps_id']] = $rowrelate;
                    }
                }

                $props_sprav = array_merge($props_sprav, $props_sprav_temp);
                // КОНЕЦ Зависимые свойтва


            }
            //deb::dump($props_sprav);

            foreach($props_sprav as $pkey=>$pval)
            {
                $props_sprav_array[$pval['rp_id']][$pval['ps_id']] = $pval;
                $props_sprav_index_array[$pval['rp_id']] = $pval;
            }

            $props_sprav_sorted_array = array();
            foreach($props_sprav_array as $pkey=>$pval)
            {
                $type_id_array = array();
                $selector_array = array();
                $value_array = array();
                $transname_array = array();
                $sort_number_array = array();

                foreach($pval as $p2key=>$p2val)
                {
                    $type_id_array[$p2key] = $p2val['type_id'];
                    $selector_array[$p2key] = $p2val['selector'];
                    $value_array[$p2key] = $p2val['value'];
                    $transname_array[$p2key] = $p2val['transname'];
                    $sort_number_array[$p2key] = $p2val['sort_number'];
                }


                switch($rubriks_props_array[$pkey]['sort_props_sprav'])
                {
                    case "asc":
                        array_multisort($value_array, SORT_ASC, $pval);
                        break;

                    case "desc":
                        array_multisort($value_array, SORT_DESC, $pval);
                        break;

                    case "sort_number":
                        array_multisort($sort_number_array, SORT_ASC, $pval);
                        break;
                }

                foreach($pval as $sortkey=>$sortval)
                {
                    $props_sprav_sorted_array[$sortval['rp_id']][$sortval['ps_id']] = $sortval;
                }

            }

            $ret['props_sprav_sorted_array'] = $props_sprav_sorted_array;
            $ret['rubriks_props_array'] = $rubriks_props_array;

        }

        unset($props_sprav_array);

        return $ret;

    }

    public function  actionSearch()
    {

        //////////////////////
        $parts = array();
        if(isset($_POST['mesto_id']))
        {
            $parts = explode("_", $_POST['mesto_id']);
        }

        $mesto_isset_tag = 0;
        $mselector = '';
        $m_id = 0;
        if(count($parts) == 2 && intval($parts[1]) > 0)
        {
            $mesto_isset_tag = 1;
            $mselector = $parts[0];
            $m_id = intval($parts[1]);
        }
        /////////////////////

        $url_parts = array();
        //if(($t_id = intval($_POST['mainblock']['t_id'])) > 0)
        if($mesto_isset_tag == 1 && $mselector == 't' && $m_id > 0)
        {
            $town = Towns::model()->findByPk($m_id);
            $url_parts[0] = $town->transname;

        }
        else if ($mesto_isset_tag == 1 && $mselector == 'reg' && $m_id > 0)
        {
            $region = Regions::model()->findByPk($m_id);
            $url_parts[0] = $region->transname;
        }
        else if ($mesto_isset_tag == 1 && $mselector == 'c' && $m_id > 0)
        {
            $country = Countries::model()->findByPk($m_id);
            $url_parts[0] = $country->transname;
        }
        else
        {
            $url_parts[0] = 'all';
        }

        if(($r_id = intval($_POST['mainblock']['r_id'])) > 0)
        {
            $rubrik = Rubriks::model()->findByPk($r_id);
            $url_parts[1] = $rubrik->transname;
        }



        $url_str = implode("/", $url_parts);
        $url = Yii::app()->createAbsoluteUrl($url_str)."?".http_build_query($_POST);

        header('Location: '.$url);

        //deb::dump($url);

        //$curl = new Curl;
        //$content = $curl->get($url);


        //echo($content);
        //deb::dump($_POST);
    }

    // Получение списка регионов (городов, регионов, стран) по подстроке
    public function actionGetRegionList()
    {
        $countries_array = Countries::getCountryListLight();
        $regions_array = Regions::getRegionListLight();

        $searchstr = $_POST['searchstr'];

        $return_array['reglist'] = array();


        // Сначала российские города и российские регионы
        $from_towns = Towns::model()->findAll(array(
            'select'=>'t_id, reg_id, c_id, name',
            'condition'=>"double_tag = 0 AND c_id = :russia_id AND name LIKE :name ",
            'order'=>'name ASC',
            'params'=>array(':name'=>'%'.$searchstr.'%', ':russia_id'=>Yii::app()->params['russia_id'])
        ));
        foreach($from_towns as $fkey=>$fval)
        {
            if(isset(Towns::$alter_regions[$fval->t_id]))
            {
                continue;
            }

            $return_array['reglist']['t_'.$fval->t_id]['id'] = 't_'.$fval->t_id;
            $return_array['reglist']['t_'.$fval->t_id]['name_ru'] = $fval->name . ", " . $regions_array[$fval->reg_id] . ", " . $countries_array[$fval->c_id];
        }

        $from_regions = Regions::model()->findAll(array(
            'select'=>'reg_id, c_id, name',
            'condition'=>"c_id = :russia_id AND name LIKE :name ",
            'order'=>'name ASC',
            'params'=>array(':name'=>'%'.$searchstr.'%', ':russia_id'=>Yii::app()->params['russia_id'])
        ));
        foreach($from_regions as $fkey=>$fval)
        {
            $return_array['reglist']['reg_'.$fval->reg_id]['id'] = 'reg_'.$fval->reg_id;
            $return_array['reglist']['reg_'.$fval->reg_id]['name_ru'] = $fval->name . ", " . $countries_array[$fval->c_id];
        }

        $from_countries = Countries::model()->findAll(array(
            'select'=>'c_id, name',
            'condition'=>"c_id = :russia_id AND name LIKE :name ",
            'order'=>'name ASC',
            'params'=>array(':name'=>'%'.$searchstr.'%', ':russia_id'=>Yii::app()->params['russia_id'])
        ));
        foreach($from_countries as $fkey=>$fval)
        {
            $return_array['reglist']['c_'.$fval->c_id]['id'] = 'c_'.$fval->c_id;
            $return_array['reglist']['c_'.$fval->c_id]['name_ru'] = $fval->name;
        }



        // Потом города и регионы СССР
        $ussr_list = implode(", ", Yii::app()->params['ussr_countries_ids']);
        $from_towns = Towns::model()->findAll(array(
            'select'=>'t_id, reg_id, c_id, name',
            'condition'=>"double_tag = 0 AND c_id <> :russia_id AND c_id IN (".$ussr_list.") AND name LIKE :name ",
            'order'=>'name ASC',
            'params'=>array(':name'=>'%'.$searchstr.'%', ':russia_id'=>Yii::app()->params['russia_id'])
        ));
        foreach($from_towns as $fkey=>$fval)
        {
            if(isset(Towns::$alter_regions[$fval->t_id]))
            {
                continue;
            }

            $return_array['reglist']['t_'.$fval->t_id]['id'] = 't_'.$fval->t_id;
            $return_array['reglist']['t_'.$fval->t_id]['name_ru'] = $fval->name . ", " . $regions_array[$fval->reg_id] . ", " . $countries_array[$fval->c_id];
        }


        $from_regions = Regions::model()->findAll(array(
            'select'=>'reg_id, c_id, name',
            'condition'=>"c_id <> :russia_id AND c_id IN (".$ussr_list.") AND name LIKE :name ",
            'order'=>'name ASC',
            'params'=>array(':name'=>'%'.$searchstr.'%', ':russia_id'=>Yii::app()->params['russia_id'])
        ));
        foreach($from_regions as $fkey=>$fval)
        {
            $return_array['reglist']['reg_'.$fval->reg_id]['id'] = 'reg_'.$fval->reg_id;
            $return_array['reglist']['reg_'.$fval->reg_id]['name_ru'] = $fval->name . ", " . $countries_array[$fval->c_id];
        }


        $from_countries = Countries::model()->findAll(array(
            'select'=>'c_id, name',
            'condition'=>"c_id <> :russia_id AND c_id IN (".$ussr_list.") AND name LIKE :name ",
            'order'=>'name ASC',
            'params'=>array(':name'=>'%'.$searchstr.'%', ':russia_id'=>Yii::app()->params['russia_id'])
        ));
        foreach($from_countries as $fkey=>$fval)
        {
            $return_array['reglist']['c_'.$fval->c_id]['id'] = 'c_'.$fval->c_id;
            $return_array['reglist']['c_'.$fval->c_id]['name_ru'] = $fval->name;
        }


        // Потом все остальные
        $from_towns = Towns::model()->findAll(array(
            'select'=>'t_id, reg_id, c_id, name',
            'condition'=>"double_tag = 0 AND c_id <> :russia_id AND c_id NOT IN (".$ussr_list.") AND name LIKE :name ",
            'order'=>'name ASC',
            'params'=>array(':name'=>'%'.$searchstr.'%', ':russia_id'=>Yii::app()->params['russia_id'])
        ));
        foreach($from_towns as $fkey=>$fval)
        {
            if(isset(Towns::$alter_regions[$fval->t_id]))
            {
                continue;
            }

            $return_array['reglist']['t_'.$fval->t_id]['id'] = 't_'.$fval->t_id;
            $return_array['reglist']['t_'.$fval->t_id]['name_ru'] = $fval->name . ", " . $regions_array[$fval->reg_id] . ", " . $countries_array[$fval->c_id];
        }


        $from_regions = Regions::model()->findAll(array(
            'select'=>'reg_id, c_id, name',
            'condition'=>"c_id <> :russia_id AND c_id NOT IN (".$ussr_list.") AND name LIKE :name ",
            'order'=>'name ASC',
            'params'=>array(':name'=>'%'.$searchstr.'%', ':russia_id'=>Yii::app()->params['russia_id'])
        ));
        foreach($from_regions as $fkey=>$fval)
        {
            $return_array['reglist']['reg_'.$fval->reg_id]['id'] = 'reg_'.$fval->reg_id;
            $return_array['reglist']['reg_'.$fval->reg_id]['name_ru'] = $fval->name . ", " . $countries_array[$fval->c_id];
        }


        $from_countries = Countries::model()->findAll(array(
            'select'=>'c_id, name',
            'condition'=>"c_id <> :russia_id AND c_id NOT IN (".$ussr_list.") AND name LIKE :name ",
            'order'=>'name ASC',
            'params'=>array(':name'=>'%'.$searchstr.'%', ':russia_id'=>Yii::app()->params['russia_id'])
        ));
        foreach($from_countries as $fkey=>$fval)
        {
            $return_array['reglist']['c_'.$fval->c_id]['id'] = 'c_'.$fval->c_id;
            $return_array['reglist']['c_'.$fval->c_id]['name_ru'] = $fval->name;
        }

        if(count($return_array['reglist']) == 0)
        {
            $return_array['reglist']['none_0']['id'] = 'none_0';
            $return_array['reglist']['none_0']['name_ru'] = 'нет совпадений';
        }

        //deb::dump($from_towns);
        echo json_encode($return_array);

    }


    // Поиск подсказок для строки поиска
    public function actionGetquerylist()
    {
        $connection = Yii::app()->db_sphinx;

        $searchstr = $_POST['searchstr'];

        $ret = array();
        $res_array = array();
        $for_snippets_array = array();
        if($rows = Notice::GetSphinxSearchStat($searchstr, 0.9, $ids_array))
        {
            $i=0;
            foreach($rows as $rkey=>$rval)
            {
                $for_snippets_array[$i] = Notice::SphinxStringPrepare($rval['query']);
                $i++;
            }

            $strs = '';
            $strs = "('".implode("','", $for_snippets_array)."')";
            $snipstr = Notice::SphinxStringPrepare($searchstr);

            $sql = "CALL SNIPPETS(".$strs.", 'rt_search_stat', '".$snipstr."', 1 AS query_mode)";
            $command = $connection->createCommand($sql);
            $row_snippets = $command->queryAll();

            $snippets = array();
            foreach($row_snippets as $key=>$val)
            {
                $snippets[$key] = str_replace($searchstr, "<b>".$searchstr."</b>", $val['snippet']);
            }
//deb::dump($snippets);

            $i=0;
            foreach($rows as $rkey=>$rval)
            {
                $temp = array();
                $temp['id'] = $rval['id'];
                $temp['val'] = htmlspecialchars($rval['query']);
                $temp['snippet'] = $snippets[$i];
                $ret[$i] = $temp;

                $i++;
            }
        }

        echo json_encode($ret);

    }


    // Установка куки выбранного региона и редирект на соответствующую страницу
    public function  actionSetRegionCookie()
    {
        $parts = array();
        if(isset($_POST['region_id'])) // $_POST['region_id'] может содержать город, страну или регион
        {
            $parts = explode("_", $_POST['region_id']);

            // Подверждаем выбор региона пользователем
            if(isset($_POST['reg_confirm_tag']) && $_POST['reg_confirm_tag'] == 1)
            {
                $cookie = new CHttpCookie('region_confirm_tag', 1);
                $cookie->expire = time() + 86400*30*12;
                Yii::app()->request->cookies['region_confirm_tag'] = $cookie;
            }
        }

        if(isset($_POST['region_id']) && count($parts) == 2 && intval($parts[1]) >= 0)
        {
            self::unsetRegionCookies();

            $region_id = intval($parts[1]);

            self::SetGeolocatorCookie('geo_mytown_handchange_tag', 1, 86400*30);

            /********** Подготовка на возврат к странице предыдущего поиска, но с новым регионом **********/
            $path_replace_tag = 0;
            $redirect_url = $_SERVER['HTTP_REFERER'];
            $url_parts = parse_url($_SERVER['HTTP_REFERER']);
            $path_parts = explode("/", $url_parts['path']);

            if(strlen($url_parts['path']) > 1)
            {
                $path_parts = explode("/", $url_parts['path']);
                $transname = $path_parts[1];
                $path_parts[1] = '<--placeholder-->';
                $redirect_url = $url_parts['scheme']."://".$url_parts['host'].implode("/", $path_parts);
                if(isset($url_parts['query']))
                {
                    $redirect_url .= "?".$url_parts['query'];
                }

                if($town_r = Towns::model()->findByAttributes(array('transname'=>$transname)))
                {
                    $path_replace_tag = 1;
                }
                else if($region_r = Regions::model()->findByAttributes(array('transname'=>$transname)))
                {
                    $path_replace_tag = 1;
                }
                else if($country_r = Countries::model()->findByAttributes(array('transname'=>$transname)))
                {
                    $path_replace_tag = 1;
                }


            }

            /******* КОНЕЦ Подготовка на возврат к странице предыдущего поиска, но с новым регионом ********/

            if($parts[0] == 't')
            {
                $town = Towns::model()->findByPk($region_id);
                $region = Regions::model()->findByPk($town->reg_id);
                $country = Countries::model()->findByPk($town->c_id);

                self::SetGeolocatorCookie('geo_mytown', $town->t_id, 86400*30);
                self::SetGeolocatorCookie('geo_mytown_name', $town->name, 86400*30);

                self::SetGeolocatorCookie('geo_myregion', $region->reg_id, 86400*30);
                self::SetGeolocatorCookie('geo_myregion_name', $region->name, 86400*30);

                self::SetGeolocatorCookie('geo_mycountry', $country->c_id, 86400*30);
                self::SetGeolocatorCookie('geo_mycountry_name', $country->name, 86400*30);

                if($path_replace_tag == 1)
                {
                    $redirect_url = str_replace('<--placeholder-->', $town->transname, $redirect_url);
                    $redirect_url = preg_replace('|mesto_id=[a-z]+_[\d]+&|siU', 'mesto_id=t_'.$town->t_id.'&', $redirect_url);
                    header('Location: '.$redirect_url);
                }
                else
                {
                    header('Location: /'.$town->transname);
                }
            }

            if($parts[0] == 'reg')
            {
                $region = Regions::model()->findByPk($region_id);
                $country = Countries::model()->findByPk($region->c_id);

                self::SetGeolocatorCookie('geo_myregion', $region->reg_id, 86400*30);
                self::SetGeolocatorCookie('geo_myregion_name', $region->name, 86400*30);

                self::SetGeolocatorCookie('geo_mycountry', $country->c_id, 86400*30);
                self::SetGeolocatorCookie('geo_mycountry_name', $country->name, 86400*30);

                if($path_replace_tag == 1)
                {
                    $redirect_url = str_replace('<--placeholder-->', $region->transname, $redirect_url);
                    $redirect_url = preg_replace('|mesto_id=[a-z]+_[\d]+&|siU', 'mesto_id=reg_'.$region->reg_id.'&', $redirect_url);
                    header('Location: '.$redirect_url);
                }
                else
                {
                    header('Location: /'.$region->transname);
                }

            }

            if($parts[0] == 'c')
            {
                $country = Countries::model()->findByPk($region_id);

                self::SetGeolocatorCookie('geo_mycountry', $country->c_id, 86400*30);
                self::SetGeolocatorCookie('geo_mycountry_name', $country->name, 86400*30);

                if($path_replace_tag == 1)
                {
                    $redirect_url = str_replace('<--placeholder-->', $country->transname, $redirect_url);
                    $redirect_url = preg_replace('|mesto_id=[a-z]+_[\d]+&|siU', 'mesto_id=c_'.$country->c_id.'&', $redirect_url);
                    header('Location: '.$redirect_url);
                }
                else
                {
                    header('Location: /'.$country->transname);
                }
            }


            if($parts[0] == 'none')
            {
                header('Location: /all');
            }

        }
        else
        {
            header('Location: /all');
        }

    }


    // Установка кук геолокации
    public static function SetGeolocatorCookie($name, $value, $period)
    {
        $cookie = new CHttpCookie($name, $value);
        $cookie->expire = time() + $period;
        Yii::app()->request->cookies[$name] = $cookie;
    }



    // Обнуление кук выбора региона
    public static  function unsetRegionCookies()
    {

        unset(Yii::app()->request->cookies['geo_mytown']);
        unset(Yii::app()->request->cookies['geo_myregion']);
        unset(Yii::app()->request->cookies['geo_mycountry']);
        unset(Yii::app()->request->cookies['geo_mytown_name']);
        unset(Yii::app()->request->cookies['geo_myregion_name']);
        unset(Yii::app()->request->cookies['geo_mycountry_name']);

    }


    // Формирование списка выбора местоположения в фильтре поиска объявлений
    public function actionMestolistgenerate()
    {
        $parts = array();
        if(isset($_POST['mesto_id'])) // $_POST['region_id'] может содержать город, страну или регион
        {
            $parts = explode("_", $_POST['mesto_id']);
        }

        $ret = array();
        if(isset($_POST['mesto_id']) && count($parts) == 2 && intval($parts[1]) >= 0)
        {
            $mesto_selector = $parts[0];
            $mesto_id = intval($parts[1]);

            $data = self::ListMestoForSearch($mesto_selector, $mesto_id);

            $ret['status']['code'] = 'ok';
            $ret['status']['message'] = '';
            $ret['data'] = $data;
        }
        else
        {
            $ret['status']['code'] = 'error';
            $ret['status']['message'] = 'Некорректные входные параметры';
        }

        echo json_encode($ret, JSON_UNESCAPED_SLASHES);

    }



    // Генерация списка выбора местоположения для формы поиска
    public static function ListMestoForSearch($mesto_selector, $mesto_id)
    {
        $data = '';

        ob_start();

        switch($mesto_selector)
        {
            case "t":
                $town = Towns::model()->findByPk($mesto_id);

                if(isset(Towns::$alter_regions[$town->t_id]))
                {
                    $region = Regions::model()->findByPk(Towns::$alter_regions[$town->t_id]);
                }
                else
                {
                    $region = Regions::model()->findByPk($town->reg_id);
                }

                $country = Countries::model()->findByPk($town->c_id);

                ?>
                <option value="c_<?= $country->c_id;?>"><?= $country->name;?></option>
                <option selected value="t_<?= $town->t_id;?>"><?= $town->name;?></option>
                <option value="reg_<?= $region->reg_id;?>"><?= $region->name;?></option>
                <?

                break;

            case "reg":
                $region = Regions::model()->findByPk($mesto_id);
                $country = Countries::model()->findByPk($region->c_id);

                ?>
                <option value="c_<?= $country->c_id;?>"><?= $country->name;?></option>
                <option selected value="reg_<?= $region->reg_id;?>"><?= $region->name;?></option>
                <?
                if(isset(Regions::$alter_regions[$region->reg_id]))
                {
                    $region2 = Regions::model()->findByPk(Regions::$alter_regions[$region->reg_id]);
                    ?>
                    <option value="reg_<?= $region2->reg_id;?>"><?= $region2->name;?></option>
                <?
                }

                break;

            case "c":
                $country = Countries::model()->findByPk($mesto_id);

                ?>
                <option selected value="c_<?= $country->c_id;?>"><?= $country->name;?></option>
                <?

                break;

            case "none":
                ?>
                <option selected value="none_0">регион не определен</option>
                <?
            break;

        }

        ?>
        <option value="other" >Выбрать другой...</option>
        <?

        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }



    // Временный экшн, выставление цены для старых объяв
    public function actionChangeprice()
    {
        die('Блокировано! Для разблокировки удалить!');

        $n_id = intval($_POST['n_id']);
        $price = floatval($_POST['price']);
        $cost_valuta = $_POST['cost_valuta'];

        if($notice = Notice::model()->findByPk($n_id))
        {
            $notice->cost = $price;
            $notice->cost_valuta = $cost_valuta;
            $notice->save();

            $ret['status'] = 'ok';
            $ret['price'] = $notice->cost;
        }
        else
        {
            $ret['status'] = 'error';
            $ret['message'] = 'Не сохранено';
        }

        echo json_encode($ret);
    }

    // Установка $_SESSION['sort_select_mode'] = 1
    public function actionSetsortmode()
    {
        if(isset($_POST['m']) && $_POST['m'] == 1 )
        {
            $_SESSION['sort_select_mode'] = 1;

            echo "1";
        }
    }






	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}