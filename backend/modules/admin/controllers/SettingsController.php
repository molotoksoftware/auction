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
                'actions' => array('common', 'pagesPro', 'settingsPro', 'localization'),
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
                'title' => Yii::t('common', 'Texts'),
                'text_pro_account' => $text['text_pro_account'],
                'text_certified' => $text['text_certified']
            )
        );
    }

    public function actionSettingsPro()
    {
        if ($configs = Yii::app()->request->getParam('Setting')) {

            Setting::updateSettings($configs, Setting::TYPE_PRO);

        }
        $model = Setting::model()->getByType(Setting::TYPE_PRO)->findAll();

        $this->render(
            'common',
            array(
                'model' => $model,
                'title' => Yii::t('common', 'PRO settings')
            )
        );
    }

    public function actionLocalization()
    {
        if ($configs = Yii::app()->request->getParam('Setting')) {
            $configs['defaultLocation'] = [];
            if(isset($configs['id_country'])) {
                $configs['defaultLocation']['country'] = $configs['id_country'];
                unset($configs['id_country']);
            }

            if(isset($configs['id_region'])) {
                $configs['defaultLocation']['region'] = $configs['id_region'];
                unset($configs['id_region']);
            }

            if(isset($configs['id_city'])) {
                $configs['defaultLocation']['city'] = $configs['id_city'];
                unset($configs['id_city']);
            }

            $configs['defaultLocation'] = json_encode($configs['defaultLocation']);

            Setting::updateSettings($configs, Setting::TYPE_LOCALIZATION);
        }

        $model = Setting::model()->getByType(Setting::TYPE_LOCALIZATION)->findAll();

        $this->render(
            'localization',
            [
                'model' => $model,
                'title' => Yii::t('common', 'Localization')
            ]
        );
    }

    public function actionCommon()
    {

        if ($configs = Yii::app()->request->getParam('Setting')) {
            Setting::updateSettings($configs, Setting::TYPE_COMMON);
        }
        $model = Setting::model()->getByType(Setting::TYPE_COMMON)->findAll();

        $this->render(
            'common',
            array(
                'model' => $model,
                'title' => Yii::t('common', 'Main settings')
            )
        );
    }

}
