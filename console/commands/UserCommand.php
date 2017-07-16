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
 * Class UserCommand
 */
class UserCommand extends CConsoleCommand
{
    use LogTrait;

    public $minute;

    public function init()
    {
        $this->minute = 10;
        $this->isCli = true;
    }

    /**
     * Меняет статус(online|offline) пользователям.
     *
     * Вызов из консоли:
     * php yiic.php user authStatus
     *
     * @return int
     */
    public function actionAuthStatus()
    {
        $users = User::getNoActives($this->minute);
        Yii::log('Меняем статус пользователям count=' . count($users));
        foreach ($users as $user) {
            Yii::app()->db->createCommand()
                ->update(
                    'users',
                    ['online' => 0],
                    'user_id=:user_id',
                    [':user_id' => $user['user_id']]
                );
        }
        return 1;
    }

}