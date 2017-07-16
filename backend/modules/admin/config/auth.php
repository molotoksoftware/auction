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
 
 /* Russian to English
 * создания пользователя create user
 * обновления пользователя update user
 * просмотреть список пользователей view user list
 * удалить пользователя delete user
 * Управления пользователями manage users
 */


return array(
    'createUser' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'create user',
        'bizRule' => null,
        'data' => null,
    ),
    'updateUser' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'update user',
        'bizRule' => null,
        'data' => null,
    ),
    'listUser' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'list users',
        'bizRule' => null,
        'data' => null,
    ),
    'deleteUser' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'delete user',
        'bizRule' => 'return !(Yii::app()->user->id==$params["user_id"]);',
        'data' => null,
    ),

    'managesUser' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'manage users',
        'children' => array(
            'listUser',
            'deleteUser',
            'updateUser',
            'createUser',
        ),
        'data' => null,
        'bizRule' => null
    ),
    'deleteSim' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => '',
        'data' => null,
        'bizRule' => null
    ),
    'employe' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'user',
        'bizRule' => null,
        'data' => null,
    ),
    'admin' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'admin',
        'children' => array(
            'finance',
            'agents',
            'deleteUser',
            'updateUser',
            'createUser',
            'deleteSim'
        ),
        'bizRule' => null,
        'data' => null
    ),
    'root' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'root',
        'children' => array(
            'admin',
            'deleteUser'
        ),
        'bizRule' => null,
        'data' => null
    ),
);