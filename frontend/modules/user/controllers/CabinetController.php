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


class CabinetController extends FrontController
{
    public $layout = '//layouts/cabinet';

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
            array('allow',
                'actions' => array('index', 'viewed'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }

    public function actionIndex()
    {
        $this->layout = '//layouts/cabinet';
        $this->pageTitle = Yii::t('basic', 'Common');

        $this->render('index');
    }

    public function actionViewed($type, $id)
    {

        $model = Auction::model()->find('auction_id=:auction_id AND owner=:owner', array(':auction_id' => (int)$id, ':owner' => Yii::app()->user->id));
        if (!isset($model->auction_id)) {throw new CHttpException(404, "Access denied");}
        $url = '/auction/'.$model->auction_id;

        $this->pageTitle = Yii::t('basic', 'View statistic') . ': "'.$model->name.'"';

        $max_viewed = Yii::app()->db->createCommand()
            ->select('day_viewed')
            ->from('viewed_count')
            ->where('type=:type and auction_id=:auction_id', array(':type' => $type, ':auction_id' => $id))
            ->order('day_viewed DESC')
            ->queryRow();

        $sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('viewed_count')
            ->where('type=:type and auction_id=:auction_id', array(':type' => $type, ':auction_id' => $id));

        $count = Yii::app()->db->createCommand()
            ->select('COUNT(*)')
            ->from('viewed_count')
            ->where('type=:type and auction_id=:auction_id', array(':type' => $type, ':auction_id' => $id))
            ->queryScalar();

        $dataProvider = new CSqlDataProvider($sql, array(
            'totalItemCount' => $count,
            'keyField' => 'viewed_count_id',
            'sort' => array(
                'defaultOrder' => 'date_viewed DESC'
            ),
            'pagination' => array(
                'pageSize' => 30
            ),
        ));

        $this->render('viewed', array(
            'model' => $model, 
            'dataProvider' => $dataProvider, 
            'max_viewed' => $max_viewed,
            'count' => $count,
            'url' => $url
        ));
    }
}