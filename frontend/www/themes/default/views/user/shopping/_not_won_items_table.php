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

$params = array(
    ':owner' => Yii::app()->user->id,
    ':buyer' => Yii::app()->user->id,
    ':status_1' => Auction::ST_SOLD_BLITZ,
    ':status_2' => Auction::ST_COMPLETED_SALE
);

$sql = Yii::app()->db->createCommand()
    ->select('a.*, s.buyer, s.date as date, s.price as s_price')
    ->from('bids b')
    ->join('auction a', 'b.lot_id=a.auction_id')
    ->join('sales s', 's.sale_id=a.sales_id and s.buyer<>:buyer')
    ->where('b.owner=:owner')
    ->andWhere('a.status=:status_1 or a.status=:status_2')
    ->andWhere("s.date >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') - INTERVAL 100 MONTH")
    ->group('a.auction_id');

$count = CounterInfo::quantityNoWonItems();

if (is_null($limit)) {
 //   $template = "{items}\n{pager}";
    $template = "{items}";
    $pageSize = Yii::app()->params['cabinetTablePageSize'];
} else {
    $template = "{items}";
    $pageSize = $limit;
}

$dataProvider = new CSqlDataProvider($sql->text, array(
    'totalItemCount' => $count,
    'keyField' => 'auction_id',
    'params' => $params,
    'sort' => array(
        'defaultOrder' => 'date DESC',
        /*'attributes' => array(
            'date',
        ),*/
    ),
    'pagination' => array(
        'pageSize' => $pageSize
    ),
        ));
?>
<h3><?=Yii::t('basic', 'Didn\'t win');?> <?=UI::showQuantityTablHdr($count); ?></h3>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
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
            'header' => Yii::t('basic', 'Date of sale'),
            'name' => 'date',
            'value' => 'TableItem::getDateField($data)',
            'headerHtmlOptions' => array('class' => 'th2'),
            'htmlOptions' => array('class' => 'td2')
        ),
        array(
            'header' => Yii::t('basic', 'Price'),
            'type' => 'raw',
            'name' => 'price',
            'value' => 'TableItem::getPriceField($data["s_price"])',
            'headerHtmlOptions' => array('class' => 'th3'),
            'htmlOptions' => array('class' => 'td3')
        )
    ),
));
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