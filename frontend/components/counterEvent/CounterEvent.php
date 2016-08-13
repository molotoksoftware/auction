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


class CounterEvent
{

    public $type;

    public function inc($onwer, $item = 0)
    {
        Yii::app()->db->createCommand()
                ->insert('counter_event', array(
                    'owner' => (int) $onwer,
                    'type' => $this->type,
                    'item' => $item,
                    'date' => date('Y-m-d H:i:s', time())
        ));
    }

    public function dec($onwer)
    {
        return Yii::app()->db->createCommand()
                        ->delete('counter_event', 'owner=:owner and type=:type', array( // TODO: Нужен ли type
                            ':owner' => $onwer,
                            ':type' => $this->type
        ));
    }

    public function decByItem($onwer, $item)
    {
        return Yii::app()->db->createCommand()
                        ->delete('counter_event', 'owner=:owner and type=:type and item=:item', array(
                            ':owner' => $onwer,
                            ':item' => $item,
                            ':type' => $this->type
        ));
    }

    public function allowCreateEvent($type, $owner, $item)
    {
        $r = Yii::app()->db->createCommand()
                ->select('COUNT(*)')
                ->from('counter_event c')
                ->where('c.owner=:owner and c.type=:type and c.item=:item')
                ->queryScalar(array(
            ':owner' => $owner,
            ':type' => $type,
            ':item' => (int) $item
        ));
        return ($r >= 1) ? false : true;
    }

    public static function count($type, $owner)
    {
        return Yii::app()->db->createCommand()
                        ->select('COUNT(*)')
                        ->from('counter_event c')
                        ->where('c.owner=:owner and c.type=:type')
                        ->queryScalar(array(
                            ':owner' => $owner,
                            ':type' => $type
        ));
    }

}
