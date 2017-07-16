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



<h4 class="margint_top_30"><?= Yii::t('basic', 'Last news'); ?></h4>


<?php foreach ($news as $key => $item): ?>

    <p><small><?php echo Yii::app()->dateFormatter->format('d MMMM yyyy', strtotime($item->date)) ?></small>
    <a href="<?= Yii::app()->createUrl('/news/view', array('alias' => $item->alias)); ?>"><?= $item->title; ?></a></p>

<?php endforeach; ?>
