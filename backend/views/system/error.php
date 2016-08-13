<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title> </title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/themes/fit-default/css/normalize.css">
    <link rel="stylesheet" href="/themes/fit-default/css/main.css">
    <!--[if IE]>
        <link rel="stylesheet" href="/themes/fit-default/css/ie.css">
    <![endif]-->
    <!--[if lt IE 8]>
    <div class="form_bg">
        <div class="atten_b">
            <div class="at_title">
                Ваш браузер устарел.
            </div>
            <div class="at_cont">
                Пожалуйста обновите его для полноценной работы сайта
                <br />
                <a href="http://browsehappy.com/">Обновить браузер</a>
            </div>
            <a href="#" class="at_cont">продолжить</a>
        </div> 
    </div>
    <![endif]-->
</head>
<body>
    <br/>
    <br/>
    <br/>
    <div class="main">
        <div class="inside no-shadow no-bg">
            <aside class="page" style="border:none;">
                <h2 class="small_h2">
                    <span class="er404">При обработке веб-сервером вашего запроса произошла ошибка.</span>
                </h2>
                <div class="t_a_c go_home">
                    <a href="<?= Yii::app()->homeUrl; ?>"><button type="button" class="btn big_btn">Вернутся на главную</button></a>
                </div>
                <div class="t_a_c img404">
                    <img src="<?= tbu(); ?>/img/404.png" alt="404" />
                </div>
                <div class="hr"></div>
            </aside>  <!-- end left_clo -->

            <div class="clearfix"> </div>
        </div> <!-- end inside -->
    </div>   <!-- end main -->
</body>
</html>