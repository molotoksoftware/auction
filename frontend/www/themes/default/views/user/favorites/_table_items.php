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
    ':user_id' => Yii::app()->user->id,
    ':ftype' => 1
);

$sql_fav = Yii::app()->db->createCommand()
        ->select('a.*, bid.price as current_bid,f.created as fcreated,f.favorite_id')
        ->from('auction a')
        ->join('favorites f', 'f.item_id=a.auction_id and f.type=:ftype and f.user_id=:user_id')
        ->leftJoin('bids bid', 'bid.bid_id=a.current_bid');
   //     ->leftJoin('locations l', 'l.location_id=a.location');


if (is_null($limit)) {
    $template = "{items}";
    $pageSize = Yii::app()->params['cabinetTablePageSize'];
} else {
    $template = "{items}";
    $pageSize = $limit;
}

$dataProvider = new CSqlDataProvider($sql_fav->text, array(
    'totalItemCount' => CounterInfo::quantityFavItems(),
    'keyField' => 'auction_id',
    'params' => $params,
    'sort' => array(
        'defaultOrder' => 'a.bidding_date ASC',
        'attributes' => array(
            'date',
        ),
    ),
    'pagination' => array(
        'pageSize' => $pageSize
    ),
        ));
?>

<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'table-items',
    'emptyText' => Yii::t('basic', 'No items'),
    'dataProvider' => $dataProvider,
    'template' => $template,
    'htmlOptions' => array('class' => ''),
    'cssFile' => false,
    'itemsCssClass' => 'table favorite_items',
    'columns' => array(
        array(
            'class'               => 'CCheckBoxColumn',
            'selectableRows'      => 2,
            'name' => 'favorite_id',
        ),
        array(
            'header' => Yii::t('basic', 'Photo'),
            'type' => 'raw',
            'name' => 'auction_id',
            'value' => 'Item::getPreview($data, array("width" => 120, "height" => 120, "class" => "img-thumbnail"))',
            'headerHtmlOptions' => array('class' => '', 'style' => 'width: 115px;'),
            'htmlOptions' => array('class' => '')
        ),
        array(
            'header' => Yii::t('basic', 'Information'),
            'type' => 'raw',
            'name' => 'auction_id',
            'value' => 'Table::getMainInfoRow($data)',
            'headerHtmlOptions' => array('class' => ''),
            'htmlOptions' => array('class' => '')
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
