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


$this->pageTitle = 'Создание нового значения';

$this->breadcrumbs = array(
    array(
        'icon' => 'icon-cog ',
        'label' => 'Настройки ',
        'url' => array('/admin/settings/index'),
    ),
    array(
        'icon' => 'icon-money',
        'label' => 'Цены/сроки на объявления',
        'url' => array('/catalog/advertRates/index'),
    ),
    array(
        'icon' => 'icon-plus',
        'label' => 'Создание',
        'url' => '',
    ),
);
?>

<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="box-header">
                    <span class="title"><i class="icon-plus"></i> Создание </span>
                    <ul class="nav nav-tabs nav-tabs-right">
                        <li>
                            <a rel="tooltip" data-original-title="Вернуться"
                               href="<?= Yii::app()->createUrl('/catalog/advertRates/index'); ?>"><i
                                    class="icon-reply"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="box-content">
                    <?php
                    $form = $this->beginWidget(
                        'bootstrap.widgets.TbActiveForm',
                        array(
                            'id' => 'form-advert-rates',
                            'type' => 'horizontal',
                            'enableAjaxValidation' => true,
                            'enableClientValidation' => true,
                            'clientOptions' => array(
                                'validateOnSubmit' => true,
                                'validateOnChange' => false,
                                'validateOnType' => false,
                            ),
                            'focus' => array($model, 'price'),
                            'htmlOptions' => array(//'enctype' => 'multipart/form-data'
                            ),
                        )
                    );
                    ?>
                    <div class="padded">
                        <?php echo $form->errorSummary($model); ?>
                        <?php
                        echo $form->textFieldRow(
                            $model,
                            'days',
                            array(
                                'class' => 'span8',
                                'hint' => 'Введите целое количество дней (только целые числа)'
                            )
                        );
                        ?>
                        <?php
                        echo $form->textFieldRow(
                            $model,
                            'price',
                            array(
                                'class' => 'span8'
                            )
                        );
                        ?>




                    </div>
                    <!--end paped -->
                    <div class="form-actions">
                        <div class="pull-right">
                            <?php
                            echo CHtml::link(
                                '<span class="icon-circle-arrow-left"></span> Вернуться',
                                '/catalog/advertRates/index',
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
                                    'label' => 'Создать',
                                    'type' => null,
                                    'htmlOptions' => array(
                                        'class' => 'btn btn-blue',
                                        'value' => 'save',
                                        'name' => 'submit',
                                    ),
                                    'size' => 'small'
                                )
                            );
                            ?>
                            <?php
                            $this->widget(
                                'bootstrap.widgets.TbButton',
                                array(
                                    'buttonType' => 'submit',
                                    'label' => 'Сохранить и выйти',
                                    'type' => null,
                                    'htmlOptions' => array(
                                        'class' => 'btn btn-default',
                                        'value' => 'index',
                                        'name' => 'submit',
                                    ),
                                    'size' => 'small',
                                )
                            );
                            ?>
                        </div>
                    </div>
                    <?php $this->endWidget(); ?>
                </div>
                <!-- end box content -->
            </div>
        </div>
    </div>
    <!-- row-fluid-->
</div><!--container-fluid-->