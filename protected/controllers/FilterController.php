<?php

class FilterController extends Controller
{
	public function actionIndex()
	{

        $connection=Yii::app()->db;

        // Местоположение
        $mesto_sql = "1 ";
        //if(isset($_GET['c_id']))
        if(isset($_GET['mainblock']['c_id']))
        {
            $mesto_sql = " n.c_id = ".intval($_GET['mainblock']['c_id']);
        }
        if(isset($_GET['mainblock']['reg_id']))
        {
            $mesto_sql = " n.reg_id = ".intval($_GET['mainblock']['reg_id']);
        }
        if(isset($_GET['mainblock']['t_id']))
        {
            $mesto_sql = " n.t_id = ".intval($_GET['mainblock']['t_id']);
        }

        //Рубрика
        $rubrik_sql = "1 ";
        //deb::dump($_GET);
        //if(isset($_GET['mainblock']['r_id']))
        if(!isset($_GET['parent_r_id']) && isset($_GET['mainblock']['r_id']))
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
            'condition'=>$rubrik_sql." AND hierarhy_tag = 1 ",
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
            )
        );

        $q_sql = " ";
        if(isset($_GET['params']['q']) && trim($_GET['params']['q']) != '')
        {
            $q_sql = " AND n.title LIKE '%".trim($_GET['params']['q'])."%' ";
        }
    //deb::dump($_GET);
    //deb::dump($q_sql);
        $search_adverts = array();  // Найденные объявы
        if(count($_GET['prop']) > 0 || (isset($_GET['addfield']) && count($_GET['addfield']) > 0 ) )
        {
            $rp_ids = array();
            $rubriks_props_poryadok_array = array();
            $rubriks_poryadok_props_array = array();
            $i=2;
            foreach ($rubriks_props as $rkey=>$rval)
            {
                $rubriks_props_poryadok_array[$rval->rp_id] = $i++;
                $rubriks_poryadok_props_array[$i-1] = $rval->rp_id;
                $rp_ids[$rval->rp_id] = $rval->rp_id;
            }
//deb::dump($rp_ids);
//deb::dump($rubriks_props_poryadok_array);
//deb::dump($rubriks_poryadok_props_array);
            $props_sprav = PropsSprav::model()->findAll(array('condition'=>'rp_id IN ('.implode(", ", $rp_ids).')'));
            $props_route_items = array();
            foreach($props_sprav as $pkey=>$pval)
            {
                $props_route_items[$rubriks_props_poryadok_array[$pval->rp_id]][$pval->transname] = $pval;
            }
            //deb::dump($props_route_items);
//deb::dump($_GET['prop']);
            $props_sql_array = array();
            $path_ps_id_array = array();
            $ps_id = 0;
            foreach($_GET['prop'] as $pkey=>$pval)
            {
                if(isset($props_route_items[$pkey][$pval]))
                {
                    $props_sql_array[] = $props_route_items[$pkey][$pval];
                    $ps_id = $props_route_items[$pkey][$pval]->ps_id;
                    $path_ps_id_array[] = $ps_id;
                }
                else
                {

                }
            }
            $current_ps_id = $ps_id;

          //deb::dump($props_route_items);
//            deb::dump($_GET);

            // Ищем объявы с совпадением значений всех указанных свойств
            if(count($_GET['prop']) == count($props_sql_array))
            {
//deb::dump($_GET);



                $kol_props = count($props_sql_array);
                $from_tables_array = array();
                $from_tables_sql = "";
                $where_n_array = array();
                $where_n = "";
                $where_filter_array = array();
                $where_filter_sql = "";
                for($i=1; $i<=$kol_props; $i++)
                {
                    $from_tables_array[] = $connection->tablePrefix . "notice_props n".$i;
                    $where_n_array[] = " AND n".$i.".n_id = n".($i+1).".n_id ";
                    $where_filter_array[] = "n".$i.".ps_id = ".$props_sql_array[$i-1]->ps_id;
                }
                $from_tables_sql = implode(", ", $from_tables_array);
                unset($where_n_array[count($where_n_array)-1]);
                $where_n = implode(" ", $where_n_array);
                $where_filter_sql = implode(" AND ", $where_filter_array);
                //deb::dump($from_tables_sql);
                //deb::dump($where_n);

                $rubrik_prop_sql = str_replace("r_id", "n.r_id", $rubrik_sql);
                $sql = "SELECT n.*, t.name town_name, t.transname town_transname
                        FROM ". $connection->tablePrefix . "notice n,
                        ".$from_tables_sql.",
                        ". $connection->tablePrefix . "towns t
                        WHERE
                        $mesto_sql AND $rubrik_prop_sql AND
                        $where_filter_sql
                        ".$where_n.$q_sql."
                        AND n1.n_id = n.n_id
                        AND n.t_id = t.t_id ";
                //deb::dump($sql);
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                while(($row = $dataReader->read())!==false)
                {
                    $search_adverts[$row['n_id']] = $row;
                    //deb::dump($row);
                }


                // Формирование данных для ссылок на подгруппы
                $rubrik_groups = array();

                $subprop_rp_id = $rubriks_poryadok_props_array[count($_GET['prop'])+2];
                if(isset($subprop_rp_id))
                {
                    /*
                    deb::dump($subprop_rp_id);
                    $subprops = PropsSprav::model()->findAll(array('condition'=>'rp_id = '.$subprop_rp_id));
                    deb::dump($_GET['prop']);
                    */
                    $subprops = PropsRelations::model()->findAll(array('condition'=>'parent_ps_id = '.$current_ps_id));
                    //deb::dump($subprops);

                    $sql = "SELECT nsub.ps_id, ps.value, ps.transname, count(nsub.ps_id) cnt
                            FROM ". $connection->tablePrefix . "notice n,
                            ".$from_tables_sql.",
                            ". $connection->tablePrefix . "notice_props nsub,
                            ". $connection->tablePrefix . "props_sprav ps
                            WHERE
                            $mesto_sql AND $rubrik_prop_sql AND
                            $where_filter_sql
                            ".$where_n."
                            AND n1.n_id = n.n_id
                            AND n.n_id = nsub.n_id AND nsub.rp_id = ".$subprop_rp_id . "
                            AND nsub.ps_id = ps.ps_id
                            GROUP BY nsub.ps_id, ps.value, ps.transname ";
                //deb::dump($sql);
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    while(($row = $dataReader->read())!==false)
                    {
                        //deb::dump($row);
                        $row['path'] = Yii::app()->getRequest()->getPathInfo()."/".$row['transname'];
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
        // Если поиск только по местоположению/рубрике - простой запрос
        else
        {
            $mesto_rub_sql = str_replace(" n.", " t.", $mesto_sql);
            $q_sql = str_replace(" n.", " t.", $q_sql);
            $adverts = Notice::model()->with('town')->findAll(
                array(
                    'select'=>'*, town.name as town_name, town.transname as town_transname',
                    'condition'=>$mesto_rub_sql." AND ".$rubrik_sql.$q_sql
                )
            );
//deb::dump(count($adverts));
            foreach ($adverts as $akey=>$aval)
            {
                //deb::dump($aval->town['name']);
                $search_adverts[$aval->n_id] = $aval->attributes;
                $search_adverts[$aval->n_id]['town_name'] = $aval->town['name'];
                $search_adverts[$aval->n_id]['town_transname'] = $aval->town['transname'];
            }
//deb::dump(count($search_adverts));

            // Разбивка выбранного раздела на подгруппы
            // Если выбранный раздел является родительской рубрикой
            if(isset($_GET['parent_r_id']))
            {
                $rubrik_simple_sql = " r.r_id IN (". implode(", ", $subrubs_ids).") ";

                $sql = "SELECT r.name, r.transname, COUNT(n.n_id) cnt
                        FROM
                        ". $connection->tablePrefix . "notice n,
                        ". $connection->tablePrefix . "rubriks r
                        WHERE
                        ".$mesto_sql." AND ".$rubrik_simple_sql."
                        AND r.parent_id <> 0 AND n.r_id = r.r_id
                        GROUP BY r.name, r.transname ";
                //deb::dump($sql);
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $rubrik_groups = array();
                while(($rowgroup = $dataReader->read())!==false)
                {
                    $curr_path_parts = explode("/", Yii::app()->getRequest()->getPathInfo());
                    $rowgroup['path'] = $curr_path_parts[0]."/".$rowgroup['transname'];
                    $rubrik_groups[] = $rowgroup;
                }

                //deb::dump($rubrik_groups);
            }
            // Если выбранный раздел является подрубрикой
            else if (isset($_GET['mainblock']['r_id']) && !isset($_GET['mainblock']['parent_r_id']) )
            {
                $props_sprav = PropsSprav::model()->findAll(array('condition'=>'rp_id = '.$rubriks_props[0]->rp_id));

                $props_groups = array();
                $props_rows = array();
                foreach($props_sprav as $pkey=>$pval)
                {
                    $props_groups[] = $pval->ps_id;
                    $props_rows[$pval->ps_id] = $pval;
                }
            //deb::dump($props_sprav);
                $mesto_simple_sql = str_replace(" n.", " ", $mesto_sql);
                $sql = "SELECT p.ps_id, count(p.ps_id) cnt
                        FROM
                        ". $connection->tablePrefix . "notice n,
                        ". $connection->tablePrefix . "rubriks r,
                        ". $connection->tablePrefix . "notice_props p
                        WHERE
                        n.r_id = ".intval($_GET['mainblock']['r_id'])."
                        AND $mesto_simple_sql
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
                    $rubrik_groups[$row['ps_id']]['path'] = Yii::app()->getRequest()->getPathInfo()."/".$props_rows[$row['ps_id']]->transname;

                }

                //deb::dump($rubrik_groups);


            }
            // Если выбранные раздел является местоположением (страна или регион или город)
            else
            {
                $sql = "SELECT r.name, r.transname, COUNT(n.n_id) cnt
                        FROM
                        ". $connection->tablePrefix . "notice n,
                        ". $connection->tablePrefix . "rubriks r,
                        ". $connection->tablePrefix . "rubriks r2
                        WHERE
                        ".$mesto_sql." AND r.parent_id = 0 AND r2.parent_id = r.r_id
                        AND n.r_id = r2.r_id
                        GROUP BY r.name, r.transname ";
                //deb::dump($sql);
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $rubrik_groups = array();
                while(($rowgroup = $dataReader->read())!==false)
                {
                    $rowgroup['path'] = Yii::app()->getRequest()->getPathInfo()."/".$rowgroup['transname'];
                    $rubrik_groups[] = $rowgroup;
                }
                //deb::dump($rubrik_groups);
            }



        }

        //deb::dump($search_adverts);
        // Шаблоны отображения из рубрик
        $shablons = Rubriks::model()->findAll(array('select'=>'r_id, advert_list_item_shablon'));
        $shablons_display = array();
        foreach($shablons as $skey=>$sval)
        {
            $shablons_display[$sval->r_id] = $sval->advert_list_item_shablon;
        }
//deb::dump($search_adverts);
        // Подготовка данных для отображения
        $props_array = array();
        $rubriks_all_array = Rubriks::get_all_subrubs();
        foreach($search_adverts as $key=>$val)
        {

            $props_display = array();
            $photos = array();
            $xml = new SimpleXMLElement($val['props_xml']);
            foreach($xml->block as $bkey=>$bval)
            {
                foreach($bval as $b2key=>$b2val)
                {
                    $temp = array();
                    foreach($b2val->item as $ikey=>$ival)
                    {
                        if($ival->hand_input_value != '')
                        {
                            $temp[] = (string)$ival->hand_input_value;
                            if($ival->vibor_type == 'photoblock')
                            {
                                if(strlen($ival->hand_input_value) > 0)
                                {
                                    $files_str = (string)$ival->hand_input_value;
                                    if($files_str[strlen($files_str)-1] == ';')
                                    {
                                        $files_str = substr($files_str, 0, strlen($files_str)-1);
                                    }
                                    $photos = explode(";", $files_str);
                                }
                            }
                        }
                        else
                        {
                            $temp[] = (string)$ival->value;
                        }
                    }

                    $props_display[$b2key] = implode(", ", $temp);
                    //deb::dump($temp);
//            deb::dump($b2val);
                }
//        deb::dump($bval);
            }

            $short_advert_display = $shablons_display[$val['r_id']];
            foreach($props_display as $pkey=>$pval)
            {
                $short_advert_display = str_replace('['.$pkey.']', $pval, $short_advert_display);
            }

            preg_match_all('|\{([a-zA-Z0-9_-]+)\}|siU', $short_advert_display, $matches);
            //deb::dump($matches[1]);
            foreach($matches[1] as $match)
            {
                $short_advert_display = str_replace('{'.$match.'}', $val[$match], $short_advert_display);
            }

            //deb::dump($val);
            //$short_advert_display = str_replace('[[advert_page_url]]');
            $short_advert_display = str_replace('[[mestopolozhenie]]', $val['town_name'], $short_advert_display);
            $date_add_str = date('d-m-Y H:i', $val['date_add']);
            if(time() - $val['date_add'] < 86400)
            {
                $date_add_str = date('Сегодня H:i', $val['date_add']);
            }
            if((time() - $val['date_add'] > 86400) && (time() - $val['date_add'] < 86400*2))
            {
                $date_add_str = date('Вчера H:i', $val['date_add']);
            }
            $short_advert_display = str_replace('[[date_add]]', $date_add_str, $short_advert_display);

            // Генерация ссылки на объяву
            $transliter = new Supporter();
            $advert_page_url = $val['town_transname']."/".$rubriks_all_array[$val['r_id']]->transname."/".$transliter->TranslitForUrl($val['title'])."_".$val['daynumber_id'];
            //deb::dump($advert_page_url);
            $short_advert_display = str_replace('[[advert_page_url]]', Yii::app()->createUrl($advert_page_url), $short_advert_display);

            $props_array[$key]['props_display'] = $short_advert_display;
            $props_array[$key]['photos'] = $photos;


        }

//deb::dump(count($search_adverts));

        /**************************************************************************/
        // Формирование данных для фильтра по свойствам
        if(isset($_GET['mainblock']['r_id']) && intval($_GET['mainblock']['r_id']) > 0)
        {
            $r_id = intval($_GET['mainblock']['r_id']);
            $rubriks_props = RubriksProps::model()->findAll(array(
                'select'=>'*',
                'condition'=>'r_id = '.$r_id . " AND use_in_filter = 1 ",
//                'condition'=>'r_id = '.$r_id . " AND use_in_filter = 1 AND (parent_id = 0 OR all_values_in_filter = 1) ",
                'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
                //'limit'=>'10'
            ));

            $rubriks_props_array = array();
            $rp_id_ids_array = array();
            foreach ($rubriks_props as $mkey=>$mval)
            {
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
                                        AND ps.value >= " . intval($from->value) . " AND ps.value <= " . intval($to->value);
                        }
                        else
                        if(isset($_GET['addfield'][$parent_selector]['from'])
                            && $_GET['addfield'][$parent_selector]['from'] != '')
                        {
                            $from = PropsSprav::model()->findByPk(intval($_GET['addfield'][$parent_selector]['from']));
                            $sql = "SELECT *
                                FROM ". $connection->tablePrefix . "props_sprav ps
                                WHERE ps.rp_id = ".$rubriks_props_array[$parent_ps_id]->rp_id . "
                                    AND ps.value >= " . intval($from->value) ;
                        }
                        else
                        if(isset($_GET['addfield'][$parent_selector]['to'])
                            && $_GET['addfield'][$parent_selector]['from'] != '')
                        {
                            $to = PropsSprav::model()->findByPk(intval($_GET['addfield'][$parent_selector]['to']));
                            $sql = "SELECT *
                            FROM ". $connection->tablePrefix . "props_sprav ps
                            WHERE ps.rp_id = ".$rubriks_props_array[$parent_ps_id]->rp_id . "
                                AND ps.value <= " . intval($to->value);
                        }



                        $command2 = $connection->createCommand($sql);
                        $dataReader2 = $command2->query();

                        while(($row2 = $dataReader2->read())!==false)
                        {
                            $parent_ps_id_array[] = $row2['ps_id'];
                        }

                    }

                }
                //deb::dump($mval->selector);
                //deb::dump($parent_ps_id_array);
                //deb::dump($props_id_hierarhy);

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
//deb::dump($props_sprav_array);

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

                // $rubriks_props_array
                //deb::dump($rubriks_props_array[$pkey]['sort_props_sprav']);
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

                //deb::dump($pval);
                //deb::dump($sort_number_array);

            }


            //deb::dump($props_sprav_sorted_array);
            //deb::dump($rubriks_props_array);
        }

        unset($props_sprav_array);

        // END формирование данных



        $rub_array = Rubriks::get_rublist();

		$this->render('index', array(
            'rubrik_groups'=>$rubrik_groups,
            'search_adverts'=>$search_adverts,
            'props_array'=>$props_array,
            'rub_array'=>$rub_array,
            'props_sprav_sorted_array'=>$props_sprav_sorted_array,
            'rubriks_props_array'=>$rubriks_props_array,
        ));
	}


    public function  actionSearch()
    {
        $url_parts = array();
        if(($t_id = intval($_POST['mainblock']['t_id'])) > 0)
        {
            $town = Towns::model()->findByPk($t_id);
            $url_parts[0] = $town->transname;
        }
        else if (($reg_id = intval($_POST['mainblock']['reg_id'])) > 0)
        {
            $region = Regions::model()->findByPk($reg_id);
            $url_parts[0] = $region->transname;
        }
        else if (($c_id = intval($_POST['mainblock']['c_id'])) > 0)
        {
            $country = Countries::model()->findByPk($c_id);
            $url_parts[0] = $country->transname;
        }
        else
        {
            $url_parts[0] = 'rossiya';
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