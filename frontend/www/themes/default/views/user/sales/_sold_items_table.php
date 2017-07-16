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

/** @var CController $this */
$cs = Yii::app()->clientScript;

$cs->registerScript(
    'sold-items-actions', '

    $(".content_cabinet").on("click", ".contact-block", function(){ 
      return false;

    });
',
    CClientScript::POS_END
);

$gridId = 'sold-items-table';

$this->renderPartial('//user/_popups/_delete_purchase');

$params = [
    ':owner' => Yii::app()->user->id,
];

$sqlHistoryBuyers = Yii::app()->db->createCommand()
    ->select('
         s.sale_id,
         s.date,
         s.price as s_price,
         s.review_my_about_saller,
         s.review_about_my_buyer,
         s.quantity as s_quantity,
         s.buyer as buyer,
         s.seller_id as seller,
         s.amount,
         s.status as sales_status,

         a.image,
         a.name,
         a.auction_id,
         a.owner,
         a.id_city,
         a.id_country,

         own.login as owner_login,
         own.nick as owner_nick,

         c.user_id buyer_id,
         c.login buyer_login,
         c.nick buyer_nick,
         c.email buyer_email,
         c.telephone buyer_telephone,
         '
    )
    ->from('sales s')
    ->leftJoin('auction a', 'a.auction_id=s.item_id')
    ->leftJoin('users own', 'own.user_id=s.seller_id')
    ->leftJoin('users c', 'c.user_id=s.buyer')
    ->where('s.seller_id=:owner AND s.del_status=0');

$count = CounterInfo::quantitySoldItems();

if ($count == 0 && isset($hide_empty) && $hide_empty) return;

$pageSize = $limit;

GridLotFilter::appendQueryToCommand($sqlHistoryBuyers);

if (isset($auction)) {
    $sqlHistoryBuyers->andWhere('a.name LIKE :name');
    $params[':name'] = '%' . $auction . '%';
}

$sqlAllBuyers = clone $sqlHistoryBuyers;
$sqlAllBuyersParams = $params;
CounterInfo::applyFilterBuyer($sqlHistoryBuyers, $params);
$sqlAllBuyers->select('s.buyer');
$allBuyerIds = $sqlAllBuyers->queryAll(false, $sqlAllBuyersParams);

$allBuyerIds = array_map(function ($each) {
    return $each[0];
}, $allBuyerIds);

$buyers = Yii::app()
    ->getDb()
    ->createCommand()
    ->select(['user_id', 'nick', 'login'])
    ->from(User::model()->tableName())
    ->where(['in', 'user_id', $allBuyerIds])
    ->queryAll();

$buyerArray = [];
foreach ($buyers as $eachBuyer) {
    $buyerArray[$eachBuyer['user_id']] = $eachBuyer['nick'] ? $eachBuyer['nick'] : $eachBuyer['login'];
}
asort($buyerArray);

$dataProvider = new CSqlDataProvider($sqlHistoryBuyers->text, [
    'totalItemCount' => $count,
    'keyField' => 'auction_id',
    'params' => $params,
    'sort' => [
        'defaultOrder' => 'date DESC',
        'attributes' => [
            'name', 's_price', 'date', 'buyer_login',
        ],
    ],
    'pagination' => [
        'pageSize' => $pageSize,
    ],
]);

$gridFiltersHtml = $this->widget(
    'application.widgets.auction.AuctionGridFilters', [
    'gridId' => $gridId,
    'searchFieldName' => 'Auction[name]',
    'showSearchFilter' => true,
    'showPeriodFilter' => true,
    'showBuyerFilter' => true,
    'buyersArray' => $buyerArray,

], true);

$template = $gridFiltersHtml . $gridViewTemplate;
?>

    <h3><?= Yii::t('basic', 'Sold')?> <?= UI::showQuantityTablHdr($count); ?></h3>

<?php if (Yii::app()->user->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissable">
        <?= Yii::app()->user->getFlash('success'); ?>
    </div>
<?php elseif (Yii::app()->user->hasFlash('error')): ?>
    <div class="alert alert-warning alert-dismissable">
        <?= Yii::app()->user->getFlash('error'); ?>
    </div>
<?php endif; ?>

<?php

function buyerCellData($id, $nick, $login, $email, $telephone)
{
    echo CHtml::link(User::outUName($nick, $login), ['/' . $login]);
    echo '<br />';
    echo '<div class="form-group"><div class="btn-group">';
    echo CHtml::link(Yii::t('basic', 'Contacts'), '#', [
        'class' => 'dropdown-toggle contact-link',
        'data-user-id' => $id,
        'data-toggle' => 'dropdown',
    ]);
    echo '<div class="dropdown-menu padding15 contact-block">';
    echo '<strong>Email:</strong> ' . $email . '<br><strong>'.Yii::t('basic', 'Phone number').':</strong> ' . $telephone;
    echo '</div></div></div>';
}

function ind_price($price, $quantity, $amount, $params = [])
{
    $result = '';
    $result .= '<span class="price1">' . Item::getPriceFormat($price) . '</span>';

    if ($quantity > 1) {
        $result .= '<div>x ' . $quantity . ' = ' . $amount . '</div>';
    }

    echo $result;
}


$this->widget(
    'zii.widgets.grid.CGridView',
    [
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'template' => $template,
        'emptyText' => Yii::t('basic', 'No items'),
        'pager' => isset($gridViewPager) ? $gridViewPager : null,
        'pagerCssClass' => 'false',
        'htmlOptions' => ['class' => ''],
        'itemsCssClass' => 'table table-hover grid_cabinet',
        //      'filter'        => isset($auction) ? $auction : null,
        'columns' => [
            [
                'class' => 'CCheckBoxColumn',
                'name' => 'sale_id',
                'selectableRows' => 2,
                'checkBoxHtmlOptions' => ['class' => 'ch_sold'],
                //    'cssClassExpression'  => 'Table::getSalesCheckBoxColumnCss($data, "seller")',
            ],
            [
                'header' => Yii::t('basic', 'Item'),
                'type' => 'raw',
                'name' => 'name',
                'value' => 'TableItem::getTovarField($data)',
                'headerHtmlOptions' => ['class' => 'th1'],
                'htmlOptions' => ['class' => 'td1'],
                'filter' => false,
            ],
            [
                'header' => Yii::t('basic', 'Date of sale'),
                'name' => 'date',
                'value' => 'TableItem::getDateField($data)',
                'headerHtmlOptions' => ['class' => 'th2'],
                'htmlOptions' => ['class' => 'td2'],
                'filter' => false,
            ],
            [
                'header' => Yii::t('basic', 'Buyer'),
                'type' => 'raw',
                'name' => 'buyer_login',
                'value' => 'buyerCellData($data["buyer_id"], $data["buyer_nick"], $data["buyer_login"], $data["buyer_email"], $data["buyer_telephone"])',
                'headerHtmlOptions' => ['class' => 'th3'],
                'htmlOptions' => ['class' => 'td3'],
                'filter' => false,
            ],
            [
                'header' => Yii::t('basic', 'Price'),
                'type' => 'raw',
                'name' => 's_price',
                'value' => 'ind_price(
                    $data["s_price"],
                    $data["s_quantity"],
                    $data["amount"],
                    ["data" => $data]
                )',
                'headerHtmlOptions' => ['class' => 'th3'],
                'htmlOptions' => ['class' => 'td3'],
                'filter' => false,
            ],
            [
                'header' => '<span class="span_up"></span>',
                'type' => 'raw',
                'name' => 'review_about_my_buyer',
                'value' => 'TableItem::getMyReviewBySallerForSales($data)',
                'headerHtmlOptions' => ['class' => 'th4'],
                'htmlOptions' => ['class' => 'td4'],
                'filter' => false,
            ],
            [
                'header' => '<span class="span_down"></span>',
                'type' => 'raw',
                'name' => 'review_my_about_saller',
                'value' => 'TableItem::getReviewBuyerForSales($data)',
                'headerHtmlOptions' => ['class' => 'th5'],
                'htmlOptions' => ['class' => 'td5'],
                'filter' => false,
            ],
            [
                'class' => 'frontend.components.ButtonColumn',
                'header' => Yii::t('basic', 'Actions'),
                'headerHtmlOptions' => ['class' => 'th6'],
                'htmlOptions' => ['class' => 'td6'],
                'template' => '<div>{create_review}</div><div>{repub_link}</div><div>{delete_sale}</div>',
                'buttons' => [
                    'create_review' => array(
                        'label' => Yii::t('basic', 'Leave feedback'),
                        'options' => array(
                            'class' => 'create-one-review',
                        ),
                        'visible' => function ($rowId, $data) {
                            return $data["review_my_about_saller"] ? false : true;
                        },
                        'dataExpression' => array(
                            'data-sale_id' => '$data["sale_id"]',
                        ),
                        'url' => 'Yii::app()->createUrl("/user/reviews/preCreate", ["role" => Reviews::ROLE_SELLER, "id" => $data["sale_id"]])'

                    ),
                    'repub_link' => [
                        'label' => Yii::t('basic', 'Republish item'),
                        'options' => [
                            'class' => 'trndf',
                        ],
                        'url' => function ($data) {
                            return Yii::app()->createUrl(
                                '/editor/lot',
                                [
                                    'strepub' => 1,
                                    'id' => $data['auction_id'],
                                ]
                            );
                        },
                        'visible' => 'true'
                        ,
                    ],
                    'delete_sale' => [
                        'label' => Yii::t('basic', 'Delete item'),
                        'options' => ['class' => 'js-delete-purchase'],
                        'dataExpression' => [
                            'data-sale_id' => '$data["sale_id"]',
                            'data-id-table' => function () use ($gridId) {
                                return $gridId;
                            },
                        ],
                    ],
                ],
            ],
        ],
    ]
);

?>
<?php if ($count > 0): ?>

    <div class="form-group">
        <label><?= Yii::t('basic', 'Actions with marked')?>:</label>
        <button class="btn btn-info" id="create_rewiev"><?= Yii::t('basic', 'Leave feedback')?></button>
        <button class="btn btn-danger" id="bulk_delete_purchases"><?= Yii::t('basic', 'Delete')?></button>
    </div>
<?php endif; ?>

<?php
cs()->registerScriptFile('/js/auction.common.js', CClientScript::POS_END);

cs()->registerScript('create_rewiew', "

SendReviews.init ({
    enable: true
});

DeletePurchase.init ({
    enable: true,
    grid_table: '{$gridId}',
});

        ", CClientScript::POS_END);

?>
<?php $form = $this->beginWidget('CActiveForm', ['id' => 'reviews-send', 'action' => '/user/reviews/preCreate']); ?>
    <input type=hidden name=role value='1'>
<?php $this->endWidget(); ?>