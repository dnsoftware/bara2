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
        //$_GET['id'] = 322223;
        //return 'page/default/view';

        return false;  // не применяем данное правило
    }

    public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
    {
        //deb::dump($pathInfo);
        $parts = explode("/", $pathInfo);
        //deb::dump($parts);

        if($parts[0] == '')
        {
            return "/";
        }

        $params = array();
        if($town = Towns::model()->findByAttributes(array('transname'=>$parts[0])))
        {
            $_GET['t_id'] = $town->t_id;
        }
        else
        if($region = Regions::model()->findByAttributes(array('transname'=>$parts[0])))
        {
            $_GET['reg_id'] = $region->reg_id;
        }
        else
        if($country = Countries::model()->findByAttributes(array('transname'=>$parts[0])))
        {
            $_GET['c_id'] = $country->c_id;
        }
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
                $_GET['r_id'] = $rubrik->r_id;
            }
            else if($rubrik && $rubrik->parent_id == 0)
            {
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
                    $controller_action_url = 'advert/viewadvert';
                    return $controller_action_url;
                }
            }
            //deb::dump($params);
        }

        //deb::dump($_GET);
        //deb::dump($controller_action_url.$params_str);
        return $controller_action_url;

        //return false;  // не применяем данное правило
    }


}