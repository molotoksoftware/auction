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

/** @var AuctionBulkChanges $this */
/** @var FormBulkChangeCategory $model */
/** @var string $actionUrl */
/** @var int $lotsCount */
/** @var string $gridId */
/** @var array $dependentCurrencies */

$options = [
    'csrfToken'            => Yii::app()->request->csrfToken,
    'csrfTokenName'        => Yii::app()->request->csrfTokenName,
];

?>
<!-- Блок с кнопками -->
<div class="form-group">
      <label>Действия с отмеченными:</label>
   <a style="cursor: pointer" class="btn btn-info" href="javascript:void(0);" id="action_mass_autorepub">Назначить автоперевыставление</a>
    <a style="cursor: pointer" class="btn btn-danger" href="" id="action_close_all">Снять с торгов</a>

  </div>

<? echo CHtml::beginForm('/editor/massAutoRepub', 'POST', array('id' => 'autorepub_mass_form', 'style' => 'display: none;')); ?><? echo CHtml::endForm(); ?>
<!--/ Блок с кнопками -->
