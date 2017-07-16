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

class Table
{

    public static function getImageColumn($data)
    {

        $str = Item::getPreview($data, array('width' => 106, 'height' => 106, 'class' => 'img-thumbnail'));


        return $str;
    }

    public static function getMainInfoRow($data)
    {
        return Yii::app()->controller->renderPartial('_info_row_item', ['data' => $data], true);
    }

    public static function getSalesCheckBoxColumnCss($data, $userType, $context = null)
    {
        $css = [];

        if ($userType == 'seller') {
            $css[] = "spech_" . $data["auction_id"] . "_" . $data["owner"] . "_" . $data["buyer"] . "_" . $data["sale_id"] . ($data["review_my_about_saller"] > 0 ? " hidden_ch" : '');
        } else if ($userType == 'buyer') {
            $css[] = "spechbb_" . $data["auction_id"] . "_" . $data["owner"] . "_" . $data["buyer"] . "_" . $data["sale_id"] . ($data["review_about_my_buyer"] > 0 ? " js_hidden_bb" : '');
        }

        return implode(' ', $css);
    }

}
