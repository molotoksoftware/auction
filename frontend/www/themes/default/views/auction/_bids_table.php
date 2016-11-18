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

    if ($user['nick']) {
    $name_view=$user['nick'];
    } else {
    $name_view=$user['login'];
    }

    $str .= '<a href="' . Yii::app()->createUrl(
        '/'.$user['login']
    ) . '">' . $name_view . '</a>';

    $str .= ' <span class="span_love">(' . $user['rating'] . ')</span>';


    return $str;
}

?>

<?php
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
        'enableSorting' => false,
        'itemsCssClass' => 'table table-hover margint_top',
        'template' => "{items}",
        'dataProvider' => $dataProvider,
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
            ),
            array(
                'header' => Yii::t('basic', 'Delete'),
                'type' => 'raw',
                'value' => 'CHtml::link("'.Yii::t('basic', 'Delete').'", "javascript:void(0);", array("data-id" => $data["bid_id"], "class" => "delete-icon remove-bid"))'
            )
        )
    )
);
?>
