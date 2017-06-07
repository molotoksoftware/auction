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


class ProController extends FrontController
{

    public $layout = '//layouts/settings';

    public function filters()
    {
        return array(
            'accessControl',
            array(
                'ESetReturnUrlFilter'
            )
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }

    public function actionIndex()
    {
        $this->pageTitle = Yii::t('basic', 'PRO account');

        $text = Yii::app()->db->createCommand()->select('text_pro_account')->from('pages_pro')->queryRow();

        $this->render('index', [
            'text' => $text,
        ]);
    }

}
