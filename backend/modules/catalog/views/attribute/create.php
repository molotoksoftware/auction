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


$this->pageTitle = 'Создание нового атрибута';

$this->breadcrumbs = array(
    array(
        'icon' => 'icon-folder-open',
        'label' => 'Каталог',
        'url' => array('/catalog/category/index'),
    ),
    array(
        'icon' => 'icon-list-alt',
        'label' => 'Атрибуты',
        'url' => array('/catalog/attribute/index'),
    ),
    array(
        'icon' => 'icon-plus',
        'label' => 'Создание атрибута',
        'url' => '',
    ),
);
$sortable = <<<EOD
// Enable table rows sorting
    $(".optionsEditTable tbody").sortable();
EOD;

$scripts = <<<EOD
// Add new row
    $(".optionsEditTable .plusOne").click(function(){
        var row = $(".optionsEditTable .copyMe").clone().removeClass('copyMe');
        row.appendTo(".optionsEditTable tbody");
        row.find(".value").each(function(i, el){
            $(el).attr('name', 'values[]');
        });
        return false;
    });
    
// Delete row
    $(".optionsEditTable").delegate(".deleteRow", "click", function(){
        $(this).parent().parent().remove();

        if($(".optionsEditTable tbody tr").length == 1)
        {
            $(".optionsEditTable .plusOne").click();
        }
        return false;
    });
    
//first start    
    if($(".optionsEditTable tbody tr").length == 1)
    {
        $(".optionsEditTable .plusOne").click();
    }


//change type
    $('#Attribute_type').change(function(){
        if (($(this).val()==type_text) || ($(this).val()==type_text_area) || ($(this).val()==type_text_range) || ($(this).val()==type_child_element)) {
            $("a[href='#options']").hide();
        } else {
            $("a[href='#options']").show();
        }
    });
EOD;
Yii::app()->clientScript
    ->registerCoreScript('jquery.ui')
    ->registerScript(
        'types',
        'var type_text = ' . Attribute::TYPE_TEXT .
            ', type_text_area = ' . Attribute::TYPE_TEXT_AREA .
            ', type_text_range = ' . Attribute::TYPE_TEXT_RANGE .
            ', type_child_element = ' . Attribute::TYPE_CHILD_ELEMENT . ';'
        ,
        CClientScript::POS_END
    )
    ->registerScript('sortable', $sortable, CClientScript::POS_READY)
    ->registerScript('scripts', $scripts, CClientScript::POS_END);
?>

<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="box-header">
                    <span class="title"><i class="icon-plus"></i> Создание атрибута</span>
                    <ul class="nav nav-tabs nav-tabs-right">
                        <li class="active"><a data-toggle="tab" href="#common"><i class="icon-home"></i></a></li>
                        <li><a data-toggle="tab" href="#options"><i class="icon-cog"></i> <span>Опции</span></a></li>
                        <li>
                            <a rel="tooltip" data-original-title="Вернуться"
                               href="<?= Yii::app()->createUrl('/catalog/attribute/index'); ?>"><i
                                    class="icon-reply"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="box-content">
                    <?php
                    /** @var TbActiveForm $form */
                    $form = $this->beginWidget(
                        'bootstrap.widgets.TbActiveForm',
                        array(
                            'id' => 'form-attribute',
                            'type' => 'horizontal',
                            'enableAjaxValidation' => true,
                            'enableClientValidation' => true,
                            'clientOptions' => array(
                                'validateOnSubmit' => true,
                                'validateOnChange' => false,
                                'validateOnType' => false,
                            ),
                            'focus' => array($model, 'name'),
                            'htmlOptions' => array(),
                        )
                    );
                    ?>
                    <div class="padded">
                        <?php echo $form->errorSummary($model); ?>
                        <div class="tab-content">
                            <div id="common" class="tab-pane active">
                                <?php
                                echo $form->textFieldRow(
                                    $model,
                                    'name',
                                    array(
                                        'class' => 'span8',
                                        'hint' => 'Название которое отображается на сайте'
                                    )
                                );
                                ?>
                                <?php
                                echo $form->textFieldRow(
                                    $model,
                                    'sys_name',
                                    array(
                                        'class' => 'span8',
                                        'hint' => 'Полное имя  (отображения в админ. части )'
                                    )
                                );
                                ?>
                                <?php
                                echo $form->textFieldRow(
                                    $model,
                                    'identifier',
                                    array(
                                        'class' => 'span8',
                                        'hint' => 'Должно быть уникальным на всю систему (только латинские символы)'
                                    )
                                );
                                ?>
                                <?php
                                echo $form->dropDownListRow(
                                    $model,
                                    'type',
                                    Attribute::getAvailableType(),
                                    array(
                                        'class' => 'span5'
                                    )
                                );
                                ?>
                                <?php // $form->checkBoxRow($model, 'show_expanded')?>
                                <?php echo $form->toggleButtonRow($model, 'mandatory'); ?>
                                <?php // echo $form->toggleButtonRow($model, 'display_preview_page'); ?>
                                <?php echo $form->toggleButtonRow($model, 'display_filter'); ?>
                            </div>
                            <div id="options" class="tab-pane">
                                <!------------------------------------------------->
                                <table class="optionsEditTable">
                                    <thead>
                                    <tr>
                                        <td></td>
                                        <td>Значения</td>
                                        <td>
                                            <a class="plusOne" href="#"><i class="icon-plus-sign"></i> Добавить</a>
                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody class="ui-sortable" style="">
                                    <tr class="copyMe" style="">
                                        <td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
                                        <td>
                                            <input type="text" class="value" name="sample">
                                        </td>
                                        <td>
                                            <a class="deleteRow" href="#"><i class="icon-trash"></i> Удалить</a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <!------------------------------------------------->
                            </div>
                        </div>
                    </div>
                    <!--end paped -->
                    <div class="form-actions">
                        <div class="pull-right">
                            <?php
                            echo CHtml::link(
                                '<span class="icon-circle-arrow-left"></span> Вернуться',
                                '/catalog/attribute/index',
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