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


$this->pageTitle = "Редактирование страницы  \" {$model->title}\"";

$this->breadcrumbs = array(
    array(
        'icon' => 'icon-file-alt',
        'label' => 'Страницы',
        'url' => array('/page/page/index'),
    ),
    array(
        'icon' => 'icon-pencil',
        'label' => 'Редактирования',
        'url' => '',
    ),
);
?>

<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="box-header">
                    <span class="title"><i class="icon-plus"></i> Редактирования</span>
                    <ul class="box-toolbar">
                        <li>                            
                            <a rel="tooltip" data-original-title="Вернуться>" href="<?= Yii::app()->createUrl('/page/page/index'); ?>"><i class="icon-reply"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="box-content">
                    <?php
                    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                        'id' => 'form-page',
                        'type' => 'horizontal',
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => true,
                        'clientOptions' => array(
                            'validateOnSubmit' => true,
                            'validateOnChange' => false,
                            'validateOnType' => false,
                        ),
                        'focus' => array($model, 'title'),
                        'htmlOptions' => array(),
                            ));
                    ?>
                    <div class="padded">
                        <?php echo CHtml::link('<b>Ссылка на страницу</b>', Yii::app()->params["siteUrl"].'/pages/'.$model->alias,array('target'=>"_blank"));?>

                        <?php echo $form->textFieldRow($model, 'title', array('class' => 'span8')); ?>
                        <?php if (!$model->isNewRecord && $model->protected != 1): ?>
                            <?php echo $form->textFieldRow($model, 'alias', array('class' => 'span8')); ?>
                        <?php endif; ?>

                        <div class="well">

                        <?php
                        $this->widget('common.extensions.seo.widgets.SeoWidget', array(
                            'model' => $model,
                            'titleAttribute' => 'meta_title',
                            'descriptionAttribute' => 'meta_description',
                            'keywordsAttribute' => 'meta_keywords'
                        ));
                        ?> 
			</div>

                        <?php
                        Yii::import('ext.redactor.RedactorWidget');
                        $this->widget('RedactorWidget', array(
                            'model' => $model,
                            'attribute' => 'body',
                        ));
                        ?>


                    </div><!--end paped -->

                    <div class="form-actions">
                        <div class="pull-right">
                            <?php
                            echo CHtml::link('<span class="icon-circle-arrow-left"></span> Вернуться', '/page/page/index', array(
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