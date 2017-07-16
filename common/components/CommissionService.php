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

class CommissionService
{
    /**
     * @var null|float
     */
    private $targetSum;

    function __construct()
    {
    }

    /**
     * Sel item
     *
     * @param User    $seller
     * @param Auction $lot
     * @param float   $targetSum
     *
     * @return bool
     */
    public function onLotSale(User $seller, Auction $lot, $targetSum)
    {
        $this->targetSum = floatval($targetSum);

        if (!$seller->getIsNewRecord() && $this->targetSum) {

            $percents = Yii::app()->params['amountCommission'];
            $commission = round($this->targetSum * ($percents / 100), 2);
            $comment = Yii::t('basic','Commission on sold item #') . $lot->getPrimaryKey();
            $balanceHistoryType = BalanceHistory::STATUS_COMMISSION_SALE_LOT;
            $this->takeCommission($seller, $commission, $comment, $balanceHistoryType);
            return true;
        }
        return false;
    }

    /**
     * Get commission
     *
     * @param User   $user
     * @param float  $commission
     * @param string $comment
     * @param int    $balanceHistoryType
     *
     * @return bool
     */
    private function takeCommission(User $user, $commission, $comment, $balanceHistoryType)
    {
        $user->moneySub($commission, $comment, $balanceHistoryType);
        return $user->save(false);
    }
}