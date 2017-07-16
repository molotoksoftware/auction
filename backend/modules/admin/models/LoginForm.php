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



class LoginForm extends CFormModel
{

    public $username;
    public $password;
    public $rememberMe;
    private $_identity;

    public function rules()
    {
        return array(
            array('username, password', 'required', 'message' => 'Пожалуйста, укажите Ваш логин и пароль.'),
            array('rememberMe', 'boolean'),
            array('username, password', 'filter', 'filter' => 'trim'),
            array('password', 'authenticate'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'rememberMe' => 'Не выходить из системы',
            'username' => 'Логин',
            'password' => 'Пароль',
        );
    }

    public function authenticate($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            if (!$this->_identity->authenticate()) {
                $this->addError('password', '<b>Не удается войти.</b><br>Пожалуйста, проверьте правильность написания <b>логина</b> и <b>пароля</b>.');
            }
        }
    }

    public function login()
    {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            $this->_identity->authenticate();
        }
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
            Yii::app()->user->login($this->_identity, $duration);
            return true;
        } else
            return false;
    }

}
