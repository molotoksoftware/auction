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


class QuestionsController extends FrontController
{

    public $layout = '//layouts/cabinet';

    const COUNT_PER_QUESTIONS = 10;

    public function filters()
    {
        return array(
            'accessControl',
            'postOnly + create',
            'ajaxOnly + create, manager',
            array(
                'ESetReturnUrlFilter'
            )
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index', 'manager', 'create'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }

    public function actionIndex()
    {

        $this->pageTitle = Yii::t('basic', 'Questions about items');

        $dataProvider = new CActiveDataProvider(
            Questions::model()->byUserId(Yii::app()->user->id), array(
            'pagination' => array(
                'class' => 'CastomPagination',
                'pageVar' => 'p',
                'method' => $_POST,
                'pageSize' => self::COUNT_PER_QUESTIONS
            ),
            'sort' => array(
                'defaultOrder' => 'created DESC'
            )
        ));

        $jsonData = array();
        foreach ($dataProvider->getData() as $key => $value) {

            $owner_name = $value->owner->nick?$value->owner->nick:$value->owner->login;
            $owner_telephone = !empty($value->owner->telephone)?$value->owner->telephone:Yii::t('basic', 'No');

            $jsonData[] = array(
                'id' => $value->id,
                'item_id' => $value->item_id,
                'item_name' => $value->auction->name,
                'owner_name' => $owner_name,
                'owner_login' => $value->owner->login,
                'owner_email' => $value->owner->email,
                'owner_telephone' => $owner_telephone,
                'text' => $value->text,
                'read' => (bool)$value->read,
                'date' => Yii::app()->dateFormatter->format('dd MMMM yyyy H:m:s', $value->created)
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

    public function actionManager($action, $elements)
    {
        $elem = CJSON::decode($elements);
        switch ($action) {
            case 'removes':
                if (!empty($elem)) {
                    foreach ($elem as $i) {
                        Questions::model()->updateByPk(
                            (int)$i,
                            array(
                                'status' => 2
                            )
                        );
                    }
                }
                break;
            case 'read':
                if (!empty($elem)) {
                    foreach ($elem as $i) {
                        Questions::model()->updateByPk(
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
                        Questions::model()->updateByPk(
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

    public function actionCreate() {

        if ($request = Yii::app()->getRequest()->getPost('FormQuestion')) {

            if ($request['owner_id'] == Yii::app()->user->id) 
                RAjax::error();

            $newQuestion = new Questions();

            $newQuestion->author_id = Yii::app()->user->id;
            $newQuestion->item_id = (int)$request['auction_id'];
            $newQuestion->owner_id = (int)$request['owner_id'];
            $newQuestion->text = htmlspecialchars($request['text']);
            $newQuestion->status = Questions::STATUS_ACTIVE;
            $newQuestion->read = Questions::UNREAD_STATUS;

            if ($newQuestion->save()) {
                RAjax::success();
            } else {
                RAjax::error($newQuestion->getErrors());
            }

        }

    }

}
