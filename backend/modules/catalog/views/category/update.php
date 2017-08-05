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


$this->pageTitle = "Редактирование категории  \" {$model->name}\"";
$this->breadcrumbs = array(
    array(
        'icon' => 'icon-folder-open',
        'label' => 'Каталог',
        'url' => array('/catalog/category/index'),
    ),
    array(
        'icon' => 'icon-inbox',
        'label' => 'Категории',
        'url' => array('/catalog/category/index'),
    ),
    array(
        'icon' => 'icon-edit',
        'label' => 'Редактирование категории',
        'url' => '',
    ),
);
?>


<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="box-header">
                    <span class="title"><i class="icon-edit"></i> Редактирование категории  "<?php echo $model->name; ?>"</span>
                    <ul class="nav nav-tabs nav-tabs-right">
                        <li class="active"><a data-toggle="tab" href="#common"><i class="icon-home"></i></a></li>
                        <li><a data-toggle="tab" href="#options"><i class="icon-cog"></i> <span>Параметры</span></a></li>
                        <li>                            
                            <a rel="tooltip" data-original-title="back" href="<?= Yii::app()->createUrl('/catalog/category/index'); ?>"><i class="icon-reply"></i></a>
                        </li>

                    </ul>
                </div>
                <div class="box-content">
                    <?php
                    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                        'id' => 'form-category',
                        'type' => 'horizontal',
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => true,
                        'clientOptions' => array(
                            'validateOnSubmit' => true,
                            'validateOnChange' => false,
                            'validateOnType' => false,
                        ),
                        'focus' => array($model, 'name'),
                        'htmlOptions' => array(
//                            'enctype' => 'multipart/form-data'
                        ),
                            ));
                    ?>
                    <div class="padded">
                        <div class="tab-content">
                            <div id="common" class="tab-pane active">
                                <?php
                                echo $form->textFieldRow($model, 'name', array(
                                    'class' => 'span8'
                                ));
                                ?>
                                <?php
                                echo $form->textFieldRow($model, 'alias', array(
                                    'class' => 'span8',
                                    'hint' => 'Должно быть уникальным на всю систему (только латинские символы)'
                                ));
                                ?>
                                <?php
//                                echo $form->dropDownListRow($model, 'status', array(Category::ST_ACTIVE => 'Да', Category::ST_NO_ACTIVE => 'Нет'), array(
//                                    'class' => 'span5',
//                                    'hint' => 'Отображать на сайте'
//                                ));
                                ?>

                                <?php
                                echo $form->textAreaRow($model, 'description', array('row' => 60, 'cols' => 3, 'class' => 'span8'));
                                ?>
                                <?php
                                $this->widget('common.extensions.seo.widgets.SeoWidget', array(
                                    'model' => $model,
                                ));
                                ?>
                                <div class="control-group ">
                                    <label for="duration" class="control-label">Применить значения атрибутов к дочерним категориям</label>
                                    <div class="controls">
                                        <?php echo $form->checkBox($model, 'applyToChild', array()); ?>
                                        <?php echo $form->error($model, 'applyToChild'); ?>
                                    </div>
                                </div>
                            </div>
                            <div id="options" class="tab-pane" >
                                <?php
                                //CategoryAttributes::model()->findAll('category_id=:category_id', array(':category_id' => $model->category_id))
                                $this->widget('backend.widgets.multiselect.MultiselectWidget', array(
                                    'data' => Chtml::listData(Attribute::model()->findAll(), 'attribute_id', 'sys_name'),
                                    'name' => 'options[]',
                                    'value' => $model->getFavAttrForSelect(),
                                    'id' => 'multiple-options'
                                ));
                                ?>
                            </div>
                        </div>
                    </div><!--end paped -->
                    <div class="form-actions">
                        <div class="pull-right">
                            <?php
                            echo CHtml::link('<span class="icon-circle-arrow-left"></span> back', '/catalog/category/index', array(
                                'class' => 'link'
                            ));
                            ?>
                            <?php
                            $this->widget('bootstrap.widgets.TbButton', array(
                                'buttonType' => 'submit',
                                'label' => 'save',
                                'type' => null,
                                'htmlOptions' => array(
                                    'class' => 'btn btn-blue',
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