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



class SettingsController extends BackController
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
            array(
                'allow',
                'actions' => array('common', 'pagesPro', 'settingsPro'),
                'roles' => array('admin', 'root'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }

    public function actionPagesPro()
    {
        if ($form = Yii::app()->request->getParam('Form')) {
            Yii::app()->db->createCommand()
                ->update(
                    'pages_pro',
                    array(
                        'text_pro_account' => $form['text_pro_account'],
                        'text_certified' => $form['text_certified']
                    )
                );
            Yii::app()->user->setFlash('success', 'Успешно сохранено');
        }

        $text = Yii::app()->db->createCommand()
            ->select('*')
            ->from('pages_pro')
            ->queryRow();
        if ($text == false) {
            throw new CHttpException(404);
        }

        $this->render(
            'pagesPro',
            array(
                'title' => 'Тексты',
                'text_pro_account' => $text['text_pro_account'],
                'text_certified' => $text['text_certified']
            )
        );
    }

    public function actionSettingsPro()
    {
        if ($configs = Yii::app()->request->getParam('Setting')) {

            foreach ($configs as $name => $value) {
                Setting::model()->updateAll(
                    array('value' => $value),
                    "name=:name and type=:type",
                    array(
                        ':name' => $name,
                        ':type' => Setting::TYPE_PRO
                    )
                );
            }
            Yii::app()->user->setFlash('success', 'Успешно сохранено');

        }
        $model = Setting::model()->getByType(Setting::TYPE_PRO)->findAll();

        $this->render(
            'common',
            array(
                'model' => $model,
                'title' => 'Настройки ПРО'
            )
        );
    }

    public function actionCommon()
    {

        if ($configs = Yii::app()->request->getParam('Setting')) {

            foreach ($configs as $name => $value) {
                Setting::model()->updateAll(
                    array('value' => $value),
                    "name=:name and type=:type",
                    array(
                        ':name' => $name,
                        ':type' => Setting::TYPE_COMMON
                    )
                );
            }
            Yii::app()->user->setFlash('success', 'Успешно сохранено');

        }
        $model = Setting::model()->getByType(Setting::TYPE_COMMON)->findAll();

        $this->render(
            'common',
            array(
                'model' => $model,
                'title' => 'Настройки сайта'
            )
        );
    }

}
