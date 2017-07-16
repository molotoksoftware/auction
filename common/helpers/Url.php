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


class Url {
    /**
     * @param $url
     * @param $paramName
     * @param $value
     *
     * @return string
     */
    public static function appendParam($url, $paramName, $value)
    {
        if (!isset($paramName) || !isset($value)) {
            return $url;
        }

        $url_parts = parse_url($url);
        if (!isset($url_parts['query'])) {
            $url_parts['query'] = '';
        }
        parse_str($url_parts['query'], $params);

        $params[$paramName] = urlencode($value);

        $url_parts['query'] = http_build_query($params);

        if (!isset($url_parts['scheme'], $url_parts['host'])) {
            return $url_parts['path'] . '?' . $url_parts['query'];
        } else {
            return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query'];
        }
    }
}