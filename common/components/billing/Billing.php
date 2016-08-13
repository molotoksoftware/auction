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
 * Class Billing
 */
class Billing
{
    /**
     * Код системной валюты.
     *
     * @var string
     */
    public $systemCurrencyCode = 'RUR';

    /**
     * @var string
     */
    public $defaultUserCurrencyCode = 'RUR';

    /**
     * Время кэширования в секундах для получения курса валюты.
     *
     * @var int
     */
    public $currencyRateCache = 3600; // 60 * 60; 1 час

    public $availableCurrenciesCache = 86400; // сутки.

    public function init()
    {

    }

    /**
     * @param float    $amount
     * @param int      $toCurrencyId
     * @param null|int $fromCurrencyId
     *
     * @return float
     * @throws CException
     */
    public function convertMoney($amount, $toCurrencyId, $fromCurrencyId = null)
    {
        $amount = floatval($amount);
        if (!($amount > 0)) {
            return $amount;
        }

        if (null === $fromCurrencyId) {
            $currencies = self::getAvailableCurrencies('code');
            $fromCurrencyId = $currencies[$this->systemCurrencyCode]->id;
        }

        if (intval($fromCurrencyId) === intval($toCurrencyId)) {
            return $amount;
        }

        $systemCurrencyId = $this->getCurrencyIdByCode($this->systemCurrencyCode);
        if ($toCurrencyId != $systemCurrencyId && $fromCurrencyId != $systemCurrencyId) {
            throw new CException(sprintf('Запрещена конвертация без использования системной валюты'));
        }

        $currencies = self::getAvailableCurrencies('id');

        if (!isset($currencies[$toCurrencyId])) {
            throw new CException('Invalid "to currency id" value');
        }
        $toCurrency = $currencies[$toCurrencyId];
        if (!$toCurrency->is_active) {
            throw new CException('$toCurrency is not active');
        }
        if (!isset($currencies[$fromCurrencyId])) {
            throw new CException('Invalid "from currency id" value');
        }
        $fromCurrency = $currencies[$fromCurrencyId];
        if (!$fromCurrency->is_active) {
            throw new CException('$fromCurrency is not active');
        }

        // Если конвертруют назад в сист. валюту, меняем from и to currency местами.
        $fromSystemCurrencyDirection = $fromCurrencyId == $systemCurrencyId;
        $currencyRateModel = self::getLatestCurrencyRate(
            $fromSystemCurrencyDirection ? $fromCurrencyId : $toCurrencyId,
            $fromSystemCurrencyDirection ? $toCurrencyId : $fromCurrencyId
        );
        if (empty($currencyRateModel)) {
            throw new CException('Currency rate not found');
        }

        // Проверяем последнее обновление курса, если больше суток логируем ошибку в билинге.
        $t1 = time();
        $t2 = strtotime($currencyRateModel->created_at);
        $diff = $t1 - $t2;
        $minutes = $diff / 60;
        if ($minutes > (60 * 24 + 15)) { // сутки + 15 минут.
            Yii::log(
                sprintf(
                    'Курс устарел больше чем на сутки, последний курс %s, fromCurrencyId %s, toCurrencyId %s.',
                    $currencyRateModel->created_at, $fromCurrencyId, $toCurrencyId
                ),
                CLogger::LEVEL_ERROR, 'billing_currency_parser'
            );
        }

        // Конвертируем сумму.
        $rate = floatval($currencyRateModel->rate);
        if ($fromSystemCurrencyDirection) {
            $amount = $amount / $rate;
        } else {
            $amount = $amount * $rate;
        }

        return $amount;
    }

    /**
     * @param string $indexBy
     *
     * @return BillingCurrency[]
     */
    public static function getAvailableCurrencies($indexBy = 'code')
    {
        $dependency = new CDbCacheDependency('SELECT MAX(updated_at) FROM billing_currency');
        $dependency->resetReusableData();
        return ArrayHelper::index(
            BillingCurrency::model()
                ->cache(Getter::billing()->availableCurrenciesCache, $dependency)
                ->filterActive()
                ->findAll(),
            $indexBy
        );
    }

    /**
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     *
     * @return BillingCurrencyRate|null
     */
    private function getLatestCurrencyRate($fromCurrencyId, $toCurrencyId)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'from_currency_id = :from_currency_id AND to_currency_id = :to_currency_id';
        $criteria->params = [
            ':from_currency_id' => $toCurrencyId, // todo указан курс продажи в таблице, поэтому наоборот параметры.
            ':to_currency_id'   => $fromCurrencyId,
        ];
        $criteria->order = 'id DESC';
        $criteria->limit = 1;

        return BillingCurrencyRate::model()->cache($this->currencyRateCache)->find($criteria);
    }

    /**
     * @param string $code
     *
     * @return string
     * @throws CException
     */
    public function getCurrencyIdByCode($code)
    {
        $currencies = $this->getAvailableCurrencies();
        if (!isset($currencies[$code])) {
            throw new CException(sprintf('Currency with code "%s" not found', $code));
        }

        return $currencies[$code]->getPrimaryKey();
    }
}