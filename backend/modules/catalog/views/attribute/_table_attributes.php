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



$csrfTokenName = Yii::app()->request->csrfTokenName;
$csrfToken = Yii::app()->request->csrfToken;
$csrf = "'$csrfTokenName':'$csrfToken'";

Yii::app()->clientScript->registerScript('multidelete', '
    function multiDelete(values){
    $elements = [];    
    $.each(values,function(i, val){
        $elements.push($(val).val());
    });
    $.ajax({
        url:"' . $this->createUrl('/catalog/attribute/MultipleRemove') . '",
        data:{data:JSON.stringify($elements), ' . $csrf . '},
        dataType:"json",
        type:"POST",
        success:function(data){
            if (data.response.status=="success"){        
                $.fn.yiiGridView.update(\'table-attribute\');
                    $(".top-right").notify({
                        type:"bangTidy",
                        fadeOut:{enabled: true, delay: 3000 },
                        transition:"fade",                                                                                 
                        message: { text: data.response.data.messages }
                    }).show();

                } else {
                    $(".top-right").notify({
                        type:"bangTidy",
                        fadeOut:{enabled: true, delay: 3000 },
                        transition:"fade",                                                                                 
                        message: { text: data.response.data.messages }
                    }).show();
                }
        }
    });
    }
');




$this->widget('ex-bootstrap.widgets.ETbExtendedGridView', array(
    'id' => 'table-attribute',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'ajaxUrl' => array('/catalog/attribute/index'),
    'summaryText' => 'Атрибуты {start}—{end} из <span>{count}</span>.',
    'bulkActions' => array(
        'actionButtons' => array(
            array(
                'id' => 'category',
                'buttonType' => 'button',
                'type' => 'danger',
                'size' => 'small',
                'label' => 'Удалить выбранные',
                'click' => 'js:function(values){if(confirm("Вы действительно хотите удалить выбранные элементы?")){multiDelete(values);} }'
            //'click' => 'js:bootbox.confirm("<p class=\'lead\'>Вы действительно хотите удалить выбранные вами страницы?</p>",
            //function(value){console.log("Confirmed: "+value);})'
            ),
        ),
        'checkBoxColumnConfig' => array(
            'name' => 'attribute_id'
        ),
    ),
    'columns' => array(
        array(
            'type' => 'raw',
            'name' => 'sys_name',
        ),
        array(
            'class' => 'bootstrap.widgets.TbToggleColumn',
            'toggleAction' => '/catalog/attribute/toggle',
            'name' => 'mandatory',
            'filter' => false,
            'sortable' => false,
            'header' => '<i class="icon-info-sign"></i>',
            'headerHtmlOptions' => array(
                'title' => 'Показывает что атрибут обязателен для заполнения'
            ),
            'htmlOptions' => array(
                'width' => '25px',
                'style' => 'text-align:center'
            )
        ),/*
        array(
            'header' => '<i class="icon-info-sign"></i>',
            'type' => 'raw',
            'value' => '$data->getDisplayPreviewAdminTable()',
            'headerHtmlOptions' => array(
                'title' => 'Показывает что атрибут отображается на странице аукционов'
            ),
            'htmlOptions' => array(
                'width' => '25px',
                'style' => 'text-align:center'
            )
        ),*/
        array(
            'header' => '<i class="icon-info-sign"></i>',
            'type' => 'raw',
            'value' => '$data->getDisplayFilterAdminTable()',
            'headerHtmlOptions' => array(
                'title' => 'Показывает что атрибут будет отображаться в фильтре'
            ),
            'htmlOptions' => array(
                'width' => '25px',
                'style' => 'text-align:center'
            )
        ),
        array(
            'type' => 'raw',
            'name' => 'type',
            'filter' => false,
            'value' => '$data->getTypeForTable()',
            'htmlOptions' => array('width' => '100px'),
        ),
        array(
            'htmlOptions' => array('nowrap' => 'nowrap'),
            'header' => '',
            'class' => 'ex-bootstrap.widgets.ETbButtonColumn',
            'template' => '{update} {delete}',
            'afterDelete' => 'function(link,success,data){
                            data =  $.parseJSON(data);
                            if(data.response.status=="success"){
                                $(".top-right").notify({
                                    type:"bangTidy",
                                    fadeOut:{enabled: true, delay: 3000 },
                                    transition:"fade",                                                                                 
                                    message: { text: data.response.data.messages }
                                }).show();
                            }else{
                                $(".top-right").notify({
                                    type:"bangTidy",
                                    fadeOut:{enabled: true, delay: 3000 },
                                    transition:"fade",                                                 
                                    message: { text: data.response.data.messages }
                                }).show();

                        }
                        }',
//            'deleteButtonVisible' => '($data->protected==1)?false:true',
        ),
    )
));
