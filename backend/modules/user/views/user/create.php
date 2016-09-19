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


$this->pageTitle = 'Добавить';
$this->breadcrumbs = array(
    array(
        'icon' => 'icon-user',
        'label' => 'Пользователи',
        'url' => array('/user/user/index'),
    ),
    array(
        'icon' => 'icon-plus',
        'label' => 'Добавить',
        'url' => '',
    )
);
?>

<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="box-header">
                    <span class="title"><i class="icon-plus"></i> Добавить нового пользователя </span>
                    <ul class="box-toolbar">
                        <li>
                            <a rel="tooltip" data-original-title="Вернуться" href="<?= Yii::app()->createUrl('/user/user/index'); ?>"><i class="icon-reply"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="box-content">
                    <?php
                    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                        'id' => 'form-user',
                        'type' => 'horizontal',
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => true,
                        'clientOptions' => array(
                            'validateOnSubmit' => true,
                            'validateOnChange' => false,
                            'validateOnType' => false,
                        ),
                        'focus' => array($model, 'firstname'),
                        'htmlOptions' => array(
                            'enctype' => 'multipart/form-data'
                        ),
                            ));
                    ?>
                    <?php echo $form->errorSummary($model); ?>
                    <div class="padded">
                        <?php echo $form->textFieldRow($model, 'firstname'); ?>
                        <?php echo $form->textFieldRow($model, 'lastname'); ?>
                        <?php echo $form->textFieldRow($model, 'email'); ?>
                        <?php echo $form->textFieldRow($model, 'login'); ?>
                        <?php echo $form->textFieldRow($model, 'nick'); ?>
                        <?php echo $form->textFieldRow($model, 'password'); ?>
                        <?php echo $form->textFieldRow($model, 'telephone'); ?>
                        <?php echo $form->textFieldRow($model, 'rating'); ?>

                        <?php
                        $this->widget('backend.extensions.simpleImageUpload.SimpleImageUploadWidget', array(
                            'model' => $model,
                            'form' => $form,
                            'attribute' => 'avatar'
                        ));
                        ?>

                        <?php echo $form->toggleButtonRow($model, 'certified'); ?>

                        <div class="control-group ">
                            <?php echo CHtml::activeLabel($model, 'birthday', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php
                                $date = date('d-m-Y', time());
                                $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                    'name' => CHtml::activeName($model, 'birthday'),
                                    'options' => array(
                                        // 'showAnim'=>'fold',
                                        'dateFormat' => 'dd-mm-yy',
                                    ),
                                    'language' => 'ru',
                                    'value' => $date,
                                    'htmlOptions' => array(
                                        'style' => 'width:130px;', 'autocomplete' => "off")
                                ));
                                ?>
                            </div>
                        </div>



                    </div><!--end paped -->
                    <div class="form-actions">
                        <div class="pull-right">
                            <?php
                            echo CHtml::link('<span class="icon-circle-arrow-left"></span> Вернуться', '/user/user/index', array(
                                'class' => 'link'
                            ));
                            ?>
                            <?php
                            $this->widget('bootstrap.widgets.TbButton', array(
                                'buttonType' => 'submit',
                                'label' => 'Создать',
                                'type' => null,
                                'htmlOptions' => array(
                                    'class' => 'btn btn-blue',
                                    'value' => 'save',
                                    'name' => 'submit',
                                ),
                                'size' => 'small'
                            ));
                            ?>
                            <?php
                            $this->widget('bootstrap.widgets.TbButton', array(
                                'buttonType' => 'submit',
                                'label' => 'Сохранить и выйти',
                                'type' => null,
                                'htmlOptions' => array(
                                    'class' => 'btn btn-default',
                                    'value' => 'index',
                                    'name' => 'submit',
                                ),
                                'size' => 'small',
                            ));
                            ?>
                        </div>
                    </div>
                    <?php $this->endWidget(); ?>
                </div><!-- end box content -->
            </div>
        </div>
    </div><!-- row-fluid-->
</div><!--container-fluid-->