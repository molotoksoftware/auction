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
class SalesController extends FrontController
{

    public $layout = '//layouts/cabinet';

    public function filters()
    {
        return [
            'accessControl',
        ];
    }

    public function accessRules()
    {
        return [
            ['allow',
                'actions' => [
                    'completedItems',
                    'activeItems',
                    'soldItems',
                    'workWithLots',
                    'cancelLots',
                    'del_lot',
                    'completeDeal',
                    'bulkCompleteDeal',
                    'markDeleted',
                ],
                'users' => ['@'],
            ],
            ['deny'],
        ];
    }

    public function actionActiveItems()
    {
        $request = Yii::app()->getRequest();

        if (isset($_GET['sort'])) {
            $this->saveCookieInf('u_active_items_sort', $_GET['sort']);
        } else {
            if (isset(Yii::app()->request->cookies['u_active_items_sort']->value))
                $_GET['sort'] = Yii::app()->request->cookies['u_active_items_sort']->value;
        }
        if ($request->getQuery('size')) {
            $this->saveCookieInf('u_active_items_page_size', $request->getQuery('size'));
        } else {
            if (isset($request->cookies['u_active_items_page_size']->value)) {
                $_GET['size'] = Yii::app()->request->cookies['u_active_items_page_size']->value;
            }
        }

        $this->pageTitle = Yii::t('basic', 'Active items');

        $auction = new Auction;
        $auctionData = $request->getQuery('Auction');
        $searchAuctionCategoryIds = [];
        if ($auctionData) {
            $auction->attributes = $auctionData;
            if (array_key_exists('category_id', $auctionData)) {
                $selectedCategoryId = $auctionData['category_id'];
                $auction->category_id = $selectedCategoryId;
                $selectedCategoryId = (int)$selectedCategoryId;
                if ($selectedCategoryId > 0) {
                    /** @var Category[] $descendants */
                    $selectedCategory = Category::model()->findByPk($selectedCategoryId);
                    $descendants = $selectedCategory->descendants()->findAll();
                    $searchAuctionCategoryIds = array_map(function (Category $eachCategory) {
                        return $eachCategory->category_id;
                    }, $descendants);
                    $searchAuctionCategoryIds[] = $selectedCategoryId;
                    $searchAuctionCategoryIds[] = 0;
                }
            }
        }

        $userCategories = Category::getUserCategoriesHavingLotsByStatus(
            Yii::app()->user->id, Auction::ST_ACTIVE
        );
        $userCategoriesList = Category::categoriesToFormattedDropDownArray($userCategories);
        $gridPageSize = GridView::pageSizeDropDown();
        $gridViewAfterAjaxUpdate = 'function(gridId, html, et, err) { if(err === undefined) {
            resetGridSelectAllChk(); initGrid();
        }}';

        $this->render('//user/sales/_active_lots_table', [
            'limit' => GridView::getPageSize(),
            'auction' => $auction,
            'gridViewTemplate' => "{items}\n<hr><div class='row'><div class='col-xs-2'>$gridPageSize</div><div class='col-xs-10 text-right'>{pager}</div></div><hr>",
            'userCategoriesList' => $userCategoriesList,
            'searchAuctionCategoryIds' => $searchAuctionCategoryIds,
            'gridViewPager' => ['class' => 'CLinkPager',
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
            'gridViewSummaryText' => Yii::t('basic', 'Showed {start} to {end}. All {count}'),
            'gridViewAfterAjaxUpdate' => $gridViewAfterAjaxUpdate,
        ]);

        $counter = new ActiveLots();
        $counter->dec(Yii::app()->user->id);
    }


    public function actionCompletedItems()
    {
        /** @var CHttpRequest $request */
        $request = Yii::app()->getRequest();

        if (isset($_GET['sort'])) {
            $this->saveCookieInf('u_completed_items_sort', $_GET['sort']);
        } else {
            if (isset(Yii::app()->request->cookies['u_completed_items_sort']->value))
                $_GET['sort'] = Yii::app()->request->cookies['u_completed_items_sort']->value;
        }
        if ($request->getQuery('size')) {
            $this->saveCookieInf('u_completed_items_page_size', $request->getQuery('size'));
        } else {
            if (isset($request->cookies['u_completed_items_page_size']->value)) {
                $_GET['size'] = Yii::app()->request->cookies['u_completed_items_page_size']->value;
            }
        }

        $this->pageTitle = Yii::t('basic', 'Unsold');

        $auction = new Auction;
        $auctionData = $request->getQuery('Auction');
        $searchAuctionCategoryIds = [];
        if ($auctionData) {
            $auction->attributes = $auctionData;
            if (array_key_exists('category_id', $auctionData)) {
                $selectedCategoryId = $auctionData['category_id'];
                $auction->category_id = $selectedCategoryId;
                $selectedCategoryId = (int)$selectedCategoryId;
                if ($selectedCategoryId > 0) {
                    /** @var Category[] $descendants */
                    $selectedCategory = Category::model()->findByPk($selectedCategoryId);
                    $descendants = $selectedCategory->descendants()->findAll();
                    $searchAuctionCategoryIds = array_map(function (Category $eachCategory) {
                        return $eachCategory->category_id;
                    }, $descendants);
                    $searchAuctionCategoryIds[] = $selectedCategoryId;
                    $searchAuctionCategoryIds[] = 0;
                }
            }
        }

        $userCategories = Category::getUserCategoriesHavingLotsByStatus(
            Yii::app()->user->id, Auction::ST_COMPLETED_EXPR_DATE
        );
        $userCategoriesList = Category::categoriesToFormattedDropDownArray($userCategories);
        $gridPageSize = GridView::pageSizeDropDown();
        $gridViewAfterAjaxUpdate = 'function(gridId, html, et, err) { if(err === undefined) {
            resetGridSelectAllChk(); initGridButtons();
        }}';

        $this->render('//user/sales/_completed_items_table', [
            'limit' => GridView::getPageSize(),
            'auction' => $auction,
            'gridViewTemplate' => "{items}\n<hr><div class='row'><div class='col-xs-2'>$gridPageSize</div><div class='col-xs-10 text-right'>{pager}</div></div><hr>",
            'userCategoriesList' => $userCategoriesList,
            'searchAuctionCategoryIds' => $searchAuctionCategoryIds,
            'gridViewPager' => ['class' => 'CLinkPager',
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
            'gridViewSummaryText' => Yii::t('basic', 'Showed {start} to {end}. All {count}'),
            'gridViewAfterAjaxUpdate' => $gridViewAfterAjaxUpdate,
        ]);
    }

    public function actionSoldItems()
    {
        $request = Yii::app()->getRequest();
        $cookies = $request->getCookies();

        if ($request->getParam('sort') !== null) {
            $this->saveCookieInf('u_sold_items_sort', $request->getParam('sort'));
        } else {
            if (isset($cookies['u_sold_items_sort']->value)) {
                $_GET['sort'] = $cookies['u_sold_items_sort']->value;
            }
        }

        if ($request->getQuery('size')) {
            $this->saveCookieInf('u_sold_items_page_size', $request->getQuery('size'));
        } else {
            if (isset($request->cookies['u_sold_items_page_size']->value)) {
                $_GET['size'] = Yii::app()->request->cookies['u_sold_items_page_size']->value;
            }
        }

        $this->pageTitle = Yii::t('basic', 'Sold');

        $auction = $request->getParam('Auction');
        $gridPageSize = GridView::pageSizeDropDown();

        $this->render('_sold_items_table', [
            'limit' => GridView::getPageSize(),
            'auction' => CHtml::encode($auction['name']),
            'gridViewTemplate' => "{items}\n<hr><div class='row'><div class='col-xs-2'>$gridPageSize</div><div class='col-xs-10 text-right'>{pager}</div></div><hr>",
            'gridViewPager' => ['class' => 'CLinkPager',
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
        ]);
    }

    public function actionWorkWithLots()
    {
        /** @var CHttpRequest $request */
        $request = Yii::app()->getRequest();

        if ($request->getIsAjaxRequest() && $request->getIsPostRequest() && !Yii::app()->user->isGuest) {
            $select_action = $request->getParam('select_action');
            $check_lots = $request->getParam('check_lots');

            switch ($select_action) {
                case 1: // republish items

                    $period = $request->getParam('period');

                    if (!empty($check_lots) && ($period > 0 && $period < 9)) {
                        foreach ($check_lots as $lot) {
                            if ($lot != 0) {
                                $count = Auction::model()->count('owner=:owner AND auction_id=:auction_id', [':owner' => Yii::app()->user->id, ':auction_id' => $lot]);

                                if ($count == 1) {
                                    /** @var Auction $model */
                                    $model = Auction::model()->findByPk($lot);
                                    $model->current_bid = 0;
                                    $model->quantity_sold = 0;
                                    $model->viewed = 0;
                                    $model->sales_id = 0;
                                    $date = new DateTime();
                                    $model->created = $date->format('Y-m-d H:i:s');
                                    $interval_spec = Auction::getDateSpecForDuration($period);
                                    $date->add(new DateInterval($interval_spec));
                                    $model->bidding_date = $date->format('Y-m-d H:i:s');
                                    $model->status = Auction::ST_ACTIVE;
                                    $model->duration = $period;
                                    $model->update();

                                    //bids
                                    Yii::app()->db->createCommand()
                                        ->delete(
                                            'bids',
                                            'lot_id=:lot_id',
                                            [
                                                ':lot_id' => (int)$model->auction_id,
                                            ]
                                        );
                                }
                            }
                        }
                    }
                    break;
                case 2:
                    if (!empty($check_lots)) {
                        foreach ($check_lots as $lot) {
                            if ($lot > 0) {
                                /** @var Auction $model */
                                $model = Auction::model()->find(
                                    'owner=:owner AND auction_id=:auction_id',
                                    [':owner' => Yii::app()->user->id, ':auction_id' => $lot]
                                );
                                if ($model) {
                                    $model->status = 10;
                                    $model->update();
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }

    public function actionCancelLots()
    {
        if (isset($_POST['Lot'])) {
            $criteria = new CDbCriteria();
            $criteria->compare('owner', Yii::app()->user->id);
            $criteria->addInCondition('auction_id', $_POST['Lot']);

            Auction::model()->updateAll(['status' => Auction::ST_COMPLETED_EXPR_DATE, 'bidding_date' => '0000-00-00 00:00:00'], $criteria);
        }

        $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function actionDel_lot($id)
    {
        $count = Auction::model()->count('owner=:owner AND auction_id=:auction_id', [':owner' => Yii::app()->user->id, ':auction_id' => $id]);

        if ($count == 1) {
            $model = Auction::model()->findByPk($id);
            $model->status = Auction::ST_DELETED;
            $model->update();
        }

        $this->redirect('/user/sales/completedItems');
    }


    public function actionMarkDeleted()
    {

        $request = Yii::app()->getRequest();

        if (!$request->getIsAjaxRequest()) {
            throw new CHttpException(400);
        }

        $sales = $request->getPost('sale_id');
        $id_table = $request->getPost('data-id-table');

        if (!empty($sales)) {

            foreach ($sales as $key => $value) {
                $this->markDeleted($value, $id_table[0]);
            }
            RAjax::success();

        } else {
            RAjax::error();
        }

    }


    private function markDeleted($saleId, $id_table)
    {
        /** @var Sales $sale */
        $sale = Sales::model()->findByAttributes([
            'sale_id' => $saleId,
        ]);

        if ($sale->buyer == Yii::app()->user->id OR $sale->seller_id == Yii::app()->user->id) {

            $result = false;
            if ($id_table != 'history-shopping-table') {
                if (!$sale->del_status) {
                    $sale->del_status = 1;
                    $sale->update(['del_status']);
                    $result = true;
                }
            } else {
                if (!$sale->del_status_buyer) {
                    $sale->del_status_buyer = 1;
                    $sale->update(['del_status_buyer']);
                    $result = true;
                }
            }
            return $result;
        }
    }
}