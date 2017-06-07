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

        <a href="<?= Yii::app()->createUrl('/' . $userArr['login']); ?>" class="seller_info_name" style="font-weight: bold;"><?= User::outUName($userArr['nick'], $userArr['login']); ?></a>
        <span class="seller_love">(<?= $userArr['rating']; ?>)</span>
    <?php if ($userArr['certified'] == 1): echo UserDataHelper::getStarColor($userArr['rating']);
    endif; ?>

        <div class="clear"></div>
    </div>

<?php elseif ($scope == UserInfo::SCOPE_GRID_LOT_HISTORY_SHOPPING): ?>

    <div class="auction_container_item_info_seller mt2">
        <a href="<?= Yii::app()->createUrl('/' . $userArr['login']); ?>" class="seller_info_name" style="font-weight: bold;"><?= User::outUName($userArr['nick'], $userArr['login']); ?></a>
        <span class="seller_love">(<?= $userArr['rating']; ?>)</span>
    <?php if ($userArr['certified'] == 1): echo UserDataHelper::getStarColor($userArr['rating']);
    endif; ?>

        <div class="clear"></div>
    </div>

<?php elseif ($scope == UserInfo::SCOPE_TOP_USER_PANEL): ?>
    <li><a href="/creator/lot"><b><?= Yii::t('basic', 'Create item') ?></a></b></li>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= Yii::t('basic', 'Your auction') ?> <b class="caret"></b></a>
        <ul class="dropdown-menu">
            <li><a href="/user/shopping/historyShopping"><?= Yii::t('basic', 'Purchases') ?></a></li>
            <li><a href="/user/sales/activeItems"><?= Yii::t('basic', 'Sales') ?></a></li>
            <li><a href="/user/favorites/items"><?= Yii::t('basic', 'Favorite items') ?> <?= UI::showSmallQuantity(CounterInfo::quantityFavItems()); ?></a></li>
            <li><a href="/user/lenta/index"><?= Yii::t('basic', 'Your subscribe') ?> <?= UI::showSmallQuantity(CounterInfo::quantityOtslItems()); ?></a></li>
            <li class="divider"></li>
            <li><a href="/systemNotification/index"><?= Yii::t('basic', 'Notifications') ?> <?= UI::showSmallQuantity(Getter::userModel()->getUnreadNotificationsCount()); ?></a></li>
            <li><a href="/user/questions/index"><?= Yii::t('basic', 'Questions') ?> <?= UI::showSmallQuantity(CounterInfo::quantityQuestionsForMe()); ?></a></li>
        </ul>
    <li>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= $userModel->outName(); ?> <b class="caret"></b></a>
        <ul class="dropdown-menu">

            <li><a href="/user/balance"><?= Yii::t('basic', 'Balance') ?>: <?= PriceHelper::formate(Getter::userModel()->getBalance(2)); ?></a></li>
            <li><a href="/user/pro/index"><?= Yii::t('basic', 'PRO account') ?></a></li>
            <li><a href="/user/settings/common"><?= Yii::t('basic', 'Settings') ?></a></li>
        </ul>
    </li>
    <li><a href="/logout"><?= Yii::t('basic', 'Logout') ?></a></li>



<?php elseif ($scope == UserInfo::SCOPE_SELLER_PAGE_LOT): ?>

    <a href="<?= Yii::app()->createUrl('/' . $userModel->login); ?>" class="seller_info_name" style="font-weight: bold;"><?= $userModel->outName(); ?></a>
    <span class="seller_info_love">(<?= $userModel->rating; ?>)</span>
    <?php if ($userModel->certified == 1): echo UserDataHelper::getStarColor($userModel->rating);
    endif; ?>


<?php elseif ($scope == UserInfo::SCOPE_USER_PROFILE_PAGE): ?>

    <div class="prods_owner">
        <a href="<?= Yii::app()->createUrl('/' . $userModel->login); ?>" class="seller_info_name" style="font-weight: bold;"><?= $userModel->outName(); ?></a>
        <span class="seller_info_love">(<?= $userModel->rating; ?>)</span>
        <?php if ($userModel->certified == 1): echo UserDataHelper::getStarColor($userModel->rating);
        endif; ?>
    </div>
<?php elseif ($scope == UserInfo::SCOPE_USER_SIMPLE): ?>


    <a href="<?= Yii::app()->createUrl('/' . $userModel->login); ?>" class="seller_info_name" style="font-weight: bold;"><?= $userModel->outName(); ?></a>
    <span class="seller_info_love">(<?= $userModel->rating; ?>)</span>
    <?php if ($userModel->certified == 1): echo UserDataHelper::getStarColor($userModel->rating);
    endif;?> 


<?php elseif ($scope == UserInfo::SCOPE_DISCUSSIONS_GRID): ?>

    <div>
        <a href="<?= Yii::app()->createUrl('/' . $userModel->login); ?>" class="seller_info_name" style="font-weight: bold;"><?= $userModel->outName(); ?></a>
        <span class="seller_info_love">(<?= $userModel->rating; ?>)</span>
    <?php if ($userModel->certified == 1): echo UserDataHelper::getStarColor($userModel->rating);
    endif; ?>
    </div>

<?php elseif ($scope == UserInfo::SCOPE_AUTHOR_DISCUSSION_PAGE): ?>

    <a href="<?= Yii::app()->createUrl('/' . $userModel->login); ?>" class="seller_info_name" style="font-weight: bold;"><?= $userModel->outName(); ?></a>
    <span class="seller_info_love">(<?= $userModel->rating; ?>)</span>
    <?php if ($userModel->certified == 1): echo UserDataHelper::getStarColor($userModel->rating);
    endif; ?>

<?php elseif ($scope == UserInfo::SCOPE_COMMENT_AUTHOR_DISCUSSION_PAGE): ?>

    <a href="<?= Yii::app()->createUrl('/' . $userModel->login); ?>" class="seller_info_name" style="font-weight: bold;"><?= $userModel->outName(); ?></a>
    <span class="seller_info_love">(<?= $userModel->rating; ?>)</span>
    <?php if ($userModel->certified == 1): echo UserDataHelper::getStarColor($userModel->rating);
    endif; ?>

<?php endif; ?>