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

class Item
{

    public static function getPriceFormat($value)
    {
        return PriceHelper::formate($value);
    }

    public static function getLink($item, $htmlOptions = array())
    {
        $link = array('/auction/view', 'id' => $item['auction_id']);
        return CHtml::link($item['name'], $link, $htmlOptions);
    }

    public static function getName($item, $className = '', $limiter = 500)
    {
        $class = 'link_title';

        $name = '<h4 class="' . $className . '">' . Text::characterLimiter($item['name'], $limiter) . '</h4>';
        $link = array('/auction/view', 'id' => $item['auction_id']);

        return CHtml::link($name, $link, array('class' => $class, 'title' => $item['name']));
    }

    /**
     * @param string $item
     * @param array  $htmlOptions
     * @param string $prefix
     * @param array  $params
     *  - false|array setDataImages
     *
     * @return string
     */
    public static function getPreview($item, $htmlOptions = [], $prefix = 'prv', $params = [])
    {
        $htmlOptions = \Yiinitializr\Helpers\ArrayX::merge([
            'class' => '',
        ], $htmlOptions);

        $params = \Yiinitializr\Helpers\ArrayX::merge([
            'setDataImages' => false,
        ], $params);

        if (empty($item['image'])) {
            $info = Auction::$versions[$prefix]['cresize'];
            $w = $info['width'];
            $h = $info['height'];
            $imageSrc = 'http://placehold.it/' . $w . 'x' . $h . '/ffffff/ffffff';
            $htmlOptions = ['style' => 'opacity: 0.1;'];
        } else {
            $path = Yii::app()->request->getHostInfo();
            $path .= ImageAR::getImageURI($item['owner'], true, $prefix . '_' . $item['image']);
            $htmlOptions['data-original'] = $path;

            $imageSrc = AuctionHelper::getImageURL($item, $item['image'], $prefix);

            if (is_array($params['setDataImages']) && count($params['setDataImages']) > 0) {

                $imagesCount = isset($params['setDataImages']) ? count($params['setDataImages']) : 0;
                $htmlOptions['data-auction_id'] = $item['auction_id'];
                $htmlOptions['data-images_count'] = $imagesCount;
                if ($imagesCount > 0) {
                    $htmlOptions['class'] .= ' js-popup-slider';
                }

                if ($imagesCount) {
                    $counter = 1;
                    /** @var ImageAR $image */
                    foreach ($params['setDataImages'] as $image) {
                        $imageUrl = AuctionHelper::getImageURL($item, $image->image, 'large');
                        $htmlOptions['data-image-' . $counter++] = $imageUrl;
                    }
                }
            }
        }
        return CHtml::image($imageSrc, $item['name'], $htmlOptions);
    }

    public static function getLocationName($item)
    {

    }

    /**
     * @param        $item
     * @param string $separator
     * @param array  $before
     * @param array  $options
     * - bool only_last
     *
     * @return string
     */
    public static function getBreadcrumbs($item, $separator = ' / ', $before = [], $options = [])
    {
        $result = [];
        if (!empty($before)) {
            array_unshift($result, $before);
        }
        $breadcrumbs = Category::getAncestorCategoryByBreadcrumbs($item['category_id'], $options);

        if (!empty($options['only_last'])) {
            end($breadcrumbs);
            $lastKey = key($breadcrumbs);
            $breadcrumbs = [$lastKey => $breadcrumbs[$lastKey]];
        }

        foreach ($breadcrumbs as $name => $url) {
            $result[] = CHtml::link($name, $url);
        }

        return implode($separator, $result);
    }

    public static function getTimeLeftSimple($item)
    {
        $dateTime = '';

        if ($item['status'] != Auction::ST_ACTIVE) {
            $dateTime = '<span>'.Yii::t('basic', 'Ended').'</span>';
        }

        /**
         * @var DateTime
         */
        $date = new DateTime('now');
        $date_end = new DateTime($item['bidding_date']);
        $interval = $date->diff($date_end);

        if ($interval->format('%R') == '-') {
            $dateTime = '<span>'.Yii::t('basic', 'Ended').'</span>';
        } else {
            $days = '';
            if ($interval->format('%a') > 0) {
                $days = $interval->format('%a') . ' ' . Yii::t(
                    'app',
                    'day|days',
                    $interval->format('%a')
                ) . '';

                $time = $interval->format('%H:%I');
            } else {
                $time = '<span style="font-size: 14px;">'.$interval->format('%H:%I:%S').'</span>';
            }

            $dateTime = $days . ' ' . $time;
        }


        return $dateTime;
    }


    public static function getTimeLeft($item)
    {
        return Item::getTimeLeftSimple($item);
    }

    public static function getPrice($item, $format = true)
    {
        $result = '';
        if ($item['type'] == BaseAuction::TYPE_AUCTION) {
            $s_p = $item['starting_price'];
            if (!is_null($item['current_bid'])) {
                $s_p = $item['current_bid'];
            }

            if ($s_p > 0) {
                $result = $s_p;
            } else {
                if (!empty($item['price']) && $item['price'] > 0) {
                    $result = floatval($item['price']);
                }
            }
        }

        if ($format) {
            $result = number_format($result, 0, ' ', ' ');
        }

        return $result;
    }

    public static function getStaticPriceValue($item, $format = true)
    {
        $result = 0;
        if ($item['type'] == BaseAuction::TYPE_AUCTION) {
            $s_p = $item['starting_price'];
            if (!is_null($item['current_bid'])) {
                $s_p = $item['current_bid'];
            }

            if ($s_p > 0) {
                $result = $s_p;
            } else {
                if (!empty($item['price']) && $item['price'] > 0) {
                    $result = floatval($item['price']);
                }
            }
        }

        if ($format) {
            $result = number_format($result, 0, ' ', ' ');
        }

        return $result;
    }


    public static function getPriceBlock($item)
    {
        $result = '';

            $s_p = $item['starting_price'];
            if (!is_null($item['current_bid'])) {
                $s_p = $item['current_bid'];
            }


            if ($s_p > 0) {
                $result .= CHtml::tag(
                    'span',
                    [
                        'class' => 'price_1 ',
                        'title' => Yii::t('basic', 'Current price'),
                    ],
                    PriceHelper::formate($s_p)
                );
            }

            if (!empty($item['price']) && $item['price'] > 0) {
                $class = [];
                if (empty($result)) {
                    $class[] = 'standart-auction';
                }
                $class = implode(' ', $class);
                $price = PriceHelper::formate($item['price']);
                $result .= "\n" . '<span class="' . $class . ' price_2"><span title="'.Yii::t('basic', 'Buy now').'"><nobr>' . $price . "</nobr></span></span>";
            }

        return $result;
    }

    /**
     * @param array $item
     * @param array $htmlOptions
     *
     * @return string
     */
    public static function getBidsBlock($item, $htmlOptions = [])
    {
        $htmlOptions = \Yiinitializr\Helpers\ArrayX::merge([
            'class' => 'label label-default bids'
        ], $htmlOptions);

        $html = '';
        if ($item['type'] == BaseAuction::TYPE_AUCTION && $item['current_bid'] != 0 && $item['bid_count'] != 0) {
            $html = CHtml::tag('span', $htmlOptions, Yii::t('app', '{n} bid|{n} bids|{n} bids|{n} bids', $item['bid_count']));
        }
        return $html;
    }

    public static function spanEnvelopment($value)
    {
        return '<span>'.$value.'</span>';
    }

    public static function issetItemsForCategory($category, $type, $itemId)
    {


        $count = Yii::app()->db->createCommand()
            ->select('count(*)')
            ->from('auction')
            ->where(
                'status=:status and category_id=:category_id',
                array(
                    ':status' => Auction::ST_ACTIVE,
                    ':category_id' => $category,
                )
            )
            ->andWhere(array('not in', 'auction_id', array($itemId)))
            ->queryScalar();
        return ($count == false) ? 0 : $count;
    }

    public static function searchHelper($keyword, $search_type_filter, $user = false)
    {
        $db = Yii::app()->db->createCommand();
        $db->select('auction_id, category_id');
        $db->from('auction');
        $db->where('status = 1');

        if (isset($search_type_filter)) {
            if ($search_type_filter == 'default') {
                $db->andWhere('type_transaction = 0');
            }
            if ($search_type_filter == 'buynow') {
                $db->andWhere('type_transaction = 1');
            }
            if ($search_type_filter == 'nulls') {
                $db->andWhere('type_transaction = 2');
            }
        }

        if ($user) {
            $db->andWhere('owner = :owner', [':owner' => $user]);
        }

        $db->andWhere("name LIKE '%$keyword%'");

        return $db->queryAll();
    }
}
