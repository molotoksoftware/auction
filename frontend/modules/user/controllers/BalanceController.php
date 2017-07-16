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


class BalanceController extends FrontController {

    public $layout = '//layouts/settings';

    public function filters() {
        return array(
            'accessControl'
        );
    }

    public function accessRules() {
        return array(
            array(
                'allow',
                'actions' => array('index', 'recharge', 'payment'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }

    public function actionIndex($payment = false) {
        $this->pageTitle = Yii::t('basic', 'Payments');
        $this->layout = '//layouts/settings';

        if ($payment == 'fail') {
            Yii::app()->user->setFlash('failure_pay', Yii::t('basic', 'Payment received!'));
            Yii::app()->controller->redirect('/user/balance/index');
        }
        if ($payment == 'success') {
            Yii::app()->user->setFlash('global', Yii::t('basic', 'Payment is not received!'));
            Yii::app()->controller->redirect('/user/balance/index');
        }

        $count = BalanceHistory::model()->findAllByAttributes(['user_id' => Yii::app()->user->id]);

        $criteria = new CDbCriteria();
        $criteria->condition = 'user_id=:user_id';
        $criteria->params = [':user_id' => Yii::app()->user->id];
        $criteria->order = '`created_on` DESC';
        $pages = new CPagination(count($count));
        $pages->pageSize = 25;
        $pages->applyLimit($criteria);

        $balance_history = BalanceHistory::model()->findAll($criteria);

        $payment = new FormPayment();

        $this->render('//user/balance/index', ['balance' => $balance_history, 'pages' => $pages, 'payment' => $payment]);
    }

    public function actionRecharge() {
        $this->render('//user/balance/recharge');
    }

    public function actionPayment() {
        $this->pageTitle = Yii::t('basic', 'Payments');
        $this->layout = '//layouts/settings';

        $this->render('//user/balance/payment');
    }

}
