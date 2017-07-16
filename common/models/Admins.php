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

/**
 * @property string $id_admin
 * @property integer $role
 * @property string $last_name
 * @property string $first_name
 * @property string $father_name
 * @property string $avatar_file
 * @property string $email
 * @property string $password
 * @property string $salt
 * @property string $login
 * @property integer $status
 * @property string $registration_date
 */

class Admins extends CActiveRecord
{

    public static $thumbs = array(
        'preview' => array(
            'centeredpreview' => array(
                'width' => 120,
                'height' => 120,
            ),
        ),
    );

    const SAVE_PATH = 'frontend.www.images.admins';
    const ROLE_ADMIN = 'admin';
    const ROLE_ROOT = 'root';

    private $_fio = null;
    public $minPasswordLength = 6;
    public $changePassword;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'admins';
    }

    /**
     * @param $event
     */
    public function onUpdatePassword($event)
    {
        $this->raiseEvent('onUpdatePassword', $event);
    }

    public function showTable()
    {
        $this->getDbCriteria()->mergeWith(array(
            'conditon' => 'not in(:users)',
            'param' => array(Yii::app()->user->id),
        ));
        return $this;
    }

    public function behaviors()
    {
        return array(
            'uploadedFile' => array(
                'class' => 'backend.extensions.simpleImageUpload.SimpleImageUploadBehavior',
                'attributeName' => 'avatar_file',
                'savePathAlias' => self::SAVE_PATH,
                'versions' => self::$thumbs
            )
        );
    }

    public function rules()
    {
        return array(
        array('first_name, email, login', 'required'),
        array('password', 'required', 'except' => 'update'),
        array('email', 'email'),
        array('login', 'unique'),
        array('changePassword', 'safe'),
        array('role', 'default', 'value' => self::ROLE_ADMIN),
        array('status', 'numerical', 'integerOnly' => true),
        //array('role', 'in', 'range' => array_keys($this->getRoleList())),
        array('last_name, first_name, father_name, login', 'length', 'max' => 255),
        array('password', 'length', 'min' => 6),
        array('login', 'length', 'min' => 4),
        array('avatar_file', 'length', 'max' => 400),
        array('email, password, salt', 'length', 'max' => 40),
        array('id_admin, last_name, first_name, father_name, avatar_file, email, password, salt, login, status, registration_date, count_application_handeld, count_application_canceled', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'last_name' => 'Фамилия',
            'first_name' => 'Имя',
            'father_name' => 'Отчество',
            'avatar_file' => 'Аватар',
            'email' => 'Email',
            'password' => 'Пароль',
            'login' => 'Логин',
            'status' => 'Статус',
            'changePassword' => 'Сменить пароль',
            'registration_date' => 'Дата регистрации'
        );
    }

    //--------------------------------------------------------------------------


    public function getFio()
    {
        $separator = ' ';
        if ($this->_fio === null) {
            return ($this->first_name || $this->last_name) ?
                    ucfirst($this->last_name) . $separator . ucfirst($this->first_name) . ($this->father_name ? ($separator . ucfirst($this->father_name)) : "") : ucfirst($this->first_name);
        } else {
            return $this->_fio;
        }
    }

    public function setFio($value)
    {
        $this->_fio = $value;
    }

    public function changePassword($password)
    {
        $event = new CModelEvent($this);
        $this->onUpdatePassword($event);
        $this->password = $this->hashPassword($password, $this->salt);
        return $event->isValid;
    }

    public function beforeSave()
    {
        if (!$this->isNewRecord) {

            if (!empty($this->changePassword)) {
                $this->changePassword($this->changePassword);
            }
        }
        return parent::beforeSave();
    }

    public function hashPassword($password, $salt)
    {
        return md5($salt . $password);
    }

    public function validatePassword($password)
    {
        if ($this->password === $this->hashPassword($password, $this->salt))
            return true;

        return false;
    }

    public function generateSalt()
    {
        return md5(uniqid('', true) . time());
    }

    public function generateRandomPassword($length = null)
    {
        if (!$length)
            return substr(md5(uniqid(mt_rand(), true) . time()), 0, $this->minPasswordLength);
    }

    public function generateActivationKey()
    {
        //    return md5(time() . $this->email . uniqid());
    }

    /*
     * @return string
     */
    public function getAvatar()
    {
        if (!empty($this->avatar_file)) {
            return $this->uploadedFile->getImage('preview');
        } else {
            return '';
        }
    }

    //--------------------------------------------------------------------------

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id_admin', $this->id_admin, true);
        $criteria->compare('last_name', $this->last_name, true);
        $criteria->compare('first_name', $this->first_name, true);
        $criteria->compare('father_name', $this->father_name, true);
        $criteria->compare('avatar_file', $this->avatar_file, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('salt', $this->salt, true);
        $criteria->compare('login', $this->login, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('registration_date', $this->registration_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'multiSort' => true,
                'defaultOrder' => array(
                    'fio' => CSort::SORT_ASC,
                ),
                'attributes' => array(
                    'fio' => array(
                        'asc' => 't.last_name',
                        'desc' => 't.last_name DESC',
                    ),
                    '*'
                ),
            ),
        ));
    }

    public function getShortName()
    {
        return implode(' ', array(
            $this->first_name,
            $this->last_name,
            $this->father_name
        ));


    }

}
