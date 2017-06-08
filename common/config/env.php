<?php

return [
    'preload' => ['debug', 'log'],
    'components' => [

        /*'debug' => array(
            'class' => 'common.extensions.yii2-debug.Yii2Debug',
        ),*/
        'db'  => [
            'connectionString'      => 'mysql:host=localhost;dbname=dbname',
            'emulatePrepare'        => true,
            'username'              => 'dbname',
            'password'              => 'dbpassword',
            'charset'               => 'utf8',
            'enableProfiling'       => true,
            'enableParamLogging'    => true,
            'tablePrefix'           => '',
            'schemaCachingDuration' => 0,
        ],
        'configDb'=>array(
            'class'=>'CDbConnection',
            'connectionString'=>'sqlite:'.dirname(__FILE__).'/../data/setting.db',
            'tablePrefix'=>'',
        ),
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
