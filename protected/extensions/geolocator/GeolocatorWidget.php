<?php
/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 11.09.15
 * Time: 12:06
 */

class GeolocatorWidget extends CWidget
{
    /**
     * Запуск виджета
     */
    public function run()
    {
        /*
        $countries = Countries::model()->findAll();
        $regions = Regions::model()->findAll(array(
            'condition'=>'c_id=1',
            'order'=>'name'
        ));
        */

        $path = Yii::getPathOfAlias('webroot');
        $SxGeo = new SxGeo($path.'/sypexgeo/SxGeoCity.dat');
        $ip = $_SERVER['REMOTE_ADDR'];
        if($ip == '127.0.0.1')
        {
            $ip = '178.213.108.155';
        }
        //$ip = '178.213.108.155';
        //$ip = '176.31.32.106';
        //$ip = '1761.31.32.106';
        $geodata = $SxGeo->getCityFull($ip);
        //deb::dump($geodata);


        $cookie_mytown_handchange_tag = Yii::app()->request->cookies->contains('geo_mytown_handchange_tag') ?
            Yii::app()->request->cookies['geo_mytown_handchange_tag']->value : 0;

        $cookie['mytown'] = Yii::app()->request->cookies->contains('geo_mytown') ?
            Yii::app()->request->cookies['geo_mytown']->value : 0;
        $cookie['myregion'] = Yii::app()->request->cookies->contains('geo_myregion') ?
            Yii::app()->request->cookies['geo_myregion']->value : 0;
        $cookie['mycountry'] = Yii::app()->request->cookies->contains('geo_mycountry') ?
            Yii::app()->request->cookies['geo_mycountry']->value : 0;

        $cookie['mytown_name'] = Yii::app()->request->cookies->contains('geo_mytown_name') ?
            Yii::app()->request->cookies['geo_mytown_name']->value : '';
        $cookie['myregion_name'] = Yii::app()->request->cookies->contains('geo_myregion_name') ?
            Yii::app()->request->cookies['geo_myregion_name']->value : '';
        $cookie['mycountry_name'] = Yii::app()->request->cookies->contains('geo_mycountry_name') ?
            Yii::app()->request->cookies['geo_mycountry_name']->value : '';
//deb::dump($cookie);
        // Если титул - проверяем если установлен тег ручной смены - редирект на зафиксированный регион
        // если тег не установлен - вычисляем регион, выставляем его в куки и редиректим туда
        $cur_url = Yii::app()->getRequest()->getRequestUri();

        if($cookie_mytown_handchange_tag == 1)
        {
            // Если титул и куки уже определены вручную
            if ($cur_url == '/' || $cur_url == '/site/index')
            {
                // если определен город
                if($cookie['mytown'] > 0)
                {
                    if($town = Towns::model()->findByPk($cookie['mytown']))
                    {
                        header('Location: /'.$town->transname);
                    }
                    else
                    {
                        // Редирект на все регионы
                        header('Location: /russia');
                    }
                }
                else
                if($cookie['myregion'] > 0)
                {
                    if($region = Regions::model()->findByPk($cookie['myregion']))
                    {
                        header('Location: /'.$region->transname);
                    }
                    else
                    {
                        // Редирект на все регионы
                        header('Location: /russia');
                    }
                }
                else
                if($cookie['mycountry'] > 0)
                {
                    if($country = Countries::model()->findByPk($cookie['mycountry']))
                    {
                        header('Location: /'.$country->transname);
                    }
                    else
                    {
                        // Редирект на все регионы
                        header('Location: /russia');
                    }
                }
            }

            /*
            if($cookie['mytown'] > 0 && $cookie['myregion'] == 0)
            {
                $cookie = new CHttpCookie('geo_myregion', $geodata['region']['id']);
                $cookie->expire = time() + 86400*30;
                Yii::app()->request->cookies['geo_myregion'] = $cookie;

                $cookie = new CHttpCookie('geo_myregion', $geodata['region']['id']);
                $cookie->expire = time() + 86400*30;
                Yii::app()->request->cookies['geo_myregion'] = $cookie;

                $cookie['myregion'] = $geodata['region']['id'];
                $cookie['myregion'] == $geodata['region']['name_ru'];
            }
            */

        }
        // Если куки не определены вручную
        else
        {
            unset(Yii::app()->request->cookies['geo_mytown']);
            unset(Yii::app()->request->cookies['geo_myregion']);
            unset(Yii::app()->request->cookies['geo_mycountry']);
            unset(Yii::app()->request->cookies['geo_mytown_name']);
            unset(Yii::app()->request->cookies['geo_myregion_name']);
            unset(Yii::app()->request->cookies['geo_mycountry_name']);

            // если определен город
            if(isset($geodata['city']) && $geodata['city']['id'] > 0)
            {
                $cookie = new CHttpCookie('geo_mytown', $geodata['city']['id']);
                $cookie->expire = time() + 86400*30;
                Yii::app()->request->cookies['geo_mytown'] = $cookie;

                $cookie = new CHttpCookie('geo_mytown_name', $geodata['city']['name_ru']);
                $cookie->expire = time() + 86400*30;
                Yii::app()->request->cookies['geo_mytown_name'] = $cookie;

                $cookie = new CHttpCookie('geo_myregion', $geodata['region']['id']);
                $cookie->expire = time() + 86400*30;
                Yii::app()->request->cookies['geo_myregion'] = $cookie;

                $cookie = new CHttpCookie('geo_myregion_name', $geodata['region']['name_ru']);
                $cookie->expire = time() + 86400*30;
                Yii::app()->request->cookies['geo_myregion_name'] = $cookie;

                $cookie = new CHttpCookie('geo_mycountry', $geodata['country']['id']);
                $cookie->expire = time() + 86400*30;
                Yii::app()->request->cookies['geo_mycountry'] = $cookie;

                $cookie = new CHttpCookie('geo_mycountry_name', $geodata['country']['name_ru']);
                $cookie->expire = time() + 86400*30;
                Yii::app()->request->cookies['geo_mycountry_name'] = $cookie;

                self::SetGeolocatorCookie('geo_mytown_handchange_tag', 1, 86400*30);

                if ($cur_url == '/' || $cur_url == '/site/index')
                {
                    if($city = Towns::model()->findByPk($geodata['city']['id']))
                    {
                        header('Location: /'.$city->transname);
                    }
                    else
                    {
                        // Редирект на все регионы
                        header('Location: /russia');
                    }
                }

            }
            else
            if(isset($geodata['region']) && $geodata['region']['id'] > 0)
            {
                $cookie = new CHttpCookie('geo_myregion', $geodata['region']['id']);
                $cookie->expire = time() + 86400*30;
                Yii::app()->request->cookies['geo_myregion'] = $cookie;

                self::SetGeolocatorCookie('geo_mytown_handchange_tag', 1, 86400*30);

                $cookie = new CHttpCookie('geo_myregion_name', $geodata['region']['name_ru']);
                $cookie->expire = time() + 86400*30;
                Yii::app()->request->cookies['geo_myregion_name'] = $cookie;

                if ($cur_url == '/' || $cur_url == '/site/index')
                {
                    if($region = Regions::model()->findByPk($geodata['region']['id']))
                    {
                        header('Location: /'.$region->transname);
                    }
                    else
                    {
                        // Редирект на все регионы
                        header('Location: /russia');
                    }
                }
            }
            else
            if(isset($geodata['country']) && $geodata['country']['id'] > 0)
            {
                $cookie = new CHttpCookie('geo_mycountry', $geodata['country']['id']);
                $cookie->expire = time() + 86400*30;
                Yii::app()->request->cookies['geo_mycountry'] = $cookie;

                self::SetGeolocatorCookie('geo_mytown_handchange_tag', 1, 86400*30);

                $cookie = new CHttpCookie('geo_mycountry_name', $geodata['country']['name_ru']);
                $cookie->expire = time() + 86400*30;
                Yii::app()->request->cookies['geo_mycountry_name'] = $cookie;

                if ($cur_url == '/' || $cur_url == '/site/index')
                {
                    if($country = Countries::model()->findByPk($geodata['country']['id']))
                    {
                        header('Location: /'.$country->transname);
                    }
                    else
                    {
                        // Редирект на все регионы
                        header('Location: /russia');
                    }
                }
            }
            else
            {
                if ($cur_url == '/' || $cur_url == '/site/index')
                {
                    header('Location: /russia');
                }
            }


        }






        $this->render('index');
    }


    // Установка кук геолокации
    public static function SetGeolocatorCookie($name, $value, $period)
    {
        $cookie = new CHttpCookie($name, $value);
        $cookie->expire = time() + $period;
        Yii::app()->request->cookies[$name] = $cookie;
    }


}