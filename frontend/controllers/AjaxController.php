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
 *  AjaxController class file
 *
 */

class AjaxController extends FrontController
{
    public function filters()
    {
        return [
            'accessControl',
            'postOnly + createComplaint',
            'ajaxOnly + createComplaint, loadBlock, loadBlocks, filterAttribute',
        ];
    }

    public function accessRules()
    {
        return [
            ['allow',
                'actions' => [
                    'createComplaint',
                ],
                'users'   => ['@'],
            ],
            [
                'allow',
                'actions' => ['loadBlock', 'loadBlocks', 'filterAttribute'],
                'users'   => ['*'],
            ],
            ['deny'],
        ];
    }


    public function actionFilterAttribute()
    {
        $request = Yii::app()->getRequest();

        $array_id = array_filter($request->getQuery('select_val', []), "Auction::id_validator");
        $list_id = implode(",", $array_id);
        if ($list_id == '') {
            $list_id = '-1';
        }

        if (isset($_GET['select_child_val'])) {
            $array_child_id = array_filter($_GET['select_child_val'], "Auction::id_validator");
        }

        $params = Yii::app()->db->createCommand()
            ->select('value_id, value')
            ->from('attribute_values')
            ->where("parent_id IN ($list_id)")
            ->order('sort ASC')
            ->queryAll();

        $options = null;

        if (!empty($params)) {
            foreach ($params as $param) {
                if (isset($_GET['select_child_val'])) {
                    $key = array_search($param['value_id'], $array_child_id);
                    if ($key !== false) {
                        $check = 'selected="selected"';
                    } else {
                        $check = null;
                    }
                } else {
                    $check = null;
                }
                $options .= '<option value="' . $param['value_id'] . '" ' . $check . '>' . $param['value'] . '</option>';
            }
        }

        echo $options;
    }
}