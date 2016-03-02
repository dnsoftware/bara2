<?php

class AdminadvertController extends Controller
{
	public function actionIndex()
	{

        $query_delta = 0;
        $page = intval($_GET['mainblock']['page']);
        $col_on_page = 500;
        if(isset($_GET['mainblock']['col_on_page']) && intval($_GET['mainblock']['col_on_page'] > 0))
        {
            $col_on_page = intval($_GET['mainblock']['col_on_page']);
        }

        $connection=Yii::app()->db;

        $parts = array();
        if(isset($_GET['mesto_id'])) // $_POST['region_id'] может содержать город, страну или регион
        {
            $parts = explode("_", $_GET['mesto_id']);
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

        // Показ архивных
        $expire_sql = " ";

        // Местоположение
        $mesto_sql = " 1 ";

        if($mesto_isset_tag && $mselector == 'c')
        {
            $mesto_sql = " n.c_id = ".intval($m_id);
        }
        if($mesto_isset_tag && $mselector == 'reg')
        {
            $mesto_sql = " n.reg_id = ".intval($m_id);
        }
        if($mesto_isset_tag && $mselector == 't')
        {
            $mesto_sql = " n.t_id = ".intval($m_id);
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
//deb::dump($rubriks_props);
        // Старая рубрика
        $old_rubrik_sql = " AND 1 ";
        if(isset($_GET['mainblock']['old_r_id']) && $_GET['mainblock']['old_r_id'] != '')
        {
            $old_rubrik_sql = " AND old_r_id = ".intval($_GET['mainblock']['old_r_id']). " ";
        }

        // Подстрока поиска
        $q_sql = " ";
        if(isset($_GET['params']['q']) && trim($_GET['params']['q']) != '')
        {
            $q_sql = " AND ( n.title LIKE :q_sql OR n.notice_text LIKE  :q_sql ) ";
        }

        // Код объявления daynumber_id
        $daynumber_sql = " ";
        if(isset($_GET['mainblock']['daynumber_id']) && trim($_GET['mainblock']['daynumber_id']) != '')
        {
            $daynumber_sql = " AND daynumber_id = '".trim($_GET['mainblock']['daynumber_id'])."' ";
        }

        // Email пользователя
        $user_email_sql = " ";
        if(isset($_GET['mainblock']['user_email']) && trim($_GET['mainblock']['user_email']) != '')
        {
            if($search_u_id_row = User::model()->findByAttributes(array('email'=>trim($_GET['mainblock']['user_email']))))
            {
                $user_email_sql = " AND u_id = ".$search_u_id_row->id;
            }
            else
            {
                $_GET['mainblock']['user_email'] = '';
            }
        }

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

        // Переводим переменные из $_GET['prop'] в $_GET['addfield']
        if(isset($_GET['prop']) && count($_GET['prop']) > 0)
        {
            foreach($_GET['prop'] as $gpkey=>$gpval)
            {
                $rp_selector = $pubriks_props_array[$rubriks_poryadok_props_array[$gpkey]]->selector;
                if($prop_row = PropsSprav::model()->findByAttributes(array('rp_id'=>$rubriks_poryadok_props_array[$gpkey], 'transname'=>$gpval)))
                {
                    if($pubriks_props_array[$rubriks_poryadok_props_array[$gpkey]]->filter_type == 'range')
                    {
                        $_GET['addfield'][$rp_selector]['from'] = $prop_row->ps_id;
                        $_GET['addfield'][$rp_selector]['to'] = $prop_row->ps_id;
                    }
                    else
                    {
                        $_GET['addfield'][$rp_selector] = $prop_row->ps_id;
                    }
                }
            }
            //deb::dump($rubriks_poryadok_props_array);
        }
        // КОНЕЦ Переводим переменные из $_GET['prop'] в $_GET['addfield']

        // Переводим переменные из $_GET['addfield'] в $_GET['prop']
        // Только для тех кто участвует в иерархии
        if(isset($_GET['addfield']) && count($_GET['addfield']) > 0 )
        {
            foreach($_GET['addfield'] as $akey=>$aval)
            {
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

        $search_adverts = array();  // Найденные объявы


        if( /*count($_GET['prop']) > 0 || */(isset($_GET['addfield']) && count($_GET['addfield']) > 0 ) )
        {
            $props_sprav = PropsSprav::model()->findAll(array('condition'=>'rp_id IN ('.implode(", ", $rp_ids).')'));
            $props_route_items = array();
            $props_route_items_by_id = array();
            foreach($props_sprav as $pkey=>$pval)
            {
                $props_route_items[$rubriks_props_poryadok_array[$pval->rp_id]][$pval->transname] = $pval;
                $props_route_items_by_id[$pval->ps_id] = $pval;
            }

            foreach($_GET['prop'] as $pkey=>$pval)
            {
                if(isset($props_route_items[$pkey][$pval]))
                {
                    $ps_id = $props_route_items[$pkey][$pval]->ps_id;
                }
            }
            $current_ps_id = $ps_id;

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
                            //deb::dump($gval);
                            if(count($gval > 0))
                            {
                                foreach($gval as $g2key=>$g2val)
                                {
                                    $gval[$g2key] = intval($g2val);
                                }
                                $from_tables_array[] = $connection->tablePrefix . "notice_props n".$i;
                                $where_n_array[] = " AND n".$i.".rp_id = ".$switch_rp_id;
                                $where_n_array[] = " AND n".$i.".n_id = n".($i+1).".n_id ";
                                $where_filter_array[] = "n".$i.".ps_id IN (".implode(", ", $gval).")";
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

                $from_tables_sql = implode(", ", $from_tables_array);
                unset($where_n_array[count($where_n_array)-1]);
                $where_n = implode(" ", $where_n_array);
                $where_filter_sql = implode(" AND ", $where_filter_array);
                //deb::dump($from_tables_sql);
                //deb::dump($where_n);

                // Полный запрос
                $rubrik_prop_sql = str_replace("r_id", "n.r_id", $rubrik_sql);
                $sql_full = "SELECT DISTINCT n.n_id
                        FROM ". $connection->tablePrefix . "notice n,
                        ".$from_tables_sql.",
                        ". $connection->tablePrefix . "towns t,
                        ". $connection->tablePrefix . "users u
                        WHERE 1 AND $expire_sql
                        $mesto_sql $old_rubrik_sql AND $rubrik_prop_sql AND
                        $where_filter_sql
                        ".$where_n.$q_sql.$daynumber_sql.$user_email_sql."
                        AND n1.n_id = n.n_id
                        AND n.t_id = t.t_id
                        AND n.u_id = u.id
                        ORDER BY n.date_add DESC " ;



//deb::dump($sql_full);

                $command = $connection->createCommand($sql_full . " LIMIT 0, 2000 "); // патч по количеству, иначе вылетает изза нехватки памяти

                if(isset($_GET['params']['q']) && strlen($_GET['params']['q']) > 0)
                {
                    $substr = "%".$_GET['params']['q']."%";
                    $command->bindParam(":q_sql", $substr, PDO::PARAM_STR);
                }
                $dataReader = $command->query();
                $rowcount = $dataReader->getRowCount();
                $col_pages = ceil($rowcount / $col_on_page);

                // Постраничный запрос
                if($page == 0)
                {
                    $page = 1;
                }
                $start = ($page - 1)*$col_on_page;
                $stop = $col_on_page;

                $sql = "SELECT DISTINCT n.*, t.name town_name, t.transname town_transname,
                             u.email useremail
                        FROM ". $connection->tablePrefix . "notice n,
                        ".$from_tables_sql.",
                        ". $connection->tablePrefix . "towns t,
                        ". $connection->tablePrefix . "users u
                        WHERE 1 AND $expire_sql
                        $mesto_sql $old_rubrik_sql AND $rubrik_prop_sql AND
                        $where_filter_sql
                        ".$where_n.$q_sql.$daynumber_sql.$user_email_sql."
                        AND n1.n_id = n.n_id
                        AND n.t_id = t.t_id
                        AND n.u_id = u.id
                        ORDER BY n.date_add DESC
                        LIMIT $start, $stop";

//deb::dump($col_pages);
                $start_time = microtime();

                $command = $connection->createCommand($sql);
                if(isset($_GET['params']['q']) && strlen($_GET['params']['q']) > 0)
                {
                    $substr = "%".$_GET['params']['q']."%";
                    $command->bindParam(":q_sql", $substr, PDO::PARAM_STR);
                }
                $dataReader = $command->query();

                $stop_time = microtime();
                $query_delta = $stop_time - $start_time;

                while(($row = $dataReader->read())!==false)
                {
                    $search_adverts[$row['n_id']] = $row;
                }
//deb::dump($sql);
                //deb::dump($search_adverts);
                //die();

            }
            else    // Нет записей удовлетворяющих критерию
            {

            }

            $rubriks = Rubriks::get_simple_rublist();


        }
        // Если поиск только по местоположению/рубрике - простой запрос
        else
        {

            $mesto_rub_sql = str_replace(" n.", " t.", $mesto_sql);
            $q_sql = str_replace(" n.", " t.", $q_sql);

            $adverts_full = Notice::model()->with('town')->findAll(
                array(
                    'select'=>'n_id, town.name as town_name, town.transname as town_transname',
                    'condition'=>' 1 AND '.$expire_sql.
                        $mesto_rub_sql." $old_rubrik_sql AND ".$rubrik_sql.$q_sql.$daynumber_sql.$user_email_sql,
                    'order'=>'t.date_add DESC',
                    'limit'=>2000,
                    'params'=>array(':q_sql'=>'%'.$_GET['params']['q'].'%')
                )
            );

            $rowcount = count($adverts_full);
            $col_pages = ceil($rowcount / $col_on_page);

            // Постраничный запрос
            if($page == 0)
            {
                $page = 1;
            }
            $start = ($page - 1)*$col_on_page;
            $adverts = Notice::model()->with('town')->with('user')->findAll(
                array(
                    'select'=>'*, town.name as town_name, town.transname as town_transname, user.email as user_email',
                    'condition'=>' 1 AND '.$expire_sql.
                        $mesto_rub_sql." $old_rubrik_sql AND   ".$rubrik_sql.$q_sql.$daynumber_sql.$user_email_sql,
                    'order'=>'t.date_add DESC',
                    'limit'=>$col_on_page,
                    'offset'=>$start,
                    'params'=>array(':q_sql'=>'%'.$_GET['params']['q'].'%')
                )
            );

            $rubriks = Rubriks::get_simple_rublist();

            foreach ($adverts as $akey=>$aval)
            {
                $search_adverts[$aval->n_id] = $aval->attributes;
                $search_adverts[$aval->n_id]['town_name'] = $aval->town['name'];
                $search_adverts[$aval->n_id]['town_transname'] = $aval->town['transname'];
                $search_adverts[$aval->n_id]['useremail'] = $aval->user['email'];

                if($rubriks[$aval->r_id]->title_advert_shablon != '' && $aval['old_base_tag'] == 0)
                {
                    $search_adverts[$aval->n_id]['title'] = AdvertController::MakeTitleByShablon($aval, $rubriks[$aval->r_id]->title_advert_shablon);
                }
            }

//deb::dump(count($search_adverts));


/*
            // Разбивка выбранного раздела на подгруппы
            // Если выбранный раздел является родительской рубрикой
            if(isset($_GET['parent_r_id']))
            {
                $rubrik_simple_sql = " r.r_id IN (". implode(", ", $subrubs_ids).") ";

                $sql = "SELECT r.name, r.transname, COUNT(n.n_id) cnt
                        FROM
                        ". $connection->tablePrefix . "notice n,
                        ". $connection->tablePrefix . "rubriks r
                        WHERE 1 AND $expire_sql
                        ".$mesto_sql." $old_rubrik_sql AND ".$rubrik_simple_sql."
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
            else if (isset($_GET['mainblock']['r_id']) && $_GET['mainblock']['r_id'] != '' && !isset($_GET['mainblock']['parent_r_id']) )
            {
//deb::dump($_GET['mainblock']['r_id']);
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
                        WHERE 1 $old_rubrik_sql AND $expire_sql
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
                        WHERE 1 AND $expire_sql
                        ".$mesto_sql." $old_rubrik_sql AND r.parent_id = 0 AND r2.parent_id = r.r_id
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
*/


        }



        // Подготовка данных для отображения
        //// Шаблоны отображения из рубрик
        $shablons_display = Rubriks::GetShablonsDisplay();

        $rubriks_all_array = Rubriks::get_all_subrubs();

        $props_array = Notice::DisplayAdvertsList($search_adverts, $shablons_display, $rubriks_all_array);


//deb::dump(count($search_adverts));

        /**************************************************************************/
        // Формирование данных для фильтра по свойствам
        $filter_controller = new FilterController('filter_controller');
        $ret = $filter_controller->MakeDataFilter();
        $props_sprav_sorted_array = $ret['props_sprav_sorted_array'];
        $rubriks_props_array = $ret['rubriks_props_array'];
        /*************** END формирование данных ********************************/

        $rub_array = Rubriks::get_rublist(true);

        $props_sprav_sorted_array = $ret['props_sprav_sorted_array'];
        $rubriks_props_array = $ret['rubriks_props_array'];

        $rub_old_array = RubriksOld::get_rublist();
//deb::dump($rub_old_array);
        $transliter = new Supporter();

        $this->render('index', array(
            'adverts'=>$search_adverts,
            'rubriks'=>$rubriks,
            'props_array'=>$props_array,
            'transliter'=>$transliter,

            //для формы поиска
            'rub_array'=>$rub_array,
            'mselector'=>$mselector,
            'm_id'=>$m_id,
            'props_sprav_sorted_array'=>$props_sprav_sorted_array,
            'rubriks_props_array'=>$rubriks_props_array,
            'rubriks_all_array'=>$rubriks_all_array,
            'rub_old_array'=>$rub_old_array,

            'col_on_page'=>$col_on_page,
            'page'=>$page,
            'col_pages'=>$col_pages

        ));

/**********************************************************************
        $rubriks = Rubriks::get_simple_rublist();

        $adverts = Notice::model()->findAll(array(
            'select'=>'*',
            'order'=>'date_add DESC ',
            'limit'=>100
        ));

        $search_adverts = array();
        $props_array = array();
        foreach($adverts as $key=>$val)
        {
            $search_adverts[$val->n_id] = $val->attributes;

            $props_display = array();
            $photos = array();
            $xml = new SimpleXMLElement($val->props_xml);
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
                                    //deb::dump($photos);
                                }
                            }
                        }
                        else
                        {
                            $temp[] = (string)$ival->value;
                        }
                    }

                    $props_display[$b2key] = implode(", ", $temp);
                    //deb::dump($props_display);
//            deb::dump($b2val);
                }
//        deb::dump($bval);
            }

            $props_array[$val->n_id]['photos'] = $photos;
            //deb::dump($props_array);
        }

        //////////////////////
        $parts = array();
        if(isset($_GET['mesto_id']))
        {
            $parts = explode("_", $_GET['mesto_id']);
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


        // Подготовка данных для отображения
        //// Шаблоны отображения из рубрик
        $shablons_display = Rubriks::GetShablonsDisplay();
        $rubriks_all_array = Rubriks::get_all_subrubs();
        $props_array = Notice::DisplayAdvertsList($search_adverts, $shablons_display, $rubriks_all_array);


        $rub_array = Rubriks::get_rublist();
        $filter_controller = new FilterController('filter_controller');
        $ret = $filter_controller->MakeDataFilter();
        $props_sprav_sorted_array = $ret['props_sprav_sorted_array'];
        $rubriks_props_array = $ret['rubriks_props_array'];

        $this->render('index', array(
            'adverts'=>$adverts,
            'rubriks'=>$rubriks,
            'props_array'=>$props_array,

            //для формы поиска
            'rub_array'=>$rub_array,
            'mselector'=>$mselector,
            'm_id'=>$m_id,
            'props_sprav_sorted_array'=>$props_sprav_sorted_array,
            'rubriks_props_array'=>$rubriks_props_array,
            'rubriks_all_array'=>$rubriks_all_array,

        ));

*****************************************************************************************/


	}


    // Активация/деактивация объявы
    public function actionSetadvert_act()
    {
        $n_id = intval($_POST['n_id']);

        $advert = Notice::model()->findByPk($n_id);
        $ret = '';

        if($advert->active_tag == 1)
        {
            $advert->active_tag = 0;
            $ret = 'deact';
        }
        else
        if($advert->active_tag == 0)
        {
            $advert->active_tag = 1;
            $ret = 'act';
        }

        if($advert->save())
        {
            echo $ret;
        }

    }

    // Верификация/деверификация объявы
    public function actionSetadvert_ver()
    {
        $n_id = intval($_POST['n_id']);

        $advert = Notice::model()->findByPk($n_id);
        $ret = '';

        if($advert->verify_tag == 1)
        {
            $advert->verify_tag = 0;
            $ret = 'deact';
        }
        else
            if($advert->verify_tag == 0)
            {
                $advert->verify_tag = 1;
                $ret = 'act';
            }

        if($advert->save())
        {
            echo $ret;
        }
        else
        {
            echo deb::dump($advert->getErrors());
        }

    }

    // Пометка удаленности объявы
    public function actionSetadvert_del()
    {
        $n_id = intval($_POST['n_id']);

        $advert = Notice::model()->findByPk($n_id);
        $ret = '';

        if($advert->deleted_tag == 1)
        {
            $advert->deleted_tag = 0;
            $ret = 'act';
        }
        else
            if($advert->deleted_tag == 0)
            {
                $advert->deleted_tag = 1;
                $ret = 'deact';
            }

        if($advert->save())
        {
            echo $ret;
        }

    }


    // Удаление навсегда
    public function actionAdvert_kill()
    {
        $part_path = '/'.Yii::app()->params['photodir'].'/';
        $n_id = intval($_POST['n_id']);
        //$n_id = intval($_GET['n_id']);

        if($advert = Notice::model()->findByPk($n_id))
        {
            $props_relate = RubriksProps::model()->with('notice_props')->find(array(
                'select'=>'*',
                'condition'=>'r_id='.$advert->r_id . " AND n_id=".$advert->n_id . " AND type_id = 'photo_block' ",
                'order'=>'t.hierarhy_tag DESC, t.hierarhy_level ASC, t.display_sort, t.rp_id'
            ));

            $photo_list = $props_relate->notice_props[0]['hand_input_value'];
            $photo_array = Notice::getImageArray($photo_list);
            //deb::dump($photo_array);
            if(count($photo_array) > 0)
            {
                foreach($photo_array as $pkey=>$pval)
                {
                    $curr_dir = Notice::getPhotoDirMake(Yii::app()->params['photodir'], $pval);
                    $output_dir = $_SERVER['DOCUMENT_ROOT']."/".Yii::app()->params['photodir']."/".$curr_dir."/";

                    @unlink ( $output_dir.$pval);
                }
            }

            $res = NoticeProps::model()->deleteAll(array(
                'condition'=>'n_id = '.$n_id
            ));

            $advert->delete();

            echo "del";
        }

    }


    // Формирование списка свойсв рубрики для формы панели групповой смены свойств
    public function actionGetPanelProps()
    {
        $connection = Yii::app()->db;

        if(isset($_POST['panel']))
        {
            Yii::app()->session['panel'] = $_POST['panel'];
        }
        $panel = Yii::app()->session['panel'];
        $r_id = intval($panel['r_id']);

        $rubriks_props = RubriksProps::model()->findAll(array(
            'select'=>'*',
            'condition'=>'r_id = '.$r_id . ' AND
                    (vibor_type = "selector"
                     OR vibor_type = "listitem"
                     OR vibor_type = "autoload"
                     OR vibor_type = "autoload_with_listitem" )',
            'order'=>'hierarhy_tag DESC, hierarhy_level ASC, display_sort, rp_id',
            //'limit'=>'10'
        ));


        $rprops_array = array();

        foreach($rubriks_props as $pkey=>$pval)
        {
            if($pval->parent_id <= 0)
            {
                $prop_items = PropsSprav::model()->findAll(array(
                    'select'=>'*',
                    'condition'=>'rp_id = '. $pval->rp_id,
                    'order'=>'sort_number'
                ));

                $temp = $pval->attributes;
                foreach($prop_items as $pikey=>$pival)
                {
                    //deb::dump($pival->ps_id);
                    $temp['sprav_items'][$pival->ps_id] = $pival->attributes;
                }

                $rprops_array[$pval->rp_id] = $temp;
            }
            else
            {
                $parent_rubriks_props = RubriksProps::model()->findByPk($pval->parent_id);

                $temp = $pval->attributes;

                if(isset($_SESSION['panel'][$parent_rubriks_props->selector])
                    && intval($_SESSION['panel'][$parent_rubriks_props->selector]) > 0)
                {
//            deb::dump($parent_rubriks_props);
//            die();
                    $parent_ps_id = intval($_SESSION['panel'][$parent_rubriks_props->selector]);

                    $sql = "SELECT *
                        FROM
                        ". $connection->tablePrefix . "props_relations pr,
                        ". $connection->tablePrefix . "props_sprav ps
                        WHERE pr.parent_ps_id = $parent_ps_id AND pr.child_ps_id = ps.ps_id
                                AND ps.rp_id = ".$pval->rp_id."
                        ORDER BY ps.sort_number " ;
                    //deb::dump($sql);
                    $command = $connection->createCommand($sql);
                    $dataReader = $command->query();
                    while(($row = $dataReader->read())!==false)
                    {
                        $temp['sprav_items'][$row['ps_id']] = $row;
                    }

                    $rprops_array[$pval->rp_id] = $temp;
                }

            }



        }

//deb::dump(Yii::app()->session['panel']);
        ?>
        <div style="margin-top: 5px;">Свойства:</div>
        <div style="border: #999 solid 1px; padding: 5px;">
        <?
        // Выводим сформированные списки
        foreach($rprops_array as $rkey=>$rval)
        {
            ?>
            <div style="float: left;">
            <?= $rval['name'];?>:<br>
            <select class="prop_item" name="panel[<?= $rval['selector'];?>]" style="width: 200px;">
                <option value="0">-- выберите свойство --</option>
            <?
            if(count($rval['sprav_items']) > 0)
            {
                foreach($rval['sprav_items'] as $ikey=>$ival)
                {
                    $selected = " ";
                    if($ival['ps_id'] == $_SESSION['panel'][$rval['selector']])
                    {
                        $selected = " selected ";
                    }
                ?>
                    <option <?= $selected;?> value="<?= $ival['ps_id'];?>"><?= $ival['value'];?></option>
                <?
                }
            }
            ?>
            </select>
            </div>
            <?
        }
        ?>
            <br clear="all">
        </div>
        <?
        //deb::dump($rprops_array);
        ?>

        <script>
            $('.prop_item').change(function(){
                GetPanelProps();
            });
        </script>
        <?

    }


    // Установка рубрики и свойств для одной объявы
    public function actionSetnewprops()
    {
        //sleep(1);

        $n_id = intval($_POST['n_id']);
        $advert = Notice::model()->findByPk($n_id);
        if($advert->validate())
        {

            $panel = $_POST['panel'];

            $r_id = intval($panel['r_id']);
            $rubrik_row = Rubriks::model()->findByPk($r_id);

            if($r_id > 0)
            {
                $props_array = array();
                foreach($panel as $pkey=>$pval)
                {
                    if($pkey == 'r_id')
                    {
                        continue;
                    }

                    if($pval > 0)
                    {
                        $rubprop = RubriksProps::model()->findByAttributes(array('selector'=>$pkey));
                        //deb::dump($rubprop);
                        $temprow = array();
                        $temprow['n_id'] = $n_id;
                        $temprow['rp_id'] = $rubprop->rp_id;
                        $temprow['ps_id'] = $pval;
                        $temprow['hand_input_value'] = '';
                        $props_array[] = $temprow;
                    }
                }

                // Удаляем и меняем

                // Нужно сохранить свойство Фотографии,
                // Для этого получим значение старого свойства notice_props,
                // и присвоим ему rp_id = rp_id нового rubriks_props
                $old_photo_rubriks_prop_photoblock = RubriksProps::model()->find(array(
                    'select'=>'*',
                    'condition'=>'r_id = '. $advert->r_id . " AND vibor_type = 'photoblock' "
                ));

                $old_notice_props_photoblock = NoticeProps::model()->findByAttributes(array(
                    'n_id'=>$n_id,
                    'rp_id'=>$old_photo_rubriks_prop_photoblock->rp_id
                ));

                $new_photo_rubriks_prop_photoblock = RubriksProps::model()->find(array(
                    'select'=>'*',
                    'condition'=>'r_id = '. $r_id . " AND vibor_type = 'photoblock' "
                ));

                // Для свойства "фотоблок" есть только одна запись в таблице props_sprav
                // Находим ее для получения значения ps_id
                $new_props_sprav_photoblock = PropsSprav::model()->findByAttributes(array(
                    'rp_id'=>$new_photo_rubriks_prop_photoblock->rp_id
                ));

                if($old_photo_rubriks_prop_photoblock && $new_photo_rubriks_prop_photoblock)
                {
                    $temprow = array();
                    $temprow['n_id'] = $n_id;
                    $temprow['rp_id'] = $new_photo_rubriks_prop_photoblock->rp_id;
                    $temprow['ps_id'] = $new_props_sprav_photoblock->ps_id;
                    $temprow['hand_input_value'] = $old_notice_props_photoblock->hand_input_value;
                    $props_array[] = $temprow;

                }

                NoticeProps::model()->deleteAll('n_id = '.$n_id);

                $advert->r_id = $r_id;
                $advert->parent_r_id = $rubrik_row->parent_id;
                $advert->save();

                if(count($props_array) > 0)
                {
                    foreach($props_array as $pkey=>$pval)
                    {
                        $notice_prop = new NoticeProps();
                        $notice_prop->n_id = $pval['n_id'];
                        $notice_prop->rp_id = $pval['rp_id'];
                        $notice_prop->ps_id = $pval['ps_id'];
                        $notice_prop->hand_input_value = $pval['hand_input_value'];
                        $notice_prop->save();
                    }
                }

                // Формируем xml
                AdvertController::PropsXmlGenerate($n_id);

                $ret['status'] = 'ok';
                $ret['message'] = 'Корректировка прошла успешно!';


            }
            else
            {
                $ret['status'] = 'error';
                $ret['message'] = 'Не выбрана рубрика!';
            }

        }
        else
        {
            $errors = $advert->getErrors();
            $errors_array = array();
            foreach($errors as $ekey=>$eval)
            {
                $errors_array[] = $eval[0];
            }

            $ret['status'] = 'error';
            $ret['message'] = implode(', ', $errors_array);
        }

        echo json_encode($ret);
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