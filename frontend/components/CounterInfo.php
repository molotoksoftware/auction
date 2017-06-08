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


Yii::import('frontend.components.counterEvent.CounterEvent');
Yii::import('frontend.components.counterEvent.types.*');

class CounterInfo
{
    private static $quantity_active_bids;
    private static $quantity_completed_items;
    private static $quantity_active_lots;
    private static $quantity_history_shopping;
    private static $quantity_sold_items;
    private static $quantity_fav_items;
    private static $quantity_no_won_items;
    private static $quantity_my_otsl;
    private static $quantity_questions_for_me;
    private static $quantity_reviews;

    public static function quantityCompletedItems()
    {
        $params = array(
            ':owner' => Yii::app()->user->id,
            ':status' => Auction::ST_COMPLETED_EXPR_DATE
        );

        if (empty(self::$quantity_completed_items)) {
            self::$quantity_completed_items = Yii::app()->db->createCommand()
                ->select('COUNT(*)')
                ->from('auction a')
                ->where('a.status=:status and owner=:owner')
                ->queryScalar($params);
        }
        if (self::$quantity_completed_items == false) {
            return 0;
        }
        return self::$quantity_completed_items;
    }



    public static function quantityFavItems()
    {
        if (empty(self::$quantity_fav_items)) {
            self::$quantity_fav_items = Yii::app()->db->createCommand()
                ->select('count(*)')
                ->from('favorites f')
                ->where('f.user_id=:user_id and f.type=:type')
                ->queryScalar(
                    array(
                        ':user_id' => Yii::app()->user->id,
                        ':type' => 1
                    )
                );
        }
        if (self::$quantity_fav_items == false) {
            return 0;
        }
        return self::$quantity_fav_items;
    }

    public static function quantityOtslItems()
    {
        if (empty(self::$quantity_my_otsl)) {
            self::$quantity_my_otsl = Yii::app()->db->createCommand()
                ->select('count(*)')
                ->from('track_owners f')
                ->where('f.id_user=:id_user ')
                ->queryScalar(
                    array(
                        ':id_user' => Yii::app()->user->id
                    )
                );
        }
        if (self::$quantity_my_otsl == false) {
            return 0;
        }
        return self::$quantity_my_otsl;
    }

    public static function quantityQuestionsForMe()
    {
        $params = array(
            ':status' => Questions::STATUS_ACTIVE,
            ':owner_id' => Yii::app()->user->id,
            ':read' => Questions::UNREAD_STATUS,
        );

            $questions = Yii::app()->db->createCommand()
                ->select('COUNT(*)')
                ->from('questions')
                ->where('status=:status AND owner_id=:owner_id AND `read`=:read', [':status' => Questions::STATUS_ACTIVE,
            ':owner_id' => Yii::app()->user->id,
            ':read' => Questions::UNREAD_STATUS,])
                ->queryScalar();

            self::$quantity_questions_for_me = $questions;

        return (self::$quantity_questions_for_me == false) ? 0 : self::$quantity_questions_for_me;
    }


    public static function quantityActiveBets($user_id)
    {
        $params = array(
            ':status' => Auction::ST_ACTIVE,
            ':owner' => $user_id
        );

        if (empty(self::$quantity_active_bids)) {

            $auctions = Yii::app()->db->createCommand()
                ->select('COUNT(*)')
                ->from('auction a')
                ->join('bids bid', 'bid.owner=:owner and bid.lot_id=a.auction_id')
                ->where('a.status=:status')
                ->group('a.auction_id')
                ->query($params);

            self::$quantity_active_bids = $auctions->rowCount;
        }
        return (self::$quantity_active_bids == false) ? 0 : self::$quantity_active_bids;
    }

    public static function quantityActiveLots($categoryIds = array(), $searchAuction = '')
    {
        $params = array(
            ':owner' => Yii::app()->user->id,
            ':status' => Auction::ST_ACTIVE,
        );

        if (empty(self::$quantity_active_lots)) {
            $dbCommand = Yii::app()->db->createCommand()
                ->select('COUNT(*)')
                ->from('auction a')
                // ->join('bids b', 'b.bid_id=a.current_bid')
                ->where('a.status=:status and a.owner=:owner');
            if (!empty($categoryIds)) {
                $dbCommand->andWhere(array('in', 'a.category_id', $categoryIds));
            }
            if (!empty($searchAuction)) {
                $dbCommand->andWhere('a.name LIKE :name', [':name' => '%' . $searchAuction . '%']);
            }
            self::$quantity_active_lots = $dbCommand->queryScalar($params);
        }
        return (self::$quantity_active_lots == false) ? 0 : self::$quantity_active_lots;
    }

    public static function quantityHistoryShopping($withFilters = true)
    {
        $params = array(
            ':buyer' => Yii::app()->user->id
        );

        if (empty(self::$quantity_history_shopping)) {
            $command = Yii::app()->db->createCommand()
                ->select('COUNT(*)')
                ->from('auction a')
                ->join('sales s', 's.item_id=a.auction_id and s.buyer=:buyer')
                ->where('s.del_status_buyer=0');

            if ($withFilters) {
                $search = GridLotFilter::getSearchQuery();
                if (strlen($search)) {
                    $command->andWhere('a.name LIKE :name', [':name' => '%' . $search . '%']);
                }

                GridLotFilter::appendQueryToCommand($command);

                self::applyFilterSeller($command);
            }


            self::$quantity_history_shopping = $command->queryScalar($params);

        }
        return (self::$quantity_history_shopping == false) ? 0 : self::$quantity_history_shopping;
    }

    public static function quantitySoldItems($withFilters = true)
    {
        $params = [
            ':owner' => Yii::app()->user->id,
        ];
        $result = self::$quantity_sold_items;
        if (empty($result)) {
            $command = Yii::app()->db->createCommand()
                ->select('COUNT(*)')
                ->from('auction a')
                ->join('sales s', 's.item_id=a.auction_id')
                ->where('a.owner=:owner AND s.del_status=0');

            if ($withFilters) {
                $search = GridLotFilter::getSearchQuery();
                if (strlen($search)) {
                    $command->andWhere('a.name LIKE :name', [':name' => '%' . $search . '%']);
                }

                GridLotFilter::appendQueryToCommand($command);

                self::applyFilterBuyer($command);
            }

            $result = $command->queryScalar($params);
            if (!$withFilters) {
                self::$quantity_sold_items = $result;
            }
        }
        return $result;
    }

    public static function quantityNoWonItems()
    {
        $params = array(
            ':owner' => Yii::app()->user->id,
            ':buyer' => Yii::app()->user->id,
            ':status_1' => Auction::ST_SOLD_BLITZ,
            ':status_2' => Auction::ST_COMPLETED_SALE
        );

        if (empty(self::$quantity_no_won_items)) {
            $query = "
                SELECT COUNT(auction_id)
                FROM (
                    SELECT a.auction_id
                    FROM `bids` `b`
                    JOIN `auction` `a` ON b.lot_id = a.auction_id
                    JOIN `sales` `s` ON s.sale_id = a.sales_id and s.buyer <> :buyer
                    WHERE
                    (b.owner = :owner)
                    AND (a.status = :status_1 or a.status = :status_2)
                    AND (s.date >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') - INTERVAL 1 MONTH)
                    GROUP BY `a`.`auction_id`
                ) bids
            ";

            self::$quantity_no_won_items = Yii::app()
                ->getDb()
                ->createCommand($query)
                ->queryScalar($params);
        }

        return (self::$quantity_no_won_items == false) ? 0 : self::$quantity_no_won_items;
    }

    public static function applyFilterBuyer(CDbCommand $command, &$params = null)
    {
        $buyerId = Yii::app()->getRequest()->getQuery('buyer');
        if (is_numeric($buyerId) && $buyerId > 0) {
            $command->andWhere(
                's.buyer = :buyer',
                $params === null ? [':buyer' => $buyerId] : []
            );
            if (null !== $params) {
                $params[':buyer'] = $buyerId;
            }
        }
    }

    public static function applyFilterSeller(CDbCommand $command, &$params = null)
    {
        $sellerId = Yii::app()->getRequest()->getQuery('seller');
        if (is_numeric($sellerId) && $sellerId > 0) {
            $command->andWhere(
                'a.owner= :seller_id',
                $params === null ? [':seller_id' => $sellerId] : []
            );
            if (null !== $params) {
                $params[':seller_id'] = $sellerId;
            }
        }
    }

    public static function quantityReviews ($role)
    {
        $params = array(
            ':user' => Yii::app()->user->id,
        );

        $query =Yii::app()->getDb()
            ->createCommand()
            ->select('COUNT(*)')
            ->from('reviews');

        if ($role == 'to_me') {
            $query->where('user_to = :user', $params);
        } elseif ($role == 'from_me') {
            $query->where('user_from = :user', $params);
        }

        self::$quantity_reviews = $query->queryScalar();

        return (self::$quantity_reviews == false) ? 0 : self::$quantity_reviews;
    }
}
