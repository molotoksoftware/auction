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
        $activeSortLabel = 'По цене';
        $activeSortDirectionLabel = 'по возрастанию';
        break;
    case 'price.desc':
        $activeSortLabel = 'По цене';
        $activeSortDirectionLabel = 'по убыванию';
        break;

    case 'numBids':
        $activeSortLabel = 'По ставкам';
        $activeSortDirectionLabel = 'по возрастанию';
        break;
    case 'numBids.desc':
        $activeSortLabel = 'По ставкам';
        $activeSortDirectionLabel = 'по убыванию';
        break;

    case 'dateEnd':
        $activeSortLabel = 'Время до окончания';
        $activeSortDirectionLabel = 'по возрастанию';
        break;
    case 'dateEnd.desc':
        $activeSortLabel = 'Время до окончания';
        $activeSortDirectionLabel = 'по убыванию';
        break;
    default:
        $activeSortLabel = 'Время до окончания';
        $activeSortDirectionLabel = 'по возрастанию';
}

$sortCssClass = 'list_selector__options__link';
?>

<div class="dropdown sortSelector">
 <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
     <?= $activeSortLabel ?>: <?= $activeSortDirectionLabel ?>
     <span class="caret"></span>
</button>
<ul class="dropdown-menu">
    <li class="dropdown-header">По цене:</li>
    <li><?= CHtml::link(
                'по убыванию',
                $sort->createUrl(Yii::app()->getController(), ['price' => true]),
                ['class' => $sortCssClass]
            ) ?></li>
    <li><?= CHtml::link(
                'по возрастанию',
                $sort->createUrl(Yii::app()->getController(), ['price' => false]),
                ['class' => $sortCssClass]
            ) ?></li>
    <li class="divider"></li>
    <li class="dropdown-header">По ставкам:</li>
    <li><?= CHtml::link(
                'по убыванию',
                $sort->createUrl(Yii::app()->getController(), ['numBids' => true]),
                ['class' => $sortCssClass]
            ) ?></li>
    <li><?= CHtml::link(
                'по возрастанию',
                $sort->createUrl(Yii::app()->getController(), ['numBids' => false]),
                ['class' => $sortCssClass]
            ) ?></li>
    <li class="divider"></li>
    <li class="dropdown-header">Время до окончания:</li>
    <li><?= CHtml::link(
                'по убыванию',
                $sort->createUrl(Yii::app()->getController(), ['dateEnd' => true]),
                ['class' => $sortCssClass]
            ) ?></li>
    <li><?= CHtml::link(
                'по возрастанию',
                $sort->createUrl(Yii::app()->getController(), ['dateEnd' => false]),
                ['class' => $sortCssClass]
            ) ?></li>
</ul>
</div>



