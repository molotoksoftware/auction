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


$this->widget(
    'zii.widgets.CMenu',
    array(
        'items' => array(
            [
                'label' => Yii::t('basic', 'Items'),
                'url' => ['/user/page/' . $model->login],
                'itemOptions' => array('class' => Yii::app()->controller->action->id == 'page' ? 'active' : '')
            ],

            [
                'label' => Yii::t('basic', 'Reviews') . ' <span class="label label-info">' . UserDataHelper::getSummaryCountReviews($model->user_id) . '</span>',
                'url' => Yii::app()->createAbsoluteUrl('/user/reviews/view', ['login' => $model->login]),
                'itemOptions' => array('class' => Yii::app()->controller->id == 'reviews' ? 'active' : '')
            ],

            [
                'label' => Yii::t('basic', 'About') . ' ' . $model->getNickOrLogin(),
                'url' => Yii::app()->createAbsoluteUrl('/user/user/about_me', ['login' => $model->login]),
                'itemOptions' => array('class' => Yii::app()->controller->action->id == 'about_me' ? 'active' : '')
            ],

        ),
        'encodeLabel' => false,
        'itemTemplate' => '{menu}',

        'htmlOptions' => array(
            'class' => 'nav nav-tabs'
        ),
    )
);
?>