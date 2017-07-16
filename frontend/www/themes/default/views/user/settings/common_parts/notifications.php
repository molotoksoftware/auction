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

cs()->registerScriptFile(Yii::app()->baseUrl.'/js/user/settings/common.js');
?>

<h3><?= Yii::t('basic', 'Notifications e-mail')?></h3>

<?php if (Yii::app()->user->hasFlash('success-edit-profile')): ?>
    <div class="alert alert-success">
        <?= Yii::app()->user->getFlash('success-edit-profile'); ?>
    </div>
<?php endif; ?>

<?php
/** @var CActiveForm $form */
$form = $this->beginWidget(
    'CActiveForm',
    array(
        'errorMessageCssClass' => 'error',
        'clientOptions' => array(
            'errorCssClass' => 'error-row',
            'successCssClass' => 'success-row',
        ),
    )
);
        ?>

 <div class="form-group">
     <p>
        <?= Yii::t('basic', 'The system automatically sends you important notifications to the mailbox. Do not turn off this feature if you want to be informed about all events.')?>
    </p>
  <div class="checkbox">
    <label>
          <?php echo $form->checkBox($user, 'consent_receive_notification'); ?>
        <?= Yii::t('basic', 'Receive e-mail notifications')?>
    </label>
  </div>
    <?php echo CHtml::submitButton(Yii::t('basic', 'Save change'), ['class' => 'btn btn-default']); ?>
 </div>

<?php $this->endWidget(); ?>

