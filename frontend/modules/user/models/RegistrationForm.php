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

class RegistrationForm extends CFormModel
{


    public $email;
    public $login;
    public $password;
    public $confirmPassword;
    public $agreeLicense;
    public $agreeNotifier;


    private $_identity;

    public function rules()
    {
        return [
            ['login, password, email', 'required', 'message' => Yii::t('basic', 'You need specify field "{attribute}"')],
            ['login', 'match', 'pattern' => '/^[A-Za-z0-9_\-]{2,50}$/', 'message' => Yii::t('basic', 'You need to enter Letters of the Latin alphabet and numbers')],
            ['email', 'email', 'message' => Yii::t('basic', 'Incorrect E-mail')],
            ['email', 'unique', 'className' => 'User', 'attributeName' => 'email', 'message' => Yii::t('basic', 'User with e-mail "{value}" already exists')],
            ['login', 'unique', 'className' => 'User', 'attributeName' => 'login', 'message' => Yii::t('basic', 'User with login "{value}" already exists')],
            ['login, email', 'filter', 'filter' => 'trim'],
            ['agreeLicense', 'agree'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('basic', 'Password must be repeated exactly')],
            ['login', 'uniqueNick'],
        ];
    }

    public function uniqueNick()
    {
        $user = User::model()->findByAttributes(['nick' => $this->login]);

        if ($user) {
            $this->addError('login',  Yii::t('basic', 'Login already exists'));
        }
    }

    public function agree($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ((boolean)$this->$attribute == false) {
                $this->addError($attribute, Yii::t('basic','You didn\'t read the Terms and Conditions'));
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'email'                  => Yii::t('basic','E-mail'),
            'login'                  => Yii::t('basic', 'Your login'),
            'password'               => Yii::t('basic','Password'),
            'confirmPassword'        => Yii::t('basic','Repeat password'),
            'agreeNotifier'          => Yii::t('basic','Receive e-mail notification'),
            'agreeLicense'           => Yii::t('basic','Terms and Conditions'),
        ];
    }


}
