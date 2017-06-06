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
$this->pageTitle = Yii::t('basic', 'Signup');

?>
<div class="row">
    <div class="col-xs-2"></div>
    <div class="col-xs-6">
    <h1><?= Yii::t('basic', 'Signup')?></h1>
    <?php
     //   $model = new RegistrationForm();
        /** @var CActiveForm $form */
        $form = $this->beginWidget(
            'CActiveForm',
            array(
                'id' => 'form-registration',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'action' => Yii::app()->createUrl('/user/user/registration'),
                'clientOptions' => array(
                    'validateOnSubmit' => true,
                    'errorCssClass' => 'error-row',
                    'validateOnChange' => false,
                    'validateOnType' => false,
                ),
                'htmlOptions' => array(
                    'autocomplete' => 'on',
                    'class' => 'form-horizontal'
                )
            )
        );
        ?>

        <div class="form-group">
            <?php echo $form->label($model, 'email', ['class'=> 'col-xs-4 control-label']); ?>
            <div class="col-xs-8">
            <?php echo $form->textField($model, 'email', ['class'=> 'form-control']); ?>
            <?php echo $form->error($model, 'email'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->label($model, 'login', ['class'=> 'col-xs-4 control-label']); ?>
            <div class="col-xs-8">
            <?php echo $form->textField($model, 'login', ['class'=> 'form-control']); ?>
            <?php echo $form->error($model, 'login'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->label($model, 'password', ['class'=> 'col-xs-4 control-label']); ?>
            <div class="col-xs-8">
            <?php echo $form->passwordField($model, 'password', ['class'=> 'form-control']); ?>
            <?php echo $form->error($model, 'password'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->label($model, 'confirmPassword', ['class'=> 'col-xs-4 control-label']); ?>
            <div class="col-xs-8">
            <?php echo $form->passwordField($model, 'confirmPassword', ['class'=> 'form-control']); ?>
            <?php echo $form->error($model, 'confirmPassword'); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-4 col-xs-8">
                <div class="checkbox">
                    <?php echo $form->error($model, 'agreeLicense'); ?>
                    <?php echo $form->checkBox($model, 'agreeLicense'); ?>
                    <label>
                        <a target="_blank" href="<?= Yii::app()->createUrl('/page/view', array('alias' => 'rules')); ?>">
                            <?= Yii::t('basic', 'I agree to the Terms and Conditions')?>
                        </a>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-4 col-xs-8">
            <?php echo CHtml::submitButton(Yii::t('basic', 'Sign up'), ['name' => 'submit_register', 'class' => 'btn btn-default']); ?>
            </div>
        </div>

        <?php $this->endWidget(); ?>

            </div>
    <div class="col-xs-4"></div>

</div>
