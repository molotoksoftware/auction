<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://www.molotoksoftware.com/
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

/**
 * @var BackendController $this
 */

$this->pageTitle = 'Авторизация';

$this->layout = '//layouts/clean';
$this->bodyAddClass = 'login-page';

Yii::app()->clientScript->registerScriptFile(bu() . '/plugins/iCheck/icheck.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile('/plugins/iCheck/square/blue.css');

Yii::app()->clientScript->registerScript('iCheck', "
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
", CClientScript::POS_END);

?>

<div class="login-box">
    <div class="login-logo">
        <a href="">MOLOTOK</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg"><?= Yii::t('common', 'authorization'); ?></p>


            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'form-login',
                'enableAjaxValidation' => true,
                'enableClientValidation' => false,
                'clientOptions' => array(
                    'validateOnSubmit' => true,
                    'validateOnChange' => false,
                    'validateOnType' => false,
                ),
                'focus' => array($model, 'username'),
                'htmlOptions' => array(
                    'class' => ''
                ),
            ));
            ?>


        <div class="form-group has-feedback">
                <?php echo $form->textField($model, 'username', ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('username')]); ?>
                <?php $form->error($model, 'username'); ?>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <?php echo $form->passwordField($model, 'password', ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('password')]); ?>
                <?php $form->error($model, 'password'); ?>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>



            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label for="LoginForm_rememberMe">
                            <?php echo $form->checkBox($model, 'rememberMe'); ?>
                            <?php echo $model->getAttributeLabel('rememberMe'); ?>
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" id="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
                <!-- /.col -->
            </div>

        <?php echo $form->errorSummary($model,"",""); ?>



        <?php $this->endWidget(); ?>

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
