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
/** @var null|bool $showRecommendedContainer */
/** @var CSqlDataProvider $dataProvider */
/** @var array $users */
/** @var City[] $cities */
/** @var array $auctionAttributes */
/** @var array $auctionsImages */

Yii::import('frontend.widgets.search.SortSelectorWidget');
Yii::import('frontend.widgets.search.PeriodHtmlSelectorWidget');


$this->registerSEO($category);


if ($category == false) {
    $category_name = Yii::t('basic', 'All items');
    $breadcrumbs = array();
} else {
    $category_name = $category->name;
    $breadcrumbs = Category::getAncestorCategoryByBreadcrumbs($category->category_id);
}
?>

<div class="row auction">
    <div class="col-xs-9">
        <h2><?= $category_name; ?></h2>
        <?php
        $this->widget(
            'zii.widgets.CBreadcrumbs',
            array(
                'links' => $breadcrumbs,
                'tagName' => 'div',
                'htmlOptions' => array('class' => 'breadcrumbs'),
                'separator' => ' - ',
                'inactiveLinkTemplate' => '<span>{label}</span>',
                'homeLink' => CHtml::link(Yii::t('basic', 'Auction'), array('/auction/index')),
            )
        );
        ?>
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
        <?php

        if (!$search_active) {
            $this->widget('frontend.widgets.category.CategoryWidget');
        } else {
            $this->widget('frontend.widgets.category_search.CategorySearchWidget', ['auc_id_arr' => $auc_id_arr]);
        } ?>
        <hr class="horizontal_line">
        <?php
        function filterCrop($uri)
        {
            return preg_replace("/filter\=[a-z]{1,10}\&/ui", "", $uri);
        }

        function filterItem($label, $filter, $params = array())
        {
            $getWithOutFilter = '&' . filterCrop(Yii::app()->getRequest()->getQueryString());
            return array(
                'label' => $label,
                'url' => '/' . Yii::app()->request->getPathInfo() . '?filter=' . $filter . $getWithOutFilter,
                'active' => isset($_GET['filter']) && $_GET['filter'] == $filter,
                'itemOptions' => isset($params['itemOptions']) ? $params['itemOptions'] : array()
            );
        }

        ?>
        <span class="pull-right unset_par">
            <a href="<?= filterCrop(Yii::app()->getRequest()->getRequestUri()); ?>"><?= Yii::t('basic', 'view all') ?></a>
        </span>
        <b><?= Yii::t('basic', 'Type of sell') ?></b>

        <?php

        $this->widget(
            'zii.widgets.CMenu',
            array(
                'items' => array(
                    filterItem(Yii::t('basic', 'Auction'), 'default'),
                    filterItem(Yii::t('basic', 'Buy Now'), 'buynow'),
                    filterItem(Yii::t('basic', 'From') . ' ' . PriceHelper::formate(1), 'nulls', array('itemOptions' => array('class' => 'from_1_ruble_item'))),
                ),
                'firstItemCssClass' => 'first',
                'htmlOptions' => ['class' => 'list-unstyled nomark type_transaction']
            )
        );
        ?>
        <hr class="horizontal_line">
        <!-- FILTER -->
        <?php
        $this->renderPartial(
            '_filter',
            [
                'filter' => $filter,
                'category' => $category,
                'options' => $options,
                'actionUrlRoute' => '/auction/index',
                'actionUrlParams' => $_GET,
            ]
        );
        ?>
        <!-- FILTER -->
    </div>
    <div class="col-xs-9">
        <div class="row top_auction_list">
            <div class="col-xs-4">
                <?php $this->widget('frontend.widgets.search.SortSelectorWidget', [
                    'scope' => SortSelectorWidget::SCOPE_PAGE_AUCTION,
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
                        'firstPageLabel' => '<<',
                        'lastPageLabel' => '>>',
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
        <div class="row top_auctions">
            <div class="col-xs-12">
                <?php $items = $dataProvider->getData(); ?>
                <?php if (count($items) <= 0) : ?>
                    <div class="alert alert-info"><?= Yii::t('basic', 'There doesn\'t seem to be any items matching your search result') ?></div>
                <?php endif; ?>

            </div>
        </div>
        <?php foreach ($items as $itemKey => $item): ?>
            <?php $this->renderPartial(
                '_item',
                [
                    'data' => $item,
                    'owner' => isset($users[$item['owner']]) ? $users[$item['owner']] : null,
                    'city' => isset($cities[$item['id_city']]) ? $cities[$item['id_city']] : null,
                    'additional' => [],
                    'is' => [],
                    'auctionImages' => isset($auctionsImages[$item['auction_id']])
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

    </div>
</div>