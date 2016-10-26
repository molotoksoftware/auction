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

class WebUser extends CWebUser
{

    private $_model = null;

    public $cabinetUrl;

    /**
     * @var BillingCurrency
     */

    public function init()
    {
        parent::init();

        if (!Getter::getSession()->getIsInitialized()) {
            parent::init();
        }
        if ($this->isGuest == false) {
            $user = $this->getModel();
            $user->lastvisit = date('Y-m-d H:i:s', time());
            $user->online = 1;
            $user->update(['lastvisit', 'online']);
        }
    }


    public function beforeLogout()
    {
        if ($this->isGuest == false) {
            $user = $this->getModel();
            $user->lastvisit = date('Y-m-d H:i:s', time());
            $user->online = 0;
            $user->update(['lastvisit', 'online']);
        }
        return parent::beforeLogout();
    }

    public function getRole()
    {
        if ($user = $this->getModel()) {
            return $user->role;
        }
    }

    /**
     * @return User|null
     */
    public function getModel()
    {
        if (!$this->isGuest && $this->_model === null) {
            $this->_model = User::model()->findByPk($this->id);
        }
        return $this->_model;
    }


}
