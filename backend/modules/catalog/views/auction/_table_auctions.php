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
        url:"' . $this->createUrl('/catalog/auction/MultipleRemove') . '",
        data:{data:JSON.stringify($elements), ' . $csrf . '},
        dataType:"json",
        type:"POST",
        success:function(data){
            if (data.response.status=="success"){        
                $.fn.yiiGridView.update(\'table-auction\');
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
    'id' => 'table-auction',
    'dataProvider' => $model->search(),
    'ajaxUrl' => array('/catalog/auction/index'),
    'filter' => $model,
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
            'name' => 'auction_id'
        ),
    ),
    'columns' => array(
        array(
            'type' => 'raw',
            'name' => 'name',
            'value' => '$data->getNameForAdminTable()'
        ),
        array(
            'type' => 'raw',
            'name' => 'category_id',
            'filterHtmlOptions' => array(
                'encode' => false,
            ),
        //    'filter' => Chtml::activeDropDownList($model, 'category_id', Category::getListAllCategories(), array('encode' => false, 'empty' => '')),
            'filter' => false,
            'value' => '$data->getCategoryName()'
        ),
        /*
        array(
            'header' => 'Ставки',
            'filter' => false,
            'name' => 'countBids',
            'value' => '$data->getCountBids()'
        ),*/
        array(
            'header' => 'Д. созд.',
            'filter' => false,
            'name' => 'created',
            'value' => '$data->getDateCreated()'
        ),
        array(
            'header' => 'До завершения',
            'filter' => false,
            'type' => 'raw',
            'name' => 'bidding_date',
            'value' => '$data->getDateCompletion()'
        ),
        array(
            'header' => 'Статус',
            'filter' => Auction::getStatusList(),
            'type' => 'raw',
            'name' => 'status',
            'value' => '$data->getStatus()'
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
