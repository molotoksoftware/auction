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
* Russian to English:
* Главная is Home
* Главная is Home
* Вкладка содержит список часто используемых модулей Frequently Used Modules
* Members Members List
* Лоты (Lots - auction lots) Listings
* Страницы Pages
* Новости News
* Платежи Payments
* Настройки Settings
*/

$this->pageTitle = 'Home';

$this->header_info = array(
    'icon' => 'icon-home icon-2x',
    'title' => 'Home',
    'description' => 'Frequently Used Modules'
);
?>
<div class="container-fluid padded">
    <!-- find me in partials/action_nav_normal_one_row -->
    <hr class="divider">
    <!--big normal buttons-->
    <div class="action-nav-normal">

          <div class="row-fluid">
            <div class="span2 action-nav-button">
                <a title="Members List" href="<?= Yii::app()->createUrl('/user/user/index'); ?>">
                <i class="icon-user icon-2x"></i>
                <span>Members List</span>
                </a>
            </div>

            <div class="span2 action-nav-button">
                <a title="Listings" href="<?= Yii::app()->createUrl('/catalog/auction/index'); ?>">
                <i class="icon-legal icon-2x"></i>
                <span>Listings</span>
                </a>
            </div>

            <div class="span2 action-nav-button">
                <a title="Pages" href="<?= Yii::app()->createUrl('/page/page/index'); ?>">
                <i class="icon-file-alt icon-2x"></i>
                <span>Pages</span>
                </a>
            </div>
            <div class="span2 action-nav-button">
                <a title="News" href="<?= Yii::app()->createUrl('/news/news/index'); ?>">
                <i class="icon-leaf icon-2x"></i>
                <span>News</span>
                </a>
            </div>
            <div class="span2 action-nav-button">
                <a title="Payments" href="<?= Yii::app()->createUrl('/money/history/recharge'); ?>">
                <i class="icon-money icon-2x"></i>
                <span>Payments</span>
                </a>
            </div>
            <div class="span2 action-nav-button">
                <a title="Settings" href="<?= Yii::app()->createUrl('/admin/settings/common'); ?>">
                <i class="icon-cog icon-2x"></i>
                <span>Settings</span>
                </a>
            </div>
          </div>


    </div>
    <hr class="divider">



</div>