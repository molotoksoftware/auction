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
    throw new Exception('для layout необходима глобальная переменная user');
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
            <h2>Лоты пользователя</h2>

        </div>
        <div class="col-xs-3 text-right">
            <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
            <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
            <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir,gplus,twitter" data-size="s"></div>
        </div>
</div>
<hr class="top10 horizontal_line">

<?php // print_r($_GET['Auction']); ?>
<div class ="row">
    <div class="col-xs-3 sidebar_left">
        <?php $this->widget('frontend.widgets.categories.CategoriesWidget', [
            'htmlOptions'              => [
                'class' => 'main_nav profile_cat_tree list-group',
            ],
            'prefix'                   => 'auction',
            'widgetCacheKey'           => 'auction_user_' . $this->user->user_id . '_hour_' . date('h'),
            'countRelationName'        => 'count',
            'categories'               => $this->categories,
            'activeCategory'           => $this->userSelectedCategory,
            'prependAllCategoriesItem' => [
                'label'               => 'Все категории',
                'url'                 => Yii::app()->createUrl(
                    '/user/user/page',
                    ['login' => $this->user->login, 'path' => 'all']
                ),
                'count'               => null,
                'num'                 => null,
                'spec'                => 0,
                'level'               => null,
                'alias'               => '',
                'isAllCategoriesItem' => true,
                'active'              => $this->userSelectedCategory === 0,
                'linkOptions'         => ['class' => 'all-cat-item'],
            ],
            'linkBaseUrl'              => Yii::app()->createUrl('/user/user/page', [
                'login' => $this->user->login,
            ]),
            'itemCssClass' => 'subcat list-group-item',
            'cacheMenuItems' => false
        ]); ?>
        <hr class="horizontal_line">
        <span class="pull-right unset_par">
            <a href="/<?=Yii::app()->getRequest()->getPathInfo(); ?>">сбросить</a>
        </span>
        <b>Тип торгов</b>
        <?php $this->widget('zii.widgets.CMenu', [
        'items'       => [
            [
                'label' => 'Аукцион',
                'url'   => createFilterUrl(
                    Yii::app()->createUrl('/user/user/page', ['login' => $this->user->login]),
                    Auction::TP_TR_STANDART
                ),
                'linkOptions' => [
                    'data-param-name'  => $paramTypeTransaction,
                    'data-param-value' => Auction::TP_TR_STANDART,
                ],
                'active' => $currentTransactionType == Auction::TP_TR_STANDART
            ],
            [
                'label'       => 'Купить сейчас',
                'url'   => createFilterUrl(
                    Yii::app()->createUrl('/user/user/page', ['login' => $this->user->login]),
                    Auction::TP_TR_SALE
                ),
                'linkOptions' => [
                    'data-param-name'  => $paramTypeTransaction,
                    'data-param-value' => Auction::TP_TR_SALE,
                ],
                'active' => $currentTransactionType == Auction::TP_TR_SALE
            ],
            [
                'label'       => 'С 1 рубля',
                'url'   => createFilterUrl(
                    Yii::app()->createUrl('/user/user/page', ['login' => $this->user->login]),
                    Auction::TP_TR_START_ONE
                ),
                'linkOptions' => [
                    'data-param-name'  => $paramTypeTransaction,
                    'data-param-value' => Auction::TP_TR_START_ONE,
                ],
                'active' => $currentTransactionType == Auction::TP_TR_START_ONE
            ],
        ],
        'htmlOptions' => [
            'class' => 'ttf-items',
        ]]); ?>
    <hr class="horizontal_line">
     <?php $this->renderPartial(
            '//auction/_filter',
            [
                'filter'           => $filter,
                'category'         => $category,
                'options'          => $attributeOptions,
                'actionUrlRoute'   => '/user/user/page',
                'actionUrlParams'  => ArrayHelper::merge([
                    'login' => $ownerModel->login,
                    'path'  => Yii::app()->getRequest()->getQuery('path'),
                ], $_GET),
                'hideCitySelector' => true,
            ]
        );  ?>
    </div>
    <div class="col-xs-9">

<? $this->widget('frontend.widgets.user.UserPageLabel', [
    'user'       => $ownerModel,
]); ?>

<? $this->widget('frontend.widgets.user.UserPageTabs', [
    'user'       => $ownerModel,
]); ?>

<?php
        $this->renderPartial('_page/_current_lots_table', [
            'ownerModel'               => $ownerModel,
            'limit'                    => null,
            'auction'                  => $auction,
            'gridCssClass'             => $gridCssClass,
            'gridViewPager'            => $gridViewPager,
            'scope'                    => $scope,
            'gridViewSummaryText'      => $gridViewSummaryText,
            'showRecommendedContainer' => $showRecommendedContainer,
            'filter'                   => $filter,
            'category'                 => $category,
            'cities'                   => $cities,
            'dataProvider'             => $dataProvider,
            'auctionsImages'           => $auctionsImages,
        ]);

?>

    </div>
</div>
