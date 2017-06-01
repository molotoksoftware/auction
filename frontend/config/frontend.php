<?php

/**
 *
 * frontend.php configuration file
 *
 * @author    Antonio Ramirez <amigo.cobos@gmail.com>
 * @link      http://www.ramirezcobos.com/
 * @link      http://www.2amigos.us/
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
defined('APP_CONFIG_NAME') or define('APP_CONFIG_NAME', 'frontend');


return [
    'id'                => 'auction',
    'basePath'          => realPath(__DIR__ . '/..'),
    'defaultController' => 'site',
    'theme'             => 'default',
    'layout'            => 'main',
    'aliases'           => [],
    'behaviors'         => [],
    'controllerMap'     => [],
    'preload'           => ['log'],
    'modules'           => [
        'user'        => [
            'class'   => 'frontend.modules.user.UserModule',
            'enabled' => true,
        ],
    ],
    'import'            => [
        'frontend.modules.user.components.*',
        'frontend.extensions.ESetReturnUrlFilter',
        'frontend.components.counterEvent.CounterEvent',
        'frontend.components.counterEvent.types.*',
        'frontend.components.notification.Notification',
        'frontend.components.HttpRequest',
        'frontend.widgets.user.UserInfo',
        'common.components.events.*',
    ],
    'components'        => [
        'request'     => [
            'class'                  => 'HttpRequest',
            'noCsrfValidationRoutes' => [
                '^user/sales/workwithlots$',
            ],
            'enableCsrfValidation' => true,
            'enableCookieValidation' => true,
            'csrfTokenName'          => 'token',
        ],
        'cache'       => [
            'class' => 'system.caching.CFileCache',
        ],
        'user'        => [
            'class'           => 'frontend.modules.user.components.WebUser',
            'cabinetUrl'      => '/user/cabinet/index',
            'allowAutoLogin'  => true,
            'authTimeout'     => 24 * 3600,
            'autoRenewCookie' => true,
            'loginUrl'        => '/user/user/login',
        ],
        'authManager' => [
            //'class' => 'application.modules.user.components.UPhpAuthManager',
            'defaultRoles' => ['guest'],
        ],
        'log'         => [
            'class'   => 'CLogRouter',
            'enabled' => true,
            'routes'  => [
                [
                    'class'  => 'CFileLogRoute',
                    'levels' => 'info',
                    'logFile' => 'appli.log',
                ],
                [
                    'class'   => 'CFileLogRoute',
                    'levels'  => 'error',
                    'logFile' => 'application.error.log',
                ],
                [
                    'class'      => 'CFileLogRoute',
                    'categories' => ['system.db.CDbCommand'],
                    'logFile'    => 'query.log',
//                    'levels'     => ['info'],
                    'enabled'    => YII_DEBUG,
                ],
            ],
        ],
        'urlManager'              => [
            'urlFormat'      => 'path',
            'showScriptName' => false,
            'cacheID'        => 'cache',
            'urlRuleClass'   => 'frontend.components.CustomUrlRule',
            'rules'          => [
                '/'                                                     => 'site/index',
                '/login'                                                => '/user/user/login',
                '/logout'                                               => '/user/user/logout',
                '/registration'                                         => '/user/user/registration',
                '/recovery'                                             => '/user/user/recovery',
                '/pages/<alias>'                                        => 'page/view',
                '/auction'                                              => ['auction/index', 'defaultParams' => ['path' => 'all']],
                '/auctions/<path:.+>'                                   => '/auction/index',
                'newBid'                                                => '/auction/newBid',
                'bidBlitz'                                              => '/auction/bidBlitz',
                '/news'                                                 => '/news/index',
                '/auction/<id:\d+>'                                     => '/auction/view',
                '/user/page/<login:[\w-]+>/<path:.+>'                   => '/user/user/page',
                '/user/page/<login:[\w-]+>'                             => '/user/user/page',
                '/<login:[\w-]+>' => '/user/user/landing',
            ],
        ],
        'errorHandler'            => ['errorAction' => 'site/error'],
        'session'                 => [
            'class'   => 'CCacheHttpSession',
            'cacheID' => 'cache',
            'timeout' => 43200,
        ],
        'userWebsiteNotification' => ['class' => 'application.components.UserWebsiteNotification'],
    ],
    'params'            => [
        //'cache_duration' => 60 * 60,
        'cabinetTablePageSize' => 1,
    ],

];

