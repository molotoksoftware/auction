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
 * Class Getter
 */
class Getter
{
    /**
     * @return WebUser
     */
    public static function webUser()
    {
        /** @var CWebApplication $app */
        $app = Yii::app();
        return $app->getUser();
    }

    /**
     * @return User|null
     */
    public static function userModel()
    {
        $user = self::webUser();
        return !$user->getIsGuest() ? $user->getModel() : null;
    }

    /**
     * @return CClientScript
     */
    public static function clientScript()
    {
        return Yii::app()->getComponent('clientScript');
    }

    /**
     * @return CHttpSession
     */
    public static function getSession()
    {
        return Yii::app()->getComponent('session');
    }

    /**
     * @return CImageComponent
     */
    public static function image()
    {
        return Yii::app()->getComponent('image');
    }

    /**
     * @return CImageHandler
     */
    public static function imageHandler()
    {
        return Yii::app()->getComponent('imageHandler');
    }


    /**
     * @return YiiMail
     */
    public static function mail2()
    {
        return Yii::app()->getComponent('mail');
    }

    /**
     * @return EmailQueue
     */
    public static function emailQueue()
    {
        return Yii::app()->getComponent('emailQueue');
    }


    /**
     *
     * @return bool
     */
    public static function getIsEnabledEmailNtf()
    {
        return isset(Yii::app()->params['disableEmailNtf'])
            ? !Yii::app()->params['disableEmailNtf']
            : true;
    }

    /**
     * @return AccessManager
     */
    public static function accessManager()
    {
        return Yii::app()->getComponent('accessManager');
    }



}