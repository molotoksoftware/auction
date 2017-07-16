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
class RecoveryForm extends CFormModel
{
    public $email;
    private $_user = null;

    public function rules()
    {
        return array(
            array('email', 'required', 'message' => Yii::t('basic', 'You need specify field "{attribute}"')),
            array('email', 'email'),
            array('email', 'checkEmail'),
        );
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('basic', 'E-mail'),
        ];
    }

    public function checkEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_user = User::model()->find('email = :email', array(':email' => $this->email));
            if (!$this->_user) {
                $this->addError(
                    'email',
                    Yii::t('basic', 'E-mail {email} is not available', [
                        '{email}' => $this->email
                    ])
                );
            }
        }
    }

    public function getUser()
    {
        if (is_null($this->_user)) {
            $this->_user = User::model()->find('email = :email', array(':email' => $this->email));
        }
        return $this->_user;
    }
}