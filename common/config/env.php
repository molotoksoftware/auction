<?php

return [
    'preload'    => ['log'],
    'components' => [
        'db'  => [
            'connectionString'      => 'mysql:host=localhost;dbname=buysell',
            'emulatePrepare'        => true,
            'username'              => 'buysell',
            'password'              => 'buysell',
            'charset'               => 'utf8',
            'enableProfiling'       => true,
            'enableParamLogging'    => true,
            'tablePrefix'           => '',
            'schemaCachingDuration' => 0,
        ],
        'mail'              => [
            'class'            => 'common.extensions.mail.YiiMail',
            'viewPath'         => 'frontend.views.mail',
            'logging'          => true,
            'transportType'    => 'smtp',
            'transportOptions' => [
                'host'       => 'ssl://smtp.gmail.com',
                'username'   => 'demo@gmail.com',
                'password'   => 'password',
                'port'       => '465',
            ],
        ],
        'log' => [
            'class'   => 'CLogRouter',
            'enabled' => true,
            'routes'  => [
                [
                    'class'      => 'CFileLogRoute',
                    'levels'     => 'info, error, trace',
                    'categories' => 'application.sms',
                ],
                [
                    'class'      => 'CFileLogRoute',
                    'levels'     => '',
                    'categories' => 'message_queue.*',
                ],
            ],
        ],
    ],
    'params'     => [
        'yii.debug'         => !empty($_GET["debug"]) ? true : false,
        'yii.traceLevel'    => 0,
        'yii.handleErrors'  => APP_CONFIG_NAME !== 'test',




//        // Отключить отправки email.
//        'disableEmailNtf'   => true,
    ],
];
