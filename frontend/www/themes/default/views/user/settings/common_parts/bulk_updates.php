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
/** @var User $user */
/** @var array $errors */
/** @var string $minPricePercents */
/** @var string $maxPricePercents */

cs()->registerScriptFile(Yii::app()->baseUrl . '/js/user/settings/common.js');
$request = Yii::app()->getRequest();

?>

<h3><?= Yii::t('basic', 'Bulk updates')?></h3>


<?php if (Getter::webUser()->hasFlash('success_bulk_update')): ?>
    <div class="alert alert-success">
        <?=Getter::webUser()->getFlash('success_bulk_update');?>
    </div>
<?php endif; ?>


<div class="panel panel-default">
  <div class="panel-heading"><?= Yii::t('basic', 'Items republish')?></div>
  <div class="panel-body">
      <p>
          <?= Yii::t('basic', 'You can activate (or cancel) function of automatically republishing of all your items.')?>
      </p>
        <?php echo CHtml::beginForm('', 'post', ['name' => 'form1']); ?>
        <div class="control">
            <?php echo CHtml::radioButton(
                'switch_auto_republish',
                $request->getPost('switch_auto_republish') == 'y',
                ['value' => 'y', 'id' => 'switch_auto_republish_yes']
            ); ?>
            <?php echo CHtml::label(Yii::t('basic', 'Set automatic publish of items'), 'switch_auto_republish_yes'); ?>
        </div>

        <div class="control last">
            <?php echo CHtml::radioButton(
                'switch_auto_republish',
                $request->getPost('switch_auto_republish') == 'n',
                ['value' => 'n', 'id' => 'switch_auto_republish_no']
            ); ?>
            <?php echo CHtml::label(Yii::t('basic', 'Unset automatic publish of items'), 'switch_auto_republish_no'); ?>
        </div>
        <?php echo CHtml::submitButton(Yii::t('basic', 'Confirm'), ['class' => 'btn btn-default']); ?>
        <?php echo CHtml::endForm(); ?>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading"><?= Yii::t('basic', 'Change items price')?></div>
  <div class="panel-body">
    <p>

        <?= Yii::t('basic', 'Using this function you can change prices of all your items. Will be changed start price and "buy now" price. Lots with bids will be missed.')?>
        </p>

    <?php echo CHtml::beginForm('', 'post', ['name' => 'form2', 'class' => 'form-inline']); ?>
        <div class="form-group">
            <?php echo CHtml::label(Yii::t('basic', 'You should indicate how many percent you want to raise prices for your items. A value with a negative sign.'),
                'price_update'
            ); ?>
        </div>
        <div class="form-group">
            <?php echo CHtml::textField(
                'price_update',
                $request->getPost('price_update'),
                ['id' => 'price_update',
                 'class' => 'form-control']
            ); ?>
        </div>
    <?php echo CHtml::submitButton(Yii::t('basic', 'Confirm'), ['class' => 'btn btn-default']); ?>
    <?php echo CHtml::endForm(); ?>
  </div>
</div>


