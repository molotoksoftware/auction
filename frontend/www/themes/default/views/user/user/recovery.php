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

$this->layout = '//layouts/common';
$this->pageTitle = 'Восстановление пароля';

?>

<div class="row">
    <div class="col-xs-2"></div>
    <div class="col-xs-6">
    <h1>Восстановление пароля</h1>
    <p><small>
    На вашу электронную почту мы отправим новый пароль. Он генерируется автоматически и его сложно запомнить. Рекомендуем поменять пароль в настройках аккаунта.
    Если письмо не пришло, посмотрите в папке Нежелательная почта (спам), возможно оно там. Либо попробуйте восстановить пароль заново.
    Учтите, пароль будет изменён!
        </small></p>
    
<?php 
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'form-recovery',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'action' => Yii::app()->createUrl('/user/user/recovery'),
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => true,
        'validateOnType' => false,
    ),
    'htmlOptions' => array(
        'autocomplete' => 'off'
    )
        ));
?>
 <?php if (Yii::app()->user->hasFlash('failure_sent')): ?>
    <div class='alert alert-danger'>
        <?=Yii::app()->user->getFlash('failure_sent');?>
    </div>
    <?php elseif (Yii::app()->user->hasFlash('succes_sent')): ?>
    <div class='alert alert-success'>
        <?=Yii::app()->user->getFlash('succes_sent'); ?>
    </div>
<?php endif; ?>
<div class="form-group">
    <?php echo $form->label($model, 'email', ['class'=> 'col-xs-4 control-label']); ?>
    <div class="col-xs-8">
    <?php echo $form->textField($model, 'email', ['class'=> 'form-control']); ?>
    <?php echo $form->error($model, 'email'); ?>
    </div>
</div>

 <div class="form-group">
    <div class="col-xs-offset-4 col-xs-8">
        <?php echo CHtml::submitButton('Получить новый пароль', ['class' => 'btn btn-default margint_top_30']);?>
    </div>
</div>

<?php $this->endWidget(); ?>

</div>
</div>