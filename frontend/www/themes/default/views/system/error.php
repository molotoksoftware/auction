<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8" />
        <!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
        <title>Неправильно набран адрес</title>
        <link rel="stylesheet" href="<?php echo Yii::app()->baseUrl; ?>/css/style.css">
            <!--[if lte IE 6]><link rel="stylesheet" href="css/style_ie.css" type="text/css" media="screen, projection" /><![endif]-->
            <link rel="icon" href="/favicon.png" type="image/x-icon" />
            <link rel="shortcut icon" href="/favicon.png" type="image/x-icon" />
    </head>
    <body>
        <div class="container_404">
            <?php if (isset($message)): ?>
            <?php echo CHtml::encode($message); ?>
            <?php else: ?>
            <p>Неправильно набран адрес<br/>или такой страницы не существует.</p>
            <?php endif; ?>
            <p>Пожалуйста, <a href="<?= Yii::app()->params['siteUrl']; ?>">перейдите на главную</a></p>
        </div>
    </body>
</html>