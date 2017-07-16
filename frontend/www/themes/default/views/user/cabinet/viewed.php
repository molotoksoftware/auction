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

<h3><?= Yii::t('basic', 'View statistic')?></h3>
<p><?= Yii::t('basic', 'Item')?>: "<a href="<?= $url; ?>" target="_blank"><?= $model->name; ?></a>"</p>


<?php if (isset($max_viewed['day_viewed']) && !empty($max_viewed['day_viewed'])): ?>

<?php 
$px = round(500/$max_viewed['day_viewed']); // X px in 1 view
$this->widget('zii.widgets.CListView', array(
    'dataProvider' => $dataProvider,
    'itemView' => '_viewed',
    'template'=>'{items}<div style="clear: both;"></div>{pager}',
    'summaryText' => '{start}&ndash;{end} '.Yii::t('basic', 'from').' {count}',
    'viewData' => array('px' => $px)
));
?>

<?php else: ?>

<div class="alert alert-info"><?= Yii::t('basic', 'No views')?></div>

<?php endif; ?>
