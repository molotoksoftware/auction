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

Yii::import('console.components.cli.ConsoleCommandTrait');
Yii::import('console.components.cli.LockScriptTrait');

/**
 * Основные действия
 * - закрыт завершающиеся лоты
 * - отдать лот победителю
 * - посылать уведомления
 *
 * 2015-09-08
 * Метод run переимнеован в actionIndex
 * Нужно использовать вместо него методы ProcessLotWithBids и ProcessLotWithoutBids:
 * раз в минуту: * * * * * lot processLotWithBids
 * раз в минуту: * * * * * lot processLotWithoutBids
 *
 * lot removeImagesFromDeletedLots --processLots=10 --queryLimit=10
 *
 * Class LotCommand
 */



class LotCommand extends CConsoleCommand
{
    use ConsoleCommandTrait;
    use LockScriptTrait;

    /**
     *
     * TODO старый метод который все делает
     *
     * Время выполнения 16,25 ан 10к юзеров.
     */
    public function actionIndex()
    {
        $this->startTimeTracking();

        $timestamp = time();
        $dateTimeComplete = date('Y-m-d H', $timestamp) . ':' . date('i', $timestamp) . ':59';
        $items = $this->findCompleted($dateTimeComplete);

        $fp = fopen(Yii::getPathOfAlias('console.runtime') . '/2.txt', "w+");
        $mytext = count($items) . "\r\n";
        fwrite($fp, $mytext);
        fclose($fp);

        foreach ($items as $item) {
            $fp = fopen(Yii::getPathOfAlias('console.runtime') . '/2.txt', "a");
            $mytext = $item['auction_id'] . "-";
            fwrite($fp, $mytext);
            fclose($fp);

            //Проверка наличия ставок
            if ($this->checkExistBets($item['auction_id'])) {
                //визначить победителя,
                if (($winner = $this->getWinnerAuction($item['current_bid'])) !== false) {
                    Yii::log('победитель ' . $winner['user_id'], CLogger::LEVEL_INFO, 'lot');
                    $this->giveLotWinner($item, $winner['user_id']);
                } else {
                    Yii::log('не найден победитель по лоту ' . $item['auction_id'], CLogger::LEVEL_INFO, 'lot');
                }
            } else {
                $fp = fopen(Yii::getPathOfAlias('console.runtime') . '/2.txt', "a");
                $mytext = "нет ставок-";
                fwrite($fp, $mytext);
                fclose($fp);

                //завершить торги по лоту
                $user = User::model()->findByPk($item['owner']);

                $fp = fopen(Yii::getPathOfAlias('console.runtime') . '/2.txt', "a");
                $mytext = $user->user_id;
                fwrite($fp, $mytext);
                fclose($fp);

                if ($item['is_auto_republish'] == 1) {
                    $this->republishLot($item);
                } else {
                    $this->markCompleted($item);
                }
            }
        }
        $this->stopTimeTracking();
        $this->showTime();
    }

    /**
     * Лоты со ставками. Назначение победителя если есть.
     *
     * Вызов из консоли:
     * php yiic.php lot processLotWithBids
     */
    public function actionProcessLotWithBids()
    {
        $scriptName = __METHOD__;
        if ($this->isLockedScript($scriptName)) {
            self::log(sprintf('%s is already running!', $scriptName));
            return 1;
        }
        $this->lockScript($scriptName);

        $this->startTimeTracking();

        $timestamp = time();
        $dateTimeComplete = date('Y-m-d H', $timestamp) . ':' . date('i', $timestamp) . ':01';
        $items = $this->findCompletedWithBids($dateTimeComplete);

        $fp = fopen(Yii::getPathOfAlias('console.runtime') . '/2.txt', "w+");
        $mytext = count($items) . "\r\n";
        fwrite($fp, $mytext);
        fclose($fp);

        foreach ($items as $item) {
            $fp = fopen(Yii::getPathOfAlias('console.runtime') . '/2.txt', "a");
            $mytext = $item['auction_id'] . "-";
            fwrite($fp, $mytext);
            fclose($fp);

            // Назначить победителя.
            if (($winner = $this->getWinnerAuction($item['current_bid'])) !== false) {
                Yii::log('победитель ' . $winner['user_id'], CLogger::LEVEL_INFO, 'lot');
                $this->giveLotWinner($item, $winner['user_id']);
            } else {
                Yii::log('не найден победитель по лоту ' . $item['auction_id'], CLogger::LEVEL_INFO, 'lot');
            }
        }
        $this->stopTimeTracking();
        $this->showTime();
        $this->unLockScript($scriptName);

        return 0;
    }

    /**
     * Лоты без ставок для перевыставления или для окончания.
     *
     * Вызов из консоли:
     * php yiic.php lot processLotWithoutBids --memoryUsageLimit=1000
     *
     * @param int $memoryUsageLimit
     *
     * @return int
     */
    public function actionProcessLotWithoutBids($memoryUsageLimit = 1000)
    {
        ini_set('memory_limit', '1512M');
        $scriptName = __METHOD__;
        if ($this->isLockedScript($scriptName)) {
            self::log(sprintf('%s is already running!', $scriptName));
            return 1;
        }
        $this->lockScript($scriptName);

        $this->startTimeTracking();

        $timestamp = time();
        $dateTimeComplete = date('Y-m-d H', $timestamp) . ':' . date('i', $timestamp) . ':59';
        $items = $this->findCompletedWithBids($dateTimeComplete, false);

        $fp = fopen(Yii::getPathOfAlias('console.runtime') . '/process.lot_without_bids', "w+");
        $mytext = count($items) . "\r\n";
        fwrite($fp, $mytext);

        $itemsCount = count($items);
        foreach ($items as $i => $item) {
            $memoryUsage = round(memory_get_peak_usage() / 1024 / 1024);
            $mytext = '[' . date("H:i:s") . '] ' . $item['auction_id'] . "-нет ставок-" . $item['owner'] . ' (#' . $i . ' from ' . $itemsCount . ', memory: ' . $memoryUsage . 'mb)' . "\n";
            fwrite($fp, $mytext);
            if (!empty($memoryUsageLimit) && $memoryUsage > $memoryUsageLimit) {
                fwrite($fp, sprintf("Процесс останавливается изза высокого потребелния памяти %d", $memoryUsageLimit));
                break;
            }

            if ($item['is_auto_republish'] == 1) {
                $this->republishLot($item);
            } else {
                $this->markCompleted($item);
            }
        }
        fclose($fp);
        $this->stopTimeTracking();
        $this->showTime();
        $this->unLockScript($scriptName);

        return 0;
    }

    /**
     * Проверка наличия ставок
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function checkExistBets($id)
    {
        return (boolean)Yii::app()->db->createCommand()
            ->select('COUNT(*) as count')
            ->from('bids')
            ->where('lot_id=:lot_id', [':lot_id' => $id])
            ->queryScalar();
    }


    /**
     * Отдать лот победителю
     *
     * @param mixed $lot
     * @param int   $idWinner
     */
    public function giveLotWinner($lot, $idWinner)
    {
        //цена за которую куплен лот
        $price = Yii::app()->db->createCommand()
            ->select('price')
            ->from('bids')
            ->where('bid_id=:bid_id', [':bid_id' => $lot['current_bid']])
            ->queryScalar();

        //формируем запись о покупке лота
        Yii::app()->db->createCommand()
            ->insert(
                'sales',
                [
                    'item_id'   => $lot['auction_id'],
                    'price'     => $price, //цена за которую куплен лот
                    'buyer'     => $idWinner,
                    'date'      => date('Y-m-d H:i:s', time()),
                    'amount'    => $price,
                    'type'      => 2, //выигран
                    'seller_id' => $lot['owner'],
                ]
            );

        $sales_id = Yii::app()->db->lastInsertID;
        Yii::app()->db->createCommand()
            ->update(
                'auction',
                [
                    'sales_id'      => $sales_id,
                    'status'        => Auction::ST_COMPLETED_SALE,
                    'quantity'      => $lot['quantity'] - 1,
                    'quantity_sold' => $lot['quantity_sold'] + 1,
                    'update'        => time(),
                ],
                'auction_id=:auction_id',
                [':auction_id' => $lot['auction_id']]
            );

        /**
         * @var $lot Auction
         */
        $lot = Auction::model()->findByPk($lot['auction_id']);

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
            'lotPrice'     => $price,
            'quantity'     => 1,
            'lotModel'     => $lot,
            'amount'       => ($price * 1),
            'sellerModel'  => $sellerModel,
        ];
        $ntf = new Notification($idWinner, $params, Notification::TYPE_WINNER_AUCTION);
        $ntf->send();

        /**
         * @notify
         *
         * послать уведомление продавцу
         */
        $params = [
            'linkItem'     => $lot->getLink(true),
            'lotModel'     => $lot,
            'quantity'     => 1,
            'bidPrice'     => $price,
            'amount'       => ($price * 1),
            'buyerModel'   => User::model()->findByPk($idWinner),
        ];
        $ntf = new Notification(
            $lot->owner, $params, Notification::TYPE_COMPLETED_WINNER_LOT);
        $ntf->send();

        $ce = new HistoryShopping();
        $ce->inc($idWinner, $lot->auction_id);

        $ce = new HistorySales();
        $ce->inc($lot->owner, $lot->auction_id);

         if (Yii::app()->params['commission'] == 1) {
            $commissionService = new CommissionService();
            $commissionService->onLotSale($sellerModel, $lot, $price);
        }

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
            [
                ':lot_id' => (int)$lot->auction_id,
                ':owners' => $idWinner,
            ]
        );


        if (!empty($bids)) {
            foreach ($bids as $bid) {
                $ce = new NotWonItems();
                $ce->inc($bid['owner']);
                $params = [
                    'linkItem'     => $lot->getLink(true),
                    'bidPrice'     => $bid['price'],
                ];
                $ntf = new Notification(
                    $bid['owner'], $params, Notification::TYPE_NOT_WON_BIDDING_LOT);
                $ntf->send();
            }
        }
    }

    /**
     * @param $item
     */
    public function markCompleted($item)
    {
        Yii::app()->db->createCommand()
            ->update(
                'auction',
                ['status' => BaseAuction::ST_COMPLETED_EXPR_DATE],
                'auction_id=:auction_id',
                [
                    ':auction_id' => (int)$item['auction_id'],
                ]
            );


        $params = [
            'linkItem' => BaseAuction::staticGetLink($item['name'], $item['auction_id']),
            'lotName'  => $item['name'],
        ];
        //$ntf = new Notification($item['owner'], $params, Notification::TYPE_COMPLETED_LOT);
        //$ntf->send();
    }

    public function findCompleted($dateTimeCompleted)
    {
        return Yii::app()->db->createCommand()
            ->select('*')
            ->from('auction')
            ->where(
                '(DATE_FORMAT(bidding_date, "%Y-%m-%d %H:%i:%s")<=:date_ending) and status=:status AND status!=10',
                [
                    ':date_ending' => $dateTimeCompleted,
                    ':status'      => Auction::ST_ACTIVE,
                ]
            )
            ->queryAll();
    }

    public function findCompletedWithBids($dateTimeCompleted, $withBids = true)
    {
        $sign = $withBids ? '>' : '=';
        $query = '
            SELECT
              a.*,
              (SELECT COUNT(*)
               FROM bids
               WHERE lot_id = a.auction_id) AS bidsCount
            FROM auction a
            WHERE (DATE_FORMAT(a.bidding_date, "%Y-%m-%d %H:%i:%s") <= :date_ending)
              AND a.status = :status
              AND a.type = :type
              AND a.status != 10
            HAVING bidsCount ' . $sign . ' 0
        ';

        $cmd = Yii::app()
            ->getDb()
            ->createCommand($query);

        $cmd->params = [
            ':date_ending' => $dateTimeCompleted,
            ':status'      => Auction::ST_ACTIVE,
            ':type'        => BaseAuction::TYPE_AUCTION,
        ];

        return $cmd->queryAll();
    }

    public function getWinnerAuction($current_bid)
    {
        return Yii::app()->db->createCommand()
            ->select('u.*')
            ->from('bids b')
            ->join('users u', 'b.owner=u.user_id')
            ->where('b.bid_id=:bid_id', [':bid_id' => $current_bid])
            ->queryRow();
    }

    public function republishLot($item)
    {
        $date = new DateTime();
        $dateCreated = $date->format('Y-m-d H:i:s');
        $interval_spec = Auction::getDateSpecForDuration($item['duration']);
        $date->add(new DateInterval($interval_spec));
        $bidding_date = $date->format('Y-m-d H:i:s');

        Yii::app()->db->createCommand()
            ->update(
                'auction',
                [
                    'status'       => BaseAuction::ST_ACTIVE,
                    'bid_count'    => 0,
                    'bidding_date' => $bidding_date,
                    'created'      => $dateCreated,
                    'current_bid'  => 0,
                    'viewed'       => 0,
                ],
                'auction_id=:auction_id',
                [
                    ':auction_id' => (int)$item['auction_id'],
                ]
            );

        Yii::app()->db->createCommand()
            ->delete(
                'bids',
                'lot_id=:lot_id',
                [
                    ':lot_id' => (int)$item['auction_id'],
                ]
            );


    }

    /**
     * Удаление фоток(+ записи из images) у удаленных лотов.
     *
     * @param int $processLots
     * @param int $queryLimit
     *
     * Вызов:
     * php yiic.php lot removeImagesFromDeletedLots --processLots=10 --queryLimit=10
     *
     * @throws CException
     */
    public function actionRemoveImagesFromDeletedLots($processLots = 10, $queryLimit = 10)
    {
        $this->log('Begin remove images from lots.');

        if (!is_numeric($processLots) || !($processLots > 0)) {
            throw new CException('processLots must be greater than 0');
        }

        $appProcess = new RemoveAuctionImages();
        $queryLimit = $processLots > $queryLimit ? $queryLimit : $processLots;
        $globalRemovedImagesCounter = 0;
        $removedImages = 0;
        $processedLots = 0;
        $lastId = $appProcess->getBeginAuctionId();

        while (true) {
            $removedImages = 0;
            $criteria = new CDbCriteria();
            $criteria->condition = 'status = 10 AND auction_id > :from_auction_id';
            $criteria->params[':from_auction_id'] = $lastId;
            $criteria->limit = $queryLimit;
            $criteria->order = 'auction_id ASC';


            /** @var Auction[] $auctions */
            $auctions = Auction::model()->findAll($criteria);
            if ($auctions) {
                foreach ($auctions as $auction) {
                    $removedImages += $auction->removeImages();
                    $lastId = $auction->auction_id;
                }
                $globalRemovedImagesCounter += $removedImages;
            }

            $auctionCount = count($auctions);
            $processedLots += $auctionCount;
            $this->log(sprintf('Processed %s lots, removed %s images.', $processedLots, $removedImages));

            $stop = empty($auctions) || $processedLots >= $processLots;
            if ($stop) {
                if (empty($auctions)) {
                    $lastId = 0;
                }
                break;
            }
        }
        $appProcess->setProcessData($lastId, $processedLots, $removedImages);

        $this->log(sprintf('Remove images from lots completed. Last id is %s. Processed lots %s, removed %s images.', $lastId, $processLots, $globalRemovedImagesCounter));
    }

    /**
     * Удаляет ориг. фото у ВСЕХ аукционов.
     *
     * Вызов:
     * php yiic.php lot removeOriginalImages --queryLimit=10 --processImages=10
     *
     * @param int $queryLimit    Кол-во записей за одну итерацию.
     * @param int $processImages Общее кол-во для обработки за один вызов. 0 - все записи.
     */
    public function actionRemoveOriginalImages($queryLimit = 300, $processImages = 0)
    {
        $this->log('Begin remove original images from lots.');

        $appProcess = new RemoveOriginalAuctionImage();
        $globalRemovedImagesCounter = 0;
        $removedImages = 0;
        $processedImages = 0;
        $lastId = $appProcess->getBeginAuctionImageId();

        // Если нужно обработать часть записей то лимит не должен быть
        // выше этой части чтобы лишнее в один запрос не вытянуть.
        if ($processImages > 0 && $queryLimit > $processImages) {
            $queryLimit = $processImages;
        }

        while (true) {
            $removedImages = 0;

            $images = Yii::app()
                ->getDb()
                ->createCommand()
                ->select(['i.image_id', 'i.image', 'i.item_id', 'a.owner'])
                ->from(ImageAR::model()->tableName() . ' i')
                ->join(Auction::model()->tableName() . ' a', 'a.auction_id = i.item_id')
                ->where('i.image_id > :from_image_id', [':from_image_id' => $lastId])
                ->limit($queryLimit)
                ->queryAll();

            foreach ($images as $image) {
                $filePath = ImageAR::getImageSavePath($image['owner'], false, $image['image']);
                if ($filePath && is_file($filePath)) {
                    if (unlink($filePath)) {
                        $this->log(sprintf('Removed image "%s" from auction ID %s', $filePath, $image['item_id']));
                        $removedImages++;
                    }
                }

                $lastId = $image['image_id'];
            }
            $globalRemovedImagesCounter += $removedImages;

            $imageCount = count($images);
            $processedImages += $imageCount;
            $this->log(sprintf('Processed %s images, removed %s files.', $processedImages, $removedImages));

            $stop = empty($images) || ($processImages > 0 && $processedImages >= $processImages);
            if ($stop) {
                if (empty($images)) {
                    $lastId = 0;
                }
                break;
            }
        }
        $appProcess->setProcessData($lastId, $processedImages, $removedImages);

        $this->log(sprintf(
            'Remove images from lots completed. Last id is %s. Processed images %s, removed %s files.',
            $lastId, $processImages, $globalRemovedImagesCounter
        ));
    }

    /**
     * Проверяет и удаляет блокировку(кеш) для комманд "processLotWithBids" и "processLotWithoutBids"
     *
     * Вызов: php yiic.php lot checkAndDeleteCommandsCache
     */
    public function actionCheckAndDeleteCommandsCache()
    {
        $s1 = 'LotCommand::actionProcessLotWithBids';
        $s2 = 'LotCommand::actionProcessLotWithoutBids';

        $cache = Yii::app()->getCache();

        if ($cache->get($s1)) {
            echo "\n$s1 cache deleted\n";
            $cache->delete($s1);
        }

        if ($cache->get($s2)) {
            echo "\n$s2 cache deleted\n";
            $cache->delete($s2);
        }
    }
}