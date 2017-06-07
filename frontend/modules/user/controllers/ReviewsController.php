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
class ReviewsController extends FrontController
{

    protected $user;

    public function filters()
    {
        return [
            'accessControl',
        ];
    }

    public function accessRules()
    {
        return [
            [
                'allow',
                'actions' => [
                    'view',
                ],
                'users' => ['*'],
            ],
            [
                'allow',
                'actions' => [
                    'get',
                    'create',
                    'preCreate',
                    'bulkCreate',
                    'view',
                    'index',
                ],
                'users' => ['@'],
            ],
            ['deny'],
        ];
    }

    public function actionIndex($route = '')
    {
        $this->layout = '//layouts/cabinet';
        $this->pageTitle = Yii::t('basic', 'Reviews');

        $user = User::model()->findByPk(Yii::app()->user->id);

        if ($route == 'from_me') {
            $sqlCountRiveiws = 'SELECT COUNT(*) FROM reviews WHERE user_from=' . $user->user_id;
        } else {
            $sqlCountRiveiws = 'SELECT COUNT(*) FROM reviews WHERE user_to=' . $user->user_id;
        }

        $countReview = Yii::app()->db->createCommand($sqlCountRiveiws)->queryScalar();

        $criteria = new CDbCriteria();
        $criteria->order = '`date` DESC';
        $pages = new CPagination($countReview);
        $pages->pageSize = 10;
        $pages->applyLimit($criteria);

        if ($route == 'from_me') {
            $items = Reviews::model()->getFrom($user)->findAll($criteria);
        } else {
            $items = Reviews::model()->getTo($user)->findAll($criteria);
        }

        $this->render('index', [
            'items' => $items,
            'pages' => $pages,
            'count' => $countReview,
            'route' => $route,
        ]);
    }

    public function actionView($login, $role = '', $value = '')
    {
        if ($value == 'negative') {
            $value = 1;
        } elseif ($value == 'positive') {
            $value = 5;
        }

        $user = User::getByLogin($login);

        $user_name = $user->nick ? $user->nick : $user->login;

        $this->pageTitle = Yii::t('basic', 'Reviews about') . ' ' . $user_name;
        $this->layout = '//layouts/userPageLayout';
        $this->user = $user;

        $reviews = UserDataHelper::getCountReviews($user->user_id);

        $countReview = $reviews['negative'] + $reviews['positive'];
        if ($role == '1') {
            $countReview = $reviews['roleBuyer'];
        } elseif ($role == '2') {
            $countReview = $reviews['roleSaller'];
        }

        if ($value == '1') {
            $countReview = $reviews['negative'];
        } elseif ($value == '5') {
            $countReview = $reviews['positive'];
        }

        $criteria = new CDbCriteria();
        $criteria->order = '`date` DESC';
        $pages = new CPagination($countReview);
        $pages->pageSize = 25;
        $pages->applyLimit($criteria);

        $condition = new CDbCacheDependency('SELECT MAX(`update`) FROM reviews WHERE user_to=' . $user->user_id);
        if ($role) {
            $items = Reviews::model()->cache(10000, $condition)->getTo($user)->getRole($role)->findAll($criteria);
        } elseif ($value) {
            $items = Reviews::model()->cache(9000, $condition)->getTo($user)->getValue($value)->findAll($criteria);
        } else {
            $items = Reviews::model()->cache(8000, $condition)->getTo($user)->findAll($criteria);
        }

        $this->prepareUserCategoriesTreeData($user->user_id);
        $this->searchAction = '/user/page/' . $user->login;
        $this->userNick = $user->getNickOrLogin();

        $this->render(
            'view',
            [
                'items' => $items,
                'role' => $role,
                'value' => $value,
                'pages' => $pages,
                'count' => $countReview,
            ]
        );
    }


    public function actionPreCreate($id = false, $role = false)
    {

        $this->layout = '//layouts/cabinet';
        $this->pageTitle = Yii::t('basic', 'Leave feedback');

        if (isset($_POST['review']) && isset($_POST['role'])) {
            $array_reviews = $_POST['review'];
            $role = $_POST['role'];

            $lots = self::querySalesData($array_reviews, $role);

            $this->render('preCreate', [
                'count' => count($lots),
                'lots' => $lots,
                'role' => $role,
            ]);
        }

        if ($id) {
            $reviews = [$id];

            if ($lot = self::querySalesData($reviews, $role)) {

                $this->render('preCreate', [
                    'count' => count($lot),
                    'lots' => $lot,
                    'role' => $role,
                ]);

            } else {
                throw new CHttpException(403);
            }
        } else {
            throw new CHttpException(403);
        }

    }

    public function actionCreate()
    {

        if (($sales = $_POST['sale']) AND ($text = $_POST['text']) AND ($rating = $_POST['value']) AND ($role = $_POST['role'])) {

            if (count($sales) > 1) {
                self::createBulkReviews($sales, $text, $rating, $role);
            } elseif (count($sales) == 1) {
                self::createOneReview($sales, $text, $rating);
            }

        } else {
            Yii::app()->user->setFlash('error', 'Leave feedback error');
            Yii::app()->controller->redirect(($role == Reviews::ROLE_BUYER) ? '/user/shopping/historyShopping' : '/user/sales/soldItems');
        }

    }


    private function createBulkReviews($sales, $text, $rating, $role)
    {

        foreach ($sales as $eachLot) {

            $sale = Sales::model()->findByPk($eachLot);

            self::createReview($sale->sale_id, $sale->seller_id, $sale->buyer, $sale->item_id, $text, $rating);

        }

        Yii::app()->user->setFlash('success', Yii::t('basic', 'Feedback has been sent'));
        Yii::app()->controller->redirect(($role == Reviews::ROLE_BUYER) ? '/user/shopping/historyShopping' : '/user/sales/soldItems');

    }


    private function createOneReview($sales, $text, $rating)
    {

        $sale = Sales::model()->findByPk($sales[0]);
        $isBuyer = Yii::app()->user->id == $sale->buyer;

        if ($result = self::createReview($sale->sale_id, $sale->seller_id, $sale->buyer, $sale->item_id, $text, $rating)) {
            Yii::app()->user->setFlash('success', Yii::t('basic', 'Feedback has been sent'));
            Yii::app()->controller->redirect(($isBuyer == true) ? '/user/shopping/historyShopping' : '/user/sales/soldItems');

        } else {
            Yii::app()->user->setFlash('error', 'Leave feedback error');
            Yii::app()->controller->redirect(($isBuyer == true) ? '/user/shopping/historyShopping' : '/user/sales/soldItems');

        }
    }

    private function querySalesData($arraySales, $role)
    {

        $query = Yii::app()->db->createCommand()
            ->select('s.*, a.auction_id, a.name, u.login')
            ->from('sales s')
            ->leftJoin('auction a', 'a.auction_id = s.item_id')
            ->leftJoin('users u', 'u.user_id = s.seller_id')
            ->where(['in', 'sale_id', $arraySales]);

        if ($role == Reviews::ROLE_BUYER) {
            $query->andWhere('review_about_my_buyer = 0 AND buyer = :user', [':user' => Yii::app()->user->id]);
        } else {
            $query->andWhere('review_my_about_saller = 0 AND seller_id = :user', [':user' => Yii::app()->user->id]);
        }

        return $query->queryAll();
    }


    public static function createReview($sale_id, $seller, $buyer, $item, $text, $rating)
    {
        return Reviews::makeReview($sale_id, $seller, $buyer, $item, $text, $rating);
    }


    public static function sendReviewNtf($seller, $buyer, $item, $text, $isBulk = false)
    {
        $senderUserId = Getter::webUser()->getId();
        $targetUserId = $senderUserId == $seller ? $buyer : $seller;
        $authorModel = User::model()->findByPk($senderUserId);

        if (!$isBulk) {
            /** @var Auction $auctionModel */
            $auctionModel = Auction::model()->findByPk($item);
            if ($auctionModel && $authorModel && $text) {
                $params = [
                    'lotModel' => $auctionModel,
                    'authorModel' => $authorModel,
                    'reviewText' => $text,
                ];
                $ntfType = Notification::TYPE_REVIEW;
            }
        } else {
            if (is_array($item) && $item && $authorModel && $text) {
                $params = [
                    'auctions' => $item,
                    'authorModel' => $authorModel,
                    'reviewText' => $text,
                ];
                $ntfType = Notification::TYPE_REVIEW_MULTIPLE;
            }
        }
        if (isset($params, $ntfType)) {
            $ntf = new Notification($targetUserId, $params, $ntfType);
            $ntf->send();
        }
    }
}
