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


class SystemNotificationController extends FrontController
{

    public $layout = '//layouts/cabinet';

    const COUNT_PER_NOTIFY = 10;

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index', 'manager'),
                'users' => array('@')
            ),
            array('deny'),
        );
    }


    public function actionManager($action, $elements)
    {
        $elem = CJSON::decode($elements);
        switch ($action) {
            case 'removes':
                if (!empty($elem)) {
                    foreach ($elem as $i) {
                        SystemNotification::model()->deleteByPk((int)$i);
                    }
                }
                break;
            case 'read':
                if (!empty($elem)) {
                    foreach ($elem as $i) {
                        SystemNotification::model()->updateByPk(
                            (int)$i,
                            array(
                                'read' => 1
                            )
                        );
                    }
                }
                break;
            case 'unread':
                if (!empty($elem)) {
                    foreach ($elem as $i) {
                        SystemNotification::model()->updateByPk(
                            (int)$i,
                            array(
                                'read' => 0
                            )
                        );
                    }
                }
                break;

        }

        RAjax::success();
    }

    public function actionIndex()
    {
        $this->pageTitle = Yii::t('basic', 'System Notifications');

        $dataProvider = new CActiveDataProvider(
            SystemNotification::model()->byUserId(Yii::app()->user->id), array(
            'pagination' => array(
                'class' => 'CastomPagination',
                'pageVar' => 'p',
                'method' => $_POST,
                'pageSize' => self::COUNT_PER_NOTIFY
            ),
            'sort' => array(
                'defaultOrder' => 'date_created DESC'
            )
        ));


        $jsonData = array();
        foreach ($dataProvider->getData() as $key => $value) {
            $jsonData[] = array(
                'id' => $value->id,
                'text' => $value->text,
                'read' => (bool)$value->read,
                'date' => Yii::app()->dateFormatter->format('dd MMMM yyyy H:m:s', $value->date_created)
            );
        }

        $data = array(
            'data' => $jsonData,
            'item_count' => (int)$dataProvider->getTotalItemCount(),
            'current_page' => (int)Yii::app()->request->getPost('p', 0)
        );


        if (Yii::app()->request->isAjaxRequest && isset($_POST['get-content']) && $_POST['get-content'] === 'scroll') {
            RAjax::data($data);
        }


        $this->render(
            'index',
            array(
                'pagination' => $dataProvider->getPagination()
            )
        );
    }

}
