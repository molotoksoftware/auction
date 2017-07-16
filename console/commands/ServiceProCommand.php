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
 * Class ServiceProCommand
 */
class ServiceProCommand extends CConsoleCommand {
    
    
    
    public function actionReminderExpiryPro() {

        $date = new DateTime('now');
        $interval = new DateInterval('P1D');
        $completed = $date->add($interval);
        $items = $this->findEnding($completed->format('Y-m-d'));

        foreach ($items as $item) {

            $params = array(
                'completionDate' => date('Y-m-d', strtotime($item['completion_date'])),
            );
            $ntf = new Notification($item['id_user'], $params, Notification::TYPE_REM_EXPIRY_PRO);
            $ntf->send();
        }
    }

    public function actionComplete() {
        $items = $this->findCompleted(date('Y-m-d H'));
        foreach ($items as $item) {
            Yii::app()->db->createCommand()
                    ->update('paid_service_order', ['status' => PaidServices::STATUS_SERVICE_UNACTIVE], 'services_id=:services_id AND services_type=:services_type', [
                        ':services_id' => $item['id'],
                        ':services_type' => PaidServices::TYPE_SERVICE_PRO_ACCOUNT]);

            Yii::app()->db->createCommand()
                    ->update('service_pro_accounts', ['status' => PaidServices::STATUS_SERVICE_EXPIRY], 'id=:id', [':id' => $item['id']]);

            Yii::app()->db->createCommand()
                    ->update('users', ['pro' => 0], 'user_id=:user_id', [':user_id' => $item['id_user']]);

            Yii::log('истек срок сервиса id=' . $item['id'], CLogger::LEVEL_INFO, 'service');
        }
    }

    public function findCompleted($dateTimeCompleted) {
        return Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('service_pro_accounts')
                        ->where(
                                '(DATE_FORMAT(completion_date, "%Y-%m-%d %H")<=:completion_date) and status=:status', [
                            ':completion_date' => $dateTimeCompleted,
                            ':status' => PaidServices::STATUS_SERVICE_ACTIVE
                                ])
                        ->queryAll();
    }

    public function findEnding($dateTimeCompleted) {
        return Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('service_pro_accounts')
                        ->where(
                                'DATE_FORMAT(completion_date, "%Y-%m-%d")=:date_ending and status=:status', [
                            ':date_ending' => $dateTimeCompleted,
                            ':status' => PaidServices::STATUS_SERVICE_ACTIVE
                                ])
                        ->queryAll();
    }

}
