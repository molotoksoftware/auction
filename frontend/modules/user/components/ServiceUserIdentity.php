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


class ServiceUserIdentity extends UserIdentity 
{
    const ERROR_NOT_AUTHENTICATED = 3;

    public $service;
    protected $_id;

    public function __construct($service) 
    {
        $this->service = $service;
    }

    public function authenticate() 
    {
        if ($this->service->isAuthenticated) {
            $user_service = UsersService::model()->find('service=:service AND service_id=:service_id', array(':service' => $this->service->serviceName, ':service_id' => $this->service->id));

            if (isset($user_service->id_users_service)) {
                $user = User::model()->findByPk($user_service->id);

                $this->_id = $user->user_id;
                $this->setState('name', $user->login);        
            }

            $this->errorCode = self::ERROR_NONE;  
        } else {
            $this->errorCode = self::ERROR_NOT_AUTHENTICATED;
        }

        return !$this->errorCode;
    }

    public function getId()
    {
        return $this->_id;
    }
}