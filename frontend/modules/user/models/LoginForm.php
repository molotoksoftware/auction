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


class LoginForm extends CFormModel
{

    public $login;
    public $password;
    public $rememberMe;
    private $_identity;

    public function rules()
    {
        return array(
            array('login, password', 'required', 'message' => Yii::t('basic', 'You need specify field "{attribute}"')),
            array('rememberMe', 'boolean'),
            array('password', 'authenticate')
        );
    }

    public function attributeLabels()
    {
        return [
            'login'      => Yii::t('basic', 'Your login'),
            'password'   => Yii::t('basic', 'Password'),
            'rememberMe' => Yii::t('basic', 'Don\'t remember me'),
        ];
    }

    public function authenticate($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_identity = new UserIdentity($this->login, $this->password);

            if (!$this->_identity->authenticate()) {
                $this->addError('password', Yii::t('basic', 'Incorrect login or password'));
            }
        }
    }

    public function login()
    {
        if (is_null($this->_identity)) {
            $this->_identity = new UserIdentity($this->login, $this->password);
            $this->_identity->authenticate();
        }
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = (!$this->rememberMe) ? 3600 * 24 * 30 : 0; // 30 days
            Yii::app()->getUser()->login($this->_identity, $duration);
            return true;
        } else {
            return false;
        }
    }

}
