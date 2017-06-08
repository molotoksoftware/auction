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

// part of url, for working with Debug mode
$name_test_server = 'github';
$isDev = strpos($_SERVER['HTTP_HOST'], $name_test_server) !== false;

if  ($isDev === true) {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 5);
}

require('./../../common/lib/vendor/autoload.php');
require('./../../common/lib/vendor/yiisoft/yii/framework/yii.php');
require('./../../common/lib/global.php');

Yii::setPathOfAlias('Yiinitializr', './../../common/lib/Yiinitializr');

use Yiinitializr\Helpers\Initializer;

Initializer::create('./../', 'frontend', [
    __DIR__ . '/../../common/config/main.php',
    __DIR__ . '/../../common/config/env.php',
    __DIR__ . '/../../common/config/local.php',
    'main',
    'env',
    'local',
])->run();
