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

class IndexAction extends CAction
{

    /**
     * @var string view for render
     */
    public $view;


    /**
     * @var string view for render table
     */
    public $viewTable;


    /**
     * @var CActiveRecord
     */
    public $modelClass;

    public function run()
    {
        $model = new $this->modelClass;
        $model->setScenario('search');
        $model->unsetAttributes();

        if(isset($_GET[$this->modelClass])) {
            $model->setAttributes($_GET[$this->modelClass]);
        }

        if (isset($_GET['ajax'])) {
            $this->controller->renderPartial(
                $this->viewTable,
                array(
                    'model' => $model,
                )
            );
        } else {
            $this->controller->render(
                $this->view,
                array(
                    'model' => $model,
                )
            );
        }
    }

}