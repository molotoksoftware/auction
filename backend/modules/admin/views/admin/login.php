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


$this->pageTitle = 'Авторизация';
$this->layout = '//layouts/static';
?>

<div class="navbar navbar-top navbar-inverse">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" href="<?php echo Yii::app()->createUrl(Yii::app()->params['adminUrl']); ?>"> Панель управления</a>
            <ul class="nav pull-right">
                <li class="toggle-primary-sidebar hidden-desktop" data-toggle="collapse" data-target=".nav-collapse-primary"><a><i class="icon-th-list"></i></a></li>
                <li class="collapsed hidden-desktop" data-toggle="collapse" data-target=".nav-collapse-top"><a><i class="icon-align-justify"></i></a></li>
            </ul>
        </div>
    </div>
</div>
<div class="container">
    <div class="span4 offset4">
        <div class="padded">
            <div class="login box" style="margin-top: 80px;">
                <div class="box-header">
                    <span class="title"><?= Yii::t('common', 'authorization'); ?></span>
                </div>
                <div class="box-content padded">
                    <?php
                    $form = $this->beginWidget('CActiveForm', array(
                        'id' => 'form-login',
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => true,
                        'clientOptions' => array(
                            'validateOnSubmit' => true,
                            'validateOnChange' => false,
                            'validateOnType' => false,
                        ),
                        'focus' => array($model, 'username'),
                        'htmlOptions' => array(
                            'class' => 'separate-sections'
                        ),
                    ));
                    ?>
                    <?php echo $form->error($model, 'password', array('class' => 'alert alert-error')); ?>

                    <div class="input-prepend">
                        <span class="add-on">
                            <i class="icon-user"></i>
                        </span>
                        <?php echo $form->textField($model, 'username', array('placeholder' => $model->getAttributeLabel('username'))); ?>
                        <?php $form->error($model, 'username'); ?>
                    </div>

                    <div class="input-prepend">
                        <span class="add-on">
                            <i class="icon-key"></i>
                        </span>
                        <?php echo $form->passwordField($model, 'password', array('placeholder' => $model->getAttributeLabel('password'))); ?>
                        <?php $form->error($model, 'password'); ?>
                    </div>
                    <div class="input-prepend">
                        <label for="LoginForm_rememberMe" class="checkbox">
                            <?php echo $form->checkBox($model, 'rememberMe'); ?>
                            <?php echo $model->getAttributeLabel('rememberMe'); ?>    
                        </label>
                    </div>
                    <div>
                        <?php
                        $this->widget('bootstrap.widgets.TbButton', array(
                            'label' => 'Войти',
                            'icon' => 'icon-signin',
                            'type' => null,
                            'size' => null,
                            'htmlOptions' => array(
                                'class' => 'btn-blue btn-block',
                                'id' => 'submit',
                            )
                        ));
                        ?>
                        <?php Yii::app()->clientScript->registerScript('loginSubmit', '
                        $("#submit").click(function(){
                            $("#form-login").submit();
                        });    

                        ', CClientScript::POS_LOAD); ?>
                    </div>
                    <?php $this->endWidget(); ?>
                </div>

            </div>
        </div>
    </div>
</div>