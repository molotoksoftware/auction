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
            <h2>Мой auction</h2>
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
                        'label' => '<span class="glyphicon glyphicon-download"></span> Мои покупки',
                        'itemOptions' => ['class' => 'list-group-item head_li'],
                    ],
                        array(
                            'label' => '<span>Корзина</span> ',
                            'url' => '#',
                        ),
                        array(
                            'label' => '<span>Избранные лоты</span> ' . UI::showQuantityLeftMenu(CounterInfo::quantityFavItems()),
                            'url' => array('/user/favorites/items')
                        ),
                        array(
                            'label' => '<span>Любимые продавцы</span> ' . UI::showQuantityLeftMenu(CounterInfo::quantityOtslItems()),
                            'url' => array('/user/lenta/index')
                        ),
                        array(
                            'label' => '<span>Активные ставки</span> ' . UI::showQuantityLeftMenu(CounterInfo::quantityActiveBets(Yii::app()->user->id)),
                            'url' => array('/user/shopping/activeBets')
                        ),
                        array(
                            'label' => '<span>Не выигранные</span> ' . UI::showQuantityLeftMenu(CounterInfo::quantityNoWonItems()),
                            'url' => array('/user/shopping/notWonItems')
                        ),
                        array(
                            'label' => '<span>История покупок</span> ' . UI::showQuantityLeftMenu(CounterInfo::quantityHistoryShopping()),
                            'url' => array('/user/shopping/historyShopping')
                        ),
                    [
                        'label'=>'<span class="glyphicon glyphicon-upload"></span> Продажи',
                        'itemOptions' => ['class' => 'list-group-item head_li'],
                    ],

                        array(
                            'label' => '<span>Активные лоты</span> ' . UI::showQuantityLeftMenu(CounterInfo::quantityActiveLots()),
                            'url' => array('/user/sales/activeItems')
                        ),
                        array(
                            'label' => '<span>Непроданные лоты</span> ' . UI::showQuantityLeftMenu(CounterInfo::quantityCompletedItems()),
                            'url' => array('/user/sales/completedItems')
                        ),
                        array(
                            'label' => '<span>Проданные лоты</span> ' . UI::showQuantityLeftMenu(CounterInfo::quantitySoldItems()),
                            'url' => array('/user/sales/soldItems')
                        ),
                        array(
                            'label' => '<span>Вопросы по лотам</span> ' . UI::showQuantityLeftMenu(CounterInfo::quantityQuestionsForMe()),
                            'url' => array('/user/questions/index')
                        ),
                    [
                        'label' => '<span class="glyphicon glyphicon-bullhorn"></span> Отзывы',
                        'itemOptions' => ['class' => 'list-group-item head_li'],
                    ],
                        array(
                            'label' => '<span>Отзывы обо мне</span> ' . UI::showQuantityLeftMenu(CounterInfo::quantityReviews('to_me')),
                            'url' => array('/user/reviews/index/route/to_me')
                        ),
                        array(
                            'label' => '<span>Мои отзывы</span> ' . UI::showQuantityLeftMenu(CounterInfo::quantityReviews('from_me')),
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