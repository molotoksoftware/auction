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

/** @var null|int $limit */
/** @var null|string $gridViewTemplate */
/** @var array $userCategoriesList */
/** @var null|array $searchAuctionCategoryIds */
/** @var null|string $gridViewPager */
/** @var null|string $gridViewSummaryText */
/** @var null|string $gridViewAfterAjaxUpdate */

/** @var CHttpRequest $request */
$request = Yii::app()->getRequest();

Yii::app()->clientScript->registerScriptFile(bu() . '/js/auction_view_owner.js');

$gridId = 'unit-owner-grid';

Yii::app()->clientScript->registerScript('windowHistoryPushState', '
$(".yiiPager > li > a").on("click", function() {
    window.history.pushState({}, "", $(this).attr("href"));
});
');

$pageSize = $limit;

if (!empty($gridViewTemplate)) {
    $template = $gridViewTemplate;
}

$params = array(
    ':owner' => Yii::app()->user->id,
    ':status' => Auction::ST_COMPLETED_EXPR_DATE
);

/** @var CDbCommand $sql */
$sql = Yii::app()->db->createCommand()
    ->select(
        'a.image, a.auction_id, a.category_id, a.name, a.owner, a.bidding_date, a.viewed, a.type,
        a.status, a.price, a.starting_price, a.id_city, a.id_country,
        c.login, c.rating, c.pro, c.certified'
    )
    ->from('auction a')
    ->leftJoin('sales b', 'b.sale_id=a.sales_id')
    ->leftJoin('users c', 'c.user_id=b.buyer')
    ->where('a.status=:status and owner=:owner');

/** @var CDbCommand $count_sql */
$count_sql = Yii::app()->db->createCommand()
    ->select('COUNT(*)')
    ->from('auction a')
    ->where('a.status=:status and owner=:owner');

$userModel = Getter::userModel();
$isPro = $userModel->getIsPro();

if (isset($auction)) {
    if ($auction->name) {
        $sql->andWhere('a.name LIKE :name');
        $count_sql->andWhere('a.name LIKE :name');
        $params[':name'] = '%' . $auction->name . '%';
    }
    if (!empty($searchAuctionCategoryIds)) {
        $sql->andWhere(array('in', 'category_id', $searchAuctionCategoryIds));
        $count_sql->andWhere(array('in', 'category_id', $searchAuctionCategoryIds));
    }
}

$count = $count_sql->queryScalar($params);

$dataProvider = new CSqlDataProvider($sql->text, array(
    'totalItemCount' => $count,
    'keyField' => 'auction_id',
    'params' => $params,
    'sort' => array(
        'attributes' => array('name',
            'price' => array(
                'asc' => 'IF(starting_price = 0, a.price, starting_price) ASC',
                'desc' => 'IF(starting_price = 0, a.price, starting_price) DESC'
            ),
            'bidding_date', 'bid_created', 'viewed'),
        'defaultOrder' => 'a.bidding_date DESC'
    ),
    'pagination' => array(
        'pageSize' => $pageSize
    ),
));

function getLinkRePost($auction_id)
{
    return '/editor/lot/strepub/1/id/' . $auction_id;
}

function getLinkDel($data)
{
    return '/user/sales/del_lot/id/' . $data['auction_id'];
}

$cs = Yii::app()->clientScript;

$cs->registerScript(
    'create-act', '

    $(".send_mass_reopen").click(function()
    {
        var period = $(".mass_select option:selected").val();
        var count_ch = $("input.ch_cl:checkbox:checked").length;
        
        if (count_ch == 0)
        {
            alert("'.Yii::t('basic', 'You need to mark one or more items').'");
        }
        else
        {
            if (period != "")   
            {
                var i = 0; var check_lots =  new Array();
                $("input.ch_cl:checkbox:checked").each(function(){
                    i++; check_lots[i] = $(this).val();
                });

                $.ajax({
                    url      : "/user/sales/workwithlots",
                    data     : {"check_lots": check_lots, "select_action": 1, "period": period},
                    type     : "POST",
                    success : function(data) {
                        location.reload();
                    }
                });
            }
            else {alert("'.Yii::t('basic', 'You need to choose the period').'");}
        }
    });

    $("#action_sale_del").click(function()
    {
        var count_ch = $("input.ch_cl:checkbox:checked").length;

        if (count_ch == 0)
        {
            alert("'.Yii::t('basic', 'You need to mark one or more items').'");
        }
        else
        {
            var i = 0; var check_lots =  new Array();
            $("input.ch_cl:checkbox:checked").each(function(){
                i++; check_lots[i] = $(this).val();
            });

            if (confirm("'.Yii::t('basic', 'Do you really want to remove marked items?').'"))
            {
                $.ajax({
                    url      : "/user/sales/workwithlots",
                    data     : {"check_lots": check_lots, "select_action": 2},
                    type     : "POST",
                    success : function(data) {
                        location.reload();
                    }
                });
            }
        }

    return false;
    });

    $("#unit-owner").hide();

    $("#action_sale").click(function(){ 
      return true;
    });
    $("#drop_down_box").click(function(){ 
      return false;

    });
',
    CClientScript::POS_READY
);

?>

    <h3><?= Yii::t('basic', 'Unsold')?> <?= UI::showQuantityTablHdr($count); ?></h3>

<? Yii::app()->clientScript->registerScript('active_lots_grid', '
    $(".yiiPager > li > a").on("click", function() {
        updateWindowUrl($(this).attr("href"));
    });
    $(document).on("change", "#Auction_category_id", function() {
        setParamToPageUrl("Auction[category_id]", $(this).val());
    });
') ?>

<?php

function show_date($data)
{


    return '<small class="show_date_sold_items">' . $data['bidding_date'] . '</small>';
}

function getViewed_urls($data)
{
    $result = '';
    if (isset($data['auction_id']) && isset($data['viewed'])) {
        $result = '<a href="/user/cabinet/viewed/type/0/id/' . $data['auction_id'] . '">' . $data['viewed'] . '</a>';
    }
    return $result;
}

function getPrice($data, $params = [])
{
    $price = Item::getPriceFormat($data['price']);
    $starting_price = Item::getPriceFormat($data['starting_price']);

    $html = '<div class="price1"><p>-<span></span></p></div>';


    if ($price == '0.00' && $starting_price != '0.00') {
        $showPrice = $starting_price;
        $html = '<div class="price1"><p>' . $showPrice . '<span></span></p></div>';
    }
    if ($price != '0.00' && $starting_price == '0.00') {
        $showPrice = $price;
        $html = '<div class="price2"><p>' . $showPrice . '<span></span></p></div>';
    }
    if ($price != '0.00' && $starting_price != '0.00') {
        $showPrice1 = $starting_price;

        $html = '<div class="price1"><p>' . $showPrice1 . '<span></span></p></div>';
        $showPrice2 = $price;
        $html .= '<div class="price2"><p>' . $showPrice2 . '<span></span></p></div>';
    }

    return $html;
}


$gridFiltersHtml = $this->widget(
    'application.widgets.auction.AuctionGridFilters', [
    'gridId' => $gridId,
    'searchFieldName' => 'Auction[name]',
    'showSearchFilter' => true,
    'auction' => $auction,
    'userCategoriesList' => $userCategoriesList,
    'showSortCategory' => true,
], true);

$template = $gridFiltersHtml . $template;


/** @var CGridView $grid */
$grid = $this->widget(
    'zii.widgets.grid.CGridView',
    array(
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'template' => $template,
        'enableSorting' => true,
        'emptyText' => Yii::t('basic', 'No items'),
        'htmlOptions' => array('class' => '', 'style' => 'margin: 0px;'),
        'itemsCssClass' => 'table table-hover grid_cabinet',
        'pager' => isset($gridViewPager) ? $gridViewPager : null,
        'pagerCssClass' => 'false',
        'summaryText' => !empty($gridViewSummaryText) ? $gridViewSummaryText : null,
        'afterAjaxUpdate' => isset($gridViewAfterAjaxUpdate) ? $gridViewAfterAjaxUpdate : null,
        'columns' => array(
            array(
                'class' => 'CCheckBoxColumn',
                'selectableRows' => 2,
                'checkBoxHtmlOptions' => array('class' => 'ch_cl'),
            ),
            array(
                'header' => Yii::t('basic', 'Item'),
                'type' => 'raw',
                'name' => 'name',
                'value' => 'TableItem::getTovarField($data, array("showQuestions" => true))',
                'headerHtmlOptions' => array('class' => 'th1 auction_name_column'),
                'htmlOptions' => array('class' => 'td1')
            ),
            array(
                'header' => Yii::t('basic', 'Time left'),
                'type' => 'raw',
                'name' => 'bidding_date',
                'value' => 'show_date($data)',
                'headerHtmlOptions' => array('class' => 'endDate_clumn'),
                'htmlOptions' => array('class' => 'td3'),
            ),
            array(
                'header' => Yii::t('basic', 'Price'),
                'type' => 'raw',
                'name' => 'price',
                'value' => 'getPrice($data)',
                'headerHtmlOptions' => array('class' => 'prices_column'),
                'htmlOptions' => array('class' => 'td3 text_al_left'),
                'filter' => false
            ),
            array(
                'header' => Yii::t('basic', 'Views'),
                'type' => 'raw',
                'name' => 'viewed',
                'value' => 'getViewed_urls($data)',
                'headerHtmlOptions' => array('class' => 'views_column'),
                'htmlOptions' => array('class' => 'hh_'),
                'filter' => false
            ),
            array(
                'class' => 'frontend.components.ButtonColumn',
                'header' => Yii::t('basic', 'Actions'),
                'headerHtmlOptions' => array('class' => 'th6'),
                'htmlOptions' => array('class' => 'td6'),
                'template' => '    
                    <div>{repost}</div><div>{del}</div>
                ',
                'buttons' => array(
                    '_id' => array(
                        'raw_text' => function ($data) {
                            return $data['auction_id'];
                        }
                    ),
                    'repost' => array(
                        'label' => Yii::t('basic', 'Republish item'),
                        'options' => array(
                            'class' => 'trndf crtreop',
                        ),
                        'url' => 'getLinkRePost($data["auction_id"])'
                    ),
                    'del' => array(
                        'label' => Yii::t('basic', 'Delete'),
                        'options' => array(
                            'class' => 'trndf',
                            'onclick' => 'return confirm("'.Yii::t('basic', 'Do you really want to delete this item?').'")'
                        ),
                        'url' => 'getLinkDel($data)'
                    )
                )
            )
        ),
    )
);
?>

<?php if ($count > 0): ?>
    <div class="form-group">
        <label><?= Yii::t('basic', 'Actions with marked')?>:</label>
        <div class="btn-group">
            <a style="cursor: pointer" class="btn btn-info dropdown-toggle" data-toggle="dropdown" href=""
               id="action_sale"><?= Yii::t('basic', 'Republish items')?></a>
            <div class="dropdown-menu padding15" id="drop_down_box">
                <div class="input-group">
                    <?php echo CHtml::dropDownList('listname', '', Auction::getDurationList(), [
                        'empty' => Yii::t('basic', 'Period'),
                        'class' => 'form-control selectbox mass_select',
                        'style' => 'width: 120px;'
                    ]); ?>
                    <span class="input-group-btn">
                  <input type="button" name="name" value="Ok" class="send_mass_reopen btn btn-default"/>
                  </span>
                </div>
            </div>
        </div>
        <a style="cursor: pointer" class="btn btn-danger" href="" id="action_sale_del"><?= Yii::t('basic', 'Delete')?></a>

    </div>


<?php endif; ?>