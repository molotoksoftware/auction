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
 *
 * Class MailingCommand
 */
class MailingCommand extends CConsoleCommand
{
    /**
     * Обрабатывает очередь email сообщений.
     * Вызов из консоли:
     * php yiic.php mailing processEmailQueue --process=30
     *
     * @param int $process Кол-во строк за раз.
     * @param int $makeTest
     *
     * @return int
     */
    public function actionProcessEmailQueue($process = 30, $makeTest = 0)
    {
        if (!$makeTest) {
            $result = Getter::emailQueue()->process($process);
        } else {
            $result = Getter::emailQueue()->testProcess($process);
        }
        return $result ? 0 : 1;
    }
}
