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

class TableItem
{
    /**
     * @param       $item
     * @param array $params
     *
     * @return string
     */
    public static function getTovarField($item, $params = array())
    {

        return '<div class="fl_l">' . Item::getPreview($item, array('width' => 106, 'height' => 106, 'class' => 'img-thumbnail')) . '</div>' .
            '<div class="fl_r">' . Item::getLink($item) . self::getAuctionNumber($item) . '</div>';
    }

    public static function getTimeLestFieldActlLotTables($item, $params = [])
    {
        $time_row = (Item::getTimeLeftSimple($item) == '<span>'.Yii::t('basic', 'Ended').'</span>')
                ? '<i style="color:red">'.Yii::t('basic', 'Ended').'</i>'
                : Item::getTimeLeftSimple($item);
        $res = "<div>" . $time_row . '</div>';

        if (!empty($params['showPeriod'])) {
            if (!empty($item['created'])) {
                $res .= '<div style="color: gray; font-size: 80%;">';
                $res .= Yii::t('basic', 'from').' ' . date('d.m.Y H:i', strtotime($item['created']));
                if (!empty($item['bidding_date'])) {
                    $res .= '<br />'.Yii::t('basic', 'to').' ' . date('d.m.Y H:i', strtotime($item['bidding_date']));
                }
                $res .= '</div>';
            }
        }

        $res .= '<a href="javascript:void(0);" class="active_items_autorepub'.($item['is_auto_republish'] ? ' active' : '').'" data-id="'.$item['auction_id'].'"></a>';

        return $res;
    }

    /**
     * @param       $item
     * @param array $params
     *
     * @return string
     */
    public static function getTovarFieldActlLotTables($item, $params = array())
    {
        $quantityAndSold = !empty($params['showQuantityAndSold']) ? self::getAuctionQuantityAndSold($item) : '';

        $res =  '<div class="fl_l">' . Item::getPreview($item, array('width' => 106, 'height' => 106)) . '</div>' .
                '<div class="fl_r">' . Item::getLink($item) . self::getAuctionNumber($item) . $quantityAndSold .
                '</div>';

        return $res;
    }

    public static function getDateField($item)
    {
        return date('d.m.Y', strtotime($item['date']));
    }

    public static function getPriceField($price, $params = array())
    {
        $result = '<span>' . Item::getPriceFormat($price) . '</span>';
        return $result;
    }

    public static function getReviewBuyerForShopping($item)
    {

        if ($item['review_about_my_buyer'] > 0) {
            return '<span title="'.Yii::t('basic', 'You left feedback about the seller').'" class="rew_yes_no up_active"></span>';
        } else {
            return '<span title="'.Yii::t('basic', 'Feedback needed').'" class="rew_yes_no up_noactive"></span>';
        }
    }

    public static function getReviewBuyerForSales($item)
    {

        if ($item['review_about_my_buyer'] > 0) {
            return '<span title="'.Yii::t('basic', 'Feedback received').'" class="rew_yes_no down_active"></span>';
        } else {
            return '<span title="'.Yii::t('basic', 'No feedback received').'" class="rew_yes_no down_noactive"></span>';
        }
    }


    public static function getMyReviewBySallerForSales($item)
    {
        if ($item['review_my_about_saller'] > 0) {
            return '<span title="'.Yii::t('basic', 'You left feedback about the buyer').'" class="rew_yes_no up_active"></span>';
        } else {
            return '<span title="'.Yii::t('basic', 'Feedback needed').'" class="rew_yes_no up_noactive"></span>';
        }
    }

    public static function getMyReviewBySallerShopping($item)
    {
         if ($item['review_my_about_saller'] > 0) {
            return '<span title="'.Yii::t('basic', 'Feedback received').'" class="rew_yes_no down_active"></span>';
        } else {
            return '<span title="'.Yii::t('basic', 'No feedback received').'" class="rew_yes_no down_noactive"></span>';
        }

    }

    /**
     * @param $item
     *
     * @return string
     */
    public static function getAuctionNumber($item)
    {
        $html = '';
        if (!empty($item['auction_id'])) {
            $html = "<div class='grid_lot_number'>".Yii::t('basic', 'Item')." # {$item['auction_id']}</div>";
        }
        return $html;
    }

    public static function getAuctionLatestBid($item)
    {
        $html = '';
        if (!empty($item['current_bid'])) {
            $html = '<div>'.Yii::t('basic', 'Last bid').': ' . Item::getPriceFormat($item['current_price_bid']) . '</div>';
        }
        return $html;
    }

    public static function getAuctionBidCount($item)
    {
        $html = '';
        if (!empty($item['bid_count'])) {
            $html = $item['bid_count'];
        } else {
            $html = 0;
        }
        return $html;
    }

    public static function getAuctionBidLeaderLink($item)
    {
        $html = '';
        if (!empty($item['bid_count']) && (!empty($item['bid_leader_login']) || !empty($item['bid_leader_nick']))) {
            $text = !empty($item['bid_leader_nick']) ? $item['bid_leader_nick'] : $item['bid_leader_login'];
            $html = '<br><br><span style="font-size:80%;color:gray;">'.Yii::t('basic', 'Leader').':<Br>' . CHtml::link($text, '/' . $item['bid_leader_login']) . '</span>';
        }
        return $html;
    }

    public static function getAuctionQuantityAndSold($item)
    {
        $html = '';
        if (!isset($item['quantity']) || !isset($item['quantity_sold'])) {
            return $html;
        }
        if ($item['quantity'] > 1 || $item['quantity_sold'] > 0) {
            $html .= "<div class='grid_lot_quantity'>".Yii::t('basic', 'Quantity').": {$item['quantity']}</div>";
            if ($item['quantity_sold'] > 0) {
                $html .= "<div class='grid_lot_sold_quantity'>".Yii::t('basic', 'Quantity sold').": {$item['quantity_sold']}</div>";
            }
        }
        return $html;
    }


    /**
     * @param array $user
     * @param string $scope
     *
     * @return mixed
     * @see UserInfo
     */
    public static function getUserInfo($user, $scope)
    {
        return Yii::app()->getController()->widget('frontend.widgets.user.UserInfo',
            ['userArr' => $user, 'scope' => $scope],
            true
        );
    }
}
