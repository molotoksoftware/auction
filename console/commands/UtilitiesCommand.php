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


class UtilitiesCommand extends CConsoleCommand
{
    public function run($args)
    {
        $this->garbageCollectionFile();
    }

    /**
     * удаляем ненужные старые папки
     *
     */
    public function garbageCollectionFile()
    {
        Yii::log('Очистка временных папок для загрузки файлов', CLogger::LEVEL_INFO, 'utilities');
        $time = 60 * 60; //10H
        //tmp directories
        $directories = array(
            dirname(__FILE__) . '/../..' . '/backend/www/tmp/',
            dirname(__FILE__) . '/../..' . '/frontend/www/tmp/'
        );

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if ($file == '..' || $file == '.') {
                            continue;
                        }
                        $info = stat($dir . $file);
                        if (($info[9] + $time) <= time()) {
                            FileHelper::removeDirectory($dir . $file);
                        }
                    }
                    closedir($dh);
                }
            }
        }
    }
}