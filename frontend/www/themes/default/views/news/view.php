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


?>

<div class="row auction">
        <div class="col-xs-12">
            <h2><?= $model->title; ?></h2>
        </div>
</div>
<hr class="top10 horizontal_line">

<div class="row">
    <div class="col-xs-3">
        <div class="row">
            <div class="col-xs-12">
                <?php
                echo CHtml::image($model->uploadedFile->getImage('large'), $model->title);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <?php $this->widget('frontend.widgets.lastNews.LastNewsWidget'); ?>
            </div>
        </div>
        
    </div>
    <div class="col-xs-9">
        <p><a href="<?= Yii::app()->createUrl('/news/index'); ?>"><span class="glyphicon glyphicon-arrow-left"></span> <?= Yii::t('basic', 'Back to news feed')?></a></p>
        <p><small><?= Yii::app()->dateFormatter->format('dd MMMM yyyy', strtotime($model->date)); ?></small></p>
        <div><?= $model->content; ?> </div>
    </div>
</div>


