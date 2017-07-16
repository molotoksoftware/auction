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

$this->beginContent('//layouts/main'); ?>
<div class="container">
<div class="row auction">
        <div class="col-xs-9">
            <h2><?= Yii::t('basic', 'My auction')?></h2>
        </div>
</div>
<hr class="top10 horizontal_line">
<div class="row">
    <div class="col-xs-3 nav_cabinet">
        <?php
        $this->widget(
            'zii.widgets.CMenu',
            array(
                'firstItemCssClass' => 'first',
                'lastItemCssClass' => 'last',
                'items' => array(
                    [
                        'label' => '<span class="glyphicon glyphicon-download"></span> '.Yii::t('basic', 'My purchases'),
                        'itemOptions' => ['class' => 'list-group-item head_li'],
                    ],
                        array(
                            'label' => Item::spanEnvelopment(Yii::t('basic', 'Favorite items')) . UI::showQuantityLeftMenu(CounterInfo::quantityFavItems()),
                            'url' => array('/user/favorites/items')
                        ),
                        array(
                            'label' => Item::spanEnvelopment(Yii::t('basic', 'Favorite sellers')) . UI::showQuantityLeftMenu(CounterInfo::quantityOtslItems()),
                            'url' => array('/user/lenta/index')
                        ),
                        array(
                            'label' => Item::spanEnvelopment(Yii::t('basic', 'Bids')) . UI::showQuantityLeftMenu(CounterInfo::quantityActiveBets(Yii::app()->user->id)),
                            'url' => array('/user/shopping/activeBets')
                        ),
                        array(
                            'label' => Item::spanEnvelopment(Yii::t('basic', 'Didn\'t win')) . UI::showQuantityLeftMenu(CounterInfo::quantityNoWonItems()),
                            'url' => array('/user/shopping/notWonItems')
                        ),
                        array(
                            'label' => Item::spanEnvelopment(Yii::t('basic', 'Purchase history')) . UI::showQuantityLeftMenu(CounterInfo::quantityHistoryShopping()),
                            'url' => array('/user/shopping/historyShopping')
                        ),
                    [
                        'label'=>'<span class="glyphicon glyphicon-upload"></span> '.Yii::t('basic', 'Sell'),
                        'itemOptions' => ['class' => 'list-group-item head_li'],
                    ],

                        array(
                            'label' => Item::spanEnvelopment(Yii::t('basic', 'Active items')) . UI::showQuantityLeftMenu(CounterInfo::quantityActiveLots()),
                            'url' => array('/user/sales/activeItems')
                        ),
                        array(
                            'label' => Item::spanEnvelopment(Yii::t('basic', 'Unsold')) . UI::showQuantityLeftMenu(CounterInfo::quantityCompletedItems()),
                            'url' => array('/user/sales/completedItems')
                        ),
                        array(
                            'label' => Item::spanEnvelopment(Yii::t('basic', 'Sold')) . UI::showQuantityLeftMenu(CounterInfo::quantitySoldItems()),
                            'url' => array('/user/sales/soldItems')
                        ),
                        array(
                            'label' => Item::spanEnvelopment(Yii::t('basic', 'Questions')) . UI::showQuantityLeftMenu(CounterInfo::quantityQuestionsForMe()),
                            'url' => array('/user/questions/index')
                        ),
                    [
                        'label' => '<span class="glyphicon glyphicon-bullhorn"></span> '.Yii::t('basic', 'Reviews'),
                        'itemOptions' => ['class' => 'list-group-item head_li'],
                    ],
                        array(
                            'label' => Item::spanEnvelopment(Yii::t('basic', 'About me')) . UI::showQuantityLeftMenu(CounterInfo::quantityReviews('to_me')),
                            'url' => array('/user/reviews/index/route/to_me')
                        ),
                        array(
                            'label' => Item::spanEnvelopment(Yii::t('basic', 'From me')). UI::showQuantityLeftMenu(CounterInfo::quantityReviews('from_me')),
                            'url' => array('/user/reviews/index/route/from_me')
                        ),
                ),
                'encodeLabel' => false,
                'itemTemplate' => '{menu}',
                'itemCssClass' => 'list-group-item',
                'firstItemCssClass' => 'first',
                'htmlOptions' => array(
                    'class' => 'list-group'
                ),
            )
        );
        ?>
        
    </div>
    <div class="col-xs-9 content_cabinet">
        <?php echo $content; ?>
    </div>
</div>

</div>

<?php $this->endContent(); ?>