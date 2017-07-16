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

/*
*создания пользователя create user
*
*
*/
 
return array(
    'postComments' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'postComments',
        'bizRule' => null,
        'data' => null,
    ),
    'createUser' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Create User',
        'bizRule' => null,
        'data' => null,
    ),
    'updateUser' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Update User',
        'bizRule' => null,
        'data' => null,
    ),
    'listUser' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'List Users',
        'bizRule' => null,
        'data' => null,
    ),
    'deleteUser' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Delete User',
        'bizRule' => null,
        'data' => null,
    ),
    //запрет на удаение самого себя / restrict the ability to delete yourself
    'editOwnUser' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'Edit Own User',
        'bizRule' => 'return Yii::app()->user->id==$params["user"];',
        'data' => null,
    ),
    'managesUser' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'Manage User',
        'children' => array(
            //'editOwnUser',
            'listUser',
            'deleteUser',
            'updateUser',
            'createUser',
        ),
        'data' => null,
        'bizRule' => null
    ),
    'user' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'User',
        'bizRule' => null,
        'data' => null,
    ),
    'admin' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'admin',
        'children' => array(
            'updateUser',
            'createUser',
            'postComments'
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