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

/**
 * Remove bid
 */
class RemoveBidAction extends CAction
{
    public function run()
    {
        $bid_id = Yii::app()->request->getParam('bid_id', null);
        $bid = Yii::app()->db->createCommand()->select('*')->from('bids')->where('bid_id = :bid_id', array(':bid_id' => $bid_id))->queryRow();

        if ($bid) {
            $lot = Auction::model()->findByPk($bid['lot_id']);

            if ($lot->owner == Yii::app()->user->id) {
                if (Bid::remove($bid['bid_id'])) {
                    AutoBid::model()->deleteAllByAttributes(array('auction_id' => $bid['lot_id'], 'user_id' => $bid['owner']));
                    echo '{"result": "ok"}';
                } else {
                    echo '{"result": "error"}';
                }
            }
        } else {
            echo '{"result": "error"}';
        }
    }
}
