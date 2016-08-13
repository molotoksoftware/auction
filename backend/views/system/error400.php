<!DOCTYPE html>
<!--[if IE 7 ]>    <html lang="ru" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="ru" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="ru" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="ru" class="no-js"> <!--<![endif]-->
<html lang="en-US">
	<head>
		<meta charset="utf-8"/>
		<title></title>
		<script type="text/javascript" src="<?=tbu();?>/js/browser-detection.js"></script>
		<script type="text/javascript" src="<?=tbu();?>/js/libs/modernizr-2.6.1.min.js"></script>
		<link rel="shortcut icon" href="favicon.ico" />
                <link rel="stylesheet" href="<?=tbu();?>/css/reset.css" media="screen" />
		<link rel="stylesheet" href="<?=tbu();?>/css/global.css" media="screen" />
	</head>
	<body id="body " class="error_page_bg">

			<div class=" error_page">
				<div class=" error_page_logo">
					<a href="<?=Yii::app()->homeUrl; ?>" ><img src="<?=tbu();?>/images/logo_404.png" alt="" /></a>
				</div> <!--  close error_page_logo -->

				<div class="error_page_block">
					<h1>400</h1>
					<p>Неправильно набран адрес <br />или такой страницу не существует.</p><a href="<?=Yii::app()->homeUrl; ?>">на главную</a>
				</div><!-- close  error_page_block -->


			</div><!-- close  error_page -->

	<!-- PLUGINS: jQuery v1.7.2 -->
	<script src="<?=tbu();?>/js/plugins.js"></script>
	<script src="<?=tbu();?>/js/popup.js"></script>
	<!--[if lt IE 9]>
		<script type="text/javascript" src="<?=tbu();?>/js/libs/selectivizr.min.js"></script>
		<script src="<?=tbu();?>/js/libs/IE9.js"></script>
	<![endif]-->
	<!--[if lt IE 10]>
		<script type="text/javascript" src="<?=tbu();?>/js/libs/PIE.js"></script>
	<![endif]-->
	</body>
</html>