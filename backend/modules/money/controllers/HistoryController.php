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
class HistoryController extends BackController {

    public function filters() {
        return array(
            'accessControl'
        );
    }

    public function accessRules() {
        return array(
            array(
                'allow',
                'actions' => array('order', 'recharge'),
                'roles' => array('admin', 'root'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }

    public function actionRecharge() {

        $model = new BalanceHistory('searchAdmin');
        $model->unsetAttributes();

        if (isset($_GET['BalanceHistory'])) {
            $model->attributes = $_GET['BalanceHistory'];
        }

        if (isset($_GET['ajax'])) {
            $this->renderPartial('_table_history_recharge', ['model' => $model]);
        } else {
            $this->render('recharge', ['model' => $model]);
        }
    }

    public function actionOrder() {
        $model = new PaidServices('search');
        $model->unsetAttributes();

        if (isset($_GET['PaidServices'])) {
            $model->attributes = $_GET['PaidServices'];
        }

        $criteria = new CDbCriteria(array(
            'condition' => 'user_id<>0'
        ));

        $dataProvider = new CActiveDataProvider($model, array(
            'pagination' => array(
                'pageSize' => 25,
            ),
            'sort' => array(
                'defaultOrder' => 'created_date DESC'
            ),
            'criteria' => $criteria,
        ));

        if (isset($_GET['ajax'])) {
            $this->renderPartial(
                    '_table_history_order', array(
                'dataProvider' => $dataProvider,
                    )
            );
        } else {
            $this->render(
                    'order', array(
                'dataProvider' => $dataProvider,
                    )
            );
        }
    }

}
