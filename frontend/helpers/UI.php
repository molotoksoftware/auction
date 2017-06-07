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

class UI
{
    public static function  showQuantity($quantity, $template = null)
    {

        if (empty($quantity) || (int)$quantity <= 0) {
            return false;
        }

        if ($template == null) {
            $template = '<span class="badge badge-info pull-right">{number}</span>';
        }

        return strtr($template, array('{number}' => $quantity));
    }

    public static function  showSmallQuantity($quantity)
    {

        if (empty($quantity) || (int)$quantity <= 0) {
            return false;
        }

        return "<small>$quantity</small>";
    }


    public static function  showQuantityTablHdr($quantity, $class = 'qnt-large')
    {
        return self::showQuantity($quantity, '<span class="' . $class . '">({number})</span>');
    }

    public static function  showQuantityLeftMenu($quantity, $class = 'label label-info pull-right')
    {
        return self::showQuantity($quantity, '<i class="' . $class . '">{number}</i>');
    }

}