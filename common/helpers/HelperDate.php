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


class HelperDate {


    /**
     * @return array
     * @param date_start timestamp
     * @param date_end timestamp
     *
     */
    public static function diffMonth($date_start, $date_end) {
        $dates = array();
        //86400 60*60*24
        //date('t', $time) * 86400 добавляем 1 месяц от текущей даты
        for ($time = $date_start, $month = 0; $time < $date_end; $time = $time + date('t', $time) * 86400) {
            $month++;
        }
        // Количество месяцев
        $count_month = $month;

        if ($count_month > 1) {
            for ($time = $date_start, $month = 0; $time < $date_end; $time = $time + date('t', $time) * 86400, $month++) {

                if ($month == 0) {
                    //first
                    $dates[] = array(
                        'start' => date('Y-m-d H:i:s', $date_start),
                        'end' => date('Y-m', $date_start) . '-' . date("t", $date_start) . ' ' . '23:59:00'
                    );
                } elseif ($count_month == ($month + 1)) {
                    //last

                    $dates[] = array(
                        'start' => date('Y-m', $date_end) . '-01' . ' ' . '00:00:00',
                        'end' => date('Y-m', $date_end) . '-' . date("d", $date_end) . ' 23:59:00'
                    );
                } else {
                    $dates[] = array(
                        'start' => date('Y-m', $time) . '-01' . ' 00:00:00',
                        'end' => date('Y-m', $time) . '-' . date("t", $time) . ' ' . '23:59:00'
                    );
                }
            }
        } else {
            $dates[] = array(
                'start' => date('Y-m-d H:i:s', $date_start),
                'end' => date('Y-m-d H:i:s', $date_end)
            );
        }
        return $dates;
    }

    //        echo Date::make_date(time()-rand(1,10000));
    //        echo Date::sec_to_time(60*60*rand(1,60));
    static protected $_LANGDATE = array(
        'January' => "января",
        'February' => "февраля",
        'March' => "марта",
        'April' => "апреля",
        'May' => "мая",
        'June' => "июня",
        'July' => "июля",
        'August' => "августа",
        'September' => "сентября",
        'October' => "октября",
        'November' => "ноября",
        'December' => "декабря",
        'Jan' => "янв",
        'Feb' => "фев",
        'Mar' => "мар",
        'Apr' => "апр",
        'Jun' => "июн",
        'Jul' => "июл",
        'Aug' => "авг",
        'Sep' => "сен",
        'Oct' => "окт",
        'Nov' => "ноя",
        'Dec' => "дек",
        'Sunday' => "Воскресенье",
        'Monday' => "Понедельник",
        'Tuesday' => "Вторник",
        'Wednesday' => "Среда",
        'Thursday' => "Четверг",
        'Friday' => "Пятница",
        'Saturday' => "Суббота",
        'Sun' => "Вс",
        'Mon' => "Пн",
        'Tue' => "Вт",
        'Wed' => "Ср",
        'Thu' => "Чт",
        'Fri' => "Пт",
        'Sat' => "Сб",
    );

    /**
     * @desc translate eng=>rus
     */
    static public function _date($format, $timestamp) {
        return strtr(@date($format, $timestamp), self::$_LANGDATE);
    }

    /**
     * @desc convert unix-timestamp to norm date (Now ...., Yesterday ...., and etc)
     * @param int $timestamp unix-timestamp
     * @return string time
     */
    static public function make_date($timestamp) {
        if (date('Ymd', $timestamp) == date('Ymd', time())) {
            return 'Сегодня' . self::_date(', H:i', $timestamp);
        } elseif (date('Ymd', $timestamp) == date('Ymd', (time() - 86400))) {
            return 'Вчера' . self::_date(', H:i', $timestamp);
        } else {
            return Yii::app()->dateFormatter->format('d MMMM yyyy', date('Y-m-d H:i:s', $timestamp));
            //self::_date('j-m-Y, H:i', $timestamp);
        }
    }

    CONST PERIOD_YEAR = 31536000;
    CONST PERIOD_MONTH = 2592000;
    CONST PERIOD_DAY = 86400;
    CONST PERIOD_HOUR = 3600;
    CONST PERIOD_MINUTE = 60;

    /**
     * @desc convert seconds to norm format (y.d.m.h)
     * @param int $seconds seconds
     * @return string time
     */
    static public
    function sec_to_time($seconds) {
        //self::_number_ending($n, "продуктов", "продукт", "продукта")
        $seconds_period = array(
            self::PERIOD_YEAR => 'г.',
            self::PERIOD_MONTH => 'м',
            self::PERIOD_DAY => 'д.',
            self::PERIOD_HOUR => 'ч.',
            self::PERIOD_MINUTE => 'мин.',
        );
        $out = '';
        foreach ($seconds_period as $period => $date_words) {
            $number = floor($seconds / $period);
            $out .= $number ? $number . $date_words . ' ' : '';
            $seconds -= $number * $period;
        }
        return $out;
    }

    /**
     * Переводим TIMESTAMP в формат вида: 5 дн. назад
     * или 1 мин. назад и тп.
     *
     * @param unknown_type $date_time
     * @return unknown
     */
    public static function getTimeAgo($date_time) {
        $timeAgo = time() - $date_time;
        // $timeAgo = $date_time;

        $timePer = array(
            'day' => array(3600 * 24, 'дн.'),
            'hour' => array(3600, ''),
            'min' => array(60, 'мин.'),
            'sek' => array(1, 'сек.'),
        );
        foreach ($timePer as $type => $tp) {
            $tpn = floor($timeAgo / $tp[0]);
            if ($tpn) {

                switch ($type) {
                    case 'hour':
                        if (in_array($tpn, array(1, 21))) {
                            $tp[1] = 'час';
                        } elseif (in_array($tpn, array(2, 3, 4, 22, 23))) {
                            $tp[1] = 'часa';
                        } else {
                            $tp[1] = 'часов';
                        }
                        break;
                }
                return $tpn . ' ' . $tp[1] . ' назад';
            }
        }
    }

    public static function dateRidN2R($str) {
        $arrFrom = array(
            'январь',
            'февраль',
            'март',
            'апрель',
            'май',
            'июнь',
            'июль',
            'август',
            'сентябрь',
            'октябрь',
            'ноябрь',
            'декабрь',);
        $arrTo = array(
            'января',
            'февраля',
            'марта',
            'апреля',
            'мая',
            'июня',
            'июля',
            'августа',
            'сентября',
            'октября',
            'ноября',
            'декабря');
        $str = str_replace($arrFrom, $arrTo, strtolower($str));
        return $str;
    }

}