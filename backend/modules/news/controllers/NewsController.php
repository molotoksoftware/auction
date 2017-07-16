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


class NewsController extends BackController
{

    public function filters()
    {
        return array(
            'accessControl'
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('toggle', 'create'),
                'roles' => array('writeNews'),
            ),
            array('allow',
                'actions' => array('getCommentsForNotifier', 'test', 'toggle', 'index', 'MultipleRemove', 'delete', 'update', 'view'),
                'roles' => array('admin'),
            ),
            array('allow',
                'actions' => array('getCommentsForNotifier', 'test', 'toggle', 'index', 'MultipleRemove', 'delete', 'update', 'create', 'view'),
                'roles' => array('admin', 'root'),
            ),
            array('deny'),
        );
    }

    public function actionToggle($id, $attribute)
    {
        if (!Yii::app()->request->isPostRequest)
            throw new CHttpException(400, 'Некорректный запрос');
        if (!in_array($attribute, array('status')))
            throw new CHttpException(400, 'Некорректный запрос');

        $model = $this->_loadModel($id);
        $model->$attribute = $model->$attribute ? 0 : 1;
        $model->save();

        if (!Yii::app()->request->isAjaxRequest)
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function actionIndex()
    {
        $news = new News('search');
        $news->unsetAttributes();

        if (isset($_GET['News'])) {
            $news->attributes = $_GET['News'];
        }

        if (isset($_GET['ajax'])) {
            $this->renderPartial('_table_news', array(
                'model' => $news,
            ));
        } else {
            $this->render('index', array(
                'model' => $news,
            ));
        }
    }

    public function actionCreate()
    {
        $model = new News('insert');
        $this->performAjaxValidation($model, 'form-news');

        if (isset($_POST['News'])) {
            $model->attributes = $_POST['News'];

            if (!empty($model->date)) {
                $date = date('Y-m-d H:i:s', CDateTimeParser::parse($model->date, 'dd-MM-yyyy', array('hour' => 0, 'minute' => 0, 'second' => 0)));
                $model->date = $date;
            }


            if ($model->validate()) {
                $model->save();
                Yii::app()->user->setFlash('success', 'Новость успешно создана');
                if ($_POST['submit'] == 'index') {
                    $this->redirect(array('/news/news/index'));
                } else {
                    $this->refresh();
                }
            }
        }

        $this->render('create', array('model' => $model));
    }

    public function actionView($id)
    {
        $model = $this->_loadModel($id);
        $this->render('view', array(
            'model' => $model
        ));
    }

    public function actionUpdate($id)
    {

        $model = $this->_loadModel($id);
        $model->scenario = "update";
        $this->performAjaxValidation($model, 'form-news');


        if (Yii::app()->request->isPostRequest && $_POST['News']) {
            $model->attributes = $_POST['News'];
            if (!empty($model->date)) {
                $date = date('Y-m-d H:i:s', CDateTimeParser::parse($model->date, 'dd-MM-yyyy', array('hour' => 0, 'minute' => 0, 'second' => 0)));
                $model->date = $date;
            }

            if ($model->validate()) {

                $model->save();
                Yii::app()->user->setFlash('success', 'Изменения успешно применены');

                $this->redirect(array('/news/news/index'));
            }
        }

        $this->render('update', array(
            'model' => $model
        ));
    }

    protected function _loadModel($id)
    {
        //with('counts_comments', 'comments')
        if (!$model = News::model()->findByPk($id)) {
            if (Yii::app()->request->isAjaxRequest) {
                RAjax::error(array('messages' => 'Новость не существует'));
            } else {
                throw new CHttpException(404, 'Новость не существует');
            }
        }
        return $model;
    }

    public function actionMultipleRemove()
    {
        $this->multipleRemove('News');
    }

    public function actionDelete($id)
    {
        $deleted = $this->_loadModel($id)->delete();
        if ($deleted == 0) {
            RAjax::error(array('messages' => 'Ошибка при удалении'));
        } else {
            RAjax::success(array('messages' => 'Успешно удалено'));
        }
    }
}
