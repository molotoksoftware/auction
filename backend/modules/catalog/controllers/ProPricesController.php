<?php


/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://www.molotoksoftware.com/
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


class ProPricesController extends BackController
{

    public $defaultAction = 'index';

    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + delete, MultipleRemove'
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'index',
                    'MultipleRemove',
                    'delete',
                    'update',
                    'create'
                ),
                'roles' => array('admin', 'root'),
            ),
            array('deny'),
        );
    }

    public function actionIndex()
    {
        $model = new ProPrice('search');
        $model->unsetAttributes();

        if (isset($_GET['ProPrice'])) {
            $model->attributes = $_GET['ProPrice'];
        }

        if (isset($_GET['ajax'])) {
            $this->renderPartial(
                '_table_pro_price',
                array(
                    'model' => $model,
                )
            );
        } else {
            $this->render(
                'index',
                array(
                    'model' => $model,
                )
            );
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel('ProPrice', $id);
        $model->scenario = "update";
        $this->performAjaxValidation($model, 'form-pro-price');

        if (isset($_POST['ProPrice'])) {
            $model->attributes = $_POST['ProPrice'];
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Успешно создано');
                $this->redirect(array('/catalog/proPrices/index'));
            }
        }

        $this->render(
            'update',
            array(
                'model' => $model
            )
        );
    }

    public function actionCreate()
    {
        $model = new AdvertPricePublish('insert');
        $this->performAjaxValidation($model, 'form-advert-rates');

        if (isset($_POST['AdvertPricePublish'])) {
            $model->attributes = $_POST['AdvertPricePublish'];
            if ($model->save()) {

                Yii::app()->user->setFlash('success', 'Успешно создано');
                if ($_POST['submit'] == 'index') {
                    $this->redirect(array('/catalog/advertRates/index'));
                } else {
                    $this->refresh();
                }
            }
        }

        $this->render(
            'create',
            array(
                'model' => $model
            )
        );
    }

    public function actionDelete($id)
    {
        $deleted = $this->loadModel('AdvertPricePublish', $id)->delete();
        if ($deleted == 0) {
            RAjax::error(array('messages' => 'Ошыбка при удалении'));
        } else {
            RAjax::success(array('messages' => 'Лот успешно удален'));
        }
    }

    public function actionMultipleRemove()
    {
        $this->multipleRemove('AdvertPricePublish');
    }

}
