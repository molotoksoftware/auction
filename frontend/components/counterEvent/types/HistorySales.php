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

class HistorySales extends CounterEvent
{

    const TYPE = 5;

    public function __construct()
    {
        $this->type = self::TYPE;
    }

    /**
     * @param int $onwer
     * @param int $item
     *
     * @return boolean
     */
    public function inc($onwer, $item = 0)
    {
        Yii::log('inc user' . $onwer);
        parent::inc($onwer, $item);
        return true;
    }

    public function decByItem($onwer, $item)
    {
        Yii::log('dec by' . $item);
        parent::decByItem($onwer, $item);
        return true;
    }

}
