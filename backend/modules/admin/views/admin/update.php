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


$this->pageTitle = 'Администраторы';

$this->breadcrumbs = array(
    array(
        'icon' => 'icon-user',
        'label' => 'Администраторы',
        'url' => array('/admin/admin/index'),
    ),
    array(
        'icon' => 'icon-plus',
        'label' => 'Редактирование',
        'url' => '',
    )
);
?>

<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">

            <div class="box">
                <div class="box-header">
                    <span class="title"><i class="icon-plus"></i> Редактирование</span>

                    <ul class="box-toolbar">
                        <li>
                            <a rel="tooltip" data-original-title="Вернуться" href="<?= Yii::app()->createUrl('/admin/admin/index'); ?>"><i class="icon-reply"></i></a>
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
                        'focus' => array($model, 'first_name'),
                        'htmlOptions' => array(
                            'enctype' => 'multipart/form-data'
                        ),
                    ));
                    ?>
                    <div class="padded">
                        <?php echo $form->textFieldRow($model, 'first_name'); ?>
                        <?php echo $form->textFieldRow($model, 'last_name'); ?>
                        <?php echo $form->textFieldRow($model, 'father_name'); ?>
                        <?php echo $form->textFieldRow($model, 'email'); ?>
                        <?php echo $form->textFieldRow($model, 'login'); ?>
                        <?php echo $form->textFieldRow($model, 'changePassword'); ?>



                        <?php
                        $this->widget('backend.extensions.simpleImageUpload.SimpleImageUploadWidget', array(
                            'model' => $model,
                            'form' => $form,
                            'attribute' => 'avatar_file',
                            'versionName' => 'preview'
                        ));
                        ?>

                    </div><!--end paped -->
                    <div class="form-actions">
                        <div class="pull-right"> 
                            <?php
                            echo CHtml::link('<span class="icon-circle-arrow-left"></span> Вернуться', '/admin/admin/index', array(
                                'class' => 'link'
                            ));
                            ?>
                            <?php
                            $this->widget('bootstrap.widgets.TbButton', array(
                                'buttonType' => 'submit',
                                'label' => 'Сохранить',
                                'type' => null,
                                'htmlOptions' => array(
                                    'class' => 'btn btn-blue',
                                    'value' => 'save',
                                    'name' => 'submit',
                                ),
                                'size' => 'small'
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