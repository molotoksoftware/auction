<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://molotoksoftware.com/
 * @copyright 2017 MolotokSoftware
 * @license GNU General Public License, version 3
 */

/**
 *
 * This file is part of MolotokSoftware.
 *
 * MolotokSoftware is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MolotokSoftware is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with MolotokSoftware.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @var BackendController $this
 */

cs()->registerCoreScript('bootstrap');
cs()->registerCoreScript('font-awesome');
cs()->registerCoreScript('ionicons');
cs()->registerCoreScript('adminLTE');
cs()->registerCoreScript('jquery');


$csrfTokenName = Yii::app()->request->csrfTokenName;
$csrfToken = Yii::app()->request->csrfToken;

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?= Yii::app()->language ?>">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title><?= CHtml::encode($this->pageTitle); ?></title>
    <meta name="description" content="<?= CHtml::encode($this->pageDescription); ?>">

    <?php $this->renderMetaTags(); ?>

    <?php cs()->registerScriptFile(bu() . '/dist/js/app.min.js', CClientScript::POS_END); ?>
    <?php cs()->registerScriptFile(bu() . '/bootstrap/js/bootstrap.min.js', CClientScript::POS_END); ?>
    <?php // cs()->registerScriptFile(bu() . '/dist/js/demo.js', CClientScript::POS_END); ?>

    <?php // cs()->registerScriptFile(bu() . '/plugins/fastclick/fastclick.js', CClientScript::POS_END); ?>
    <?php // cs()->registerScriptFile(bu() . '/plugins/sparkline/jquery.sparkline.min.js', CClientScript::POS_END); ?>
    <?php // cs()->registerScriptFile(bu() . '/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js', CClientScript::POS_END); ?>
    <?php // cs()->registerScriptFile(bu() . '/plugins/jvectormap/jquery-jvectormap-world-mill-en.js', CClientScript::POS_END); ?>
    <?php // cs()->registerScriptFile(bu() . '/plugins/slimScroll/jquery.slimscroll.min.js', CClientScript::POS_END); ?>
    <?php // cs()->registerScriptFile(bu() . '/plugins/chartjs/Chart.min.js', CClientScript::POS_END); ?>
    <?php // cs()->registerScriptFile(bu() . '/dist/js/pages/dashboard2.js', CClientScript::POS_END); ?>

    <link rel="icon" href="/favicon.png" type="image/x-icon"/>
    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon"/>
</head>
<body class="hold-transition <?= $this->bodyAddClass ?>">

<?= $content; ?>

</body>
</html>