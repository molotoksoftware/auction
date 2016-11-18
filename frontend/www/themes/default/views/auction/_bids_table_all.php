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


$sql = Yii::app()->db->createCommand()
    ->from('bids b')
    ->select('b.*, u.login')
    ->join('users u', 'b.owner=u.user_id')
    ->where('b.lot_id=:lot_id ORDER BY created DESC, bid_id DESC');

$dataProvider = new CSqlDataProvider($sql, array(
    'keyField' => 'bid_id',
    'pagination' => array(
        'pageSize' => 300
    ),
    'params' => array(
        ':lot_id' => $auction_id
    )
));

function userBar($id)
{

    $user = Yii::app()->db->createCommand()
        ->select('pro, rating, login, nick, online')
        ->from('users')
        ->where('user_id=:user_id', array(':user_id' => $id))
        ->queryRow();

    $str = null;

    $out_login = $user['nick'] ? $user['nick'] : $user['login'];
    mb_internal_encoding("UTF-8");
    $out_login = mb_substr($out_login, 0, 1).'******'.mb_substr($out_login, mb_strlen($out_login) - 1, mb_strlen($out_login));
    $str .= $out_login;


    $str .= ' <span class="span_love">(' . $user['rating'] . ')</span>';


    return $str;
}
?>



<?php 
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
        'enableSorting' => false,
        //'itemsCssClass' => 'table-normal table-hover-row',
        'template' => "{items}",
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-hover margint_top',
        'columns' => array(
            array(
                'header' => Yii::t('basic', 'Nick'),
                'name' => 'user',
                'type' => 'raw',
                'value' => 'userBar($data["owner"])'
            ),
            array(
                'header' => Yii::t('basic', 'Bid'),
                'name' => 'bid',
                'type' => 'raw',
                'value' => 'PriceHelper::formate($data["price"])',
                'htmlOptions'=>array('class'=>'stavka')
            ),
            array(
                'header' => Yii::t('basic', 'Date'),
                'name' => 'created',
                'htmlOptions'=>array('class'=>'day'),
                'value' => 'Yii::app()->dateFormatter->format("dd.MM.yyyy H:mm:ss", $data["created"])'
            )
        )
    )
); 

