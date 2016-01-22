<?php

error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('Europe/Moscow');

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

// change the following paths if necessary
$yii=dirname(__FILE__).'/yii/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';


require_once($yii);
//Yii::createWebApplication($config)->run();

// Класс BaraholkaWebApplication положил в /yii/BaraholkaWebApplication.php
Yii::createApplication('BaraholkaWebApplication', $config)->run();

