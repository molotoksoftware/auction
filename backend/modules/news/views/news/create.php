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


$this->pageTitle = 'Добавление новости';
$this->breadcrumbs = array(
    array(
        'icon' => 'icon-leaf',
        'label' => 'Новости',
        'url' => array('/news/news/index'),
    ),
    array(
        'icon' => 'icon-plus',
        'label' => 'Добавление новости',
        'url' => '',
    ),
);
?>
<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="box-header">
                    <span class="title"><i class="icon-plus"></i> Добавление новости</span>
                    <ul class="box-toolbar">
                        <li>                            
                            <a rel="tooltip" data-original-title="Вернуться" href="<?= Yii::app()->createUrl('/news/news/index'); ?>"><i class="icon-reply"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="box-content">
                    <?php
                    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                        'id' => 'form-news',
                        'type' => 'vertical',
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => false,
                        'clientOptions' => array(
                            'validateOnSubmit' => true,
                            'validateOnChange' => false,
                            'validateOnType' => false,
                        ),
                        'focus' => array($model, 'title'),
                        'htmlOptions' => array(
                            'enctype' => 'multipart/form-data'
                        ),
                            ));
                    ?>
                    <div class="padded">
                        <?php echo $form->errorSummary($model); ?>
                        <?php echo $form->textFieldRow($model, 'title', array(
                            'class'=>'span8'
                        )); ?>
                        
                        <div class="control-group ">
                            <label class="control-label" for="<?php echo CHtml::activeId($model, 'date') ?>"><?php echo $model->getAttributeLabel('date'); ?></label>
                            <div class="controls">
                               <?php
                                $date = date('d-m-Y', time());
                                $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                    'name' => CHtml::activeName($model, 'date'),
                                    'options' => array(
                                        // 'showAnim'=>'fold',
                                        'dateFormat' => 'dd-mm-yy',
                                    ),
                                    'language' => 'ru',
                                    'value' => $date,
                                    'htmlOptions' => array(
                                        'style' => 'width:100px;')
                                ));
                                ?>
                            </div>
                        </div>
                        
                        <?php echo $form->textAreaRow($model, 'short_description', array(
                            'class'=>'span8'
                        )); ?>
                        <?php
                        Yii::import('ext.redactor.RedactorWidget');
                        $this->widget('RedactorWidget', array(
                            'model' => $model,
                            'attribute' => 'content',
                        ));
                        ?>
                        <?php $form->error($model, 'content'); ?>
                        <hr/>
                        <?php echo $form->toggleButtonRow($model, 'status'); ?>

                        <?php $this->widget('backend.extensions.simpleImageUpload.SimpleImageUploadWidget', array(
                            'model'=>$model,
                            'form'=>$form,
                            'attribute'=>'images'
                        ));?>
                        
                        <?php
                        $this->widget('common.extensions.seo.widgets.SeoWidget', array(
                            'model' => $model,
                            'titleAttribute' => 'meta_title',
                            'descriptionAttribute' => 'meta_description',
                            'keywordsAttribute' => 'meta_keywords'
                        ));
                        ?>
                    </div><!--end paped -->
                    <div class="form-actions">
                        <div class="pull-right">
                            <?php
                            echo CHtml::link('<span class="icon-circle-arrow-left"></span> Вернуться', '/news/news/index', array(
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