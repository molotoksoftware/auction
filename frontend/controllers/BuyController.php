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
 * Class BuyController
 */
class BuyController extends FrontController
{
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
                'actions' => array('pro'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }




    public function actionPro($id)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {

            $userModel = Getter::userModel();
            if ($userModel['pro']==1) {
                $paidServices = PaidServices::model()->findByAttributes(['user_id' => $userModel['user_id'], 'status' => 1]);
                $paidServices->updateProAccount(Yii::app()->user->getModel(), (int)$id);

                if ($paidServices->update()) {
                    $transaction->commit();

                    Yii::app()->user->setFlash(
                        'successful',
                        Yii::t('basic', 'PRO account has been renewed')

                    );
                    Yii::app()->controller->redirect('/user/pro/index');
                }

            } elseif ($userModel['pro']==0) {
                $paidServices = new PaidServices();
                $paidServices->createProAccount(Yii::app()->user->getModel(), (int)$id);
                if ($paidServices->save()) {
                    $transaction->commit();

                    Yii::app()->user->setFlash(
                        'successful',
                        Yii::t('basic', 'PRO account has been activated')
                    );
                    Yii::app()->controller->redirect('/user/pro/index');
                }
            }

        } catch (Exception $e) {
            $transaction->rollBack();

            if ($e->getCode() == 12) {
                Yii::app()->user->setFlash('failure_pay', $e->getMessage());
                Yii::app()->controller->redirect('/user/balance/index');

            } else {
                echo $e->getMessage();
                die();
            }
        }
    }
}
