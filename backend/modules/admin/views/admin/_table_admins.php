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
        url:"' . $this->createUrl('/admin/admin/MultipleRemove') . '",
        data:{data:JSON.stringify($elements), ' . $csrf . '},
        dataType:"json",
        type:"POST",
        success:function(data){
            if (data.response.status=="success"){
                $.fn.yiiGridView.update(\'user-list\');
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
    //'type' => 'striped bordered condensed',
    'id' => 'user-list',
    //'enableSorting' => false,
    //'itemsCssClass' => 'table-normal table-hover-row',
    //'template' => "{items}",
    'dataProvider' => $model->search(),
    'filter' => $model,
    'pagerCssClass' => 'pagination pagination-centered',
    'summaryText' => 'Администраторы {start}—{end} из {count}.',
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
            'name' => 'id_admin'
        ),
    ),
    'columns' => array(
        'login',
        array(
            'header' => 'ФИО',
            'name' => 'fio',
            'value' => '$data->fio'
        ),
        array(
            'htmlOptions' => array('nowrap' => 'nowrap'),
            //'header' => 'Действия',
            'class' => 'ex-bootstrap.widgets.ETbButtonColumn',
            'template' => '{update}  {delete}',
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
            'deleteButtonVisible' => '(Yii::app()->user->checkAccess("deleteUser",array("user_id"=>$data->id_admin)))',
        )
    )
));
?>