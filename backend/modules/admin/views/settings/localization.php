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



$this->pageTitle = $title;

$this->header_info = array(
    'icon' => 'icon-cog icon-2x',
    'title' => Yii::t('common', 'Localization'),
);

?>

<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="box-header">
                    <span class="title"><i class="icon-pencil"></i> <?=$title;?></span>
                    <ul class="nav nav-tabs nav-tabs-right">
                        <li>
                            <a rel="tooltip" data-original-title="Вернуться"
                               href="<?= Yii::app()->createUrl('/catalog/auction/index'); ?>"><i class="icon-reply"></i></a>
                        </li>
                    </ul>
                </div>
                <?php echo CHtml::beginForm('', 'post', ['class' => 'form-horizontal']); ?>

                <div class="box-content">
                    <div class="padded">

                        <? $this->widget('backend.modules.admin.widgets.menuSettingWidget'); ?>

                        <?php foreach ($model as $field): ?>
                        <div class="control-group">
                            <?php echo CHtml::label($field->title, "Setting[{$field->name}]", ['class' => 'control-label']); ?>
                            <div class="controls">
                                <?php
                                switch($field->type_field) {
                                    case Setting::TYPE_FIELD_TEXT:
                                        echo CHtml::textField(
                                            "Setting[{$field->name}]",
                                            $field->value,
                                            array('class' => '')
                                        );
                                        break;
                                    case Setting::TYPE_FIELD_TEXT_AREA:
                                        echo CHtml::textArea(
                                            "Setting[{$field->name}]",
                                            $field->value,
                                            array('class' => '')
                                        );
                                        break;
                                    case Setting::TYPE_FIELD_CHECK_BOX:
                                        echo CHtml::checkBox(
                                            "Setting[{$field->name}]",
                                            $checked = $field->value?true:false,
                                            array('class' => '', 
                                                'value' => '1',
                                                'uncheckValue' => '0')
                                        );
                                        break;
                                    case Setting::TYPE_FIELD_LOCATION:
                                        $this->widget('frontend.widgets.citySelector.CitySelectorWidget', array('model' => $field, 'baseUrl' => Yii::app()->params['siteUrl']));
                                        break;
                                };
                                ?>
                            </div>
                        </div>
                        <?php endforeach; ?>


                    </div>

                    <div class="form-actions">
                        <div class="pull-right">
                            <?php
                            echo CHtml::link(
                                '<span class="icon-circle-arrow-left"></span> Вернуться',
                                '/main/index',
                                array(
                                    'class' => 'link'
                                )
                            );
                            ?>
                            <?php
                            $this->widget(
                                'bootstrap.widgets.TbButton',
                                array(
                                    'buttonType' => 'submit',
                                    'label' => 'Сохранить',
                                    'type' => null,
                                    'htmlOptions' => array(
                                        'class' => 'btn btn-blue',
                                        'value' => 'index',
                                        'name' => 'submit',
                                    ),
                                    'size' => 'small',
                                )
                            );
                            ?>
                        </div>
                    </div>
                </div>
                <?php echo CHtml::endForm(); ?>
            </div>
        </div>
    </div>
</div>
