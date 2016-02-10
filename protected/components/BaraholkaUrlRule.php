<?php
/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 05.07.15
 * Time: 13:25
 */

class BaraholkaUrlRule extends CBaseUrlRule
{
    public $connectionID = 'db';

    public function createUrl($manager,$route,$params,$ampersand)
    {
        //deb::dump($route);
        //die();
        //$_GET['id'] = 322223;
        //return 'page/default/view';

        return false;  // не применяем данное правило
    }

    public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
    {
        $query = null;
        if(isset(parse_url($request->requestUri)['query']))
        {
            $query = parse_url($request->requestUri)['query'];
        }
        parse_str($query, $query_array);
        //deb::dump($query_array);
        //deb::dump($pathInfo);
        $parts = explode("/", $pathInfo);

        // Проверка на старую ссылку
        //deb::dump($parts);
        //die();
        if(isset($parts[0]) && ($parts[0] == 'board' || $parts[0] == 'boardreg'))
        {
            //deb::dump($parts[0]);
            $_GET = null;
            if($parts[0] == 'board')
            {
                if(isset($parts[1]))
                {
                    $town = explode("-", $parts[1]);
                    if(intval($town[1]) > 0)
                    {
                        $_GET['old_t_id'] = intval($town[1]);
                    }
                }
            }

            if($parts[0] == 'boardreg')
            {
                if(isset($parts[1]))
                {
                    $region = explode("-", $parts[1]);
                    if(intval($region[1]) > 0)
                    {
                        $_GET['old_reg_id'] = intval($region[1]);
                    }
                }
            }

            if(isset($parts[2]))
            {
                $rubrik = explode("-", $parts[2]);
                if(intval($rubrik[1]) > 0)
                {
                    $_GET['old_r_id'] = intval($rubrik[1]);
                }
            }


            if(isset($_GET['old_reg_id']) || isset($_GET['old_t_id']))
            {
                $controller_action_url = 'advert/oldreglistredirect';
                return $controller_action_url;
            }

        }
        else
        if(isset($parts[0]) && count($parts) == 1 && strpos($parts[0], '.html'))
        {

            if(preg_match('|_([0-9]{13})\.html|siU', $parts[0], $match))
            {
                $_GET = null;
                $_GET['daynumber_id'] = $match[1];
                $controller_action_url = 'advert/oldadvertredirect';
                return $controller_action_url;
            }

            if(preg_match('|_([0-9]{7})\.html|siU', $parts[0], $match))
            {
                $_GET = null;
                $_GET['n_id'] = $match[1];
                $controller_action_url = 'advert/oldadvertredirectbyid';
                return $controller_action_url;
            }


        }
        /////////////////////////

        if($parts[0] == '')
        {
            return "/";
        }

        $params = array();

        if($parts[0] == 'all')
        {
            $_GET['mesto_id'] = 0;
        }
        else
        if($town = Towns::model()->findByAttributes(array('transname'=>$parts[0])))
        {
            //$_GET['t_id'] = $town->t_id;

            /*
            $_GET['mainblock']['t_id'] = $town->t_id;
            $_GET['mainblock']['reg_id'] = $town->reg_id;
            $_GET['mainblock']['c_id'] = $town->c_id;
            */

            $_GET['mesto_id'] = 't_'.$town->t_id;
        }
        else
        if($region = Regions::model()->findByAttributes(array('transname'=>$parts[0])))
        {
            //$_GET['reg_id'] = $region->reg_id;
            /*
            $_GET['mainblock']['reg_id'] = $region->reg_id;
            $_GET['mainblock']['c_id'] = $region->c_id;
            */

            $_GET['mesto_id'] = 'reg_'.$region->reg_id;
        }
        else
        if($country = Countries::model()->findByAttributes(array('transname'=>$parts[0])))
        {
            //$_GET['c_id'] = $country->c_id;
            /*
            $_GET['mainblock']['c_id'] = $country->c_id;
            */

            $_GET['mesto_id'] = 'c_'.$country->c_id;
        }
        /*
        // Патч, Россия если не определен регион
        else if ($parts[0] == 'allregions')
        {
            $_GET['mainblock']['c_id'] = 185;
        }
        */
        else
        {
            return false;
        }
        $controller_action_url = 'filter/index';

        if(isset($parts[1]) && $parts[1] != '')
        {
            $rubrik = Rubriks::model()->findByAttributes(
                array('transname'=>$parts[1]) );
            if($rubrik && $rubrik->parent_id > 0)
            {
                //$_GET['r_id'] = $rubrik->r_id;
                $_GET['mainblock']['r_id'] = $rubrik->r_id;
            }
            else if($rubrik && $rubrik->parent_id == 0)
            {
                $_GET['mainblock']['r_id'] = $rubrik->r_id;
                $_GET['parent_r_id'] = $rubrik->r_id;
            }
            else
            {
                return false;
            }
        }

        // Фильтр по свойствам
        if(isset($parts[2]) && $parts[2] != '')
        {


            $j=2;
            while(isset($parts[$j]))
            {
                $_GET['prop'][$j] = $parts[$j];
                $j++;
            }

//deb::dump($_GET['prop']);
            // Если третий сегмент есть, и последняя его часть состоит только из 13-ти цифр -
            // то это ссылка на конкретное объявление
            if(count($parts) == 3)
            {
                $parts_third = explode("_", $parts[2]);
                $last_part = $parts_third[count($parts_third)-1];
                preg_match('|[0-9]+?|siU', $last_part, $match);
                //deb::dump($match[0]);
                if(count($parts_third)>1 && $match[0] == $last_part && strlen($last_part) == 13)
                {
                    $_GET = null;
                    $_GET['daynumber_id'] = $last_part;
                    $_GET = array_merge($_GET, $query_array);
//                    deb::dump($_GET);
                    $controller_action_url = 'advert/viewadvert';
                    //deb::dump($_GET);
                    return $controller_action_url;
                }
            }
            //deb::dump($params);


        }



        //deb::dump($_GET);
        //deb::dump($controller_action_url.$params_str);
        $_GET = array_merge($_GET, $query_array);
        //deb::dump($_GET);
        return $controller_action_url;

        //return false;  // не применяем данное правило
    }


}