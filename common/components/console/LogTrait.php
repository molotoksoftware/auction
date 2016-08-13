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


trait LogTrait
{
    public $logCategory = 'log_trait';

    protected $isCli;

    protected function log($msg, $level)
    {
        if (null === $this->isCli) {
            throw new CException('IsCli property must be set');
        }
        Yii::log($msg, $level, $this->logCategory);
        if ($this->isCli) {
            printf("%s [%s] %s\n", date('Y-m-d H:i:s'), $level, $msg);
        }
    }

    protected function error($msg)
    {
        $this->log($msg, CLogger::LEVEL_ERROR);
    }

    protected function warning($msg)
    {
        $this->log($msg, CLogger::LEVEL_WARNING);
    }

    protected function info($msg)
    {
        $this->log($msg, CLogger::LEVEL_INFO);
    }

    protected function trace($msg)
    {
        $this->log($msg, CLogger::LEVEL_TRACE);
    }

    protected function line()
    {
        $this->trace('+' . str_repeat('-', 100) . '+');
    }
}