<?php

return array(
    /*'jquery'=> array(

        //'baseUrl' => 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.2',
        'js'=>array('jquery.min.js')
    ),*/
    'fancybox' => array(
        'basePath' => 'webroot.themes.fit-admin.libs.fancybox',
        'js' => array('jquery.fancybox-1.3.4.pack.js','jquery.mousewheel-3.0.4.pack.js'),
        'css' => array('jquery.fancybox-1.3.4.css')
    ),
    'dataTables' => array(
        'basePath' => 'webroot.themes.fit-admin.libs.dataTables.media.js',
        'js' => array('jquery.dataTables.js'),
        'css' =>  array(),
    ),
    'Timepicker' => array(
        'basePath' => 'webroot.themes.fit-admin.libs.Timepicker',
        'js' => array('jquery-ui-timepicker-addon.js'),
        'css' =>  array('jquery-ui-timepicker-addon.css'),
        'depends'=>array('jquery.ui')
    ),


);