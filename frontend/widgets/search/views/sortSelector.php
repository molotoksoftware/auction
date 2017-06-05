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

/**
 * @var $dataProvider CDataProvider
 */

$sort = $dataProvider->getSort();
$request = Yii::app()->request;
$activeSort = $request->getQuery('sort');

switch ($activeSort) {
    case 'price':
        $activeSortLabel = Yii::t('basic', 'Price');
        $activeSortDirectionLabel = Yii::t('basic','lowest first');
        break;
    case 'price.desc':
        $activeSortLabel = Yii::t('basic', 'Price');
        $activeSortDirectionLabel = Yii::t('basic','highest first');
        break;

    case 'numBids':
        $activeSortLabel = Yii::t('basic', 'Number of bids');
        $activeSortDirectionLabel = Yii::t('basic', 'fewest first');
        break;
    case 'numBids.desc':
        $activeSortLabel = Yii::t('basic', 'Number of bids');
        $activeSortDirectionLabel = Yii::t('basic', 'most first');
        break;

    case 'dateEnd':
        $activeSortLabel = Yii::t('basic', 'Time');
        $activeSortDirectionLabel = Yii::t('basic', 'ending soonest');
        break;
    case 'dateEnd.desc':
        $activeSortLabel = Yii::t('basic', 'Time');
        $activeSortDirectionLabel = Yii::t('basic', 'newly listed');
        break;
    default:
        $activeSortLabel = Yii::t('basic', 'Time');
        $activeSortDirectionLabel = Yii::t('basic', 'ending soonest');
}

$sortCssClass = 'list_selector__options__link';
?>

<div class="dropdown sortSelector">
 <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
     <?= $activeSortLabel ?>: <?= $activeSortDirectionLabel ?>
     <span class="caret"></span>
</button>
<ul class="dropdown-menu">
    <li class="dropdown-header"><?=Yii::t('basic', 'Price')?>:</li>
    <li><?= CHtml::link(
            Yii::t('basic','highest first'),
                $sort->createUrl(Yii::app()->getController(), ['price' => true]),
                ['class' => $sortCssClass]
            ) ?></li>
    <li><?= CHtml::link(
            Yii::t('basic','lowest first'),
                $sort->createUrl(Yii::app()->getController(), ['price' => false]),
                ['class' => $sortCssClass]
            ) ?></li>
    <li class="divider"></li>
    <li class="dropdown-header"><?=Yii::t('basic', 'Number of bids')?>:</li>
    <li><?= CHtml::link(
                Yii::t('basic', 'most first'),
                $sort->createUrl(Yii::app()->getController(), ['numBids' => true]),
                ['class' => $sortCssClass]
            ) ?></li>
    <li><?= CHtml::link(
            Yii::t('basic', 'fewest first'),
                $sort->createUrl(Yii::app()->getController(), ['numBids' => false]),
                ['class' => $sortCssClass]
            ) ?></li>
    <li class="divider"></li>
    <li class="dropdown-header"><?=Yii::t('basic', 'Time')?>:</li>
    <li><?= CHtml::link(
            Yii::t('basic', 'newly listed'),
                $sort->createUrl(Yii::app()->getController(), ['dateEnd' => true]),
                ['class' => $sortCssClass]
            ) ?></li>
    <li><?= CHtml::link(
            Yii::t('basic', 'ending soonest'),
                $sort->createUrl(Yii::app()->getController(), ['dateEnd' => false]),
                ['class' => $sortCssClass]
            ) ?></li>
</ul>
</div>



