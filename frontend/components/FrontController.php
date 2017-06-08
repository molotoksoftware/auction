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


class FrontController extends BaseController
{

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
                $webUser = Getter::webUser();
                $lastSavedIpAddress = $webUser->getState('lastIpAddress');
                $ip = Yii::app()->getRequest()->getUserHostAddress();
                if (!$lastSavedIpAddress || $ip != $lastSavedIpAddress) {
                    $userModel->last_ip_addr = $ip;
                    $userModel->update(['last_ip_addr']);
                    $webUser->setState('lastIpAddress', $ip);
                }

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

        if (Yii::app()->params['maintenanceMode'] && $this->id != 'system' && $action->id != 'maintenance') {
            $this->redirect('/system/maintenance');
        }


        return true;
    }



}