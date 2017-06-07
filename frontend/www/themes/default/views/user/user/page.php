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

if (is_null($this->user)) {
    throw new Exception('Loading UserModel error');
}

$paramTypeTransaction = CHtml::activeName(new Auction(), 'type_transaction');

function createFilterUrl($url, $transactionValue)
{
    $params = $_GET;
    $paramTypeTransaction = CHtml::activeName(new Auction(), 'type_transaction');
    $params[$paramTypeTransaction] = $transactionValue;
    return $url . '?' . http_build_query($params);
}

$currentTransactionType = '-1';
if (isset($_GET['Auction']['type_transaction']) && $_GET['Auction']['type_transaction'] !== '') {
    $currentTransactionType = $_GET['Auction']['type_transaction'];
}

$issetLot = UserDataHelper::issetLot($this->user->user_id);

?>

<div class="row auction">
    <div class="col-xs-9">
        <h2><?= Yii::t('basic', 'Items of') ?> <?= $this->user->getNickOrLogin(); ?></h2>

    </div>
    <div class="col-xs-3 text-right">
        <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
        <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
        <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir,gplus,twitter"
             data-size="s"></div>
    </div>
</div>
<hr class="top10 horizontal_line">

<div class="row">
    <div class="col-xs-3 sidebar_left">
        <?php $this->widget('frontend.widgets.category_search.CategorySearchWidget', [
            'auc_id_arr' => $auc_id_arr,
            'userLogin' => $this->user->login,
        ]); ?>
        <hr class="horizontal_line">
        <span class="pull-right unset_par">
            <a href="/<?= Yii::app()->getRequest()->getPathInfo(); ?>"><?= Yii::t('basic', 'view all') ?></a>
        </span>
        <b><?= Yii::t('basic', 'Type of sell') ?></b>
        <?php $this->widget('zii.widgets.CMenu', [
            'items' => [
                [
                    'label' => Yii::t('basic', 'Auction'),
                    'url' => createFilterUrl(
                        Yii::app()->createUrl('/user/user/page', ['login' => $this->user->login]),
                        Auction::TP_TR_STANDART
                    ),
                    'linkOptions' => [
                        'data-param-name' => $paramTypeTransaction,
                        'data-param-value' => Auction::TP_TR_STANDART,
                    ],
                    'active' => $currentTransactionType == Auction::TP_TR_STANDART
                ],
                [
                    'label' => Yii::t('basic', 'Buy Now'),
                    'url' => createFilterUrl(
                        Yii::app()->createUrl('/user/user/page', ['login' => $this->user->login]),
                        Auction::TP_TR_SALE
                    ),
                    'linkOptions' => [
                        'data-param-name' => $paramTypeTransaction,
                        'data-param-value' => Auction::TP_TR_SALE,
                    ],
                    'active' => $currentTransactionType == Auction::TP_TR_SALE
                ],
                [
                    'label' => Yii::t('basic', 'From') . ' ' . PriceHelper::formate(1),
                    'url' => createFilterUrl(
                        Yii::app()->createUrl('/user/user/page', ['login' => $this->user->login]),
                        Auction::TP_TR_START_ONE
                    ),
                    'linkOptions' => [
                        'data-param-name' => $paramTypeTransaction,
                        'data-param-value' => Auction::TP_TR_START_ONE,
                    ],
                    'active' => $currentTransactionType == Auction::TP_TR_START_ONE
                ],
            ],
            'htmlOptions' => [
                'class' => 'ttf-items list-unstyled nomark type_transaction',
            ]]); ?>
        <hr class="horizontal_line">
        <?php $this->renderPartial(
            '//auction/_filter',
            [
                'filter' => $filter,
                'category' => $category,
                'options' => $attributeOptions,
                'actionUrlRoute' => '/user/user/page',
                'actionUrlParams' => ArrayHelper::merge([
                    'login' => $ownerModel->login,
                    'path' => Yii::app()->getRequest()->getQuery('path'),
                ], $_GET),
                'hideCitySelector' => true,
            ]
        ); ?>
    </div>
    <div class="col-xs-9">

        <? $this->widget('frontend.widgets.user.UserPageLabel', [
            'user' => $ownerModel,
        ]); ?>

        <? $this->widget('frontend.widgets.user.UserPageTabs', [
            'user' => $ownerModel,
        ]); ?>

        <?php
        $this->renderPartial('_page/_current_lots_table', [
            'ownerModel' => $ownerModel,
            'limit' => null,
            'auction' => $auction,
            'gridCssClass' => $gridCssClass,
            'gridViewPager' => $gridViewPager,
            'scope' => $scope,
            'gridViewSummaryText' => $gridViewSummaryText,
            'showRecommendedContainer' => $showRecommendedContainer,
            'filter' => $filter,
            'category' => $category,
            'cities' => $cities,
            'dataProvider' => $dataProvider,
            'auctionsImages' => $auctionsImages,
        ]);

        ?>

    </div>
</div>
