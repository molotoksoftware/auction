<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0">
        <link rel="stylesheet" href="<?php echo Yii::app()->baseUrl; ?>/css/global.css">
        <meta charset="utf-8">
        <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
        <title><?php echo Yii::app()->name; ?> - 404</title>
    <body>
        <div class="navbar navbar-top navbar-inverse">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="brand" href="<?php echo Yii::app()->createUrl(Yii::app()->params['adminUrl']); ?>"> Панель управления</a>
                    <ul class="nav pull-right">
                        <li class="toggle-primary-sidebar hidden-desktop" data-toggle="collapse" data-target=".nav-collapse-primary"><a><i class="icon-th-list"></i></a></li>
                        <li class="collapsed hidden-desktop" data-toggle="collapse" data-target=".nav-collapse-top"><a><i class="icon-align-justify"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row-fluid">
                <div class="span8 offset2">
                    <div class="error-box">
                        <div class="message-small">Страница не найдена</div>
                        <div class="message-big">404</div>
                        <div class="message-small">К сожалению, такой страницы не существует.</div>

                        <div style="margin-top: 50px">
                            <a class="btn btn-blue" href="<?php echo Yii::app()->createUrl(Yii::app()->params['adminUrl']); ?>">
                                <i class="icon-arrow-left"></i> <?php echo Yii::t('common', 'return to the site'); ?>  
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>