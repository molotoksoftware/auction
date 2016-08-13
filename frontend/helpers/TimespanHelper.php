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

class TimespanHelper {


    public static function getTime($seconds, $time = null) {
        if(is_null($time))
            $time = time();

        $result = self::timespan($time, $seconds);

        if(count($result) != 0) {
            $str = implode(', ',$result);
        } else{
            $str = 'сейчас';
        }

        return $str;
    }

    public static function timespan($seconds = 1, $time = '') {
        if (!is_numeric($seconds)) {
            $seconds = 1;
        }

        if (!is_numeric($time)) {
            $time = time();
        }
        if ($time <= $seconds) {
            $seconds = 1;
        } else {
            $seconds = $time - $seconds;
        }

        $str = array();
        $years = floor($seconds / 31536000);

        if ($years > 0) {
            $str[] = $years . ' ' . self::getDateFormat($years, 'год', 'лет', 'года');
        }

        $seconds -= $years * 31536000;
        $months = floor($seconds / 2628000);

        if ($years > 0 || $months > 0) {
            if ($months > 0) {
                $str[] = $months . ' ' . self::getDateFormat($months, 'месяц', 'месяцев', 'месяца');
            }

            $seconds -= $months * 2628000;
        }

        $weeks = floor($seconds / 604800);

        if ($years > 0 || $months > 0 || $weeks > 0) {
            if ($weeks > 0) {
                $str[] = $weeks . ' ' . self::getDateFormat($weeks, 'неделю', 'недель', 'недели');
            }

            $seconds -= $weeks * 604800;
        }

        $days = floor($seconds / 86400);

        if ($months > 0 || $weeks > 0 || $days > 0) {
            if ($days > 0) {
                $str[] = $days . ' ' . self::getDateFormat($days, 'день', 'дней', 'дня');
            }

            $seconds -= $days * 86400;
        }

        $hours = floor($seconds / 3600);

        if ($days > 0 || $hours > 0) {
            if ($hours > 0) {

                $str[] = $hours . ' ' . self::getDateFormat($hours, 'час', 'часов', 'часа');
            }

            $seconds -= $hours * 3600;
        }

        $minutes = floor($seconds / 60);

        if ($days > 0 || $hours > 0 || $minutes > 0) {
            if ($minutes > 0) {
                $str[] = $minutes . ' ' . self::getDateFormat($minutes, 'минута', 'минут', 'минуты');
            }

            $seconds -= $minutes * 60;
        }

        if ($str == '') {
            $str[] = $seconds . ' ' . self::getDateFormat($seconds, 'секунда', 'секунд', 'секунды');
        }

        return $str;
    }

    public static function getDateFormat($date, $first, $second, $third) {
        if ((($date % 10) > 4 && ($date % 10) < 10) || ($date > 10 && $date < 20)) {
            return $second;
        }
        if (($date % 10) > 1 && ($date % 10) < 5) {
            return $third;
        }
        if (($date % 10) == 1) {
            return $first;
        } else {
            return $second;
        }

    }
}