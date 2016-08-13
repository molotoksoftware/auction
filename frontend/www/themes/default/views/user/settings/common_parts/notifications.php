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

<h3>Уведомления</h3>

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
        Система автоматически присылает важные уведомления Вам на почтовый ящик. Не отключайте эту функцию, если хотите быть в курсе всех
        событий связанных с Вашим участием на торговой площадке.
    </p>
  <div class="checkbox">
    <label>
          <?php echo $form->checkBox($user, 'consent_recive_notification'); ?>
        Получать уведомления на E-mail
    </label>
  </div>
    <?php echo CHtml::submitButton('Сохранить', ['class' => 'btn btn-default']); ?>
 </div>


                
<?php $this->endWidget(); ?>

