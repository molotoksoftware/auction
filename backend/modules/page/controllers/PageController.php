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


class PageController extends BackController
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
            array('allow',
                'actions' => array('index', 'MultipleRemove', 'delete', 'update', 'create', 'view'),
                'roles' => array('admin', 'root'),
            ),
            array('deny'),
        );
    }

    /**
     * Просмотр в таблице всех страниц.
     *
     */
    public function actionIndex()
    {
        $page = new Page('search');
        $page->unsetAttributes();

        if (isset($_GET['Page'])) {
            $page->attributes = $_GET['Page'];
        }

        if (isset($_GET['ajax'])) {
            $this->renderPartial('_table_pages', array(
                'model' => $page,
            ));
        } else {
            $this->render('index', array(
                'model' => $page,
            ));
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->_loadModel($id);
        $model->scenario = "update";
        $this->performAjaxValidation($model, 'form-page');
        if (isset($_POST['Page'])) {

            $model->attributes = $_POST['Page'];

            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Страница успешно сохранена');
                $this->redirect(array('/page/page/index'));
            }
        }

        $this->render('update', array('model' => $model));
    }

    public function actionCreate()
    {
        $model = new Page('insert');
        $this->performAjaxValidation($model, 'form-page');

        if (isset($_POST['Page'])) {
            $model->attributes = $_POST['Page'];

            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Страница успешно создана');
                if ($_POST['submit'] == 'index') {
                    $this->redirect(array('/page/page/index'));
                } else {
                    $this->refresh();
                }
            }
        }

        $this->render('create', array('model' => $model));
    }

    public function actionDelete($id)
    {
        $deleted = $this->_loadModel($id)->delete();
        if ($deleted == 0) {
            RAjax::error(array('messages' => 'Ошыбка при удалении'));
        } else {
            RAjax::success(array('messages' => 'Страница успешно удалена'));
        }
    }

    private function _loadModel($id)
    {
        if (!$model = Page::model()->findByPk($id)) {
            if (Yii::app()->request->isAjaxRequest) {
                RAjax::error(array('messages' => 'Страница не существует'));
            } else {
                throw new CHttpException(404, 'Страница не существует');
            }
        }
        return $model;
    }

    public function actionMultipleRemove()
    {
        $this->multipleRemove('Page');
    }

}
