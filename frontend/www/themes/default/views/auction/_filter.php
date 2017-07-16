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

/** @var string $actionUrlRoute */
/** @var array $actionUrlParams */
/** @var bool $hideCitySelector */

if (isset($actionUrlParams['Filter'])) {
    unset($actionUrlParams['Filter']);
}


$form = $this->beginWidget(
    'CActiveForm',
    [
        'id' => 'filter-form',
        'action' => $this->createUrl($actionUrlRoute, $actionUrlParams),
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'errorMessageCssClass' => 'error',
        'method' => 'GET',
        'clientOptions' => [
            'validateOnSubmit' => false,
            'validateOnChange' => false,
            'validateOnType' => false,
        ],
        'htmlOptions' => [],
    ]
);
?>

    <label><?= Yii::t('basic', 'Price') ?></label>
    <div class="form-group form-inline">
        <?php echo $form->textField($filter, 'price_min', ['class' => 'form-control', 'style' => 'width:80px']); ?> -
        <?php echo $form->textField($filter, 'price_max', ['class' => 'form-control', 'style' => 'width:80px']); ?>
        <input type="submit" value=">" class="btn btn-default" style="width: 30px;">
    </div>

<?php if (empty($hideCitySelector)): ?>
    <?php $this->widget('frontend.widgets.citySelector.CitySelectorWidget', [
        'model' => $filter,
        'className' => '',
        'showIco' => false,
        'useUserRegion' => false,
        'scope' => 'category',
    ]); ?>
<?php endif; ?>


<?php if ($options): ?>
    <div class="options-container attr-filter">
        <h4><span class="glyphicon glyphicon-asterisk"></span> <?= Yii::t('basic', 'Attributes') ?></h4>
        <br/>
        <?php
        $this->renderPartial(
            '//auction/_options',
            [
                'options' => $options,
                'filter' => $filter,
            ]
        );
        ?>
    </div>
<?php endif; ?>

    <input type="submit" value="<?= Yii::t('basic', 'Search')?>" class="btn btn-info margint_top_30">
<?php $this->endWidget(); ?>