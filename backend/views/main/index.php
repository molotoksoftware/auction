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


$this->pageTitle = 'Главная';

$this->header_info = array(
    'icon' => 'icon-home icon-2x',
    'title' => 'Главная',
    'description' => 'Вкладка содержит список часто используемых модулей'
);
?>
<div class="container-fluid padded">
    <!-- find me in partials/action_nav_normal_one_row -->
    <hr class="divider">
    <!--big normal buttons-->
    <div class="action-nav-normal">

          <div class="row-fluid">
            <div class="span2 action-nav-button">
                <a title="Пользователи" href="<?= Yii::app()->createUrl('/user/user/index'); ?>">
                <i class="icon-user icon-2x"></i>
                <span>Пользователи</span>
                </a>
            </div>

            <div class="span2 action-nav-button">
                <a title="Лоты" href="<?= Yii::app()->createUrl('/catalog/auction/index'); ?>">
                <i class="icon-legal icon-2x"></i>
                <span>Лоты</span>
                </a>
            </div>

            <div class="span2 action-nav-button">
                <a title="Страницы" href="<?= Yii::app()->createUrl('/page/page/index'); ?>">
                <i class="icon-file-alt icon-2x"></i>
                <span>Страницы</span>
                </a>
            </div>
            <div class="span2 action-nav-button">
                <a title="Новости" href="<?= Yii::app()->createUrl('/news/news/index'); ?>">
                <i class="icon-leaf icon-2x"></i>
                <span>Новости</span>
                </a>
            </div>
            <div class="span2 action-nav-button">
                <a title="Платежи" href="<?= Yii::app()->createUrl('/money/history/recharge'); ?>">
                <i class="icon-money icon-2x"></i>
                <span>Платежи</span>
                </a>
            </div>
            <div class="span2 action-nav-button">
                <a title="Настройки" href="<?= Yii::app()->createUrl('/admin/settings/common'); ?>">
                <i class="icon-cog icon-2x"></i>
                <span>Настройки</span>
                </a>
            </div>
          </div>


    </div>
    <hr class="divider">



</div>