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
 * Class EditUserForm
 */
class EditUserForm extends CFormModel
{
    public $user;

    public $login;
    public $nick;
    public $firstname;
    public $lastname;
    public $passwordNew;
    public $passwordOld;
    public $passwordRe;
    public $email;
    public $about;
    public $show_telephone;
    public $add_contact_info;
    public $terms_delivery;
    public $consent_receive_notification;
    public $telephone;

    public $id_country;
    public $id_region;
    public $id_city;

    //public $rating;

    public function rules()
    {
        return [
            ['id_country, id_region, id_city', 'required', 'message' => Yii::t('basic', 'You need specify field "{attribute}"')],
            ['login, nick, firstname, lastname, passwordNew, passwordOld, passwordRe, about', 'length'],
            ['email', 'email'],
            ['add_contact_info', 'length', 'max' => 512],
            ['terms_delivery', 'length', 'max' => 2048],
            ['show_telephone, consent_receive_notification', 'boolean'],
            ['telephone', 'length', 'max' => 20],
            ['telephone', 'numerical', 'integerOnly' => true],
            ['passwordRe', 'compare', 'compareAttribute' => 'passwordNew'],
            ['nick', 'length', 'min' => 3, 'max' => 25],
            ['nick', 'unique'],
            ['nick', 'once'],
        ];
    }

    public function once($attribute)
    {
        if ($this->user->nick) {
            $this->nick = $this->user->nick;
        }
    }

    public function unique($attribute)
    {
        if (!$this->{$attribute}) {
            $this->{$attribute} = null;
            return;
        }

        if ($this->user->nick && $this->{$attribute} == $this->user->nick) return;
        $user = User::model()->findByAttributes([$attribute => $this->{$attribute}]);

        if ($user) {
            $this->addError($attribute, Yii::t('basic', 'This NICK is already used'));
        } else {
            $user = User::model()->findByAttributes(['login' => $this->{$attribute}]);

            if ($user && $user->user_id != $this->user->user_id) {
                $this->addError($attribute, Yii::t('basic', 'This NICK is already used'));
            }
        }
    }

    public function afterValidate()
    {
        if (!$this->hasErrors())
            if (!empty($this->passwordOld)) {
                if (Yii::app()->user->getModel()->validatePassword($this->passwordOld)) {
                    if (empty($this->passwordNew)) {
                        $this->addError('passwordNew', Yii::t('basic', 'Specify new password'));
                    }
                } else {
                    $this->addError('passwordOld', Yii::t('basic', 'Wrong password'));
                }
            }
    }

    public function attributeLabels()
    {
        return [
            'nick'        => Yii::t('basic', 'Nick'),
            'firstname'   => Yii::t('basic', 'First name'),
            'lastname'    => Yii::t('basic', 'Last name'),
            'email'       => 'E-mail',
            'passwordRe'  => Yii::t('basic', 'Repeat password'),
            'passwordNew' => Yii::t('basic', 'New password'),
            'passwordOld' => Yii::t('basic', 'Current password'),
        ];
    }
}
