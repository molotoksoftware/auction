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

/**
 * Class BaseBillingHelper
 */
abstract class BaseBillingHelper
{
    public static $lowRateCurrencies = [
        BillingCurrency::CODE_RUR,
        BillingCurrency::CODE_BYR,
        BillingCurrency::CODE_AMD,
        BillingCurrency::CODE_KZT,
        BillingCurrency::CODE_UAH,
    ];

    /**
     * @return string
     */
    public static function getDefaultUserCurrencyCode()
    {
        return Getter::billing()->defaultUserCurrencyCode;
    }

    /**
     * Округление до 4-х знаков.
     *
     * @param $amount
     *
     * @return float
     */
    public static function round4($amount)
    {
        return round($amount, 4);
    }

    /**
     * @return BillingCurrency[]
     */
    public static function getAvailableCurrencies()
    {
        return Getter::billing()->getAvailableCurrencies();
    }


    /**
     * @param float  $amount
     * @param bool   $round
     * @param string $currencyCode
     *
     * @return float|string
     */
    public static function calculatePrice($amount, $currencyCode, $round = false)
    {
        $systemCurrencyCode = Getter::billing()->systemCurrencyCode;

        $result = floatval($amount);

        if ($systemCurrencyCode != $currencyCode) {
            $result = Getter::billing()->convertMoney(
                $amount,
                Getter::billing()->getCurrencyIdByCode($currencyCode)
            );
        }
        if ($round) {
            $result = self::roundPrice($result, $currencyCode);
        }
        return $result;
    }

    public static function roundPrice($amount, $currencyCode)
    {
        if (!is_float($amount)) {
            $amount = floatval($amount);
        }

        $precision = self::isLowRateCurrency($currencyCode) ? 0 : 2;

        return round(
            floatval($amount),
            $precision
        );
    }

    /**
     * @param float  $amount   Сумма
     * @param string $currency Код валюты
     * @param array  $options
     *
     * @return float
     */
    public static function formatCurrency($amount, $currency, $options = [])
    {
        $origAmount = $amount;
        $decimals = 2;
        if (self::isLowRateCurrency($currency)) {
            $decimals = isset($options['lrcDecimals']) ? $options['lrcDecimals'] : 0;
            $amount = round($origAmount, $decimals);
            if ($amount == 0) {
                $decimals = 2;
                $amount = round($origAmount, $decimals);
            }
        }

        $result = number_format($amount, $decimals, '.', ' ');

        $showCurrency = !isset($options['showCurrencySymbol']) || $options['showCurrencySymbol'];
        if ($showCurrency) {
            if (isset($options['rurCurrencySign']) && $currency == BillingCurrency::CODE_RUR) {
                $options['currencySign'] = $options['rurCurrencySign'];
            }
            if (!array_key_exists('currencySign', $options)) {
                $currencyCode = $currency;
                if (BillingCurrency::CODE_RUR == $currencyCode) {
                    $currencyCode = BillingCurrency::CODE_RUB;
                }
                $options['currencySign'] = self::getCurrencySymbol($currencyCode);
            }
            $result .= ' ' . $options['currencySign'];
        }

        return $result;
    }

    protected static function isLowRateCurrency($currency)
    {
        return in_array($currency, self::$lowRateCurrencies);
    }

    /**
     * @param float  $amount
     * @param string $currencyCode
     * @param array  $options
     *
     * @return float|string
     */
    public static function getPriceWithCurrency($amount, $currencyCode, $options = [])
    {
        $result = self::calculatePrice($amount, $currencyCode);
        return self::formatCurrency(
            $result,
            $currencyCode,
            [
                'rurCurrencySign' => array_key_exists('rurCurrencySign', $options) ? $options['rurCurrencySign'] : null,
            ]
        );
    }

    /**
     * @param float  $amount
     * @param string $currencyCode
     * @param bool   $showCurrency
     *
     * @return float|string
     */
    public static function getPrice($amount, $currencyCode, $showCurrency = true)
    {
        $result = self::calculatePrice($amount, $currencyCode);
        return self::formatCurrency($result, $currencyCode, ['showCurrencySymbol' => $showCurrency]);
    }

    protected static function calculateRurFromOtherCurrency($amount, $fromCurrency)
    {
        return Getter::billing()->convertMoney(
            $amount,
            Getter::billing()->getCurrencyIdByCode(BillingCurrency::CODE_RUR),
            Getter::billing()->getCurrencyIdByCode($fromCurrency)
        );
    }

    /**
     * @param string $code
     *
     * @return string
     */
    private static function getCurrencySymbol($code)
    {
        $symbol = Yii::app()->getLocale()->getCurrencySymbol($code);
        if ($symbol === null || $symbol === '') {
            $symbol = $code;
        }
        return $symbol;
    }
}