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


class FavoritesController extends FrontController
{

    public $layout = '//layouts/cabinet';

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
                'actions' => array('index', 'delete', 'items'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }

    public function actionDelete()
    {
        if (isset($_GET['data'])) {
            $removes = CJSON::decode($_GET['data']);
            if (!empty($removes)) {
                foreach ($removes as $i) {
                    Yii::app()->db->createCommand()
                        ->delete(
                            'favorites',
                            'favorite_id=:id and user_id=:user',
                            array(
                                ':id' => (int)$i,
                                ':user' => Yii::app()->user->id
                            )
                        );
                }

            }
            RAjax::success();
        }
    }

    public function actionItems()
    {
        $this->pageTitle = Yii::t('basic', 'Favorite items');
        $this->render('items');
    }
}
