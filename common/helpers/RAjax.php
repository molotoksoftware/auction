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
 * -----------------------------------------------------------------------------
 * helpers для ajax ответов
 * 
 * RAjax::success(array('messages' => 'запись успешно сохранена'));
 * 
 * RAjax::error(array('errors' => array(
 *  array(
 *    'id'=>'',
 *    'messages' => 'error1'
 *  ),
 * array(
 *  'id'=>'',
 *  'messages' => 'error1'
 *  ),
 * 
 * )));
 * 
 * return
 *  {"response":{"status":"error","data":{"messages":""}}}
 * 
 * RAjax::data(array('messages'=>'test'));
 * 
 * RAjax::dataText('messages');
 * 
 * -----------------------------------------------------------------------------
 *
 * 
 * @name RAjax
 * @package helpers
 * @version 0.1
 * 
 */
class RAjax
{

    /**
     *
     * @param $data array
     */
    public static function success($data = null)
    {
        echo CJSON::encode(['response' => [
            'status' => 'success',
            'data'   => $data,
        ]]);

        Yii::app()->end();
    }

    /**
     *
     * @param $data array
     */
    public static function error($data = null)
    {
        echo CJSON::encode(['response' => [
            'status' => 'error',
            'data'   => $data,
        ]]);

        Yii::app()->end();
    }

    public static function modelErrors($errors = array())
    {
        $messages = array();
        foreach ($errors as $attr => $message) {
            $messages[] = $message[0];
        }


        echo CJSON::encode(array('response' => array(
                'status' => 'error',
                'messages' => $messages,
                )));

        Yii::app()->end();
    }

    /**
     *
     * @param  $data array
     */
    public static function data($data)
    {
        echo CJSON::encode($data);
        Yii::app()->end();
    }

    /**
     *
     * @param $data text
     */
    public static function dataText($data)
    {
        echo $data;
        Yii::app()->end();
    }

}