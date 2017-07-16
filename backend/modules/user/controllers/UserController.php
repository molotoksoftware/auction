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
class UserController extends BackController {

    public function filters() {
        return array(
            'accessControl'
        );
    }

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('login', 'logout'),
                'users' => array('*'),
            ),
            array('allow',
                'actions' => array('index', 'profile', 'update', 'create', 'delete', 'MultipleRemove', 'toggle', 'ban', 'sameUserIPs'),
                'roles' => array('admin', 'root'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }

    public function actionIndex() {
        $user = new User('search');
        $user->unsetAttributes();

        if (isset($_GET['User'])) {
            $user->attributes = $_GET['User'];
        }


        if (isset($_GET['ajax'])) {
            $this->renderPartial('_table_users', array(
                'model' => $user,
            ));
        } else {
            $this->render('index', array(
                'model' => $user,
            ));
        }
    }

    public function actionToggle($id, $attribute) {
        if (!Yii::app()->request->isPostRequest)
            throw new CHttpException(400, 'Некорректный запрос');
        if (!in_array($attribute, array('certified')))
            throw new CHttpException(400, 'Некорректный запрос');

        $model = $this->_loadModel($id);
        $model->$attribute = $model->$attribute ? 0 : 1;
        $model->save();

        if (!Yii::app()->request->isAjaxRequest)
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    // Бан пользователя
    public function actionBan($id, $attribute) {
        if (!Yii::app()->request->isPostRequest)
            throw new CHttpException(400, 'Некорректный запрос');
        if (!in_array($attribute, array('ban')))
            throw new CHttpException(400, 'Некорректный запрос');

        $model = $this->_loadModel($id);
        $model->$attribute = $model->$attribute ? 0 : 1;
        $model->save();

        if (!Yii::app()->request->isAjaxRequest)
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function actionCreate() {
        $model = new User();
        $this->performAjaxValidation($model, 'form-user');

        if (Yii::app()->request->isPostRequest && isset($_POST['User'])) {
            $model->attributes = $_POST['User'];
            $model->birthday = date('Y-m-d', CDateTimeParser::parse($_POST['User']['birthday'], 'dd-MM-yyyy'));
            $model->password = $model->hashPassword($model->password);

            if ($model->validate()) {

                if ($model->save(false)) {
                    Yii::app()->user->setFlash('success', 'Пользователь успешно создан');

                    if ($_POST['submit'] == 'index') {
                        $this->redirect(array('/user/user/index'));
                    } else {
                        $this->refresh();
                    }
                }
            }
        }

        $this->render('create', array('model' => $model));
    }

    public function actionProfile() {
        $model = Users::model()->findByPk(11);

        $this->render('profile', array(
            'model' => $model
        ));
    }

    public function actionUpdate($id) {
        $model = $this->_loadModel($id);
        $oldPassword = $model->password;

        $model->scenario = "update";
        $this->performAjaxValidation($model, 'form-user');

        if (Yii::app()->request->isPostRequest && $_POST['User']) {
            $model->attributes = $_POST['User'];
            $model->birthday = date('Y-m-d', CDateTimeParser::parse($_POST['User']['birthday'], 'dd-MM-yyyy'));

            if (!$model->password) {
                $model->password = $oldPassword;
            } else {
                $model->password = $model->hashPassword($model->password);
            }

            if ($model->validate()) {
                $model->save(false);
                Yii::app()->user->setFlash('success', 'Изменения успешно применены');
                $this->redirect(array('/user/user/index'));
            }
        }

        $model->password = false;

        $this->render('update', array(
            'model' => $model
        ));
    }

    protected function _loadModel($id) {
        if (!$model = User::model()->findByPk($id)) {
            if (Yii::app()->request->isAjaxRequest) {
                RAjax::error(array('messages' => 'Пользователь не существует'));
            } else {
                throw new CHttpException(404, 'Пользователь не существует');
            }
        }
        return $model;
    }

    public function actionMultipleRemove() {
        $this->multipleRemove('User');
    }

    public function actionDelete($id) {
        $deleted = 0;
        $deleted = $this->_loadModel($id)->delete();

        if ($deleted == 0) {
            RAjax::error(array('messages' => 'Ошибка при удалении'));
        } else {
            RAjax::success(array('messages' => 'Успешно удалено'));
        }
    }

}
