<?php

/**
 *
 * backend.php configuration file
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
defined('APP_CONFIG_NAME') or define('APP_CONFIG_NAME', 'backend');

// web application configuration
return array(
    'name' => '',
    'basePath' => realPath(__DIR__ . '/..'),
    'defaultController' => 'main',
    'aliases' => array(
        'ex-bootstrap' => dirname(__FILE__) . '/../extensions/ex-bootstrap',
    ),
    'import' => array(
        'backend.extensions.bootstrap.widgets.*',
        'backend.components.notification.Notification'
    ),
    'sourceLanguage' => 'en',
    'language' => 'ru',
    'preload' => array('bootstrap'),
    'behaviors' => array(),
    'controllerMap' => array(),
    'modules' => array(
        'catalog' => array(
            'class' => 'application.modules.catalog.CatalogModule',
            'enabled' => true,
        ),
        'user' => array(
            'class' => 'application.modules.user.UserModule',
            'enabled' => true,
        ),
        'admin' => array(
            'class' => 'application.modules.admin.AdminModule',
            'enabled' => true,
        ),
        'page' => array(
            'class' => 'application.modules.page.PageModule',
            'enabled' => true,
        ),
        'news' => array(
            'class' => 'application.modules.news.NewsModule',
            'enabled' => true,
        ),
        'money' => array(
            'class' => 'application.modules.money.MoneyModule',
            'enabled' => true,
        ),
        'sales' => array(
            'class' => 'application.modules.sales.SalesModule',
            'enabled' => true,
        ),
    ),
    'components' => array(
        'user' => array(
            'class' => 'backend.modules.admin.components.WebUser',
            'allowAutoLogin' => true,
            'loginUrl' => array('/admin/admin/login')
        ),
        'authManager' => array(
            'class' => 'backend.modules.admin.components.PhpAuthManager',
            'defaultRoles' => array('guest')
        ),
        'bootstrap' => array(
            'class' => 'application.extensions.bootstrap.components.Bootstrap',
            'responsiveCss' => true,
            'enableNotifierJS' => false,
            'enableBootboxJS' => false,
            'coreCss' => true,
            'fontAwesomeCss' => true,
            'yiiCss' => true,
            'jqueryCss' => true,
        ),
        'clientScript' => array(
            'scriptMap' => array(
              //'jquery.js' => false
//				'bootstrap.min.css' => false,
//				'bootstrap.min.js' => false,
//				'bootstrap-yii.css' => false
            )
        ),
        'urlManager' => array(
            // uncomment the following if you have enabled Apache's Rewrite module.
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                '/user/page/<login:[\w-]+>' => '/user/user/page',

                // default rules
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        //'errorHandler' => array('errorAction' => 'site/error'),
    ),
    'params' => array(
        'cache_duration' => 10 * 60,
        'adminUrl' => '/main/index'
    ),
);
