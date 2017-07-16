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


class Favorite extends CActiveRecord
{
    public function tableName()
    {
        return 'favorites';
    }

    public function defaultScope()
    {
        return array(
            //'order' => 'order_weight DESC'
        );
    }

    public function rules()
    {
        return array(
            array('item_id, type, user_id', 'required'),
            array('item_id, type, user_id', 'numerical', 'integerOnly' => true)
        );
    }

    public static function hasFavorite($item_id, $type, $user_id) {
        $favorite = Favorite::model()->findByAttributes(array('item_id' => $item_id, 'type' => $type, 'user_id' => $user_id));

        if($favorite) return true;
        return false;
    }

    public static function deleteFavorite($item_id, $type, $user_id) {
        Favorite::model()->deleteAllByAttributes(array('item_id' => $item_id, 'type' => $type, 'user_id' => $user_id));
    }

    /*
     * Создает запись "Избранные". подсчет количества в аукционах реализован за счет триггеров
     */
    public static function createFavorite($item_id, $type, $user_id) {
        $f = Yii::app()->db->createCommand()
            ->insert(
                'favorites',
                array(
                    'item_id' => $item_id,
                    'type' => $type,
                    'created' => date('Y-m-d H:i:s', time()),
                    'user_id' => $user_id
                )
            );

        return $f;
    }

    public function relations()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array();
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
