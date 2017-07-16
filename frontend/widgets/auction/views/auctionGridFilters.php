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
/** @var string $gridId */

/** @var bool $showPeriodFilter */
/** @var array $datePeriodOptions */
/** @var string $activePeriodOption */

/** @var bool $showSearchFilter */
/** @var string $searchFieldName */
/** @var string $searchFieldValue */

/** @var int $totalItems */
/** @var bool $showBuyersFilter */
/** @var array $buyersArray */

/** @var bool $showSellerFilter */
/** @var array $sellersArray */

/** @var bool $showUnprocessedFilter */
/** @var int $totalItems */
/** @var float $totalSum */

$request = Yii::app()->getRequest();
$cs = Getter::clientScript();

?>
<div class="row" id="row grid-lot-filter">
    <div class="col-xs-12">
         <div class="panel panel-default">
            <div class="panel-heading"><?= Yii::t('basic', 'Filter') ?></div>
                <div class="panel-body">
                <div class="col-xs-6">
                    <!-- search-filter -->
                    <?php if ($showSearchFilter): ?>
                        <div class="grid-search-filter-cnt input-group">
                            <?=CHtml::textField($searchFieldName, $searchFieldValue, [
                                'id'          => 'grid-search-filter',
                                'placeholder' => Yii::t('basic', 'Search'),
                                'class'       => 'form-control'
                            ])?> 
                            <span class="input-group-btn">
                                <?=CHtml::button(Yii::t('basic', 'Search'), ['id' => 'gsf-find-btn', 'class' => 'btn btn-default'])?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-xs-3">
                    <?php if ($showPeriodFilter): ?>
                        <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            <strong><?= Yii::t('basic', 'Period')?>:</strong> <?= Yii::t('basic', $datePeriodOptions[$activePeriodOption])?> <span class="caret"></span>
                         </button>
                        <?php
                        $items = [];
                        foreach ($datePeriodOptions as $period => $title) {
                            $items[] = [
                                'label'       => Yii::t('basic', $title),
                                'url'         => '',
                                'active'      => $activePeriodOption == $period,
                                'linkOptions' => [
                                    'class'       => 'grid-period-filter-link',
                                    'data-period' => $period,
                                ],
                            ];
                        }
                        ?>

                        <?php $this->widget('zii.widgets.CMenu', [
                            'items'       => $items,
                            'htmlOptions' => ['id' => 'grid-period-filter', 'class' => 'dropdown-menu'],
                        ]); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($showSortCategory): ?>
                            <?=isset($auction) && !empty($userCategoriesList)
                            ? CHtml::activeDropDownList(
                                $auction, 'category_id',
                                $userCategoriesList,
                                array('empty' => Yii::t('basic', 'All categories'), 'encode' => false, 'class' => 'form-control')
                            )
                            : false; ?>
                            <?php
                            $js = "
                            $(document).on('change', '#Auction_category_id', function() {
                                updateGridView('category_id', $(this).val());
                                return false;
                            });
                            ";
                            $cs->registerScript("seller_filter", $js); ?>
                    <?php endif; ?>
                </div>
                <div class="col-xs-3">


                   <!-- buyer-filter -->
                    <?php if ($showBuyersFilter): ?>
                        <div class="grid-buyer-filter-cnt">
                            <?=CHtml::dropDownList(
                                'buyer',
                                $request->getParam('buyer'),
                                $buyersArray,
                                ['prompt' => Yii::t('basic', 'All buyers'),
                                 'class'  => 'form-control'
                                ]
                            )?>
                            <?php
                            $js = "
                            $(document).on('change', '#buyer', function() {
                                updateGridView('buyer', $(this).val());
                                return false;
                            });
                            ";
                            $cs->registerScript("buyer_filter", $js); ?>
                        </div>
                    <?php endif; ?>

                    <!-- seller-filter -->
                    <?php if ($showSellerFilter): ?>

                        <div class="btn-group" style="float:right">
                            <?=CHtml::dropDownList(
                                'seller',
                                $request->getParam('seller'),
                                $sellersArray,
                                ['prompt' => Yii::t('basic', 'All sellers'), 'class' => 'form-control']
                            )?>
                            <?php
                            $js = "
                            $(document).on('change', '#seller', function() {
                                updateGridView('seller', $(this).val());
                                return false;
                            });
                            ";
                            $cs->registerScript("seller_filter", $js); ?>
                        </div>
                    <?php endif; ?>



                </div>
            </div>
        </div>
</div>
</div>

<?php
$cs->registerScript("auction-grid-filters", "
    // Period filter

    $(document).on('click', '#grid-period-filter a.grid-period-filter-link', function() {
        $('#grid-period-filter').find('li.active').removeClass('active');
        var link = $(this);
        link.parent().addClass('active');
        updateGridView('period', encodeURIComponent(link.data('period')));
        return false;
    });

    // Search filter
    $(document).bind('enterKey', '#grid-search-filter', function(e) {
        submitSearch();
    });
    $(document).on('blur', '#grid-search-filter', function(e) {
        submitSearch();
    });
    $(document).on('click', '#gsf-find-btn', function(e) {
        submitSearch();
    });
    $(document).on('keyup', '#grid-search-filter', function(e) {
        if (e.keyCode == 13) {
            submitSearch();
        }
    });
    function submitSearch() {
        var value = $.trim($('#grid-search-filter').val());
        updateGridView('{$searchFieldName}', value);
    }

    // Common
    function updateGridView(param, value) {
        var data = {};
        data[param] = value;
        $.fn.yiiGridView.update('{$gridId}', {
            data: data,
            url: window.location.href,
        });
        setParamToPageUrl(param, value);
    }
");