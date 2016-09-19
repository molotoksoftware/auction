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


class RegistrationAction extends CAction
{

    public function run()
    {

        if (!Yii::app()->user->isGuest){
            Yii::app()->controller->redirect(Yii::app()->homeUrl);
        }



        $model = new RegistrationForm();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'form-registration') {
			echo CActiveForm::validate($model);
		//	Yii::app()->end();
        }

        if (isset($_POST['RegistrationForm'])) {
            $model->attributes = $_POST['RegistrationForm'];
            if ($model->validate()) {

                $user = new User();
                $user->password = $user->hashPassword($model->password);
                $user->email = $model->email;
                $user->login = $model->login;
                $user->createtime = date('Y-m-d H:i:s');

                if ($user->save()) {

                    $identity = new UserIdentity($model->email, $model->password);
                    $identity->authenticate();
                    if ($identity->errorCode === UserIdentity::ERROR_NONE) {
                        Yii::app()->getUser()->login($identity);
                        Yii::app()->controller->redirect(Yii::app()->user->cabinetUrl);
                    } else {
                        throw new CException('error authenticate');
                    }
                } else {
                    throw new CException('error create new user');
                }
            }
        }

        $this->controller->render('registration', array('model' => $model));
    }

    /**
     * @param User             $user
     * @param RegistrationForm $model
     */
    public function onAfterRegistration(User $user, RegistrationForm $model)
    {
        $event = new AfterRegistrationEvent();
        $event->setUser($user);
        $event->setLogin($model->login);
        $event->setPassword($model->password);
        $this->getController()->onAfterRegistration($event);
    }
}

