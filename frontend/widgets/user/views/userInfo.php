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
/** @var array|null $userArr */
/** @var User|null $userModel */
/** @var string $scope */

?>

<?php if ($scope == UserInfo::SCOPE_GRID_LOT): ?>

    <div class="auction_container_item_info_seller">
        
        <a href="<?= Yii::app()->createUrl('/'.$userArr['login']); ?>" class="seller_info_name" style="font-weight: bold;"><?=User::outUName($userArr['nick'], $userArr['login']);?></a>
        <span class="seller_love">(<?= $userArr['rating']; ?>)</span>
        <?php if ($userArr['certified'] == 1): echo UserDataHelper::getStarColor($userArr['rating']); endif; ?>

        <div class="clear"></div>
    </div>

<?php elseif ($scope == UserInfo::SCOPE_GRID_LOT_HISTORY_SHOPPING): ?>

    <div class="auction_container_item_info_seller mt2">
        <a href="<?= Yii::app()->createUrl('/'.$userArr['login']); ?>" class="seller_info_name" style="font-weight: bold;"><?=User::outUName($userArr['nick'], $userArr['login']);?></a>
        <span class="seller_love">(<?= $userArr['rating']; ?>)</span>
        <?php if ($userArr['certified'] == 1): echo UserDataHelper::getStarColor($userArr['rating']); endif; ?>

        <div class="clear"></div>
    </div>

<?php elseif ($scope == UserInfo::SCOPE_TOP_USER_PANEL): ?>
<li><a href="/creator/lot"><b>Создать лот</a></b></li>
<li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Мой auction <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="/user/shopping/historyShopping">Покупки</a></li>
                <li><a href="/user/sales/activeItems">Продажи</a></li>
                <li><a href="/user/favorites/items">Избранное <?=UI::showQuantity(CounterInfo::quantityFavItems());?></a></li>
                <li><a href="/user/lenta/index">Продавцы <?=UI::showQuantity(CounterInfo::quantityOtslItems());?></a></li>
                <li class="divider"></li>
                <li><a href="/systemNotification/index">Уведомления <?=UI::showQuantity(Getter::userModel()->getUnreadNotificationsCount());?></a></li>
                <li><a href="/user/questions/index">Вопросы <?=UI::showQuantity(CounterInfo::quantityQuestionsForMe());?></a></li>
              </ul>
<li>
<li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$userModel->outName();?> <b class="caret"></b></a>
              <ul class="dropdown-menu">

                <li><a href="/user/balance">Баланс: <?=Getter::userModel()->getBalance(2);?> руб.</a></li>
                <li><a href="/user/pro/index">ПРО-аккаунт</a></li>
                <li><a href="/user/settings/common">Настройки</a></li>
              </ul>
            </li>
<li><a href="/logout">Выход</a></li>



<?php elseif ($scope == UserInfo::SCOPE_SELLER_PAGE_LOT): ?>

    <a href="<?= Yii::app()->createUrl('/'.$userModel->login); ?>" class="seller_info_name" style="font-weight: bold;"><?= $userModel->outName(); ?></a>
    <span class="seller_info_love">(<?= $userModel->rating; ?>)</span>
    <?php if ($userModel->certified == 1): echo UserDataHelper::getStarColor($userModel->rating); endif; ?>


<?php elseif ($scope == UserInfo::SCOPE_USER_PROFILE_PAGE): ?>

    <div class="prods_owner">
        <a href="<?= Yii::app()->createUrl('/'.$userModel->login); ?>" class="seller_info_name" style="font-weight: bold;"><?= $userModel->outName(); ?></a>
        <span class="seller_info_love">(<?= $userModel->rating; ?>)</span>
        <?php if ($userModel->certified == 1): echo UserDataHelper::getStarColor($userModel->rating); endif; ?>
    </div>
<?php elseif ($scope == UserInfo::SCOPE_USER_SIMPLE): ?>


    <a href="<?= Yii::app()->createUrl('/'.$userModel->login); ?>" class="seller_info_name" style="font-weight: bold;"><?= $userModel->outName(); ?></a>
    <span class="seller_info_love">(<?= $userModel->rating; ?>)</span>
    <?php if ($userModel->certified == 1): echo UserDataHelper::getStarColor($userModel->rating); endif; ?>
    

<?php elseif ($scope == UserInfo::SCOPE_DISCUSSIONS_GRID): ?>

    <div>
        <a href="<?= Yii::app()->createUrl('/'.$userModel->login); ?>" class="seller_info_name" style="font-weight: bold;"><?= $userModel->outName(); ?></a>
    <span class="seller_info_love">(<?= $userModel->rating; ?>)</span>
    <?php if ($userModel->certified == 1): echo UserDataHelper::getStarColor($userModel->rating); endif; ?>
    </div>

<?php elseif ($scope == UserInfo::SCOPE_AUTHOR_DISCUSSION_PAGE): ?>

    <a href="<?= Yii::app()->createUrl('/'.$userModel->login); ?>" class="seller_info_name" style="font-weight: bold;"><?= $userModel->outName(); ?></a>
    <span class="seller_info_love">(<?= $userModel->rating; ?>)</span>
    <?php if ($userModel->certified == 1): echo UserDataHelper::getStarColor($userModel->rating); endif; ?>

<?php elseif ($scope == UserInfo::SCOPE_COMMENT_AUTHOR_DISCUSSION_PAGE): ?>

    <a href="<?= Yii::app()->createUrl('/'.$userModel->login); ?>" class="seller_info_name" style="font-weight: bold;"><?= $userModel->outName(); ?></a>
    <span class="seller_info_love">(<?= $userModel->rating; ?>)</span>
    <?php if ($userModel->certified == 1): echo UserDataHelper::getStarColor($userModel->rating); endif; ?>

<?php endif; ?>