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

class CategoriesCommand extends CConsoleCommand
{

    public function getArray($str)
    {
        $pos = strpos($str, ' ');
        $raw = substr($str, 0, $pos);
        return array_filter(explode('.', $raw));
    }

    public function getValue($str)
    {
        $pos = strpos($str, ' ');
        return substr($str, $pos + 1, mb_strlen($str));
    }

    public function create($par, $value)
    {
        if (is_null($par)) {
            throw new CException('Error parent');
        }


        $cat = new Category();
        $cat->name = $value;
        $node = Category::model()->findByPk((int)$par);
        $cat->appendTo($node);
        return $cat->category_id;
    }


    public function run($args)
    {
        set_time_limit(0);

        $basePath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data');
        $file_array = file($basePath . "/cat");
        $parentL1 = Category::DEFAULT_CATEGORY;
        $parentL2 = null;
        $parentL3 = null;

        while (list($line_num, $line) = each($file_array)) {
            $ar = $this->getArray($line);

            if (count($ar) == 1) {
                $parentL2 = $this->create($parentL1, $this->getValue($line));
            } elseif (count($ar) == 2) {
                $parentL3 = $this->create($parentL2, $this->getValue($line));

            } elseif (count($ar) == 3) {
                $n = $this->create($parentL3, $this->getValue($line));

            } else {
                throw new Exception('not');
            }
            echo 'save '.$line_num . PHP_EOL;

            unset($ar);
        }

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


    }
}