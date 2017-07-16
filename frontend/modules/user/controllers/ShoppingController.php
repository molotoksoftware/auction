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


class ShoppingController extends FrontController
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
                'actions' => array('index', 'historyShopping', 'activeBets', 'notWonItems'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }

    public function actionIndex()
    {
        $this->pageTitle = Yii::t('basic', 'Purchases');
        $this->render('//user/shopping/index');
    }

    public function actionHistoryShopping()
    {
        $this->pageTitle = Yii::t('basic', 'Purchase history');

        $request = Yii::app()->getRequest();
        $cookies = $request->getCookies();

        if ($request->getQuery('size')) {
            $this->saveCookieInf('u_history_shopping_page_size', $request->getQuery('size'));
        } else {
            if (isset($request->cookies['u_history_shopping_page_size']->value)) {
                $_GET['size'] = Yii::app()->request->cookies['u_history_shopping_page_size']->value;
            }
        }

        $gridPageSize = GridView::pageSizeDropDown();

        $this->render(
            '//user/shopping/_history_shopping_table',
            array(
                'limit' => GridView::getPageSize(),
                'gridViewTemplate'   => "{items}\n<hr><div class='row'><div class='col-xs-2'>$gridPageSize</div><div class='col-xs-10 text-right'>{pager}</div></div><hr>",
                'gridViewPager'      => ['class' => 'CLinkPager', 
                        'maxButtonCount' => 5,
                        'firstPageLabel' => Yii::t('basic', 'First page'),
                        'lastPageLabel' => Yii::t('basic', 'Last page'),
                        'selectedPageCssClass' => 'active',
                        'prevPageLabel' => '&lt; ',
                        'nextPageLabel' => ' &gt;',
                        'header' => '',
                        'footer' => '',
                        'cssFile' => false,
                        'htmlOptions' => ['class' => 'pagination']],
            )
        );
    }

    public function actionActiveBets()
    {
        $this->pageTitle = Yii::t('basic', 'Active bids');

        $this->render(
            '//user/shopping/_active_bets_table',
            array(
                'limit' => null
            )
        );
        $counter = new ActiveBetsItem();
        $counter->dec(Yii::app()->user->id);
    }

    public function actionNotWonItems()
    {
        $this->pageTitle = Yii::t('basic', 'Didn\'t win');

        $this->render(
            '//user/shopping/_not_won_items_table',
            array(
                'limit' => null
            )
        );
        $counter = new NotWonItems();
        $counter->dec(Yii::app()->user->id);
    }


}
