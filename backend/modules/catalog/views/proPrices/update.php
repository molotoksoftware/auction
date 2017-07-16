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


$this->pageTitle = 'Редактирование';

$this->breadcrumbs = array(
    array(
        'icon' => 'icon-cog ',
        'label' => 'Настройки ',
        'url' => array('/admin/settings/index'),
    ),
    array(
        'icon' => 'icon-money',
        'label' => 'ПРО (цены/сроки)',
        'url' => array('/catalog/proPrices/index'),
    ),
    array(
        'icon' => 'icon-edit',
        'label' => 'Редактирование',
        'url' => '',
    ),
);
?>

<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="box-header">
                    <span class="title"><i class="icon-plus"></i> Редактирование </span>
                    <ul class="nav nav-tabs nav-tabs-right">
                        <li>                            
                            <a rel="tooltip" data-original-title="Вернуться" href="<?= Yii::app()->createUrl('/catalog/advertRates/index'); ?>"><i class="icon-reply"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="box-content">
                    <?php
                    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                        'id' => 'form-pro-price',
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
                        ),
                            ));
                    ?>
                    <div class="padded">
                        <?php echo $form->errorSummary($model); ?>
                        <?php
                        echo $form->textFieldRow($model, 'name', array(
                            'class' => 'span8',
                            'hint' => ''
                        ));
                        ?>
                        <?php
                            echo $form->dropDownListRow($model, 'duration', ProPrice::getDurationList());
                        ?>
                        <?php
                        echo $form->textFieldRow($model, 'price', array(
                                'class' => 'span8'
                            ));
                        ?>
                        <?php
                        echo $form->textAreaRow($model, 'description', array(
                            'class' => 'span8'
                        ));
                        ?>
                        <table width="300px" class="table table-normal">
                            <thead>
                            <tr>
                                <td>Период номенклатурный</td>
                                <td>Описания</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Y</td>
                                <td>год</td>
                            </tr>
                            <tr>
                                <td>M</td>
                                <td>месяц</td>
                            </tr>
                            <tr>
                                <td>D</td>
                                <td>день</td>
                            </tr>
                            </tbody>
                        </table>
                        <?php
                        echo $form->textFieldRow($model, 'interval', array(
                                'class' => 'span8'
                            ));
                        ?>



                    </div><!--end paped -->
                    <div class="form-actions">
                        <div class="pull-right">
                            <?php
                            echo CHtml::link('<span class="icon-circle-arrow-left"></span> Вернуться', '/catalog/attribute/index', array(
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