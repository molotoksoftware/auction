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


class Bid extends CModel
{
    public $bid_id;
    public $price;
    public $owner;
    public $created;
    public $lot_id;
    public $lot;

    public $skip_max_valid;
    public $send_active_lots_nf = true;

    public function rules()
    {
        return array(
            array('owner, lot_id, price', 'required'),
            array('owner, lot_id', 'numerical'),
            array('price', 'numerical', 'numberPattern'=>'/^[0-9]{1,9}(\.[0-9]{1,2})?$/'),
            array('lot_id', 'validateLot')
        );
    }

    public function validateLot($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->lot == false) {
                $this->addError($attribute, Yii::t('basic', 'Item is not available for bid'));
            }
            if ($this->lot['type_transaction'] == Auction::TP_TR_SALE) {
                $this->addError($attribute, Yii::t('basic', 'Only Buy now'));
            }
        }
    }

    public function attributeNames()
    {
        return array(
            'bid_id',
            'price',
            'owner',
            'lot_id'
        );
    }

    public function __construct($price, $owner, $lot_id)
    {
        $this->lot_id = $lot_id;
        $this->lot = Auction::model()->findByPk(intval($this->lot_id));
        $this->price = $price;
        $this->owner = $owner;
    }

    public function getCurrentBidForLot()
    {
        $bid = Yii::app()->db->createCommand()
            ->select('max(price) as max_price, owner')
            ->from('bids')
            ->where('lot_id=:lot_id', array(':lot_id' => $this->lot_id))
            ->group('lot_id')
            ->queryRow();


        return $bid;
    }

    public function onBeforeBid(CEvent $event)
    {
        $this->raiseEvent('onBeforeBid', $event);
    }

    public function onAfterBid(CEvent $event)
    {
        $this->lot->bid_count++;

        if (!$this->lot->save()) {
            Yii::log(sprintf(
                '"onAfterBid" Cant save lot model, errors: %s, attrs: %s',
                $this->lot->errors,
                $this->lot->attributes
            ), CLogger::LEVEL_ERROR);
        }

        $this->raiseEvent('onAfterBid', $event);
    }

    protected function saveBid()
    {
        if ($this->hasEventHandler('onBeforeBid')) {
            $event = new CEvent($this);
            $event->params = array();
            $this->onBeforeBid($event);
        }

        $result = Yii::app()->db->createCommand()
            ->insert(
                'bids',
                array(
                    'price' => $this->price,
                    'owner' => $this->owner,
                    'lot_id' => $this->lot_id,
                    'created' => date('Y-m-d H:i:s')
                )
            );

        if ($result) {
            $bid_id = Yii::app()->db->lastInsertID;

            if ($this->hasEventHandler('onAfterBid')) {
                $event = new CEvent($this);
                $event->params = array(
                    'bid_id' => $bid_id,
                    'lot_id' => $this->lot_id,
                    'lot' => $this->lot,
                    'bidPrice' => $this->price,
                    'owner' => $this->owner,
                    'send_active_lots_nf' => $this->send_active_lots_nf
                );
                $this->onAfterBid($event);
            }
            return $bid_id;
        }

        return $result;
    }

    public function createBid()
    {
        if ($this->validate()) {
            $current_bid = $this->getCurrentBidForLot();
            $owner = User::model()->findByPk($this->lot->owner);
            $user = Yii::app()->user->getModel();

            if ($current_bid) {
                $starting_price = $current_bid['max_price'];
                $step = round($starting_price * Yii::app()->params['minStepRatePercentage'] / 100, 2) > 1
                    ? round($starting_price * Yii::app()->params['minStepRatePercentage'] / 100, 2)
                    : 1;

                if ($this->skip_max_valid || $current_bid['max_price'] + $step <= $this->price) {
                    return $this->saveBid();
                } else {
                    $this->addError('price', Yii::t('basic', 'Your bid ({bid}) should be more then current price + minimal step ({need_bid})',
                        [
                            '{bid}' => PriceHelper::formate(floatval($this->price)),
                            '{need_bid}' => PriceHelper::formate((($current_bid['max_price'] + $step)))
                        ]));
                    return false;
                }
            } else {
                if ($this->lot['starting_price'] <= $this->price) {
                    return $this->saveBid();
                } else {
                    $this->addError('price', Yii::t('basic', 'Your bid should be more than current price or equal'));
                }
            }
        } else {
            return false;
        }
    }

    public static function getMaxBidByLot($idLot)
    {
        $maxBidId = Yii::app()->db->createCommand()
            ->from('bids')
            ->select('bid_id')
            ->where('lot_id=:lot_id', array(':lot_id' => $idLot))
            ->order('price desc')
            ->limit(1)
            ->queryScalar();

        return $maxBidId;
    }


    public static function remove($id)
    {
        $bid = Yii::app()->db->createCommand()
            ->from('bids')
            ->select('lot_id, owner')
            ->where('bid_id=:bid_id', array(':bid_id' => $id))
            ->queryRow();
        
        $autoBidMaxUser = Yii::app()->db->createCommand()
            ->from('autobids')
            ->select('user_id')
            ->where('auction_id=:lot_id', array(':lot_id' => $bid['lot_id']))
            ->order('price desc')
            ->limit(1)
            ->queryScalar();

        if (!empty($bid['lot_id'])) {

            $transaction = Yii::app()->db->beginTransaction();

            try {
                Yii::app()->db->createCommand()
                    ->delete(
                        'bids',
                        'bid_id=:bid_id',
                        array(
                            ':bid_id' => (int)$id
                        )
                    );

                if ($autoBidMaxUser == $bid['owner']) {
                    Yii::app()->db->createCommand()
                    ->delete(
                        'autobids',
                        'user_id=:user_id AND auction_id=:auction_id',
                        array(
                            ':user_id' => $bid['owner'],
                            ':auction_id' => $bid['lot_id'],
                        )
                    );
                }

                $maxBidId = self::getMaxBidByLot($bid['lot_id']);
                $auction = Auction::model()->findByPk($bid['lot_id']);

                $auction->current_bid = $maxBidId;
                $auction->update = time();
                $auction->bid_count = $auction->bid_count - 1;

                $auction->save();
                $transaction->commit();

                return true;

            } catch (Exception $e) {
                $transaction->rollback();
                return false;
            }


        }

    }
}
