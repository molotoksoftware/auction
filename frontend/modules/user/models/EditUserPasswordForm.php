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



class EditUserPasswordForm extends CFormModel
{

    public $user;

    public $passwordNew;
    public $passwordOld;
    public $passwordRe;

    public function rules()
    {
        return [
            ['passwordNew, passwordOld, passwordRe', 'length'],
            ['passwordRe', 'compare', 'compareAttribute' => 'passwordNew'],
        ];
    }

    public function afterValidate()
    {
        //check change password
        if (!$this->hasErrors())
            if (!empty($this->passwordOld)) {
                if (Getter::userModel()->validatePassword($this->passwordOld)) {
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
            'passwordRe'  => Yii::t('basic', 'Repeat new password'),
            'passwordNew' => Yii::t('basic', 'New password'),
            'passwordOld' => Yii::t('basic', 'Current password'),
        ];
    }

}
