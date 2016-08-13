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
/** @var array $owner */

if (empty($owner)) {
    $owner = Yii::app()->db->cache(3600)->createCommand()
        ->select('rating, login, nick, online, certified')
        ->from('users')
        ->where('user_id=:user_id', [':user_id' => $id])
        ->queryRow();
}
?>
<?php if (!empty($owner)): ?>
    <div class="auction_container_item_info_seller">

    <a href="<?= Yii::app()->createUrl('/'.$owner['login']); ?>" class="seller_info_name" style="font-weight: bold;">
        <?= User::outUName($owner['nick'], $owner['login']); ?></a>
        <span class="seller_love">(<?= $owner['rating']; ?>)</span>
        <?php if ($owner['certified'] == 1): echo UserDataHelper::getStarColor($owner['rating']); endif; ?>

        <div class="clear"></div>
    </div>
<?php endif; ?>