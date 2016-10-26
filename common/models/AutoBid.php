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

class AutoBid extends CActiveRecord
{
    public function tableName()
    {
        return 'autobids';
    }

    public function rules()
    {
        return array(
            array('auction_id, price, user_id', 'required'),
            array('auction_id, user_id', 'numerical', 'integerOnly' => true),
            array('price', 'numerical', 'numberPattern'=>'/^[0-9]{1,9}(\.[0-9]{1,2})?$/')
        );
    }

    public function behaviors()
    {
        return array(
        );
    }

    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'auction' => array(self::BELONGS_TO, 'Auction', 'auction_id')
        );
    }

    public function attributeLabels()
    {
        return array(
        );
    }

    public function search()
    {
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function setMaxPrice($auction_id, $user_id, $max_price) {
        AutoBid::model()->deleteAllByAttributes(array('auction_id' => $auction_id, 'user_id' => $user_id));
        $autobid = new AutoBid;
        $autobid->auction_id = $auction_id;
        $autobid->user_id = $user_id;
        $autobid->price = $max_price;

        return $autobid->save();
    }

    /*
     * Возвращает текущую цену, которую можно поставить за аукцион
     */
    public static function calcAuctionPrice($auction_id) {
        $auction = Auction::model()->findByPk($auction_id);
        $price = $auction->starting_price;

        if($auction->current_bid) {
            $bid = BidAR::model()->findByPk($auction->current_bid);
            $price = $bid->price;
        } else return $price;

        $price = $price + round($price * Yii::app()->params['minStepRatePercentage'] / 100, 2);
        return $price;
    }

    public static function getAuctionLeader($auction_id) {
        return AutoBid::model()->find('auction_id=:auction_id ORDER BY price DESC', array(':auction_id' => $auction_id));
    }
}
