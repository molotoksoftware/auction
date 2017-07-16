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


class TestCommand extends CConsoleCommand
{
    /**
     * Проверка компонента mail
     *
     * Вызов:
     * php yiic.php test mailComponent
     */
    public function actionMailComponent()
    {
        $message = new YiiMailMessage();
        $message->view = 'test';
        $message->setSubject('Тестовое писмо');
        $message->setBody([
            'text' => 'Текст пиьма asdada d as!',
        ], 'text/html');
//        $message->addTo('test@email.ru'); // Существующий емайл
//        $message->addTo('test@email.ru'); // Плохой емайл
        $message->addTo('test@test'); // Плохой емайл
        $message->setFrom(
            ['test@test' => 'demo Auction']
        );

        /** @var YiiMail $mail */
        $mail = Yii::app()->getComponent('mail');
        $r = $mail->send($message, $failedRecipients);
        var_dump($r, $failedRecipients);
    }

    /**
     * Проверка компонента mail2
     *
     * Вызов:
     * php yiic.php test mail2ComponentSimple
     */
    public function actionMailComponentSimple()
    {
        /** @var YiiMail $mail */
        $mail = Yii::app()->getComponent('mail');
        $r = $mail->sendSimple(
            'test@test',
            'test@test',
            'Тестовое писмо',
            'Текст пиьма!'
        );
        var_dump($r);
    }
}