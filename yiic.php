<?php
/**
 *
 * Yiic.php bootstrap file
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 0);
date_default_timezone_set('Europe/Moscow');
ini_set('default_charset', 'utf-8');

error_reporting(E_ALL);
// I don't know if you need to wrap the 1 inside of double quotes.
ini_set("display_startup_errors",1);
ini_set("display_errors",1);
ini_set("memory_limit", "1024M");

require(__DIR__.'/common/lib/vendor/autoload.php');

Yiinitializr\Helpers\Initializer::create(__DIR__.'/console', 'console', array(
    __DIR__.'/common/config/main.php',
    __DIR__.'/common/config/env.php',
    __DIR__.'/common/config/local.php',
))->run();