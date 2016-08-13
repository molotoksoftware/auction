<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://www.molotoksoftware.com/
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



class UserIdentity extends CUserIdentity
{

    private $_id;

    public function authenticate()
    {

        $record = Admins::model()->find('LOWER(login)=?', array($this->username));

        if (is_null($record)) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else if ($record->validatePassword($this->password) === false) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            Yii::app()->user->clearStates();

            $this->setState('code', md5(uniqid(mt_rand(), true) . time()));
            $this->_id = $record->id_admin;
            $this->errorCode = self::ERROR_NONE;
        }

        return !$this->errorCode;
    }

    public function getId()
    {
        return $this->_id;
    }

}
