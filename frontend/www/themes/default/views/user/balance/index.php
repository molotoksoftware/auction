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
/** @var $this FrontController */
?>


<h3><?= Yii::t('basic', 'Payments')?></h3>

<?php if (Yii::app()->user->hasFlash('failure_pay')): ?>
    <div class='alert alert-success'>
        <?= Yii::app()->user->getFlash('failure_pay'); ?>
    </div>
<?php elseif (Yii::app()->user->hasFlash('global')): ?>
    <div class='alert alert-danger'>
        <?= Yii::app()->user->getFlash('global'); ?>
    </div>
<?php endif; ?>

<div class="panel panel-default">
    <div class="panel-body">
        <h4><?= Yii::t('basic', 'Credit remaining')?>: <?= PriceHelper::formate(Getter::webUser()->getModel()->getBalance()) ?></h4>
        <a href=""><?= Yii::t('basic', 'Make a payment')?></a>
    </div>
</div>

<?php if (($textInfo = Setting::getByName('textInformationBalance')) != ''): ?>
    <div class="panel panel-default">
        <div class="panel-heading"><?= Yii::t('basic', 'Information')?></div>
        <div class="panel-body">
            <?php echo $textInfo; ?>
        </div>
    </div>
<?php endif; ?>

<h4 class="margint_top_30"><?= Yii::t('basic', 'Transaction history')?></h4>

<?php
$this->renderPartial('//user/balance/_table_history_recharge_user', ['balance' => $balance,
    'pages' => $pages]);
?>
