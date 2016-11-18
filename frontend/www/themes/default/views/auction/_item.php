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

/** @var Controller $this */
/** @var array $data */
/** @var array|null $owner */
/** @var City|null $city */
/** @var array $auctionAttributes */
/** @var array|null $breadcrumbOptions */
/** @var array $auctionImages */


$itemDivAttributes = [
    'class' => implode(' ', [
        'row auctions'
    ]),
];

$breadcrumbOptions = isset($breadcrumbOptions) ? $breadcrumbOptions : [];

?>

<?= CHtml::openTag('div', $itemDivAttributes) ?>

        <div class="col15-xs-3 relative">
            <?= Item::getPreview($data, ['class' => 'lazy img-thumbnail'], 'prv', ['setDataImages' => $auctionImages]) ?>
            <?php if ($data['image_count'] > 1): ?>
                <p class="label label-default count_img_auc"><?php echo $data['image_count']; ?> <?= Yii::t('basic', 'photos')?></p>
            <?php endif; ?>
        </div>
        <div class="col15-xs-9 info_lot">
            <div class="div_bread_cat"><?= Item::getBreadcrumbs($data, ' / ', [], $breadcrumbOptions); ?></div>
            <?php echo Item::getName($data); ?>
            <? if(!empty($city)): ?>
            <span class="span_town"><?php echo City::getCityPathString($city); ?></span>
            <? endif; ?>

            <div class="row saller_end">
                <div class="col-xs-6">

            <?php
            $id = $data['owner'];
            require __DIR__.'/../decorators/userPanel.php';
            ?>

                </div>
                <div class="col-xs-6">
                    <?= Yii::t('basic', 'Time left')?>:
                    <?= Item::getTimeLeft($data); ?>
                </div>

            </div>
        </div>

<div class="col15-xs-3 text-right">
    <div class="auction_container_item_right">
        <?= Item::getPriceBlock($data); ?>
    </div>
    <div class="auction_container_item_right_info" style="position:relative;float:right">
        <?= Item::getBidsBlock($data) ?>
    </div>
</div>


<?= CHtml::closeTag('div') ?> <hr class="horizontal_line">