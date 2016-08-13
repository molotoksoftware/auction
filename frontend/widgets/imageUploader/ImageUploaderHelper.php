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


class ImageUploaderHelper
{
    public static function getFilesById($id, $storage)
    {
        $directories = array(
            'backend' => Yii::getPathOfAlias('backend') . '/www/tmp/',
            'frontend' => Yii::getPathOfAlias('frontend') . '/www/tmp/');
       
        $data = array();
        if (isset($directories[$storage])) {
            $dir = $directories[$storage].$id;
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if ($file=='..' || $file=='.' || is_dir($dir .'/'. $file)) continue;
                        $data[] = array(
                                'file'=> realpath($dir) .'/'. $file,
                                'id'  => $file);
                        }
                        closedir($dh);
                }
            }
        } else {
            throw new CException('Error type storage, no found "' . $storage . "\"");
        }
        return $data;
    }
}