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
 * Class RemoveOriginalAuctionImage
 */
class RemoveOriginalAuctionImage
{
    const APP_PROCESS_CODE = 'RemoveOriginalAuctionImage';

    const APP_PROCESS_TITLE = 'Remove Original Auctions Image files';

    private $fromAuctionImageId;
    private $processedAuctions;
    private $removedImages;

    /**
     * @var AppProcess
     */
    private $appProcess;

    public function __construct()
    {
        $this->appProcess = AppProcess::model()->findByAttributes(['code' => self::APP_PROCESS_CODE]);
        if (!$this->appProcess) {
            $this->appProcess = new AppProcess();
            $this->appProcess->code = self::APP_PROCESS_CODE;
            $this->appProcess->title = self::APP_PROCESS_TITLE;
            $this->appProcess->data = [
                'fromAuctionImageId' => 0,
                'processedAuctions'  => 0,
                'removedImages'      => 0,
            ];
        }

        $this->fromAuctionImageId = $this->appProcess->data['fromAuctionImageId'];
        $this->processedAuctions = $this->appProcess->data['processedAuctions'];
        $this->removedImages = $this->appProcess->data['removedImages'];
    }

    /**
     * ID лота с которого начать обработку лотов.
     *
     * @return int
     */
    public function getBeginAuctionImageId()
    {
        return $this->fromAuctionImageId;
    }

    public function setProcessData($auctionId, $processedAuctions = 0, $removedImages = 0)
    {
        $data = [
            'fromAuctionImageId' => $auctionId,
            'processedAuctions'  => $this->appProcess->data['processedAuctions'] + intval($processedAuctions),
            'removedImages'      => $this->appProcess->data['removedImages'] + intval($removedImages),
        ];
        $this->appProcess->data = $data;

        if (!$this->appProcess->save()) {
            echo 'Validation errors<pre>';
            print_r($this->appProcess->getErrors());
            echo "</pre>";
        }
    }
}