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
/** @var EditUserForm $model */

cs()->registerScriptFile(Yii::app()->baseUrl . '/js/user/settings/common.js');
?>


<h3><?= Yii::t('basic', 'Access control') ?></h3>

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
        'htmlOptions' => [
            'class' => 'form-horizontal',
        ]
    )
);
?>
<div class="form-group">
    <label for="passwordOld" class="col-sm-2 control-label"><?= Yii::t('basic', 'Current password') ?></label>
    <div class="col-sm-10">
        <?php echo $form->error($model, 'passwordOld'); ?>
        <?php echo $form->passwordField($model, 'passwordOld', ['class' => 'form-control', 'style' => 'width:200px;']); ?>
    </div>
</div>
<div class="form-group">
    <label for="passwordNew" class="col-sm-2 control-label"><?= Yii::t('basic', 'New password') ?></label>
    <div class="col-sm-10">
        <?php echo $form->error($model, 'passwordNew'); ?>
        <?php echo $form->passwordField($model, 'passwordNew', ['class' => 'form-control', 'style' => 'width:200px;']); ?>
    </div>
</div>
<div class="form-group">
    <label for="passwordRe" class="col-sm-2 control-label"><?= Yii::t('basic', 'Repeat new password') ?></label>
    <div class="col-sm-10">
        <?php echo $form->error($model, 'passwordRe'); ?>
        <?php echo $form->passwordField($model, 'passwordRe', ['class' => 'form-control', 'style' => 'width:200px;']); ?>
    </div>
</div>


<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <?php echo CHtml::submitButton(Yii::t('basic', 'Confirm'), ['class' => 'btn btn-default']); ?>
    </div>
</div>


<?php $this->endWidget(); ?>
