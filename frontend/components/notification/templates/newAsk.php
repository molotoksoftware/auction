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

<?=Yii::t('mail', 'In your item {item} {item_title}, a new question.',[
    '{item}' => $lotModel->auction_id,
    '{item_title}' => $linkItem,
]); ?><br />

<strong><?=Yii::t('mail', 'Question')?></strong>: <?=$question?><br /><br />

<?=Yii::t('mail', 'Contact information')?>:<br />
E-mail: <?=$author->email?>, <?=Yii::t('mail', 'telephone')?>: <?=$author->telephone?$author->telephone:Yii::t('mail', 'not specified')?>