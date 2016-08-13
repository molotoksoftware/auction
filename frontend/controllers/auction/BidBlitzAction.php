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
 * купил лот
 *
 */
class BidBlitzAction extends CAction
{

    public function run()
    {
        $lotId = Yii::app()->request->getParam('lotId', null);
        $quantity = Yii::app()->request->getParam('quantity', 1);
        if (!isset($quantity) || $quantity <= 0) {
            $quantity = 1;
        }

        if (Yii::app()->user->isGuest) {
            RAjax::error(
                array(
                    'type' => 'NOT_AUTHORIZED',
                    'returnUrl' => Yii::app()->user->loginUrl
                )
            );
        }

        if (Auction::verifiedLot($lotId)) {
            if ($salesId = Auction::bidBlitz($lotId, Yii::app()->user->id, $quantity)) {
                //events--------------------------------------------------------
                Yii::log('купил лот (' . $lotId . ') кол ' . $quantity . ' покупатель ' . Yii::app()->user->id);

                /** @var Auction $lot */
                $lot = Auction::model()->findByPk($lotId);

                /** @var User $sellerModel */
                $sellerModel = User::model()->findByPk($lot->owner);

                /**
                 * @notify
                 *
                 * послать уведомление покупателю
                 * Поздравляем вы победили в аукционе
                 */
                $params = [
                    'linkItem'     => $lot->getLink(true),
                    'lotPrice'     => $lot->price,
                    'quantity'     => $quantity,
                    'lotName'      => $lot->name,
                    'lotModel'     => $lot,
                    'amount'       => ($lot->price * $quantity),
                    'sellerModel'  => $sellerModel,
                    'currencyCode' => Getter::webUser()->getCurrencyCode(),
                ];
                $ntf = new Notification(
                    Yii::app()->user->id, $params, Notification::TYPE_WINNER_AUCTION);
                $ntf->send();

                /**
                 * @notify
                 *
                 * послать уведомление продавцу
                 */
                $params = [
                    'linkItem'     => $lot->getLink(true),
                    'lotModel'     => $lot,
                    'lotName'      => $lot->name,
                    'quantity'     => $quantity,
                    'amount'       => ($lot->price * $quantity),
                    'buyerModel'   => Yii::app()->user->getModel(),
                    'currencyCode' => $sellerModel->getCommonData()->currency->code,
                ];
                $ntf = new Notification(
                    $lot->owner, $params, Notification::TYPE_COMPLETED_WINNER_LOT);
                $ntf->send();


                //увеличить счетчик события "История покупок" покупателю
                $ce = new HistoryShopping();
                $ce->inc(Yii::app()->user->id, $lotId);


                //увеличить счетчик события "Проданные лоты" продавцу
                $ce = new HistorySales();
                $ce->inc($lot->owner, $lotId);
                
                // Возьмем комиссию если в настройках установлен флг
                if (Yii::app()->params['comission'] == 1) {
                    $commissionService = new CommissionService();
                    $commissionService->onLotSale($sellerModel, $lot, $lot->price * $quantity);
                }


                if ($lot->quantity <= 1) {

                    //проверить были ли ставки на лот
                    $sql = <<<EOD
        SELECT *
FROM (
SELECT b . *
FROM bids b
WHERE b.lot_id =:lot_id
AND owner NOT
IN (:owners)
ORDER BY b.created DESC
) AS inv
GROUP BY owner
EOD;
                    $bids = Yii::app()->db->createCommand($sql)->queryAll(
                        true,
                        array(
                            ':lot_id' => (int)$lotId,
                            ':owners' => Yii::app()->user->id
                        )
                    );


                    if (!empty($bids)) {
                        //пройтись по участникам
                        foreach ($bids as $bid) {
                            //увеличить счетчик события не Выигранные
                            $ce = new NotWonItems();
                            $ce->inc($bid['owner']);
                            /**
                             * послать уведомление о проигрыше
                             * @notify
                             */
                            $params = [
                                'linkItem'     => $lot->getLink(true),
                                'bidPrice'     => $bid['price'],
                                'currencyCode' => User::getCurrencyCodeByUserId($bid['owner']),
                            ];
                            $ntf = new Notification(
                                $bid['owner'], $params, Notification::TYPE_NOT_WON_BIDDING_LOT);
                            $ntf->send();
                        }
                    }

                }
                //end if quantity
            }

            RAjax::success(array('salesId' => $salesId));
        } else {
            RAjax::error();
        }
    }
}
