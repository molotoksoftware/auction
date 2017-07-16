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


Yii::import('frontend.widgets.search.SortSelectorWidget');
Yii::import('frontend.widgets.search.PeriodHtmlSelectorWidget');

$gridId = 'lots-grid';
$scope = !empty($scope) ? $scope : '';
$isUserPageScope = $scope == 'user-page';


$issetLot = UserDataHelper::issetLot($this->user->user_id);

?>

<div class="row top_auction_list margint_top_30">
    <div class="col-xs-4">
        <?php $this->widget('frontend.widgets.search.SortSelectorWidget', [
        'scope'        => SortSelectorWidget::SCOPE_PAGE_AUCTION,
        'dataProvider' => $dataProvider,
    ]); ?>
    </div>
    <div class="col-xs-4">
        <?php $this->widget('frontend.widgets.search.PeriodHtmlSelectorWidget', [
        'scope' => PeriodHtmlSelectorWidget::SCOPE_PAGE_AUCTION,
    ]); ?>
    </div>
    <div class="col-xs-4 text-right">
    <?php
        $this->widget(
            'CLinkPager',
            array(
                'pages' => $dataProvider->getPagination(),
                'maxButtonCount' => 1,
                'firstPageLabel' => Yii::t('First page', 'Auction'),
                'lastPageLabel' => Yii::t('Last page', 'Auction'),
                'selectedPageCssClass' => 'active',
                'prevPageLabel' => '&lt; ',
                'nextPageLabel' => ' &gt;',
                'header' => '',
                'footer' => '',
                'cssFile' => false,
                'htmlOptions' => array(
                    'class' => 'pagination'
                )
            )
        );
        ?>

    </div>
</div>
<hr class="horizontal_line">



<div class ="row top_auctions">
    <div class="col-xs-12">
     <?php $items = $dataProvider->getData(); ?>

     <?php if (count($items) <= 0) : ?>
         <div class="alert alert-info"><?= Yii::t('basic', 'There doesn\'t seem to be any items matching your search result') ?></div>
     <?php endif; ?>

    </div>
</div>


    <?php foreach ($items as $itemKey => $item): ?>

        <?php $this->renderPartial(
            '//auction/_item',
            [
                'data'                => $item,
                /*'owner'               => $ownerModel,*/
                'city'                => isset($cities[$item['id_city']]) ? $cities[$item['id_city']] : null,
                'hasServicePromotion' => isset($servicePromotions[$item['auction_id']]),
                'additional'          => [],
                'is'                  => [],
                'auctionImages'       => isset($auctionsImages[$item['auction_id']])
                    ? $auctionsImages[$item['auction_id']]
                    : [],
            ],
            false
        ); ?>
    <?php endforeach; ?>

<div class="row bottom_auctions">
    <div class="col-xs-6">
        <?php
        $this->widget(
            'frontend.widgets.sizerList.SizerListCookieWidget',
            array(
                'dataProvider' => $dataProvider,
                'sizerCssClass' => 'pagination',
            //    'sizerHeader' => '',
                'sizerAttribute' => 'size',
                'sizerVariants' => array(25, 50, 100)
            )
        );
        ?>
    </div>
    <div class="col-xs-6 text-right">
        <?php
        $this->widget(
            'CLinkPager',
            array(
                'pages' => $dataProvider->getPagination(),
                'maxButtonCount' => 5,
                'firstPageLabel' => Yii::t('First page', 'Auction'),
                'lastPageLabel' => Yii::t('Last page', 'Auction'),
                'selectedPageCssClass' => 'active',
                'prevPageLabel' => '&lt; ',
                'nextPageLabel' => ' &gt;',
                'header' => '',
                'footer' => '',
                'cssFile' => false,
                'htmlOptions' => array(
                    'class' => 'pagination'
                )
            )
        );
        ?>
    </div>
</div>


<?php
cs()->registerScriptFile(bu() . '/js/validate_text_range.js');
