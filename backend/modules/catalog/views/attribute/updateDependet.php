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


$this->pageTitle = "Редактирование атрибута  \" {$model->name}\"";

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
        'icon' => 'icon-edit',
        'label' => 'Редактирование атрибута ',
        'url' => '',
    ),
);
$sortable = <<<EOD
// Enable table rows sorting
    $(".optionsEditTable tbody").sortable();
    $(".children-container").sortable();

EOD;
$scripts = <<<EOD
// Add new row
    $(".optionsEditTable .plusOne").click(function(){
        var maxIndex = $('.optionsEditTable').find('tr:last').index();
        var row = $(".optionsEditTable .copyMe").clone().removeClass('copyMe');
        row.appendTo(".optionsEditTable tbody");
        var child = row.find('.childrensBlok');
        var rootAttributeId = row.find('td > input').data('rootAttributeId');
        var childAttributeId = row.find('td > input').data('childAttributeId');

        $(child).find('input').attr('data-parent-value', maxIndex);
        $(child).find('input').attr('data-parent-id', 'n' + maxIndex);
        $(child).find('input').attr('data-child-attribute-id', childAttributeId);


        row.find("td > input").attr('name', 'values[update][root]['+rootAttributeId+'][n' + maxIndex +']');
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
    if (($('#Attribute_type').val()==type_text) || ($('#Attribute_type').val()==type_text_area)) {
        $("a[href='#options']").hide();
    }
    
//change type
    $('#Attribute_type').change(function(){
        if (($(this).val()==type_text) || ($(this).val()==type_text_area)) {
            $("a[href='#options']").hide();
        } else {
            $("a[href='#options']").show();
        }
    });

    //children =========================================================================================================

    //toggle show/hide childs
    $('a.btn-toggle-children-block').live('click', function() {
        var nameClass = $(this).find('i').attr('class');
        if (nameClass=='icon-chevron-down') {

            $(this).parent().parent().find('.childrensBlok').show();
            $(this).find('i').removeClass('icon-chevron-down').addClass('icon-chevron-up');
        } else {
            $(this).parent().parent().find('.childrensBlok').hide();
            $(this).find('i').removeClass('icon-chevron-up').addClass('icon-chevron-down');
        }

        return false;
    });

    $('a.toggle-show-oll-child').live('click', function() {
        var nameClass = $(this).find('i').attr('class');
        if (nameClass=='icon-chevron-down') {

            $('.optionsEditTable tr > td.btn-column').each(function(i, el){
                $(el).find('.btn-toggle-children-block').click();
            });


            $(this).find('i').removeClass('icon-chevron-down').addClass('icon-chevron-up');
            $(this).find('span').html('Скрыть все дочерние');


        } else {

            $('.optionsEditTable tr > td.btn-column').each(function(i, el){
                $(el).find('.btn-toggle-children-block').click();
            });

            $(this).find('i').removeClass('icon-chevron-up').addClass('icon-chevron-down');
            $(this).find('span').html('Показать все дочерние');

        }

        return false;
    });




    //add child
    $('.add-child').live('click', function() {
        var mainBlock = $(this).parent().parent().find('.children-container');
        var row = mainBlock.find('.child-row-empty').clone().removeClass('child-row-empty').addClass('child-row');
        row.appendTo(mainBlock);
        var maxIndex = $(mainBlock).find('.child-row:last').index();
        var parentId = mainBlock.find('input').data('parentId');
        var parentValue = mainBlock.find('input').data('parentValue');
        var childAttributeId = row.find('input').data('childAttributeId');

        if (row.find('input').data('attributeId')==undefined) {
            //new
        //    values[update][dep][50][927][930]
            row.find('input').attr('name', 'values[update][dep][' + childAttributeId + '][' + parentId  + '][nd'+maxIndex+']');
        } else {
            //update
            var attrId = row.find('input').data('attributeId');
            row.find('input').attr('name', 'values[update][dep][' + attrId + '][' + parentId + '][nd' + maxIndex  + ']');
        }


        return false;
    });

    //remove child
    $('.delete-child').live("click", function() {
        $(this).parent().remove();
    });



EOD;
Yii::app()->clientScript
    ->registerCoreScript('jquery.ui')
    ->registerScript(
        'types',
        'var type_text = ' . Attribute::TYPE_TEXT . ', type_text_area = ' . Attribute::TYPE_TEXT_AREA . ';'
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
    <span class="title"><i class="icon-edit"></i> Редактирование атрибута  "<?= $model->name; ?>"</span>
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
                    'hint' => 'Назва которая отображается на сайте'
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
                'child_id',
                Attribute::getAllChildAttribute(),
                array(
                    'class' => 'span5'
                )
            );
            ?>
            <?=$form->checkBoxRow($model, 'show_expanded')?>

            <?php
            echo $form->toggleButtonRow($model, 'display_preview_page');
            ?>
            <?php
            echo $form->toggleButtonRow($model, 'display_filter');
            ?>
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
                        <a class="toggle-show-oll-child" href="#"><i class="icon-chevron-down"></i> <span>Показать все дочерние</span></a>
                    </td>
                </tr>
                </thead>
                <tbody class="ui-sortable" style="">

                <tr class="copyMe" style="">
                    <td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
                    <td>
                        <input data-root-attribute-id="<?php echo $model->attribute_id; ?>" type="text" class="value"
                               data-child-attribute-id="<?php echo $model->child_id; ?>"

                               name="">

                        <div class="childrensBlok">
                            <div class="btn-tools">
                                <a class="add-child" href="#"><i class="icon-plus-sign"></i>
                                    Добавить</a>
                            </div>

                            <div class="children-container">
                                <div class='child-row-empty'>
                                    <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                    <input type="text" value="" data-parent-value=""
                                           data-parent-id='' class="value" name="">
                                    <a class="delete-child" href="#"><i class="icon-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class='btn-column'>
                        <a class="btn-toggle-children-block" href="#"><i
                                class="icon-chevron-down"></i></a>
                        <a class="deleteRow" href="#"><i class="icon-trash"></i> Удалить</a>
                    </td>
                </tr>

                <?php if (!empty($values)): ?>
                    <?php foreach ($values as $i => $value): ?>
                        <tr class="">
                            <td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
                            <td>
                                <input type="text" value="<?= $value->value; ?>" class="value"
                                       name="values[update][root][<?= $value->attribute_id; ?>][<?= $value->value_id; ?>]">

                                <div class="childrensBlok">
                                    <div class="btn-tools">
                                        <a class="add-child" href="#"><i class="icon-plus-sign"></i>
                                            Добавить</a>
                                    </div>
                                    <div class="children-container">

                                        <div class='child-row-empty'>
                                            <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                            <input type="text" value=""
                                                   data-attribute-id="<?= $model->child_id; ?>"
                                                   data-parent-value="<?= $i; ?>"
                                                   data-parent-id='<?= $value->value_id; ?>'
                                                   class="value" name="">
                                            <a class="delete-child" href="#"><i
                                                    class="icon-trash"></i></a>
                                        </div>

                                        <?php $child = $value->child;?>
                                        <?php if (!is_null($child)): ?>
                                            <?php foreach ($child as $chl): ?>
                                                <div class='child-row'>
                                                    <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                                    <input type="text" value="<?= $chl->value; ?>"
                                                           data-parent-value="<?= $i; ?>"
                                                           data-parent-id='<?= $value->value_id; ?>'
                                                           class="value"
                                                           name="values[update][dep][<?= $chl->attribute_id; ?>][<?= $value->value_id; ?>][<?= $chl->value_id; ?>]">
                                                    <a class="delete-child" href="#"><i
                                                            class="icon-trash"></i></a>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class='btn-column'>
                                <a class="btn-toggle-children-block" href="#"><i
                                        class="icon-chevron-down"></i></a>
                                <a class="deleteRow" href="#"><i class="icon-trash"></i> Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
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
<?php $this->endWidget(); ?>
</div>
<!-- end box content -->
</div>
</div>
</div>
<!-- row-fluid-->
</div><!--container-fluid-->