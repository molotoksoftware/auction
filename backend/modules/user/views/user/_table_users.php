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
        url:"' . $this->createUrl('/user/user/MultipleRemove') . '",
        data:{data:JSON.stringify($elements), ' . $csrf . '},
        dataType:"json",
        type:"POST",
        success:function(data){
            if (data.response.status=="success"){        
                $.fn.yiiGridView.update(\'table-users\');
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
    'id' => 'table-users',
    'dataProvider' => $model->search(),
    'filter' => $model,
    //'pagerCssClass' => 'pagination pagination-centered',
    'summaryText' => 'Пользователи {start}—{end} из {count}.',
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
            'name' => 'user_id'
        ),
    ),
    'columns' => array(
        'login',
        array(
            'header' => 'Email',
            'name' => 'email',
            'value' => '$data->email'
        ),
        array(
            'name' => 'createtime',
            'filter' => false,
            'value' => 'Yii::app()->dateFormatter->format("dd MMMM yyyy", $data->createtime)'
        ),
        array(
            'name' => 'rating',
            'filter'=>false
        ),
        array(
            'header'=>'Кол. лотов',
            'value'=>'$data->count_lots',
        ),
        array(
            'class' => 'bootstrap.widgets.TbToggleColumn',
            'toggleAction' => '/user/user/toggle',
            'name' => 'certified',
            'filter' => false,
            'sortable' => false,
        ),
        array(
            'class' => 'bootstrap.widgets.TbToggleColumn',
            'toggleAction' => '/user/user/ban',
            'name' => 'ban',
            'filter' => false,
            'sortable' => false,
        ),
        array(
            'htmlOptions' => array('nowrap' => 'nowrap'),
            'class' => 'ex-bootstrap.widgets.ETbButtonColumn',
            'template' => '{update}',
            'afterDelete' => 'function(link,success,data){
                            data =  $.parseJSON(data);

                        }
                        }',
        //'deleteButtonVisible' => '(Yii::app()->user->checkAccess("deleteUser",array("user_id"=>$data->id_admin)))',
        )
    )
));
?>