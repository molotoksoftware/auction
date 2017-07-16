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



class SalesController extends BackController {
    
        public function filters()
    {
        return array(
            'accessControl'
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('login', 'logout'),
                'users' => array('*'),
            ),
            array('allow',
                'actions' => array('index', 'view'),
         //       'roles' => array('admin', 'root'),
                'users' => array('@'),
            ),
            array('deny'),
        );
    }

    public function actionIndex()
    {

        $sales = new Sales('search');
        $sales->unsetAttributes();

        if (isset($_GET['Sales'])) {
            $sales->attributes = $_GET['Sales'];
        }

        if (isset($_GET['ajax'])) {
            $this->renderPartial('_table_sales', ['model'=>$sales]);
        } else {
            $this->render('index', ['model' => $sales]);
        }

    }

}