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


/*
 * Активные лоты таблица
 */
/** @var Controller $this */
/** @var null|int $limit */
/** @var null|string $gridViewTemplate */
/** @var array $userCategoriesList */
/** @var null|array $searchAuctionCategoryIds */
/** @var null|string $gridCssClass */
/** @var null|string $gridViewPager */
/** @var null|string $gridViewSummaryText */
/** @var null|string $gridViewAfterAjaxUpdate */

$gridId = 'grid-active-lots';


$params = array(
    ':owner' => Yii::app()->user->id,
    ':status' => Auction::ST_ACTIVE,
);

$pageSize = $limit;

if (!empty($gridViewTemplate)) {
    $template = $gridViewTemplate;
}

/** @var CDbCommand $sql */
$sql = Yii::app()->db->createCommand()
    ->select(
        'a.image, a.auction_id, a.is_auto_republish, a.category_id, a.favorites_count, a.starting_price,a.price, a.type,
        a.bidding_date ,a.status, a.owner, a.name as name, a.viewed, a.bid_count, a.quantity, a.quantity_sold,
        a.id_city, a.id_country,
        b.owner as current_leader_bid, b.price as current_price_bid, a.current_bid, a.created, b.created as bid_created,
        u.nick as bid_leader_nick, u.login as bid_leader_login'
    )
    ->from('auction a')
    ->leftJoin('bids b', 'b.bid_id=a.current_bid')
    ->leftJoin('users u', 'u.user_id=b.owner')
    ->where('a.status=:status and a.owner=:owner');

if(isset($auction)) {
    if ($auction->name) {
        $sql->andWhere('a.name LIKE :name');
        $params[':name'] = '%' . $auction->name . '%';
    }
    if (!empty($searchAuctionCategoryIds)) {
        $sql->andWhere(array('in', 'category_id', $searchAuctionCategoryIds));
    }
}

$count = CounterInfo::quantityActiveLots(
    isset($searchAuctionCategoryIds) ? $searchAuctionCategoryIds : array(),
    isset($auction->name) ? $auction->name : ''
);
if($count == 0 && isset($hide_empty) && $hide_empty) return;

$dataProvider = new CSqlDataProvider($sql->text, array(
    'totalItemCount' => $count,
    'keyField' => 'auction_id',
    'params' => $params,
    'sort' => array(
        // 'modelClass' => 'Auction',
        'attributes' => array('name',
            'price' => array(
                'asc' => 'IF(starting_price = 0, a.price, starting_price) ASC',
                'desc' => 'IF(starting_price = 0, a.price, starting_price) DESC'
            ),
        'bidding_date', 'bid_created', 'viewed', 'bid_count'),
        'defaultOrder' => 'bid_created DESC'
    ),
    'pagination' => array(
        'pageSize' => $pageSize
    ),
));

function getLinkEdit($data)
{
    if ($data['owner'] == Yii::app()->user->id) {
        if ($data['type'] == BaseAuction::TYPE_AUCTION) {
            $link = '/editor/lot';
        }
        return Yii::app()->createUrl($link, array('id' => $data['auction_id']));
    }
    return '#';
}


function getLinkRemoveTrading($data)
{
    return Yii::app()->createUrl(
        '/editor/removeTrading',
        array(
            'type' => 'item',
            'id' => $data['auction_id'],
            'returnUrl' => 'activeItems'
        )
    );
}


function getPricess($data, $params = array())
{
    $price = $data['price'];
    $starting_price = $data['starting_price'];
    $prc = "";

    $prefix = '';
    if (!empty($params['showLatestBet'])) {
        if (!empty($data['current_bid'])) {
            $prefix =  Item::getPriceFormat($data['current_price_bid']);
        }
    }

    if ($price == '0.00' && $starting_price != '0.00') {
        $prc = '<div class="price1"><p class="disp_inl_bl">' . ($prefix ? $prefix . '<br /> (' : '') . FrontBillingHelper::getUserPriceWithCurrency($starting_price) . ($prefix ? ')' : '') . '</p></div>';
    }
    if ($price != '0.00' && $starting_price == '0.00') {
        $prc = '<div class="price2"><p class="disp_inl_bl">' . ($prefix ? $prefix . '<br /> (' : '') . FrontBillingHelper::getUserPriceWithCurrency($price) . ($prefix ? ')' : '') . '</p></div>';
    }
    if ($price != '0.00' && $starting_price != '0.00') {
        $prc = '<div class="price1"><p class="disp_inl_bl">' . ($prefix ? $prefix . '<br /> (' : '') . FrontBillingHelper::getUserPriceWithCurrency($starting_price) . ($prefix ? ')' : '') . '</p></div><div class="price2"><p class="disp_inl_bl">' . FrontBillingHelper::getUserPriceWithCurrency($price) . '</p></div>';
    }


    return $prc;
}

function getViewed_urls2($data)
{
    $result = '';
    if(empty($data['viewed'])) $data['viewed'] = 0;
    if (isset($data['auction_id'])) {$result = '<a href="/user/cabinet/viewed/type/0/id/'.$data['auction_id'].'">'.$data['viewed'].'</a>';}
    $result .= '<div title="В избранном" style="display:inline-block; width: 27px; margin-left: 8px;"><span class="favorites-icon"></span> '.$data['favorites_count'].'</div>';
    return $result;
}

?>

<?php

cs()->registerScript(
    'init-grid',
    '
        function setAutoRepubTitle(obj) {
            if($(obj).hasClass("inactive")) return $(obj).attr("title", "Автоперевыставление лота");
            $(obj).attr("title", $(obj).hasClass("active") ? "Отменить перевыставление" : "Активировать перевыставление")
        }

        function initGrid() {
            $(".active_items_autorepub").each(function() { setAutoRepubTitle(this); });

            $(".active_items_autorepub").click(function() {
                if($(this).hasClass("inactive")) return;
                $(this).toggleClass("active");
                setAutoRepubTitle(this);

                $.ajax("/editor/setAutoRepublish/id/" + $(this).attr("data-id") + "/auto_republish/" + ($(this).hasClass("active") ? 1 : 0));
            });
        }

    ',
    CClientScript::POS_BEGIN
);
cs()->registerScript(
	'create-close-all', '

    initGrid();

    $("#action_mass_autorepub").click(function() {
        if ($("input.ch_act:checkbox:checked").length == 0)
        {
            alert("Необходимо отметить, как минимум, один лот");
        } else {
            var form = $("#autorepub_mass_form");
            var i = 0;

            $("input.ch_act:checkbox:checked").each(function(){
                i++;
                form.append($("<input>").attr("type", "hidden").attr("name", "Auction[" + i + "]").val($(this).val()));
            });

            form.submit();
        }
    });

    $("#action_close_all").click(function()
    {
        var count_ch = $("input.ch_act:checkbox:checked").length;
        
        if (count_ch == 0)
        {
            alert("Необходимо отметить, как минимум, один завершённый лот");
        }
        else
        {
            if (confirm("Вы действительно хотите снять выбранные лоты с торгов? Если по лоту имеются ставки, Ваш рейтинг будет уменьшен на единицу."))
            {
                $("input.ch_act:checkbox:checked").each(function(e){
                    var id = $(this).val();
                    $.ajax({
                        url: "/editor/removeTrading/type/item/id/" + id,
                        success: function(data) {
                            if (e == 0) {location.reload();}
                        }
                    });
                });
            }
        }
    });
',
	CClientScript::POS_READY
); 
?>

<?  Yii::app()->clientScript->registerScript('active_lots_grid', '
    $(".yiiPager > li > a").on("click", function() {
        updateWindowUrl($(this).attr("href"));
    });
    $(document).on("change", "#Auction_category_id", function() {
        setParamToPageUrl("Auction[category_id]", $(this).val());
    });
')  ?>
<h3>Активные лоты <?=UI::showQuantityTablHdr($count); ?></h3>
<?php

$gridFiltersHtml = $this->widget(
    'application.widgets.auction.AuctionGridFilters', [
    'gridId'            => $gridId,
    'searchFieldName'   => 'Auction[name]',
    'showSearchFilter'  => true,
    'auction'           => $auction,
    'userCategoriesList' => $userCategoriesList,
    'showSortCategory' => true,
], true); 

$template = $gridFiltersHtml . $template;

?>
<?php

function bidCellData($data) {
    $html = '<small><strong>'.TableItem::getAuctionBidCount($data).'</strong></small>';
    $html .= TableItem::getAuctionBidLeaderLink($data);
    return $html;
}

/** @var CGridView $grid */
$grid = $this->widget(
    'zii.widgets.grid.CGridView',
    array(
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'template' => $template,
        'enableSorting' => true,
        'emptyText' => 'Активные лоты отсутствуют',
        'htmlOptions' => array('class' => ''),
        'itemsCssClass' => 'table table-hover grid_cabinet margint_top',
        'pager' => isset($gridViewPager) ? $gridViewPager : null,
        'pagerCssClass' => 'false',
        'summaryText' => !empty($gridViewSummaryText) ? $gridViewSummaryText : null,
        'afterAjaxUpdate' => isset($gridViewAfterAjaxUpdate) ? $gridViewAfterAjaxUpdate : null,
        'columns' => array(
            array(
                'class' => 'CCheckBoxColumn',
                'selectableRows' => 2,
                'checkBoxHtmlOptions' => array('class' => 'ch_act')
            ),
            array(
                'header' => 'Товар',
                'type' => 'raw',
                'name' => 'name',
                'value' => 'TableItem::getTovarFieldActlLotTables($data, array(
                    "showQuestions" => true,
                    "showQuantityAndSold" => true
                ))',
                'headerHtmlOptions' => array('class' => 'th1 auction_name_column'),
                'htmlOptions' => array('class' => 'td1'),

            ),
            array(
                'header' => 'До окончания',
                'type' => 'raw',
                'name' => 'bidding_date',
                'value' => 'TableItem::getTimeLestFieldActlLotTables($data, ["showPeriod" => true])',
                'headerHtmlOptions' => array('class' => 'endDate_clumn'),
                'htmlOptions' => array('class' => 'td3'),
            ),
            array(
                'header' => 'Цена',
                'type' => 'raw',
                'name' => 'price',
                'value' => 'getPricess($data, array("showLatestBet" => true))',
                'headerHtmlOptions' => array('class' => 'prices_column'),
                'htmlOptions' => array('class' => 'td3 text_al_left'),
                'filter' => false
            ),
            array(
                'header' => 'Ставки',
                'type' => 'raw',
                'name' => 'bid_count',
                'value' => 'bidCellData($data)',
                'headerHtmlOptions' => array('class' => 'bet_column'),
                'htmlOptions' => array('class' => ''),
                'filter' => false
            ),
            array(
                'header' => 'Просм.',
                'type' => 'raw',
                'name' => 'viewed',
                'value' => 'getViewed_urls2($data)',
                'headerHtmlOptions' => array('class' => 'views_column'),
                'htmlOptions' => array('class' => ''),
                'filter' => false
            ),
            array(
                'class' => 'frontend.components.ButtonColumn',
                'header' => 'Действия',
                'headerHtmlOptions' => array('class' => 'th6'),
                'htmlOptions' => array('class' => 'td6'),
                'template' => '<div>{long_term_completed}</div><div>{remove_trading}</div><div>{edit}</div>',
                'buttons' => array(
                    'long_term_completed' => array(
                        'label' => 'Завершить досрочно',
                        'options' => array(
                            'class' => 'long_term_completed',
                            'onclick' => 'return confirm("Вы действительно хотите завершить торги досрочно? Лот будет продан по последней наивысшей ставке.")'
                        ),
                        'visible' => function ($row, $data) {
                            return (empty($data['current_bid'])) ? false : true;
                        },
                        'url' => function ($data) {
                            return Yii::app()->createUrl(
                                '/editor/longTermCompleted',
                                array(
                                    'id' => $data['auction_id']
                                )
                            );
                        }
                    ),
                    'edit' => array(
                        'label' => 'Редактировать',
                        'options' => array(
                            'class' => '',
                        ),
                        'visible' => function ($row, $data) {
                            return (empty($data['current_bid'])) ? true : false;
                        },
                        'url' => 'getLinkEdit($data)'
                    ),
                    'remove_trading' => array(
                        'label' => 'Cнять с торгов',
                        'options' => array(
                            'class' => '',
                            'onclick' => 'return confirm("Вы действительно хотите снять лот с торгов? Если  по лоту имеются ставки, Ваш рейтинг будет уменьшен на единицу.")'

                        ),
//                        'visible' => function ($row, $data) {
//                            return (empty($data['current_bid']))?true:false;
//                        },
                        'url' => 'getLinkRemoveTrading($data)'
                    ),
                )
            )
        ),
    )
);
?>

<?php if ($count > 0): ?>
    <div style="line-height: 32px; margin-bottom: 20px !important;">
        <?php $this->widget('application.widgets.auction.AuctionBulkChanges', [
            'lotsCount'            => $dataProvider->getItemCount(),
            'gridId'               => $gridId,
        ]); ?>
    </div>
<?php endif; ?>