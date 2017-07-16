<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://molotoksoftware.com/
 * @copyright 2016 MolotokSoftware
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


cs()->registerCoreScript('jquery');
cs()->registerCssFile(bu() . '/css/global.css');
Yii::app()->clientScript->registerScript('basePath', "basePath = '" . Yii::app()->getRequest()->getHostInfo() . "';", CClientScript::POS_HEAD);
?>
<?php
Yii::app()->clientScript->registerScript('loading', ' 
    $(".ajax-loading").bind("ajaxSend", function(){ 
        $(this).show(); 
    }).bind("ajaxComplete", function(){ 
        $(this).hide(); 
    }); 
', CClientScript::POS_READY);


//Подключаем виджет для вывода сообщений пользователям
$this->widget('ex-bootstrap.widgets.ENotify', array(
    'position' => 'top-right',
));
?>

<!DOCTYPE html>
<html lang="<?= Yii::app()->getLanguage(); ?>">
    <!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
    <!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
    <!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
    <!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0">
            <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
            <title><?php echo CHtml::encode($this->pageTitle); ?></title>

            <noscript>
            <?php
            /**
             * @todo сделать noscript
             */
            ?>

            <meta http-equiv="refresh" content="0; URL=<?= $this->createAbsoluteUrl('/core/offline/noscript'); ?>" />
            </noscript>
            <link rel="shortcut icon" href="<?= bu(); ?>/favicon.ico" />
        </head>
        <body>
            <?php $this->renderPartial('//layouts/navbar'); ?>
            <?php $this->renderPartial('//layouts/sidebar'); ?>

            <div class="main-content">
                <div class='notifications top-right'></div>
                <?php $this->renderPartial('//layouts/header'); ?>
                <?php echo $content; ?>
            </div>
        </body>
    </html>
