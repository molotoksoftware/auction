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
 * Класс для запуска консольных команд в фоновом режиме.
 *
 * Пример использования:
 * ```
 * ...
 * $cr = new ConsoleRunner;
 * $cr->run('users/signup param1 param2 ...');
 * ...
 * ```
 */
class ConsoleRunner extends CApplicationComponent
{
    /**
     * @var string Данная переменная хранит путь к индекс файлу консоли оносительно крневой папки приложения @root.
     */
    protected $_consoleFile = 'yiic';


    /**
     * Запускает консольную команду в фоновом режиме.
     * @param string $cmd Конаольная команда которая должна выполнятся в фоновом режиме.
     * @return boolean
     */
    public function run($cmd)
    {
        $cmd = realpath(Yii::getPathOfAlias('root')) . DIRECTORY_SEPARATOR . $this->_consoleFile . ' ' . $cmd;

        if ($this->isWindows()) {
            pclose(popen('start /b ' . $cmd, 'r'));
        } else {
            pclose(popen($cmd . ' /dev/null &', 'r'));
        }
        return true;
    }

    /**
     * Функция для проверки операционной системы.
     */
    protected function isWindows()
    {
        if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32') {
            return true;
        } else {
            return false;
        }
    }
}