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

    public $searchAction = '/auction/index';
    public $userNick = false;
    public $auc_id_arr = [];

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
        
        Yii::app()->language = Yii::app()->params['language'];

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


    public function prepareUserCategoriesTreeData($user_id)
    {
        if ($result = Item::searchHelper('', false, $user_id)) {

            foreach ($result as $item) {
                // Составляем массив из идентификаторов найденных аукционов
                $auc_id_arr[] = intval($item['auction_id']);

            }

             $this->auc_id_arr = $auc_id_arr;
        }
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

}