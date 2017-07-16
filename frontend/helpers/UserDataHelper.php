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

class UserDataHelper {

    public static function issetLot ($user_id) 
    {

        $sql = Yii::app()->db
             ->cache(1800)
             ->createCommand()
             ->select('auction_id')
             ->from('auction')
             ->where('owner = :user_id AND status=1', [':user_id' => $user_id])
             ->limit('1')
             ->queryScalar();

        return $sql;
    }

    public static function countLot ($user_id) 
    {

        $sql = Yii::app()->db
             ->cache(3600)
             ->createCommand()
             ->select('COUNT(*)')
             ->from('auction')
             ->where('owner = :user_id AND status=1', [':user_id' => $user_id])
             ->queryScalar();

        return $sql;
    }

    public static function getCountReviews ($user_id) 
    {

        $condition = new CDbCacheDependency('SELECT MAX(`update`) FROM reviews WHERE user_to='.$user_id);

        $sqlPositive = Yii::app()->db
                ->cache(3600, $condition)
                ->createCommand()
                ->select('COUNT(*)')
                ->from('reviews')
                ->where('user_to = :user_id AND value=5', [':user_id' => $user_id])
                ->queryScalar();

        $sqlNegative = Yii::app()->db
                ->cache(3600, $condition)
                ->createCommand()
                ->select('COUNT(*)')
                ->from('reviews')
                ->where('user_to = :user_id AND value=1', [':user_id' => $user_id])
                ->queryScalar();

        $sqlRoleBuyer = Yii::app()->db
                ->cache(3600, $condition)
                ->createCommand()
                ->select('COUNT(*)')
                ->from('reviews')
                ->where('user_to = :user_id AND role=1', [':user_id' => $user_id])
                ->queryScalar();

        $sqlRoleSaller = Yii::app()->db
                ->cache(3600, $condition)
                ->createCommand()
                ->select('COUNT(*)')
                ->from('reviews')
                ->where('user_to = :user_id AND role=2', [':user_id' => $user_id])
                ->queryScalar();

        return $reviews = [
            'positive'   => $sqlPositive,
            'negative'   => $sqlNegative,
            'roleSaller' => $sqlRoleSaller,
            'roleBuyer'  => $sqlRoleBuyer,
        ];

    }

    public static function getSummaryCountReviews ($user_id) 
    {
        $reviews = self::getCountReviews($user_id);
        return $reviews['positive'] + $reviews['negative'];
    }

    public static function getPercentRewiews ($user_id) {

        $reviews = self::getCountReviews($user_id);

        if (!$sumReviews = $reviews['positive'] + $reviews['negative']) {
            $sumReviews = 1;
        }

        $sum = 100*$reviews['positive']/$sumReviews;

        $value = round($sum, 1, PHP_ROUND_HALF_UP);

        return $value;
    }

    public static function getCityCountryUser($city, $country) 
    {

        $sqlCity = Yii::app()->db
                ->cache(18000)
                ->createCommand()
                ->select('name')
                ->from('city')
                ->where('id_city = :id_city', [':id_city' => $city])
                ->queryRow();

        $sqlCountry = Yii::app()->db
                ->cache(18000)
                ->createCommand()
                ->select('name')
                ->from('country')
                ->where('id_country = :id_country', [':id_country' => $country])
                ->queryRow();

        if ($sqlCity['name'] AND $sqlCountry['name']) {
            return $sqlCountry['name'].', '.$sqlCity['name'];
        } else {
            return false;
        }

    }

   public static function getUserData ($userId) 
   {

        return Yii::app()->db->createCommand()
                ->select('login, nick, email, telephone, certified, rating')
                ->from('users')
                ->where('user_id=:user_id', array(':user_id' => $userId))
                ->queryRow();
    }

    public static function getUserAllstatus ($userId) 
    {
        return Yii::app()->db->createCommand()
                ->select('pro, ban')
                ->from('users')
                ->where('user_id=:user_id', array(':user_id' => $userId))
                ->queryRow();
    }

    public static function getStarColor($rating) 
    {
        switch ($rating) {
            case $rating<15:
                    $class = 'star_ver green';
                    break;
            case ($rating>=15 && $rating <150):
                    $class = 'star_ver yellow';
                    break;
            case ($rating>=150 && $rating <500):
                    $class = 'star_ver red';
                    break;
            case ($rating>=500 && $rating <1000):
                    $class = 'star_ver blue';
                    break;
            case ($rating>=1000):
                    $class = 'star_ver violet';
                    break;
        }

        return '<span class="'.$class.'" title="'.Yii::t('basic', 'Verified user').'"></span>';
    }
}
