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

    if(!$model->id_city && $useUserRegion) {
        $user = User::model()->findByPk(Yii::app()->user->id);
        $model->id_country = $user->id_country;
        $model->id_region = $user->id_region;
        $model->id_city = $user->id_city;
    }
?>

<div class="city-selector">
    <? if($model->hasErrors('id_city') || $model->hasErrors('id_region') || $model->hasErrors('id_country')) { ?>
        <div>
            <small><span style="color: red;">Укажите местоположение с точностью до города</span></small>
        </div>
        <div class="clear"></div>
    <? } ?>

    <?php if ($scope == CitySelectorWidget::SCOPE_CATEGORY): ?>
        <label for="<?= CHtml::activeId($model, 'id_country') ?>">
            Местоположение лотов
        </label>
    <?php endif; ?>

    <div class="select_cat_id_cont">
        <?php echo CHtml::activeDropDownList(
            $model,
            'id_country',
            CHtml::listData(Country::getAllCountries(), 'id_country', 'name'),
            array('empty' => '- выберите страну -', 'class' => 'country-select form-control country_select_style')
        ); ?>
    </div>

    <? if($model->id_country) { ?>
        <div class="select_cat_id_cont">
            <?php echo CHtml::activeDropDownList(
                $model,
                'id_region',
                CHtml::listData(Region::getRegionsByCountry($model->id_country), 'id_region', 'name'),
                array('empty' => '- выберите регион -', 'class' => 'region-select form-control country_select_style')
            ); ?>
        </div>

        <? if($model->id_region) { ?>
            <div class="select_cat_id_cont">
                <?php echo CHtml::activeDropDownList(
                    $model,
                    'id_city',
                    CHtml::listData(City::getCitiesByRegion($model->id_region), 'id_city', 'name'),
                    array('empty' => '- выберите город -', 'class' => 'city-select form-control country_select_style')
                ); ?>
            </div>
        <? } ?>
    <? } ?>
</div>