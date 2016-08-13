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



class EWebUser extends CWebUser {

    private $_model = null;
    private $_adminUrl = null;

    public function getIsGuest() {
        return !$this->hasState('admin');
    }

    public function afterLogout() {

    }

    public function getRole() {
        if ($user = $this->getModel()) {
            return $user->role;
        }
    }

    public function getModel() {
        if (!Yii::app()->user->isGuest && $this->_model === null) {
            $this->_model = Admins::model()->findByPk($this->id);
        }

        return $this->_model;
    }

    public function getAvatar() {
        if ($user = $this->getModel()) {
            return $user->getAvatar();
        }
    }

    public function getShortName() {
        if ($user = $this->getModel()) {
            return $user->first_name . ' ' . $user->last_name;
        }
    }

}

