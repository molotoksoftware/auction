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


class AdminController extends BackController
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
                'actions' => array('login', 'logout'),
                'users' => array('*'),
            ),
            array('allow',
                'actions' => array('index', 'update', 'create', 'delete', 'MultipleRemove'),
                'roles' => array('admin', 'root'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }

    public function actionLogin()
    {
        if (Yii::app()->user->isGuest) {
            $model = new LoginForm();
            //ajax validate
            $this->performAjaxValidation($model, 'form-login');


            if (Yii::app()->request->isPostRequest && isset($_POST['LoginForm'])) {
                $model->attributes = $_POST['LoginForm'];

                if ($model->validate() && $model->login()) {
                    if (Yii::app()->user->returnUrl) {

                        $this->redirect(array(Yii::app()->params['adminUrl']));
                    } else {
                        $this->redirect(array(Yii::app()->params['adminUrl']));
                    }
                }//end if validate
            }//end if isset

            $this->render('login', array('model' => $model));

        } else {
            $this->redirect(array(Yii::app()->params['adminUrl']));
        }
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(array(Yii::app()->params['adminUrl']));
    }

    public function actionCreate()
    {
        $model = new Admins();
        $this->performAjaxValidation($model, 'form-user');

        if (Yii::app()->request->isPostRequest && isset($_POST['Admins'])) {
            $model->attributes = $_POST['Admins'];

            if ($model->validate()) {

                $salt = $model->generateSalt();
                $model->setAttributes(array(
                    'salt' => $salt,
                    'password' => $model->hashPassword($model->password, $salt),
                ));

                if ($model->save(false)) {
                    Yii::app()->user->setFlash('success', 'Пользователь успешно создан');

                    if ($_POST['submit'] == 'index') {
                        $this->redirect(array('/admin/admin/index'));
                    } else {
                        $this->refresh();
                    }
                } else {
                    print_r($model->getErrors());
                }
            }
        }

        $this->render('create', array('model' => $model));
    }

    public function actionIndex()
    {
        $user = new Admins('search');
        $user->unsetAttributes();

        if (isset($_GET['Admins'])) {
            $user->attributes = $_GET['Admins'];
        }


        if (isset($_GET['ajax'])) {
            $this->renderPartial('_table_user', array(
                'model' => $user,
            ));
        } else {
            $this->render('index', array(
                'model' => $user,
            ));
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->_loadModel($id);

        $model->scenario = "update";
        $this->performAjaxValidation($model, 'form-user');
        if (Yii::app()->request->isPostRequest && $_POST['Admins']) {
            $model->attributes = $_POST['Admins'];
            if ($model->validate()) {
                $model->save(false);
                Yii::app()->user->setFlash('success', 'Изменения успешно применены');
                $this->redirect(array('/admin/admin/index'));
            }
        }

        $this->render('update', array(
            'model' => $model
        ));
    }

    protected function _loadModel($id)
    {
        if (!$model = Admins::model()->findByPk($id)) {
            if (Yii::app()->request->isAjaxRequest) {
                RAjax::error(array('messages' => 'Пользователь не существует'));
            } else {
                throw new CHttpException(404, 'Пользователь не существует');
            }
        }
        return $model;
    }

    public function actionDelete($id)
    {
        $params = array('user_id' => $id);
        $deleted = 0;
        $deleted = $this->_loadModel($id)->delete();


        if ($deleted == 0) {
            RAjax::error(array('messages' => 'Ошибка при удалении'));
        } else {
            RAjax::success(array('messages' => 'Успешно удалено'));
        }
    }

}
