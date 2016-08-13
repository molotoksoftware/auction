<?php

/**
 *
 * console.php configuration file
 *
 * @author    Antonio Ramirez <amigo.cobos@gmail.com>
 * @link      http://www.ramirezcobos.com/
 * @link      http://www.2amigos.us/
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
defined('APP_CONFIG_NAME') or define('APP_CONFIG_NAME', 'console');

return [
    'id'         => 'auction',
    'preload'    => ['log'],
    'basePath'   => realPath(__DIR__ . '/..'),
    'import'     => [
        'frontend.helpers.*',
        'frontend.components.*',
        'frontend.components.notification.Notification',
        'frontend.components.notification.templates.*',
        'frontend.extensions.ESetReturnUrlFilter',
        'frontend.components.counterEvent.CounterEvent',
        'frontend.components.counterEvent.types.*',
        'common.components.import.*',
        'console.components.cli.*',
        'console.commands.BaseCommand',
        'common.models.appProcesses.*',
        'common.components.billing.*',
        'common.components.billing.parsers.*',
    ],
    'components' => [
        'request'        => [
            'hostInfo'  => '',
            'baseUrl'   => '',
            'scriptUrl' => '',
        ],
        'cache'          => [
            'class'     => 'system.caching.CFileCache',
            'cachePath' => __DIR__ . '/../../frontend/runtime/cache',
        ],
        'image'          => [
            'class'  => 'common.extensions.image.CImageComponent',
            'driver' => 'GD',
            'params' => ['directory' => '/opt/local/bin'],
        ],
        'urlManager'     => [
            'urlFormat'      => 'path',
            'showScriptName' => false,
            'cacheID'        => 'cache',
            'urlRuleClass'   => 'frontend.components.CustomUrlRule',
            'rules'          => [
                '/'                      => 'site/index',
                '/login'                 => '/user/user/login',
                '/logout'                => '/user/user/logout',
                '/registration'          => '/user/user/registration',
                '/recovery'              => '/user/user/recovery',
                '/pages/<alias>'         => 'page/view',
                '/auction'               => ['auction/index', 'defaultParams' => ['path' => 'all']],
                '/auctions/<path:.+>'    => '/auction/index',
                'newBid'                 => '/auction/newBid',
                'bidBlitz'               => '/auction/bidBlitz',
                '/news'                  => '/news/index',
                '/auction/<id:\d+>'      => '/auction/view',
                '/user/page/<login:\w+>' => '/user/user/page',
            ],
        ],
        'log'            => [
            'class'   => 'CLogRouter',
            'enabled' => true,
            'routes'  => [
                [
                    'logFile'    => 'ads.log',
                    'class'      => 'CFileLogRoute',
                    'levels'     => 'info',
                    'categories' => 'ads',
                ],

                [
                    'logFile'    => 'lot.log',
                    'class'      => 'CFileLogRoute',
                    'levels'     => 'info',
                    'categories' => 'lot',
                ],
                [
                    'logFile'    => 'mailing.log',
                    'class'      => 'CFileLogRoute',
                    'levels'     => 'info',
                    'categories' => 'mail',
                ],
                [
                    'logFile' => 'error.log',
                    'class'   => 'CFileLogRoute',
                    'levels'  => 'error',
                ],
                /*[
                    'class'      => 'CFileLogRoute',
                    'categories' => ['ext.yii-mail.YiiMail.*'],
                    'logFile'    => 'yii-mail.log',
                ],*/
                [
                    'class'      => 'CFileLogRoute',
                    'categories' => ['message_queue.*'],
                    'logFile'    => 'message_queue.log',
                ],
                [
                    'logFile'    => 'billing_currency.log',
                    'class'      => 'CFileLogRoute',
                    'categories' => ['billing_currency_parser'],
                ],
            ],
        ],
    ],
    'commandMap' => [
        'migrate'  => [
            'class'         => 'system.cli.commands.MigrateCommand',
            'migrationPath' => 'application.migrations',
        ],
        'database' => [
            'class' => 'console.extensions.database-command.EDatabaseCommand',
        ],
    ],
];