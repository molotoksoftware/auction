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
 * Class AuctionHelper
 */
class AuctionHelper
{
    /**
     * @param mixed $auction Auction или array.
     *
     * @return string
     */
    public static function getSocialImageUrl($auction)
    {
        $url = Yii::app()->params["siteUrl"] . '/img/logo.png';
        if (!empty($auction['image'])) {
            $url = Yii::app()->params["siteUrl"] . '/i2/' . $auction['owner'] . '/thumbs/large_' . $auction['image'];
        }

        return $url;
    }

    /**
     * @param array     $auctionIds
     * @param bool|true $groupByAuctionId
     *
     * @return array|ImageAR[]
     */
    public static function getImagesByIds($auctionIds, $groupByAuctionId = true)
    {
        if (empty($auctionIds)) {
            return [];
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('item_id', $auctionIds);
        /** @var ImageAR[] $images */
        $images = ImageAR::model()->sort()->findAll($criteria);

        if (!$groupByAuctionId) {
            $result = $images;
        } else {
            $result = [];
            foreach ($images as $image) {
                if (!isset($result[$image->item_id])) {
                    $result[$image->item_id] = [];
                }
                $result[$image->item_id][] = $image;
            }
        }

        return $result;
    }

    /**
     * @param array|Auction $auction
     * @param string        $imageName
     * @param string        $prefix
     *
     * @return string
     */
    public static function getImageURL($auction, $imageName, $prefix = '')
    {
        $host = Yii::app()->request->getHostInfo();
        $uri = ImageAR::getImageURI(
            $auction['owner'],
            true,
            $prefix . '_' . $imageName
        );
        return $host . $uri;
    }
}