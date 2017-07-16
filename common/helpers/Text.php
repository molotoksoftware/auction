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


/* helper для работы с текстом */

class Text
{
    public static function translit($str)
    {
        $str = str_replace(' ', '-', $str);
        $str = str_replace('_', '-', $str);

        $tr = [
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
            "Д" => "D", "Е" => "E", "Ж" => "J", "З" => "Z", "И" => "I",
            "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
            "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
            "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
            "Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
            "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
            "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j",
            "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
            "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
            "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
        ];

        $str = strtolower(strtr($str, $tr));
        $str = preg_replace('/[^0-9a-z\-]/', '', $str);

        return $str;
    }

    /**
     * Character Limiter
     *
     * Обрезать текст до определенного колиства символов, добавив в конце "..."
     *
     * @access public
     *
     * @param  string  - строка для обрезания
     * @param  integer - до скольких символов обрезать строку
     * @param  string  - окончание текста
     *
     * @return string  - новая строка
     */
    public static function characterLimiter($str, $n = 500, $end_char = '&#8230;')
    {
        if (mb_strlen($str, Yii::app()->charset) < $n)
            return $str;

        $str = preg_replace("/\s+/", ' ', str_replace(["\r\n", "\r", "\n"], ' ', $str));

        if (mb_strlen($str, Yii::app()->charset) <= $n)
            return $str;

        $out = "";
        foreach (explode(' ', trim($str, Yii::app()->charset)) as $val) {
            $out .= $val . ' ';

            if (mb_strlen($out, Yii::app()->charset) >= $n) {
                $out = trim($out, Yii::app()->charset);
//                $out = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $out));

                $out = mb_substr($out, 0, $n, Yii::app()->charset);

                return (mb_strlen($out, Yii::app()->charset) == mb_strlen($str, Yii::app()->charset)) ? $out : $out . $end_char;
            }
        }
    }

    /**
     * Word Limiter
     *
     * Обрезать текст до определенного колиства слов, добавив в конце "..."
     *
     * @access public
     *
     * @param  string  - строка для обрезания
     * @param  integer - до скольких символов обрезать строку
     * @param  string  - окончание текста
     *
     * @return string  - новая строка
     */
    public static function wordLimiter($str, $limit = 100, $end_char = '&#8230;')
    {
        if (trim($str) == '')
            return $str;

        preg_match('/^\s*+(?:\S++\s*+){1,' . (int)$limit . '}/', $str, $matches);

        if (mb_strlen($str) == mb_strlen($matches[0]))
            $end_char = '';

        return rtrim($matches[0]) . $end_char;
    }

    /**
     * Цензор слов
     *
     * Принимает строку и массив запрещенных слов. Слова в строке,
     * которые содержатся в массиве заменяются на символы ###
     *
     * @access public
     *
     * @param  string - строка
     * @param  array  - массив запрещенных слов
     * @param  string - чем замещать слова
     *
     * @return string - строка после замены
     */
    public static function wordCensor($str, $censored, $replacement = '')
    {
        if (!is_array($censored))
            return $str;

        $str = ' ' . $str . ' ';

        // \w, \b and a few others do not match on a unicode character
        // set for performance reasons. As a result words like über
        // will not match on a word boundary. Instead, we'll assume that
        // a bad word will be bookended by any of these characters.
        $delim = '[-_\'\"`(){}<>\[\]|!?@#%&,.:;^~*+=\/ 0-9\n\r\t]';

        foreach ($censored as $badword) {
            if ($replacement != '')
                $str = preg_replace(
                    "/({$delim})(" . str_replace('\*', '\w*?', preg_quote($badword, '/')) . ")({$delim})/i", "\\1{$replacement}\\3", $str
                );
            else
                $str = preg_replace(
                    "/({$delim})(" . str_replace('\*', '\w*?', preg_quote($badword, '/')) . ")({$delim})/ie", "'\\1'.str_repeat('#', strlen('\\2')).'\\3'", $str
                );
        }

        return trim($str);
    }

    /**
     * Выделить фразу
     *
     * Выделить фразу в тексте
     *
     * @access public
     *
     * @param  string - строка для поиска
     * @param  string - фраза для выделения
     * @param  string - текст, который будет вставлен до найденной фразы
     * @param  string - текст, который будет вставлен после найденной фразы
     *
     * @return string - строка с выделенными фразами
     */
    public static function highlightPhrase($str, $phrase, $tag_open = '<strong>', $tag_close = '</strong>')
    {
        if ($str == '')
            return '';

        if ($phrase != '')
            return preg_replace('/(' . preg_quote($phrase, '/') . ')/i', $tag_open . "\\1" . $tag_close, $str);

        return $str;
    }

    /**
     * Word Wrap
     *
     * Wraps text at the specified character.  Maintains the integrity of words.
     * Anything placed between {unwrap}{/unwrap} will not be word wrapped, nor
     * will URLs.
     *
     * @access public
     *
     * @param  string  - the text string
     * @param  integer - the number of characters to wrap at
     *
     * @return string
     */
    function wordWrap($str, $charlim = '76')
    {
        // Se the character limit
        if (!is_numeric($charlim))
            $charlim = 76;

        // Reduce multiple spaces
        $str = preg_replace("| +|", " ", $str);

        // Standardize newlines
        if (strpos($str, "\r") !== FALSE)
            $str = str_replace(["\r\n", "\r"], "\n", $str);

        // If the current word is surrounded by {unwrap} tags we'll
        // strip the entire chunk and replace it with a marker.
        $unwrap = [];
        if (preg_match_all("|(\{unwrap\}.+?\{/unwrap\})|s", $str, $matches))
            for ($i = 0; $i < count($matches['0']); $i++) {
                $unwrap[] = $matches['1'][$i];
                $str = str_replace($matches['1'][$i], "{{unwrapped" . $i . "}}", $str);
            }

        // Use PHP's native function to do the initial wordwrap.
        // We set the cut flag to FALSE so that any individual words that are
        // too long get left alone.  In the next step we'll deal with them.
        $str = wordwrap($str, $charlim, "\n", FALSE);

        // Split the string into individual lines of text and cycle through them
        $output = "";
        foreach (explode("\n", $str) as $line) {
            // Is the line within the allowed character count?
            // If so we'll join it to the output and continue
            if (strlen($line) <= $charlim) {
                $output .= $line . "\n";
                continue;
            }

            $temp = '';
            while ((strlen($line)) > $charlim) {
                // If the over-length word is a URL we won't wrap it
                if (preg_match("!\[url.+\]|://|wwww.!", $line))
                    break;

                // Trim the word down
                $temp .= substr($line, 0, $charlim - 1);
                $line = substr($line, $charlim - 1);
            }

            // If $temp contains data it means we had to split up an over-length
            // word into smaller chunks so we'll add it back to our current line
            $output .= ($temp != '') ? $temp . "\n" . $line : $line;
            $output .= "\n";
        }

        // Put our markers back
        if (count($unwrap) > 0)
            foreach ($unwrap as $key => $val)
                $output = str_replace("{{unwrapped" . $key . "}}", $val, $output);

        // Remove the unwrap tags
        $output = str_replace(['{unwrap}', '{/unwrap}'], '', $output);

        return $output;
    }

    public static function asciiToEntities($str)
    {
        $count = 1;
        $out = '';
        $temp = [];

        for ($i = 0, $s = mb_strlen($str); $i < $s; $i++) {
            $ordinal = ord($str[$i]);

            if ($ordinal < 128) {
                /*
                 * If the $temp array has a value but we have moved on, then it seems only
                 * fair that we output that entity and restart $temp before continuing. -Paul
                 */
                if (count($temp) == 1) {
                    $out .= '&#' . array_shift($temp) . ';';
                    $count = 1;
                }

                $out .= $str[$i];
            } else {
                if (count($temp) == 0)
                    $count = ($ordinal < 224) ? 2 : 3;

                $temp[] = $ordinal;

                if (count($temp) == $count) {
                    $number = ($count == 3) ? (($temp['0'] % 16) * 4096) + (($temp['1'] % 64) * 64) + ($temp['2'] % 64) : (($temp['0'] % 32) * 64) + ($temp['1'] % 64);

                    $out .= '&#' . $number . ';';
                    $count = 1;
                    $temp = [];
                }
            }
        }

        return $out;
    }

    public static function entitiesToAscii($str, $all = TRUE)
    {
        if (preg_match_all('/\&#(\d+)\;/', $str, $matches)) {
            for ($i = 0, $s = count($matches['0']); $i < $s; $i++) {
                $digits = $matches['1'][$i];
                $out = '';

                if ($digits < 128)
                    $out .= chr($digits);
                elseif ($digits < 2048) {
                    $out .= chr(192 + (($digits - ($digits % 64)) / 64));
                    $out .= chr(128 + ($digits % 64));
                } else {
                    $out .= chr(224 + (($digits - ($digits % 4096)) / 4096));
                    $out .= chr(128 + ((($digits % 4096) - ($digits % 64)) / 64));
                    $out .= chr(128 + ($digits % 64));
                }

                $str = str_replace($matches['0'][$i], $out, $str);
            }
        }

        if ($all)
            $str = str_replace(
                ["&amp;", "&lt;", "&gt;", "&quot;", "&apos;", "&#45;"], ["&", "<", ">", "\"", "'", "-"], $str
            );

        return $str;
    }

    // Склонение числа $num
    public static function declination($num, $one, $ed, $mn, $notnumber = false)
    {
        // $one="статья";
        // $ed="статьи";
        // $mn="статей";
        if ($num === "")
            print "";
        if (($num == "0") or (($num >= "5") and ($num <= "20")) or preg_match("|[056789]$|", $num))
            if (!$notnumber)
                return "$num $mn";
            else
                return $mn;
        if (preg_match("|[1]$|", $num))
            if (!$notnumber)
                return "$num $one";
            else
                return $one;
        if (preg_match("|[234]$|", $num))
            if (!$notnumber)
                return "$num $ed";
            else
                return $ed;
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Удаляет префикс в строке.
     *
     * @param string $prefix
     * @param string $str
     *
     * @return string
     */
    public static function removePrefix($prefix, $str)
    {
        if (substr($str, 0, strlen($prefix)) == $prefix) {
            $str = substr($str, strlen($prefix));
        }
        return $str;
    }

    /**
     * Оставляет в строке только цифры.
     *
     * @param $str
     *
     * @return mixed
     */
    public static function removeNonNumericalChars($str)
    {
        return preg_replace("/[^0-9,.]/", "", $str);
    }

    public static function formatPhoneNumber($countryCode, $telephone)
    {
        $parts = [
            substr($telephone, 0, 3),
            substr($telephone, 3, 3),
            substr($telephone, 6/*, 3*/),
//            substr($telephone, 9)
        ];
        $to = implode('-', $parts);
        return "+{$countryCode} {$to}";
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public static function removeQueryStringFromUrl($url)
    {
        return preg_replace('/\?.*/', '', $url);
    }

    /**
     * Приведение текста к заглавной букве.
     * Работает для кириллических строк.
     *
     * @param $string
     *
     * @return string
     */
    public static function ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($string, 1, NULL, 'UTF-8');
    }
}