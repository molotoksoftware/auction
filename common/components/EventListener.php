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
 * Class EventListener
 */
class EventListener extends CComponent
{
    /**
     * @param AfterLoginEvent $event
     */
    public static function onAfterLogin(AfterLoginEvent $event)
    {
        self::addOrUpdateUserToForum($event);
    }

    /**
     * @param AfterRegistrationEvent $event
     */
    public static function onAfterRegistration(AfterRegistrationEvent $event)
    {
        self::addOrUpdateUserToForum($event);
        UserHelper::createRequiredFoldersAfterCreate($event->getUser());
    }

    public static function onAfterPasswordUpdate(AfterPasswordUpdateEvent $event)
    {
        self::addOrUpdateUserToForum($event);
    }

    public static function onAfterPasswordReset(AfterPasswordResetEvent $event)
    {
        self::addOrUpdateUserToForum($event);
    }

    public static function onAfterNickUpdate(AfterNickUpdateEvent $event)
    {
        self::addOrUpdateUserToForum($event);
    }

    /**
     * @param AfterLoginEvent|AfterRegistrationEvent|AfterPasswordUpdateEvent|AfterPasswordResetEvent|AfterNickUpdateEvent $event
     */
    protected static function addOrUpdateUserToForum($event)
    {
        $user = $event->getUser();
        $userId = $user->getPrimaryKey();

        if (method_exists($event, 'getLogin')) {
            $user->login = $event->getLogin();
        }
        if (method_exists($event, 'getPassword')) {
            $user->password = $event->getPassword();
        }

        try {

            if ($event instanceof AfterLoginEvent) {

            }

            if ($event instanceof AfterRegistrationEvent) {
            }

            if ($event instanceof AfterPasswordUpdateEvent) {

            }

            if ($event instanceof AfterPasswordResetEvent) {

            }

            if ($event instanceof AfterNickUpdateEvent) {

            }

        } catch (CException $e) {
            $errorText = 'Exception ' . get_class($e) . ' with message: "' . $e->getMessage() . '"' . "\n";
            $errorText .= 'File ' . $e->getFile() . ':' . $e->getLine() . "\n";
            $errorText .= 'Trace ' . $e->getTraceAsString() . "\n";
            Yii::log('Error on add/update forum user: ' . $errorText, CLogger::LEVEL_ERROR);
        }
    }
}