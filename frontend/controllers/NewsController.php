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


class NewsController extends FrontController
{

    const DEFAULT_PAGE_SIZE = 20;
    public $defaultAction = 'index';
    
    public function actions()
    {
        return array(
            'static' => array(
                'class' => 'CViewAction',
                'basePath' => 'static',
                'layout' => 'application.www.themes.' . Yii::app()->theme->name . '.views.layouts.main'
            ),
        );
    }

    public function behaviors()
    {
        return array(
            'seo' => array(
                'class' => 'common.extensions.seo.SeoControllerBehavior',
                'defaultAttributeTitle' => 'title',
                'titleAttribute' => 'meta_title',
                'descriptionAttribute' => 'meta_description',
                'keywordsAttribute' => 'meta_keywords'
            ),
        );

    }

    public function actionView($alias)
    {
        $this->layout = 'common';

        $model = News::model()->published()->find('alias=:alias', array(':alias' => $alias));
        if (!isset($model)) {
            throw new CHttpException(404);
        }

        $this->registerSEO($model);
        $this->render('view', array('model' => $model));
    }

    public function actionIndex()
    {

        $this->pageTitle = Yii::t('basic', 'News');
        $this->layout = 'common';
        $data = new CActiveDataProvider('News', array(
            'pagination' => array(
                'pageSize' => self::DEFAULT_PAGE_SIZE,
                'pageVar' => 'page',
            ),
            'sort' => array(
                'defaultOrder' => 'date DESC'
            ),
        ));

        $this->render('index', array('data' => $data));
    }

}