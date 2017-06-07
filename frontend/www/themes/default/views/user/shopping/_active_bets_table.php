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
 * Active bids
 */
if (is_null($limit)) {
  //  $template = "{items}\n{pager}";
    $template = "{items}";
    $pageSize = Yii::app()->params['cabinetTablePageSize'];
} else {
    $template = "{items}";
    $pageSize = $limit;
}

$params = array(
    ':owner' => Yii::app()->user->id,
    ':status' => Auction::ST_ACTIVE
);

$sql = Yii::app()->db->createCommand()
    ->select(
        'a.image, a.owner, a.status, a.bidding_date, a.auction_id, a.category_id, a.name, b.owner as current_leader_bid, b.price as current_price_bid, b.created'
    )
    ->from('auction a')
    ->join('bids bid', 'bid.owner=:owner and bid.lot_id=a.auction_id')
    ->leftJoin('bids b', 'b.bid_id=a.current_bid')
    ->where('a.status=:status')
    ->group('a.auction_id');

$count = CounterInfo::quantityActiveBets(Yii::app()->user->id);


$dataProvider = new CSqlDataProvider($sql->text, array(
    'totalItemCount' => $count,
    'keyField' => 'auction_id',
    'params' => $params,
    'sort' => array(
        'defaultOrder' => 'a.bidding_date ASC'
    ),
    'pagination' => array(
        'pageSize' => $pageSize,
    ),
));

function getState($item)
{
    $res = "<div>".Yii::t('basic', 'Current price').": <span>".Item::getPriceFormat($item['current_price_bid']).'</span></div>';
    $ab = AutoBid::model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'auction_id' => $item['auction_id']));

    if($ab) {
        $res .= '<div>'.Yii::t('basic', 'Max. bid').': <span class="max-bet">' . Item::getPriceFormat($ab->price) . '</span></div>';
    }

    if ($item['current_leader_bid'] == Yii::app()->user->id) {
        $res .= '<div><b style="color:green;">'.Yii::t('basic', 'You are leader').'</div>';
    } else {
        $res .= '<div><b style="color:red;">'.Yii::t('basic', 'Your bid has been outbid').'</div>';
    }

    return $res;
}

?> 
    <h3><?= Yii::t('basic', 'Active bids')?> <?=UI::showQuantityTablHdr($count); ?></h3>
<?php
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
        'dataProvider' => $dataProvider,
        'template' => $template,
        'emptyText' => Yii::t('basic', 'No items'),
        'htmlOptions' => array('class' => ''),
        'itemsCssClass' => 'table table-hover grid_cabinet',
        'columns' => array(
            array(
                'header' => Yii::t('basic', 'Item'),
                'type' => 'raw',
                'name' => 'name',
                'value' => 'TableItem::getTovarField($data)',
                'headerHtmlOptions' => array('class' => 'th1'),
                'htmlOptions' => array('class' => 'td1')
            ),
            array(
                'header' => Yii::t('basic', 'Information'),
                'type' => 'raw',
                'name' => 'name',
                'value' => 'getState($data)',
                'headerHtmlOptions' => array('class' => ''),
                'htmlOptions' => array('class' => 'td3')
            ),
            array(
                'header' => Yii::t('basic', 'Time left'),
                'type' => 'raw',
                'name' => 'name',
                'value' => 'Item::getTimeLeft($data)',
                'headerHtmlOptions' => array('class' => ''),
                'htmlOptions' => array('class' => 'td4')
            ),
        ),
    )
);
?>
<div class="row bottom_auctions">
    <div class="col-xs-6">
        <?php /*
        $this->widget(
            'frontend.widgets.sizerList.SizerListCookieWidget',
            array(
                'dataProvider' => $dataProvider,
                'sizerCssClass' => 'pagination',
            //    'sizerHeader' => '',
                'sizerAttribute' => 'size',
                'sizerVariants' => array(25, 50, 100)
            )
        ); */
        ?>
    </div>
    <div class="col-xs-6 text-right">
        <?php
        $this->widget(
            'CLinkPager',
            array(
                'pages' => $dataProvider->getPagination(),
                'maxButtonCount' => 5,
                'firstPageLabel' => Yii::t('basic', 'First page'),
                'lastPageLabel' => Yii::t('basic', 'Last page'),
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