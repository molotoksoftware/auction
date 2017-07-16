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

$request = Yii::app()->getRequest();
$gridId = 'history-shopping-table';

$this->renderPartial('//user/_popups/_delete_purchase');

$auction = $request->getParam('Auction');

$params = array(
    ':buyer' => Yii::app()->user->id,
);
$sqlHistoryPurchases = Yii::app()->db->createCommand()
    ->select(
        'a.image,
         a.name,
         s.date,
         a.auction_id, a.owner,
         a.owner,
         a.id_city,
         a.id_country,
         s.price as s_price,
         s.review_my_about_saller,
         s.review_about_my_buyer,
         s.quantity as s_quantity,
         s.amount,
         s.buyer as buyer,
         s.status,
         s.status as sales_status,
         s.sale_id,
         own.login as owner_login,
         own.nick as owner_nick,
         own.pro as owner_pro,
         own.certified as owner_certified,
         own.rating as owner_rating
         '
    )
    ->from('auction a')
    ->join('sales s', 's.item_id=a.auction_id and s.buyer=:buyer')
    ->leftJoin('users own', 'a.owner=own.user_id')
    ->where('s.del_status_buyer=0');


$countHistoryPurchases = CounterInfo::quantityHistoryShopping();
if($countHistoryPurchases == 0 && isset($hide_empty) && $hide_empty) return;

$pageSize = $limit;


// period filter
GridLotFilter::appendQueryToCommand($sqlHistoryPurchases);

if (isset($auction)) {
    $sqlHistoryPurchases->andWhere('a.name LIKE :name');
    $params[':name'] = '%' . CHtml::encode($auction['name']) . '%';
}

// seller filter.
$sqlAllSellers = clone $sqlHistoryPurchases;
$sqlAllSellersParams = $params;
CounterInfo::applyFilterSeller($sqlHistoryPurchases, $params);
$sqlAllSellers->select('a.owner');
$allSellerIds = $sqlAllSellers->queryAll(false, $sqlAllSellersParams);
$allSellerIds = array_map(function($each) { return $each[0]; }, $allSellerIds);
$selectedSeller = intval($request->getParam('seller'));

if ($selectedSeller && array_search($selectedSeller, $allSellerIds) === false) {
    $allSellerIds[] = $selectedSeller;
}

$sellers = Yii::app()
    ->getDb()
    ->createCommand()
    ->select(['user_id', 'nick', 'login'])
    ->from(User::model()->tableName())
    ->where(['in', 'user_id', $allSellerIds])
    ->queryAll();

$sellersArray = [];

foreach ($sellers as $eachSeller) {
    $sellersArray[$eachSeller['user_id']] = $eachSeller['nick'] ? $eachSeller['nick'] : $eachSeller['login'];
}
asort($sellersArray);

$gridFiltersHtml = $this->widget(
    'application.widgets.auction.AuctionGridFilters', [
    'gridId'           => $gridId,
    'showSearchFilter' => true,
    'searchFieldName'  => 'Auction[name]',
    'showBuyerFilter'  => false,
    'showSellerFilter' => true,
    'showPeriodFilter' => true,
    'sellersArray'     => $sellersArray,
], true);

$template = $gridFiltersHtml . $gridViewTemplate;

$dataProvider = new CSqlDataProvider($sqlHistoryPurchases->text, array(
    'totalItemCount' => $countHistoryPurchases,
    'keyField' => 'auction_id',
    'params' => $params,
    'sort' => array(
        'defaultOrder' => 'date DESC',
        'attributes' => array(
            'date',
        ),
    ),
    'pagination' => array(
        'pageSize' => $pageSize
    ),
));
?>


<h3><?= Yii::t('basic', 'Purchase history')?> <?= UI::showQuantityTablHdr($countHistoryPurchases); ?></h3>

<?php if (Yii::app()->user->hasFlash('success')): ?>
<div class="alert alert-success alert-dismissable">
    <?=Yii::app()->user->getFlash('success');?>
</div>
<?php elseif(Yii::app()->user->hasFlash('error')): ?>
<div class="alert alert-warning alert-dismissable">
    <?=Yii::app()->user->getFlash('error');?>
</div>
<?php endif; ?>
<?php

function getDateCellData($data)
{
    $html = TableItem::getDateField($data);

    $html .= TableItem::getUserInfo([
        'login'            => $data['owner_login'],
        'nick'             => $data['owner_nick'],
        'pro'              => $data['owner_pro'],
        'certified'        => $data['owner_certified'],
        'rating'           => $data['owner_rating'],
    ], UserInfo::SCOPE_GRID_LOT_HISTORY_SHOPPING);

    return $html;
}

function getPriceCellData($data)
{
    if ($data["s_quantity"] > 1) {
        $html = sprintf(
            "%s <b>х</b> %s <br><span>%s</span>",
            Item::getPriceFormat($data["s_price"]),
            $data["s_quantity"],
            TableItem::getPriceField($data["amount"])
        );
    } else {
        $html = TableItem::getPriceField($data["s_price"]);
    }

    return $html;
}

$this->widget(
    'zii.widgets.grid.CGridView',
    array(
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'template' => $template,
        'emptyText' => Yii::t('basic', 'No items'),
        'pager' => isset($gridViewPager) ? $gridViewPager : null,
        'pagerCssClass' => 'false',
        'htmlOptions' => array('class' => ''),
        'itemsCssClass' => 'table table-hover grid_cabinet',
        'columns' => array(
            array(
                'class'               => 'CCheckBoxColumn',
                'selectableRows'      => 2,
                'name'                => 'sale_id',
                'checkBoxHtmlOptions' => array('class' => 'checkbox'),
            ),
            array(
                'header' => Yii::t('basic', 'Item'),
                'type' => 'raw',
                'name' => 'name',
                'value' => 'TableItem::getTovarField($data)',
                'headerHtmlOptions' => array('class' => 'th1'),
                'htmlOptions' => array('class' => 'td1')
            ),
            array(
                'header' => Yii::t('basic', 'Date'),
                'name' => 'date',
                'type' => 'raw',
                'value' => 'getDateCellData($data)',
                'headerHtmlOptions' => array('class' => 'th2'),
                'htmlOptions' => array('class' => 'td2')
            ),
            array(
                'header' => Yii::t('basic', 'Price'),
                'type' => 'raw',
                'name' => 'price',
                'value' => 'getPriceCellData($data)',
                'headerHtmlOptions' => array('class' => 'th3'),
                'htmlOptions' => array('class' => 'td3')
            ),
            array(
                'header' => '<span class="span_up"></span>',
                'type' => 'raw',
                'name' => 'review_about_my_buyer',
                'value' => 'TableItem::getReviewBuyerForShopping($data)',
                'headerHtmlOptions' => array('class' => 'th4'),
                'htmlOptions' => array('class' => 'td4')
            ),
            array(
                'header' => '<span class="span_down"></span>',
                'type' => 'raw',
                'name' => 'review_my_about_saller',
                'value' => 'TableItem::getMyReviewBySallerShopping($data)',
                'headerHtmlOptions' => array('class' => 'th5'),
                'htmlOptions' => array('class' => 'td5')
            ),
            array(
                'class' => 'frontend.components.ButtonColumn',
                'header' => Yii::t('basic', 'Actions'),
                'headerHtmlOptions' => array('class' => 'th6'),
                'htmlOptions' => array('class' => 'td6'),
                'template' => '<div>{leave_comment}</div><div>{delete_purchase}</div>',
                'buttons' => array(
                    'leave_comment' => array(
                        'label' => Yii::t('basic', 'Leave feedback'),
                        'options' => array(
                            'class' => 'create-one-review',
                        ),
                        'visible' => function($rowId, $data) {
                                return $data["review_about_my_buyer"]?false:true;
                        },
                        'dataExpression' => array(
                            'data-sale_id'  => '$data["sale_id"]',
                        ),
                        'url' => 'Yii::app()->createUrl("/user/reviews/preCreate", ["role" => Reviews::ROLE_BUYER, "id" => $data["sale_id"]])'
                    ),
                    'delete_purchase' => [
                        'label'          => Yii::t('basic', 'Hide purchase'),
                        'options'        => ['class' => 'js-delete-purchase'],
                        'dataExpression' => [
                            'data-sale_id'  => '$data["sale_id"]',
                            'data-id-table' => function () use ($gridId) {
                                return $gridId;
                            },
                        ],
                    ],
                )
            )
        ),
    )
);

?>

<?php if ($countHistoryPurchases > 0): ?>

  <div class="form-group">
      <label><?= Yii::t('basic', 'Actions with marked')?></label>
      <button class="btn btn-info" id="create_rewiev"><?= Yii::t('basic', 'Leave feedback')?></button>
      <button class="btn btn-danger" id="bulk_delete_purchases"><?= Yii::t('basic', 'Hide purchases')?></button>
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
<?php $form = $this->beginWidget('CActiveForm',['id' => 'reviews-send','action' => '/user/reviews/preCreate']); ?>
<input type=hidden name=role value='2'>
<?php $this->endWidget(); ?>
