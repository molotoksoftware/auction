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
class LentaController extends FrontController
{
    public $layout = '//layouts/cabinet';
    public $defaultPage = 25;

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
                'actions' => array('index', 'del'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }

    public function actionIndex()
    {
        $this->pageTitle = Yii::t('basic', 'Favorite sellers');
        $this->layout = '//layouts/cabinet';

        if (isset($_GET['size'])) {
            if (preg_match("/^[0-9]+$/", $_GET['size'])) {
                $cookie = new CHttpCookie('item_on_page', $_GET['size']);
                $cookie->expire = time() + 3600 * 24 * 180;
                Yii::app()->request->cookies['item_on_page'] = $cookie;
            }
        }

        if (isset(Yii::app()->request->cookies['item_on_page']->value)) {
            if (preg_match("/^[0-9]+$/", Yii::app()->request->cookies['item_on_page']->value)) {
                $num_page_size = Yii::app()->request->cookies['item_on_page']->value;
            }
        } else {
            $num_page_size = $this->defaultPage;
        }

        $params = array(
            ':user_id' => Yii::app()->user->id,
            ':status' => Auction::ST_ACTIVE,
        );

        $prod_all = TrackOwners::getListUserForOwner();

        $all_count = TrackOwners::getCountAuctionsFromTrackUsers($params);

        $this->render('index', [
            'num_page_size' => $num_page_size,
            'prod_all' => $prod_all,
            'all_count' => $all_count,
            'params' => $params,
            'template' => '{items}',
            'sql_fav' => TrackOwners::getLotData(),
        ]);
    }

    public function actionDel($owner)
    {
        TrackOwners::model()->deleteAll('owner=:owner AND id_user=:id_user', array(':owner' => $owner, ':id_user' => Yii::app()->user->id));
        $this->redirect('/user/lenta/index');
    }
}