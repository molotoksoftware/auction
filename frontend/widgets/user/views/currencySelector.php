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
 * @var int               $currentCurrencyCode
 * @var BillingCurrency[] $availableCurrencies
 */

$currencySelectorOptions = [];
foreach ($availableCurrencies as $billingCurrency) {
    $currencySelectorOptions[$billingCurrency->code] = CHtml::image('/img/icons/currencies/' . $billingCurrency->code . '_32.png');
}
?>

<div class="currency-selector js-currency-select">
    <a href="#" class="cs-current" data-currency_id="<?= $currentCurrencyCode ?>">
        <?= CHtml::image('/img/icons/currencies/' . $currentCurrencyCode . '_32.png') ?>
    </a>
    <ul class="cs-options">
        <?php foreach ($availableCurrencies as $billingCurrency): ?>
            <li class="cs-option <?= $billingCurrency->code == $currentCurrencyCode ? 'active' : '' ?>">
                <a href="#" data-currency_id="<?= $billingCurrency->code ?>">
                    <?= CHtml::image('/img/icons/currencies/' . $billingCurrency->code . '_32.png') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php
cs()->registerScript(
    'currency-selector',
    "
    $('.js-currency-select a.cs-current').click(function(e) {
        e.preventDefault();
        $(this).parent().toggleClass('active');
    });
    $('.js-currency-select .cs-option > a').click(function(e) {
        e.preventDefault();
        $('.js-currency-select').removeClass('active');
        if ($(this).data('currency_id') != $('.js-currency-select a.cs-current').data('currency_id')) {
            window.location.href = appendUrlParam(window.location.href, 'currency', $(this).data('currency_id'));
        }
    });
    "
);
?>