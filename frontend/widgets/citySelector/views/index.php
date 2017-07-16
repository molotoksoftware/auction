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


/** @var Auction $model */
/** @var string $scope */

    if(!$model->id_city && $useUserRegion && get_class(Yii::app()->user->model) == 'User') {
        $user = User::model()->findByPk(Yii::app()->user->id);
        $model->id_country = $user->id_country;
        $model->id_region = $user->id_region;
        $model->id_city = $user->id_city;
    }

    if(!$model->id_city && !$model->id_region && !$model->id_country && $defaultLocation && get_class(Yii::app()->user->model) == 'User') {
        $model->id_country = $defaultLocation->id_country;
        $model->id_region = $defaultLocation->id_region;
        $model->id_city = $defaultLocation->id_city;
    }

    $lock = false;
    if(get_class(Yii::app()->user->model) == 'User') {
        $lock = Yii::app()->params['defaultLocationLock'];
    }
?>

<div class="city-selector" data-base-url="<?= $baseUrl ?>">
    <?php if($model->hasErrors('id_city') || $model->hasErrors('id_region') || $model->hasErrors('id_country')): ?>
        <div>
            <small><span style="color: red;"><?= Yii::t('basic', 'Select a location')?></span></small>
        </div>
        <div class="clear"></div>
    <?php endif; ?>

    <?php if ($scope == CitySelectorWidget::SCOPE_CATEGORY): ?>
        <label for="<?= CHtml::activeId($model, 'id_country') ?>">
            <?= Yii::t('basic', 'Location of items')?>
        </label>
    <?php endif; ?>

    <?php if($lock && $defaultLocation->id_country): ?>
        <?= CHtml::activeHiddenField($model,'id_country'); ?>
    <?php else: ?>
        <div class="select_cat_id_cont">
            <?php echo CHtml::activeDropDownList(
                $model,
                'id_country',
                CHtml::listData(Country::getAllCountries(), 'id_country', 'name'),
                array('empty' => '- '.Yii::t('basic', 'select a country').' -', 'class' => 'country-select form-control country_select_style')
            ); ?>
        </div>
    <?php endif; ?>

    <? if($model->id_country): ?>
        <?php if($lock && $defaultLocation->id_region): ?>
            <?= CHtml::activeHiddenField($model,'id_region'); ?>
        <?php else: ?>
            <div class="select_cat_id_cont">
                <?php echo CHtml::activeDropDownList(
                    $model,
                    'id_region',
                    CHtml::listData(Region::getRegionsByCountry($model->id_country), 'id_region', 'name'),
                    array('empty' => '- '.Yii::t('basic', 'select a region').' -', 'class' => 'region-select form-control country_select_style')
                ); ?>
            </div>
        <?php endif; ?>

        <? if($model->id_region): ?>
            <?php if($lock && $defaultLocation->id_city): ?>
                <?= CHtml::activeHiddenField($model,'id_city'); ?>
            <?php else: ?>
                <div class="select_cat_id_cont">
                    <?php echo CHtml::activeDropDownList(
                        $model,
                        'id_city',
                        CHtml::listData(City::getCitiesByRegion($model->id_region), 'id_city', 'name'),
                        array('empty' => '- '.Yii::t('basic', 'select a city').' -', 'class' => 'city-select form-control country_select_style')
                    ); ?>
                </div>
            <?php endif; ?>
        <? endif; ?>
    <? endif; ?>
</div>