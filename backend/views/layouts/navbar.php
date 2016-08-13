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
                    <?php
//                    $dependency_c_orders = new CDbCacheDependency('SELECT MAX(`update`) FROM `orders`');
//                    $orders = Yii::app()->db->cache(8000, $dependency_c_orders)
//                            ->createCommand()
//                            ->select('count(*) as count')
//                            ->from('orders')
//                            ->where('order_status=:order_status', array(':order_status' => Orders::STATUS_NEW))
//                            ->queryScalar();
//                    $applications_new = Yii::app()->db->createCommand()
//                            ->select('count(*) as count')
//                            ->from('application')
//                            ->where('status=:status', array(':status' => Application::NEW_APPLICATION))
//                            ->queryScalar();
//                    
//                    $testimonial_new = Yii::app()->db->createCommand()
//                            ->select('count(*) as count')
//                            ->from('testimonials')
//                            ->where('testimonial_status=:testimonial_status', array(':testimonial_status' => Testimonials::STATUS_NEW))
//                            ->queryScalar();

                    /*
                      <li class="active"><a rel="tooltip" data-placement="bottom" data-original-title="Новых заказов" href="<?= Yii::app()->createUrl('/admin/testimonial/index') ?>"  title="Новых отзывов"><i class="icon-thumbs-up"></i> <?= $testimonial_new; ?></a></li>
                      <li class="active"><a rel="tooltip" data-placement="bottom" data-original-title="Новых заказов" href="<?= Yii::app()->createUrl('/admin/order/index') ?>"  title="Новых заказов"><i class="icon-shopping-cart"></i> <?= $orders; ?></a></li>
                      <li class="active"><a id="count-application" rel="tooltip" data-placement="bottom" data-original-title="Новых заявок" href="<?= Yii::app()->createUrl('/admin/application/index') ?>"  title="Новых заявок"><i class="icon-book"></i> <?=$applications_new; ?></a></li>
                     */
                    ?> 
                </ul>
                <?php /*
                  <ul class="nav pull-right">

                  <li class="active" ><a id="comments-notifier" href="#"><i class="icon-comments"></i> <?=$comments['count']; ?></a>
                  <div class="comments-containner">

                  <div class="comments-containner-list hide search-dropdown box">
                  </div>
                  </div>
                  </li>
                  <li class="active"><a rel="tooltip" data-placement="bottom" data-original-title="Баланс системы"  href="#" title="Баланс системы"><i class="icon-money"></i> <?= $balanc['current_balanc'] . ' ' . Yii::t('common', 'RUB'); ?></a></li>
                  <li class="active"><a rel="tooltip" data-placement="bottom" data-original-title="Активных пользователей" href="#" title="Активных пользователей"><i class="icon-user"></i> <?= count($participant_actives); ?></a></li>
                  <li class="active"><a rel="tooltip" data-placement="bottom" data-original-title="Новых заявок" href="<?= Yii::app()->createUrl('/admin/application/index') ?>"  title="Новых заявок"><i class="icon-book"></i> <?= count($applications_new); ?></a></li>
                  <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Быстрый доступ <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                  <li><a href="<?= Yii::app()->createUrl('/admin/participant/agentСreate'); ?>">Создать агента</a></li>
                  <li><a href="<?= Yii::app()->createUrl('/admin/participant/agents'); ?>">Список агентов</a></li>
                  <li><a href="<?= Yii::app()->createUrl('/admin/application/index'); ?>">Заявки</a></li>
                  <li><a href="<?= Yii::app()->createUrl('/admin/finance/history'); ?>">Фин. история</a></li>
                  </ul>
                  </li>
                  </ul>
                 */ ?>
            </div>
        </div>
    </div>
</div>
