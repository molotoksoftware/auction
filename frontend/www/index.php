<?php
/**
 *
 * Bootstrap index file
 *
 * @author    Antonio Ramirez <amigo.cobos@gmail.com>
 * @link      http://www.ramirezcobos.com/
 * @link      http://www.2amigos.us/
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

// часть url адреса локального сервера, чтобы определился как дев
$name_test_server = '.test';

$isDev = strpos($_SERVER['HTTP_HOST'], $name_test_server) !== false;
$isProd = !$isDev;


ini_set('display_errors', $isProd ? -1 : 1);

set_time_limit(0);
ini_set("max_execution_time", "200");
ini_set("max_input_time", "200");
ini_set("memory_limit", "1024M");

date_default_timezone_set($isDev ? 'Europe/Chisinau' : 'Europe/Moscow');
ini_set('display_errors', true);
ini_set('default_charset', 'utf-8');

if ($isProd) {
    define('YII_ENV', 'prod');
} else {
    define('YII_ENV', 'dev');
}

defined('YII_DEBUG') or define('YII_DEBUG', !$isProd);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

require('./../../common/lib/vendor/autoload.php');
require('./../../common/lib/vendor/yiisoft/yii/framework/yii.php');
require('./../../common/lib/global.php');

Yii::setPathOfAlias('Yiinitializr', './../../common/lib/Yiinitializr');

// set_error_handler("exception_error_handler");

function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}

use Yiinitializr\Helpers\Initializer;

Initializer::create('./../', 'frontend', [
    __DIR__ . '/../../common/config/main.php',
    __DIR__ . '/../../common/config/env.php',
    __DIR__ . '/../../common/config/local.php',
    'main',
    'env',
    'local',
])->run();
