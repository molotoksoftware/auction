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
 * Форма регистрации
 *
 */
class RegistrationForm extends CFormModel
{


    public $email;
    public $login;
    public $password;
    public $confirmPassword;
    public $agreeLicense;
    public $agreeNorifier;


    private $_identity;

    public function rules()
    {
        return [
            ['login, password, email', 'required', 'message' => 'Заполните поле "{attribute}"'],
            ['login', 'match', 'pattern' => '/^[A-Za-z0-9_\-]{2,50}$/', 'message' => 'Допустимы только буквы латинского алфавита и цифры.'],
            ['email', 'email', 'message' => 'Некорректный e-mail'],
            ['email', 'unique', 'className' => 'User', 'attributeName' => 'email', 'message' => 'Пользователь с e-mail \'{value} \' уже существует'],
            ['login', 'unique', 'className' => 'User', 'attributeName' => 'login', 'message' => 'Пользователь с логином \'{value} \' уже существует'],
            ['login, email', 'filter', 'filter' => 'trim'],
            ['agreeLicense', 'agree'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароль должен совпадать'],
            ['login', 'uniqueNick'],
        ];
    }

    public function uniqueNick()
    {
        $user = User::model()->findByAttributes(['nick' => $this->login]);

        if ($user) {
            $this->addError('login', 'Логин занят');
        }
    }

    public function agree($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ((boolean)$this->$attribute == false) {
                $this->addError($attribute, 'Вы не ознакомились с Правилами');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'email'                  => 'E-mail',
            'login'                  => 'Логин',
            'password'               => 'Пароль',
            'confirmPassword'        => 'Повторите пароль',
            'agreeNorifier'          => 'Получать уведомленияя',
            'agreeLicense'           => 'Лицензионное соглашения',
        ];
    }


}
