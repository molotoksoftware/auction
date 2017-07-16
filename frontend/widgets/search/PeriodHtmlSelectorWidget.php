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
 * Class PeriodHtmlSelectorWidget
 */
class PeriodHtmlSelectorWidget extends CWidget
{
    const SCOPE_PAGE_AUCTION = 'page-auction';

    /**
     * @var string
     */
    public $scope = self::SCOPE_PAGE_AUCTION;

    /**
     * @var array
     */
    public $periods = [];

    private $currentPeriod;
    private $currentPeriodTitle;

    public function init()
    {
        if (empty($this->periods)) {
            if ($this->scope == self::SCOPE_PAGE_AUCTION) {
                $this->periods = [
                    '3h'  => Yii::t('basic', 'Past 3 hours'),
                    '12h' => Yii::t('basic', 'Past 12 hours'),
                    '1d'  => Yii::t('basic', 'Past 1 day'),
                    '3d'  => Yii::t('basic', 'Past 3 days'),
                    '1w'  => Yii::t('basic', 'Past week'),
                    'all' => Yii::t('basic', 'All time'),
                ];
            }
        }

        end($this->periods);
        $lastKey = key($this->periods);
        $lastValue = $this->periods[$lastKey];

        $request = Yii::app()->request;
        $this->currentPeriod = $request->getQuery('period', $lastKey);
        $this->currentPeriodTitle = $lastValue;

        foreach ($this->periods as $period => $title) {
            if ($this->currentPeriod == $period) {
                $this->currentPeriodTitle = $title;
            }
        }

        parent::init();
    }

    public function run()
    {
        $this->render('periodHtmlSelector', [
            'periods'            => $this->periods,
            'currentPeriod'      => $this->currentPeriod,
            'currentPeriodTitle' => $this->currentPeriodTitle,
        ]);
    }
}