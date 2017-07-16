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



return array(
    array('label' => 'Главная', 'url' => array('/main/index'), 'icon' => 'icon-home icon-2x'),
    array('label' => 'Администраторы', 'url' => array('/admin/admin/index'), 'icon' => 'icon-group icon-2x'),
    array('label' => 'Пользователи', 
        'url' => array('#user'), 
        'icon' => 'icon-user icon-2x',
        'submenuOptions' => array('class' => 'collapse', 'id' => 'user'),
        'items' => [
            [
                'label' => 'Пользователи',
                'url' => array('/user/user/index'),
                'icon' => 'icon-user',
            ],
            [
                'label' => 'Продажи',
                'url' => array('/sales/sales/index'),
                'icon' => 'icon-star',
            ]
        ],
        ),
    array(
        'label' => 'Каталог',
        'url' => '#catalog',
        'icon' => 'icon-folder-open icon-2x',
        'submenuOptions' => array('class' => 'collapse', 'id' => 'catalog'),
        'items' => array(
            array(
                'label' => 'Категории',
                'url' => array('/catalog/category/index'),
                'icon' => 'icon-inbox',
            ),
            array(
                'label' => 'Атрибуты',
                'url' => array('/catalog/attribute/index'),
                'icon' => 'icon-list-alt',
            )
        )
    ),
    array('label' => 'Лоты', 'url' => array('/catalog/auction/index'), 'icon' => 'icon-legal icon-2x'),
    array('label' => 'Страницы', 'url' => array('/page/page/index'), 'icon' => 'icon-file-alt icon-2x'),
    array('label' => 'Новости', 'url' => array('/news/news/index'), 'icon' => 'icon-leaf icon-2x'),
    array(
        'label' => 'Платежи',
        'url' => '#monetary_transactions',
        'icon' => 'icon-money icon-2x',
        'submenuOptions' => array('class' => 'collapse', 'id' => 'monetary_transactions'),
        'items' => array(
            array(
                'label' => 'История пополнений',
                'url' => array('/money/history/recharge'),
                'icon' => 'icon-credit-card',
            ),
            array(
                'label' => 'История покупок',
                'url' => array('/money/history/order'),
                'icon' => 'icon-shopping-cart',
            ),

        )
    ),
    array(
        'label' => 'Настройки',
        'url' => '#settings',
        'icon' => 'icon-cog icon-2x',
        'submenuOptions' => array('class' => 'collapse', 'id' => 'settings'),
        'items' => array(
            array(
                'label' => 'Настройки сайта',
                'url' => array('/admin/settings/common'),
                'icon' => 'icon-desktop',
            ),
            array(
                'label' => 'ПРО (цены/сроки)',
                'url' => array('/catalog/proPrices/index'),
                'icon' => 'icon-asterisk',
            ),
        )
    ),
);
