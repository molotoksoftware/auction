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
    'title' => 'Настройки сайта',
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
                <?php echo CHtml::beginForm(); ?>

                <div class="box-content">
                    <div class="padded">

                        <? $this->widget('backend.modules.admin.widgets.menuSettingWidget'); ?>


                        <h3>ПРО аккаунт</h3>
                        <?php
                        Yii::import('ext.redactor.RedactorWidget');
                        $this->widget(
                            'RedactorWidget',
                            array(
                                'name' => 'Form[text_pro_account]',
                                'value' => $text_pro_account,
                                'selector' => '#text_pro_account',
                                'htmlOptions' => array('id' => 'text_pro_account')
                            )
                        );
                        ?>

                        <h3>Текст для раздела Верификация аккаунта</h3>
                        <?php
                        Yii::import('ext.redactor.RedactorWidget');
                        $this->widget(
                            'RedactorWidget',
                            array(
                                'name' => 'Form[text_certified]',
                                'value' => $text_certified,
                                'selector' => '#text_certified',
                                'htmlOptions' => array('id' => 'text_certified')
                            )
                        );
                        ?>

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
