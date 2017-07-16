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


$sql = Yii::app()->db->createCommand()
    ->from('bids b')
    ->select('b.*, u.login')
    ->join('users u', 'b.owner=u.user_id')
  //  ->leftJoin('autobids ab', 'b.owner=ab.user_id')
    ->where('b.lot_id=:lot_id');

$dataProvider = new CSqlDataProvider($sql, array(
    'keyField' => 'bid_id',
    'pagination' => array(
        'pageSize' => 50
    ),
    'params' => array(
        ':lot_id' => $model->auction_id
    )
));

function getLinkUser($id)
{
    return User::model()->findByPk($id)->getLink();
}
?>
<div class="box">
    <div class="box-header">
        <span class="title">Ставки (<?= count($dataProvider->getData()); ?>)</span>
    </div>
    <div class="box-content">
        <?php
        $this->widget(
            'ex-bootstrap.widgets.ETbExtendedGridView',
            array(
                //'type' => 'striped bordered condensed',
                'id' => 'providers-list',
                'enableSorting' => true,
                //'itemsCssClass' => 'table-normal table-hover-row',
                //'template' => "{items}",
                'dataProvider' => $dataProvider,
                'summaryText' => 'Ставки {start}—{end} из {count}.',
                'columns' => array(
                    array(
                        'header' => 'Цена',
                        'name' => 'price',
                        'value' => '$data["price"]'
                    ),
                    array(
                        'header' => 'Дата',
                        'name' => 'created',
                        'value' => 'Yii::app()->dateFormatter->format("dd MMMM yyyy H:mm:ss", $data["created"])'
                    ),
                    array(
                        'header' => 'Учасник',
                        'name' => 'owner',
                        'type' => 'raw',
                        'value' => 'getLinkUser($data["owner"])'
                    ),
                    array(
                        'htmlOptions' => array('nowrap' => 'nowrap'),
                        'header' => '',
                        'class' => 'ex-bootstrap.widgets.ETbButtonColumn',
                        'template' => '{delete}',
                        'deleteButtonUrl' => function ($data) {
                            return Yii::app()->createUrl('/catalog/auction/removeBid', array('id' => $data['bid_id']));
                        },
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
                    ),
                ),

            )
        );
        ?>
    </div>
</div>
