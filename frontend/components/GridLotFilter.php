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

class GridLotFilter
{
    const DEFAULT_PERIOD_OPTION = 'all';

    public static function appendQueryToCommand(CDbCommand $command)
    {
        switch (self::getPeriod()) {
            case 'last_year':
                $command->andWhere('s.date > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 YEAR)');
                break;
            case 'last_half_year':
                $command->andWhere('s.date > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 6 MONTH)');
                break;
            case 'last_month':
                $command->andWhere('s.date > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 MONTH)');
                break;
            case 'last_week':
                $command->andWhere('s.date > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 WEEK)');
                break;
            case 'from_yesterday':
                $command->andWhere('s.date BETWEEN DATE_FORMAT(NOW(), "%Y-%m-%d 00:00:00") - INTERVAL 1 DAY AND DATE_FORMAT(NOW(), "%Y-%m-%d 00:00:00")');
                break;
            case 'today':
                $command->andWhere('s.date > DATE_FORMAT(NOW(), "%Y-%m-%d 00:00:00")');
                break;
        }
    }

    public static function getPeriod()
    {
        $cookieKey = 'u_sold_items_period';
        $request = Yii::app()->getRequest();
        $cookies = $request->getCookies();
        $periodDefault = isset($cookies[$cookieKey]) ? $cookies[$cookieKey]->value : self::DEFAULT_PERIOD_OPTION;
        if ($request->getParam('period')) {
            Cookie::saveForWebUser($cookieKey, $request->getParam('period'));
        }
        return $request->getParam('period', $periodDefault);
    }

    public static function getSearchQuery()
    {
        $text = '';
        $param = Yii::app()->getRequest()->getParam('Auction');
        if (is_array($param) && array_key_exists('name', $param)) {
            $text = $param['name'];
        }
        return $text;
    }
}