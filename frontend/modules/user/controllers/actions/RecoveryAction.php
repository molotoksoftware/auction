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


class RecoveryAction extends CAction
{

    public function run()
    {

        if (!Yii::app()->user->isGuest) {
            $this->controller->redirect(Yii::app()->user->returnUrl);
        }

        $model = new RecoveryForm();
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'form-recovery') {
            echo CActiveForm::validate($model);
             Yii::app()->end();
        }
        // collect user input data
        if (isset($_POST['RecoveryForm'])) {
            $model->attributes = $_POST['RecoveryForm'];
            if ($model->validate()) {
                /** @var User $user */
                $user = $model->getUser();

                $new_password = substr(md5(uniqid(mt_rand(), true) . time()), 0, 6);
                $user->password = $user->hashPassword($new_password);
                $user->save();
                $this->onAfterPasswordReset($user, $new_password);

                $message = new YiiMailMessage();
                $message->view = 'recovery-pass';
                $message->setSubject(Yii::t('basic', 'Password recovery'));
                $message->setBody(
                    array(
                        'login' => $user->login,
                        'pass' => $new_password
                    ),
                    'text/html'
                );
                $message->addTo($user->email);
                $message->setFrom(
                    array(Yii::app()->params['adminEmail'] => CHtml::encode(Yii::app()->params['adminName']))
                );

                $mail = Yii::app()->getComponent('mail');

                if ($mail->send($message)) {
                    Yii::app()->user->setFlash('succes_sent', Yii::t('basic', 'New password has been sent to your e-mail')
                    );
                } else {
                     Yii::app()->user->setFlash('failure_sent', Yii::t('basic', '')
                     );
                }

            }
        }

        $this->controller->render('recovery', array(
            'model' => $model
        ));
    }

    public function onAfterPasswordReset(User $user, $password)
    {
        $event = new AfterPasswordResetEvent();
        $event->setUser($user);
        $event->setPassword($password);
        $this->getController()->onAfterPasswordReset($event);
    }
}