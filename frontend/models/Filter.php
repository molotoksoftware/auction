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

class Filter extends CFormModel
{

    public $filters = array();

    /**
     * Override magic getter for filters
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->filters)) {
            $this->filters[$name] = null;
        }
        return $this->filters[$name];
    }

    public function __isset($name)
    {
        if (array_key_exists($name, $this->filters)) {
            return true;
        }
        return false;
    }

    public function __set($name, $value){
        $this->filters[$name] = $value;
    }

    /**
     * Filter input array by key value pairs
     * @param array $data rawData
     * @return array filtered data array
     */
//    public function filters(array $data)
//    {
//        foreach ($data AS $rowIndex => $row) {
//            foreach ($this->filters AS $key => $value) {
//                // unset if filter is set, but doesn't match
//                if (array_key_exists($key, $row) AND !empty($value)) {
//                    if (stripos($row[$key], $value) === false)
//                        unset($data[$rowIndex]);
//                }
//            }
//        }
//        return $data;
//    }

}