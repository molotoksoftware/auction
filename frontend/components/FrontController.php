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

/**
 *
 * Основной контроллер клиентской части приложения
 *
 */
class FrontController extends BaseController
{
    const COOKIE_CURRENCY_KEY = 'currency_code';
    const CACHE_USER_COMMON_CURRENCY_ID = 'user_common_currency_id';

    public $defaultPage = 25;

    public $categories;

    public $userSelectedCategory = 0;

    public $active_search = false;

    public function behaviors()
    {
        return [
            'seo' => [
                'class' => 'common.extensions.seo.SeoControllerBehavior',
            ],
        ];

    }

    public function getPageNum()
    {
        if (isset($_GET['page'])) return intval($_GET['page']);
        return 1;
    }

    public function saveCookieInf($key, $value)
    {
        Cookie::saveForWebUser($key, $value);
    }

    public function getPageSize()
    {
        // Запоминаем кол-во элементов на странице
        if (isset($_GET['size'])) {
            if (preg_match("/^[0-9]+$/", $_GET['size'])) {
                $cookie = new CHttpCookie('item_on_page', $_GET['size']);
                $cookie->expire = time() + 3600 * 24 * 180;
                Yii::app()->request->cookies['item_on_page'] = $cookie;
            }
        }

        // Узнаем выбранное кол-во элементов на странице
        if (isset(Yii::app()->request->cookies['item_on_page']->value)) {
            if (preg_match("/^[0-9]+$/", Yii::app()->request->cookies['item_on_page']->value)) {
                $num_page_size = Yii::app()->request->cookies['item_on_page']->value;
            }
        }

        if (!isset($num_page_size)) {
            $num_page_size = $this->defaultPage;
        }

        return $num_page_size;
    }

    public function init()
    {
        $this->registerSeo(false);

        $request = Yii::app()->getRequest();
        $isGetRequest = !$request->isAjaxRequest
            && !$request->isPostRequest
            && !$request->isDeleteRequest
            && !$request->isPutRequest
            && !$request->isFlashRequest;

        if ($isGetRequest) {
            $userModel = Getter::userModel();
            if ($userModel) {
                // Проверяем и сохраняем IP юзера.
                $webUser = Getter::webUser();
                $lastSavedIpAddress = $webUser->getState('lastIpAddress');
                $ip = Yii::app()->getRequest()->getUserHostAddress();
                if (!$lastSavedIpAddress || $ip != $lastSavedIpAddress) {
                    $userModel->last_ip_addr = $ip;
                    $userModel->update(['last_ip_addr']);
                    $webUser->setState('lastIpAddress', $ip);
                }

                // Бан.
                if (isset($userModel->ban) && $userModel->ban == 1 && Yii::app()->controller->id != 'ban' && Yii::app()->controller->id != 'user') {
                    $this->redirect('/ban/index');
                }
            }
        }

        $this->attachEventHandlers();

        parent::init();
    }

    private function attachEventHandlers()
    {
        $eventHandlers = [
            'onAfterLogin'          => ['EventListener', 'onAfterLogin'],
            'onAfterRegistration'   => ['EventListener', 'onAfterRegistration'],
            'onAfterPasswordUpdate' => ['EventListener', 'onAfterPasswordUpdate'],
            'onAfterPasswordReset'  => ['EventListener', 'onAfterPasswordReset'],
            'onAfterNickUpdate'     => ['EventListener', 'onAfterNickUpdate'],
        ];
        foreach ($eventHandlers as $eventName => $eventHandler) {
            if ($this->hasEvent($eventName)) {
                $this->$eventName = $eventHandler;
            }
        }
    }

    /**
     * @param $event
     *
     * @throws CException
     */
    public function onAfterLogin($event)
    {
        $this->raiseEvent('onAfterLogin', $event);
    }

    /**
     * @param $event
     *
     * @throws CException
     */
    public function onAfterRegistration($event)
    {
        $this->raiseEvent('onAfterRegistration', $event);
    }

    /**
     * @param $event
     *
     * @throws CException
     */
    public function onAfterPasswordReset($event)
    {
        $this->raiseEvent('onAfterPasswordReset', $event);
    }

    /**
     * @param User     $user
     * @param null|int $selectedCategoryId
     *
     * @return array
     */
    public function prepareUserCategoriesTreeData(User $user, $selectedCategoryId = null)
    {
        /** @var HttpRequest $request */
        $request = Yii::app()->getRequest();
        if ($selectedCategoryId === null) {
            $selectedCategoryId = $request->getQuery('category_id');
            $selectedCategoryId = is_numeric($selectedCategoryId) ? (int)$selectedCategoryId : null;
        } else {
            $selectedCategoryId = (int)$selectedCategoryId;
        }

        // Товары юзера.
        /** @var CDbCommand $userProductsDbComm */
        $userProductsDbComm = Yii::app()->db->createCommand();
        $userAuctions = $userProductsDbComm
            ->select('a.category_id')
            ->from('auction a')
            ->where(
                'a.status=:status and a.owner=:owner',
                [
                    ':owner'  => $user->user_id,
                    ':status' => Auction::ST_ACTIVE,
                ]
            )->queryAll();
        $userCategoryIds = array_map(function ($eachAuction) {
            return $eachAuction['category_id'];
        }, $userAuctions);
        $userCategoryIds = array_unique($userCategoryIds);

        /** @var Category[] $userCategories */
        $userCategories = Category::model()->byIdsAndAncestors($userCategoryIds)->findAll();

        /** @var Category[] $userCategoriesById */
        $userCategoriesById = [];
        foreach ($userCategories as $i => $eachCategory) {
            if (!isset($userCategoriesById[$eachCategory->category_id])) {

                $eachCategory->auction_count = 0;
                $userCategoriesById[$eachCategory->category_id] = $eachCategory;
                $userCategoriesById[$eachCategory->category_id]->url = Yii::app()->createUrl(
                    '/user/user/page',
                    ['login' => $user->login, 'path' => $eachCategory->getPath()]
                );
            }
        }
        unset($userCategories);

        // Добавляем категориям кол-во лотов юзера.
        foreach ($userAuctions as $eachAuction) {
            if (!empty($eachAuction['category_id']) && isset($userCategoriesById[$eachAuction['category_id']])) {
                $userCategoriesById[$eachAuction['category_id']]->auction_count++;
            }
        }
        $this->categories = $userCategoriesById;

        $this->userSelectedCategory = $selectedCategoryId;

        $userSelectedCategoriesIds = [];
        if (!empty($selectedCategoryId)) {
            /** @var Category[] $descendants */
            $selectedCategory = Category::model()->findByPk($selectedCategoryId);
            $descendants = $selectedCategory->descendants()->findAll();
            $userSelectedCategoriesIds = array_map(function (Category $eachCategory) {
                return $eachCategory->category_id;
            }, $descendants);
            $userSelectedCategoriesIds[] = $selectedCategoryId;
            $userSelectedCategoriesIds[] = 0;

        }
        return [
            'userSelectedCategoriesIds' => $userSelectedCategoriesIds,
        ];
    }



    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (YII_ENV == 'dev') {
            date_default_timezone_set('Europe/Chisinau');
        }

        if (!empty(Yii::app()->params['maintenanceMode']) && $this->id != 'system' && $action->id != 'maintenance') {
            $this->redirect('/system/maintenance');
        }

        $this->initUserCurrency();

        return true;
    }

    private function initUserCurrency()
    {
        $request = Yii::app()->getRequest();
        $cookies = $request->getCookies();
        $webUser = Getter::webUser();
        $cache = Yii::app()->getCache();

        $availableCurrencies = FrontBillingHelper::getAvailableCurrencies();

        $selectedCurrency = $request->getQuery('currency');
        if ($selectedCurrency && isset($availableCurrencies[$selectedCurrency])) {
            $currentCurrencyCode = $selectedCurrency;
            $cookies[self::COOKIE_CURRENCY_KEY] = new CHttpCookie(self::COOKIE_CURRENCY_KEY, $currentCurrencyCode);
        } else {
            if (isset($cookies[self::COOKIE_CURRENCY_KEY])) {
                $currentCurrencyCode = $cookies[self::COOKIE_CURRENCY_KEY]->value;
            } else {
                if (!$webUser->getIsGuest()) {
                    $currentCurrencyCode = $webUser->getModel()->getCommonData()->currency->code;
                } else {
                    $currentCurrencyCode = FrontBillingHelper::getDefaultUserCurrencyCode();
                }
                $cookies[self::COOKIE_CURRENCY_KEY] = new CHttpCookie(self::COOKIE_CURRENCY_KEY, $currentCurrencyCode);
            }
        }

        $webUser->setCurrency($availableCurrencies[$currentCurrencyCode]);

        if (!$webUser->getIsGuest()) {
            $commonModel = $webUser->getModel()->getCommonData();
            if ($currentCurrencyCode != $commonModel->currency->code) {
                $commonModel->currency_id = $webUser->getCurrencyId();
                $commonModel->update(['currency_id']);
            }
            $cacheCurrencyId = $cache->get(self::CACHE_USER_COMMON_CURRENCY_ID);
            if ($cacheCurrencyId === false || $webUser->getCurrencyId() != $cacheCurrencyId) {
                $cache->set(self::CACHE_USER_COMMON_CURRENCY_ID, $webUser->getCurrencyId());
            }
        }
    }

    public function prepareSearchCategoriesTreeData($auctionIds, $selectedCategoryId = null, $d)
    {
        /** @var HttpRequest $request */
        $request = Yii::app()->getRequest();
        if ($selectedCategoryId === null) {
            $selectedCategoryId = $request->getQuery('category_id');
            $selectedCategoryId = is_numeric($selectedCategoryId) ? (int)$selectedCategoryId : null;
        } else {
            $selectedCategoryId = (int)$selectedCategoryId;
        }

        // Товары для виджета категорий в зависимости от поисковой строки
        $sql_a = Yii::app()->db->createCommand()
            ->select('a.category_id')
            ->from('auction a')
            ->where('a.status=:status',[':status' => Auction::ST_ACTIVE])
            ->andWhere(['in', 'auction_id', $auctionIds]);

        if (!empty($d)) {
            $sql_a->andWhere(['in', 'category_id', $d]);
        }

        $auctions = $sql_a->queryAll();

        $auctionCategoryIds = array_map(function ($eachAuction) {
                return $eachAuction['category_id'];
            }, $auctions);

        $auctionCategoryIds = array_unique($auctionCategoryIds);

        $userCategories = Category::model()->byIdsAndAncestors($auctionCategoryIds)->findAll();

        $userCategoriesById = [];

        $getWithOutCatId = preg_replace("/\&cat\=[0-9]{1,5}/ui", "", Yii::app()->getRequest()->getQueryString());

        foreach ($userCategories as $i => $eachCategory) {
            if (!isset($userCategoriesById[$eachCategory->category_id])) {

                $eachCategory->auction_count = 0;
                $userCategoriesById[$eachCategory->category_id] = $eachCategory;
                $userCategoriesById[$eachCategory->category_id]->url = '/auctions/'.$eachCategory->getPath().'?'.$getWithOutCatId;
            }
        }
        unset($userCategories);

        // Добавляем категориям кол-во лотов по поиску.
        foreach ($auctions as $eachAuction) {
            if (!empty($eachAuction['category_id']) && isset($userCategoriesById[$eachAuction['category_id']])) {
                $userCategoriesById[$eachAuction['category_id']]->auction_count++;
            }
        }
        $this->categories = $userCategoriesById;

        $this->userSelectedCategory = $selectedCategoryId;

        $userSelectedCategoriesIds = [];
        if (!empty($selectedCategoryId)) {
            /** @var Category[] $descendants */
            $selectedCategory = Category::model()->findByPk($selectedCategoryId);
            $descendants = $selectedCategory->descendants()->findAll();
            $userSelectedCategoriesIds = array_map(function (Category $eachCategory) {
                return $eachCategory->category_id;
            }, $descendants);
            $userSelectedCategoriesIds[] = $selectedCategoryId;
            $userSelectedCategoriesIds[] = 0;

        }
        return [
            'userSelectedCategoriesIds' => $userSelectedCategoriesIds,
        ];
    }

}