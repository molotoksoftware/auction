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


class SystemController extends FrontController
{
    public function actionClear()
    {
        if (Yii::app()->hasComponent('cache')) {
            Yii::app()->cache->flush();
        }

        $directory = Yii::getPathOfAlias('webroot.assets') . DIRECTORY_SEPARATOR;
        $items = glob($directory . DIRECTORY_SEPARATOR . '{,.}*', GLOB_MARK | GLOB_BRACE);
        foreach ($items as $item) {
            if (basename($item) == '.' || basename($item) == '..')
                continue;
            if (substr($item, -1) == DIRECTORY_SEPARATOR)
                CFileHelper::removeDirectory($item);
            else
                unlink($item);
        }
        echo "clear....";
        Yii::app()->end();
    }

    public function actionMaintenance()
    {
        $this->layout = 'common';
        $this->pageTitle = Yii::t('basic', 'Maintenance works');
        $this->render('maintenance');
    }
}
