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




?>
<div class="navbar navbar-top navbar-inverse">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" href="<?php echo Yii::app()->createUrl('/main'); ?>">Панель управления</a>
            <ul class="nav pull-right">
                <li class="toggle-primary-sidebar hidden-desktop" data-toggle="collapse" data-target=".nav-collapse-primary"><a><i class="icon-th-list"></i></a></li>
                <li class="collapsed hidden-desktop" data-toggle="collapse" data-target=".nav-collapse-top"><a><i class="icon-align-justify"></i></a></li>
            </ul>
            <div class="nav-collapse nav-collapse-top">
                <ul class="nav full pull-right">
                    <li class="dropdown user-avatar">  
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span>
                                <?php echo CHtml::image(Yii::app()->user->getModel()->getAvatar(), Yii::app()->user->getModel()->getShortName(), array('class' => 'menu-avatar')); ?>
                                <span><?php echo Yii::app()->user->getModel()->getShortName(); ?>  <i class="icon-caret-down"></i></span>
                            </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="with-image">
                                <div class="avatar">
                                    <?php echo CHtml::image(Yii::app()->user->getModel()->getAvatar(), Yii::app()->user->getModel()->getShortName()); ?>
                                </div>
                                <span><?php echo Yii::app()->user->getModel()->getShortName(); ?></span>
                            </li>
                            <li class="divider"></li>
                            <li><a href="<?php echo Yii::app()->createUrl('/admin/admin/logout'); ?>"><i class="icon-off"></i> 
                                    <span>Выйти</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav pull-right">
                </ul>
            </div>
        </div>
    </div>
</div>
