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


class BidAR extends CActiveRecord
{
    public function tableName()
    {
        return 'bids';
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
            array('owner, lot_id, price', 'required'),
            array('owner, lot_id, price, st_notify', 'numerical', 'integerOnly' => true)
        );
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
