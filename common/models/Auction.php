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

class Auction extends BaseAuction
{
    // duration
    const ONE_DAY = 1;
    const TWO_DAY = 2;
    const THREE_DAY = 3;
    const FIVE_DAY = 4;
    const WEEK = 5;
    const TEN_DAY = 6;
    const TWO_WEEK = 7;
    const THREE_WEEK = 8;

    //type_transaction
    const TP_TR_STANDART = 0; // Standart
    const TP_TR_SALE = 1; // Buy now
    const TP_TR_START_ONE = 2; // From 1

    //status
    const ST_SOLD_BLITZ = 2; // Sold for buy now price
    const ST_COMPLETED_SALE = 3; // Sold winner.

    static $versions = array(
        'big' => array(
            'cresize' => array(
                'width' => 1280,
                'height' => 1024,
            ),
        ),
        'large' => array(
            'cresize' => array(
                'width' => 500,
                'height' => 500,
            ),
        ),
        'medium' => array(
            'resize' => array(
                'width' => 250,
                'height' => 250,
            ),
        ),
        'prv' => array(
            'cresize' => array(
                'width' => 150,
                'height' => 150,
            ),
        ),
    );

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return CMap::mergeArray(
            parent::rules(),
            array(
                array('quantity', 'numerical', 'max' => 999),
                array('is_auto_republish', 'boolean'),
                array('type_transaction', 'in', 'range' => $this->getTransactionList()),
                array('price', 'numerical', 'message' => 'Укажите цену'),
                array('bid_count', 'numerical'),

                array('image_count', 'filter', 'filter' => array($obj = new CHtmlPurifier(), 'purify'))
            )
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            array(
                'quantity' => Yii::t('basic', 'Items quantity')
            )
        );
    }

    /**
     * *************************************************************************
     * API
     * *************************************************************************
     */
    public static function getDurationList()
    {
        return array(
            self::ONE_DAY => Yii::t('basic', '1 day'),
            self::TWO_DAY => Yii::t('basic', '2 days'),
            self::THREE_DAY => Yii::t('basic', '3 days'),
            self::FIVE_DAY => Yii::t('basic', '5 days'),
            self::WEEK => Yii::t('basic', '1 week'),
            self::TEN_DAY => Yii::t('basic', '10 days'),
            self::TWO_WEEK => Yii::t('basic', '2 weeks'),
            self::THREE_WEEK => Yii::t('basic', '3 weeks'),
        );
    }

    public static function getTransactionList()
    {
        return array(
            self::TP_TR_SALE,
            self::TP_TR_START_ONE,
            self::TP_TR_STANDART,
        );
    }

    protected function afterSave()
    {
        if ($this->status == 10) {
            /** @var ImageAR[] $images */
            $images = ImageAR::model()->findAllByAttributes(['item_id' => $this->auction_id]);
            if (count($images)) {
                $name = basename(ImageAR::getImageSavePath($this->owner, false, $this->image));
                $splitName = explode(".", $name);
                if (isset($splitName[0])) {
                    $dir = dirname(ImageAR::getImageSavePath($this->owner, true, $this->image));
                    foreach (glob($dir . "/*" . $splitName[0] . "*") as $thumb) {
                        if (is_file($thumb)) {
                            unlink($thumb);
                        }
                    }
                    $dir = dirname(ImageAR::getImageSavePath($this->owner, false, $this->image));
                    foreach (glob($dir . "/*" . $splitName[0] . "*") as $i2) {
                        if (is_file($i2)) {
                            unlink($i2);
                        }
                    }
                }

                // Удаляем все миниатюры.
                foreach ($images as $eachImage) {
                    $eachImage->deleteAndUnlink($this->owner);
                }
            }
        }
        parent::afterSave();
    }


    public function afterDelete()
    {

        $images = ImageAR::model()->findAllByAttributes(['item_id' => $this->auction_id]);
        if (count($images)) {

            $name = basename(ImageAR::getImageSavePath($this->owner, false, $this->image));
            $splitName = explode(".",$name);
            if(isset($splitName[0])) {
                $dir = dirname(ImageAR::getImageSavePath($this->owner, false, $this->image));
                foreach (glob($dir . "/*" . $splitName[0] . "*") as $img) {
                    if (file_exists($img))
                        @unlink($img);
                }
                $dir = dirname(ImageAR::getImageSavePath($this->owner, true, $this->image));
                foreach (glob($dir . "/*" . $splitName[0] . "*") as $thumb) {
                    if (file_exists($thumb))
                        @unlink($thumb);
                }
                $dir = dirname(ImageAR::getImageSavePath($this->owner, false, $this->image));
                foreach (glob($dir . "/*" . $splitName[0] . "*") as $i2) {
                    if (file_exists($i2))
                        @unlink($i2);
                }
            }
        }
        parent::afterDelete();
    }

    public function beforeValidate()
    {
        /* Тип аукциона */

        /* С 1 рубля */
        if ($this->type_transaction == Auction::TP_TR_START_ONE) {
            $this->starting_price = 1;
        }

        /* Фиксированная цена */
        if ($this->type_transaction == Auction::TP_TR_SALE) {
            $this->starting_price = 0;
            if (((int)$this->price) <= 0) {
                $this->addError('price', Yii::t('basic', 'Specify price'));
            }
        }


        if ($this->type_transaction == Auction::TP_TR_STANDART) {

            if (!$this->hasErrors()) {
                /* Стандартный */
                if (((float)$this->starting_price) <= 0) {
                    $this->addError('starting_price', Yii::t('basic', 'Specify the starting price'));
                }
            }

            if (!$this->hasErrors()) {
                if (($this->price > 0) && ($this->starting_price > $this->price)) {
                    $this->addError('starting_price', Yii::t('basic', 'Starting price can\'t be more than buy now price'));
                }
            }

            if (!$this->hasErrors()) {
                if (($this->price > 0) && ($this->starting_price == $this->price)) {
                    $this->addError('starting_price', Yii::t('basic', 'Starting price can\'t be equal to the buy now price'));
                }
            }
        }

        return parent::beforeValidate();
    }


    public function afterFind()
    {
        $this->price = floatval($this->price);
        $this->starting_price = floatval($this->starting_price);
        parent::afterFind();
    }

    public static function getDateSpecForDuration($duration)
    {
        $interval_spec = '';
        switch ($duration) {
            case Auction::ONE_DAY:
                $interval_spec = 'P1D';
                break;
            case Auction::TWO_DAY:
                $interval_spec = 'P2D';
                break;
            case Auction::THREE_DAY:
                $interval_spec = 'P3D';
                break;
            case Auction::FIVE_DAY:
                $interval_spec = 'P5D';
                break;
            case Auction::TEN_DAY:
                $interval_spec = 'P10D';
                break;
            case Auction::WEEK:
                $interval_spec = 'P7D';
                break;
            case Auction::TWO_WEEK:
                $interval_spec = 'P14D';
                break;
            case Auction::THREE_WEEK:
                $interval_spec = 'P21D';
                break;
            default:
                $interval_spec = 'P1D';
                break;
        }
        return $interval_spec;
    }

    /**
     *
     * @param type $id auction_id
     * @return boolean
     */
    public static function verifiedLot($id)
    {
        $lot = Yii::app()->db->createCommand()
            ->select()
            ->from('auction')
            ->where(
                'auction_id=:id and status=:status',
                array(
                    ':status' => Auction::ST_ACTIVE,
                    ':id' => $id
                )
            )
            ->andWhere('bidding_date>=:date', array(':date' => date('Y-m-d H:i:s', time())))
            ->queryRow();

        return ($lot == false) ? false : true;
    }

    public static function bidBlitz($id, $buyer, $quantity = 1)
    {
        $lot = Yii::app()->db->createCommand()
            ->select('auction_id, price, owner, current_bid, quantity, quantity_sold')
            ->from('auction')
            ->where('auction_id=:id', array(':id' => $id))
            ->queryRow();

        if ($lot == false) {
            return false;
        }

        $user = User::model()->findByPk($buyer);
        $owner = User::model()->findByPk($lot['owner']);

        $transaction = Yii::app()->db->beginTransaction();

        try {

            Yii::app()->db->createCommand()
                ->insert(
                    'sales',
                    [
                        'item_id'   => $id,
                        'price'     => $lot['price'],
                        'amount'    => ($lot['price'] * $quantity),
                        'quantity'  => $quantity,
                        'buyer'     => $buyer,
                        'date'      => date('Y-m-d H:i:s', time()),
                        'type'      => 1,
                        'seller_id' => $lot['owner'],
                    ]
                );

            $sales_id = Yii::app()->db->lastInsertID;

            //quantity
            if ($lot['quantity'] > 1 && $lot['quantity'] > $quantity) {


                Yii::app()->db->createCommand()
                    ->update(
                        'auction',
                        array(
                            'sales_id' => 0,
                            'quantity' => $lot['quantity'] - $quantity,
                            'quantity_sold' => $lot['quantity_sold'] + $quantity,
                        ),
                        'auction_id=:id',
                        array(':id' => $id)
                    );


                /**
                 * проверить были ли ставки текущем пользователем
                 */
                $bids = Yii::app()->db->createCommand()
                    ->from('bids')
                    ->select('bid_id')
                    ->where(
                        'owner=:owner and lot_id=:lot_id',
                        array(
                            ':owner' => Yii::app()->user->id,
                            ':lot_id' => $id
                        )
                    )
                    ->queryColumn();


                if (!empty($bids)) {
                    Yii::app()->db->createCommand()
                        ->delete('bids', array('in', 'bid_id', $bids));

                    /**
                     * сделать предыдущую ставку активной
                     */
                    $activeBid = Yii::app()->db->createCommand()
                        ->from('bids')
                        ->select('bid_id')
                        ->where(
                            'lot_id=:lot_id',
                            array(
                                ':lot_id' => $id
                            )
                        )
                        ->limit(1)
                        ->order('created DESC')
                        ->queryRow();


                    Yii::app()->db->createCommand()
                        ->update(
                            'auction',
                            array(
                                'current_bid' => (int)$activeBid['bid_id'],
                            ),
                            'auction_id=:id',
                            array(':id' => $id)
                        );

                }


            } else {
                Yii::app()->db->createCommand()
                    ->update(
                        'auction',
                        array(
                            'current_bid' => 0,
                            'sales_id' => $sales_id,
                            'quantity' => $lot['quantity'] - $quantity,
                            'quantity_sold' => $lot['quantity_sold'] + $quantity,
                            'status' => Auction::ST_SOLD_BLITZ
                        ),
                        'auction_id=:id',
                        array(':id' => $id)
                    );
            }


            $transaction->commit();
            return $sales_id;
        } catch (Exception $e) {
            $transaction->rollback();
            return false;
        }
    }

    public static function getStatusList()
    {
        return array(
            self::ST_ACTIVE => Yii::t('basic', 'Active'),
            self::ST_COMPLETED_SALE => Yii::t('basic', 'Sold winner'),
            self::ST_SOLD_BLITZ => Yii::t('basic', 'Sold buynow'),
            self::ST_COMPLETED_EXPR_DATE => Yii::t('basic', 'Completed')
        );
    }

    public function getStatus()
    {
        $statuses = self::getStatusList();
        return (isset($statuses[$this->status])) ? $statuses[$this->status] : '-';
    }


    /**
     * @return boolean
     */
    public function hasBids()
    {
        $count_bids = Yii::app()->db->createCommand()
            ->select('count(bid_id)')
            ->from('bids')
            ->where('lot_id=:lot_id', array(':lot_id' => $this->auction_id))
            ->queryScalar();

        if ($count_bids > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public static function id_validator($id)
    {
        return (preg_match("/^[0-9]+$/", $id));
    }

    public function updateMainImg()
    {
        //cache main image
        $main_img = Yii::app()->db->createCommand()
            ->select('image')
            ->from('images')
            ->order('sort ASC, image_id ASC')
            ->where('item_id=:item_id', array(':item_id' => $this->auction_id))
            ->limit(1)
            ->queryColumn();
        if ($main_img) {
            Yii::app()->db->createCommand()
                ->update(
                    'auction',
                    array(
                        'image' => $main_img[0]
                    ),
                    'auction_id=:auction_id',
                    array(
                        ':auction_id' => $this->auction_id
                    )
                );
        }
    }

    /**
     * @param int $saleId
     *
     * @return int|null
     */
    public static function getSoldPrice($saleId)
    {
        $dbCommand = Yii::app()->getDb()->createCommand();
        $price = $dbCommand
            ->select('price')
            ->from('sales')
            ->where('sale_id = :sale_id', array(':sale_id' => (int)$saleId))
            ->queryScalar();

        return $price;
    }



}