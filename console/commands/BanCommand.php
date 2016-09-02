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
 * Основные действия
 * - Автоматический бан пользователя с низким рейтингом
 */
class BanCommand extends CConsoleCommand {

    /**
     * @param array $args
     *
     * Вызов из консоли:
     * php yiic.php ban
     */
    public function run($args) {
        echo "Начинаем устанавливать флаг ban=1 юзерам с плохим рейтингом" . "\n";

        $r = Yii::app()
                ->getDb()
                ->createCommand()
                ->update(
                'users', ['ban' => 1], 'rating <= :rating AND ban = 0', [':rating' => (int)Yii::app()->params['banRating']]
        );
        echo sprintf("Обработно %s юзеров", $r) . "\n";

        echo "И обратная операция" . "\n";

        $r = Yii::app()
                ->getDb()
                ->createCommand()
                ->update(
                'users', ['ban' => 0], 'rating > :rating AND ban = 1', [':rating' => (int)Yii::app()->params['banRating']]
        );
        echo sprintf("Обработно %s юзеров", $r) . "\n";
    }
}
