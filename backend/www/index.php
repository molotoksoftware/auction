<?php
/**
 *
 * Bootstrap index file
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

set_time_limit(0);
ini_set("max_execution_time", "200");
ini_set("max_input_time", "200");
ini_set("memory_limit", "1024M");

date_default_timezone_set('Europe/Moscow');
ini_set('display_errors', true);
ini_set('default_charset', 'utf-8');
error_reporting(E_ALL);

//defined('YII_DEBUG') or define('YII_DEBUG', true);
//defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

require('./../../common/lib/vendor/autoload.php');
require('./../../common/lib/vendor/yiisoft/yii/framework/yii.php');
require('./../../common/lib/global.php');

Yii::setPathOfAlias('Yiinitializr', './../../common/lib/Yiinitializr');

use Yiinitializr\Helpers\Initializer;



Initializer::create('./../', 'backend', array(
	__DIR__ .'/../../common/config/main.php',
	__DIR__ .'/../../common/config/env.php',
	__DIR__ .'/../../common/config/local.php',
))->run();
